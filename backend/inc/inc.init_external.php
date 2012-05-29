<?PHP
// File: $Id: inc.init_external.php 658 2012-02-28 22:10:54Z holger $
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
// + Autor: $Author: holger $
// +----------------------------------------------------------------------+
// + Revision: $Revision: 658 $
// +----------------------------------------------------------------------+
// + Description:
// +----------------------------------------------------------------------+
// + Changes: 
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+

error_reporting (E_ALL & ~E_NOTICE & ~E_DEPRECATED);

//send header
if(! defined('SF_SKIP_HEADER') ){
	header('Content-type: text/html; charset=UTF-8');
}

if(! defined('SKIP_COMMON_SETTINGS') ){
	// Output buffering starten
	ob_start();
	
	// Magic Quotes ausschalten
	set_magic_quotes_runtime (0);
	
	// zeige alle Fehlermeldungen, aber keine Warnhinweise
	error_reporting (E_ALL & ~E_NOTICE & ~E_DEPRECATED);
	
	// alle GET, POST und COOKIE wegen Globals_off parsen
	$types_to_register = array('GET','POST');
	foreach ($types_to_register as $global_type) {
	        $arr = @${'HTTP_'.$global_type.'_VARS'};
	        if (@count($arr) > 0) extract($arr, EXTR_OVERWRITE);
	        else {
	                 $arr = @${'_'.$global_type};
	                if (@count($arr) > 0) extract($arr, EXTR_OVERWRITE);
	        }
	}
	
	$sefrengo = ( empty($sefrengo) ) ? $_COOKIE['sefrengo']: $sefrengo;
}

// notwendige Dateien includen
$this_dir = str_replace ('\\', '/', dirname(__FILE__) . '/');
//go directory up
$strip_slash = substr ($this_dir, 0, strlen($folder)-1);
$to_replace = strrchr ($strip_slash, "/");
$pos = strrpos ($strip_slash, $to_replace);
$this_dir = substr($strip_slash, "0",$pos )."/";
chdir($this_dir);

	require_once ($this_dir.'inc/config.php');
	//Load API
	require_once ($this_dir.'API/inc.apiLoader.php');
	$config = sf_api('LIB', 'Config');
	$config->set('db', $cms_db);

	// Logger initialisieren
	$cms_log = sf_api('LIB', 'Logger');
	$cms_log->setIsBackend(TRUE);
	$cms_log->setLogfilePath($cfg_cms['log_path']);
	$cms_log->setLogfileSize($cfg_cms['log_size']);
	$cms_log->setLogfileMailAddress($cfg_cms['logfile_mailaddress']);
	
	require_once ($this_dir.'inc/class.cms_debug.php');
	include_once ($this_dir.'external/phplib/prepend.php');
	include_once ($this_dir.'inc/class.user_perms.php');
	include_once ($this_dir.'inc/class.values_ct.php');
	require_once ($this_dir.'inc/fnc.general.php');
	
	// Klassen initialisieren
	$db = new DB_cms;
	//$val_ct = new values_ct();
	
	// Konfigurationsparameter einlesen
	//$cfg_cms_temp = $val_ct -> get_cfg();
	//$cfg_cms = array_merge($cfg_cms, $cfg_cms_temp);
	$valcol = sf_api('MODEL', 'ValueSqlCollection');
	$valcol->getByGroup('cfg');
	$cfg_cms = (is_array($cfg_cms) == TRUE) ? $cfg_cms : array();
	$cfg_cms = array_merge($cfg_cms, $valcol->getAssocKeyArray());
	$valcol->getByGroup('lang');
	$cms_lang = (is_array($cms_lang) == TRUE) ? $cms_lang : array();
	$cms_lang = array_merge($cms_lang, $valcol->getAssocKeyArray());
	unset($valcol);
	//print_r($cfg_cms);
	unset($cfg_cms_temp);



$config->set('cms', $cfg_cms);
$config->setVal('env', 'path_backend_http', $config->cms('path_base_http').$config->cms('path_backend_rel'));
$config->setVal('env', 'path_backend', $config->cms('path_base').$config->cms('path_backend_rel'));
	
// Weitere Einstellungen werden nach erfolgreicher DB Connection gesetzt

// Session starten
if(! defined('SF_USE_FRONTEND_SESSION') ) {
	page_open(array('sess' => 'cms_Backend_Session',
                'auth' => 'cms_Backend_Auth'));
} else {
	page_open(array('sess' => 'cms_Frontend_Session',
                	'auth' => 'cms_Frontend_Auth'));
}
$config->set('sess', $sess);
$config->set('auth', $auth);

// Sessionvariablen initialisieren
$sess->register('sid_client');
$sess->register('sid_lang');
$sess->register('sid_lang_charset');
$sess->register('sid_area');
$sess->register('sid_sniffer');
$client       = (empty($client))       ? $sid_client       : $client;
$lang         = (empty($lang))         ? $sid_lang         : $lang;
//hack
$lang_charset = (empty($lang_charset)) ? 'iso-8859-1' : $lang_charset;


$perm         = new cms_perms($client, $lang);
$config->set('perm', $perm);

$client       = $perm -> get_client();
$lang         = $perm -> get_lang();
$lang_charset = $perm -> get_lang_charset();

$config->setVal('env', 'debug', $config->cms('display_errors'));
$config->setVal('env', 'idclient', $client);
$config->setVal('env', 'idlang', $lang);

// Projekt initialisieren
$sid_client = $client;
//$cfg_client = $val_ct -> get_by_group('cfg_client', $client);
$valcol = sf_api('MODEL', 'ValueSqlCollection');
$valcol->setIdclient($client);
$valcol->getByGroup('cfg_client');
$cfg_client = $valcol->getAssocKeyArray();
unset($valcol);



$config->set('client', $cfg_client);
$sf_path_base = $config->cms('path_base');
$sf_path_http_frontend = defined('SF_USE_FRONTEND_SESSION') ? $config->client('path_http') : $config->client('path_http_edit');
$sf_path_http_frontend = str_replace(array('{%http_host}', '{%request_uri}' ), array($_SERVER['HTTP_HOST'], $_SERVER['QUERY_STRING']), $sf_path_http_frontend);

$config->setVal('env', 'path_frontend_http', $sf_path_http_frontend.$config->client('path_rel'));
$config->setVal('env', 'path_frontend', $sf_path_base.$config->client('path_rel'));
$config->setVal('env', 'path_frontend_fm_http',$sf_path_http_frontend.$config->client('path_rel').$config->client('path_fm_rel'));
$config->setVal('env', 'path_frontend_fm', $sf_path_base.$config->client('path_rel').$config->client('path_fm_rel'));
$config->setVal('env', 'path_frontend_css_http', $sf_path_http_frontend.$config->client('path_rel').$config->client('path_css_rel'));
$config->setVal('env', 'path_frontend_css', $sf_path_base.$config->client('path_rel').$config->client('path_css_rel'));
$config->setVal('env', 'path_frontend_js_http', $sf_path_http_frontend.$config->client('path_rel').$config->client('path_js_rel'));
$config->setVal('env', 'path_frontend_js', $sf_path_base.$config->client('path_rel').$config->client('path_js_rel'));

// Sprache initialisieren
$sid_lang         = $lang;
$sid_lang_charset = $lang_charset;

// Multilanguage initialisieren
$config->setVal('client', 'langs', $config->getLangsForClient( $config->env('idclient') ));

// Weitere Einstellungen für Logger
$cms_log->setIdclient($client);
$cms_log->setIdlang($lang);
$cms_log->setStorage('screen', $cfg_cms['logs_storage_screen']);
$cms_log->setStorage('logfile', $cfg_cms['logs_storage_logfile']);
$cms_log->setStorage('database', $cfg_cms['logs_storage_database']);

// Sprachdatei einlesen
// change jb
$lang_dir = $this_dir.'lang/'.$cfg_cms['backend_lang'].'/';
if (file_exists ($lang_dir.'lang_general.php')) {
	require_once($lang_dir.'lang_general.php');
} else {
	require_once($this_dir.'lang/de/lang_general.php');
}
// change jb
?>