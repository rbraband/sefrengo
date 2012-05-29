<?php
// File: $Id: inc.apiLoader.php 595 2011-12-18 22:15:26Z bjoern $
// +----------------------------------------------------------------------+
// | Version: Sefrengo $Name:  $
// +----------------------------------------------------------------------+
// | Copyright (c) 2005 - 2007 sefrengo.org <info@sefrengo.org>           |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License                 |
// |                                                                      |
// | This program is subject to the GPL license, that is bundled with     |
// | this package in the file LICENSE.TXT.                                |
// | If you did not receive a copy of the GNU General Public License      |
// | along with this program write to the Free Software Foundation, Inc., |
// | 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA               |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// +----------------------------------------------------------------------+
// + Autor: $Author: bjoern $
// +----------------------------------------------------------------------+
// + Revision: $Revision: 595 $
// +----------------------------------------------------------------------+
// + Description:
// +----------------------------------------------------------------------+
// + Changes:
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+

//get api path
$_api_path = str_replace ('\\', '/', dirname(__FILE__) . '/');

//set include pathes
$ini_separator = strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? ';' : ':';

//$ini_original = ini_get('include_path');
//$ini_original = ( strlen($ini_original) > 0 ) ? $ini_original. $ini_separator: '';
ini_set('include_path', '.' 
			. $ini_separator . $_api_path
			. $ini_separator . $_api_path . 'DEPRECATED/'
			. $ini_separator . preg_replace('!/API/$!', '/external/adodb5/', $_api_path)
			. $ini_separator . preg_replace('!/API/$!', '/external/pear.php.net/', $_api_path)			
			);

//echo $ini_original . $cfg_cms['path_base'].$cfg_cms['path_backend_rel'].  'external/adodb/';


require_once ($_api_path.'LIB/ApiObject.php');
require_once ($_api_path.'LIB/ApiObjectStore.php');
require_once ($_api_path.'LIB/ApiObjectFactory.php');

$sf_factory = new SF_LIB_ApiObjectFactory($_api_path, new SF_LIB_ApiObjectStore());

// helper functions

function sf_factoryGetObject($package, $classname, $subclassname = null, $params = null) {
	return $GLOBALS['sf_factory']->getObject($package, $classname, $subclassname, $params);
}

function sf_factoryGetObjectCache($package, $classname, $subclassname = null, $params = null, $cache_alias = 'default') {
	return $GLOBALS['sf_factory']->getObjectCache($package, $classname, $subclassname, $params, $cache_alias);
}

function sf_factoryObjectExistsInCache($package, $classname, $subclassname = null, $cache_alias = 'default') {
	return $GLOBALS['sf_factory']->objectExistsInCache($package, $classname, $subclassname, $cache_alias);
}

function sf_factoryCallMethod($package, $classname, $subclassname = null, $params = null, $method, $methodparms = null) {
	return $GLOBALS['sf_factory']->callMethod($package, $classname, $subclassname, $params, $method, $methodparms);
}

function sf_factoryCallMethodCache($package, $classname, $subclassname = null, $params = null, $method, $methodparms = null, $cache_alias = 'default') {
	return $GLOBALS['sf_factory']->callMethodCache($package, $classname, $subclassname, $params, $method, $methodparms, $cache_alias);
}

function sf_factoryClassExists($package, $classname) {
	return $GLOBALS['sf_factory']->classExists($package, $classname, $subclassname, $params);
}

function sf_api($package, $classname, $subclassname = null) {
	return sf_factoryGetObject($package, $classname, $subclassname);
}

function sf_api_exists($package, $classname) {
	return sf_factoryClassExists($package, $classname);
}

function sf_exception($priority, $message, $param = array()) {
	return sf_factoryGetObject('LIB', 'SefrengoException', null, array($priority, $message, $param));
}


//START TESTING STUFF
//$sf_db = $sf_factory->getObject('database', 'Ado'); 
//print_r($sf_db);
?>