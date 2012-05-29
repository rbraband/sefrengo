<?php

$this->includeClass('MODEL', 'AbstractSqlTree');

class SF_MODEL_DirectorySqlTree extends SF_MODEL_AbstractSqlTree
{
	/**
	 * Constructor sets up the tablename and fields.
	 * Also changes the SQL statements for use with area.
	 * @return void
	 */
	public function __construct()
	{
		
		parent::__construct();
		
		$this->_setTablename($this->cfg->db('directory'));
		$this->_setFields('id', 'iddirectory');
		$this->_setFields('parent', 'parentid');
		//$this->_setFields('sortindex', 'sortindex');

		$this->setPermCheck('directory', 1, TRUE);
		
		$this->_addSqlTemplates(
			'generate',
			'SELECT {fields} FROM {tablename} WHERE {area} AND {clientlang} {order};'
		);
		$this->_addSqlTemplates(
			'generate_noclientlang',
			'SELECT {fields} FROM {tablename} WHERE {area} {order};'
		);
	}
	
	/**
	 * Set the area for the SQL statements
	 * @param string $area 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setArea($area)
	{
		if($area == '')
		{
			return FALSE;
		}
		
		$this->treecfg['area'] = $area;
		
		return TRUE;
	}
	
	/**
	 * Overwrite the generation of the SQL statement
	 * to force the use of the area in directory trees.
	 * @see API/MODELS/SF_MODEL_AbstractSqlTree#_getGenerateSql($fields, $tablename, $clientlang, $order)
	 */
	protected function _getGenerateSql($fields, $tablename, $clientlang, $order)
	{
		$sql = parent::_getGenerateSql($fields, $tablename, $clientlang, $order);
		
		$sql = str_replace('{area}', " area = '".$this->treecfg['area']."' ", $sql);
		
		return $sql;
	}
}
?>