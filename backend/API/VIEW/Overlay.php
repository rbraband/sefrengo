<?php

$this->includeClass('VIEW', 'AbstractView');

class SF_VIEW_Overlay extends SF_VIEW_AbstractView
{
	/**
	 * Constructor sets the template filename
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		// add empty string by default to parse the template
		$this->addTemplateVar('ATTRIBUTES', ' ');
		// add empty string by default to parse the template
		$this->addTemplateVar('TITLE', ' ');
	}
	
	/**
	 * Converts the given $metadata into an json string,
	 * so the jQuery metadata plugin can read the configuration.
	 * @param array $metadata
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function addMetadata($metadata)
	{
		$json = json_encode($metadata);
		
		if($json == '')
		{
			return FALSE;
		}
		
		$this->addTemplateVar('METADATA', $json);
		return TRUE;
	}
	
}
?>