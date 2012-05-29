<?php
class SF_LIB_Config extends SF_LIB_ApiObject 
{
	/**
	 * Auth object
	 * @var auth
	 */
	protected $auth;
	
	/**
	 * Database tables
	 * @var array
	 */
	protected $db = array();
	
	/**
	 * Current information like idclient or idlang
	 * @var array
	 */
	protected $env = array(
		'debug' => FALSE, //+
	
		'current_container_editable' => FALSE,

		'idcat' => FALSE,
		'idcatside' => FALSE,
		'idclient' => FALSE,
		'idlang' => FALSE,
		'idlay' => FALSE,
		'idpage' => FALSE,
		'idtpl' => FALSE,
		'idtplconf' => FALSE,
		'is_https' => FALSE,

		'is_backend' => TRUE,
		'is_backend_edit' => FALSE,
		'is_backend_preview' => FALSE,
		'is_frontend' => FALSE,
		'is_frontend_rewrite1' => FALSE,
		'is_frontend_rewrite2' => FALSE,
		'is_frontend_rewrite_no' => FALSE,
		'is_onepage' => FALSE,

		'langs' => FALSE, //+

		'path_backend' => FALSE,
		'path_backend_http' => FALSE,
		'path_frontend' => FALSE,
		'path_frontend_http' => FALSE,
		'path_frontend_fm' => FALSE,
		'path_frontend_fm_http' => FALSE,
		'path_frontend_css' => FALSE,
		'path_frontend_css_http' => FALSE,
		'path_frontend_js' => FALSE,
		'path_frontend_js_http' => FALSE,
		'perm_edit_page' => FALSE,//backenduser hace the perm to edit the page

		'sid' => FALSE,
		'sefrengo' => FALSE,

		'view' => FALSE,
	);
	
	/**
	 * CMS configuration
	 * @var array
	 */
	protected $cms = array();
	
	/**
	 * Client configuration
	 * @var array
	 */
	protected $client = array();
	
	/**
	 * Custom configuration
	 * @var array
	 */
	protected $custom = array('default' => array());
	
	/**
	 * Language configuration
	 * @var array
	 */
	protected $lang = array();
	
	/**
	 * Permission object
	 * @var $perm object
	 */
	protected $perm;
	
	/**
	 * Session object
	 * @var $sess object
	 */
	protected $sess;
	
	/**
	 * Template engine
	 * @var pear.php.net/Template/IT
	 */
	protected $tpl;
	
	/**
	 * Constructor retrieves the objects from the global PHP context
	 * @global array $cms_db
	 * @global array $cfg_cms
	 * @global array $cfg_client
	 * @global array $cfg_lang
	 * @global integer $client
	 * @global integer $lang
	 * @global integer $idcatside
	 * @global object $sess
	 * @global object $auth
	 * @global object $perm
	 * @global object $tpl
	 * @return void
	 */
	public function __construct()
	{
		$this->_API_setObjectIsSingleton(TRUE);
		
		$this->auth = (is_object($GLOBALS['auth'])) ? $GLOBALS['auth'] : FALSE;
		$this->db = (is_array($GLOBALS['cms_db'])) ? $GLOBALS['cms_db'] : array();
		$this->cms = (is_array($GLOBALS['cfg_cms'])) ? $GLOBALS['cfg_cms'] : array();
		$this->client = (is_array($GLOBALS['cfg_client'])) ? $GLOBALS['cfg_client'] : array();
		$this->lang = (is_array($GLOBALS['cfg_lang'])) ? $GLOBALS['cfg_lang'] : array();
		$this->perm = (is_object($GLOBALS['perm'])) ? $GLOBALS['perm'] : FALSE;
		$this->sess = (is_object($GLOBALS['sess'])) ? $GLOBALS['sess'] : FALSE;
		$this->tpl = (is_object($GLOBALS['tpl'])) ? $GLOBALS['tpl'] : FALSE;
		
		// calculating the debug mode may change
		$this->env['debug'] = $GLOBALS['cfg_cms']['display_errors'];
		
		$this->env['idclient'] = $GLOBALS['client'];
		$this->env['idlang'] = $GLOBALS['lang'];
		
		// deprecated, use idpage instead
		$this->env['idcatside'] = $GLOBALS['idcatside'];
		$this->env['idpage'] = $GLOBALS['idcatside'];
		
		// get extra config data (only available through this class)
		if(! array_key_exists('langs', $this->client))
		{
			$this->client['langs'] = $this->getLangsForClient( $this->env('idclient') );
		}
	}

	/**
	 * All config arrays and objects can set completly by hand here. But be carefull. If an array or
	 * object will be set, the old one will completly overwritten.
	 * @param string $type possible are 'auth', 'perm', 'sess', 'tpl', 'db', 'cms', 'client', 'lang', 'env'
	 * @param mixed $val
	 * @return boolean
	 */
	public function set($type, $val)
	{
		$ret = FALSE;

		$possible = array('auth', 'perm', 'sess', 'tpl', 'db', 'cms', 'client', 'lang', 'env');

		if(in_array($type, $possible))
		{
			$this->$type = $val;
			$ret = TRUE;
		}

		return $ret;
	}

	/**
	 * Single config vals can set here
	 * @param string $type possible are 'db', 'cms', 'client', 'lang','env'
	 * @param string $key
	 * @param mixed $val
	 * @return boolean
	 */
	public function setVal($type, $key, $val)
	{
		$ret = FALSE;

		$possible = array('db', 'cms', 'client', 'lang','env');

		if(in_array($type, $possible))
		{
			$this->{$type}[$key] = $val;
			$ret = TRUE;
		}

		return $ret;
	}
	
	/**
	 * Returns the auth values to the given key.
	 * @param string $key 
	 * @param boolean $default
	 * @return boolean
	 */
	public function auth($key, $default= FALSE)
	{
		if (!is_null($this->auth->auth) && array_key_exists($key, $this->auth->auth))
		{
			return $this->auth->auth[$key];
		}
		
		return $default;
	}
	
	/**
	 * Returns the auth object
	 * @return object
	 */
	public function authObj()
	{
		return $this->auth;
	}
	
	/**
	 * Returns the perm object.
	 * @return object
	 */
	public function perm()
	{
		return $this->perm;
	}
	
	/**
	 * Returns the sess object.
	 * @return object
	 */
	public function sess()
	{
		return $this->sess;
	}
	
	/**
	 * Returns the tpl object.
	 * @return object
	 */
	public function tpl()
	{
		return $this->tpl;
	}
	
	/**
	 * Returns the database name to the given key.
	 * @param string $key  
	 * @param boolean $default 
	 * @return string|boolean Returns the found value or default value FALSE.
	 */
	public function db($key, $default= FALSE)
	{
        //todo - some warnings in the apache errorlog, that $this->db is not an array - this is a workaround to control this
		if (! is_array($this->db))
        {
            return $default;
        }


        if (array_key_exists($key, $this->db))
		{
			return $this->db[$key];
		}
		
		return $default;
	}
	
	/**
	 * Returns the cms configuration to the given key.
	 * @param string $key  
	 * @param boolean $default 
	 * @return string|boolean Returns the found value or default value FALSE.
	 */
	public function cms($key, $default= FALSE)
	{
		if (array_key_exists($key, $this->cms))
		{
			return $this->cms[$key];
		}
		
		return $default;
	}
	
	/**
	 * Returns the client configuration to the given key.
	 * @param string $key  
	 * @param boolean $default 
	 * @return string|boolean Returns the found value or default value FALSE.
	 */
	public function client($key, $default= FALSE)
	{
		if (array_key_exists($key, $this->client))
		{
			return $this->client[$key];
		}
		
		return $default;
	}
	
	/**
	 * Returns environment variables to the given key.
	 * @param string $key  
	 * @param boolean $default 
	 * @return string|boolean Returns the found value or default value FALSE.
	 */
	public function env($key, $default= FALSE)
	{
		if (array_key_exists($key, $this->env))
		{
			return $this->env[$key];
		}
		
		return $default;
	}
	
	/**
	 * Returns the language configuration to the given key.
	 * @param string $key  
	 * @param boolean $default 
	 * @return string|boolean Returns the found value or default value FALSE.
	 */
	public function lang($key, $default= FALSE)
	{
		if (array_key_exists($key, $this->lang))
		{
			return $this->lang[$key];
		}
		
		return $default;
	}
	
	/**
	 * Returns the custom configuration to the given key.
	 * The configuration could be divided into different namespaces.
	 * @param string $key  
	 * @param boolean $default 
	 * @param string $namespace 
	 * @return string|boolean Returns the found value or default value FALSE.
	 */
	public function custom($key, $default= FALSE, $namespace = 'default')
	{
		if( is_array($this->custom[$namespace]) ) 
		{
			if (array_key_exists($key, $this->custom[$namespace]))
			{
				return $this->custom[$namespace][$key];
			}
		}
		
		return $default;
	}
	
	/**
	 * Loads a custom configuration from a PHP file into a
	 * given namespace. If the namespace is empty it is set
	 * to 'default'. With pathprefix you're able to choose
	 * the right location to the file, so $path is only the
	 * relative path.  
	 * @param string $path
	 * @param string $namespace
	 * @param string $pathprefix
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function loadCustomByFile($path, $namespace, $pathprefix= '')
	{
		$namespace = ($namespace == '') ? 'default' : $namespace;
		
		switch($pathprefix)
		{
			case 'api':
				$pathprefix = $this->cms['path_base'].$this->cms['path_backend_rel'].'API/';
				break;
			case 'backend':
				$pathprefix = $this->cms['path_base'].$this->cms['path_backend_rel'];
				break;
			case 'plugin':
				$pathprefix = $this->cms['path_base'].$this->cms['path_backend_rel'].'plugins/';
				break;
			case 'frontend':
				$pathprefix = $this->cms['path_base'].$this->client['path_rel'];
				break;
			default:
				$pathprefix = '';
				break;
				 	
		}
		
		$path = $pathprefix .$path;
		
		if (file_exists($path))
		{
			include_once $path;
		}
		
		if (isset($cfg))
		{
			if (is_array($cfg))
			{
				if (! array_key_exists($namespace, $this->custom))
				{
					$this->custom[$namespace] = array();
				}
				
				$this->custom[$namespace] = array_merge($this->custom[$namespace], $cfg);
				
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * The function adds an given array to the custom
	 * configuration in a specified namespace. If the
	 * namespace is empty it is set to 'default'.
	 * @param array $array
	 * @param string $namespace
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function loadCustomByArray($array, $namespace)
	{
		$namespace = ($namespace == '') ? 'default' : $namespace;
		
		if (is_array($array))
		{
			if (! array_key_exists($namespace, $this->custom))
			{
				$this->custom[$namespace] = array();
			}
			
			$this->custom[$namespace] = array_merge($this->custom[$namespace], $array);
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Gets an list with all available idlangs for the given idclient.
	 * If no idclient is given the current idclient is used.
	 * 
	 * @param integer $idclient default = 0
	 * @return array Returns a plain array with all idlangs for the client
	 */
	public function getIdLangs( $idclient = 0 )
	{
		$langs = array();
		$return = array();
		$idclient = ($idclient == 0) ? $this->env('idclient') : $idclient;
		
		if(! is_numeric($idclient) || $idclient <= 0 )
		{
			return $return;
		}
		
		if($idclient == $this->env('idclient') && array_key_exists('langs', $this->client))
		{
			$langs = $this->client['langs'];
		}
		else
		{
			$langs = $this->getLangsForClient( $idclient );
		}
		
		foreach($langs as $lang)
		{
			array_push($return, $lang['idlang']);
		}
		
		return $return;
	}
	
	/**
	 * Gets the start idlang for the given idclient.
	 * If no idclient is given the current idclient is used.
	 * 
	 * @param integer $idclient default = 0
	 * @return integer Returns an integer > 0 on success. Otherwise returns 0. 
	 */
	public function getStartIdLang( $idclient = 0 )
	{
		$langs = array();
		$idclient = ($idclient == 0) ? $this->env('idclient') : $idclient;
		
		if(! is_numeric($idclient) || $idclient <= 0 )
		{
			return 0;
		}
		
		if($idclient == $this->env('idclient') && array_key_exists('langs', $this->client))
		{
			$langs = $this->client['langs'];
		}
		else
		{
			$langs = $this->getLangsForClient( $idclient );
		}
		
		foreach($langs as $lang)
		{
			if($lang['is_start'] == true)
			{
				return $lang['idlang'];
			}
		}
		
		return 0;
	}
	
	/**
	 * Generates an multidimensional array with all
	 * available langs for the given client. It also
	 * contains the start lang.
	 * @param integer $idclient
	 * @return array Returns an multidimensional array with the idlangs and startlang as boolean.
	 */
	public function getLangsForClient( $idclient )
	{
		$langs = array();
		
		if(! is_numeric($idclient) || $idclient <= 0 )
		{
			return $langs;
		}
		
		$sql = "SELECT cl.idlang, l.is_start, l.name
				FROM ".$this->db('clients_lang')." AS cl
				LEFT JOIN ".$this->db('lang')." AS l
					ON (cl.idlang = l.idlang)
				WHERE cl.idclient = '".$idclient."';";
		
		$db = sf_api('LIB', 'Ado');
		$rs = $db->Execute($sql);
		unset($db);
	
		if ($rs === FALSE || $rs->EOF )
		{
			return $langs;
		}
		
		while (! $rs->EOF) 
		{
			array_push($langs, array(
				'idlang' => $rs->fields['idlang'],
				'name' => $rs->fields['name'],
				'is_start' => ($rs->fields['is_start'] == '1') ? TRUE : FALSE,
				'is_current' => ($rs->fields['idlang'] == $this->env('idlang')) ? TRUE : FALSE
			));
			
			$rs->MoveNext();
		}
		$rs->Close();
		
		return $langs;
	}
	
	
}
?>