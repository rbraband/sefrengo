<?php 
// File: $Id: class.SF_API_ObjectStore.php 29 2008-05-11 19:19:53Z mistral $
// +----------------------------------------------------------------------+
// | Version: Sefrengo $Name:  $
// +----------------------------------------------------------------------+
// | Copyright (c) 2005 - 2007 sefrengo.org <info@sefrengo.org>           |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License                 |
// |                                                                      |
// | This program is subject to the GPL license, that is bundled with     |
// | this package in the file LICENSE.TXT.                                |
// | If you did not receive a copy of the GNU General Public License      |
// | along with this program write to the Free Software Foundation, Inc., |
// | 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA               |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// +----------------------------------------------------------------------+
// + Autor: $Author: mistral $
// +----------------------------------------------------------------------+
// + Revision: $Revision: 29 $
// +----------------------------------------------------------------------+
// + Description: The ApiObjectStore
// +----------------------------------------------------------------------+
// + Changes:
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+
/**
* The ApiObjectStore
*
* This Class provide the general ApiObjectStore.
* Any API-Object can stored within this Class.
*
* @name ApiObjectStore 
*/
class SF_LIB_ApiObjectStore
{
	/**
	* Store
	*
	* The Store identifier.
	* @param  array
	*/
	protected $_store = array();

	/**
	* Common Class Constructor
	*
	* The Class Constructor.
	*
	*/
	public function __construct()
	{ 
		
	}
	
	/**
	* Is Object stored
	* 
	* Returns true if the object stored in the Store,
	* determinate with $identifier and $alias.
	* 
	* @access protected 
	* @input (string) $identifier
	* @input (string) $alias
	* @return (boolean)
	*/
	public function isStored($identifier, $alias)
	{
		if(array_key_exists($identifier, $this->_store))
		{
			return is_object($this->_store[$identifier][strtolower($alias)]);
		}
		
		return FALSE;
	} 

	/**
	* Add Object to Store
	*
	* Store an given object. Returns true if the object is stored in the Store,
	* determinate with $identifier and $alias.
	*
	* @access protected 
	* @input (string) $identifier
	* @input (string) $alias
	* @input (object) $object
	* @return (boolean) 
	*/
	public function add($identifier, $alias, $object)
	{
		$this->_store[$identifier][strtolower($alias)] = $object;
		return $this->isStored($identifier, $alias);
	} 

	/**
	* &GetObject from Store
	*
	* Returns the object from the Store,
	* determinate with $identifier and $alias.
	*
	* @access public 
	* @input (string) $identifier
	* @input (string) $alias
	* @return (mixed) 
	*/
	public function get($identifier, $alias)
	{
		if ($this->isStored($identifier, $alias))
		{
			$this->_store[$identifier][strtolower($alias)]->_API_instant();
			return $this->_store[$identifier][strtolower($alias)];
		} 
		
		return false;
	} 

	/**
	* Unload Object from Store
	*
	* Returns (boolean) true if the object unloaded from the Store,
	* determinate with $identifier and $alias.
	*
	* @access public 
	* @input (string) $identifier
	* @input (string) $alias , optional ; default: 'all'
	* @return (boolean) 
	*/
	public function unload($identifier, $alias = 'all')
	{
		if ($this->isStored($identifier, strtolower($alias)))
		{
			$this->_store[$identifier][strtolower($alias)]->_API_unload();
			$this->_store[$identifier][strtolower($alias)] = null;
			return true;
		}
		else if (strtolower($alias) == 'all' && is_array($this->_store[$identifier]))
		{
			foreach ($this->_store[$identifier] as $_alias)
			{
				$this->_store[$identifier][$_alias]->_API_unload();
				$this->_store[$identifier][$_alias] = null;
			}
			return true; 
		} 

		return false;
	} 

	/**
	* Unload the Store
	*
	* Returns (boolean) true,
	* destroy all objects in Store by given $alias.
	*
	* @access public 
	* @input (string) $alias , optional ; default: 'all'
	* @return (boolean) 
	*/
	public function unloadAll($alias = 'all')
	{
		if (! is_array($this->_store))
		{
			return true;
		}
		
		$ikeys = array_keys($this->_store);
  
		foreach ($ikeys AS $iv)
		{
			$akeys = array_keys($this->_store[$iv]);
			foreach ($akeys as $av)
			{
				if ($alias != 'all' && $av != $alias) continue;
				$this->_store[$iv][$av]->_API_unload();
				$this->_store[$iv][$av] = null;
			} 
		} 

		return true;
	} 
} 

?>