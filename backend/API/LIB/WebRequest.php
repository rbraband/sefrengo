<?php
class SF_LIB_WebRequest extends SF_LIB_ApiObject {
	
	/**
	 * Is magic_quotes_gpc active
	 * @var boolean
	 */
	private $mq_gpc_is_active;
	
	/**
	 * $_REQUEST
	 * @var array
	 */
	private $req;
	
	/**
	 * $_POST
	 * @var array
	 */
	private $post;
	
	/**
	 * $_GET
	 * @var array
	 */
	private $get;
	
	/**
	 * $_COOKIE
	 * @var array
	 */
	private $cookie;
	
	/**
	 * $_FILES
	 * @var array
	 */
	private $files;
	
	/**
	 * $_SESSION
	 * @var array
	 */
	private $session;
	
	/**
	 * Constructor get the PHP objects and stores them.
	 * The object is defined as Singleton.
	 * @global array $_REQUEST
	 * @global array $_POST
	 * @global array $_GET
	 * @global array $_COOKIE
	 * @global array $_FILES
	 * @global array $_SESSION
	 */
	public function __construct()
	{
		$this->_API_setObjectIsSingleton(TRUE);
		
		$this->mq_gpc_is_active = get_magic_quotes_gpc();
		
		$this->req = $_REQUEST;
		$this->post = $_POST;
		$this->get = $_GET;
		$this->cookie = $_COOKIE;
		$this->files = $_FILES;
		$this->session =  $_SESSION;
	}
	
	/**
	 * Get an value by name from the $_REQUEST object
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	public function req($name, $default = FALSE)
	{
		return $this->getVal($this->req, $name, $default);
	}
	
	/**
	 * Get an value by name from the $_REQUEST object and
	 * stripes PHP and HTML code out
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	public function reqS($name, $default = FALSE)
	{
		return $this->getValSecure($this->req, $name, $default);
	}
	
	/**
	 * Get an value by name from the $_GET object
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	public function get($name, $default = FALSE)
	{
		return $this->getVal($this->get, $name, $default);
	}
	
	/**
	 * Get an value by name from the $_GET object and
	 * stripes PHP and HTML code out
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	public function getS($name, $default = FALSE)
	{
		return $this->getValSecure($this->get, $name, $default);
	}
	
	/**
	 * Get an value by name from the $_POST object
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	public function post($name, $default = FALSE)
	{
		return $this->getVal($this->post, $name, $default);
	}
	
	/**
	 * Get an value by name from the $_POST object and
	 * stripes PHP and HTML code out
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	public function postS($name, $default = FALSE)
	{
		return $this->getValSecure($this->post, $name, $default);
	}
	
	/**
	 * Get an value by name from the $_COOKIE object
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	public function cookie($name, $default = FALSE)
	{
		return $this->getVal($this->cookie, $name, $default);
	}
	
	/**
	 * Get an value by name from the $_COOKIES object and
	 * stripes PHP and HTML code out
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	public function cookieS($name, $default = FALSE)
	{
		return $this->getValSecure($this->cookie, $name, $default);
	}
	
	/**
	 * Get an value by name from the $_FILES object
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	public function files($name, $default = FALSE)
	{
		return $this->getVal($this->files, $name, $default);
	}
	
	/**
	 * Get an value by name from the $_FILES object and
	 * stripes PHP and HTML code out
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	public function filesS($name, $default = FALSE)
	{
		return $this->getValSecure($this->files, $name, $default);
	}
	
	/**
	 * Get an value by name from the $_SESSION object
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	public function session($name, $default = FALSE)
	{
		return $this->getVal($this->session, $name, $default);
	}
	
	/**
	 * Get an value by name from the $_SESSION object and
	 * stripes PHP and HTML code out
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	public function sessionS($name, $default = FALSE)
	{
		return $this->getValSecure($this->session, $name, $default);
	}
	
	/**
	 * Get an value as boolean by name and given method.
	 * @param string $name 
	 * @param string $method possible: r, g, p, c, f, s
	 * @return boolean Returns the value as boolean.
	 */
	public function asBoolean($name, $method = 'r')
	{
		$method = $this->_getMethodObject($method);
		if( $this->getVal($method, $name, FALSE) === FALSE ||
			$this->getVal($method, $name, FALSE) === 'false')
		{
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * Get an value as int by name and given method.
	 * @param string $name 
	 * @param string $method possible: r, g, p, c, f, s
	 * @param mixed $default
	 * @return integer Returns the value as integer.
	 */
	public function asInt($name, $method = 'r', $default = FALSE)
	{
		$method = $this->_getMethodObject($method);
		return (int) $this->getVal($method, $name, $default);
	}
	
	/**
	 * Get an value as encoded string by name and given method.
	 * @param string $name 
	 * @param string $method possible: r, g, p, c, f, s
	 * @param mixed $default
	 * @return string Returns the value as encoded string.
	 */
	public function asEntityEncoded($name, $method = 'r', $default = FALSE)
	{
		$method = $this->_getMethodObject($method);
		return htmlentities($this->getVal($method, $name, $default), ENT_COMPAT, 'utf-8');
	}
	
	/**
	 * Searches the name in variable. 
	 * @param array $method 
	 * @param string $name 
	 * @param mixed $default 
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	private function getVal($method, $name, $default = FALSE) {
		$v = '';
		$key_exists = FALSE;
		
		if (is_array($name)) 
		{
			$keys = array_keys($name);
			$c = count($keys);
			$key_exists = array_key_exists($name[ $keys['0'] ], $method);
			$to_eval = '$v = $method["' . $name[ $keys['0'] ] . '"]';
			for ($i = 1; $i < $c;++$i) {
				$to_eval .= '["' . $name[ $keys[$i] ] . '"]';
			} 
			$to_eval .= ';';
			eval($to_eval);
		} 
		else 
		{
			$key_exists = array_key_exists($name, $method);
			$v = $method[$name];
		} 
		
		if ($key_exists) 
		{
			// fix magic quotes
			if ($this->mq_gpc_is_active) 
			{
				$v = $this->_fixMagicQuotes($v);
			} 
			
			// check UTF-8 encoding
			$v = $this->_checkUTF8($v);
			
			return $v;
		} 
		
		return $this->_checkUTF8($default);
	}
	
	/**
	 * Searches the name in variable. Stripes out PHP and HTML code.
	 * @param array $method 
	 * @param string $name 
	 * @param mixed $default
	 * @return string|mixed Returns the value as string, if name found.
	 * Otherwise returns the default value.
	 */
	private function getValSecure($method, $name, $default = FALSE)
	{
		$value = $this->getVal($method, $name, $default);
		
		if (is_array($value))
		{
			foreach ($value as $k => $v)
			{
				$value[$k] = str_replace(array('%3C%3F', '%3F%3E'), array('<?','?'.'>'), $value[$k]);
				$value[$k] = strip_tags($value[$k]);
			} 
		}
		else
		{
			$value = str_replace(array('%3C%3F', '%3F%3E'), array('<?','?'.'>'), $value);
			$value = strip_tags($value);
		}
		
		return $value;
	}
	
	/**
	 * Runs stripslashes on strings and arrays 
	 * @param array|string $value
	 * @return array|string Returns the incoming $value.
	 */
	private function _fixMagicQuotes($value)
	{
		if (is_array($value))
		{
			foreach ($value as $k => $v)
			{
				$value[$k] = $this->_fixMagicQuotes($value[$k]);
			} 
		}
		else
		{
			$value = stripslashes($value);
		}
		return $value;
	} 
	
	/**
	 * Checks if string or array is encoded in UTF-8.
	 * If not, try to encode them.
	 * @param array|string $value
	 * @return array|string Returns the converted incoming value.
	 */
	private function _checkUTF8($value)
	{
		if (is_array($value))
		{
			foreach ($value as $k => $v)
			{
				$value[$k] = $this->_checkUTF8($value[$k]);
			}
		}
		else
		{
			// only asccii 0-127 are in use
			if (! preg_match('/[\x80-\xff]/', $value))
			{
				return $value;
			} 

			$is_utf8 = preg_match('%([\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})%xs', $value) ? true : false; 
			if (! $is_utf8)
			{
				$value = utf8_encode($value);
			} 
		} 
		return $value;
	}
	
	/**
	 * Returns the method array by short value.
	 * Methods: {@link $post}, {@link $get}, {@link $cookie},
	 * {@link $files}, {@link $session}, {@link $req},
	 * @param string $method
	 * @return array
	 */
	private function _getMethodObject($method)
	{
		switch ($method) 
		{
			case 'p':
				return $this->post;
				break;
			case 'g':
				return $this->get;
				break;
			case 'c':
				return $this->cookie;
				break;
			case 'f':
				return $this->files;
				break;
			case 's':
				return $this->session;
				break;
			case 'r':
			default:
				return $this->req;
		}
	}
}
?>