<?php

$this->includeClass('INTERFACE', 'Collection');

abstract class SF_MODEL_AbstractSqlCollection extends SF_LIB_ApiObject
		 implements SF_INTERFACE_Collection
{	
	/**
	 * Configuration object
	 * @var SF_LIB_Config
	 */
	protected $cfg;
	
	/**
	 * Database object
	 * @var SF_LIB_Ado
	 */
	protected $db;
	
	/**
	 * Stores the collection configuration
	 * @var array
	 */
	protected $colcfg = array(
		'model_path' => 'MODEL',
		'model' => 'itemClass',
		
		'limit_start' => false,
		'limit_max' => false,
		'order' => array(
			//'id' => 'ASC'
		),
		'idgroup' => 0,
		
		'idclient' => 0,
		'idlang' => 0,
		
		'operators' => array('=', '<=', '>=', '<', '>', '<>', '!=', 'IN', 'LIKE'),
		'freefilter' => array(
			/*'sample' => array(
				'field' => 'sample',
				'value' => '9',
				'operator' => '='
			),*/
		),
		
		'timestamp_from' => -1,
		'timestamp_to' => -1,
		
		'fulltextsearchfileds' => array(),
		'searchterm' => false,
	
		'sql' => array(
			'generate' => 'SELECT {id} FROM {tablename} WHERE {timestamp} {clientlang} {freefilter} {search} {perms} {order} {limit};',
			'countall' => 'SELECT COUNT({id}) AS countme FROM {tablename} WHERE {timestamp} {clientlang} {freefilter} {search} {perms};',
			'permcheck' => 'SELECT {iditem} AS iditem, {idparent} AS idparent FROM {tablename} WHERE {timestamp} {clientlang} {freefilter} {search};'
		),
		
		'perm_check_active' => FALSE,
		'perm_type' => '',
		'perm_nr' => '',
		'perm_dbfield_id' => '',
		'perm_dbfield_parent' => '',
		
	);
	
	
	/**
	 * Stores the tables and table fields
	 * as one dimensional array.
	 * @var array
	 */
	protected $tables = array(
		//'cms_table' => array('id', 'created', 'lastmodified')
	);
	
	/**
	 * Stores the loaded items
	 * @var array
	 */
	protected $items = array();
	
	/**
	 * Stores the loaded ids
	 * @var array
	 */
	protected $ids = array();
	
	/**
	 * Over all number of possible items for this collection
	 * @var integer
	 */
	protected $count_all = 0;
	
	/**
	 * Number of loaded items in this collection
	 * @var integer
	 */
	protected $count = 0;
	
	
	/**
	 * Constructor sets up {@link $db} and {@link $cfg}.
	 * Also retrieves the tables and tablefields from the given model.
	 */
	public function __construct()
	{
		$this->db = sf_api('LIB', 'Ado');
		$this->cfg = sf_api('LIB', 'Config');
		
		$this->_setTablesFromModel();
	}
	
	/**
	 * Sets the object configuration 
	 * @param string $key
	 * @param mixed $value
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setCfg($key, $value)
	{
		if (! array_key_exists($key, $this->colcfg))
		{
			return FALSE;
		}
		
		$this->colcfg[$key] = $value;
		
		return TRUE;
	}
	
	/**
	 * Set the idclient for the SQL statements
	 * @param integer $idclient 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setIdclient($idclient)
	{
		if(! is_numeric($idclient) || $idclient < 0)
		{
			return FALSE;
		}
		
		$this->colcfg['idclient'] = $idclient;
		
		return TRUE;
	}

	/**
	 * Set the idlang for the SQL statements
	 * @param integer $idlang
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setIdlang($idlang)
	{
		if(! is_numeric($idlang) || $idlang < 0)
		{
			return FALSE;
		}
		
		$this->colcfg['idlang'] = $idlang;
		
		return TRUE;
	}
	
	/**
	 * Set a daterange to create a start and stop
	 * timestamp.
	 * @param array $from e.g. array('d'=>13, 'm'=>2, 'y'=>2099)
	 * @param array $to e.g. array('d'=>13, 'm'=>2, 'y'=>2099)
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setDaterange($from, $to)
	{
		$from = array_merge(array('d' => date('d'), 'm' => date('m'), 'y' => date('Y')), $from);
		$to = array_merge(array('d' => date('d'), 'm' => date('m'), 'y' => date('Y')), $to);
		
		$this->colcfg['timestamp_from'] = mktime(0, 0, 0, $from['m'], $from['d'], $from['y']);
		$this->colcfg['timestamp_to'] = mktime(23, 59, 59, ($to['m']+1), $to['d'], $to['y']);
		
		return TRUE;
	}
	
	/**
	 * Set a searchterm
	 * @param string $searchterm 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setSearchterm($searchterm) 
	{
		if ($searchterm == '')
		{
			return FALSE;
		}
		
		$this->colcfg['searchterm'] = $searchterm;
		
		return TRUE;
	}
	
	/**
	 * Sets the max value to the limit clause.
	 * @param integer $max
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setLimitMax($max) 
	{
		$this->colcfg['limit_max'] = (int) $max;
		return TRUE;
	}
	
	/**
	 * Sets the start value to the limit clause.
	 * @param integer $start
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setLimitStart($start) 
	{
		$this->colcfg['limit_start'] = (int) $start;
	}
	
	/**
	 * Sets a key value pair for the free filter.
	 * The key is a table field.
	 * @param string $key 
	 * @param mixed $value 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setFreefilter($key, $value, $operator = '=')
	{
		if ($key == '' || !in_array($operator, $this->colcfg['operators']))
		{
			return FALSE;
		}
		
		$this->colcfg['freefilter'][$key] = array(
			'field' => $key,
			'value' => $value,
			'operator' => $operator
		);
		
		return TRUE;
	}
	
	/**
	 * Sets a field and the optional direction for the order clause.
	 * @param string $order 
	 * @param string $direction Can be ASC or DESC
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setOrder($order, $direction = 'ASC') 
	{
		if ($order == '')
		{
			return FALSE;
		}
		
		$this->colcfg['order'][$order] = (strtoupper($direction) == 'DESC') ? 'DESC':'ASC';
		
		return TRUE;
	}
	
	/**
	 * Enable or disable the permcheck. If the permcheck is active, only the
	 * items with the 'can view' perm will be generated
	 * @param boolean $bool
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setPermCheckActive($bool) 
	{
		//Only allow setting if all needed vars are set
		if ($this->colcfg['perm_type'] == '' || $this->colcfg['perm_nr'] == '' || $this->colcfg['perm_dbfield_id'] == '')
		{
			return FALSE;
		}
		
		$this->colcfg['perm_check_active'] = (bool) $bool;
		return TRUE;
	}

	/**
	 * Generates the collection to the set criteria.
	 * First build the SQL statement and select only the ids.
	 * Then load the models (items) by id and push them to stack.
	 * Note: The id field is taken from the first table of the model.
	 * @see API/INTERFACES/SF_INTERFACE_Collection#generate()
	 * @param boolean $load_ids_only Loads only the IDs of the items, not the items itself
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function generate($load_ids_only = FALSE)
	{
		$clientlang = $this->_getSqlClientLang(
			$this->colcfg['idclient'],
			$this->colcfg['idlang'],
			array(
				'client' => 'idclient', 
				'lang' => 'idlang'
			)
		);
		$timestamp = $this->_getSqlTimestamp(
			'created',
			$this->colcfg['timestamp_from'],
			$this->colcfg['timestamp_to']
		);
		$freefilter = $this->_getSqlFreefilter(
			$this->colcfg['freefilter']
		);
		$search = $this->_getSqlSearch(
			$this->colcfg['searchterm'],
			$this->colcfg['fulltextsearchfileds']
		);
		$order = $this->_getSqlOrder(
			$this->colcfg['order']
		);
		$limit = $this->_getSqlLimit(
			$this->colcfg['limit_start'],
			$this->colcfg['limit_max']
		);
		
		// get the id from the first table of the model
		$item = sf_api($this->colcfg['model_path'], $this->colcfg['model']);
		$idfield = $item->mapFieldToRow('id', $this->tables[0]);
		$table = $this->tables[0];
		unset($item);
		
		$sql = $this->_getGenerateSql($idfield, $table, $clientlang, $timestamp, $freefilter, $search, $order, $limit);
		//echo $sql."<br />";
		
		if($sql === FALSE)
		{
			return FALSE;
		}
		
		$rs = $this->db->Execute($sql);
		
		if ($rs === FALSE || $rs->EOF) 
		{
			return FALSE;
		}
		
		while (! $rs->EOF) 
		{
			if($load_ids_only == TRUE)
			{
				array_push($this->ids, $rs->fields[$idfield]);
			}
			else
			{
				$item = sf_api($this->colcfg['model_path'], $this->colcfg['model']);
				$item->setIdclient( $this->colcfg['idclient'] );
				$item->setIdlang( $this->colcfg['idlang'] );
				if($item->loadById($rs->fields[$idfield])) 
				{
					array_push($this->ids, $rs->fields[$idfield]);
					array_push($this->items, $item);
				}
			}
			$rs->MoveNext();
		}
		$rs->Close();
		
		$this->count = count($this->items);
		
		return TRUE;
	}
	
	/**
	 * Returns all IDs from the loaded items as array.
	 * @see API/INTERFACES/SF_INTERFACE_Collection#getIdsAsArray()
	 * @return Returns all IDs from the loaded items as array
	 */
	public function getIdsAsArray()
	{
		return $this->ids;
	}
	
	/**
	 * Returns the generated items as array.
	 * @see API/INTERFACES/SF_INTERFACE_Collection#getItemsAsArray()
	 * @return Returns the generated items as array 
	 */
	public function getItemsAsArray()
	{
		return $this->items;
	}
	
	/**
	 * Returns the generated items as PHP5 array iterator 
	 * @see API/INTERFACES/SF_INTERFACE_Collection#getItemsAsArray()
	 * @return Returns the generated items as PHP5 array iterator
	 */
	public function getItemsAsIterator()
	{
		$arrayobject = new ArrayObject($this->items);
		return $arrayobject->getIterator();
	}
	
	/**
	 * Calculate all possible items and return the number.
	 * @see API/INTERFACES/SF_INTERFACE_Collection#getCountAll()
	 * @return Returns the number of possible items
	 */
	public function getCountAll()
	{
		if ($this->count_all < 1)
		{
			$this->_countAll();
		}
		
		return (int) $this->count_all;
	}
	
	/**
	 * Returns the number of generated/loaded items.
	 * @see API/INTERFACES/SF_INTERFACE_Collection#getCount()
	 * @return Returns the number of generated/loaded items.
	 */
	public function getCount()
	{
		return (int) $this->count;
	}
	
	/**
	 * Reset the collection to set new configurations
	 * and generate the collection one more time.
	 * @see API/INTERFACES/SF_INTERFACE_Collection#reset()
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function reset()
	{
		$this->items = array();
		$this->ids = array();
		$this->count_all = 0;
		$this->count = 0;
		
		// TODO reset some $colcfg fields
		
		return TRUE;
	}
	
	
	/*
	 * PROTECTED
	 */
	
	/**
	 * Retrieve the tables and table fields from the given model
	 * and stores them in the $tables variable from the collection. 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _setTablesFromModel()
	{
		$model = sf_api($this->colcfg['model_path'], $this->colcfg['model']);
		$this->tables = $model->getTablenames();
		foreach($this->tables as $tablename)
		{
			$this->tables[$tablename] = $model->getMappedFieldsForTable($tablename);
		}
		return TRUE;
	}

	/**
	 * Adds an new SQL template e.g. for load or delete SQL.
	 * The template may contain variables like {tablename},
	 * {where}, etc. 
	 * @param string $what 
	 * @param string $value 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _addSqlTemplates($what, $value)
	{
		if($what == '' || $value == '')
		{
			return FALSE;
		}
		
		$this->colcfg['sql'][$what] = $value;
		
		return TRUE;
	}
	
	/**
	 * Calculate the number of all possible item to the set criteria.
	 * First build the SQL statement and count the number by id.
	 * Note: The id field is taken from the first table of the model.
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _countAll() 
	{
		$clientlang = $this->_getSqlClientLang(
			$this->colcfg['idclient'],
			$this->colcfg['idlang'],
			array(
				'client' => 'idclient', 
				'lang' => 'idlang'
			)
		);
		$timestamp = $this->_getSqlTimestamp(
			'created',
			$this->colcfg['timestamp_from'],
			$this->colcfg['timestamp_to']
		);
		$freefilter = $this->_getSqlFreefilter(
			$this->colcfg['freefilter']
		);
		$search = $this->_getSqlSearch(
			$this->colcfg['searchterm'],
			$this->colcfg['fulltextsearchfileds']
		);
		
		$item = sf_api($this->colcfg['model_path'], $this->colcfg['model']);
		$idfield = $item->mapFieldToRow('id', $this->tables[0]);
		$table = $this->tables[0];
		unset($item);
		
		$sql = $this->_getCountAllSql($idfield, $table, $clientlang, $timestamp, $freefilter, $search);
		
		if($sql === FALSE)
		{
			return FALSE;
		}
		
		$rs = $this->db->Execute($sql);
		
		if ($rs === FALSE || $rs->EOF) {
			return FALSE;
		}
		$rs->Close();
		
		$this->count_all = $rs->fields['countme'];
		return TRUE;
	}
	
	/**
	 * Generate a SQL statement for the given idclient and idlang
	 * @param integer|array $idclient If variable is an array all given values must be integer
	 * @param integer|array $idlang If variable is an array all given values must be integer
	 * @param boolean $accept_id_zero By default idclient = 0 or idlang = 0 ignored. If true, use them.
	 * @return string Returns the piece of the SQL statement.
	 * If an error occurs it returns an empty string.
	 */
	protected function _getSqlClientLang($idclient, $idlang, $fields, $accept_id_zero = FALSE)
	{
		$sql_clientlang = '';
		$idclients = (!is_array($idclient)) ? array($idclient) : $idclient;
		$idlangs = (!is_array($idlang)) ? array($idlang) : $idlang;
		unset($idclient, $idlang);
		
		foreach($idclients as $idclient)
		{
			if(!is_numeric($idclient))
			{
				return $sql_clientlang;
			}
		}
		foreach($idlangs as $idlang)
		{
			if(!is_numeric($idlang))
			{
				return $sql_clientlang;
			}
		}
		
		if(count($idclients) == 1 && ($idclients[0] > 0 || ($idclients[0] == 0 && $accept_id_zero == TRUE)))
		{
			$sql_clientlang .= " ".$fields['client']." = '".$idclients[0]."'";
		}
		else if(count($idclients) > 1)
		{
			$sql_clientlang .= " ".$fields['client']." IN (".implode(',', $idclients).") ";
		}
		
		if(count($idlangs) == 1 && ($idlangs[0] > 0 || ($idlangs[0] == 0 && $accept_id_zero == TRUE)))
		{
			$sql_clientlang .= ($sql_clientlang != '') ? " AND " : "";
			$sql_clientlang .= " ".$fields['lang']." = '".$idlangs[0]."'";
		}
		else if(count($idlangs) > 1)
		{
			$sql_clientlang .= ($sql_clientlang != '') ? " AND " : "";
			$sql_clientlang .= " ".$fields['lang']." IN (".implode(',', $idlangs).") ";
		}
		
		return ' AND '.$sql_clientlang;
	}
	
	/**
	 * Generate a SQL statement for searchterm and
	 * every given fulltext searchfield.
	 * Therefore the searchterm is splitted at space character
	 * and set with SQL LIKE operator for every fulltext
	 * searchfield.
	 * @param string $searchterm 
	 * @param array $fulltextsearchfileds 
	 * @return string Returns the piece of the SQL statement.
	 * If an error occurs it returns an empty string.
	 */
	protected function _getSqlSearch($searchterm, $fulltextsearchfileds)
	{
		if($searchterm == '' || !is_array($fulltextsearchfileds) || count($fulltextsearchfileds) <= 0)
		{
			return '';
		}
		
		$searchterm = trim($searchterm);
		$term = mysql_real_escape_string($searchterm);
		$pieces = explode(' ', $term);
		$sql_search_array = array();
		foreach ($pieces AS $word) 
		{
			if (trim($word) == '') 
			{
				continue;
			}
			$sql_search_array_single = array();
			foreach ($fulltextsearchfileds AS $field) 
			{
				array_push($sql_search_array_single, $field." LIKE '%".$word."%'");
			}
			array_push($sql_search_array, ' ( ' .implode(' OR ', $sql_search_array_single) .' ) ');
		}
		return ' AND '. implode(' AND ' ,$sql_search_array);
	}
	
	/**
	 * Generate a SQL statement for a given field
	 * and the timestamp between start and stop.
	 * @param string $field 
	 * @param double $timestamp_from 
	 * @param double $timestamp_to 
	 * @return string Returns the piece of the SQL statement.
	 * If an error occurs it returns an empty string.
	 */
	protected function _getSqlTimestamp($field, $timestamp_from, $timestamp_to)
	{
		if( $field == '' ||
			!is_numeric($timestamp_from) || $timestamp_from < 0 ||
			!is_numeric($timestamp_to) || $timestamp_to < 0)
		{
			return '';
		}
		
		//return " ".$field." BETWEEN ".$this->db->DBTimeStamp($timestamp_from)." AND ".$this->db->DBTimeStamp($timestamp_to)." ";
		return " ".$field." BETWEEN ".$timestamp_from." AND ".$timestamp_to." ";
	}
	
	/**
	 * Generate a SQL statement to a given freefilter.
	 * @param array $freefilter multidimensional array with key, val, operator
	 * @param boolean $check_fields_exists Checks if the freefilter field really exists in table definition
	 * @return string Returns the piece of the SQL statement.
	 * If an error occurs it returns an empty string.
	 */
	protected function _getSqlFreefilter($freefilter, $check_fields_exists = TRUE)
	{
		if(count($freefilter) <= 0)
		{
			return '';
		}
		
		$arr = array();
		foreach($freefilter as $field => $fieldarr)
		{
			// check if the field really exists
			if( ($check_fields_exists == TRUE && $this->in_array_recursive($field, $this->tables))
				|| $check_fields_exists == FALSE)
			{
				if($fieldarr['operator'] == 'IN')
				{
					$fieldarr['value'] = (!is_array($fieldarr['value'])) ? array($fieldarr['value']) : $fieldarr['value'];
					
					foreach($fieldarr['value'] as $key => $val)
					{
						$fieldarr['value'][$key] = mysql_real_escape_string($val);
					}
					
					$arr[] = mysql_real_escape_string($fieldarr['field'])
							 .' '.$fieldarr['operator']."('".
							 implode("','", $fieldarr['value'])
							 ."')";
				}
				else
				{
					$arr[] = mysql_real_escape_string($fieldarr['field'])
							 .' '.$fieldarr['operator']." '".
							 mysql_real_escape_string($fieldarr['value'])."'";
				}
			}
			
		}

		return " AND ".implode(' AND ', $arr)." ";
	}
	
	/**
	 * Generate a SQL statement to order by field and direction.
	 * @param array $order 
	 * @return string Returns the piece of the SQL statement.
	 * If an error occurs it returns an empty string.
	 */
	protected function _getSqlOrder($order)
	{
		if(count($order) <= 0)
		{
			return '';
		}
		
		$arr = array();
		foreach($order as $field => $direction)
		{
			if($this->in_array_recursive($field, $this->tables))
			{
				$arr[] = mysql_real_escape_string($field).' '.mysql_real_escape_string($direction);
			}
			
		}
		if(count($arr) <= 0)
		{
			return '';
		}
		
		return ' ORDER BY '.implode(',', $arr);
	}
	
	/**
	 * Generate a SQL statement to set the limit by start and max value.
	 * @param integer $start
	 * @param integer $max
	 * @return string Returns the piece of the SQL statement.
	 * If an error occurs it returns an empty string.
	 */
	protected function _getSqlLimit($start, $max)
	{
		$sql_limit = '';
		if ($start || $max) 
		{
			if ($max) 
			{
				$sql_limit = ' LIMIT '. (int) $start.', '. (int) $max;
			} 
			else if ($start) 
			{
				$sql_limit = ' LIMIT '. (int) $start;
			}
		}
		
		return $sql_limit;
	}
	
	/**
	 * Generate a SQL statement to set the perm limits
	 * @return string Returns the piece of the SQL statement.
	 * If an error occurs it returns an empty string.
	 */
	protected function _getSqlPerms()
	{
		static $sql_perms = '';
		
		
		//perms are allready generated, return the string
		if ($sql_perms != '')
		{
			return $sql_perms;
		}
		
		//init perms with nothing
		$sql_perms = ' ';
		
		//Check if permcheck is enabled
		if (! $this->colcfg['perm_check_active'])
		{
			return $sql_perms;
		}
		
		//TODO make it for other clients/langs work
		if (TRUE)
		{
			$perm = $this->cfg->perm();
		}
		else
		{
			//TODO $client, $lang
			$perm = new cms_perms($client, $lang, TRUE, $this->cfg->perm()->get_group());
		}
		
		//admin has all perms, no more actions are needed
		if ($perm->is_admin())
		{
			return $sql_perms;
		}
		
		$fielditem = $this->colcfg['perm_dbfield_id'];
		$fieldparent = ($this->colcfg['perm_dbfield_parent'] != '') ? $this->colcfg['perm_dbfield_parent'] : NULL;
		$tablename = $this->tables[0];
		$clientlang = $this->_getSqlClientLang(
			$this->colcfg['client'],
			$this->colcfg['lang'],
			array(
				'client' => 'idclient', 
				'lang' => 'idlang'
			)
		);
		$timestamp = $this->_getSqlTimestamp(
			'created',
			$this->colcfg['timestamp_from'],
			$this->colcfg['timestamp_to']
		);
		$freefilter = $this->_getSqlFreefilter(
			$this->colcfg['freefilter']
		);
		$search = $this->_getSqlSearch(
			$this->colcfg['searchterm'],
			$this->colcfg['fulltextsearchfileds']
		);
		
		$item = sf_api($this->colcfg['model_path'], $this->colcfg['model']);
		$idfield = $item->mapFieldToRow('id', $this->tables[0]);

		$sql = $this->_getPermcheckSql($fielditem, $fieldparent, $tablename, $clientlang, $timestamp, $freefilter, $search);
		
		if($sql === FALSE)
		{
			return $sql_perms;
		}
		
		$rs = $this->db->Execute($sql);
		
		if ($rs === FALSE || $rs->EOF) 
		{
			return $sql_perms;
		}

		$positives = array();
		$negatives = array();
		//perms with dependancy
		if ($this->colcfg['perm_dbfield_parent'] != NULL)
		{
			while (! $rs->EOF) 
			{
				if ($perm->have_perm($this->colcfg['perm_nr'], $this->colcfg['perm_type'], $rs->fields['iditem'], $rs->fields['idparent']))
				{
					array_push($positives, $rs->fields['iditem']);
				}
				else
				{
					array_push($negatives, $rs->fields['iditem']);
				}
				$rs->MoveNext();
			}	
		}
		//perms without dependancy
		else
		{
			while (! $rs->EOF) 
			{
				if ($perm->have_perm($this->colcfg['perm_nr'], $this->colcfg['perm_type'], $rs->fields['iditem']))
				{
					array_push($positives, $rs->fields['iditem']);
				}
				else
				{
					array_push($negatives, $rs->fields['iditem']);
				}
				$rs->MoveNext();
			}
		}
		$rs->Close();
		
		$count_pos = count($positives);
		$count_neg = count($negatives);
		if ($count_pos == 0 && $count_neg == 0)
		{
			return $sql_perms;
		}
		else if ($count_pos < $count_neg && $count_pos > 0 )
		{
			$sql_perms = 'AND '. $this->colcfg['perm_dbfield_id'] . ' IN ('.implode(',', $positives).') ';
		}
		else if ($count_neg > 0)
		{
			$sql_perms = 'AND '. $this->colcfg['perm_dbfield_id'] . ' NOT IN ('.implode(',', $negatives).') ';
		}
		
		return $sql_perms;
	}
	
	/**
	 * Builds the SQL statements with the 
	 * given parameters.
	 * @param string $id 
	 * @param string $tablename 
	 * @param string $clientlang 
	 * @param string $timestamp
	 * @param string $freefilter
	 * @param string $search 
	 * @param string $order
	 * @param string $limit
	 * @return string Returns the SQL statement
	 */
	protected function _getGenerateSql($id, $tablename, $clientlang, $timestamp, $freefilter, $search, $order, $limit)
	{
		$sql = $this->colcfg['sql']['generate'];
		
		if ($id == '' || $tablename == '' || $sql == '') 
		{
			return FALSE;
		}
		
		$sql = str_replace(
			array(
				'{id}',
				'{tablename}',
				'{clientlang}',
				'{timestamp}',
				'{freefilter}',
				'{search}',
				'{perms}',
				'{order}',
				'{limit}',
			),
			array(
				$id,
				$tablename,
				$clientlang,
				$timestamp,
				$freefilter,
				$search,
				$this->_getSqlPerms(),
				$order,
				$limit
			),
			$sql
		);
		
		$sql = $this->_replaceInvalidWhereClauses($sql);
		
		return $sql;
	}
	
	/**
	 * Builds the SQL statements with the 
	 * given parameters.
	 * @param string $id 
	 * @param string $tablename 
	 * @param string $clientlang 
	 * @param string $timestamp
	 * @param string $freefilter
	 * @param string $search
	 * @return string Returns the SQL statement
	 */
	protected function _getCountAllSql($id, $tablename, $clientlang, $timestamp, $freefilter, $search)
	{
		$sql = $this->colcfg['sql']['countall'];
		
		if ($id == '' || $tablename == '' || $sql == '') 
		{
			return FALSE;
		}
		
		$sql = str_replace(
			array(
				'{id}',
				'{tablename}',
				'{clientlang}',
				'{timestamp}',
				'{freefilter}',
				'{search}',
				'{perms}',
			),
			array(
				$id,
				$tablename,
				$clientlang,
				$timestamp,
				$freefilter,
				$search,
				$this->_getSqlPerms()
			),
			$sql
		);
		
		$sql = $this->_replaceInvalidWhereClauses($sql);
		
		return $sql;
	}
	
	/**
	 * Builds the SQL statements with the 
	 * given parameters.
	 * @param string $fielditem
	 * @param string $fieldparent
	 * @param string $tablename 
	 * @param string $clientlang 
	 * @param string $timestamp
	 * @param string $freefilter
	 * @param string $search
	 * @return string Returns the SQL statement
	 */
	protected function _getPermcheckSql($fielditem, $fieldparent = NULL, $tablename, $clientlang, $timestamp, $freefilter, $search)
	{
		$sql = $this->colcfg['sql']['permcheck'];
		
		if ($fielditem == '' || $tablename == '' || $sql == '') 
		{
			return FALSE;
		}
		
		$fieldparent = (strlen($fieldparent) < 1) ? $fielditem : $fieldparent;
		
		$sql = str_replace(
			array(
				'{iditem}',
				'{idparent}',
				'{tablename}',
				'{clientlang}',
				'{timestamp}',
				'{freefilter}',
				'{search}'
			),
			array(
				$fielditem,
				$fieldparent,
				$tablename,
				$clientlang,
				$timestamp,
				$freefilter,
				$search
			),
			$sql
		);
		
		$sql = $this->_replaceInvalidWhereClauses($sql);
		
		return $sql;
	}
	
	/**
	 * Replaces invalid where clauses e.g. 'WHERE AND' in the given SQL statement 
	 * @param string $sql
	 * @return string Returns the SQL statement
	 */
	protected function _replaceInvalidWhereClauses($sql)
	{
		// replace multiple spaces with one
		$sql = preg_replace('!\s+!', ' ', $sql);
		// relace invalid where clauses
		$sql = str_replace(
			array('WHERE AND', 'WHERE OR'),
			array('WHERE', 'WHERE'),
			$sql
		);
		
		return $sql;
	}
	
	/**
	 * Searches recursivley for $needle in
	 * $haystack and returns the boolean. 
	 * @param mixed $needle 
	 * @param array $haystack 
	 * @return Returns TRUE if the $needle is in $haystack. Otherwise returns FALSE.
	 */
	private function in_array_recursive($needle, $haystack)
	{
	    $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($haystack));
	    
	    foreach($it AS $element)
	    {
	        if($element == $needle)
	        {
	            return TRUE;
	        }
	    }
		
	    return FALSE;
	}
}
?>