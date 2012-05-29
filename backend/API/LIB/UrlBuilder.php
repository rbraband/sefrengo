<?php
class SF_LIB_UrlBuilder extends SF_LIB_ApiObject 
{
	protected $cfg = array(
		'url' => array(
			'url' => '',
			'params' => array()
		),
		'firstchar' => '@'
	);
	
	public function __construct() 
	{
		$this->_API_setObjectIsSingleton(TRUE);
    }
    
    public function urlSetBase($url, $params = array())
    {
    	$this->cfg['url']['url'] = $url;
    	$this->cfg['url']['params'] = $params;
    }
    
    public function urlAddModifyParams($params)
    {
    	$this->cfg['url']['params'] = array_merge($this->cfg['url']['params'], $params);
    }
    
    public function urlGet($params = array(), $cfg = array())
    {
    	global $sess;
    	
    	//init paramstr
    	$paramstr = '';
    
    	if (in_array('add_session_param', $cfg))
    	{
    		$params[$sess->name] = $sess->id;
    	}
    	
    	if (! in_array('params_skip', $cfg))
    	{
	    	//get params
	    	$params =  array_merge($this->cfg['url']['params'], $params);
	    	
	    	//remove empty params
	    	$params = array_filter($params);
	    	
	    	//urldecode vals
	    	$params = array_map('urldecode', $params);
	    	
	    	//params to string
	    	if (count($params)>0)
	    	{
	    		$i=0;
	    		foreach($params AS $k=>$v)
	    		{
	    			++$i;
	    			$paramstr .= ($i>1) ? '&':'?'; 
	    			$paramstr .= $k.'='.$v;
	    		}
	    	}
    	}
    	
    	//make url
    	if (in_array('add_session_param', $cfg))
    	{
    		$url = $this->cfg['url']['url'].$paramstr;
    	}
    	else
    	{
    		$url = $sess->url($this->cfg['url']['url'].$paramstr);
    	}
    	
    	return $url;
    }
    
    public function urlGetParams($params = array())
    {
    	//get params
    	$params =  array_merge($this->cfg['url']['params'], $params);
    	
    	//remove empty params
    	$params = array_filter($params);
    	
    	return $params;
    } 
	
	/**
	 * Searches for the first char in every key of the given assoziative array.
	 * If found then replace the controller name in every value of the array.
	 * Afterwarts generates an URL of the array and replaces the original '@key' with 'key'.
	 * @param array $input
	 * @param string $ctr_name (optional)
	 * @param array $search (optional)
	 * @param array $replace (optional) 
	 * @return array Returns the given $input array with replaced values (if found).
	 */
	public function replaceUrlInArray($input, $ctr_name = '', $search = array(), $replace = array())
	{
		$search = array_merge(array($this->cfg['firstchar'], '{ctr_name}'), $search);
		$replace = array_merge(array('', $ctr_name), $replace);
		
		foreach($input as $key => $val)
		{
			if(is_array($val) == TRUE)
			{
				// recursion
				$input[$key] = $this->replaceUrlInArray($val, $ctr_name, $search, $replace);
			}
			
			if(is_string($key) == TRUE && $key[0] == $this->cfg['firstchar'])
			{
				// delete old key, otherwise you get '@key' and 'key'
				unset($input[$key]);
				
				$key = str_replace($search, $replace, $key);
				$val = $this->_replaceCtrNameRecursive($val, $ctr_name, $search, $replace);
				
				$input[$key] = $this->urlGet($val);
			}
		}
		return $input;
	}
	
	/**
	 * Function replaces the controller name recursive in all values of the the given array.
	 * @param array $input
	 * @param string $ctr_name (optional)
	 * @param array $search (optional)
	 * @param array $replace (optional) 
	 * @return array Returns the given $input array with replaced values (if found). 
	 */
	protected function _replaceCtrNameRecursive($input, $ctr_name = '', $search = array(), $replace = array())
	{
		foreach($input as $key => $val)
		{
			if(is_array($val) == TRUE)
			{
				$val = $this->_replaceCtrNameRecursive($val, $ctr_name, $search, $replace);
				$input[$key] = $val;
			}
			else if(is_string($val) == TRUE)
			{
				$val = str_replace($search, $replace, $val);
				$input[$key] = $val;
			}
		}
		return $input;
	}	
}
?>
