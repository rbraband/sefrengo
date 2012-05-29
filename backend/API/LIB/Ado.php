<?php
// File: $Id: class.SF_DATABASE_Ado.php 29 2008-05-11 19:19:53Z mistral $
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
// + Autor: $Author: mistral $
// +----------------------------------------------------------------------+
// + Revision: $Revision: 29 $
// +----------------------------------------------------------------------+
// + Description:
// +----------------------------------------------------------------------+
// + Changes:
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+

class SF_LIB_Ado extends SF_LIB_ApiObject
{
	protected $conn_ado;
	
	public function __construct()
	{
		global $cfg_cms;
		
		$this->_API_setObjectBridge(TRUE);
		$this->_API_setObjectIsSingleton(TRUE);
		
		if(!defined('ADODB_ERROR_HANDLER_TYPE'))
		{
			define('ADODB_ERROR_HANDLER_TYPE', E_USER_ERROR);
		}
		
		if(!defined('ADODB_ERROR_HANDLER'))
		{
			// function is defined below the class in this file
			define('ADODB_ERROR_HANDLER', 'sf_adodb_errorhandler');
		}
		
		include_once('adodb.inc.php');
		$this->conn_ado = ADONewConnection('mysql');
		
		if ($cfg_cms['db_mysql_pconnect'] === TRUE)
		{
			$this->conn_ado->PConnect($GLOBALS['cfg_cms']['db_host'], $GLOBALS['cfg_cms']['db_user'], 
									$GLOBALS['cfg_cms']['db_password'], $GLOBALS['cfg_cms']['db_database']);
		}
		else
		{
			$this->conn_ado->Connect($GLOBALS['cfg_cms']['db_host'], $GLOBALS['cfg_cms']['db_user'], 
									$GLOBALS['cfg_cms']['db_password'], $GLOBALS['cfg_cms']['db_database']);
		}
		
		if ($cfg_cms['debug_active'] )
		{
			$this->conn_ado->LogSQL();
		}
		if ($cfg_cms['db_utf8'] === true)
		{
      		$this->conn_ado->Execute("SET NAMES 'utf8'");
      	}
	}
	
	public function &_API_getBridgeObject()
	{
		return $this->conn_ado;	
	}
		
}

/**
* ADODB Error Handler
*
* @param string $dbms The RDBMS you are connecting to, e.g. mysql
* @param string $fn The name of the calling function (in uppercase)
* @param mixed $errno The native error number from the database
* @param string $errmsg The native error msg from the database
* @param string $p1 $fn specific parameter
* @param string $p2 $fn specific parameter
* @param $thisConn $current connection object
*/
function sf_adodb_errorhandler($dbms, $fn, $errno, $errmsg, $p1, $p2, $thisConn)
{
	$message = 'Error #'.$errno.': '.$errmsg."\n";
	$params = array(
		'errno' => $errno,
		'errmsg' => $errmsg,
		'adofn' => $fn
	);
	
	switch($fn)
	{
		case 'EXECUTE':
			$params['sql'] = $p1;
			$params['inputparams'] = $p2;
			break;
		
		case 'PCONNECT':
		case 'CONNECT':
			$params['host'] = $p1;
			$params['database'] = $p2;
			break;
			
		default:
			$params['p1'] = $p1;
			$params['p2'] = $p2;
			break;
	}
	
	//$cfg = sf_api('LIB', 'Config');
	$logger = sf_api('LIB', 'Logger');
	/*$logger->setIsBackend(TRUE);
	$logger->setLogfilePath($cfg->cms('log_path'));
	$logger->setLogfileSize($cfg->cms('log_size'));
	$logger->setLogfileMailAddress($cfg->cms('logfile_mailaddress'));
	$logger->setIdclient($cfg->env('idclient'));
	$logger->setIdlang($cfg->env('idlang'));
	$logger->setStorage('screen', $cfg->cms('logs_storage_screen'));
	$logger->setStorage('logfile', $cfg->cms('logs_storage_logfile'));
	$logger->setStorage('database', $cfg->cms('logs_storage_database'));*/
	
	$logger->error('sql', $message, $params);
}
?>
