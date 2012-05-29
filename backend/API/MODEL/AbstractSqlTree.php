<?php

$this->includeClass('INTERFACE', 'Tree');

abstract class SF_MODEL_AbstractSqlTree extends SF_LIB_ApiObject
		 implements SF_INTERFACE_Tree
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
	 * Stores the tree configuration
	 * @var array
	 */
	protected $treecfg = array(
		'tablename' => '',
		
		'fields' => array(
			'id' => '',
			'parent' => '',
			'sortindex' => ''
		),
		
		'idclient' => 0,
		'idlang' => 0,
		
		'order' => array(
			//'parent' => 'ASC',
			//'sortindex' => 'ASC'
		),
		
		'sql' => array(
			'generate' => 'SELECT {fields} FROM {tablename} WHERE {clientlang} {order};',
			'generate_noclientlang' => 'SELECT {fields} FROM {tablename} {order};'
		),

		'perm' => array(
			'active' => FALSE,
			'type' => '',
			'nr' => 1
		)
	);
	
	/**
	 * Stores the loaded data in different arrays
	 * @var array
	 */
	protected $data = array(
		'items_levelorder' => array(), // [index] => iditem
		'items_level' => array(),// iditem => level
		'items_sortindex' => array(),// iditem => index
		'count_items_in_level' => array(), // [level] = count
		'last_items' => array(),
		'level_max' => 0,
		'rawdata' => array(), // [parent][index] = iditem
		'parents' => array() // [iditem] = parent
	);
	
	
	/**
	 * Constructor sets up {@link $db} and {@link $cfg}.
	 */
	public function __construct()
	{
		$this->db = sf_api('LIB', 'Ado');
		$this->cfg = sf_api('LIB', 'Config');
	}
	
	/**
	 * Set the tablename
	 * @param string $tablename
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _setTablename($tablename)
	{
		if ($tablename == '')
		{
			return FALSE;
		}
		
		$this->treecfg['tablename'] = $tablename;
		
		return TRUE;
	}
	
	/**
	 * Sets the rowname for the field
	 * @param string $fieldkey
	 * @param string $fieldname
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _setFields($fieldkey, $fieldname)
	{
		if (! array_key_exists($fieldkey, $this->treecfg['fields']))
		{
			return FALSE;
		}
		
		$this->treecfg['fields'][$fieldkey] = $fieldname;
		
		return TRUE;
	}

	/**
	 * Sets the rowname for the field
	 * @param string $fieldkey
	 * @param string $fieldname
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _setPerm($fieldkey, $fieldname)
	{
		if (! array_key_exists($fieldkey, $this->treecfg['perm']))
		{
			return FALSE;
		}

		$this->treecfg['perm'][$fieldkey] = $fieldname;

		return TRUE;
	}
	
	/**
	 * Set the perm check by type and id. Also possible to enable it with this call
	 * @param string $type
	 * @param integer $permid, default = 0
	 * @param boolean $enable, default = FALSE
	 * @return void
	 */
	public function setPermCheck($type, $permid = 0, $enable = FALSE)
	{
		if(strlen($type) > 0)
		{
			$this->_setPerm('type', $type);
		}
		
		if($permid > 0)
		{
			$this->_setPerm('nr', $permid);
		}
		
		$this->enablePermCheck($enable);
	}
	
	/**
	 * Enable the perm check for tree
	 * @param boolean $enable
	 * @return void
	 */
	public function enablePermCheck($enable)
	{
		$this->_setPerm('active', (bool) $enable);
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
		
		$this->treecfg['idclient'] = $idclient;
		
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
		
		$this->treecfg['idlang'] = $idlang;
		
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
		
		$this->treecfg['order'][$order] = ($direction == 'DESC') ? 'DESC':'ASC';
		
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
		
		$this->treecfg['sql'][$what] = $value;
		
		return TRUE;
	}
	
	
	/**
	 * Generates the collection to the set criteria.
	 * First build the SQL statement and select only the ids.
	 * Then load the models (items) by id and push them to stack.
	 * Note: The id field is taken from the first table of the model.
	 * @see API/INTERFACES/SF_INTERFACE_Collection#generate()
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function generate()
	{
		$fields = $this->_getSqlFields(
			$this->treecfg['fields']
		);
		
		$clientlang = $this->_getSqlClientLang(
			$this->treecfg['idclient'],
			$this->treecfg['idlang'],
			array(
				'client' => 'idclient', 
				'lang', 'idlang'
			)
		);
		
		// default order
		if(count($this->treecfg['order']) <= 0)
		{
			$this->treecfg['order'][$this->treecfg['fields']['parent']] = 'ASC';
			
			if($this->treecfg['fields']['sortindex'] != '')
			{
				$this->treecfg['order'][$this->treecfg['fields']['sortindex']] = 'ASC';
			}
		}
		$order = $this->_getSqlOrder(
			$this->treecfg['order']
		);
		
		$sql = $this->_getGenerateSql($fields, $this->treecfg['tablename'], $clientlang, $order);
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
		
		$sortindex = 0;
		while (! $rs->EOF) 
		{
			if($this->treecfg['fields']['sortindex'] == '')
			{
				$sortindex = count($this->data['rawdata'][ $rs->fields['parent'] ]);
			}
			else
			{
				$sortindex = (is_numeric($rs->fields['sortindex'])) ? (int) $rs->fields['sortindex'] : $rs->fields['sortindex'];
			}
			
			$this->data['rawdata'][ $rs->fields['parent'] ][ $sortindex ] = (is_numeric($rs->fields['id'])) ? (int) $rs->fields['id'] : $rs->fields['id'];
			$this->data['parents'][ $rs->fields['id'] ] = (is_numeric($rs->fields['parent'])) ? (int) $rs->fields['parent'] : $rs->fields['parent'];
			
			$rs->MoveNext();
		}
		
		$rs->Close();
		
		$this->_treeOrder(0);
		
		return TRUE;
	}
	
	/**
	 * Returns the sortindex for the given id 
	 * @param integer $id
	 * @return integer
	 */
    public function getSortindex($id)
    {
    	return $this->data['items_sortindex'][$id];
    }
    
	/**
	 * Returns the level for the given id 
	 * @param integer $id
	 * @return integer
	 */
	public function getLevel($id)
	{
		return $this->data['items_level'][$id];
	}
	
	/**
	 * Returns the parent for the given id 
	 * @see API/INTERFACES/SF_INTERFACE_Tree#getParent($id)
	 * @param integer $id
	 * @return integer
	 */
	public function getParent($id)
	{
		//echo "IN $id";
		return $this->data['parents'][$id];
	}
	
	/**
	 * Returns all parents of the given child as array.
	 * @see API/INTERFACES/SF_INTERFACE_Tree#getParentsRecursive($parent, $is_first)
	 * @param integer $parent
	 * @param boolean $is_first 
	 * @return array Returns all parents of the given child as array.
	 */
	public function getParentsRecursive($child, $is_first = TRUE)
	{
		if ($is_first)
		{
			$this->tempchilds = array($child);
		}
		
		$a = $this->getParent($child);
		
		if ($a > 0)
		{
			array_push($this->tempchilds, $a);
			$this->getParentsRecursive($a, FALSE);
		}
		
		if ($is_first)
		{
			if($child > 0)
			{
				array_push($this->tempchilds, 0);
			}
			return $this->tempchilds;
		} 
	}
	
	/**
	 * Returns a boolean if the given id has children
	 * @param integer $parent
	 * @return boolean Returns TRUE if has children, else returns FALSE.
	 */
	public function hasChildren($parent)
	{
		return is_array($this->data['rawdata'][$parent]);
	}
	
	/**
	 * Returns the direct children of the given parent as array.
	 * @param integer $parent
	 * @return array
	 */
	public function getChildren($parent)
	{
		$out = array();
		
		if (is_array($this->data['rawdata'][$parent]))
		{
			foreach ($this->data['rawdata'][$parent] AS $index => $id)
			{
				array_push($out, $id);
			}
		}
		
		return $out;
	}
	
	/**
	 * Returns all children of the given parent as array.
	 * @param integer $parent
	 * @param boolean $is_first 
	 * @return array Returns all children of the given parent as array.
	 */
	public function getChildrenRecursive($parent, $is_first = TRUE)
	{
		if ($is_first)
		{
			$this->tempchilds = array();
		}
		
		$a = $this->getChildren($parent);
		
		if (count($a) > 0)
		{
			array_push($this->tempchilds, $a);
			foreach ($a AS $id)
			{
				$this->getChildrenRecursive($id, FALSE);
			}
		}
		
		if ($is_first)
		{  		
			$out = array();
			foreach ($this->tempchilds AS $arr)
			{
				foreach($arr AS $id)
				{
					$out[] = $id;
				}
			}
			return $out;
		} 
	}
	
	/**
	 * Checks with the sortindex if the id
	 * is the first item in the level. 
	 * @param integer $id
	 * @return boolean Returns TRUE if the first. Otherwise returns FALSE.
	 */
    public function isFirstItemInLevel($id)
    {
    	return (1 == $this->data['items_sortindex'][$id]);
    }
    
    /**
     * Checks with the sortindex if the id
	 * is the last item in the level. 
	 * @param integer $id
	 * @return boolean Returns TRUE if the first. Otherwise returns FALSE.
     */
    public function isLastItemInLevel($id)
	{
    	return in_array($id, $this->data['last_items']);
    }
	
    /**
     * Returns the maximum number of level
     * @return integer
     */
	public function getMaxLevel()
	{
		return $this->data['level_max'];
	}
	
	/**
	 * Returns the number of items in the given level.
	 * @param integer $level 
	 * @return integer 
	 */
	public function countItemsInLevel($level)
	{
		return count($this->data['count_items_in_level'][$level]);
	}
	
	/**
	 * Returns the generated items as PHP5 array iterator 
	 * @see API/INTERFACES/SF_INTERFACE_Collection#getLevelorderIterator()
	 * @return Iterator Returns the generated items as PHP5 array iterator
	 */
	public function getLevelorderIterator()
	{
		$arrayobject = new ArrayObject($this->data['items_levelorder']);
		return $arrayobject->getIterator();
	}
	
	/**
	 * Reset the collection to set new configurations
	 * and generate the collection one more time.
	 * @see API/INTERFACES/SF_INTERFACE_Collection#reset()
	 * @return boolean Returns TRUE if reset successfully. Otherwise returns FALSE.
	 */
	public function reset()
	{
		return FALSE; /* TODO */
	}
	
	
	/*
	 * PROTECTED
	 */
	
	/**
	 * Generate a SQL statement for the given fields
	 * @param array $fields
	 * @return string Returns the piece of the SQL statement.
	 * If no array given it returns an string with the
	 * wildcard selector (*).
	 */
	protected function _getSqlFields($fields)
	{
		if(count($fields) <= 0)
		{
			return '*';
		}
		$arr = array();
		foreach($fields as $key => $value)
		{
			if($value != '')
			{
				$arr[] = $value.' as '.$key;
			}
		}
		return implode(', ', $arr);
	}
	
	/**
	 * Generate a SQL statement for the given idclient and idlang
	 * @param integer $idclient
	 * @param integer $idlang
	 * @return string Returns the piece of the SQL statement.
	 * If an error occurs it returns an empty string.
	 */
	protected function _getSqlClientLang($idclient, $idlang, $fields)
	{
		$sql_clientlang = '';
		if(!is_numeric($idclient) || !is_numeric($idlang))
		{
			return $sql_clientlang;
		}
		
		if($idclient > 0)
		{
			$sql_clientlang .= " ".$fields['client']." = '".$idclient."'";
		}
		
		if($idlang > 0)
		{
			$sql_clientlang .= ($sql_clientlang != '') ? " AND " : "";
			$sql_clientlang .= " ".$fields['lang']." = '".$idlang."' ";
		}
		
		return ' '.$sql_clientlang;
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
			$arr[] = $field.' '.$direction;
		}
		
		return ' ORDER BY '.implode(',', $arr);
	}
	
	/**
	 * Builds the SQL statements with the 
	 * given parameters.
	 * @param string$fields 
	 * @param string $tablename 
	 * @param string $clientlang  
	 * @param string $order 
	 * @return string Returns the SQL statement
	 */
	protected function _getGenerateSql($fields, $tablename, $clientlang, $order)
	{
		if($clientlang == '')
		{
			$sql = $this->treecfg['sql']['generate_noclientlang'];
		}
		else
		{
			$sql = $this->treecfg['sql']['generate'];
		}
		
		if ($fields == '' || $tablename == '' || $sql == '') 
		{
			return FALSE;
		}
		
		$sql = str_replace(
			array(
				'{fields}',
				'{tablename}',
				'{clientlang}',
				'{order}',
			),
			array(
				$fields,
				$tablename,
				$clientlang,
				$order
			),
			$sql
		);
		
		return $sql;
	}
	
	/**
	 * Order the tree recursivly after generating the
	 * rawdata.  
	 * @param integer $parent 
	 * @param integer $level
	 * @return boolean Returns TRUE on success, otherwise returns FALSE.
	 */
	protected function _treeOrder($parent, $level=0, $parent_replace = FALSE)
	{
		if ($level > $this->data['level_max'])
		{
			$this->data['level_max'] = $level;
		}
		
		if (is_array($this->data['rawdata'][$parent]))
		{
			foreach ($this->data['rawdata'][$parent] AS $index => $id)
			{
				$perm_ok = TRUE;
				if ($this->treecfg['perm']['active'])
				{
					$perm_ok = $this->cfg->perm()->have_perm($this->treecfg['perm']['nr'], $this->treecfg['perm']['type'], $id);
				}

				if ($perm_ok)
				{
					$this->data['items_levelorder'][] = $id;
					$this->_incrementLevelCount($level);
					$this->data['items_level'][$id] = $level;
					$this->data['items_sortindex'][$id] = $sortindex;

					if (is_array($this->data['rawdata'][$id]))
					{
						$this->_treeOrder($id, $level+1);
					}
				}
				else
				{
					if (is_array($this->data['rawdata'][$id]))
					{
						$this->_treeOrder($id, $level);
					}
					unset($this->data['rawdata'][$parent][$index]);
					unset($this->data['parents'][$id]);
				}
			}
			array_push($this->data['last_items'], $id);
		}
		
		return TRUE;
	}
	
	/**
	 * Increase the items per level
	 * @param integer $level
	 */
	protected function _incrementLevelCount($level)
	{
		if(isset($this->data['count_items_in_level'][$level]))
		{
			++$this->data['count_items_in_level'][$level];
		}
		else
		{
			$this->data['count_items_in_level'][$level] = 1;
		}		
	} 
}
?>