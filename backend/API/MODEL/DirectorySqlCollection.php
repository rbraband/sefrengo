<?php

$this->includeClass('MODEL', 'AbstractSqlCollection');

class SF_MODEL_DirectorySqlCollection extends SF_MODEL_AbstractSqlCollection
{
	/**
	 * Constructor
	 * Set up the model and fulltext searchfields
	 * @return void
	 */
	public function __construct()
	{
		// first set up the model
		$this->setCfg('model_path', 'MODEL');
		$this->setCfg('model', 'DirectorySqlItem');
		
		// set perms
		//$this->setCfg('perm_check_active', TRUE);
		$this->setCfg('perm_type', 'directory');
		$this->setCfg('perm_nr', '1');
		$this->setCfg('perm_dbfield_id', 'iddirectory');
		

		// then call parent constructor to get tablefields
		parent::__construct();
		
		// set fulltextsearchfields afterwards
		$this->setCfg(
			'fulltextsearchfileds', 
			array(
				$this->tables[0].'.name',
				$this->tables[1].'.description'
			)
		);
	}
	
	/**
	 * Overwrites the creation of the client
	 * and lang clause.
	 * @see API/MODELS/SF_MODEL_AbstractSqlCollection#_getSqlClientLang($idclient, $idlang, $fields)
	 */
	protected function _getSqlClientLang($idclient, $idlang, $fields, $accept_id_zero = FALSE)
	{
		$fields['client'] = $this->tables[0].'.idclient';
		$fields['lang'] = $this->tables[1].'.idlang';
		
		return parent::_getSqlClientLang($idclient, $idlang, $fields, TRUE); // set to TRUE, otherwise idlang = 0 is skipped 
	}
	
	/**
	 * Overwrites the creation of the timestamp
	 * to use the first table (cms_upl).
	 * @see API/MODELS/SF_MODEL_AbstractSqlCollection#_getSqlTimestamp($field, $timestamp_start, $timestamp_stop)
	 */
	protected function _getSqlTimestamp($field, $timestamp_start, $timestamp_stop)
	{
		$field = $this->tables[0].'.'.$field;
		return parent::_getSqlTimestamp($field, $timestamp_start, $timestamp_stop);
	}
	
	/**
	 * Overwrites the creation of the free filter
	 * to add the tablename to the given field (key)
	 * in the freefilter.
	 * Note: If the field exists in more than one table
	 * the first appearance of the field is used. 
	 * @see API/MODELS/SF_MODEL_AbstractSqlCollection#_getSqlFreefilter($freefilter, $check_fields_exists = TRUE)
	 */
	protected function _getSqlFreefilter($freefilter, $check_fields_exists = TRUE)
	{
		$item = sf_api($this->colcfg['model_path'], $this->colcfg['model']);
		foreach($this->tables as $tablename => $fields)
		{
			$mappedfield = $item->mapFieldToRow($freefilter['key'], $tablename);
			if(is_array($fields) && (in_array($freefilter['key'], $fields) || in_array($mappedfield, $fields)))
			{
				$freefilter['key'] = $tablename.'.'.$mappedfield;
				// break forces the first appearance of field 
				break;
			}
		}
		unset($item);
		// disable field check, because filter like '(status & 32) = 8' won't work with enabled check
		return parent::_getSqlFreefilter($freefilter, FALSE);
	}
	
	/**
	 * Overwrites the createion of the SQL order clause
	 * to add the tablename to the given field (key)
	 * in the freefilter.
	 * Note: If the field exists in more than one table
	 * the first appearance of the field is used. 
	 * @see API/MODELS/SF_MODEL_AbstractSqlCollection#_getSqlOrder($field, $direction)
	 */
	protected function _getSqlOrder($order)
	{
		if(count($order) <= 0)
		{
			return '';
		}
		
		$item = sf_api($this->colcfg['model_path'], $this->colcfg['model']);
		$arr = array();
		
		foreach($this->tables as $tablename => $fields)
		{
			foreach($order as $field => $direction)
			{
				$mappedfield = $item->mapFieldToRow($field, $tablename);
				if(is_array($fields) && (in_array($field, $fields) || in_array($mappedfield, $fields)))
				{
					$arr[] = $tablename.'.'.$mappedfield.' '.$direction;
				}
			}
		}
		unset($item);
		
		if(count($arr) <= 0)
		{
			return '';
		}
		
		return ' ORDER BY '.implode(',', $arr);
	}
	
	/**
	 * Overwrites the creation of the generate SQL
	 * statement to use multiple tables and connect
	 * them with a join statement. 
	 * @see API/MODELS/SF_MODEL_AbstractSqlCollection#_getGenerateSql($id, $table, $clientlang, $timestamp, $freefilter, $search, $order, $limit)
	 */
	protected function _getGenerateSql($id, $table, $clientlang, $timestamp, $freefilter, $search, $order, $limit)
	{
		$id = $this->_getMappedIdField($this->tables[0]);
		$table = $this->tables[0].$this->_getLeftJoinSql();
		
		return parent::_getGenerateSql($id, $table, $clientlang, $timestamp, $freefilter, $search, $order, $limit);
	}
	
	/**
	 * Overwrites the creation of the generate SQL
	 * statement to use multiple tables and connect
	 * them with a join statement. 
	 * @see API/MODELS/SF_MODEL_AbstractSqlCollection#_getCountAllSql($id, $table, $clientlang, $timestamp, $freefilter, $search)
	 */
	protected function _getCountAllSql($id, $table, $clientlang, $timestamp, $freefilter, $search)
	{
		$id = $this->_getMappedIdField($this->tables[0]);
		$table = $this->tables[0].$this->_getLeftJoinSql();
		
		return parent::_getCountAllSql($id, $table, $clientlang, $timestamp, $freefilter, $search);
	}
	
	
	/**
	 * Overwrites the creation of the perm SQL
	 * statement to use multiple tables and connect
	 * them with a join statement. 
	 * @see API/MODELS/SF_MODEL_AbstractSqlCollection#_getPermcheckSql($id, $table, $clientlang, $timestamp, $freefilter, $search)
	 */
	protected function _getPermcheckSql($fielditem, $fieldparent = NULL, $tablename, $clientlang, $timestamp, $freefilter, $search)
	{
		$table = $this->tables[0].$this->_getLeftJoinSql();
		
		return parent::_getPermcheckSql($fielditem, $fieldparent, $table, $clientlang, $timestamp, $freefilter, $search);
	}
	
	/**
	 * Returns the mapped table row of the id field 
	 * @param string $tablename
	 * @return string Returns the mapped table row of the id field
	 */
	private function _getMappedIdField($tablename)
	{
		$item = sf_api($this->colcfg['model_path'], $this->colcfg['model']);
		return $tablename.".".$item->mapFieldToRow('id', $tablename);
	}
	
	/**
	 * Returns left join sql statement for customized methods like _getGenerateSql(), 
	 * _getCountAllSql(), _getPermsSql()
	 * @return string 
	 */
	private function _getLeftJoinSql()
	{
		$sql = "
				LEFT JOIN
					".$this->tables[1]."
				ON(".
					$this->_getMappedIdField($this->tables[0])." = ".$this->tables[1].".iddirectory"
				.")";
		return $sql;
	}
}
?>