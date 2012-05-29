<?php
//TODO add charset
class SF_LIB_FrontendOutput extends SF_LIB_ApiObject
{	
	
	/**
	 * Local config array
	 * @var cfg_lib
	 */
	protected $cfg_lib = array( 
							'containertags' => array(),
							'doctype' => 'xhtml-1.0-trans',
							'title' => '',
							'meta_keywords' => '',
							'meta_description' => '',
							'meta_author' => '',
							'meta_robots' => '',
							'content_type' => '',
							'charset' => '',
							'content_container' => array( 'head' => array('top' => '', 'bottom' => '')),
							'visibility_container' => array(),
							'http_header' => array(),
							);
	/**
	 * Default vals
	 * @var cfg_lib
	 */
	protected $cfg_defaults = array( 
							'doctypes' => array('xhtml-1.0-trans' => 
												array(	'content_type' => 'text/html', 
														'doctype' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
														'with_closing_t_slash' => TRUE,
														'use_meta_tags' => TRUE),
											 'html-4.0.1-trans' =>
												array(	'content_type' => 'text/html', 
														'doctype' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
														'with_closing_t_slash' => FALSE,
														'use_meta_tags' => TRUE),
											  'html5' =>
												array(	'content_type' => 'text/html', 
														'doctype' => '<!DOCTYPE html>',
														'with_closing_t_slash' => TRUE,
														'use_meta_tags' => TRUE),
											  'xml' =>
												array(	'content_type' => 'text/xml', 
														'doctype' => '<?xml version="1.0" encoding="{charset}"?>',
														'with_closing_t_slash' => TRUE,
														'use_meta_tags' => FALSE)),
							'title' => '<title>{val}</title>',
							'meta_author' => '<meta name="author" content="{val}" {trailing_slash}>',
							'meta_description' => '<meta name="description" content="{val}" {trailing_slash}>',
							'meta_keywords' => '<meta name="keywords" content="{val}" {trailing_slash}>',
							'meta_robots' => '<meta name="robots" content="{val}" {trailing_slash}>',
							'content_type' => '<meta http-equiv="content-type" content="{val}; charset={val2}" {trailing_slash}>',
							); 
	
	/**
	 * Construktor
	 */
	public function __construct() 
	{
		//set singelton
        $this->_API_setObjectIsSingleton(true);
	}
	
	/**
	 * Returns an Array with all needed informations about the used cms tags in the 
	 * current layout. 
	 * 
	 * @return arr [0-n[type, id, title, full_tag, in_tag
	 */
	public function getCmsLayTags()
	{
		return $this->cfg_lib['containertags'];
	}
	
	public function getDoctype()
	{
		return $this->cfg_lib['doctype'];
	}
	
	public function getCharset()
	{
		return $this->cfg_lib['charset'];
	}
	
	public function getTitle()
	{
		return $this->cfg_lib['title'];
	}
	
	public function getMetaKeywords()
	{
		return $this->cfg_lib['meta_keywords'];
	}
	
	public function getMetaDescription()
	{
		return $this->cfg_lib['meta_description'];
	}
	
	public function getMetaAuthor()
	{
		return $this->cfg_lib['meta_author'];
	}
	
	public function getMetaRobots()
	{
		return $this->cfg_lib['meta_robots'];
	}
	
	/**
	 * Set Array with all needed informations about the used cms tags in the 
	 * current layout. Possible array keys and values:
	 * type - head, config or container
	 * full_tag - the full tag
	 * in_tag - not used
	 * id - the id of one container, only used by type container
	 * title - the name of one container, only used by type container
	 * 
	 * @param arr $arr [0-n[type, id, title, full_tag, in_tag
	 */
	public function setCmsLayTags($arr)
	{
		if (! is_array($arr))
		{
			return FALSE;
		}
		
		$this->cfg_lib['containertags'] = $arr;
		
		return TRUE;
	}

	public function setHttpHeader($header)
	{
		array_push($this->cfg_lib['http_header'], $header);

		return TRUE;
	}
	
	public function setDoctype($val)
	{
		if (! array_key_exists($val, $this->cfg_defaults['doctypes']))
		{
			return FALSE;
		}

		$this->cfg_lib['doctype'] = $val;

		return TRUE;
	}
	
	public function setCharset($val)
	{
		$this->cfg_lib['charset'] = $val;

		return TRUE;
	}
	
	public function setTitle($val)
	{
		$this->cfg_lib['title'] = $val;

		return TRUE;
	}
	
	public function setMetaKeywords($val)
	{
		$this->cfg_lib['meta_keywords'] = $val;
	}
	
	public function setMetaDescription($val)
	{
		$this->cfg_lib['meta_description'] = $val;
	}
	
	public function setMetaAuthor($val)
	{
		$this->cfg_lib['meta_author'] = $val;
	}
	
	public function setMetaRobots($val)
	{
		$this->cfg_lib['meta_robots'] = $val;
	}
	
	
	public function addContentToHead($content, $position = 'bottom')
	{
		$this->_addContent('head', $content, $position);
	}
	
	public function addJs($file, $attributes = '', $position = 'bottom')
	{
		$element = '<script type="text/javascript" src="'.$file.'" '.$attributes.'></script>'."\n";
		$this->_addContent('head', $element, $position);
		
		return TRUE;
	}
	
	public function addCss($file, $attributes = '', $position = 'bottom')
	{
		$trailing_slash = '/';
		if ($this->cfg_lib['doctype'] == 'html-4.0.1-trans')
		{
			$trailing_slash = '';
		}
		
		$element = '<link rel="stylesheet" type="text/css" href="'.$file.'" '.$attributes.' '.$trailing_slash.'>'."\n";
		$this->_addContent('head', $element, $position);
		
		return TRUE;
	}
	
	public function addContentToContainer($nr, $content, $position = 'bottom')
	{
		$this->_addContent($nr, $content, $position);
		
		return TRUE;
	}
	
	public function changeContainerVisibility($container, $is_visible = TRUE)
	{
		$this->cfg_lib['visibility_container'][$container] = $is_visible;
		
		return TRUE;
	}
	
	public function parse($code, $strip_comments = FALSE)
	{
		//replace head vals
		$doctype_key = $this->cfg_lib['doctype'];
		$doctype = $this->cfg_defaults['doctypes'][$doctype_key]['doctype'];
		$content_type = $this->cfg_defaults['doctypes'][$doctype_key]['content_type'];
		$use_meta_tags = $this->cfg_defaults['doctypes'][$doctype_key]['use_meta_tags'];
		$with_closing_slash = $this->cfg_defaults['doctypes'][$doctype_key]['with_closing_t_slash'];

		if ($use_meta_tags)
		{
            $code = str_replace(array('{%sf_doctype%}', '{%sf_head_title%}', '{%sf_meta_keywords%}',
									'{%sf_meta_description%}', '{%sf_meta_author%}', '{%sf_meta_robots%}', '{%sf_meta_content_type%}'),
								 array($doctype."\n", 
								 		$this->_replaceMetaVars($this->cfg_defaults['title'], $this->cfg_lib['title']), 
								 		$this->_replaceMetaVars($this->cfg_defaults['meta_keywords'], $this->cfg_lib['meta_keywords'], '', $with_closing_slash), 
								 		$this->_replaceMetaVars($this->cfg_defaults['meta_description'], $this->cfg_lib['meta_description'], '', $with_closing_slash), 
								 		$this->_replaceMetaVars($this->cfg_defaults['meta_author'], $this->cfg_lib['meta_author'], '', $with_closing_slash), 
								 		$this->_replaceMetaVars($this->cfg_defaults['meta_robots'], $this->cfg_lib['meta_robots'], '', $with_closing_slash), 
								 		$this->_replaceMetaVars($this->cfg_defaults['content_type'], $content_type, $this->cfg_lib['charset'], $with_closing_slash), 
								 		), 
								 $code);
		}
		else
		{
			//xml replacement
            $doctype = str_replace('{charset}', $this->cfg_lib['charset'], $doctype);

            $code = str_replace(array('{%sf_doctype%}', '{%sf_head_title%}', '{%sf_meta_keywords%}',
									'{%sf_meta_description%}', '{%sf_meta_author%}', '{%sf_meta_robots%}', '{%sf_meta_content_type%}'),
								 array($doctype."\n", 
								 		'', 
								 		'', 
								 		'', 
								 		'', 
								 		'', 
								 		), 
								 $code);
		}
		//send header
		header('Content-type: '.$content_type.'; charset='.$this->cfg_lib['charset']);

		foreach ($this->cfg_lib['http_header'] AS $v)
		{
			header($v);
		}
		
		//add contents
		foreach ($this->cfg_lib['content_container'] AS $type_or_nr => $vals)
		{
			if (array_key_exists('top', $vals))
			{
				$search[] = '<!--START '.$type_or_nr.'//-->';
				$replace[] = '<!--START '.$type_or_nr.'//-->'.$vals['top'];
			}
			if (array_key_exists('bottom', $vals))
			{
				$search[] = '<!--END '.$type_or_nr.'//-->';
				$replace[] = $vals['bottom'].'<!--END '.$type_or_nr.'//-->';
			}
		}
		
		if (isset($search) && isset($replace))
		{
			$code = str_replace($search, $replace, $code);
		}
		
		//strip hidden containers
		foreach ($this->cfg_lib['visibility_container'] AS $type_or_nr => $visibility)
		{
			if ($visibility === FALSE)
			{
				$code = preg_replace('#<!--START '.$type_or_nr.'//-->.+?<!--END '.$type_or_nr.'//-->#Us', '', $code);
			}
		}
		
		//replace comments
		if ($strip_comments)
		{
			$code = preg_replace('#<!--(START|END) (head|[0-9]+)//-->#U', '', $code);
		}
		
		
		return $code;
	}
	
	public function _exportConfig()
	{
		return var_export($this->cfg_lib, TRUE);
	}
	
	public function _importConfig($cfg)
	{
		$this->cfg_lib = $cfg;
	}
	
	protected function _addContent($type, $content, $position)
	{
		$position = ($position == 'top') ? 'top' : 'bottom';
		
		$this->cfg_lib['content_container'][$type][$position] = $content;
	}
	
	protected function _replaceMetaVars($tpl, $val, $val2 = '', $with_slash = FALSE)
	{
		if ($val == '')
		{
			return '';
		}
		
		$slash = $with_slash ? ' /': '';
		return str_replace(array('{val}', '{val2}', '{trailing_slash}'), 
								array(htmlspecialchars($val, ENT_COMPAT, 'utf-8'), htmlspecialchars($val2, ENT_COMPAT, 'utf-8'), $slash), $tpl) ."\n";
	}
}

?>