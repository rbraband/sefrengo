<?php

$this->includeClass('INTERFACE', 'View');

abstract class SF_VIEW_AbstractView extends SF_LIB_ApiObject
		 implements SF_INTERFACE_View
{
	/**
	 * Configuration object
	 * @var SF_LIB_Config
	 */
	protected $cfg;
	
	/**
	 * Language Strings
	 * @var SF_LIB_Lang
	 */
	protected $lng;
	
	/**
	 * Template object
	 * @var PEAR HTML_Template_IT
	 */
	protected $tpl;
	
	/**
	 * URL Builder
	 * @var SF_LIB_UrlBuilder
	 */
	protected $url;
	
	/**
	 * Stores all template variables
	 * that are given to template engine
	 * @var array
	 */
	protected $templatevars = array(
		'content' => array(),
		'common' => array()
	);
	
	/**
	 * Stores the content of the generated view
	 * Is default false until the view is generated
	 * @var string
	 */
	protected $generated_view = false;
	
	
	/**
	 * Constructor sets up {@link $cfg}, {@link lng}, {@link url}, {@link tpl}
	 * @return void
	 */
	public function __construct()
	{
		$this->cfg = sf_api('LIB', 'Config');
		$this->lng = sf_api('LIB', 'Lang');
		$this->url = sf_api('LIB', 'UrlBuilder');
		
		//$this->tpl = $this->cfg->tpl();
		$this->tpl = new HTML_Template_IT($this->cfg->env('path_backend').$this->getSkinPath());
		
		$this->_addCommonTemplateVars();
	}
	
	/**
	 * Setup the HTTP header for the output
	 * @return void
	 */
	public function sendHttpHeader()
	{
		header('Content-type: text/html; charset=UTF-8');
		// Browsern das cachen von Backendseiten verbieten
		if ($this->cfg->cms('backend_cache') == '1') {
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Datum aus Vergangenheit
			header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // immer ge�ndert
			header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
			header('Cache-Control: post-check=0, pre-check=0', false);
			header('Pragma: no-cache');
		}
	}
	
	/**
	 * Add template variables like pathes that are used in most templates.
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	protected function _addCommonTemplateVars()
	{
		$this->templatevars['common']['SKIN'] = $this->cfg->cms('skin');	
		$this->templatevars['common']['VERSION'] = $this->cfg->cms('version');
		$this->templatevars['common']['SKINPATH'] = $this->getSkinPath();
		$this->templatevars['common']['IMGPATH'] = $this->getSkinImgPath();
		
		return TRUE;
	}
	
	/**
	 * Returns the path to the current skin
	 * @return string
	 */
	public function getSkinPath()
	{
		return 'tpl/'.$this->cfg->cms('skin').'/';
	} 
	
	/**
	 * Returns the path to the image folder of current skin
	 * @return string
	 */
	public function getSkinImgPath()
	{
		return $this->getSkinPath().'/img/';
	} 
	
	/**
	 * (non-PHPdoc)
	 * @see API/INTERFACES/SF_INTERFACE_View#loadTemplatefile($filename, $remove_unknown_variables, $remove_empty_blocks)
	 */
	public function loadTemplatefile($filename, $remove_unknown_variables = TRUE, $remove_empty_blocks = TRUE)
	{
		$path = $this->cfg->env('path_backend').$this->templatevars['common']['SKINPATH'].$filename;
		if($filename == '' || !file_exists($path))
		{
			return FALSE;
		}
		
		return $this->tpl->loadTemplatefile($filename, $remove_unknown_variables, $remove_empty_blocks);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see API/INTERFACES/SF_INTERFACE_View#addTemplateVar($name, $content, $block)
	 */
	public function addTemplateVar($name, $content, $block = '__global__')
	{
		if($block == '')
		{
			return FALSE;
		}
		
		if($name != '' && $content instanceof SF_INTERFACE_View)
		{
			$this->templatevars['content'][$block][$name] = $content;
			
			return TRUE;
		}
		else if($name == '' && is_array($content))
		{
			$this->templatevars['content'][$block][] = $content;
			
			return TRUE;
		}
		else if($name != '' && is_string($content) && $content != '')
		{
			$this->templatevars['content'][$block][$name] = $content;
			return TRUE;
		}
		
		
		return FALSE;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see API/INTERFACES/SF_INTERFACE_View#generate()
	 */
	public function generate()
	{
		if(count($this->templatevars['common']) > 0)
		{
			$this->tpl->setCurrentBlock('__global__');
			$this->tpl->setVariable($this->templatevars['common']);
		}
		
		foreach($this->templatevars['content'] as $block => $variable)
		{
			// set current block if $block has no sub-block e.g. "RIGHTPANE.TITLE"
			if(strpos($block, ".") !== FALSE)
			{
				$this->tpl->setCurrentBlock($block);
			}
			
			foreach($variable as $varname => $content)
			{
				if(($content instanceof SF_INTERFACE_View) && $content->generate() == TRUE)
				{
					$this->tpl->setVariable($varname, $content->get());
				}
				else if(is_array($content))
				{
					$this->tpl->setCurrentBlock($block);
					$this->tpl->setVariable($content);
					$this->tpl->parseCurrentBlock();
				}
				else if(is_string($content) && $content != '')
				{
					$this->tpl->setVariable($varname, $content);
				}
			}
			
			if($block != '__global__')
			{
				//$this->tpl->parseCurrentBlock();
			}
		}
		
		$this->tpl->parse();
		$this->generated_view = $this->tpl->get();
		
		return TRUE;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see API/INTERFACES/SF_INTERFACE_View#get($clear_cache)
	 */
	public function get($clear_cache = FALSE)
	{
		if($this->generated_view == FALSE || $clear_cache == TRUE)
		{
			$this->generate();
		}
		
		return $this->generated_view;
	}
}
?>