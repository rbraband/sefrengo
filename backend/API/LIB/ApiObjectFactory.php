<?php 
// File: $Id: class.SF_API_ObjectFactory.php 213 2009-07-26 21:57:11Z bjoern $
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
// + Autor: $Author: bjoern $
// +----------------------------------------------------------------------+
// + Revision: $Revision: 213 $
// +----------------------------------------------------------------------+
// + Description: The ApiObjectFactory
// +----------------------------------------------------------------------+
// + Changes:
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+
/**
* The ApiObjectFactory
*
* This Class provide the general ApiObjectFactory.
* Any API-Object can load with this Class.
*
* @name ApiObjectFactory
*/
class SF_LIB_ApiObjectFactory
{
	/**
	* API Path
	*
	* The API Path identifier.
	*
	* @param  string
	*/
	protected $api_path = '';
	
	/**
	* Object Store
	*
	* The Common Class Store.
	*
	* @param  object
	*/
	protected $object_store;

	/**
	 * Class prefix
	 * 
	 * Prefix for to the class name of API classes 
	 * 
	 * @param string
	 */
	protected $class_prefix = 'SF';
	
	
	/**
	* Common Class Constructor
	*
	* The Class Constructor.
	*
	* @access protected 
	* @param string $api_path
	* @param (object) $object_store
	*/
	public function __construct($api_path, $object_store)
	{
		$this->api_path = $api_path;
		$this->object_store = $object_store;
	} 
	
	
	public function addIncludePath($path, $high_prio = false)
	{
		//set include pathes
		$ini_separator = strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? ';' : ':';
		
		$ini_original = ini_get('include_path');
		$ini_original = ( strlen($ini_original) > 0 ) ? $ini_original. $ini_separator: '';
		
		if ($high_prio)
		{
			$ini_original = preg_replace('#^\.'.$ini_separator.'#', '', $ini_original);
			ini_set('include_path', '.'. $ini_separator . $path . $ini_separator .$ini_original);
		}
		else
		{
			ini_set('include_path', $ini_original . $path . $ini_separator);
		}
		
		return true;
	}

	/**
	* Require Class
	*
	* Include an required File if exists in a holy black hole.
	*
	* @deprecated Use the function includeClass instead
	* 
	* @access protected
	* @param string $package
	* @param string $classname
	* @return (boolean)
	*/
	public function requireClass($package, $classname)
	{
		return $this->includeClass($package, $classname);
	}

	/**
	* Include Class
	*
	* Include an required File if exists in a holy black hole.
	*
	* @access protected
	* @param string $package
	* @param string $classname
	* @return (boolean)
	*/
	public function includeClass($package, $classname)
	{
	   $file = strtoupper($package) . '/' . $classname . '.php';
	   
	   include_once $file;
	   
	   return true;
	}
	
	public function classExists($package, $classname)
	{
		$include_paths = explode(PATH_SEPARATOR, ini_get('include_path'));
		
		$file = strtoupper($package) . '/' . $classname . '.php';

		foreach ($include_paths as $path) 
		{
			$include = $path.DIRECTORY_SEPARATOR.$file;
			
	   		if (is_file($include) && is_readable($include)) 
	   		{
	   			return TRUE;
			}
		}

		return FALSE;
	}
	

	/**
	* Get Object Forced
	*
	* Returns an Object, forced new Object. Deprecated, replaced with getObject
	*
	* @access public
	* @deprecated
	* @see getObject
	*/   
	public function getObjectForced($package, $classname, $subclassname = null, $params = null)
	{
		return $this->getObject($package, $classname, $subclassname, $params);						 	
	}
	/**
	* Get Object
	*
	* Returns an Object, forced new Object.
	*
	* @access public
	* @deprecated
	* @param string $package
	* @param string $classname
	* @param string $subclassname , optional; standart: null
	* @param array  $params , optional; standart: null
	* @return object
	*/
	public function getObject($package, $classname, $subclassname = null, $params = null)
	{
		$cla = ($subclassname == null) ? $classname : $subclassname;
		$cla = $this->class_prefix . '_' . str_replace('/', '_', strtoupper($package))
			 . '_' . $cla; 
		// handle singleton
		if ($this->object_store->isStored($cla, 'default'))
		{
			$obj = $this->object_store->get($cla, 'default');
			if ($obj->_API_objectIsSingleton())
			{
				if ($obj->_API_isObjectBridge())
				{
					// handle bridge object
					$bridge = $obj->_API_getBridgeObject();
					return $bridge;
				}
				else
				{
					// singleton object
					return $obj;
				} 
			}
			unset($obj); 
		} 
		if ($this->includeClass($package, $classname))
		{
			if (false === ($obj = $this->_getNewObject($cla, $params))) return false; 
			
			if ($obj->_API_isObjectBridge())
			{
				// handle bridge object
				$bridge = $obj->_API_getBridgeObject();
				return $bridge;
			}
			else if ($obj->_API_objectIsSingleton())
			{
				$obj->_API_instance();
				$this->object_store->add($cla, 'default', $obj);
				return $obj;
			}
			else
			{
				$obj->_API_instance();
				return $obj;
			} 
		}
		else
		{
			return false;
		} 
	} 

	/**
	* Get Object Cache
	*
	* Returns an Object, check for stored Object.
	*
	* @access public
	* @param string $package
	* @param string $classname
	* @param string $subclassname , optional; standart: null
	* @param array  $params , optional; standart: null
	* @param string $cache_alias , optional; standart: 'default'
	* @return object
	*/
	public function getObjectCache($package, $classname, $subclassname = null, $params = null, 
						$cache_alias = 'default')
	{
		$cla = ($subclassname == null) ? $classname : $subclassname;
		$cla = $this->class_prefix . '_' . str_replace('/', '_', strtoupper($package)) 
			 . '_' . $cla;

		if ($this->object_store->isStored($cla, $cache_alias))
		{
			$obj = $this->object_store->get($cla, $cache_alias);
		}
		else
		{
			if ($this->includeClass($package, $classname))
			{
				if (false === ($obj = $this->_getNewObject($cla, $params))) return false; 
				// force singleton
				if ($obj->_API_objectIsSingleton())
				{
					if ($this->object_store->isStored($cla, 'default'))
					{
						$obj = $this->object_store->get($cla, 'default');
					}
					else
					{
						$this->object_store->add($cla, 'default', $obj);
					} 
					// normal cache
				}
				else
				{
					$this->object_store->add($cla, $cache_alias, $obj);
				} 
			}
			else
			{
				// object does not exist
				return false;
			} 
		} 

		if ($obj->_API_isObjectBridge())
		{
			// handle bridge object
			$bridge = $obj->_API_getBridgeObject();
			return $bridge;
		}
		else
		{
			// standard object
			$obj->_API_instance();
			return $obj;
		} 
	} 
	
	public function objectExistsInCache($package, $classname, $subclassname = null, $cache_alias = 'default')
	{
		$cla = ($subclassname == null) ? $classname : $subclassname;
		$cla = $this->class_prefix . '_' . str_replace('/', '_', strtoupper($package)) 
			 . '_' . $cla;

		return $this->object_store->isStored($cla, $cache_alias);
	}
	
	public function callMethod($package, $classname, $subclassname = null, $params = null, $method, $methodparams = null)
	{
			$obj = $this->getObjectCache($package, $classname, $subclassname, $params);
			return $this->_callMethod($obj, $method, $methodparams);								
											
	}
	
	public function callMethodCache($package, $classname, $subclassname = null, $params = null, $method, 
										$methodparams = null, $cache_alias = 'default')
	{
			$obj = $this->getObjectCache($package, $classname, $subclassname, $params, $cache_alias);
			return $this->_callMethod($obj, $method, $methodparams);											
											
	}

	/**
	* Unload Object
	*
	* Destroy an Object, check for stored Object.
	*
	* @access public
	* @param string $package
	* @param string $classname
	* @param string $subclassname , optional; standart: null
	* @param string $cache_alias , optional; standart: 'default'
	* @return (boolean) 
	*/
	public function unloadObject($package, $classname, $subclassname = null, $cache_alias = 'default')
	{
		$cla = ($subclassname == null) ? $classname : $subclassname;
		$cla = $this->class_prefix . '_' . str_replace('/', '_', strtoupper($package))
			 . '_' . $cla;

		return $this->object_store->unload($cla, $cache_alias);
	} 

	/**
	* Unload Object Store
	*
	* Destroy any Object from Store.
	*
	* @access public
	* @return (boolean) 
	*/
	public function unloadAll($cache_alias = 'all')
	{
		return $this->object_store->unloadAll($cache_alias);
	} 

	/**
	* PRIVATE METHODS START HERE
	*/
	
	/**
	* &Get new Object
	*
	* Returns an Object, by given name and params.
	*
	* @access private
	* @param string $full_classname
	* @param array  $params
	* @return object 
	*/
	protected function &_getNewObject($full_classname, &$params)
	{
		$obj = '';
		if (is_array($params))
		{
			$keys = array_keys($params);
			$c = count($keys);
			$pstring = '($params["' . $keys['0'] . '"]';
			for ($i = 1; $i < $c;++$i)
			{
				$pstring .= ',$params["' . $keys[$i] . '"]';
			} 
			$pstring .= ');';
		}
		else
		{
			$pstring = '();';
		}
		
		$to_eval = '$obj = new ' . $full_classname . $pstring;
		eval($to_eval);
		
		if ($obj->_API_checkObject()) return $obj;
		
		return false;
	}
	
	protected function _callMethod(&$obj, &$method, &$params)
	{
		if (is_array($params))
		{
			$keys = array_keys($params);
			$c = count($keys);
			$pstring = '($params["' . $keys['0'] . '"]';
			for ($i = 1; $i < $c;++$i)
			{
				$pstring .= ',$params["' . $keys[$i] . '"]';
			} 
			$pstring .= ');';
		}
		else if($params != null)
		{
			$pstring = '('.$params.');';
		}
		else
		{
			$pstring = '();';
		}
		$ret = false;
		$to_eval = '$ret = $obj->'.$method.$pstring;
		eval($to_eval);
		return $ret;
	}
	
} 
?>
