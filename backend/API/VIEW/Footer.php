<?php

$this->includeClass('VIEW', 'AbstractView');
$this->includeClass('VIEW', 'DefaultJsFiles');

class SF_VIEW_Footer extends SF_VIEW_AbstractView
{
	/**
	 * Prevents the adding the default JS files, e.g. if set in header
	 * @var boolean 
	 */
	protected $prevent_default_js_files = FALSE;
	
	/**
	 * Stores the custom default JS file
	 * @var SF_VIEW_DefaultJsFiles
	 */
	protected $default_js_files = null;
	
	/**
	 * Stores the custom lang strings
	 * @var array
	 */
	protected $js_custom_lang = array();
	
	/**
	 * Stores the custom lang files
	 * @var array
	 */
	protected $js_custom_files = array();
	
	/**
	 * Constructor sets the template filename and the license to the footer
	 * @return void
	 */
	public function __construct()
	{
		// define as singleton, because only one footer exists
		$this->_API_setObjectIsSingleton(TRUE);
		
		parent::__construct();
		
		$this->loadTemplatefile('footer.tpl');
	}
	
	/**
	 * Adds the license with link to footer.
	 * @return void
	 */
	public function addFooterLicense()
	{
		$this->tpl->setCurrentBlock('FOOTER_LICENSE');
		$this->addTemplateVar('FOOTER_LICENSE', $this->lng->get('login_licence'));
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Adds language variables for use in JavaScript.
	 * Add further custom language variables as $js_custom_lang
	 * key-value array.
	 * Note: This is a wrapper function an calls the addJsLang()
	 * in the SF_VIEW_DefaultJsFiles.
	 * @param array $js_custom_lang 
	 * @return void
	 */
	public function addJsLang($js_custom_lang = array())
	{
		$this->js_custom_lang = $js_custom_lang;
	}
	
	/**
	 * Adds custom JavaScript files with the default JS path.
	 * If you want to use a custom path, set $use_custom_path to FALSE.
	 * Note: This is a wrapper function an calls the addJsFiles()
	 * in the SF_VIEW_DefaultJsFiles.
	 * @param array $files Plain array with filenames
	 * @param boolean $use_custom_path 
	 * @return void
	 */
	public function addJsFiles($files = array(), $use_custom_path = FALSE)
	{
		$this->js_custom_files[] = array(
			'files' => $files,
			'use_custom_path' => $use_custom_path
		);
	}
	
	/**
	 * Set the flag if default JS files should be prevent.
	 * @param boolean $prevent_default_js_files
	 * @return void
	 */
	public function setPreventDefaultJsFiles($prevent_default_js_files)
	{
		$this->prevent_default_js_files = (bool) $prevent_default_js_files;
	}
	
	/**
	 * Set a custom default JS file with the correct type.
	 * @param SF_VIEW_DefaultJsFiles $default_js_files
	 * @return boolean Returns TRUE on success. Otherwise returns FALSE.
	 */
	public function setDefaultJsFiles($default_js_files)
	{
		if($default_js_files instanceof SF_VIEW_DefaultJsFiles)
		{
			$this->default_js_files = $default_js_files;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Check if JavaScript language variables was added first,
	 * then generate template. 
	 * @see API/VIEWS/SF_VIEW_AbstractView#generate()
	 */
	public function generate()
	{
		// if no default js files are set, then generate some
		if($this->prevent_default_js_files == FALSE && $this->default_js_files == null)
		{
			$this->default_js_files = sf_api('VIEW', 'DefaultJsFiles');
		}
		
		// place default js files in footer (and not in header)
		if($this->default_js_files instanceof SF_VIEW_DefaultJsFiles)
		{
			// add custom lang strings
			$this->default_js_files->addJsLang($this->js_custom_lang);
			
			// add custom js files if any
			if(count($this->js_custom_files) > 0) 
			{
				foreach($this->js_custom_files as $custom_files)
				{
					$this->default_js_files->addJsFiles($custom_files['files'], $custom_files['use_custom_path']);
				}
			}
			
			$this->addTemplateVar('DEFAULT_JS_FILES', $this->default_js_files);
		}
		
		return parent::generate();
	}
}
?>