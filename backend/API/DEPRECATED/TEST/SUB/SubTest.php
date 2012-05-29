<?php
class SF_TEST_SUB_SubTest extends SF_LIB_ApiObject{
	
	var $string = "I am a String in the subtest package"; 
	
	function get(){
		return $this->string;
	}
	
	function set($v){
		$this->string = $v;
	}
}
?>
