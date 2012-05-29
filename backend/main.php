<?PHP
// File: $Id: main.php 670 2012-03-23 22:30:01Z bjoern $
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
// + Revision: $Revision: 670 $
// +----------------------------------------------------------------------+
// + Description:
// +----------------------------------------------------------------------+
// + Changes: 
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+

// Output buffering starten
ob_start();

// Magic Quotes ausschalten
if(ini_get('magic_quotes_runtime') === FALSE || ini_get('magic_quotes_runtime') != 0)
{
	ini_set('magic_quotes_runtime', 0);
}

// Pfad zum aktuellen Ordner bekommen
$this_dir = str_replace ('\\', '/', dirname(__FILE__) . '/');

// Inkludiert die Definition für die Fehlerbehandlung
require_once ($this_dir.'inc/inc.error_handling.php');

// Flag f�r Windows-Systeme, um auf Windows nicht existierende Befehle zu blocken
$is_win =  strtoupper(substr(PHP_OS, 0, 3) == 'WIN');

// alle GET, POST und COOKIE wegen Globals_off parsen
// $types_to_register = array('GET','COOKIE','POST','SERVER','FILES','ENV','SESSION','REQUEST');
$types_to_register = array('_GET','_POST');
foreach ($types_to_register as $global_type)
{
	$arr = @${$global_type};
	if (@count($arr) > 0)
	{
		extract($arr, EXTR_OVERWRITE);
	}
   
}
$cfg_cms = $cfg_client = array();

$sefrengo = ( empty($sefrengo) ) ? $_COOKIE['sefrengo']: $sefrengo;


// notwendige Dateien includen
if (! is_file($this_dir.'inc/config.php')) {
	die('NO CONFIGFILE FOUND');
}
require_once ($this_dir.'inc/config.php');


//Load API
require_once ($this_dir.'API/inc.apiLoader.php');

require_once ($this_dir.'inc/class.cms_debug.php');
include_once ('HTML/Template/IT.php');
include_once ($this_dir.'external/phplib/prepend.php');
include_once ($this_dir.'inc/class.user_perms.php');
include_once ($this_dir.'inc/class.values_ct.php');
require_once ($this_dir.'inc/fnc.general.php');
require_once ($this_dir.'inc/fnc.libary.php');
include_once ($this_dir.'inc/class.repository.php');

$config = sf_api('LIB', 'Config');
$config->set('db', $cms_db);

// Logger initialisieren
$cms_log = sf_api('LIB', 'Logger');
$cms_log->setIsBackend(TRUE);
$cms_log->setLogfilePath($cfg_cms['log_path']);
$cms_log->setLogfileSize($cfg_cms['log_size']);
$cms_log->setLogfileMailAddress($cfg_cms['logfile_mailaddress']);
// Weitere Einstellungen werden nach erfolgreicher DB Connection gesetzt

// Klassen initialisieren
$db = new DB_cms;
// Konfigurationsparameter einlesen
//$val_ct = new values_ct();
//$cfg_cms_temp = $val_ct -> get_cfg();
//$cfg_cms = array_merge($cfg_cms, $cfg_cms_temp);
//unset($cfg_cms_temp);
$valcol = sf_api('MODEL', 'ValueSqlCollection');
$valcol->getByGroup('cfg');
$cfg_cms = array_merge($cfg_cms, $valcol->getAssocKeyArray());
unset($valcol);
$config->set('cms', $cfg_cms);
$config->setVal('env', 'path_backend_http', $config->cms('path_base_http').$config->cms('path_backend_rel'));
$config->setVal('env', 'path_backend', $config->cms('path_base').$config->cms('path_backend_rel'));

// dB Optimice
if ( $cfg_cms['db_optimice_tables_enable'] && (time() > ($cfg_cms['db_optimice_tables_last_run'] + $cfg_cms['db_optimice_tables_time']))) {
    lib_optimice_tables();
	//$val_ct->set_value(array('group' => 'cfg', 'client' => 0, 'key' => 'db_optimice_tables', 'key2' => 'last_run', 'value' => time()));
	$valitem = sf_api('MODEL', 'ValueSqlItem');
	$valitem->loadByGroupAndKeys('cfg', array('key1' => 'db_optimice_tables_last_run'));
	$valitem->setField('value', time());
	$valitem->save();
	unset($valitem);
}

// Template initialisieren
$tpl = new HTML_Template_IT($this_dir.'tpl/'.$cfg_cms['skin'].'/');
$config->set('tpl', $tpl);

// Session starten
page_open(array('sess' => 'cms_Backend_Session',
                'auth' => 'cms_Backend_Auth'));
$config->set('sess', $sess);
$config->set('auth', $auth);

// Sessionvariablen initialisieren
$sess->register('sid_client');
$sess->register('sid_lang');
$sess->register('sid_lang_charset');
$sess->register('sid_area');
$client       = (empty($client))       ? $sid_client       : $client;
$lang         = (empty($lang))         ? $sid_lang         : $lang;
$lang_charset = (empty($lang_charset)) ? $sid_lang_charset : $lang_charset;

$perm         = new cms_perms($client, $lang);
$config->set('perm', $perm);

$client       = $perm -> get_client();
$lang         = $perm -> get_lang();
$lang_charset = $perm -> get_lang_charset();

$config->setVal('env', 'debug', $config->cms('display_errors'));
$config->setVal('env', 'idclient', $client);
$config->setVal('env', 'idlang', $lang);

// Projekt initialisieren
$sid_client   = $client;

// Sprache initialisieren
$sid_lang         = $lang;
$sid_lang_charset = $lang_charset;

// Multilanguage initialisieren
//$valcol = sf_api('MODEL', 'ValueSqlCollection');
//$valcol->setIdclient($client);
//$valcol->setIdlang($lang);
//$valcol->getByGroup('cfg');
//$cfg_cms = array_merge($cfg_cms, $valcol->getAssocKeyArray());
//unset($valcol);

$config->setVal('client', 'langs', $config->getLangsForClient( $config->env('idclient') ));

// Weitere Einstellungen fuer Logger
$cms_log->setIdclient($client);
$cms_log->setIdlang($lang);
$cms_log->setStorage('screen', $cfg_cms['logs_storage_screen']);
$cms_log->setStorage('logfile', $cfg_cms['logs_storage_logfile']);
$cms_log->setStorage('database', $cfg_cms['logs_storage_database']);

//$cms_log->error('bla', 'plug_export_text', array('name'=>'test'));

// If area exists, update value in session
if (isset($area))
{
	// $dais = Disable (store) area in session
	if(!isset($dais) || (isset($dais) && ($dais == '0' || $dais == 'false')))
	{
		$pairs = explode('_', $area, 2);
		$sid_area = (array_key_exists('0', $pairs) == TRUE) ? $pairs[0] : $area;
		unset($pairs);
	}
}
// Otherwise initialize area with stored session value
else 
{
	$area = $sid_area;
}

// Wenn area nicht erlaubt ist, redirecten auf erlaubte area, wenn möglich
$pos = strpos($area, '_');
$allowed_area = (!$pos) ? $area: substr( $area, 0, $pos );
if( !$perm->have_perm('area_'. $allowed_area) && $area != 'logout' && $area != 'plugin'){
	$new_area = $perm->get_first_allowed_area();
	if(! empty($new_area)){
		$area = $new_area;
		unset($new_area);
	}
}

// Sprachdatei einlesen
$lang_dir = $this_dir.'lang/'.$cfg_cms['backend_lang'].'/';
$lang_defdir = $this_dir.'lang/de/';
require_once( ( file_exists($lang_dir.'lang_general.php') ? $lang_dir: $lang_defdir ) .'lang_general.php');
// Sprachdatei f�r Area einlesen
if (file_exists ($lang_dir."lang_$allowed_area.php")) {
	include_once($lang_dir."lang_$allowed_area.php");
} else {
	//$deb -> collect('Fehlt: Sprachdatei für Area: ' . $area);
	//$cms_log->warning('sefrengo', 'Sprachdatei für Area "' . $area . '" fehlt!', array('file' => basename(__FILE__), 'line' => __LINE__));
	if (file_exists ($lang_defdir."lang_$allowed_area.php")) {
		include_once($lang_defdir."lang_$allowed_area.php");
	}
}

$valcol = sf_api('MODEL', 'ValueSqlCollection');
$valcol->setIdclient($client);
$valcol->getByGroup('lang');
$cms_lang = array_merge($cms_lang, $valcol->getAssocKeyArray());
unset($valcol);


// Log für erfolgreichen Login nachliefern und Variable in Session speichern, um doppelten Log zu verhindern 
if($sess->is_registered('login_success') === false) {
	$login_success = true;
	$cms_log->info('user', 'login_success');
	$sess->register('login_success');
}

// Rechte �berpr�fen
//$cfg_client = $val_ct -> get_by_group('cfg_client', $client);
$valcol = sf_api('MODEL', 'ValueSqlCollection');
$valcol->setIdclient($client);
$valcol->getByGroup('cfg_client');
$cfg_client = $valcol->getAssocKeyArray();
		$cfg_client['path_http'] = str_replace(array('{%http_host}', '{%request_uri}' ), array($_SERVER['HTTP_HOST'], $_SERVER['QUERY_STRING']), $cfg_client['path_http']);
		$cfg_client['path_http_edit'] = str_replace(array('{%http_host}', '{%request_uri}' ), array($_SERVER['HTTP_HOST'], $_SERVER['QUERY_STRING']), $cfg_client['path_http_edit']);
unset($valcol);

$valcol = sf_api('MODEL', 'ValueSqlCollection');
$valcol->setIdclient($client);
$valcol->setIdlang($lang);
$valcol->getByGroup('cfg_lang');
$cfg_lang = $valcol->getAssocKeyArray();
/*$deb -> collect('Projekt Id: ' . $client);
$deb -> collect('Lang Id: '    . $lang);
$deb -> collect('User Id: '    . $perm -> user_id);
$deb -> collect('Group Id: '   . $perm -> idgroup);
$deb -> collect('File:' .__FILE__.' Line:' .__LINE__, 'mem');*/
$config->set('client', $cfg_client);
$config->set('lang', $cfg_lang);
$sf_path_base = $config->cms('path_base');
$sf_path_http_frontend = $config->client('path_http_edit');
$config->setVal('env', 'path_frontend_http', $sf_path_http_frontend.$config->client('path_rel'));
$config->setVal('env', 'path_frontend', $sf_path_base.$config->client('path_rel'));
$config->setVal('env', 'path_frontend_fm_http',$sf_path_http_frontend.$config->client('path_rel').$config->client('path_fm_rel'));
$config->setVal('env', 'path_frontend_fm', $sf_path_base.$config->client('path_rel').$config->client('path_fm_rel'));
$config->setVal('env', 'path_frontend_css_http', $sf_path_http_frontend.$config->client('path_rel').$config->client('path_css_rel'));
$config->setVal('env', 'path_frontend_css', $sf_path_base.$config->client('path_rel').$config->client('path_css_rel'));
$config->setVal('env', 'path_frontend_js_http', $sf_path_http_frontend.$config->client('path_rel').$config->client('path_js_rel'));
$config->setVal('env', 'path_frontend_js', $sf_path_base.$config->client('path_rel').$config->client('path_js_rel'));

// Repository laden
$rep = new repository;
// Run init Plugins
if ( $cfg_rep['repository_init_plugins'] ) $rep->init_plugins();


// basecontroller
$sf_controller = '';
$sf_area = 'index';

$pairs = explode('_', $area, 2);

// controller
if (array_key_exists('0', $pairs))
{
	if (strlen(trim($pairs['0'])) > 0)
	{
		$sf_controller = $pairs['0'];
	}
}

// area
if (array_key_exists('1', $pairs))
{
	if (strlen(trim($pairs['1'])) > 0)
	{
		// transform area to camelcase
		$sf_area = preg_replace("/_+([a-z])/ie", 'strtoupper("$1")', trim($pairs['1']));
	}
}

// Load Controller
$sf_controller = ucfirst($sf_controller);
//Pluginloader
if (is_dir($config->env('path_backend') .'plugins/'. strtolower($sf_controller) . '/localapi'))
{
	$sf_factory->addIncludePath($config->env('path_backend') .'plugins/'. strtolower($sf_controller) . '/localapi');
}
//Backendloader
if ($sf_factory->classExists('CONTROLLER', $sf_controller))
{
	if (file_exists ($lang_dir."lang_$sf_controller.php"))
	{
		include_once($lang_dir."lang_$sf_controller.php");
	}
	
	$controller = sf_api('CONTROLLER', $sf_controller);
	if (method_exists($controller, $sf_area) && substr($sf_area,0,1) != '_')
	{
		//Set Controller Vars
		call_user_method('setInitArea', $controller, $sf_controller, trim($pairs['1']));
		//Call init all Method
		call_user_method('initControllerCall', $controller);
		// Call Controller
		echo call_user_method($sf_area, $controller);
	}
	else
	{
		echo 'Controller area not found or forbidden';
	}
}

// Fallback: Choose area via include
else if( is_readable("inc/inc.$area.php") )
{
  include("inc/inc.$area.php");
  // Generate template
	$tpl->show();
}
// No controller or area found
else
{
	echo 'Controller not found.';exit;
}
	
// unset vars
unset($sf_controller, $sf_area, $pairs, $controller);



// Output buffering beenden
if ($area != 'logout') page_close();
$output = ob_get_contents();
ob_end_clean();

// event to manipulate the output
$event = fire_event('backend_code_generated', array('output' => $output), array('output'));

$output = $event['output'];
unset($event);

//Logs auf Screen ausgeben, wenn Logs vorhanden
if( count( $cms_log->getLogs() ) > 0 ) {
	$log_output = sf_api('VIEW', 'LogOutput');
	$log_output->addItemsArray( $cms_log->getLogs() );
	
	$searches = array('<div id="sf_loading">', '</body>');
	foreach($searches as $search)
	{
		$pos = strpos($output, $search);
		if($pos !== FALSE)
		{
			// replace the last occurence of $search
			$output = substr_replace($output, ("\n".$log_output->show()."\n".$search), $pos, strlen($search));
			break; // exit
		}
	}
}

// Seite komprimieren und ausgeben
$ACCEPT_ENCODING = getenv("HTTP_ACCEPT_ENCODING");
if (array_key_exists('gzip', $cfg_cms) && $cfg_cms['gzip'] == '1' && ereg("gzip",$ACCEPT_ENCODING) && headers_sent() == FALSE)
{
	@ob_start('ob_gzhandler');
	eval($cfg_cms['manipulate_output']);
	@ob_end_flush();
}
else
{
	eval($cfg_cms['manipulate_output']);
}
// phpinfo();
?>