<?php

$this->includeClass('VIEW', 'AbstractView');

class SF_VIEW_DefaultJsFiles extends SF_VIEW_AbstractView
{
	/**
	 * Flag, if generation of addJsLang()
	 * is already called
	 * @var boolean
	 */
	private $isset_js_lang = FALSE;
	
	/**
	 * Constructor sets the template filename and the license to the footer
	 * @return void
	 */
	public function __construct()
	{
		// define as singleton, because only one inclusion exists
		$this->_API_setObjectIsSingleton(TRUE);
		
		parent::__construct();
		
		$this->loadTemplatefile('default_js_files.tpl');
		
		$this->_setDefaultJsFiles();
	}
	
	/**
	 * Adds language variables for use in JavaScript.
	 * Add further custom language variables as $js_custom_lang
	 * key-value array.
	 * Note: The function can only called once. Every further time the function does nothing!
	 * @param array $js_custom_lang 
	 * @return boolean
	 */
	public function addJsLang($js_custom_lang = array())
	{
		if($this->isset_js_lang == TRUE)
		{
			return FALSE;
		}
		
		$this->tpl->setCurrentBlock('JS_LANG');
		
		// default language variables for all areas
		$js_default_lang = array();
		$js_default_lang['deletealert'] = $this->lng->get('gen_deletealert');
		$js_default_lang['deletealert_name'] = $this->lng->get('gen_deletealert_name');
		$js_default_lang['show_params'] = $this->lng->get('gen_show_params');
		$js_default_lang['hide_params'] = $this->lng->get('gen_hide_params');
		$js_default_lang['no_selection'] = $this->lng->get('gen_no_selection');
		$js_default_lang['show_calendar'] = $this->lng->get('gen_show_calendar');
		
        if (is_array($js_custom_lang))
        {
            $js_lang = array_merge($js_default_lang, $js_custom_lang);
        }
        
		$json = str_replace('\\\\', '\\', json_encode($js_lang));
		
		// No valid json so add an empty string. Otherwise you get an error in JS. 
		if($json == '')
		{
			$json = "''";
		}
		$this->tpl->setVariable('JS_LANG', $json);
		
		$this->tpl->parseCurrentBlock();
		
		$this->isset_js_lang = TRUE;
		
		return TRUE;
	}
	
	/**
	 * Adds custom JavaScript files with the default JS path.
	 * If you want to use a custom path, set $use_custom_path to FALSE.
	 * @param array $files Plain array with filenames
	 * @param boolean $use_custom_path 
	 * @return void
	 */
	public function addJsFiles($files = array(), $use_custom_path = FALSE)
	{
		foreach ($files as $file)
		{
			if(strlen($file) == 0)
			{
				continue;
			}
			
			$block = ($use_custom_path == TRUE) ? 'JS_FILE_CUSTOM_PATH' : 'JS_FILE_DEFAULT_PATH';
			$this->tpl->setCurrentBlock($block);
			
			$file = str_replace('{SKIN}', $this->cfg->cms('skin'), $file);
			
			$this->tpl->setVariable('SKIN', $this->cfg->cms('skin'));
			$this->tpl->setVariable('JS_FILE', $file);
			
			$this->tpl->parseCurrentBlock();
		}
	}
	
	/**
	 * Decides if debug mode is enabled and
	 * parse the correct block for debug or
	 * non debug JS files.
	 * Also transfering some language variables
	 * as JSON string to JavaScript. 
	 * @return void
	 */
	private function _setDefaultJsFiles()
	{
		if($this->cfg->env('debug') == TRUE)
		{
			$this->tpl->setCurrentBlock('JS_DEBUG');
		}
		else
		{
			$this->tpl->setCurrentBlock('JS_NODEBUG');
		}
		$this->tpl->setVariable('SKIN', $this->cfg->cms('skin'));
		$this->tpl->parseCurrentBlock();
		
		$this->tpl->setCurrentBlock('JS_CONFIG');
		$this->tpl->setVariable('SKIN', $this->cfg->cms('skin'));
		$this->tpl->setVariable('BACKEND_DIR', dirname($_SERVER['PHP_SELF']).'/');
		$this->tpl->parseCurrentBlock();
	}

	/**
	 * Check if JavaScript language variables was added first,
	 * then generate template. 
	 * @see API/VIEWS/SF_VIEW_AbstractView#generate()
	 */
	public function generate()
	{
		if($this->isset_js_lang == FALSE)
		{
			$this->addJsLang();
		}
		
		return parent::generate();
	}
}
?>