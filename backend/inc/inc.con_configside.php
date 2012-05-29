<?PHP
// File: $Id: inc.con_configside.php 670 2012-03-23 22:30:01Z bjoern $
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

if(! defined('CMS_CONFIGFILE_INCLUDED')){
	die('NO CONFIGFILE FOUND');
}

/**
 * 1. Benötigte Funktionen und Klassen includieren
 */

include('inc/fnc.tpl.php');
include('inc/fnc.mipforms.php');
include('inc/fnc.mod_rewrite.php');

/**
 * 2. Eventuelle Actions/ Funktionen abarbeiten
 */

// idcatside vorhanden, prüfen, ob Recht zum konfigurieren gegeben ist
if(is_numeric($idcatside))$perm->check(20, 'side', $idcatside, $idcat);
//Neue Seite, prüfen, ob recht auf neue Seite anlegen vorhanden ist
else $perm->check(18, 'cat', $idcat);


// rewrite check
$sf_is_rewrite_error = false;
if ($action == 'save') {
	$have_rewrite_perm = ( is_numeric($idcatside) ) ? $perm->have_perm(31, 'side', $idcatside, $idcat): $perm -> have_perm(31, 'cat', $idcat) ;
	if ($cfg_client['url_rewrite'] == '2' && $have_rewrite_perm) {
		 if($_REQUEST['rewrite_use_automatic'] != '1') {
			if (! rewriteUrlIsAllowed($_REQUEST['rewrite_url'], true)) {
				$sf_is_rewrite_error = true;
				$sf_rewrite_error_message = $cms_lang['err_rw_01'];
				$action = 'change';
			} else if (! rewriteUrlIsUnique('idcatside', $idcatside, $_REQUEST['rewrite_url'])) {
				$sf_is_rewrite_error = true;
				$sf_rewrite_error_message = $cms_lang['err_rw_02'];
				$action = 'change';				
			} else if (rewriteManualUrlMatchAutoUrl($_REQUEST['rewrite_url'])) {
				$sf_is_rewrite_error = true;
				$sf_rewrite_error_message = $cms_lang['err_rw_03'];
				$action = 'change';	
			}
		 } 
	}
}



// Seitenkonfiguration speichern
switch($action) {
	case 'save':  // Template bearbeiten
		$use_redirect = isset($_REQUEST['sf_apply']) ? false: true;
		con_config_side_save($idcat, $idside, $idtpl, $idtplconf, $idsidelang, $idcatside, $idcatnew
                                       , $author, $title, $meta_keywords, $summary, $online, $user_protected
                                       , $view, $created, $lastmodified, $startdate, $starttime, $enddate, $endtime
                                       , $meta_author, $meta_description, $meta_robots, $meta_redirect_time
                                       , $meta_redirect, $meta_redirect_url, $meta_is_https, $rewrite_use_automatic, $rewrite_url
                                       , $idlay, $use_redirect);
		if ( isset($_REQUEST['sf_apply']) ) {
			$sql = "SELECT idtplconf FROM " . $cms_db['side_lang'] ." WHERE idside = $idside AND idlang=$lang";
			$db->query($sql);
			$db->next_record();
			$idtplconf = $db->f('idtplconf');
		}
		break;
	case 'change':  // Layout oder Modul wechseln
		$cconfig = tpl_change($idlay);
		break;
}

/**
 * 3. Eventuelle Dateien zur Darstellung includieren
 */

// getrennte Header f�r Backend und Frontendbearbeitung
if (empty($view)) include('inc/inc.header.php');
else {
	include_once ($cfg_cms['path_base'].$cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/frontend_config_header.php');
	$frontend_header=str_replace('{SF-VERSION}',$cfg_cms['version'],$frontend_header);
	$frontend_header=str_replace('{SKIN}',$cfg_cms['skin'],$frontend_header);
	echo $frontend_header;
}


//seite vorhanden
if ((!$action && $idside) || isset($_REQUEST['sf_apply']) && ! $sf_is_rewrite_error) {
	// Ordner der Seite suchen
	$sql = "SELECT idcat FROM ".$cms_db['cat_side']." WHERE idside='$idside'";
	$db->query($sql);
	while ($db->next_record()) $idcatnew[] = $db->f('idcat');
//echo "x";
	// Konfiguration suchen
    $sql = "SELECT * FROM ".$cms_db['side_lang']." A WHERE idside='$idside' AND idlang='$lang'";
	$db->query($sql);
	$db->next_record();
	$idsidelang    = $db->f('idsidelang');
	$title         = htmlentities($db->f('title'), ENT_COMPAT, 'UTF-8');
	$author        = $db->f('author');
	$created       = $db->f('created');
	$summary       = htmlentities(stripslashes($db->f('summary')), ENT_COMPAT, 'UTF-8');
	$lastmodified  = $db->f('lastmodified');
	$startdate     = date('d.m.Y', $db->f('start'));
	$starttime     = date('H:i', $db->f('start'));
	$enddate       = date('d.m.Y', $db->f('end'));
	$endtime       = date('H:i', $db->f('end'));
	$online        = $db->f('online');
	$userprotected = ($db->f('user_protected') == '1') ? 'selected' : '';
	$meta_is_https = ($db->f('is_https') == '1') ? 'checked="checked"' : '';
	$meta_author = htmlentities($db->f('meta_author'), ENT_COMPAT, 'UTF-8');
	$meta_description = htmlentities($db->f('meta_description'), ENT_COMPAT, 'UTF-8');
	$meta_keywords = htmlentities($db->f('meta_keywords'), ENT_COMPAT, 'UTF-8');
	$meta_robots = $db->f('meta_robots');
	$meta_redirect = ($db->f('meta_redirect') == '1') ? ' checked' : '';
	$meta_robots_time = $db->f('meta_robots_time');
	$meta_redirect_url = ($db->f('meta_redirect_url') != '') ? $db->f('meta_redirect_url') : 'http://';
	$rewrite_use_automatic = $db->f('rewrite_use_automatic');
	$rewrite_url    = $db->f('rewrite_url');	
//Neu
} elseif (!$action && !$idside) {
	$idcatnew['0'] = $idcat;
	$author = $auth->auth['uid'];
	$created = time();
	$online = '0';
	$startdate = date('d.m.Y', time());
	$starttime = '00:00';
	$enddate = date('d.m.Y', time());
	$endtime = '00:00';
	$lastmodified = $created;
	$sql = "SELECT * FROM ". $cms_db['users'] ." WHERE user_id='".$auth->auth['uid']."'";
	$db->query($sql);
	$db->next_record();
	$meta_author = htmlentities($db->f('name').' '.$db->f('surname'), ENT_COMPAT, 'UTF-8');
	//$cfg_lang = $val_ct -> get_by_group('cfg_lang', $client, $lang);
	$valcol = sf_api('MODEL', 'ValueSqlCollection');
	$valcol->getByGroup('cfg_lang');
	$cfg_lang = $valcol->getAssocKeyArray();
	$meta_description = htmlentities($config->lang('meta_description'), ENT_COMPAT, 'UTF-8');
	$meta_keywords = htmlentities($config->lang('meta_keywords'), ENT_COMPAT, 'UTF-8');
	$meta_robots = htmlentities($config->lang('meta_robots'), ENT_COMPAT, 'UTF-8');
	$meta_redirect_url = 'http://';
	$meta_is_https = '';
	$rewrite_use_automatic = 1;
	$rewrite_url    = '';
//templatewechsel
} else {
	if (!is_array($idcatnew)) $idcatnew['0'] = $idcat;
	$meta_redirect = ($meta_redirect == '1') ? ' checked' : '';
	$meta_redirect_url = ($meta_redirect_url != '') ? $meta_redirect_url : 'http://';
	$userprotected = ($user_protected == '1') ? 'selected' : '';
	$rewrite_use_automatic = $_REQUEST['rewrite_use_automatic'];
	$rewrite_url    = (string) $_REQUEST['rewrite_url'];
	$meta_is_https = ($_REQUEST['meta_is_https'] == '1') ? 'checked="checked"' : '';
}


// Selectbox darf Seite sperren erzeugen
// DEPRECATED
//$have_sidelock_perm = (is_numeric($idcatside)) ? $perm->have_perm(31, 'side', $idcatside, $idcat): $perm -> have_perm(31, 'cat', $idcat) ;
//if($have_sidelock_perm) {
//	$select_lock_side = "<select name=\"user_protected\" size=\"1\">\n
//	<option value=\"\">".$cms_lang['con_side_not_locked_for_other_editors']."</option>\n
//	<option value=\"1\" $userprotected>".$cms_lang['con_side_locked_for_other_editors']."</option>\n
//	</select>\n";
//} else 


/**
 * 4. Bildschirmausgabe aufbereiten und ausgeben
 */

$tpl->loadTemplatefile('side_config.tpl', false);
$tpl->setVariable('SKIN',$cfg_cms['skin']);	

// URL REWRITE
$have_rewrite_perm = ( is_numeric($idcatside) ) ? $perm->have_perm(31, 'side', $idcatside, $idcat, true): $perm -> have_perm(31, 'cat', $idcat) ;
if ($cfg_client['url_rewrite'] == '2' && $have_rewrite_perm) {

	$tpl->setCurrentBlock('URL_REWRITE');

	$tpl_data['LNG_REWRITE_PAGE-URL'] = $cms_lang['con_side_page_url'];
	$tpl_data['LNG_REWRITE_URL-OF-THIS-PAGE'] = $cms_lang['con_side_urlofthisside'];
	$tpl_data['LNG_REWRITE_AUTO-URL'] = $cms_lang['con_side_rwpath_autourl'];

	$tpl_data['REWRITE_USE_AUTOMATIC_CHECKED'] = ($rewrite_use_automatic == 1) ? 'checked="checked" ':'';
	$tpl_data['REWRITE_URL'] = $rewrite_url;
	$tpl_data['REWRITE_URL_DISABLED'] = ($rewrite_use_automatic == 1) ? 'disabled="disabled" ':'';
	$tpl_data['REWRITE_ERROR'] = $rewrite_error = ($sf_is_rewrite_error) ? '<p class="errormsg">'.$sf_rewrite_error_message.'</p>':'';

	if ($rewrite_use_automatic == 1) {
		$tpl_data['REWRITE_CURRENT_URL'] = ($rewrite_url == '') ? rewriteGetPath($idcat, $lang). '<em>'.$cms_lang['con_side_rwpath_thisside'].'</em>'. $cfg_client['url_rewrite_suffix']: rewriteGetPath($idcat, $lang). '<strong>'.$rewrite_url.'</strong>'. $cfg_client['url_rewrite_suffix'];
	} else {
		$tpl_data['REWRITE_CURRENT_URL'] = ($rewrite_url == '') ?  '<em>'.$cms_lang['con_side_rwpath_thisside'].'</em>': '<strong>'.$rewrite_url.'<strong>';
	}
	$tpl_data['REWRITE_CURRENT_URL'] = 'http://<em>{domain.xyz}</em>/'. $tpl_data['REWRITE_CURRENT_URL'];

	$tpl->setVariable($tpl_data);

	$tpl->parseCurrentBlock();
	unset($tpl_data);

} else {

	$tpl->setCurrentBlock('HIDDEN-FIELDS_FIELD');
	$tpl->setVariable('FIELD-NAME','rewrite_use_automatic');
	$tpl->setVariable('FIELD-VALUE',$rewrite_use_automatic);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','rewrite_url');
	$tpl->setVariable('FIELD-VALUE',$rewrite_url);
	$tpl->parseCurrentBlock();

}


// Zeitsteuerung
$radio_visibility['ONLINE-STATE_C0-LABEL'] = $cms_lang['con_side_offline'];
$radio_visibility['ONLINE-STATE_C1-LABEL'] = $cms_lang['con_side_online'];
$radio_visibility['ONLINE-STATE_C2-LABEL'] = $cms_lang['con_side_time'];
$radio_visibility['ONLINE'] = $online;
$radio_visibility['ONLINE-STATE_C0-CHECKED'] = '';
$radio_visibility['ONLINE-STATE_C1-CHECKED'] = '';
$radio_visibility['ONLINE-STATE_C2-CHECKED'] = '';

if (((int)$online & 0x03) == 0x00) {
	$radio_visibility['ONLINE-STATE_C0-CHECKED'] = 'checked="checked"';
	$radio_visibility['ONLINE-STATE_TIMER-ACTIVE'] = 'style="display:none;"';
}

if (((int)$online & 0x03) == 0x01) {
	$radio_visibility['ONLINE-STATE_C1-CHECKED'] = 'checked="checked"';
	$radio_visibility['ONLINE-STATE_TIMER-ACTIVE'] = 'style="display:none;"';
}

if (((int)$online & 0x03) == 0x02) {
	$radio_visibility['ONLINE-STATE_C2-CHECKED'] = 'checked="checked"';
	$radio_visibility['ONLINE-STATE_TIMER-ACTIVE'] = 'style="display:run-in;"';
}


// Datumsangaben für Ausgabe formatieren
$print_created = date($cfg_cms['format_date'].' '.$cfg_cms['format_time'],$created);
$print_lastmodified = date($cfg_cms['format_date'].' '.$cfg_cms['format_time'],$lastmodified);

// Zeitsteuerung
$timer_dates['STARTDATE']= $startdate;
$timer_dates['STARTTIME']=$starttime ;
$timer_dates['LNG_STARTTIME']=$cms_lang['con_timemanagement_starttime'];
$timer_dates['LNG_ENDTIME']= $cms_lang['con_timemanagement_endtime'];
$timer_dates['ENDDATE']=$enddate;
$timer_dates['ENDTIME']=$endtime;


$have_online_offline_perm = ( is_numeric($idcatside) ) ? $perm->have_perm(23, 'side', $idcatside, $idcat): $perm -> have_perm(23, 'cat', $idcat) ;
if($have_online_offline_perm) {

$select_lock_side = '';

	$tpl->setCurrentBlock('TIMER_BLOCK');

	$tpl_data['VISBILITY_DESC'] = $cms_lang['con_visibility'];
	$tpl_data['LANG_SIDE_IS'] = $cms_lang['con_side_is'];
	$tpl_data['LANG_SIDE_IS'] = $cms_lang['con_side_is'];
	$tpl_data['LNG_CALENDAR'] = $cms_lang['con_calendar'];
	$tpl->setVariable($radio_visibility);
	$tpl->setVariable($timer_dates);
	$tpl_data['LANG_ONLINE'] = $cms_lang['con_side_is_online_at'];
	$tpl_data['LANG_OFFLINE'] = $cms_lang['con_side_is_offline_at'];
	$tpl->setVariable($tpl_data);
	$tpl->parseCurrentBlock();
	unset($tpl_data);
	
} else {

	$tpl->setCurrentBlock('HIDDEN-FIELDS_FIELD');
	$tpl->setVariable('FIELD-NAME','online');
	$tpl->setVariable('FIELD-VALUE',$radio_visibility['ONLINE']);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','startdate');
	$tpl->setVariable('FIELD-VALUE',$timer_dates['STARTDATE']);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','starttime');
	$tpl->setVariable('FIELD-VALUE',$timer_dates['STARTTIME']);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','enddate');
	$tpl->setVariable('FIELD-VALUE',$timer_dates['ENDDATE']);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','endtime');
	$tpl->setVariable('FIELD-VALUE',$timer_dates['ENDTIME']);
	$tpl->parseCurrentBlock();

}

// HTTPS
if ($cfg_client['https'] == 1)
{
	$tpl_data['META_IS_HTTPS_CHECKED'] = $meta_is_https;
	$tpl_data['LNG_META_TITLE_HTTPS'] = $cms_lang['con_https_headline'];
	$tpl_data['LNG_META_DESC_HTTPS'] = $cms_lang['con_https_desc'];
}
else
{
	$tpl->setCurrentBlock('HIDDEN-FIELDS_FIELD');
	$tpl->setVariable('FIELD-NAME','meta_is_https');
	$tpl->setVariable('FIELD-VALUE', (strlen($meta_is_https) > 2 ? 1:0) );
	$tpl->parseCurrentBlock();
	unset($tpl_data);
}


// Seiten verschieben, clonen
$sql = "SELECT A.idcat, parent, sortindex, name, idtplconf
	FROM ".$cms_db['cat']." A
	LEFT JOIN ".$cms_db['cat_lang']." B USING(idcat)
	WHERE B.idlang='$lang'
	AND A.idclient='$client'
	ORDER BY parent, sortindex";
$db->query($sql);
while ($db->next_record()) {
	$con_tree[$db->f('idcat')]['name'] = $db->f('name');
	$con_tree[$db->f('idcat')]['idtplconf'] = $db->f('idtplconf');
	$tlo_tree[$db->f('parent')][$db->f('sortindex')] = $db->f('idcat');
}
tree_level_order('0', 'catlist');

$have_move_perm = (is_numeric($idcatside)) ? $perm->have_perm(30, 'side', $idcatside, $idcat): $perm -> have_perm(30, 'cat', $idcat);

if ($have_move_perm) {

	if (is_array($catlist)) {
		$tpl->setCurrentBlock('SELECT-SIDEMOVE_ENTRY');
		foreach ($catlist as $a) {
			if ($con_tree[$a]['idtplconf'] != '0' && $perm -> have_perm(1, 'cat', $a) || $a == $idcat) {
				$spaces = '&nbsp;';
				for ($i=0; $i<$catlist_level[$a]; $i++) $spaces = $spaces.'&nbsp;&nbsp;';
				if (!in_array($a,$idcatnew)) {
					$tpl->setVariable('SELECT-SIDEMOVE_ENTRY-VALUE',$a);
					$tpl->setVariable('SELECT-SIDEMOVE_ENTRY-SELECTED','');
					$tpl->setVariable('SELECT-SIDEMOVE_ENTRY-TITLE',$spaces." ".$con_tree[$a]['name']);
					$tpl->parseCurrentBlock();
				} else {
					$tpl->setCurrentBlock('SELECT-SIDEMOVE_ENTRY');
					$tpl->setVariable('SELECT-SIDEMOVE_ENTRY-VALUE',$a);
					$tpl->setVariable('SELECT-SIDEMOVE_ENTRY-SELECTED','selected="selected"');
					$tpl->setVariable('SELECT-SIDEMOVE_ENTRY-TITLE',$spaces." ".$con_tree[$a]['name']);
					$tpl->parseCurrentBlock();
				}
			}
		}
	}

	$tpl->setCurrentBlock('CLONE_AND_NOTICE');
	$tpl_data['LANG_MOVE_SIDE'] = $cms_lang['con_move_side'];
	$tpl_data['SELECT_SIDEMOVE'] = $select_sidemove;
	$tpl_data['LANG_NOTICES'] = $cms_lang['con_notices'];
	$tpl_data['SUMMARY'] = empty($summary) ? '' : $summary;
	$tpl->setVariable($tpl_data);
	$tpl->parseCurrentBlock();
	unset($tpl_data);

} else {

	$tpl->setCurrentBlock('NOTICE');
	$tpl_data['LANG_NOTICES'] = $cms_lang['con_notices'];
	$tpl_data['SUMMARY'] = empty($summary) ? '' : $summary;
	$tpl_data['HIDDEN_CLONES'] = $select_sidemove_hidden;
	$tpl->setVariable($tpl_data);
	$tpl->parseCurrentBlock();
	unset($tpl_data);
	
	if (is_array($catlist)) {
		foreach ($catlist as $a) {
			if ($con_tree[$a]['idtplconf'] != '0' && $perm -> have_perm(1, 'cat', $a) || $a == $idcat) {
				if (in_array($a,$idcatnew)) {
					$tpl->setCurrentBlock('HIDDEN-FIELDS_FIELD');
					$tpl->setVariable('FIELD-NAME','idcatnew[]');
					$tpl->setVariable('FIELD-VALUE', $a);
					$tpl->parseCurrentBlock();
				}
			}
		}
	}

}

// Metaangaben bearbeiten

// se-indication
$html_robots['SE-INDICATION_E0-TITLE'] = $cms_lang['con_metarobotsif'];
$html_robots['SE-INDICATION_E1-TITLE'] = $cms_lang['con_metarobotsin'];
$html_robots['SE-INDICATION_E2-TITLE'] = $cms_lang['con_metarobotsnf'];
$html_robots['SE-INDICATION_E3-TITLE'] = $cms_lang['con_metarobotsnn'];
$html_robots['SE-INDICATION_E0-CHECKED'] = '';
$html_robots['SE-INDICATION_E1-CHECKED'] = '';
$html_robots['SE-INDICATION_E2-CHECKED'] = '';
$html_robots['SE-INDICATION_E3-CHECKED'] = '';

if ($meta_robots == 'index, follow')
	$html_robots['SE-INDICATION_E0-CHECKED'] = 'checked="checked"';

if ($meta_robots == 'index, nofollow')
	$html_robots['SE-INDICATION_E1-CHECKED'] = 'checked="checked"';

if ($meta_robots == 'noindex, follow')
	$html_robots['SE-INDICATION_E2-CHECKED'] = 'checked="checked"';

if ($meta_robots == 'noindex, nofollow')
	$html_robots['SE-INDICATION_E3-CHECKED'] = 'checked="checked"';


$have_meta_perm = (is_numeric($idcatside)) ? $perm->have_perm(29, 'side', $idcatside, $idcat): $perm -> have_perm(29, 'cat', $idcat);
if ($have_meta_perm) {
	$tpl->setCurrentBlock('META');
	$tpl_data['LANG_CON_METACONFIG'] = $cms_lang['con_metaconfig'];
	$tpl_data['LANG_META_DESC'] = $cms_lang['con_metadescription'];
	$tpl_data['META_DESC'] = $meta_description;
	$tpl_data['LANG_META_KEYWORDS'] = $cms_lang['con_metakeywords'];
	$tpl_data['META_KEYWORDS'] = $meta_keywords;
	$tpl_data['LANG_META_AUTHOR'] = $cms_lang['con_metaauthor'];
	$tpl_data['LANG_META_ROBOTS'] = $cms_lang['con_metarobots'];
	$tpl_data['META_AUTHOR'] = $meta_author;
	$tpl_data['LANG_META_REDIRECT'] = $cms_lang['con_metaredirect'];
	$tpl_data['META_REDIRECT'] = $meta_redirect;
	$tpl_data['META_REDIRECT_URL'] = $meta_redirect_url;
	$tpl->setVariable($html_robots);
	$tpl->setVariable($tpl_data);
	$tpl->parseCurrentBlock();
	unset($tpl_data);
} else {
	$tpl->setCurrentBlock('HIDDEN-FIELDS_FIELD');
	$tpl->setVariable('FIELD-NAME','meta_description');
	$tpl->setVariable('FIELD-VALUE',$meta_description);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','meta_keywords');
	$tpl->setVariable('FIELD-VALUE',$meta_keywords);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','meta_author');
	$tpl->setVariable('FIELD-VALUE',$meta_author);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','meta_redirect_url');
	$tpl->setVariable('FIELD-VALUE',$meta_redirect_url);
	$tpl->parseCurrentBlock();
  if($meta_redirect != ''){
		$tpl->setVariable('FIELD-NAME','meta_redirect');
		$tpl->setVariable('FIELD-VALUE','1');
  }
	$tpl->setVariable('FIELD-NAME','meta_robots');
	$tpl->setVariable('FIELD-VALUE',$meta_robots);
	$tpl->parseCurrentBlock();

}

// Rechtemanagement
if (!empty($idcatside) 
		&& ( $perm->have_perm(22, 'side', $idcatside, $idcat)  
			|| $perm->have_perm(31, 'side', $idcatside, $idcat) ) ) {
	
	$tpl->setCurrentBlock('USER_RIGHTS');
			
	//backendperms
	$tpl_data['BACKENDRIGHTS'] = '';
	if ($perm->have_perm(22, 'side', $idcatside, $idcat)) {
		$panel1 = $perm->get_right_panel('side', $idcatside, array( 'formname'=>'editform' ), $cms_lang['con_word_backendrights'].' '.$cms_lang['con_word_edit'], false, false, $idcat, 'backend_' );
		if (!empty($panel1)) {
			$tpl_data['BACKENDRIGHTS'] = implode("", $panel1);
		}
	}
	
	//frontendperms area_frontend
	$tpl_data['FRONTENDRIGHTS'] = '';
	if ($perm->have_perm(14, 'cat', $idcat)) {
		$panel2 = $perm->get_right_panel('frontendpage', $idcatside, array( 'formname'=>'editform' ), $cms_lang['con_word_frontendrights'].' '.$cms_lang['con_word_edit'], false, false, $idcat, 'frontend_' );
		if (!empty($panel2)) {
			$tpl_data['FRONTENDRIGHTS'] = implode("", $panel2);
		}
	} 
	
	$tpl_data['LANG_RIGHTS'] = '';
	$tpl->setVariable($tpl_data);
	$tpl->parseCurrentBlock();
	unset($tpl_data);
}

// Darf Templatekonfiguration betreten
$have_enter_tpl_perm = (is_numeric($idcatside)) ? $perm->have_perm(26, 'side', $idcatside, $idcat): $perm -> have_perm(26, 'cat', $idcat);
if ($have_enter_tpl_perm) {

	$tpl->setCurrentBlock('TPL-CONF');
	$tpl->setVariable('LNG_TEMPLATE',$cms_lang['con_template']);

	// Darf Templates konfigurieren?
	$have_config_tpl_perm = ( is_numeric($idcatside) ) ? $perm->have_perm(27, 'side', $idcatside, $idcat): $perm -> have_perm(27, 'cat', $idcat) ;

	// konfiguriertes Template und Layout suchen
	if ($idtplconf != '0' && !$idtpl && !$configtpl) {
		$sql = "SELECT B.idlay, B.idtpl
			FROM $cms_db[tpl_conf] A
                         LEFT JOIN $cms_db[tpl] B USING(idtpl)
			WHERE idtplconf='$idtplconf'";
		$db->query($sql);
		$db->next_record();
		$idlay = $db->f('idlay');
		$idtpl = $db->f('idtpl');
		$configtpl = $idtpl;
	} else {
		$sql = "SELECT idlay, idtpl FROM $cms_db[tpl] WHERE idtpl='$idtpl'";
		$db->query($sql);
		$db->next_record();
		$idlay = $db->f('idlay');
	}

	if ($have_config_tpl_perm) {
		$tpl->setCurrentBlock('TPL-CONF_SELECT_ENTRY');
		$tpl->setVariable('ENTRY-VALUE',0);
		$tpl->setVariable('ENTRY-SELECTED','selected="selected"');
		$tpl->setVariable('ENTRY-TITLE',$cms_lang['con_cattemplate']);
		$tpl->parseCurrentBlock();
	}

	// Templates Auflisten
	$sql = "SELECT idtpl, name FROM $cms_db[tpl] WHERE idclient='$client' ORDER BY name";
	$db->query($sql);


	while ($db->next_record()) {

		if ($db->f('idtpl') == $idtpl){
			if ($have_config_tpl_perm) {
				$tpl->setVariable('ENTRY-VALUE',$db->f('idtpl'));
				$tpl->setVariable('ENTRY-SELECTED','selected="selected"');
				$tpl->setVariable('ENTRY-TITLE',$db->f('name'));
				$tpl->parseCurrentBlock();
			} else {
				$currenttemplatename=$db->f('name');
			}
		}
		else if ($perm -> have_perm(1, 'tpl', $db->f('idtpl')) && $have_config_tpl_perm) {
			$tpl->setVariable('ENTRY-VALUE',$db->f('idtpl'));
			$tpl->setVariable('ENTRY-SELECTED','');
			$tpl->setVariable('ENTRY-TITLE',$db->f('name'));
			$tpl->parseCurrentBlock();
		}
	}


	if ($have_config_tpl_perm) {
		$tpl->setCurrentBlock('TPL-CONF');
		$tpl->setVariable('TPL-NAME','');
	} else if (!empty($currenttemplatename)) {
		$tpl->setCurrentBlock('TPL-CONF');
		$tpl->setVariable('TPL-NAME',$currenttemplatename);
	} else {
		$tpl->setCurrentBlock('TPL-CONF');
		$tpl->setVariable('TPL-NAME',$cms_lang['con_cattemplate']);
	}

	$tpl->setCurrentBlock('HIDDEN-FIELDS_FIELD');
	$tpl->setVariable('FIELD-NAME','configtpl');
	$tpl->setVariable('FIELD-VALUE',$configtpl);
	$tpl->parseCurrentBlock();

// Darf Templateauswahl nicht betreten
} else {

	$sql = "SELECT B.idtpl
		FROM $cms_db[tpl_conf] A
        LEFT JOIN $cms_db[tpl] B USING(idtpl)
		WHERE idtplconf='$idtplconf'";
	$db->query($sql);
	if ($db->next_record())
		$idtpl = $db->f('idtpl');
	else $idtpl = 0;
	
	if($idtplconf != '0')
		$configtpl = $idtpl;
      
	$tpl->setCurrentBlock('HIDDEN-FIELDS_FIELD');
	$tpl->setVariable('FIELD-NAME','configtpl');
	$tpl->setVariable('FIELD-VALUE',$configtpl);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','idtpl');
	$tpl->setVariable('FIELD-VALUE',$idtpl);
	$tpl->parseCurrentBlock();
}


ob_start();

// Template konfigurieren
if ($have_config_tpl_perm) {

	include_once ($cfg_cms['path_base'].$cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/tpl_mod_config.php');

	$list = browse_layout_for_containers($idlay);

	// Einstellungen suchen
	if ($configtpl == $idtpl) {
		$sql = "SELECT A.config, A.view, A.edit, B.container, C.name, C.input, C.idmod, C.version, C.verbose, C.cat, C.source_id, C.idmod
			FROM $cms_db[container_conf] A
			LEFT JOIN $cms_db[container] B USING(idcontainer)
			LEFT JOIN $cms_db[mod] C USING(idmod)
			WHERE A.idtplconf='$idtplconf'";
	} else {
		$sql = "SELECT A.config, A.view, A.edit, B.container, C.name, C.input, C.idmod, C.version, C.verbose, C.cat, C.source_id, C.idmod
			FROM $cms_db[container_conf] A
			LEFT JOIN $cms_db[container] B USING(idcontainer)
			LEFT JOIN $cms_db[mod] C USING(idmod)
			WHERE A.idtplconf='0' AND B.idtpl='$idtpl'";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$container[$db->f('container')] = array ( $db->f('config'),      // value 0
		                                          $db->f('view'),        // value 1
		                                          $db->f('edit'),        // value 2
		                                          htmlentities($db->f('name'), ENT_COMPAT, 'UTF-8'),        // value 3
		                                          $db->f('input'),       // value 4
		                                          htmlentities($db->f('idmod'), ENT_COMPAT, 'UTF-8'),       // value 5
		                                          htmlentities($db->f('version'), ENT_COMPAT, 'UTF-8'),     // value 6 
                                              htmlentities($db->f('verbose'), ENT_COMPAT, 'UTF-8'),     // value 7
                                              htmlentities($db->f('cat'), ENT_COMPAT, 'UTF-8'),         // value 8
                                              htmlentities($db->f('source_id'), ENT_COMPAT, 'UTF-8'),   // value 9
                                              htmlentities($db->f('idmod'), ENT_COMPAT, 'UTF-8'));      // value 10
	}
	if (is_array($container)) {
		ksort($container);
		foreach ($container as $key => $value) {

			$mod_content_start=$mod_tpl_start;
			$mod_content_start = str_replace('{MOD-SELECT}','',$mod_content_start);
			$mod_content_start = str_replace('{IDLAY}','<input type="hidden" name="idlay" value="'.$idlay.'"/>',$mod_content_start);
			$mod_content_start = str_replace('{ACTIVE-SELECT_TITLE-TRUE}',$cms_lang['gen_mod_active'],$mod_content_start);
			$mod_content_start = str_replace('{ACTIVE-SELECT_TITLE-FALSE}',$cms_lang['gen_mod_deactive'],$mod_content_start);
			$mod_content_start = str_replace('{EDIT-SELECT_TITLE-TRUE}',$cms_lang['gen_mod_edit_allow'] ,$mod_content_start);
			$mod_content_start = str_replace('{EDIT-SELECT_TITLE-FALSE}',$cms_lang['gen_mod_edit_disallow'] ,$mod_content_start);
			$mod_content_start = str_replace('{SKIN}',$cfg_cms['skin'],$mod_content_start);

			if (is_array($list['id'])) {
				if (in_array($key, $list['id'])) {
					$input = $value['4'];
					// Containername
					$modname = ( (($value['7'] != '') ? $value['7'] : $value['3']) . ((empty($value['6'])) ? '' : ' (' . $value['6'] . ')') );
#					$modtitel = ( ' ++ ' .$cms_lang['gen_description'] . ' ++ &#10;' . (($value['8'] != '') ? $cms_lang['gen_cat'] . ': ' . $value['8'] . ' &#10;' : '') .
#                                                    (($value['7'] != '') ? $cms_lang['gen_verbosename'] . ': ' . $value['7'] . ' &#10;' : '') .
#                                                    (empty($value['9']) ? $cms_lang['gen_name'] : $cms_lang['gen_original']) . ': ' . $value['3'] . ' &#10;' .
#                                                    (($value['6'] != '') ? $cms_lang['gen_version'] . ': ' . $value['6'] . ' &#10;' : '') . 'IdMod: ' . $value['10'] );
					$modcursor = 'pointer';

					$mod_content_start = str_replace('{LNG_MOD-INFO}',$cms_lang['gen_description'],$mod_content_start);

					$mod_content_start = str_replace('{MODCAT-ROW_DISABLED}',(($value['8']=='') ? 'dsplnone':''),$mod_content_start);
					$mod_content_start = str_replace('{LNG_MODCAT}',$cms_lang['gen_cat'],$mod_content_start);
					$mod_content_start = str_replace('{MODCAT}',$value['8'],$mod_content_start);

					$mod_content_start = str_replace('{MODORIG-ROW_DISABLED}',(($value['9']=='') ? 'dsplnone':''),$mod_content_start);
					$mod_content_start = str_replace('{LNG_MODORIG}',$cms_lang['gen_original'],$mod_content_start);
					$mod_content_start = str_replace('{MODORIG}',$value['3'],$mod_content_start);

					$mod_content_start = str_replace('{MODVERB-ROW_DISABLED}',(($value['7']=='') ? 'dsplnone':''),$mod_content_start);
					$mod_content_start = str_replace('{LNG_MODVERB}',$cms_lang['gen_verbosename'],$mod_content_start);
					$mod_content_start = str_replace('{MODVERB}',$value['7'],$mod_content_start);


					$mod_content_start = str_replace('{MODVERS-ROW_DISABLED}',(($value['6']=='') ? 'dsplnone':''),$mod_content_start);
					$mod_content_start = str_replace('{LNG_MODVERS}',$cms_lang['gen_version'],$mod_content_start);
					$mod_content_start = str_replace('{MODVERS}',$value['6'],$mod_content_start);

					$mod_content_start = str_replace('{IDMOD}',$value['10'],$mod_content_start);


					$mod_content_start = str_replace('{CONTAINER-TITLE}',(!empty($list[$key]['title']) ? $list[$key]['title']:"$key. ".$cms_lang['tpl_container']),$mod_content_start);
					$mod_content_start = str_replace('{MOD-KEY}',$key,$mod_content_start);

					$mod_content_start = str_replace('{VAL5}','<input type="hidden" name="c'.$key.'" value="'.$value['5'].'"/>',$mod_content_start);

					$mod_content_start = str_replace('{MOD_NOTACTIVE}',(($value['1'] == '-1')  ? 'style="display:none !important;"':''),$mod_content_start);

					$mod_content_start = str_replace('{ACTIVE-SELECT_SELECTED-TRUE}',(($value['1'] == '0' || !$value['1']) ? ' selected="selected"':''),$mod_content_start);
					$mod_content_start = str_replace('{ACTIVE-SELECT_SELECTED-FALSE}',(($value['1'] == '-1') ? ' selected="selected"':''),$mod_content_start);
					$mod_content_start = str_replace('{EDIT-SELECT_SELECTED-TRUE}',(($value['2'] == '0' || !$value['2']) ? ' selected="selected"':''),$mod_content_start);
					$mod_content_start = str_replace('{EDIT-SELECT_SELECTED-FALSE}',(($value['2'] == '-1') ? ' selected="selected"':''),$mod_content_start);

					$mod_content_start = str_replace('{MOD-CURSOR}',$modcursor,$mod_content_start);
					$mod_content_start = str_replace('{MOD-TITLE}',$modtitel,$mod_content_start);
					$mod_content_start = str_replace('{MOD-NAME}',$modname,$mod_content_start);
					
					echo $mod_content_start;

					// Developer-Modul
					if (strpos($value['6'], 'dev') != false && $value['6'] != '')
						$input = '<p class="errormsg">'.$cms_lang['tpl_devmessage']."</p>\n".$input;

					// Modulkonfiguration einlesen
					if ($cconfig) $tmp1 = preg_split("/&/", $cconfig[$key]);
					else $tmp1 = preg_split("/&/", $value['0']);
					$varstring = array();
					foreach ($tmp1 as $key1=>$value1) {
						$tmp2 = explode('=', $value1);
						foreach ($tmp2 as $key2=>$value2) $varstring["$tmp2[0]"]=$tmp2[1];
					}
					foreach ($varstring as $key3=>$value3) {
						$cms_mod['value'][$key3] = cms_stripslashes(urldecode($value3));
					}
					//TODO - remove dedi backward compatibility
					$dedi_mod = $cms_mod;
					
					foreach ($value as $key4=>$value4) $cms_mod['info'][$key4] = cms_stripslashes(urldecode($value4));
					$input = str_replace("MOD_VAR", "C".$key."MOD_VAR" , $input);
					eval(' ?>'.$input);
					$input = '';
					unset($cms_mod['value'], $dedi_mod['value'], $cms_mod['info'], $dedi_mod['info']);
					echo $mod_tpl_end;
				}
			}
		}
	}


}




// Outputbuffering wieder aufnehmen
$temp_tpl_conf = ob_get_contents();
ob_end_clean();

// buttons
if (empty($view)) {
	$tpl_data['FORM_ACTION'] = $sess->url("main.php?idside=$idside&idcat=$idcat&idsidelang=$idsidelang");
	$tpl_data['ABORT'] = $sess->url("main.php?area=con");
	$tpl_data['AREA_TITLE'] = $cms_lang['area_con_configside'];
} else {
	$tpl_data['FORM_ACTION'] = $sess->url("main.php?area=con_configside&idside=$idside&idcat=$idcat&idsidelang=$idsidelang&view=$view");
	$tpl_data['ABORT'] = $sess->url($cfg_client['path_http_edit'] . $cfg_client['path_rel'].$cfg_client['contentfile']."?lang=$lang&idcatside=$idcatside&idcat=$idcat&view=$view");
	$tpl_data['AREA_TITLE'] = '';
}


$tpl->setCurrentBlock('BUTTONS');
$tpl->setVariable('BUTTON-SAVE_TITLE',$cms_lang['gen_save_titletext']);
$tpl->setVariable('BUTTON-SAVE_VALUE',$cms_lang['gen_save']);
$tpl->setVariable('BUTTON-APPLY_TITLE',$cms_lang['gen_apply_titletext']);
$tpl->setVariable('BUTTON-APPLY_VALUE',$cms_lang['gen_apply']);
$tpl->setVariable('BUTTON-CANCEL_TITLE',$cms_lang['gen_cancel_titletext']);
$tpl->setVariable('BUTTON-CANCEL_VALUE',$cms_lang['gen_cancel']);
$tpl->setVariable('BUTTON-CANCEL_ONCLICK-LOCATION',$tpl_data['ABORT']);
$tpl->parseCurrentBlock();


$tpl_data['BUTTONS-BOTTOM'] = $tpl->get('BUTTONS');
if (! $have_config_tpl_perm && !$have_enter_tpl_perm && ! $have_meta_perm ) {
	$tpl_data['BUTTONS-BOTTOM'] = '';
}


$tpl->setCurrentBlock('__global__');
$tpl_data['IDTPLCONF'] = (empty($idtplconf) && $idtplconf!=0) ? '' : $idtplconf;
$tpl_data['LASTMODIFIED'] = $lastmodified;
$tpl_data['AUTHOR'] = $author;
$tpl_data['CREATED'] = $created;
$tpl_data['IDCATSIDE'] = empty($idcatside) ? '' : $idcatside;
$tpl_data['CON_SIDECONFIG'] = $cms_lang['con_sideconfig'];
$tpl_data['SIDE_TITLE_DESC'] = $cms_lang['con_title'];
$tpl_data['SIDE_TITLE'] = empty($title) ? '' : $title;
$tpl_data['SELECT_LOCK_SIDE'] = $select_lock_side;
$tpl_data['TPL-MOD_CONF'] =  empty($temp_tpl_conf) ? '' : $temp_tpl_conf;
$tpl->setVariable($tpl_data);
unset($tpl_data);
// Look for Errors
if (!empty($errno) || $sf_is_rewrite_error) {
	$tpl->setCurrentBlock('ERROR_BLOCK');
	if ($sf_is_rewrite_error) {
		$tpl_error['ERR_MSG'] =  $cms_lang['err_0101'];
	} else {
		$tpl_error['ERR_MSG'] = $cms_lang['err_' . $errno];
	}
	$tpl->setVariable($tpl_error);
	$tpl->parseCurrentBlock();
	unset($tpl_data);
}

include('inc/inc.footer.php');
?>
