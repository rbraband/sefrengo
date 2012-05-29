<?php

class SF_TEST_Singleton extends SF_LIB_ApiObject {
    var $string = "Hi, I am a singleton - yesterday, today, tomorrow";

    function SF_TEST_Singleton() {
        $this->_API_setObjectIsSingleton(true);
    } 

    function get() {
        return $this->string;
    } 

    function set($v) {
        $this->string = $v;
    } 
} 

?>