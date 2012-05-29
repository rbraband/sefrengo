<?php

$this->includeClass('VIEW', 'AbstractView');

class SF_VIEW_Messages extends SF_VIEW_AbstractView
{
	/**
	 * Constructor sets the template filename
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->loadTemplatefile('messages.tpl');
	}
	
	/**
	 * Adds an error message
	 * @param string $message 
	 * @param string $moreinfo
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function addError($message, $moreinfo = array())
	{
		if($message == '')
		{
			return FALSE;
		}
		
		if(array_key_exists('url', $moreinfo) && array_key_exists('text', $moreinfo))
		{
			$this->addTemplateVar('ERROR_URL', $moreinfo['url']);
			$this->addTemplateVar('ERROR_TEXT', $moreinfo['text']);
		}
		
		$this->addTemplateVar('ERROR_MESSAGE', $message, 'ERROR');
		
		return TRUE;
	}
	
	/**
	 * Adds a warning message
	 * @param string $message 
	 * @param string $moreinfo
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function addWarning($message, $moreinfo = array())
	{
		if($message == '')
		{
			return FALSE;
		}
		
		if(array_key_exists('url', $moreinfo) && array_key_exists('text', $moreinfo))
		{
			$this->addTemplateVar('WARNING_URL', $moreinfo['url']);
			$this->addTemplateVar('WARNING_TEXT', $moreinfo['text']);
		}
		
		$this->addTemplateVar('WARNING_MESSAGE', $message, 'WARNING');
		
		return TRUE;
	}
	
	/**
	 * Adds an ok message
	 * @param string $message 
	 * @param string $moreinfo
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function addOk($message, $moreinfo = array())
	{
		if($message == '')
		{
			return FALSE;
		}
		
		if(array_key_exists('url', $moreinfo) && array_key_exists('text', $moreinfo))
		{
			$this->addTemplateVar('OK_URL', $moreinfo['url']);
			$this->addTemplateVar('OK_TEXT', $moreinfo['text']);
		}
		
		$this->addTemplateVar('OK_MESSAGE', $message, 'OK');
		
		return TRUE;
	}
	
	/**
	 * Adds an info message
	 * @param string $message 
	 * @param string $moreinfo
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function addInfo($message, $moreinfo = array())
	{
		if($message == '')
		{
			return FALSE;
		}
		
		if(array_key_exists('url', $moreinfo) && array_key_exists('text', $moreinfo))
		{
			$this->addTemplateVar('INFO_URL', $moreinfo['url']);
			$this->addTemplateVar('INFO_TEXT', $moreinfo['text']);
		}
		
		$this->addTemplateVar('INFO_MESSAGE', $message, 'INFO');
		
		return TRUE;
	}
	
	
}
?>