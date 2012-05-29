<?php

$this->includeClass('MODEL', 'AbstractSqlCollection');

class SF_MODEL_ValueSqlCollection extends SF_MODEL_AbstractSqlCollection
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
		$this->setCfg('model', 'ValueSqlItem');

		// then call parent constructor to get tablefields
		parent::__construct();
	}
	
	/**
	 * Loads all configuration for the given group name. 
	 * @param string $group
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function getByGroup($group) 
	{
		$this->setFreefilter('group_name', $group);
		$this->setOrder('conf_sortindex');
		return $this->generate();
	}
	
	/**
	 * Loads all configuration for the given group name and keys. 
	 * @param string $group
	 * @param array $keys Is an array in the style array('key1' => 'myval', ...);
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function getByGroupAndKeys($group, $keys) 
	{
		$fields = $keys;
		$fields['group_name'] = $group;
		
		// at this place you may add a check if fields really exists in model
		
		foreach($fields as $key => $value)
		{
			$this->setFreefilter($key, mysql_real_escape_string($value));
		}
		
		$this->setOrder('conf_sortindex');
		return $this->generate();
	}
	
	/**
	 * If the collection is generated the function builds
	 * an associative array with the keys. 
	 * @return array Returns an asscociative array with the keys. Otherwise returns an empty array. 
	 */
	public function getAssocKeyArray()
	{
		$keyarray = array();
		
		if($this->getCount() > 0)
		{
			$iter = $this->getItemsAsIterator();
			while($iter->valid())
			{
				$itemobject = $iter->current();
				
				$key1 = $itemobject->getField('key1');
				$key2 = $itemobject->getField('key2');
				$key3 = $itemobject->getField('key3');
				$key4 = $itemobject->getField('key4');
				$value = $itemobject->getField('value');
				
				if($key2 == '' && $key1 != '')
				{
					$keyarray[$key1] = $value;
				}
				else if($key3 == '')
				{
					$keyarray[$key1][$key2] = $value;
				}
				else if($key4 == '')
				{
					$keyarray[$key1][$key2][$key3] = $value;
				}
				else if($key1 == '')
				{
					$keyarray[$key1][$key2][$key3][$key4] = $value;
				}
			
				$iter->next();
			}
		}
			
		return $keyarray;
	}
	
	/**
	 * Overwrites the creation of the client and lang clause.
	 * @see API/MODELS/SF_MODEL_AbstractSqlCollection#_getSqlClientLang($idclient, $idlang, $fields)
	 */
	protected function _getSqlClientLang($idclient, $idlang, $fields, $accept_id_zero = FALSE)
	{
		return parent::_getSqlClientLang(array($idclient, 0), array($idlang, 0), $fields); 
	}
}
?>