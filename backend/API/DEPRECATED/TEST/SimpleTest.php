<?php
class SF_TEST_SimpleTest extends SF_LIB_ApiObject{
	
	var $string = "Moin, I am a simple test"; 
	
	function get(){
		return $this->string;
	}
	
	function set($v){
		$this->string = $v;
	}
}
?>
