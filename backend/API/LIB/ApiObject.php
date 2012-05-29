<?php 
// File: $Id: class.SF_API_Object.php 29 2008-05-11 19:19:53Z mistral $
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
// + Description: The Common ApiObject
// +----------------------------------------------------------------------+
// + Changes:
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+
/**
* The Common ApiObject
*
* This Class provide the general SF_API_Object.
* Any API-Object must inherit this Class.
*
* @name ApiObject
*/
class SF_LIB_ApiObject
{
	/**
	* Class Name
	*
	* The Common Class identifier.
	*
	* @param  string
	*/
	protected $_API_name = '';

	/**
	* Debug Object
	*
	* This enable the debug feature.
	*
	* @param  object
	*/
	protected $_API_debug_object;
	
	/**
	* Singleton Flag
	*
	* This Flag enable the singleton feature.
	*
	* @param  boolean
	*/
	protected $_API_is_singleton = false;

	/**
	* Bridge Flag
	*
	* This Flag enable the bridging feature.
	*
	* @param  boolean
	*/
	protected $_API_is_object_bridge = false;
	
	/**
	* Error Message
	*
	* This string identify the Object-Errormessage.
	*
	* @param  string
	*/
	protected $_API_object_error_message = '';

	/**
	* Object version
	*
	* This string identify the SF_API_Object Version.
	*
	* @param  string
	*/
	protected $_API_object_version = '$Revision: 29 $';// need to overide in any API-Object
	
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
	* Set Object is singleton
	*
	* If this set (boolean) true the Classloader will handle the Object as an
	* singleton.
	*
	* @access protected
	* @input (boolean) $bool
	*/
	public function _API_setObjectIsSingleton($bool)
	{
		$this->_API_is_singleton = (boolean) $bool;
	} 

	/**
	* Is Object singleton
	* 
	* Returns true if the object should be handle as singleton,
	* otherwise false.
	* 
	* @access protected 
	* @return (boolean)
	*/
	public function _API_objectIsSingleton()
	{
		return $this->_API_is_singleton;
	} 

	/**
	* Set Object use bridge
	*
	* If this set (boolean) true the classloader will take the object
	* from the method _API_getBridgeObject().
	* 
	* @access protected 
	* @input (boolean) $bool
	*/
	public function _API_setObjectBridge($bool)
	{
		$this->_API_is_object_bridge = (boolean) $bool;
	} 

	/**
	* Trigger Error
	*
	* If this set (string) to an Errormessage it will push the message
	* in the Error Stack.
	* 
	* @access protected
	* @input (string) $string 
	*/
	public function _API_triggerObjectError($string)
	{
		;// overwrite me
	} 

	/**
	* Use Object bridge
	*
	* Returns true if the classloader debit the object from the method
	* _API_getBridgeObject()
	* 
	* @access protected
	* @return (boolean) 
	*/
	public function _API_isObjectBridge()
	{
		return $this->_API_is_object_bridge;
	} 

	/**
	* Get the bridge object
	* 
	* Return a new Object from bridged Object.
	*
	* @access public 
	* @return (object)
	*/
	public function &_API_getBridgeObject()
	{
		return new object;// overwrite me
	} 

	/**
	* Destroy Object
	* 
	* The API classloader call this method before the object will be
	* destroyed from the object Store.
	* 
	* @access public 
	* @uses Call this to safe close the object.	  
	*/
	public function _API_unload()
	{
		;// overwrite me
	} 

	/**
	* Instant Object
	*
	* The API classloader call this method before the object will 
	* be reanimate from the object Store.
	* 
	* @access public 
	* @uses Call this for reanimate arrays or objects.	  
	*/
	public function _API_instant()
	{
		;// overwrite me
	} 

	/**
	* Object Error 
	* 
	* The API classloader call this method when the object will crashed.
	* 
	* @access public
	* @uses Call this for API errorhandling.		  
	*/
	public function _API_error()
	{
		;// overwrite me
	} 

	/**
	* Check Instance
	*
	* The API classloader call this method after instance the object.
	* 
	* @access private
	* @return (boolean) 
	* @uses Call this for instance the object.
	*/
	public function _API_instance()
	{ 
		return SF_LIB_ApiObject::__construct();// overwrite me
	}
	
	/**
	* Provide Debug-Info
	*
	* The API classloader call this method to debug the object.
	* 
	* @access private
	* @input (object) 
	* @return (object)
	* @uses Call this for debug the object.
	*/
	public function _API_debug()
	{
		preg_match('/\$revision: ([^\$]+)* \$/si', $this->_API_object_version, $match);
		if ($match[1]) $_revision = $this->_API_name . ': v' . $match[1];
		else $_revision = $this->_API_object_version;
		
		if (is_object($this->_API_debug_object))
		{
			$this->_API_debug_object->collect($_revision);
		}
		else
		{
			return $_revision . "\n";
		}
	} 

	/**
	* Check Object
	*
	* The API classloader call this method to check the object.
	* 
	* @access public 
	* @return (boolean)
	* @uses Call this for check the object.
	*/
	public function _API_checkObject()
	{
		if (is_a($this, 'SF_LIB_ApiObject'))
		{
			$this->_API_name = get_class($this);
			return true;
		}
		return false;
	}
	
} 
?>