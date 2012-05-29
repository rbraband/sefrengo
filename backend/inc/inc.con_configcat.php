<?PHP
// File: $Id: inc.con_configcat.php 600 2011-12-19 01:13:48Z bjoern $
// +----------------------------------------------------------------------+
// | Version: Sefrengo $Name:	 $																					
// +----------------------------------------------------------------------+
// | Copyright (c) 2005 - 2007 sefrengo.org <info@sefrengo.org>						|
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License									|
// |																																			|
// | This program is subject to the GPL license, that is bundled with			|
// | this package in the file LICENSE.TXT.																|
// | If you did not receive a copy of the GNU General Public License			|
// | along with this program write to the Free Software Foundation, Inc., |
// | 59 Temple Place, Suite 330, Boston, MA	 02111-1307 USA								|
// |																																			|
// | This program is distributed in the hope that it will be useful,			|
// | but WITHOUT ANY WARRANTY; without even the implied warranty of				|
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the				|
// | GNU General Public License for more details.													|
// |																																			|
// +----------------------------------------------------------------------+
// + Autor: $Author: bjoern $
// +----------------------------------------------------------------------+
// + Revision: $Revision: 600 $
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
include_once($cfg_cms['path_base'].$cfg_cms['path_backend_rel'].'inc/fnc.mod_rewrite.php');

/**
 * 2. Eventuelle Actions/ Funktionen abarbeiten
 */

// $idcat vorhanden, prüfen, ob Recht zum bearbeiten gegeben ist
if(is_numeric($idcat))$perm->check(3, 'cat', $idcat);

// Keine idcat, prüfen, ob recht auf neue Seite anlegen vorhanden ist
// jb ... 23.04.04 ... parentid des parent für rechte prüfung als id übergeben
//										 ermöglicht die prüfung ob in einer kategorie durch den user 
//										 weitere kategorien angelegt werden dürfen.
//										 Rechtegruppe auf Untergruppe cat von area_con gesetzt.
else $perm->check(2, 'cat', $parent);


// rewrite check
$sf_is_rewrite_error = false;
if ($action == 'save') {
	$have_rewrite_perm = (is_numeric($idcat)) ? $perm->have_perm(15, 'cat', $idcat) : $perm -> have_perm(15, 'area_con', 0);
	if ($cfg_client['url_rewrite'] == '2' && $have_rewrite_perm) {
		 if($_REQUEST['rewrite_use_automatic'] != '1') {
			if (! rewriteUrlIsAllowed($_REQUEST['rewrite_alias'])) {
				$sf_is_rewrite_error = true;
				$sf_rewrite_error_message = $cms_lang['err_rw_01'];
				$action = 'change';
			} else if (! rewriteUrlIsUnique('idcat', $idcat, $_REQUEST['rewrite_alias'])) {
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



// Ordnerkonfiguration speichern
switch($action) {
	case 'save':	// Template bearbeiten
		$use_redirect = isset($_REQUEST['sf_apply']) ? false: true;
		//echo '$idcat, $idcatside, $idtpl, $view, $idtplconf, $description, $name, $rewrite_use_automatic, $rewrite_alias, $parent, $area, $idlay, $use_redirect<br />';
		//echo "$idcat, $idcatside, $idtpl, $view, $idtplconf, $description, $name, $rewrite_use_automatic, $rewrite_alias, $parent, $area, $idlay, $use_redirect";exit;

		$idcat = con_config_folder_save($idcat, $idcatside, $idtpl, $view, $idtplconf, $description, $name, $rewrite_use_automatic, $rewrite_alias, $parent, $area, $idlay, $use_redirect, false);
		if ( isset($_REQUEST['sf_apply']) ) {
			$sql = "SELECT idtplconf FROM " . $cms_db['cat_lang'] ." WHERE idcat = $idcat AND idlang=$lang";
			$db->query($sql);
			$db->next_record();
			$idtplconf = $db->f('idtplconf');
		}
		
		break;
	case 'change':	// Layout oder Modul wechseln
		$cconfig = tpl_change($idlay);
		break;
}




// getrennte Header für Backend und Frontendbearbeitung
if (empty($view)) {
	include('inc/inc.header.php');
} else {
	include_once ($cfg_cms['path_base'].$cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/frontend_config_header.php');
	$frontend_header=str_replace('{SF-VERSION}',$cfg_cms['version'],$frontend_header);
	$frontend_header=str_replace('{SKIN}',$cfg_cms['skin'],$frontend_header);
	echo $frontend_header;
}

$tpl->loadTemplatefile('cat_config.tpl', false);
$tpl->setVariable('SKIN',$cfg_cms['skin']);	



// Ordnername
if (( !$action && $idcat) || isset($_REQUEST['sf_apply']) && ! $sf_is_rewrite_error) {

	$sql = "SELECT name, description, rewrite_use_automatic, rewrite_alias FROM ".$cms_db['cat_lang']." WHERE idcat='$idcat' AND idlang='$lang'";
	$db->query($sql);
	$db->next_record();
	$name = $db->f('name');
	$description = $db->f('description');
	$rewrite_use_automatic = $db->f('rewrite_use_automatic');
	$rewrite_alias = $db->f('rewrite_alias');
} else {
	remove_magic_quotes_gpc($name);
	remove_magic_quotes_gpc($description);
	if (! $idcat && ! $action) {
		// new page
		$rewrite_use_automatic = 1;
	} else {
		//on change
		remove_magic_quotes_gpc($rewrite_use_automatic);
	}
	remove_magic_quotes_gpc($rewrite_alias);
}



// URL REWRITE
$have_rewrite_perm = (is_numeric($idcat)) ? $perm->have_perm(15, 'cat', $idcat) : $perm -> have_perm(15, 'area_con', 0);
if ($cfg_client['url_rewrite'] == '2' && $have_rewrite_perm) {

	$tpl->setCurrentBlock('URL_REWRITE');

	$tpl_data['REWRITE_USE_AUTOMATIC_CHECKED'] = ($rewrite_use_automatic == 1) ? 'checked="checked" ':'';
	$tpl_data['REWRITE_URL_DISABLED'] = ($rewrite_use_automatic == 1) ? 'disabled="disabled" ':'';

	$tpl_data['LNG_REWRITE_PAGE-URL'] = $cms_lang['con_cat_page_url'];
	$tpl_data['LNG_REWRITE_URL-OF-THIS-PAGE'] = $cms_lang['con_cat_urlofthiscat'];
	$tpl_data['LNG_REWRITE_AUTO-URL'] = $cms_lang['con_cat_rwpath_autourl'];

	$tpl_data['REWRITE_ALIAS'] = htmlentities($rewrite_alias, ENT_COMPAT, 'UTF-8');
	$tpl_data['REWRITE_ERROR'] = $rewrite_error = ($sf_is_rewrite_error) ? '<p class="errormsg">'.$sf_rewrite_error_message.'</p>':'';

	$tpl_data['REWRITE_CURRENT_URL'] = ($rewrite_alias == '') ? rewriteGetPath($idcat, $lang, true). '<em>'.$cms_lang['con_cat_rwpath_thiscat'].'</em>/': rewriteGetPath($idcat, $lang, true);
	$tpl_data['REWRITE_CURRENT_URL'] = 'http://<em>{domain.xyz}</em>/'. $tpl_data['REWRITE_CURRENT_URL'];

	$tpl->setVariable($tpl_data);

	$tpl->parseCurrentBlock();
	unset($tpl_data);

	$tpl->setCurrentBlock('HIDDEN-FIELDS_FIELD');
	$tpl->setVariable('FIELD-NAME','form_is_submitted');
	$tpl->setVariable('FIELD-VALUE','true');
	$tpl->parseCurrentBlock();

} else {

	$tpl->setCurrentBlock('HIDDEN-FIELDS_FIELD');
	$tpl->setVariable('FIELD-NAME','rewrite_use_automatic');
	$tpl->setVariable('FIELD-VALUE',$rewrite_use_automatic);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','rewrite_alias');
	$tpl->setVariable('FIELD-VALUE',htmlentities($rewrite_alias, ENT_COMPAT, 'UTF-8'));
	$tpl->parseCurrentBlock();

}



// Rechtemanagement
if (!empty($idcat) && ($perm->have_perm(6, 'cat', $idcat) || $perm->have_perm(14, 'cat', $idcat) ) ) {

	
	$tpl->setCurrentBlock('USER_RIGHTS');

	$tpl_data['BACKENDRIGHTS'] = '';
	if ($perm->have_perm(6, 'cat', $idcat)) {
		$panel1 = $perm->get_right_panel('cat', $idcat, array( 'formname'=>'editform' ), $cms_lang['con_word_backendrights'].' '.$cms_lang['con_word_edit'], false, false, 0, 'backend_' );
		$panel1['spaces'] =	 "&nbsp;&nbsp;&nbsp;&nbsp;\n";
		if (!empty($panel1)) {
			$tpl_data['BACKENDRIGHTS'] = implode("", $panel1);
		}
	}
	
	$tpl_data['FRONTENDRIGHTS'] = '';	
	if ($perm->have_perm(14, 'cat', $idcat)) {
		$panel2 = $perm->get_right_panel('frontendcat', $idcat, array( 'formname'=>'editform' ),  $cms_lang['con_word_frontendrights'].' '.$cms_lang['con_word_edit'], false, false, 0,	'frontend_');
		if (!empty($panel2)) {
			$tpl_data['FRONTENDRIGHTS'] = implode("", $panel2);
		}
	}

	$tpl_data['LANG_RIGHTS'] = '';
	$tpl->setVariable($tpl_data);
	$tpl->parseCurrentBlock();
	unset($tpl_data);
}


// Ordnerbeschreibung
	$tpl->setCurrentBlock('NOTICE');
	$tpl_data['LANG_NOTICES'] = $cms_lang['con_configcat_description'];
	$tpl_data['NOTICES'] = empty($description) ? '' : htmlentities($description, ENT_COMPAT, 'UTF-8');
	$tpl->setVariable($tpl_data);
	$tpl->parseCurrentBlock();


// Darf Templatekonfiguration betreten
$have_config_tpl_perm = (is_numeric($idcat)) ? $perm->have_perm(11, 'cat', $idcat) : $perm -> have_perm(11, 'area_con', 0);


	// konfiguriertes Template und Layout suchen
	if ($idtplconf != '0' && !$idtpl && !$configtpl) {
		$sql = "SELECT 
					B.idlay, B.idtpl 
				FROM 
					".$cms_db['tpl_conf']." A 
					LEFT JOIN ".$cms_db['tpl']." B USING(idtpl) 
				WHERE 
					idtplconf='$idtplconf'";
		$db->query($sql);
		$db->next_record();
		$idlay = $db->f('idlay');
		$idtpl = $db->f('idtpl');
		$configtpl = $idtpl;
	} else if ($idtpl){
		// template was changed or reloaded - get idtpl from $_REQUEST
		$sql = "SELECT idlay, idtpl FROM ".$cms_db['tpl']." WHERE idtpl='$idtpl'";
		$db->query($sql);
		$db->next_record();
		$idlay = $db->f('idlay');
	} else {
		// new config get starttemplate to fetch idlay
		$sql = "SELECT 
							idlay, idtpl
						FROM 
							".$cms_db['tpl']." T
						WHERE 
							is_start = 1 
							AND idclient='$client'";
		$db->query($sql);
		$db->next_record();
		$idlay = $db->f('idlay');
		$idtpl = $db->f('idtpl');
		
	}

if ($have_config_tpl_perm) {

	$tpl->setCurrentBlock('TPL-CONF');
	$tpl->setVariable('LNG_TEMPLATE',$cms_lang['con_template']);
	
	// Templates Auflisten
	$sql = "SELECT idtpl, name, is_start FROM $cms_db[tpl] WHERE idclient='$client' ORDER BY name";
	$tpl->setCurrentBlock('TPL-CONF_SELECT_ENTRY');
	$db->query($sql);
	if ($db->affected_rows() == 0) {
		$tpl->setVariable('ENTRY-VALUE',0);
		$tpl->setVariable('ENTRY-SELECTED','selected="selected"');
		$tpl->setVariable('ENTRY-TITLE',$cms_lang['form_nothing']);
		$tpl->parseCurrentBlock();
	}
	while ($db->next_record()) {
		if ($db->f('idtpl') == $idtpl) {
			$tpl->setVariable('ENTRY-VALUE',$db->f('idtpl'));
			$tpl->setVariable('ENTRY-SELECTED','selected="selected"');
			$tpl->setVariable('ENTRY-TITLE',$db->f('name'));
			$tpl->parseCurrentBlock();
		} else if ($perm -> have_perm(1, 'tpl', $db->f('idtpl'))) {
			$tpl->setVariable('ENTRY-VALUE',$db->f('idtpl'));
			$tpl->setVariable('ENTRY-SELECTED','');
			$tpl->setVariable('ENTRY-TITLE',$db->f('name'));
			$tpl->parseCurrentBlock();
		}
	}
	
	$tpl->setCurrentBlock('HIDDEN-FIELDS_FIELD');
	$tpl->setVariable('FIELD-NAME','configtpl');
	$tpl->setVariable('FIELD-VALUE',$configtpl);
	$tpl->parseCurrentBlock();

} else {

	$tpl->setCurrentBlock('HIDDEN-FIELDS_FIELD');
	$tpl->setVariable('FIELD-NAME','configtpl');
	$tpl->setVariable('FIELD-VALUE',$configtpl);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','idlay');
	$tpl->setVariable('FIELD-VALUE',$idlay);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','idtplconf');
	$tpl->setVariable('FIELD-VALUE',$idtplconf);
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','idtpl');
	$tpl->setVariable('FIELD-VALUE',$idtpl);
	$tpl->parseCurrentBlock();

}

if ($have_config_tpl_perm && $idtpl) {
	
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
		$container[$db->f('container')] = array ( $db->f('config'),			 // value 0
																							$db->f('view'),				 // value 1
																							$db->f('edit'),				 // value 2
																							htmlentities($db->f('name'), ENT_COMPAT, 'UTF-8'),				// value 3
																							$db->f('input'),			 // value 4
																							htmlentities($db->f('idmod'), ENT_COMPAT, 'UTF-8'),				// value 5
																							htmlentities($db->f('version'), ENT_COMPAT, 'UTF-8'),			// value 6 
																							htmlentities($db->f('verbose'), ENT_COMPAT, 'UTF-8'),			// value 7
																							htmlentities($db->f('cat'), ENT_COMPAT, 'UTF-8'),					// value 8
																							htmlentities($db->f('source_id'), ENT_COMPAT, 'UTF-8'),		// value 9
																							htmlentities($db->f('idmod'), ENT_COMPAT, 'UTF-8'));			// value 10
	}
	$temp_tpl_conf = '';
	if (is_array($container)) {
		ksort($container);
		$temp_tpl_conf = '';
		ob_start();

		
		foreach ($container as $key => $value)
		{

			//if ($key == '410') continue;


			$mod_content_start= $mod_tpl_start;
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
#																										(($value['7'] != '') ? $cms_lang['gen_verbosename'] . ': ' . $value['7'] . ' &#10;' : '') .
#																										(empty($value['9']) ? $cms_lang['gen_name'] : $cms_lang['gen_original']) . ': ' . $value['3'] . ' &#10;' .
#																										(($value['6'] != '') ? $cms_lang['gen_version'] . ': ' . $value['6'] . ' &#10;' : '') . 'IdMod: ' . $value['10'] );

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

					$mod_content_start = str_replace('{ACTIVE-SELECT_SELECTED-TRUE}',(($value['1'] == '0' || !$value['1']) ? ' selected':''),$mod_content_start);
					$mod_content_start = str_replace('{ACTIVE-SELECT_SELECTED-FALSE}',(($value['1'] == '-1') ? ' selected':''),$mod_content_start);
					$mod_content_start = str_replace('{EDIT-SELECT_SELECTED-TRUE}',(($value['2'] == '0' || !$value['2']) ? ' selected':''),$mod_content_start);
					$mod_content_start = str_replace('{EDIT-SELECT_SELECTED-FALSE}',(($value['2'] == '-1') ? ' selected':''),$mod_content_start);

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
					
					foreach ($value as $key4=>$value4) $cms_mod['info'][$key4] = cms_stripslashes(urldecode($value4));
			
					$input = str_replace("MOD_VAR", "C".$key."MOD_VAR" , $input);
//echo $input;
					eval('?>'.$input);
					$input = '';
					
					unset($cms_mod['value'], $dedi_mod['value'], $cms_mod['info'], $dedi_mod['info']);
					echo $mod_tpl_end;
				}
			}

		}
		$temp_tpl_conf = ob_get_contents();
		ob_end_clean();
		//echo $temp_tpl_conf; exit;
	}
} 

//buttons		 
if (empty($view)) {
	$tpl_data['FORM_ACTION'] = $sess->url('main.php?area=con_configcat');
	$tpl_data['ABORT'] = $sess->url("main.php?area=con");
	$tpl_data['AREA_TITLE'] = $cms_lang['area_con_configcat'];
} else {
	$tpl_data['FORM_ACTION'] = $sess->url('main.php?area=con_configcat&view='.$view);
	$tpl_data['ABORT'] = $sess->url($cfg_client['path_http_edit'] . $cfg_client['path_rel'].$cfg_client['contentfile']."?lang=$lang&idcat=$idcat&idcatside=$idcatside&view=$view");
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
if (! $have_config_tpl_perm) {
	$tpl_data['BUTTONS-BOTTOM'] = '';
}

$tpl->setCurrentBlock('__global__');
$tpl_data['IDTPLCONF'] = (empty($idtplconf) && $idtplconf!=0) ? '' : $idtplconf;
$tpl_data['IDCAT'] = empty($idcat) ? '' : $idcat;
$tpl_data['PARENT'] = empty($parent) ? '' : $parent;
$tpl_data['CON_CATCONFIG'] = $cms_lang['con_catconfig'];
$tpl_data['TPL_NAME'] = $cms_lang['tpl_templatename'];
$tpl_data['CAT_TITLE'] = empty($name) ? '' : htmlentities($name, ENT_COMPAT, 'UTF-8');
$tpl_data['SELECT_LOCK_SIDE'] = $select_lock_side;
$tpl_data['TPL-MOD_CONF'] = empty($temp_tpl_conf) ? '' : $temp_tpl_conf;
$tpl_data['LNG_CAT-TITLE'] = $cms_lang['con_configcat_folder'];
$tpl->setVariable($tpl_data);
unset($tpl_data);
// Look for Errors
if (!empty($errno) || $sf_is_rewrite_error) {
	$tpl->setCurrentBlock('ERROR_BLOCK');
	if ($sf_is_rewrite_error) {
		$tpl_error['ERR_MSG'] =	 $cms_lang['err_0101'];
	} else {
		$tpl_error['ERR_MSG'] = $cms_lang['err_' . $errno];
	}
	$tpl->setVariable($tpl_error);
	$tpl->parseCurrentBlock();
	unset($tpl_data);
}

include('inc/inc.footer.php');
?>
