<?php 

/**
 * Convenience class for session handling.
 * reads, writes and deletes session entries at the starting 
 * position defined under $sess_cfg['session_name'].
 *
 * How to use:
 *
 * the array path used in the methodes is a dot
 * seperated string. E.g.: "one.two.three"
 *
 * initialise the session helper:
 * $this->mySession = sf_api('LIB', 'Session');
 * 
 * store a value in the Session
 * $value = array('one' => 'two');
 * $this->mySession->write('my.path', $value);
 *
 * read from the Session
 * print_r($this->mySession->read('my.path'));
 * prints:
 * Array ( [one] => two )
 *
 * print_r($this->mySession->read());
 * prints:
 * Array ( [my] => Array ( [path] => Array ( [one] => two ) ) ) 
 *
 * delete from Session
 * $this->mySession->delete('my.path');
 *
 * print_r($this->mySession->read());
 * prints now:
 * Array ( [my] => Array () ) 
 */
class SF_LIB_Session extends SF_LIB_ApiObject
{
	/**
	 * Global Config
	 */
	protected $cfg;
	
	/**
	 * Configuration of the Lib
	 */
	protected $sess_cfg = array(
		'session_name' => 'sf_usersession'
	);
	
	/**
	 * Constructor sets up {@link $cfg}
	 * @return void
	 */
	public function __construct() //{{{ 
	{
		$this->_API_is_singleton = true;
		$this->cfg = sf_api('LIB', 'Config');
	} //}}}
	
	/**
	 * Stores $val by $path in the session object.
	 * $path has the syntax "one.two.thee" which evolves to array['one']['two']['three']
	 * 
	 * @param string $path
	 * @param boolean $val
	 * @param boolean $freeze if TRUE makes Session permanent
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function write($path = null, $val, $freeze = true) //{{{ 
	{
		$sess_name = $this->getSessionName();
			
		if( !isset($GLOBALS[$sess_name])
			|| !is_array($GLOBALS[$sess_name]) ) 
		{
			$GLOBALS[$sess_name] = array();
		}
		
		$GLOBALS[$sess_name] = $this->_setValInArray($GLOBALS[$sess_name], $path, $val);
		
		$this->cfg->sess()->register($sess_name);
			
		if( $freeze ) 
		{
			$this->cfg->sess()->freeze();
		}
		
		return true;
	} //}}}
	
	/**
	 * Reads the value stored under $path in the session object.
	 * $path has the syntax "one.two.thee" which evolves to array['one']['two']['three']
	 * 
	 * @param string $path
	 * @param boolean $val
	 * @return mixed Returns value stored under path
	 */
	public function read($path = null) //{{{ 
	{
		// Check Parameters
		if( !empty($path)
			&& !is_string($path) ) 
		{
			return false;
		}
		
		// Is the session Array set?
		if( !isset($GLOBALS[$this->getSessionName()]) )
		{
			$GLOBALS[$this->getSessionName()] = array();
		}
		
		// Return complete Session Array
		if( empty($path) ) 
		{
			return $GLOBALS[$this->getSessionName()];
		}
		
		// Return value spezified by path
		$sess_array = $GLOBALS[$this->getSessionName()];
		
		$session_val = $this->_getValFromArray($path, $sess_array);
		
		return $session_val;
	} //}}}
	
	/**
	 * Deletes the $path in the session object.
	 * $path has the syntax "one.two.thee" which evolves to array['one']['two']['three']
	 * 
	 * @param string $path
	 * @param boolean $freeze if TRUE makes Session permanent
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function delete($path = null, $freeze = true) //{{{ 
	{
		$sess_name = $this->getSessionName();
			
		if( !isset($GLOBALS[$sess_name])
			|| !is_array($GLOBALS[$sess_name]) ) 
		{
			$GLOBALS[$sess_name] = array();
		}
		
		$GLOBALS[$sess_name] = $this->_deleteValFromArray($GLOBALS[$sess_name], $path);
		
		$this->cfg->sess()->register($sess_name);
			
		if( $freeze ) 
		{
			$this->cfg->sess()->freeze();
		}
		
		return true;
	} //}}}
	
	/**
	* Returns the Session name defined in sess_cfg
	*
	* @return String session_name
	*/
	public function getSessionName() //{{{ 
	{
		return $this->sess_cfg['session_name'];
	} //}}}
	
	/**
	* gets a String in from of "token1.token2" and
	* returns it in array form
	*
	* @param array $container array to insert values into
	* @param String $path the path the value will be stored at
	* @param mixed $data the value stored at the end of the path 
	* @return mixed Returns Array on success FALSE on failure.
	*/                           
	protected function _setValInArray($container, $path, $data = null) //{{{ 
	{
		if( !is_string($path) 
			|| !is_array($container) ) 
		{
			return false;
		}
		
		$path_array = explode('.', $path);
		
		$_container =& $container;
		 
		foreach( $path_array as $i => $key ) 
		{
			if( $i === count($path_array) - 1 ) 
			{
				$_container[$key] = $data;
			} 
			else 
			{
				if( !isset($_container[$key]) ) 
				{
					$_container[$key] = array();
				}
				
				$_container =& $_container[$key];
			}
		}
		
		return $container;
	} //}}}
	
	/**
	* returns Value from an Array defined by Path
	*
	* @param String $path the path the value will be retrieved
	* @param Array $container the Array the value will be retrieved from
	* @return mixed Returns mixed on success FALSE on failure.
	*/                           
	protected function _getValFromArray($path, $container) //{{{ 
	{                           
		if( !is_string($path) 
			|| !is_array($container) ) 
		{
			return false;
		}
		
		$path_array = explode('.', $path);
		
		foreach( $path_array as $value ) 
		{
			if( !isset($container[$value]) ) 
			{
				return false;
			}
			
			$container = $container[$value];
		}
		
		return $container;
	} //}}}
	 
	/**
	* deletes Value from an Array defined by Path
	*
	* @param array $container array to delete from
	* @param String $path path where the value will be deleted
	* @return mixed Returns mixed on success FALSE on failure.
	*/                           
	protected function _deleteValFromArray($container, $path = null) //{{{ 
	{
		if( !is_string($path) 
			|| !is_array($container) ) 
		{
			return false;
		}
		
		$path_array = explode('.', $path);
		
		$_container =& $container;
		 
		foreach( $path_array as $i => $key ) 
		{
			if( $i === count($path_array) - 1 ) 
			{
				unset($_container[$key]);
			} 
			else 
			{
				if( !isset($_container[$key]) )
				{
					return $container;
				}
				
				$_container =& $_container[$key];
			}
		}
		
		return $container;
	} //}}}
}

?>
