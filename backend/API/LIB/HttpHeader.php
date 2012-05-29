<?php
class SF_LIB_HttpHeader extends SF_LIB_ApiObject
{	
	public function __construct() 
	{
		//set singelton
        $this->_API_setObjectIsSingleton(true);
	}
	
	public function redirect($url, $shutdown = TRUE)
	{
		$url = str_replace('&amp;', '&', $url);

		header ('HTTP/1.1 302 Moved Temporarily');
		header ('Location:' . $url );

		if ($shutdown)
		{
			page_close();
			exit;
		}
	}
}
?>
