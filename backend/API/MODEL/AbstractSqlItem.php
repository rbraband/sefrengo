<?php

$this->includeClass('INTERFACE', 'SqlItem');

abstract class SF_MODEL_AbstractSqlItem extends SF_LIB_ApiObject
		 implements SF_INTERFACE_SqlItem
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
	 * Stores the different perms for the object.
	 * The default type can be overwritten.
	 * @var array
	 */
	protected $objperm = array(
		'default' => array(
			/*'type' => 'default'*/
		),
		/*'view' => array(
			'permid' => 1
		),
		'create' => array(
			'permid' => 1,
			'type' => 'overwritten_default_type'
		)*/
	);
	
	
	/**
	 * Stores the tables and their
	 * configurations in an array
	 * @var array
	 */
	protected $tables = array(
		'cms_table' => array(
			'name' => 'cms_table',
			
			'config' => array(
				'rowmapping' => array(
					//'id' => 'iditem'
				),
				
				'disabled' => array(
					//'ip' => true,
					//'groupkey' => true
				),
				
				'foreignkeys' => array(
					'iditem' => array(
						'table' => 'cms_othertable',
						'foreignkey' => 'idtable'
					)
				),
				
				'sql' => array(
					'load' => 'SELECT {fields} FROM {tablename} WHERE {where};',
					'delete' => 'DELETE FROM {tablename} WHERE {where};'
				),
				
				'dirty' => array()
			),
			
			'fields' => array(
				'id' => 0,
				'idclient' => '',
				'idlang' => '',
				'created' => '',
				'created_author' => '',
				'lastmodified' => '',
				'lastmodified_author' => '',
				'ip' => '',
				'groupkey' => 'def'
			),
			
			'serialized' => array(),
			
			'pipe_sep' => array(),
			
			'special' => array()
		)
	);
	
	/**
	 * Stores the item configuration
	 * @var array
	 */
	protected $itemcfg = array(
		'idclient' => 0,
		'idlang' => 0,
		
		// used for insert and update in save()
		'current_author' => 0,
	);
	
	
	/**
	 * Constructor sets up {@link $db} and {@link $cfg}.
	 */
	public function __construct()
	{		
		$this->db = sf_api('LIB', 'Ado');
		$this->cfg = sf_api('LIB', 'Config');
		
		$this->itemcfg['current_author'] = (int) $this->cfg->auth('uid');
		
		// remove default sample table
		$this->_removeTable('cms_table');
	}
	
	/**
	 * Set the idclient to the item configuration for the SQL statements
	 * Note: Do not use this function to store the value into
	 *       the database. Use the setField() method instead.
	 * @param integer $idclient 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setIdclient($idclient)
	{
		if(! is_numeric($idclient) || $idclient < 0)
		{
			return FALSE;
		}
		
		$this->itemcfg['idclient'] = $idclient;
		
		return TRUE;
	}

	/**
	 * Set the idlang to the item configuration for the SQL statements
	 * Note: Do not use this function to store the value into
	 *       the database. Use the setField() method instead.
	 * @param integer $idlang 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setIdlang($idlang)
	{
		if(! is_numeric($idlang) || $idlang < 0)
		{
			return FALSE;
		}
		
		$this->itemcfg['idlang'] = $idlang;
		
		return TRUE;
	}
	
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * @param string $tablename
	 * @return integer Returns the field 'id'
	 */
	public function getId($tablename = '')
	{
		return (int) $this->getField('id', $tablename);
	}
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * @param string $tablename
	 * @return integer Returns the field 'idclient'
	 */
	public function getIdclient($tablename = '')
	{
		return $this->getField('idclient', $tablename);
	}
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * @param string $tablename
	 * @return integer Returns the field 'idlang'
	 */
	public function getIdlang($tablename = '')
	{
		return $this->getField('idlang', $tablename);
	}
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * @param string $tablename
	 * @return integer Returns the field 'created'
	 */
	public function getCreated($tablename = '')
	{
		return $this->getField('created', $tablename);
	}
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * If $dateformat is set, the custom format is used for date(). 
	 * @param string $tablename 
	 * @param string $dateformat 
	 * @return string Returns the formatted field 'created'
	 */
	public function getCreatedDate($tablename = '', $dateformat = '')
	{
		$dateformat = ($dateformat == '') ? 'd.m.Y' : $dateformat;
		return date($dateformat, $this->getField('created', $tablename));
	}
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * If $timeformat is set, the custom format is used for date().  
	 * @param string $tablename 
	 * @param string $timeformat 
	 * @return string Returns the formatted field 'created'
	 */
	public function getCreatedTime($tablename = '', $timeformat = '')
	{
		$timeformat = ($timeformat == '') ? 'H:i' : $timeformat;
		return date($timeformat, $this->getField('created', $tablename));
	}
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * @param string $tablename
	 * @param string $userfield
	 * @return integer|string Returns the field 'created_author'
	 */
	public function getCreatedAuthor($tablename = '', $userfield = 'id')
	{
		$iduser = $this->getField('created_author', $tablename);
		if($userfield == 'id')
		{
			return $iduser;
		}
		// TODO use new user item
		$user = sf_api('ADMINISTRATION', 'User');
		$user->loadByIduser($iduser);
		
		switch($userfield)
		{
			case 'name':
				return $user->getName();
				break;
			case 'surname':
				return $user->getSurname();
				break;
			case 'username':
			default:
				return $user->getUsername();
				break;
		}
	}
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * @param string $tablename
	 * @return integer Returns the field 'lastmodified'
	 */
	public function getLastmodified($tablename = '')
	{
		return $this->getField('lastmodified', $tablename);
	}
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * If $dateformat is set, the custom format is used for date(). 
	 * @param string $tablename
	 * @param string $dateformat
	 * @return string Returns the formatted field 'lastmodified'
	 */
	public function getLastmodifiedDate($tablename = '', $dateformat = '')
	{
		$dateformat = ($dateformat == '') ? 'd.m.Y' : $dateformat;
		return date($dateformat, $this->getField('lastmodified', $tablename));
	}
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * If $timeformat is set, the custom format is used for date(). 
	 * @param string $tablename
	 * @param string $timeformat
	 * @return string Returns the formatted field 'lastmodified'
	 */
	public function getLastmodifiedTime($tablename = '', $timeformat = '')
	{
		$timeformat = ($timeformat == '') ? 'H:i' : $timeformat;
		return date($timeformat, $this->getField('lastmodified', $tablename));
	}
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * @param string $tablename
	 * @param string $userfield
	 * @return integer|string Returns the field 'lastmodified_author'
	 */
	public function getLastmodifiedAuthor($tablename = '', $userfield = 'id')
	{
		$iduser = $this->getField('lastmodified_author', $tablename);
		if($userfield == 'id')
		{
			return $iduser;
		}
		// TODO use new user item
		$user = sf_api('ADMINISTRATION', 'User');
		$user->loadByIduser($iduser);
		
		switch($userfield)
		{
			case 'name':
				return $user->getName();
				break;
			case 'surname':
				return $user->getSurname();
				break;
			case 'username':
			default:
				return $user->getUsername();
				break;
		}
	}
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * @param string $tablename
	 * @return string Returns the field 'ip'
	 */
	public function getIp($tablename = '')
	{
		return $this->getField('ip', $tablename);
	}
	
	/**
	 * Select the field and returns it's value.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * @param string $tablename
	 * @return string Returns the field 'groupkey'
	 */
	public function getGroupkey($tablename = '')
	{
		return $this->getField('groupkey', $tablename);
	}
	
	/**
	 * Selects a field and returns it's value. The function also iterates through
	 * serialized and pipe seperated fields. 
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * @param string $field 
	 * @param string $tablename
	 * @return mixed|boolean Returns the value of the field. Otherwise, if no field found returns FALSE. 
	 */
	public function getField($field, $tablename = '')
	{
		if($tablename != '' && array_key_exists($tablename, $this->tables))
		{
			return $this->_get($field, $tablename);
		}
		else if(count($this->tables) > 0)
		{
			foreach($this->tables as $name => $table)
			{
				$value = $this->_get($field, $name);
				if($value != FALSE) {
					return $value;
				}
			} 
		}
		
		return FALSE;
	}
	
	/**
	 * Selects a field and returns it's value. The function searches
	 * only in the special field.
	 * If $tablename is empty the first appearance from tables
	 * in this item is returned.
	 * @param string $field 
	 * @param string $tablename
	 * @return mixed|boolean Returns the value of the field. Otherwise, if no field found returns FALSE. 
	 */
	public function getSpecial($field, $tablename = '') 
	{
		if ($field == '') 
		{
			return FALSE;
		}
		
		if($tablename != '' && array_key_exists($field, $this->tables[$tablename]['special']))
		{
			return $this->tables[$tablename]['special'][$field];
		}
		else if(count($this->tables) > 0)
		{
			foreach($this->tables as $name => $table)
			{
				if(array_key_exists($field, $table[$where]))
				{
					return $table['special'][$field];
				}
			}
		}
		
		return FALSE;
	}

	/**
	 * Returns an array with the tablenames of this model
	 * @return array Returns an array with the tablenames of this model
	 */
	public function getTablenames()
	{
		return array_keys($this->tables);
	}
	
	/**
	 * Returns all fields of a table as array.
	 * Note: It does not include the serialized and pipe seperated fields.
	 * @param string $tablename
	 * @param boolean $remove_disabled If TRUE removes disabled fields from return array. 
	 * @return array|boolean Returns the fields as array. If no table found FALSE is returned.
	 */
	public function getFieldsForTable($tablename, $remove_disabled = TRUE)
	{
		if ($tablename == '' || !array_key_exists($tablename, $this->tables))
		{
			return FALSE;
		}
		
		$arrayobject = new ArrayObject(array_keys($this->tables[$tablename]['fields']));
		$iter = $arrayobject->getIterator();
		$fields = array();
		
		while($iter->valid())
		{
			if(($remove_disabled == TRUE && $this->_isFieldDisabled($iter->current(), $tablename) == FALSE)
			   || $remove_disabled == FALSE)
			{
				$fields[] = $iter->current();
			}
			
			$iter->next();
		}
		
		return $fields;
	}
	
	/**
	 * Returns all fields mapped to rows of a table as array.
	 * Note: It does not include the serialized and pipe seperated fields.
	 * @param string $tablename
	 * @param boolean $remove_disabled If TRUE removes disabled fields from return array. 
	 * @return array|booelan Returns the fields as array. If no table found FALSE is returned.
	 */
	public function getMappedFieldsForTable($tablename, $remove_disabled = TRUE)
	{
		if ($tablename == '' || !array_key_exists($tablename, $this->tables))
		{
			return FALSE;
		}
		
		$arrayobject = new ArrayObject(array_keys($this->tables[$tablename]['fields']));
		$iter = $arrayobject->getIterator();
		$fields = array();
		
		while($iter->valid())
		{
			if(($remove_disabled == TRUE && $this->_isFieldDisabled($iter->current(), $tablename) == FALSE)
			   || $remove_disabled == FALSE)
			{
				$fields[] = $this->mapFieldToRow($iter->current(), $tablename);
			}
			
			$iter->next();
		}
		
		return $fields;
	}
	
	/**
	 * Look up the given field as String or an simple array of fields
	 * in the given table.
	 * If $fields is an array and $strict is TRUE it checks if all fields
	 * exists in table. Otherwise if $strict is FALSE it the function
	 * returns TRUE if only one field exists.
	 * @param array|string $fields
	 * @param string $tablename
	 * @param boolean $strict Only available if $fields is an array
	 * @return booelan Returns TRUE on success. Otherwise returns FALSE.
	 */
	public function isFieldInTable($fields, $tablename, $strict = FALSE)
	{
		$field_exists = FALSE;
		$table_fields = $this->getFieldsForTable($tablename);

		if(is_array($fields) == TRUE)
		{
			// if $strict == TRUE assume that all fields exists
			$field_exists = ($strict == TRUE) ? TRUE : FALSE;
			
			foreach($fields as $field)
			{
				if($strict == FALSE && in_array($field, $table_fields) == TRUE)
				{
					$field_exists = TRUE;
					break;
				}
				else if($strict == TRUE && in_array($field, $table_fields) == FALSE)
				{
					$field_exists = FALSE;
					break;
				}
			}
		}
		else if(is_string($fields) && in_array($fields, $table_fields) == TRUE)
		{
			$field_exists = TRUE;
		}
		
		return $field_exists;
	}

	/**
	 * Return the table row if it is mapped to field,
	 * otherwise return the given field.   
	 * @param string $field 
	 * @param string $tablename 
	 * @return array Returns mapped table row or given field. 
	 */
	public function mapFieldToRow($field, $tablename)
	{
		if(is_array($this->tables[$tablename]['config']['rowmapping'])
			&& array_key_exists($field, $this->tables[$tablename]['config']['rowmapping']))
		{
			return $this->tables[$tablename]['config']['rowmapping'][$field];
		}
		
		return $field;
	}
	
	/**
	 * Return the internal field if it is mapped to a table row,
	 * otherwise return the given table row.   
	 * @param string $row 
	 * @param string $tablename 
	 * @return string Returns mapped field or given table row. 
	 */
	public function mapRowToField($row, $tablename)
	{
		$field = array_search($row, $this->tables[$tablename]['config']['rowmapping']);
		return ($field === FALSE) ? $row : $field;
	}
	
	/**
	 * Sets a value to the field.
	 * If $tablename is empty the function will only use the
	 * first table of this model and set the field within. 
	 * @param string $field 
	 * @param string $value 
	 * @param string $tablename 
	 * @return boolean Returns true, if set field was successful. Otherwise it returns false.
	 */
	public function setGroupkey($value, $tablename = '')
	{
		if($tablename == '')
		{
			$tablename = $this->_getTablenameForField('groupkey');
			
			if($tablename == FALSE)
			{
				return FALSE;
			}
		}
		
		return $this->_set($tablename, 'fields', 'groupkey', $value);
	}
	
	/**
	 * Sets a value to the given field.
	 * Serialized and pipe seperated field are converted automatically.
	 * If $tablename is empty the function will only use the
	 * first table of this model and set the field within. 
	 * @param string $field 
	 * @param string $value 
	 * @param string $tablename
	 * @return boolean Returns true, if set field was successful. Otherwise it returns false.
	 */
	public function setField($field, $value, $tablename = '')
	{
		if ($field == '') 
		{
			return FALSE;
		}
		
		if($tablename == '')
		{
			$tablename = $this->_getTablenameForField($field);
			
			if($tablename == FALSE)
			{
				return FALSE;
			}
		}
		
		$tabledata = array();
		if(array_key_exists($tablename, $this->tables))
		{
			$tabledata = $this->tables[$tablename];
		}
		else
		{
			return FALSE;
		}
		
		//set normal value
		if (array_key_exists($field, $tabledata['fields']))
		{
			if (array_key_exists($field, $tabledata['pipe_sep']))
			{
				$value = $this->_pipeStrToArray($value, $tabledata['pipe_sep'][$field]);
			}
			
			return $this->_set($tablename, 'fields', $field, $value);	
		}
		//set serialized value
		else 
		{
			foreach ($tabledata['serialized'] AS $assigned_field=>$subfieldvalues)
			{
				if (array_key_exists($field, $tabledata['pipe_sep']))
				{
					if (! is_array($value))
					{
						$value = $this->_pipeStrToArray($value, $tabledata['pipe_sep'][$field]);
					}
				}
				
				if ($assigned_field == $field || array_key_exists($field, $subfieldvalues) == TRUE)
				{
					return $this->_set($tablename, 'serialized', $assigned_field, $value);
					
				}
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Sets a value to the given special field.
	 * If $tablename is empty the function will only use the
	 * first table of this model and set the field within. 
	 * @param string $field 
	 * @param string $value 
	 * @param string $tablename 
	 * @return boolean Returns true, if set field was successful. Otherwise it returns false.
	 */
	public function setSpecial($field, $value, $tablename = '')
	{
		if ($field == '') 
		{
			return FALSE;
		}
		
		if($tablename == '')
		{
			$keys = array_keys($this->tables);
			$tablename = $keys[0];
		}
		
		return $this->_set($tablename, 'special', $field, $value);
	}
	
	
	/**
	 * Loads the item by the given id.
	 * If $tablename is empty the function uses only the
	 * first table of this model. 
	 * @param integer $id 
	 * @param string $tablename
	 * @param string|array $fields
	 * @return boolean Returns TRUE if items loads successful. Otherwise returns FALSE.
	 */
	public function loadById($id, $tablename = '', $fields = '*')
	{
		$id = (int) $id;

		if ($id < 1)
		{
			return FALSE;
		}
		
		if($tablename == '')
		{
			$tablename = $this->_getTablenameForField('id');
			
			if($tablename == FALSE)
			{
				return FALSE;
			}
		}
		
		return $this->_load($tablename, array('id' => $id), $fields);
	}
	
	/**
	 * Saves the current item.
	 * If $tablename is empty the function saves
	 * all tables defined in this model. 
	 * @param string $tablename
	 * @return boolean Returns TRUE if saved successfully. Otherwise returns FALSE.
	 */
	public function save($tablename = '')
	{
		if($tablename != '' && array_key_exists($tablename, $this->tables))
		{
			return $this->_save($tablename);
		}
		else if(count($this->tables) > 0)
		{
			$bool = array();
			foreach($this->tables as $name => $table)
			{
				$bool[] = $this->_save($name);
			}
			return (!in_array(FALSE, $bool));
		}
		
		return FALSE;
	}
	
	/**
	 * Removes the current item by id from the DB.
	 * If $tablename is empty the function removes
	 * all tables defined in this model. 
	 * @param string $tablename
	 * @return boolean Returns TRUE if removed successfully. Otherwise returns FALSE.
	 */
	public function delete($tablename = '')
	{
		if($tablename != '' && array_key_exists($tablename, $this->tables))
		{
			return $this->_delete($tablename, 'id');
		}
		else if(count($this->tables) > 0)
		{
			$bool = TRUE;
			foreach($this->tables as $name => $table)
			{
				$bool = $this->_delete($name, 'id');
				// if save fails return immediately 
				if($bool == FALSE)
				{
					return FALSE;
				}
			}
			return $bool;
		}
		
		return FALSE;
	}
	
	/**
	 * Returns the default object perm type.
	 * @return string Returns the default object perm type.
	 */
	public function getObjectPermType()
	{
		return $this->objperm['default']['type'];
	}
	
	/**
	 * Returns the perm id for a given perm name.
	 * @param string $permname
	 * @return integer/boolean Returns the perm id as integer. If no $permname found returns FALSE. 
	 */
	public function getObjectPermId($permname)
	{
		if(array_key_exists($permname, $this->objperm) == TRUE)
		{
			return $this->objperm[$permname]['permid'];
		}
		
		return FALSE;
	}
	
	/**
	 * Checks if any group perms for the item exists in database.
	 * The function uses the default object type.
	 * @param boolean $ignore_user
	 * @return integer Returns the number of setted group rights.
	 */
	public function permsExists($ignore_user = TRUE)
	{
		return $this->cfg->perm()->perms_existing(
			$this->getId(),
			$this->objperm['default']['type'],
			$ignore_user
		);
	}
	
	/**
	 * Checks by given name if the object has the specific permission.
	 * @param string $name
	 * @param string $id
	 * @param integer $parent_id
	 * @return booelan Returns TRUE if has perm. Otherwise returns FALSE.
	 */
	public function hasPerm($name, $id = FALSE, $parent_id = FALSE)
	{
		if(array_key_exists($name, $this->objperm))
		{
			if(array_key_exists('permid', $this->objperm[$name]) == FALSE)
			{
				return -1;
			}
			$permid = $this->objperm[$name]['permid'];
			
			$type = $this->objperm['default']['type'];
			if(array_key_exists('type', $this->objperm[$name]))
			{
				$type = $this->objperm[$name]['type'];
			}
			
			// use current id if nothing is set
			if($id === FALSE)
			{
				$id = $this->getId();
			}
			$id = (string)$id; // cast to string
			
			$parent_id = ($parent_id === FALSE) ? 0 : (int)$parent_id;
			
			return $this->cfg->perm()->have_perm($permid, $type, $id, $parent_id);
		}
		
		// perm not found
		return FALSE;
	}
	
	/**
	 * Set group permissions of this object.
	 * The object rights will overwrite possible area rights.
	 * Consultate documentation for information about the rights management.
	 * @param string $groupids Comma sperated list of group ids
	 * @param string $grouprights Comma seperated list of rights for each group 
	 * @param string $inherit_grouprights Comma seperated list if bequeathed rights 
	 * @param string $overwrite_grouprights Comma seperated list of overwritten group rights 
	 * @param integer $idlang Language ID; optional, default: 0 = use current $idlang 
	 * @param hexadecimal number $bitmask Bitmask to check which rights are considered; optional, default: 0xFFFFFFFF = consider all superior rights
	 * @param integer as string $parent_id ID of the parent object; optional, default '0'
	 * @param hexadecimal number $second_bitmask Bitmask of perm inheritance for more than two levels
	 * @return void 
	 */
	public function setGroupRights($groupids, $grouprights, $inherit_grouprights, $overwrite_grouprights, $idlang = 0, $bitmask = 0xFFFFFFFF, $parent_id = '0', $second_bitmask = 0xFFFFFFFF)
	{
		// convert to set rights
		$idlang = ($idlang == 0) ? '' : $idlang;
		
		$this->cfg->perm()->set_group_rights(
			$this->objperm['default']['type'],
			$this->getId(),
			$groupids,
			$grouprights,
			$inherit_grouprights,
			$overwrite_grouprights,
			$idlang,
			$bitmask,
			$parent_id,
			$second_bitmask
		);
	}
	
	/**
	 * Copys the rights from the current item
	 * to the new item with $idtarget.
	 * @param integer $idtarget ID of the new (copied) item
	 * @param integer $group
	 * @param integer $lang
	 * @param boolean $ignore_lang
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function copyPerms($idtarget, $group = 0, $lang = 0, $ignore_lang = TRUE)
	{
		return $this->cfg->perm()->copy_perm(
			$this->getId(),
			$this->objperm['default']['type'],
			$idtarget,
			$group,
			$lang,
			$ignore_lang
		);
	}
	
	/**
	 * Deletes all rights for the item.
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function deleteAllPerms()
	{
		return $this->cfg->perm()->delete_perms(
			$this->getId(),
			$this->objperm['default']['type'],
			0, // group
			0, // idperm
			0, // lang
			TRUE // ignore_lang
		);
	}
	
	
	/*
	 * PROTECTED
	 */
	
	/**
	 * Adds a table to the model. You can pass the whole
	 * table data in the optional variable $tabledata into.
	 * @param string $table 
	 * @param array $tabledata
	 * @return boolean Returns TRUE if the table is added successfully. Otherwise returns FALSE.
	 */
	protected function _addTable($tablename, $tabledata = array())
	{
		if($tablename == '')
		{
			return FALSE;
		}
		
		if(! array_key_exists($tablename, $this->tables))
		{
			$this->tables[$tablename] =
				array(
					'name' => $tablename,
					'config' => array(
						'rowmapping' => array(),
						'disabled' => array(),
						'foreignkeys' => array(),
						'sql' => array(
							'load' => 'SELECT {fields} FROM {tablename} WHERE {where};',
							'delete' => 'DELETE FROM {tablename} WHERE {where};'
						),
						'dirty' => array()
					),
					'fields' => array(),
					'serialized' => array(),
					'pipe_sep' => array(),
					'special' => array()
				);
		}
		
		if(! empty($tabledata))
		{
			$this->tables[$tablename] = array_merge($this->tables[$tablename], $tabledata);
		}
		
		return TRUE;
	}
	
	/**
	 * Removes the table by the given name from the model.
	 * @param string $tablename  
	 * @return boolean Returns TRUE if the table is removed successfully. Otherwise returns FALSE.
	 */
	protected function _removeTable($tablename)
	{
		if($tablename == '')
		{
			return FALSE;
		}
		
		if(array_key_exists($tablename, $this->tables))
		{
			unset( $this->tables[$tablename] );
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Adds mapping rules from internal fields to table rows.
	 * The mapping will be used to created correct SQL statements
	 * @param string $tablename 
	 * @param array $mapping 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _addRowMapping($tablename, $mapping)
	{
		if($tablename == '' || !array_key_exists($tablename, $this->tables) ||
			empty($mapping) || !is_array($mapping))
		{
			return FALSE;
		}
		
		$this->tables[$tablename]['config']['rowmapping'] = array_merge($this->tables[$tablename]['config']['rowmapping'], $mapping);
		
		return TRUE;
	}
	
	/**
	 * Adds an array with fields that are disabled for use in SQL statements.
	 * Note that the value of each field must be an boolean. Set the value to
	 * TRUE to disable the field.
	 * @param string $tablename 
	 * @param array $disabled 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _addDisabledFields($tablename, $disabled)
	{
		if($tablename == '' || !array_key_exists($tablename, $this->tables) ||
			empty($disabled) || !is_array($disabled))
		{
			return FALSE;
		}
		
		$this->tables[$tablename]['config']['disabled'] = array_merge($this->tables[$tablename]['config']['disabled'], $disabled);
		
		return TRUE;
	}

	/**
	 * Adds an array with foreignkeys to use in SQL statements.
	 * Note that every key in the given array is also an array with
	 * the key 'table' and 'foreignkey' to point on. 
	 * @param string $tablename 
	 * @param array $foreignkeys 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _addForeignkeys($tablename, $foreignkeys)
	{
		if($tablename == '' || !array_key_exists($tablename, $this->tables) ||
			empty($foreignkeys) || !is_array($foreignkeys))
		{
			return FALSE;
		}
		
		$this->tables[$tablename]['config']['foreignkeys'] = array_merge($this->tables[$tablename]['config']['foreignkeys'], $foreignkeys);
		
		return TRUE;
	}

	/**
	 * Adds an new SQL template e.g. for load or delete SQL.
	 * The template may contain variables like {tablename},
	 * {where}, etc. 
	 * @param string $tablename
	 * @param string $what 
	 * @param string $value
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	protected function _addSqlTemplates($tablename, $what, $value)
	{
		if($tablename == '' || !array_key_exists($tablename, $this->tables) ||
			$what == '' || $value == '')
		{
			return FALSE;
		}
		
		$this->tables[$tablename]['config']['sql'][$what] = $value;
		
		return TRUE;
	}
	
	/**
	 * Adds fields to the given table.
	 * @param string $tablename 
	 * @param array $fields 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	protected function _addTableFields($tablename, $fields)
	{
		if($tablename == '' || !array_key_exists($tablename, $this->tables) ||
			empty($fields) || !is_array($fields))
		{
			return FALSE;
		}
		
		$this->tables[$tablename]['fields'] = array_merge($this->tables[$tablename]['fields'], $fields);
		
		return TRUE;
	}
	
	/**
	 * Adds serialized fields to the given table.
	 * @param string $tablename 
	 * @param array $fields 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	protected function _addSerializedFields($tablename, $fields)
	{
		if($tablename == '' || !array_key_exists($tablename, $this->tables) ||
			empty($fields) || !is_array($fields))
		{
			return FALSE;
		}
		
		$this->tables[$tablename]['serialized'] = array_merge($this->tables[$tablename]['serialized'], $fields);
		
		return TRUE;
	}
	
	/**
	 * Adds pipe seperated fields to the given table.
	 * @param string $tablename 
	 * @param array $fields 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	protected function _addPipeSepFields($tablename, $fields)
	{
		if($tablename == '' || !array_key_exists($tablename, $this->tables) ||
			empty($fields) || !is_array($fields))
		{
			return FALSE;
		}
		
		$this->tables[$tablename]['pipe_sep'] = array_merge($this->tables[$tablename]['pipe_sep'], $fields);
		
		return TRUE;
	}
	
	/**
	 * Adds default fields for the most tables. The fields are:
	 * id, idclient, idlang, created, created_author, lastmodified,
	 * lastmodified_author, ip and groupkey.
	 * Use the row mapping to customize the fields to your table row.
	 * @param string $tablename
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	protected function _addDefaultFields($tablename)
	{
		$fields = array(
			'id' => 0,
			'idclient' => '',
			'idlang' => '',
			'created' => '',
			'created_author' => '',
			'lastmodified' => '',
			'lastmodified_author' => '',
			'ip' => '',
			'groupkey' => 'def'
		);
		
		return $this->_addTableFields($tablename, $fields);
	}
	
	/**
	 * Loads an item and stores the result in the table array.
	 * Field names are mapped automatically to table rows.
	 * @param string $tablename
	 * @param string $where 
	 * @param string|array $fields 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	protected function _load($tablename, $where = array(), $fields = '*') 
	{	
		if ($tablename == '' || !array_key_exists($tablename, $this->tables)) 
		{
			return FALSE;
		}
		
		$sql = $this->_getLoadSql($tablename, $fields, $where);
		//echo $sql."<br />";
		
		if($sql === FALSE)
		{
			return FALSE;
		}
		
		$rs = $this->db->Execute($sql);
	
		if ($rs === FALSE || $rs->EOF )
		{
			return FALSE;
		}
		
		// get sql fields
		$tabledata = $this->tables[$tablename];
		$fields = $rs->fields;
		$rs->Close();
		
		foreach ($tabledata['fields'] AS $k=>$v)
		{
			$row = $this->mapFieldToRow($k, $tablename);
			if (array_key_exists($row, $fields))
			{
				$this->tables[$tablename]['fields'][$k] = $fields[$row];
			}
		}
		
		// check and assign serialized fields
		foreach ($tabledata['serialized'] AS $assigned_field=>$subfieldvalues)
		{
			if (array_key_exists($assigned_field, $tabledata['fields']))
			{
				if ($tabledata['fields'][$assigned_field] != '')
				{
					$this->tables[$tablename]['serialized'][$assigned_field] = $this->_unserialize($tabledata['fields'][$assigned_field]);
				}
			}
			else if(array_key_exists($assigned_field, $fields))
			{
				$this->tables[$tablename]['serialized'][$assigned_field] = $this->_unserialize($fields[$assigned_field]);
			}
		}
		
		return TRUE;
	}
	
	/**
	 * Saves the current item.
	 * If id > 0 the item is updated, otherwise it is inserted into the DB.
	 * Disabled fields are removed and field names mapped to table row names
	 * according to table configuration.
	 * @param string $tablename 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	protected function _save($tablename)
	{
		if ($tablename == '' || !array_key_exists($tablename, $this->tables)) 
		{
			return FALSE;
		}
		
		$tabledata = $this->tables[$tablename];
		
		if (empty($tabledata['config']['dirty']))
		{
			return TRUE;
		}
		
		$current_time = time();
		$current_author = $this->itemcfg['current_author'];
		
		// check and assign serialized fields
		foreach ($tabledata['serialized'] AS $assigned_field=>$subfieldvalues)
		{
			if(!empty($subfieldvalues))
			{
				$tabledata['fields'][$assigned_field] = $this->_serialize($subfieldvalues);
			}
			else
			{
				$tabledata['fields'][$assigned_field] = '';
			}
		}
		
		// UPDATE
		if ($tabledata['fields']['id'] > 0) 
		{
			$tabledata['fields']['lastmodified'] = $current_time;
			$tabledata['fields']['lastmodified_author'] = $current_author;
			
			$record = array();
			// map fields to rows and remove disabled fields
			foreach($tabledata['fields'] as $field => $value)
			{
				if($this->_isFieldDisabled($field, $tabledata['name']) == FALSE)
				{
					$mappedfield = $this->mapFieldToRow($field, $tabledata['name']);
					$fk_value = $this->_getForeignkeyValue($field, $tabledata['name']);
					
					$record[$mappedfield] = ($fk_value === FALSE) ? $value : $fk_value;
				}
			}
			
			$this->db->AutoExecute($tabledata['name'], $record, 'UPDATE', $this->mapFieldToRow('id', $tabledata['name'])." = '".$tabledata['fields']['id']."'");
		} 
		// INSERT
		else 
		{
			$tabledata['fields']['created'] = $current_time;
			$tabledata['fields']['lastmodified'] = $current_time;
			$tabledata['fields']['created_author'] = $current_author;
			$tabledata['fields']['lastmodified_author'] = $current_author;
			$tabledata['fields']['ip'] = $_SERVER['REMOTE_ADDR'];
			
			$record = array();
			// map fields to rows and remove disabled fields
			foreach($tabledata['fields'] as $field => $value)
			{
				// id not needed
				if($field != 'id' && $this->_isFieldDisabled($field, $tabledata['name']) == FALSE)
				{
					$mappedfield = $this->mapFieldToRow($field, $tabledata['name']);
					$fk_value = $this->_getForeignkeyValue($field, $tabledata['name']);
					
					$record[$mappedfield] = ($fk_value === FALSE) ? $value : $fk_value;
				}
			}
			
			$this->db->AutoExecute($tabledata['name'], $record, 'INSERT');

			$this->tables[$tablename]['fields']['id'] = $this->db->Insert_ID();
		}
		unset($record);
		
		// reset dirty
		$this->tables[$tablename]['config']['dirty'] = array();
		
		return TRUE;
	}
	
	/**
	 * Removes the current item from the DB.
	 * The variable $what defines which field to use
	 * @param string $what
	 * @param string $tablename
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	protected function _delete($tablename, $what)
	{
		if ($tablename == '' || !array_key_exists($tablename, $this->tables)) 
		{
			return FALSE;
		}
		
		switch ($what) {
			case 'id':
				$sql = $this->_getDeleteSql($tablename, array('id' => $this->tables[$tablename]['fields']['id']));
				break;
			default:
				return FALSE;
		}
		
		if($sql === FALSE)
		{
			return FALSE;
		}
		
		$rs = $this->db->Execute($sql);

		if ($rs === FALSE)
		{
			return FALSE;
		}
		$rs->Close();

		return TRUE;
	}
	
	/**
	 * Sets a value to given key in a defined table.
	 * The parameter $cast allows value casting to int, float
	 * or boolean. 
	 * @param string $tablename 
	 * @param string $where 
	 * @param string $field
	 * @param mixed $value 
	 * @param string $cast
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	protected function _set($tablename, $where, $field, $value, $cast = '') 
	{
		if ($tablename == '' || $where == '' || $field == '') 
		{
			return FALSE;
		}
		
		switch ($cast) 
		{
			case 'int':
				$value = (int) $value;
				break;
			case 'float':
				$value = (float) $value;
				break;
			case 'boolean':
				$value = (boolean) $value;
				break;
		}
		
		if ($this->tables[$tablename][$where][$field] != $value) 
		{
			$this->tables[$tablename][$where][$field] = $value;
			
			array_push($this->tables[$tablename]['config']['dirty'], $field);
			
			return TRUE;
		}
		
		return FALSE;
	}

	
	/**
	 * Looking for given field.
	 * If serialized or pipe seperated is called,
	 * it is returned converted.
	 * @param string $field 
	 * @param string $tablename 
	 * @return Returns the field if found, otherwise if no result found returns FALSE.
	 */
	protected function _get($field, $tablename)
	{
		if ($field == '' || $tablename == '') 
		{
			return FALSE;
		}
		
		$tabledata = $this->tables[$tablename];
		
		if (array_key_exists($field, $tabledata['fields']))
		{
			if (array_key_exists($field, $tabledata['pipe_sep']))
			{
				return $this->_arrToPipeStr($tabledata['fields'][$field]);
			}
			
			return $tabledata['fields'][$field];
		}
		else 
		{
			foreach ($tabledata['serialized'] AS $subfieldname=>$subfieldvalues)
			{
				if (is_array($subfieldvalues) && array_key_exists($field, $subfieldvalues))
				{
					if (array_key_exists($field, $tabledata['pipe_sep']))
					{
						return $this->_arrToPipeStr($tabledata['serialized'][$subfieldname][$field]);
					}
					
					return $tabledata['serialized'][$subfieldname][$field];
				}
				else if($subfieldname == $field)
				{
					return $tabledata['serialized'][$field];
				}
			}
		}
		
		return FALSE;
	}
	
	/**
	 * Checks if the field is disabled in table configuration.
	 * @param string $field 
	 * @param string $tablename 
	 * @return boolean Returns TRUE if the field is disabled, otherwise it returns FALSE.
	 */
	protected function _isFieldDisabled($field, $tablename)
	{
		return (array_key_exists($field, $this->tables[$tablename]['config']['disabled']) &&
				$this->tables[$tablename]['config']['disabled'][$field] == TRUE);
	}

	/**
	 * Checks if foreignkeys are existing for the given field.
	 * If it does return the value from the other table.
	 * @param string $field 
	 * @param string $tablename 
	 * @return boolean Returns the value of the foreignkey if it is found, otherwise it returns FALSE.
	 */
	protected function _getForeignkeyValue($field, $tablename)
	{
		$fk = $this->tables[$tablename]['config']['foreignkeys'];
		if(array_key_exists($field, $fk) && array_key_exists($fk[$field]['foreignkey'], $this->tables[ $fk[$field]['table'] ]['fields']))
		{
			return $this->tables[ $fk[$field]['table'] ]['fields'][ $fk[$field]['foreignkey'] ];
		}
		
		return FALSE;
	}
	
	/**
	 * Search for tablename where the field appears
	 * first.
	 * @param string $field 
	 * @return string|boolean Returns the tablename if found. Otherwise returns FALSE.
	 */
	protected function _getTablenameForField($field)
	{
		foreach($this->tables as $tablename => $tabledata)
		{
			if($this->_isFieldDisabled($field, $tablename) == TRUE)
			{
				continue;
			}
			if(array_key_exists($field, $tabledata['fields']) ||
				array_key_exists($field, $tabledata['pipe_sep']) ||
				array_key_exists($field, $tabledata['serialized']) ||
				array_key_exists($field, $tabledata['special']))
			{
				return $tablename;
			}
		}
		
		return FALSE;
	}
	
	
	/**
	 * Builds the SQL statements to load an item.
	 * If id is used as $field it will be checked if > 0.
	 * Field names are mapped automatically to table rows.
	 * If parameter $where or $fields are an array they
	 * are used as fields to generate the where or fields
	 * clause. Otherwise the parameters are a string they
	 * are used as they are. 
	 * @param string $tablename 
	 * @param string|array $fields 
	 * @param string|array $where
	 * @return string|boolean Returns the SQL string. If error occurs returns FALSE.
	 */
	protected function _getLoadSql($tablename, $fields, $where)
	{
		$sql = $this->tables[$tablename]['config']['sql']['load'];
		
		if ($tablename == '' || $sql == '') 
		{
			return FALSE;
		}
		
		$sql_fields = '';
		if(is_array($fields) == TRUE)
		{
			$arr = array();
			foreach($fields as $field)
			{
				$arr[] = $this->mapFieldToRow($field, $tablename);
			}
			
			$sql_fields = implode(', ', $arr);
		}
		else 
		{
			$sql_fields = $fields;
		}
		
		$sql_where = '';
		if(is_array($where) == TRUE)
		{
			if(array_key_exists('id', $where) && $where['id'] <= 0)
			{
				return FALSE;
			}
			
			$arr = array();
			foreach($where as $field => $value)
			{
				$arr[] = $this->mapFieldToRow($field, $tablename)." = '".$value."'";
			}
			
			$sql_where = implode(' AND ', $arr);
		}
		else 
		{
			$sql_where = $where;
		}
		
		$sql = str_replace(
			array(
				'{tablename}',
				'{fields}',
				'{where}'
			),
			array(
				$tablename,
				$sql_fields,
				$sql_where
			),
			$sql
		);
					
		return $sql;
	}
	
	/**
	 * Builds the SQL statements to delete an item.
	 * If id is used as $field it will be checked if > 0.
	 * Field names are mapped automatically to table rows.
	 * If parameter $where is an array it is used as field
	 * to generate the where clause. Otherwise the
	 * parameter is a string it is used as it is. 
	 * If $foreignkey is set, the SQL statement is generated with
	 * foreignkey relation.
	 * @param string $tablename 
	 * @param string|array $fields 
	 * @param string $foreignkey
	 * @return string|boolean Returns the SQL string. If error occurs returns FALSE.
	 */
	protected function _getDeleteSql($tablename, $where, $foreignkey = '')
	{
		$sql = $this->tables[$tablename]['config']['sql']['delete'];
		
		if ($tablename == '' || $sql == '') 
		{
			return FALSE;
		}
		
		$sql_where = '';
		if($foreignkey != '' && $this->_getForeignkeyValue($foreignkey, $tablename) !== FALSE)
		{
			$sql_where = $foreignkey." = '".$this->_getForeignkeyValue($foreignkey, $tablename)."'";
		}
		else if(is_array($where) == TRUE)
		{
			if(array_key_exists('id', $where) && $where['id'] <= 0)
			{
				return FALSE;
			}
			
			$arr = array();
			foreach($where as $field => $value)
			{
				$arr[] = $this->mapFieldToRow($field, $tablename)." = '".$value."'";
			}
			
			$sql_where = implode(' AND ', $arr);
		}
		else 
		{
			$sql_where = $where;
		}
		
		$sql = str_replace(
			array(
				'{tablename}',
				'{fields}',
				'{where}'
			),
			array(
				$tablename,
				$sql_fields,
				$sql_where
			),
			$sql
		);
					
		return $sql;
	}
	
	
	/**
	 * Splits an pipe seperated string to an array.  
	 * @param string $str 
	 * @param array $definition 
	 * @param string $sep 
	 * @return array Returns the array. If something goes wrong an empty array is returned.
	 */
	protected function _pipeStrToArray($str, $definition, $sep = '@@')
	{
		if (! is_string($str))
		{
			return array();
		}
		
		$out = array();
		$str = trim($str);
		
		if ($str == '')
		{
			return $out;
		}
		$rows = explode("\n", $str);

		$i = 0;
		foreach ($rows AS $row) 
		{
			$colums = explode($sep, $row);
			foreach ($definition AS $index => $fieldname)
			{
				if (array_key_exists($index, $colums))
				{
					$out[$i][$fieldname] = trim($colums[$index]);
				}
				else
				{
					$out[$i][$fieldname] = '';
				}

			}
			++$i;
		}

		return $out;
	}
	
	/**
	 * Converts an array to an pipe seperated string.
	 * @param array $arr 
	 * @param string $sep 
	 * @return string Returns the string. If no array given, returns an empty string. 
	 */
	protected function _arrToPipeStr($arr, $sep = '@@')
	{
		$out = '';
		
		if (! is_array($arr))
		{
			return $out;
		}
	
		foreach ($arr AS $rows) 
		{
			if (! is_array($rows))
			{
				continue;
			}
			
			$out .= implode($sep, $rows). "\n";
		}
	
		return $out;
	}
	
	/**
	 * Serializes an array to a string
	 * @param array $arr 
	 * @return string Returns the serialized string
	 */
	protected function _serialize($arr)
	{
		return addslashes( serialize( $arr ) );
	}
	
	/**
	 * Unserializes an string to an array
	 * @param string $str 
	 * @return array Returns the unserialized array
	 */
	protected function _unserialize($str)
	{
		return unserialize(stripslashes($str));
	}
}
?>