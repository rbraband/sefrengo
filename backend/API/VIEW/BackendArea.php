<?php

$this->includeClass('VIEW', 'AbstractView');

class SF_VIEW_BackendArea extends SF_VIEW_AbstractView
{
	/**
	 * Constructor sets the template filename
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->loadTemplatefile('backend_area.tpl');
		
		$this->addTemplateVar('UPDATE_VIEW', $this->lng->get('gen_update_view'));
	}
	
	/**
	 * Add CMS header to backend area template
	 * @param array $js_lang Custom language variables for JavaScript as key-value array
	 * @return void
	 */
	public function addCmsHeader($js_lang = array())
	{
		$header = sf_api('VIEW', 'Header');
		if(count($js_lang) > 0)
		{
			$header->addJsLang($js_lang);
		}
		$this->addTemplateVar('HEADER', $header);
	}
	
	/**
	 * Add CMS footer to backend area template
	 * @return void
	 */
	public function addFooter($js_lang = array(), $footer_license = TRUE)
	{
		$footer = sf_api('VIEW', 'Footer');
		if(count($js_lang) > 0)
		{
			$footer->addJsLang($js_lang);
		}
		if($footer_license == TRUE)
		{
			$footer->addFooterLicense();
		}
		$this->addTemplateVar('FOOTER', $footer);
	}
	
	/**
	 * Add message to right pane in backend area template
	 * @param string $type (ERROR,WARNING,OK)
	 * @param string $message 
	 * @param array $moreinfo
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function addMessage($type, $message, $moreinfo = array())
	{
		if($message == '')
		{
			return FALSE;
		}
		
		$messages = sf_api('VIEW', 'Messages');
		
		$type = strtolower($type);
		switch($type)
		{
			case 'error':
				$messages->addError($message, $moreinfo);
				break;
			case 'warning':
				$messages->addWarning($message, $moreinfo);
				break;
			case 'ok':
				$messages->addOk($message, $moreinfo);
				break;
			case 'info':
				$messages->addInfo($message, $moreinfo);
				break;
			default:
				return FALSE;
				break;
		}
		
		$this->addTemplateVar('MESSAGE', $messages, 'RIGHTPANE');
		
		return TRUE;
	}
	
	/**
	 * Send the HTTP header first and then generate the template
	 * @see API/VIEWS/SF_VIEW_AbstractView#generate()
	 */
	public function generate()
	{
		$this->sendHttpHeader();
		return parent::generate();
	}
	
	
}
?>