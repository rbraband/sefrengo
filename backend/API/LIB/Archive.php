<?php
class SF_LIB_Archive extends SF_LIB_ApiObject
{
	protected $archive;
	
	public function __construct()
	{
		$this->_API_setObjectBridge(TRUE);
		$this->_API_setObjectIsSingleton(TRUE);
		
		include_once "File/Archive.php";
		
		$this->archive = File_Archive;
	}
	
	public function &_API_getBridgeObject()
	{
		return $this->archive;	
	}
		
}
?>
