<?php
class SF_LIB_Lang extends SF_LIB_ApiObject 
{
	protected $lang = array('default'=>array());
	protected $cfg;
	
	public function __construct()
	{
		global $cms_lang;
		
		$this->_API_setObjectIsSingleton(TRUE);
		
		$this->langstrings['default'] = $cms_lang;
		$this->cfg = sf_api('LIB', 'Config');
	}
	
	public function get($key, $namespace = 'default')
	{
		
		if (is_string($key) == TRUE && array_key_exists($key, $this->langstrings[$namespace]))
		{
			return $this->langstrings[$namespace][$key];
		}
		
		return '';
	}
	
	public function getEnt($key, $namespace = 'default')
	{
		return htmlentities($this->get($key, $namespace), ENT_COMPAT, 'UTF-8');
	}
	
	public function getSpec($key, $namespace='default')
	{
		return htmlspecialchars($this->get($key, $namespace));
	}
	
	public function getAll($filterkey = '', $namespace = 'default')
	{
		$return = array();
		
		if (array_key_exists($namespace, $this->langstrings))
		{
			if($filterkey == '')
			{
				$return = $this->langstrings[$namespace];
			}
			else
			{
				foreach($this->langstrings[$namespace] as $key => $val)
				{
					if(strpos($key, $filterkey) === 0)
					{
						$return[$key] = $val;
					}
				}
			}
		}
		return $return;
	}
	
	public function loadByFile($path, $namespace, $pathprefix= '')
	{
		//global $cms_lang;
		
		$namespace = ($namespace == '') ? 'default' : $namespace;
		
		switch($pathprefix)
		{
			case 'backend':
				$pathprefix = $this->cfg->env('path_backend');
				break;
			case 'plugin':
				$pathprefix = $this->cfg->env('path_backend').'plugins/';
				break;
			case 'frontend':
				$pathprefix = $this->cfg->env('path_frontend');
				break;
			case 'lang_dir':
				$pathprefix = $this->cfg->env('path_backend').'lang/'.$this->cfg->cms('backend_lang').'/';
				break;
			case 'lang_defdir':
				$pathprefix = $this->cfg->env('path_backend').'lang/de/';
				break;
			default:
				$pathprefix = '';
				break;
				 	
		}
		
		$path = $pathprefix.$path;
		
		if (file_exists($path))
		{
			include_once $path;
		}
		
		if (isset($cms_lang))
		{
			if (is_array($cms_lang))
			{
				if (! array_key_exists($namespace, $this->langstrings))
				{
					$this->langstrings[$namespace] = array();
				}
				
				$this->langstrings[$namespace] = array_merge($this->langstrings[$namespace], $cms_lang);
				
				return TRUE;
			}
		}
		
		return FALSE;
	}

	/**
	 * Searches for the first char in every key and value of the given assoziative array.
	 * If found then replace the controller name in every key and value of the array.
	 * Afterwarts try to translate it into an langstring and replaces the original '#key' with 'key'.
	 * @param array $input
	 * @param string $ctr_name (optional)
	 * @param array $search (optional)
	 * @param array $replace (optional) 
	 * @param boolean $is_recursive_call (private)
	 * @return array Returns the given $input array with replaced values (if found).
	 */	
	public function replaceLangInArray($input, $ctr_name = '', $search = array(), $replace = array(), $is_recursive_call = FALSE)
	{
		if($is_recursive_call == FALSE)
		{
			$search = array_merge(array('#', '{ctr_name}'), $search);
			$replace = array_merge(array('', $ctr_name), $replace);
		}
		
		foreach($input as $key => $val)
		{
			$changed = FALSE;
				
			if(is_string($key) == TRUE && $key[0] == '#')
			{
				$changed = TRUE;
				// delete old key, otherwise you get '#key' and 'key'
				unset($input[$key]);
				
				$key = str_replace($search, $replace, $key);
				$key = ($this->get($key) != '') ? $this->get($key) : $key;
			}
			
			if(is_array($val) == TRUE)
			{
				$changed = TRUE;
				// recursion
				$val = $this->replaceLangInArray($val, $ctr_name, $search, $replace, TRUE);
			}
			else if(is_string($val) == TRUE && $val[0] == '#')
			{
				$changed = TRUE;
				$val = str_replace($search, $replace, $val);
				$val = ($this->get($val) != '') ? $this->get($val) : $val;
			}
			
			if($changed == TRUE)
			{
				$input[$key] = $val;
			};
		}
		
		return $input;
	}

	/**
	 * Searches for the first char in every key and value of the given assoziative array.
	 * If found then replace the controller name in every key and value of the array.
	 * Afterwarts try to translate it into an langstring and replaces the original '#key' with 'key'.
	 * @param array $input
	 * @param string $ctr_name (optional)
	 * @param array $search (optional)
	 * @param array $replace (optional) 
	 * @return array Returns the given $input array with replaced values (if found).
	 */	
	public function replaceLangInString($input, $ctr_name = '', $search = array(), $replace = array())
	{
		$search = array_merge(array('#', '{ctr_name}'), $search);
		$replace = array_merge(array('', $ctr_name), $replace);
		
		if(is_string($input) == TRUE && $input[0] == '#')
		{
			$input = str_replace($search, $replace, $input);
			$input = ($this->get($input) != '') ? $this->get($input) : $input;
		}
		
		return $input;
	}
}

?>