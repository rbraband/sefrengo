<?php
class SF_LIB_Array2Tpl extends SF_LIB_ApiObject 
{
	var $tpl;
	var $tpl_vars;
	var $page; 
	var $cf; // Content Factory
	                         
	public function __construct() //{{{ 
	{
		$this->_API_setObjectIsSingleton(TRUE);
	} //}}}
	
	public function get($arr, $tpl, $options = array()) //{{{ 
	{        
		if( empty($tpl) )
		{
			return FALSE;
		}
		
		// set keywords
		$this->_setKeywords();
		
		// Set Options
		$this->tpl = $tpl;
		$this->tpl_vars =  empty($arr) ? array() : $arr;
		$options_def = array('delimiter_left' => '{',
							 'delimiter_right'=> '}',
							 'delete_empty' => false);
		$this->options = array_merge($options_def, $options);
		
		// Quote delimiter - TODO: get sure that quted delimiters are only used on reg expressions
		//$this->options['delimiter_left'] = preg_quote($this->options['delimiter_left'], '#');
		//$this->options['delimiter_right'] = preg_quote($this->options['delimiter_right'], '#');
		
		//{{{ Load classes
		if( stripos($tpl, 'sf_page') ) 
		{
			if( !is_object($this->page) ) 
			{
				$this->page = sf_api('LIB', 'Pageinfos');
			}	
		}
		if( stripos($tpl, 'sf_cat') ) 
		{
			if( !is_object($this->cat) ) 
			{
				$this->cat = sf_api('LIB', 'Catinfos');
			}	
		}
		if( stripos($tpl, 'sf_content') ) 
		{
			if( !is_object($this->cf) ) 
			{
				$this->cf = sf_api('PAGE', 'ContentFactory');
			}	
		} 
		//}}}
		
		// parse template
		$this->_parseTemplate();
		
		// if delete_empty is set, delete all unused template variables
		if( $this->options['delete_empty'] ) 
		{
			$delimiter_left  = preg_quote($this->options['delimiter_left'], '#');
			$delimiter_right = preg_quote($this->options['delimiter_right'], '#');
			
			//{{{ delete all blocks
			// recognize blocks
			$pattern = '#'.$delimiter_left.'/(.+?)'.$delimiter_right.'#s';
			preg_match_all($pattern, $this->tpl, $match);
			
			// delete blocks
			if( !empty($match[1]) ) 
			{
				foreach( $match[1] as $value ) 
				{
					$pattern = '#'.$delimiter_left.$value.$delimiter_right.'(.*?)'.$delimiter_left.'/'.$value.$delimiter_right.'#s';
					$replace = '';
					$this->tpl = preg_replace($pattern, $replace, $this->tpl);
				}
			} 
			//}}}

			// Delete the rest
			$pattern = '#'.$delimiter_left.'.*?'.$delimiter_right.'#';
			$replace = '';
			
			$this->tpl = preg_replace($pattern, $replace, $this->tpl);
		}
		
		return $this->tpl;
	} //}}}

	public function setKeywordOptions($keyword, $options) //{{{ 
	{
		if( is_array($options)
			&& array_key_exists($keyword, $this->keywords) ) 
		{
			$this->keywords[$keyword]['options'] = $options; 
		}
	} //}}}
	
	//{{{ Keyword functions
	//{{{ Sf_page functions
	/* protected function _sfPageRobots($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getMetaAuthor');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}} */
	
/* 	protected function _sfPageIsFirstChildOf($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getParent');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}} */
	/* protected function _sfPageActive($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getIsOnline');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}} */
	
	protected function _sfPageProtected($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getIsProtected');
		$params = array_merge($defaults, $params);
		
		$return = $this->_pageSimple($params) ? 'true' : 'false';
		
		return $return;
	} //}}}
	
	protected function _sfPageOnline($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getIsOnline');
		$params = array_merge($defaults, $params);
		
		$return = $this->_pageSimple($params) ? 'true' : 'false';
		
		return $return;
	} //}}}
	
	protected function _sfPageTplconfId($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getIdtplconf');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}}
	
	protected function _sfPageIsStartpage($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getIsStart');
		$params = array_merge($defaults, $params);
		
		$return = $this->_pageSimple($params) ? 'true' : 'false';
		
		return $return;
	} //}}}
	
	protected function _sfPageIsChildOf($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getParent');
		$params = array_merge($defaults, $params);
		
		return $this->_pageIsChildOf($params);
	} //}}} 
	
	protected function _sfPageParent($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getParent');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}}
	
	protected function _sfPageLastmodified($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getLastmodifiedTimestamp');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}}
	
	protected function _sfPageCreated($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getCreatedTimestamp');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}}
	
	protected function _sfPageUrl($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getLink');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}}
	
	protected function _sfPageAuthor($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getMetaAuthor');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}}
	
	protected function _sfPageNotice($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getSummary');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}}
	
	protected function _sfPageDesc($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getMetaDescription');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}}
	
	protected function _sfPageKeywords($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getMetaKeywords');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}}
	
	protected function _sfPageName($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getTitle');
		$params = array_merge($defaults, $params);
		
		return $this->_pageSimple($params);
	} //}}}
	
	protected function _sfPageid($params = array()) //{{{ 
	{
		if( isset($params['idcatside'])
			&& isset($params['idcatside'][0]) ) 
		{
			return $params['idcatside'][0];
		}
		
		return $this->_pageId($params);
	} //}}}
	
	protected function _sfPageActive($params = array()) //{{{ 
	{           
		return ($GLOBALS['idcatside'] == $params['idcatside'][0]);
	} //}}}
	
	//{{{ Magic Blocks
	protected function _sfBlockPagelist($params = array()) //{{{ 
	{ 
		$magic_page = $this->_pageList($params);
		$magic_page['indexname'] = $params['indexname'];
		 
		return $magic_page; 
	} //}}} 
	//}}}
	
	//{{{ Sf_cat functions
	protected function _sfCatOnline($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getIsOnline');
		$params = array_merge($defaults, $params);
		
		return $this->_catSimple($params);
	} //}}}
	
	protected function _sfCatParent($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getParent');
		$params = array_merge($defaults, $params);
		
		return $this->_catSimple($params);
	} //}}}
	
	protected function _sfCatLastmodified($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getLastmodifiedTimestamp');
		$params = array_merge($defaults, $params);
		
		return $this->_catSimple($params);
	} //}}}
	
	protected function _sfCatCreated($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getCreatedTimestamp');
		$params = array_merge($defaults, $params);
		
		return $this->_catSimple($params);
	} //}}}
	
	protected function _sfCatUrl($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getLink');
		$params = array_merge($defaults, $params);
		
		return $this->_catSimple($params);
	} //}}}
	
	protected function _sfCatDesc($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getDescription');
		$params = array_merge($defaults, $params);
		
		return $this->_catSimple($params);
	} //}}}
	
	protected function _sfCatId($params = array()) //{{{ 
	{
		if( !isset($params['idcat']) ) 
		{
			return $GLOBALS['idcat'];
		}
		
		return $params['idcat'][0];
	} //}}}
	
	protected function _sfCatName($params = array()) //{{{ 
	{
		$defaults = array('func' => 'getTitle');
		$params = array_merge($defaults, $params);
	 
		return $this->_catSimple($params);
	} //}}}
	
	protected function _sfCatActive($params = array()) //{{{ 
	{
		return ($GLOBALS['idcat'] == $params['idcat'][0]);
	} //}}}
	
	protected function _sfCatFirstPageUrl($params = array()) //{{{ 
	{
		$defaults = array();
		$params = array_merge($defaults, $params);
		
		
		$tmp = $this->_sfBlockPagelist($params);
		
		if( is_array($tmp) ) 
		{
			foreach( $tmp as $key => $value ) 
			{
				if( is_numeric($key) ) 
				{
					return $this->_sfPageUrl(array('idcatside' => array($value)));
				}
			}
		}
		
		return false;
	} //}}}
	
	protected function _sfCatPageCount($params = array()) //{{{ 
	{
		$defaults = array();
		$params = array_merge($defaults, $params);
		
		// call sf_block_page_list and count the array entries
		$tmp = $this->_sfBlockPagelist($params);
		
		// numeric keys are pages
		$count = 0;
		if( is_array($tmp) ) 
		{
			foreach( $tmp as $key => $value ) 
			{
				if( is_numeric($key) ) 
				{
					++$count;
				}
			}
		}
		
		return $count;
	} //}}}
	
	//{{{ Magic Blocks
	protected function _sfBlockCatlist($params = array()) //{{{ 
	{ 
		$magicCat = $this->_catList($params);
		$magicCat['indexname'] = $params['indexname'];
		
		return $magicCat; 
	} //}}} 
	//}}}
	//}}}
	
	//{{{ Sf environments functions
	protected function _sfEnvIdclient($params = array()) //{{{ 
	{
		return '???';
	} //}}}
	
	protected function _sfEnvIdcatside($params = array()) //{{{ 
	{
		return $GLOBALS['idcatside'];
	} //}}}
	
	protected function _sfEnvIdcat($params = array()) //{{{ 
	{
		return $GLOBALS['idcat'];
	} //}}}
	
	protected function _sfEnvBackendEdit($params = array()) //{{{ 
	{
		if( $GLOBALS['sess']->name == 'sefrengo' && $GLOBALS['view'] == 'edit' )	
		{
			$return = 'true';
		}
		else 
		{
			$return = 'false';
		}
		
		return $return;
	} //}}}
	
	protected function _sfEnvBackendPreview($params = array()) //{{{ 
	{
		if( $GLOBALS['sess']->name == 'sefrengo' && $GLOBALS['view'] == 'preview' )	
		{
			$return = 'true';
		}
		else 
		{
			$return = 'false';
		}
		
		return $return;
	} //}}} 

	protected function _sfEnvBackend($params = array()) //{{{ 
	{
		if( $GLOBALS['sess']->name == 'sefrengo' )	
		{
			$return = 'true';
		}
		else 
		{
			$return = 'false';
		}
		
		return $return;
	} //}}}
	//}}}
	//}}}  
	
	//{{{ Sf_grab functions
	protected function _sfContentCheckbox($params = array()) //{{{ 
	{
		$defaults = array('type' => 'checkbox');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	
	protected function _sfContentDate($params = array()) //{{{ 
	{
		$defaults = array('type' => 'date');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	
	protected function _sfContentFile($params = array()) //{{{ 
	{
		$defaults = array('type' => 'file');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	
	protected function _sfContentHidden($params = array()) //{{{ 
	{
		$defaults = array('type' => 'hidden');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	
	protected function _sfContentImage($params = array()) //{{{ 
	{
		$defaults = array('type' => 'image');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	
	protected function _sfContentLink($params = array()) //{{{ 
	{
		$defaults = array('type' => 'link');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	
	protected function _sfContentRadio($params = array()) //{{{ 
	{
		$defaults = array('type' => 'radio');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	
	protected function _sfContentSelect($params = array()) //{{{ 
	{
		$defaults = array('type' => 'select');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	
	protected function _sfContentSourcecode($params = array()) //{{{ 
	{
		$defaults = array('type' => 'sourcecode');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	
	protected function _sfContentText($params = array()) //{{{ 
	{
		$defaults = array('type' => 'text');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	
	protected function _sfContentTextarea($params = array()) //{{{ 
	{
		$defaults = array('type' => 'textarea');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	
	protected function _sfContentWysiwyg($params = array()) //{{{ 
	{
		$defaults = array('type' => 'wysiwyg');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	
	protected function _sfContentWysiwyg2($params = array()) //{{{ 
	{
		$defaults = array('type' => 'wysiwyg2');
		$params = array_merge($defaults, $params);
		
		$return = $this->_grabSimple($params);
		
		return $return;
	} //}}}
	//}}}
	
	//{{{ Helper
	protected function _getValue($params = array()) //{{{ 
	{
		if( isset($params['value'])
			&& !empty($params['value']) ) 
		{
			return $params['value'][0];
		}
		
		return null;
	} //}}}
	//}}}
	
	//{{{ Parser Functions
	//{{{ Page functions
	// calls functions from sf_pageinfos
	protected function _pageSimple($options = array()) //{{{ 
	{
		$defaults = array();
		$options = array_merge($defaults, $options);	
		
		if( !isset($options['func'])
			|| empty($options['func']) ) 
		{
			return false;
		}
		
		$return = null;
		if( isset($options['idcatside']) 
			&& isset($options['idcatside'][0])
			&& is_numeric($options['idcatside'][0]) ) 
		{
			$return = $this->page->{$options['func']}($options['idcatside'][0]);
		}
		else 
		{
			$return = $this->page->{$options['func']}($GLOBALS['idcatside']);
		}
		
		return $return;
	} //}}}
	
	protected function _pageIsChildOf($options = array()) //{{{ 
	{
		$defaults = array();
		$options = array_merge($defaults, $options);	
		
		if( !isset($options['func'])
			|| empty($options['func']) 
			|| !isset($options['idcat'])
			|| empty($options['idcat']) ) 
		{
			return false;
		}
		
		if( isset($options['idcatside']) 
			&& isset($options['idcatside'][0])
			&& is_numeric($options['idcatside'][0]) ) 
		{
			$idcatside = $options['idcatside'][0];
		}
		else 
		{
			$idcatside = $GLOBALS['idcatside'];
		}
		
		$return = $this->page->isChildOf($idcatside, $options['idcat'][0]) ? 'true' : 'false';
		
		return $return;
	} //}}}
	
	protected function _pageId($options = array()) //{{{ 
	{
		return $GLOBALS['idcatside'];;
	} //}}}
	
	//{{{ Magic Block 
	protected function _pageList($options = array()) //{{{ 
	{
		$defaults = array();
		$options = array_merge($defaults, $options);
		
		$return = null;
		if( isset($options['idcat']) 
			&& isset($options['idcat'][0])
			&& is_numeric($options['idcat'][0]) ) 
		{
			$params = array();
			if( @isset($options['withstartpage'][0]) ) 
			{
				$params['show_startpage'] = $options['withstartpage'][0];
			}
			if( @isset($options['order'][0]) ) 
			{
				$params['order'] = $options['order'][0];
			}
			if( @isset($options['orderdir'][0]) ) 
			{
				$params['order_dir'] = $options['orderdir'][0];
			}
			if( @isset($options['limit'][0]) ) 
			{
				$params['limit'] = $options['limit'][0];
			}
			if( @isset($options['startpos'][0]) ) 
			{
				$params['startpos'] = $options['startpos'][0];
			}
 
			$return = $this->page->getIdcatsidesByIdcat($options['idcat'][0], $params);
		}
		
		return $return;
	} //}}} 
	//}}}
	//}}}
	
	//{{{ Cat functions
	// calls functions from sf_catinfos
	protected function _catSimple($options = array()) //{{{ 
	{
		$defaults = array();
		$options = array_merge($defaults, $options);	
		
		if( !isset($options['func'])
			|| empty($options['func']) ) 
		{
			return false;
		}
		
		$return = null;
		if( isset($options['idcat']) 
			&& isset($options['idcat'][0])
			&& is_numeric($options['idcat'][0]) ) 
		{
			$return = $this->cat->{$options['func']}($options['idcat'][0]);
		}
		else 
		{
			$return = $this->cat->{$options['func']}($GLOBALS['idcat']);
		}
		
		return $return;
	} //}}}
	
	protected function _catName($options = array()) //{{{ 
	{
		$defaults = array();
		$options = array_merge($defaults, $options);
		
		$return = null;
		if( isset($options['idcat']) 
			&& isset($options['idcat'][0])
			&& is_numeric($options['idcat'][0]) ) 
		{
			$return = $this->cat->getTitle($options['idcat'][0]);
		}
		
		return $return;
	} //}}}
	
	//{{{ Magic Block 
	protected function _catList($options = array()) //{{{ 
	{
		$defaults = array();
		$options = array_merge($defaults, $options);
		
		$return = null;
		if( isset($options['idcat']) 
			&& isset($options['idcat'][0])
			&& is_numeric($options['idcat'][0]) ) 
		{
			$params = array();
			foreach( $options as $key => $value ) 
			{
			 	if( $key  == 'order_dir' ) 
				{
					$params['order_dir'] = $value[0];
				}
				
				if( $key  == 'limit' ) 
				{
					$params['limit'] = $value[0];
				}
				
				if( $key  == 'startpos' ) 
				{
					$params['startpos'] = $value[0];
				}
			}
			
			$return = $this->cat->getChilds($options['idcat'][0], $params);
		}
		
		return $return;
	} //}}} 
	//}}}
	//}}}
	
	//{{{ Modifier Funktions
	private function _substr($options = array(), $replacement) //{{{ 
	{
		if( empty($replacement) ) 
		{
			return;
		}
		
		$defaults = array();
		$options = array_merge($defaults, $options);
		
		$opt_string = '';
		
		if( !is_numeric($options[0]) ) 
		{
			return false;
		}
		
		if( isset($options[1]) 
			&& !empty($options[1]) ) 
		{
			if( !is_numeric($options[1]) ) 
			{
				return false;
			}
			
			$return_string = substr($replacement, $options[0], $options[1]);
		}
		else 
		{
			$return_string = substr($replacement, $options[0]);
		}
		
		return $return_string;
	} //}}}
	
	private function _dateformat($options = array(), $replacement) //{{{ 
	{
		if( empty($replacement) 
			|| empty($options) ) 
		{
			return;
		}
		
		$defaults = array();
		$options = array_merge($defaults, $options);
		
		$return_string = date($options[0], $replacement);
		
		return $return_string;
	} //}}}
	
	private function _default($options = array(), $replacement) //{{{ 
	{
		if( empty($replacement) ) 
		{
			return $options[0];	
		}
	} //}}}
	
	private function _toupper($options = array(), $replacement) //{{{ 
	{
		return strtoupper($replacement);	
	} //}}}
	
	private function _tolower($options = array(), $replacement) //{{{ 
	{
		return strtolower($replacement);	
	} //}}}
	//}}}
	
	//{{{ Grab functions
	// calls functions from ContentFactory
	protected function _grabSimple($options = array()) //{{{ 
	{
		$defaults = array();
		$options = array_merge($defaults, $options);	
		
		if( !isset($options['type'])
			|| empty($options['type']) ) 
		{
			return false;
		}
		
		$return = null;
		
		// Required
		if( @is_numeric($options['idcatside'][0])
			&& @is_numeric($options['idcontainer'][0]) ) 
		{
			// Required 
			$type = $options['type'];
			$idcatside = $options['idcatside'][0];
			$idcontainer = $options['idcontainer'][0];
			
			// hardcoded
			$idcmstag  = @isset($options['idcmstag'][0]) ? $options['idcmstag'][0] : 1;
			$idrepeat  = @isset($options['idrepeat'][0]) ? $options['idrepeat'][0] : 1;                          
			$idlang    = @isset($options['idlang'][0]) ? $options['idlang'][0] : 0;
			$mode      = @isset($options['mode'][0]) ? $options['mode'][0] : null;
			
			// clear array...
			unset($options['idcontainer']);
			unset($options['idcatside']);
			unset($options['idcmstag']);
			unset($options['idrepeat']);
			unset($options['idlang']);
			unset($options['mode']);
			
			// and get the rest in the args array
			$args = array();
			if( is_array($options) ) 
			{
				foreach( $options as $key => $value ) 
				{
					if( is_array($options[$key]) ) 
					{
						if( isset($value[0]) 
							&& !empty($value[0]) ) 
						{
							$args[$key] = $value[0];
						}
					}
				}
			}
			                                                                                 
			$tmp = $this->cf->getByTypenameAndIds($type, $idcatside, $idcontainer, $idcmstag, $idrepeat, $idlang);
			$tmp = $tmp->getValueStyled(array('htmloptionsareon' => true, 'mode' => $mode, $args));
			
			return $tmp;
		}
		
		return false;
	} //}}}
	//}}}
	
	//{{{ Core
	protected function _parseTemplate() //{{{ 
	{
		// Remove Keyword which are not in the template
		foreach( $this->keywords as $key => $value ) 
		{
			if( !stripos($this->tpl, $key) ) 
			{
				unset($this->keywords[$key]);
			}	
		}
		
		$this->tpl = $this->_parseMagicBlocks();
		$this->tpl = $this->_parseKeywords(null, 'grab');
		$this->tpl = $this->_parseBlocks();
		$this->tpl = $this->_parseKeywords();
		$this->tpl = $this->_assignVals($this->tpl_vars, $this->tpl);
		$this->tpl = $this->_parseKeywords(null, 'eval');
	} //}}}
	
	protected function _parseKeywords($tpl = null, $type='func', $keywords = array())	//{{{ 
	{
		// load template
		if( empty($tpl) ) 
		{
			$tpl = $this->tpl;
		}
		
		// We can send a modified keyword array, if position is relevant
		if( empty($keywords) ) 
		{
			$keywords = $this->keywords;
		}
				
		foreach( $keywords as $key => $value ) 
		{
			if( isset($value[$type])
				&& $value[$type] == true ) 
			{
				$options = array('name' => $key);
				if( isset($value['options'])
					&& is_array($value['options']) ) 
				{
					$options = array_merge($options, $value['options']);
				}
				
				if( $type == 'func' 
					|| $type == 'magicBlock'
					|| $type == 'grab') 
				{
					$tpl = $this->_parseFunction($options, $tpl);		
				}
				elseif( $type == 'eval' ) 
				{
					$fnc = '_parse'.$this->_camelize($key);
					if( method_exists($this, $fnc) ) 
					{
						$tpl = $this->{$fnc}($options, $tpl);		
					}		
				}
			}
		}
		
		return $tpl;
	} //}}}
	
	protected function _parseKeywordsAndOptions($options = array(), $tpl = null) //{{{ 
	{
		if( !isset($options['name']) ) 
		{
			return false;
		}
		
		if( empty($tpl) ) 
		{
			$tpl = $this->tpl;
		}
		
		preg_match_all('/\{'.$options['name'].'(.*?)\}/is', $tpl, $matches);
		if( !empty($matches[0]) ) 
		{
			foreach( $matches[1] as $match_key => $match ) 
			{
				$tpl_opt = array();
				if( !empty($match) ) 
				{
					// Mask escaped semikolons
					str_replace('\;', '@sf_placeholder@', $match);
					
					// Explode on the options devider
					$tmp = explode(';', $match);
					
					if( is_array($tmp) 
						&& !empty($tmp)) 
					{
						foreach( $tmp as  $value ) 
						{
							// Devide options and values
							$tpl_opt_tmp = explode(':', $value);
							if( is_array($tpl_opt_tmp)
								&& !empty($tpl_opt_tmp) ) 
							{
								// Devide values on whitespace
								$tpl_opt_tmp[1] = trim($tpl_opt_tmp[1]);
								$tpl_opt_tmp[1] = preg_replace("!(\s)+!", " ", $tpl_opt_tmp[1]); 
								$tmp_option_vals = explode(' ', $tpl_opt_tmp[1]);
								
								// trim values
								$option_vals = array();
								foreach( $tmp_option_vals as $tmp_option_vals_value ) 
								{
									$option_vals[] = trim($tmp_option_vals_value);
								}
								
								// Check for modifier
								$opt_or_mod = 'option';
								if( isset($this->keywords[$tpl_opt_tmp[0]])
									&& isset($this->keywords[$tpl_opt_tmp[0]]['mod']) 
									&& $this->keywords[$tpl_opt_tmp[0]]['mod'] == true ) 
								{
									$opt_or_mod = 'mod';
								}
								
								$tpl_opt[] = array($opt_or_mod => trim($tpl_opt_tmp[0]),
												   'value' => $option_vals);	
							}
						}
					}
					
					// unmask escaped semikolons
					str_replace('@sf_placeholder@', '\;', $match);
				}
				
				$matches['options'][$match_key] = $tpl_opt;
			}
		}
		else 
		{
			unset($matches);
			$matches[0][] = $this->options['delimiter_left'].$options['name'].$this->options['delimiter_right'];
		}
		
		return $matches;
	} //}}}
	
	protected function _parseMagicBlocks($tpl = null) //{{{ 
	{
		if( empty($tpl) ) 
		{
			$tpl = $this->tpl;
		}
		
		// We have to parse magic blocks from Top to down
		$magic_blocks = array();
		foreach( $this->keywords as $key => $value ) 
		{
			if( array_key_exists('magicBlock', $value)
				&& $value['magicBlock'] ) 
			{
				$magic_blocks[] = $key;		
			}
		}
		
		// Order magic Keys
		$orderd_magic = array();
		foreach( $magic_blocks as $value ) 
		{
			$orderd_magic[$value] = strpos($tpl, $this->options['delimiter_left'].$value);
		} 		
		asort($orderd_magic);
		
		// Make a keyword conform array out of orderd_magic
		$magic_keys = array();
		foreach( $orderd_magic as $key => $value ) 
		{
			$magic_keys[$key] = $this->keywords[$key];
		}
		
		foreach( $magic_keys as $key => $value ) 
		{
			$preg_string = '#'.$this->options['delimiter_left'].$key.'(.*?)'.$this->options['delimiter_right'].'.*?'.$this->options['delimiter_left'].'/'.$key.$this->options['delimiter_right'].'#is';
			preg_match_all($preg_string, $tpl, $matches);
			
			if( !empty($matches[0]) ) 
			{
				foreach( $matches[0] as $match ) 
				{
					$tmp =  $this->_parseKeywords($match, 'magicBlock', array($key => $value));
					$tpl = str_replace($match, $tmp, $tpl);		
				}
			} 
		}
		
		return $tpl;
	} //}}}
	
	protected function _parseBlocks($tpl = null, $tpl_vars = array()) //{{{ 
	{
		if( empty($tpl) ) 
		{
			$tpl = $this->tpl;
		}
		
		if( empty($tpl_vars) ) 
		{
			$tpl_vars = $this->tpl_vars;
		}
		
		// Parse Blocks
		foreach( $tpl_vars as $key => $value ) 
		{
			if( is_array($value) ) 
			{
				foreach( $value as $key2 => $value2 ) 
				{
					if( is_array($value2) ) 
					{
						$tpl_tmp = $this->_parseBlocks($tpl, $value2);
						
						if( !empty($tpl_tmp) ) 
						{
							$tpl = $tpl_tmp;
						}	
					}
				}
				
				// Set block counters even odds etc...
				$value = $this->_setSpecialBlockVals($value);
				
				$options = array('block' => $key,
								 'data'  => $value);
				
				$tpl_tmp = $this->_parseBlock($options, $tpl);
				
				if( !empty($tpl_tmp) ) 
				{
					$tpl = $tpl_tmp;
				}
			} 
		}
		
		return $tpl;
	} //}}}

	protected function _parseBlock($options = array(), $tpl = null) //{{{ 
	{
		if( empty($tpl) ) 
		{
			$tpl = $this->tpl;
		}
		
		$defaults = array();
		$options = array_merge($defaults, $options);
		
		//{{{ check if block exists
		$preg_k = preg_quote($options['block'], '#');
		$match = array();
	
		if( !preg_match("#".$this->options['delimiter_left'] . $preg_k . $this->options['delimiter_right'] 
						. "(.+?)".$this->options['delimiter_left']. '/' . $preg_k . $this->options['delimiter_right'] . "#s", $tpl, $match) )
		{
			return;
		} 
		//}}}
		
		//{{{  recursive check if block has children
		foreach( $this->tpl_vars as $key => $value ) 
		{
			// If Block has children, parse the child Block
			if( is_array($value) ) 
			{
				// Dont parse the Block we are in twice...
				if( $key == $options['block'] ) 
				{
					continue;
				}
				
				$options_tmp = array('block' => $key,
								     'data'  => $value);
				
				$tpl_tmp = $this->_parseBlock($options_tmp, $match[1]);
				
				// replace block template with parsed childblock template
				if( !empty($tpl_tmp) ) 
				{
					$match[1] = str_replace($match[1], $tpl_tmp, $match[1]);
				}
			} 
		} 
		//}}}
		
		//{{{  parse template vars
		$out = '';
		foreach ($options['data'] as $row)
		{
			if( is_array($row) ) 
			{
				$out .= $this->_assignVals($row, $match['1']);
			}
		}
		//}}}

		// put template together 
		$tpl = str_replace($match['0'], $out, $tpl);
		
		return $tpl;
	} //}}}

	protected function _parseMods($match, $string) //{{{ 
	{
		/* if( empty($string) ) 
		{
			return false;
		} */
		
		if( is_array($match)
			&& !empty($match) ) 
		{
			foreach( $match as $option ) 
			{
				if( isset($option['mod']) ) 
				{
					$fnc = '_'.$option['mod'];
					if( method_exists($this, $fnc) ) 
					{
						$string = $this->{$fnc}($option['value'], $string);
					}
				}
			}
		}
		
		return 	$string;
	} //}}}
	
	protected function _parseOptions($match) //{{{ 
	{
		$param = array();
		if( is_array($match)
			&& !empty($match) ) 
		{
			foreach( $match as $option ) 
			{
				if( isset($option['option']) ) 
				{
					$param[$option['option']] = $option['value'];
				}
			}	
		}
		
		return $param;
	} //}}}
	
	protected function _parseFunction($options = array(), $tpl = null) //{{{ 
	{
		if( empty($tpl) ) 
		{
			$tpl = $this->tpl;
		}
		
		$defaults = array();
		$options = array_merge($defaults, $options);
		
		$matches = $this->_parseKeywordsAndOptions($options, $tpl);
		
		$replace = array();  
		if( !empty($matches['options']) ) 
		{
			foreach( $matches['options'] as $match_key => $match_value ) 
			{
				if( !empty($match_value) ) 
				{
					// Parse Opions
					$params = $this->_parseOptions($match_value);
					
					// params has to be an array 
					if( empty($params) ) 
					{
						$params = array();
					}
		
					// go through functions
					$replace[$match_key] = $this->{'_'.$this->_camelize($options['name'])}($params);
					
					// Parse Mods
					if( $tmp = $this->_parseMods($match_value, $replace[$match_key]) ) 
					{
						$replace[$match_key] = $tmp;
					}
				}
			}
		}
		
		// Check if we have replaced all matches
		if( is_array($matches[0]) ) 
		{
			foreach( $matches[0] as $key => $value ) 
			{
				if( !isset($replace[$key]) ) 
				{
					$replace[$key] = $this->{'_'.$this->_camelize($options['name'])}(array());
				}
			}	
		}
		
		//{{{ Magic Block
		if( in_array('magicBlock', $this->keywords[$options['name']]) 
			&& $this->keywords[$options['name']]['magicBlock'] == true ) 
		{
			$regex = '#'.$this->options['delimiter_left'].$options['name'].'.*?'.$this->options['delimiter_right'].'(.*?)'.$this->options['delimiter_left'].'/'.$options['name'].'.*?'.$this->options['delimiter_right'].'#is';
			//var_dump($regex); 
			preg_match($regex, $tpl, $inner);
			
			if( isset($inner[1])
				&& !empty($inner[1]) ) 
			{
				if( !empty($replace) )
				{
					$index = '';
					if( isset($replace[0]['indexname']) 
						&& isset($replace[0]['indexname'][0])) 
					{
						$index = $replace[0]['indexname'][0];
						unset($replace[0]['indexname']);
					}	
					
					$tpl_tmp = '';
					foreach( $replace[0] as $value ) 
					{
						if( !empty($index) ) 
						{
							$regex = '#:(\s)*'.$index.'([\s;'.$this->options['delimiter_right'].']+)#is';
							$tpl_tmp .= preg_replace($regex, ':'.$value.'$2', $inner[1]);	
						}
						else 
						{
							$tpl_tmp .= $inner[1];		
						}
					}
					
					return $tpl_tmp;
				}
			}
		} 
		//}}}
		
		//{{{ Replace Template Vars
		if( !empty($replace)  
			&& !empty($matches[0]) ) 
		{
			$tpl = str_replace($matches[0], $replace, $tpl);
		}
		//}}}
		
		return $tpl;
	} //}}}
	
	protected function _assignVals($arr, $tpl) //{{{ 
	{
		foreach ($arr as $k=>$v)
		{
			$tpl = $this->_parseVal($k, $v, $tpl);
		}
		
		return $tpl;
	} //}}}
	
	protected function _parseVal($k, $v, $tpl) //{{{ 
	{
		return str_replace($this->options['delimiter_left'] . $k . $this->options['delimiter_right'], $v, $tpl);
	} //}}}

	protected function _camelize($word) //{{{ 
	{
		if( empty($word) ) 
		{
			return false;
		}
		
		$tmp = explode('_', $word);
		
		$camelized_word = '';
		$first = true;
		foreach( $tmp as $value ) 
		{
			if( $first ) 
			{
				$first = false;
				$camelized_word .= $this->_lcfirst($value);	
			}
			else 
			{
				$camelized_word .= ucfirst($value);
			}
		}
		
		return $camelized_word;
	} //}}}
	
	//{{{ Eval functions
	protected function _parseIf($options = array(), $tpl = null) //{{{ 
	{
		if( empty($tpl) ) 
		{
			$tpl = $this->tpl;
		}
		
		$defaults = array();
		$options = array_merge($defaults, $options);
		
		$tpl = str_ireplace('{/if}', '<? } ?>', $tpl);
		$tpl = str_ireplace('{else}', '<? } else { ?>', $tpl);
		$tpl = preg_replace('/\{else\s+if (.+?)\}/is', '<? } elseif( $1 ) { ?>', $tpl);
		$tpl = preg_replace('/\{elseif (.+?)\}/is', '<? } elseif( $1 ) { ?>', $tpl);
		$tpl = preg_replace('/\{if (.+?)\}/is', '<? if( $1 ){ ?>', $tpl);
		
		ob_start();
		eval(' ?> '.$tpl.' <?php ');
		$tpl = ob_get_contents();
		ob_end_clean();
		
		return $tpl;
	} //}}}
	//}}}
	//}}}
	
	//{{{ Deprecataed 
	protected function _parseSfPageId($options = array(), $tpl = null) //{{{ 
	{
		die('_parseSfPageId');
		if( empty($tpl) ) 
		{
			$tpl = $this->tpl;
		}
		
		$defaults = array('name' => 'sf_page_name');
		$options = array_merge($defaults, $options);
		
	} //}}}

	protected function _parseSfCatName($options = array(), $tpl = null) //{{{ 
	{
		die('_parseSfCatName');
		if( empty($tpl) ) 
		{
			$tpl = $this->tpl;
		}
		
		$defaults = array('name' => 'sf_cat_name');
		$options = array_merge($defaults, $options);
		
		$matches = $this->_parseKeywordsAndOptions($options, $tpl);
		
		// Parse Options and Mods
		$replace = array(); //{{{ 
		if( !empty($matches['options']) ) 
		{
			foreach( $matches['options'] as $match_key => $match_value ) 
			{
				if( !empty($match_value) ) 
				{
					// Parse Opions
					$param = $this->_parseOptions($match_value);
					
					// go through funktions
					$replace[$match_key] = $this->_pageName($param);
					
					// Parse Mods
					if( $tmp = $this->_parseMods($match_value, $replace[$match_key]) ) 
					{
						$replace[$match_key] = $tmp;
					}
				}
			}
		} //}}}

		// Replace Template Vars
		if( !empty($replace) //{{{ 
			&& !empty($matches[0]) ) 
		{
			$tpl = str_replace($matches[0], $replace, $tpl);
		} //}}}
		
		return $tpl;
	} //}}}
	//}}}
	//}}}
	
	protected function _lcFirst($string)//{{{ 
	{
		$string{0} = strtolower($string{0});
		
		return $string;
	} //}}}

	protected function _setKeywords() //{{{ 
	{
		$keywords = array(// Eval functions
						  'if' => array('eval' => true),
						  // Environment vars
						  'sf_env_backend' => array('func' => true),
						  'sf_env_backend_preview' => array('func' => true),
						  'sf_env_backend_edit' => array('func' => true),
						  //'sf_env_frontend' => array('func' => true),
						  'sf_env_idclient' => array('func' => true),
						  //'sf_env_idlang' => array('func' => true),
						  'sf_env_idcat' => array('func' => true),
						  'sf_env_idcatside' => array('func' => true),
						  //'sf_env_logged_in' => array('func' => true),
						  // Grab vars
						  'sf_content_checkbox'   => array('grab' => true),
						  'sf_content_date'       => array('grab' => true),
						  'sf_content_file'       => array('grab' => true),
						  'sf_content_hidden'     => array('grab' => true),
						  'sf_content_image'      => array('grab' => true),
						  'sf_content_link'       => array('grab' => true),
						  'sf_content_radio'      => array('grab' => true),
						  'sf_content_select'     => array('grab' => true),
						  'sf_content_sourcecode' => array('grab' => true),
						  'sf_content_textarea'   => array('grab' => true),
						  'sf_content_text'       => array('grab' => true),
						  'sf_content_wysiwyg2'   => array('grab' => true),
						  'sf_content_wysiwyg'    => array('grab' => true),
						  // Page vars
						  'sf_page_name' => array('func' => true),
						  'sf_page_id' => array('func' => true), 
						  'sf_page_keywords' => array('func' => true),
						  'sf_page_desc' => array('func' => true),
						  'sf_page_notice' => array('func' => true),
						  'sf_page_author' => array('func' => true),
						  /* 'sf_page_robots' => array('func' => true), */
						  'sf_page_url' => array('func' => true),
						  'sf_page_created' => array('func' => true),
						  'sf_page_lastmodified' => array('func' => true),
						  'sf_page_parent' => array('func' => true),
						  /* 'sf_page_is_first_child_of' => array('func' => true), */
						  'sf_page_parent' => array('func' => true),
						  'sf_page_is_child_of' => array('func' => true),
						  'sf_page_is_startpage' => array('func' => true),
						  'sf_page_tplconf_id' => array('func' => true),
						  //'sf_page_is_start' => array('func' => true), welche funktion? (sf_page_is_startpage)
						  'sf_page_active' => array('func' => true),
						  'sf_page_online' => array('func' => true),
						  'sf_page_protected' => array('func' => true),
						  // Cat vars
						  'sf_cat_id' => array('func' => true),
						  'sf_cat_name' => array('func' => true),
						  'sf_cat_desc' => array('func' => true),
						  'sf_cat_url' => array('func' => true),
						  'sf_cat_created' => array('func' => true),
						  'sf_cat_lastmodified' => array('func' => true),
						  'sf_cat_parent' => array('func' => true),                                               
						  'sf_cat_page_count' => array('func' => true),
						  'sf_cat_first_page_url' => array('func' => true),
						  //'sf_cat_is_first_child_of' => array('func' => true),
						  //'sf_cat_is_child_of' => array('func' => true),
						  'sf_cat_online' => array('func' => true),
						  'sf_cat_active' => array('func' => true),
						  // Magic Blocks
						  'sf_block_catlist' => array('magicBlock' => true),
						  'sf_block_pagelist' => array('magicBlock' => true),
						  // Mods
						  /* 'sf_block_pagelist' => array('magicBlock' => true), */
						  'substr' => array('mod' => true),
						  'dateformat' => array('mod' => true),
						  'toupper' => array('mod' => true),
						  'tolower' => array('mod' => true),
						  'default' => array('mod' => true),
						  // helper
						  'get_value' => array('func' => true));        
		
		$this->keywords = $keywords;
	} //}}}
	
	/** 
	* Set Special values in Blocks
	*
	*
	* sf_block_count - count of all blocks
	* sf_block_number - aktual block number
	* sf_block_even - is block even?
	* sf_block_odd - is block odd?
	* sf_block_isfirst - is this the first block?
	* sf_block_islast - is this the last block?
	*
	* @param $block, array with block values
	* @return array, modified block
	*/
	function _setSpecialBlockVals($block)    //{{{ 
	{
		// insert block count values
		if( is_array($block) ) 
		{
			$first = true;
			$count = count($block);
			$counter = 0;
			foreach( $block as $blk_cnt => $blk_value ) 
			{
				// set index and block cout
				$block[$blk_cnt]['sf_block_number'] = ++$counter;
				$block[$blk_cnt]['sf_block_count'] = $count;
				
				// set first
				$block[$blk_cnt]['sf_block_isfirst'] = 0;
				if( $first ) 
				{
					$first = false;
					$block[$blk_cnt]['sf_block_isfirst'] = 1;
				}
				
				// set last
				$block[$blk_cnt]['sf_block_islast'] = 0;
				if( $blk_cnt == $count ) 
				{
					$block[$blk_cnt]['sf_block_islast'] = 1;
				}
				
				// set even and odd
				if( $blk_cnt%2 ) 
				{
					$block[$blk_cnt]['sf_block_even'] = 1;
					$block[$blk_cnt]['sf_block_odd'] = 0;
				}
				else 
				{
					$block[$blk_cnt]['sf_block_even'] = 0;
					$block[$blk_cnt]['sf_block_odd'] = 1;
				}
			}
		}
		
		return $block;
	} //}}}
}
?>
