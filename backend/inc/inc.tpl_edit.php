<?PHP
// File: $Id: inc.tpl_edit.php 642 2012-02-19 00:30:53Z bjoern $
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
// + Revision: $Revision: 642 $
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
 * 1. Ben�tigte Funktionen und Klassen includieren
 */

include('inc/fnc.tpl.php');
include('inc/fnc.mipforms.php');

/**
 * 2. Eventuelle Actions/ Funktionen abarbeiten
 */

//um den Bereich betreten zu d�rfen, mu� das Recht bearbeiten gesetzt sein
if(is_numeric($idtpl)) $perm->check(3, 'tpl', $idtpl);
else $perm->check(3, 'area_tpl', 0);

switch($action) {
	case 'save':  // Template bearbeiten
		$errno = tpl_save($idtpl, $idlay, $tplname, $description, $tpl_overwrite_all);
		tpl_autoset_starttpl((int) $client, (int) $idtpl);
		if (!$errno && ! isset($_REQUEST['sf_apply'])) {
			header ('HTTP/1.1 302 Moved Temporarily');
			header ("Location:".$sess->urlRaw("main.php?area=tpl"));
			exit;
			break;
		} 
	case 'change':  // Layout oder Modul wechseln
		$cconfig = tpl_change($idlay);
		remove_magic_quotes_gpc($tplname);
		remove_magic_quotes_gpc($description);
		$tplname = htmlentities($tplname, ENT_COMPAT, 'UTF-8');
		$description = htmlentities($description, ENT_COMPAT, 'UTF-8');
		break;
}

/**
 * 3. Eventuelle Dateien zur Darstellung includieren
 */

include('inc/inc.header.php');

/**
 * 4. Bildschirmausgabe aufbereiten und ausgeben
 */
 

$tpl->loadTemplatefile('tpl_edit.tpl', false);
$tpl->setVariable('SKIN',$cfg_cms['skin']);	
 

// Templateeinstellungen suchen
if ($idtpl && (!$idlay || $idlay == '0')) {
	// Template suchen
	$sql = "SELECT A.name, A.description, B.idlay
		FROM ". $cms_db['tpl'] ." A
		LEFT JOIN ". $cms_db['lay'] ." B using(idlay)
		WHERE A.idtpl='$idtpl'";
	$db->query($sql);
	$db->next_record();
	$tplname = htmlentities($db->f('name'), ENT_COMPAT, 'UTF-8');
	$description = htmlentities($db->f('description'), ENT_COMPAT, 'UTF-8');

	// Template zur�cksetzen
	if ($idlay < 1) {
		$idlay = $db->f('idlay');
		$list = browse_layout_for_containers($idlay);
		if (is_array($list)) {
			foreach ($list['id'] as $value) unset(${'c'.$value});
		}
	} else $idlay = $db->f('idlay');

	// Containereinstellungen suchen
	$sql = "SELECT A.container, A.idmod, B.config, B.view, B.edit, C.input 
                FROM $cms_db[container] A 
                LEFT JOIN $cms_db[container_conf] B USING(idcontainer) 
                LEFT JOIN $cms_db[mod] C ON A.idmod=C.idmod 
                WHERE A.idtpl='$idtpl' AND B.idtplconf='0'";
	$db->query($sql);
	while ($db->next_record()) {
		${'c'.$db->f('container')} = $db->f('idmod');
		${'cview'.$db->f('container')} = $db->f('view');
		${'cedit'.$db->f('container')} = $db->f('edit');
		${'cconfig'.$db->f('container')} = $db->f('config');
	}

// Templateeinstellungen �bernehmen
} else {
	if (is_array($cconfig)) {
		foreach ($cconfig as $key => $value) {
			if ($changed != $key) ${'cconfig'.$key} = $value;
		}
	}
}

// Layoutbeschreibung raussuchen
$sql = "SELECT idlay, name, description 
        FROM $cms_db[lay] 
        WHERE idclient='$client' 
        ORDER BY name";
$db->query($sql);
while ($db->next_record()) {
	$lay['id'][] = $db->f('idlay');
	$lay[$db->f('idlay')]['name'] = $db->f('name');
	$lay[$db->f('idlay')]['description'] = $db->f('description');
}

// Modulliste erstellen
if ($idlay) {
	$sql = "SELECT idmod, cat, name, input, config, version, verbose, cat, source_id, idmod 
                FROM $cms_db[mod]
                WHERE idclient='$client'
                ORDER BY cat, name";
	$db->query($sql);
	while ($db->next_record()) {
		$mod['id'][] = $db->f('idmod');
		if ($db->f('cat') != '') $mod[$db->f('idmod')]['cat'] = $db->f('cat'). ':';
		$mod[$db->f('idmod')]['name'] = htmlentities($db->f('name'), ENT_COMPAT, 'UTF-8');
		$mod[$db->f('idmod')]['input'] = $db->f('input');
		$mod[$db->f('idmod')]['config'] = $db->f('config');
		$mod[$db->f('idmod')]['version'] = htmlentities($db->f('version'), ENT_COMPAT, 'UTF-8');
		$mod[$db->f('idmod')]['verbose'] = htmlentities($db->f('verbose'), ENT_COMPAT, 'UTF-8');
        $mod[$db->f('idmod')]['cat'] = htmlentities($db->f('cat'), ENT_COMPAT, 'UTF-8');
        $mod[$db->f('idmod')]['source_id'] = htmlentities($db->f('source_id'), ENT_COMPAT, 'UTF-8');
        $mod[$db->f('idmod')]['idmod'] = htmlentities($db->f('idmod'), ENT_COMPAT, 'UTF-8');
	}
}

// Template dublizieren
if($action == 'duplicate') {
	$idtpl_for_form = '';
	$tplname = $cms_lang['tpl_copy_of'].$tplname;
	unset($idtpl);
} else $idtpl_for_form = $idtpl;

// Formular zur Templatebearbeitung
#echo "    <form name=\"editform\" method=\"post\" action=\"".$sess->url("main.php?area=tpl_edit&idtpl=$idtpl_for_form#edit_container")."\">\n";

// rechte management
if (!empty($idtpl) && $action != 'duplicate' && $perm->have_perm(6, 'tpl', $idtpl)) {
	$panel = $perm->get_right_panel('tpl', $idtpl, array( 'formname'=>'editform' ), 'text');
	if (!empty($panel)) {	
		$tpl->setCurrentBlock('USER_RIGHTS');
		$tpl->setVariable('RIGHTS',implode("",$panel));
		$tpl->parseCurrentBlock();
	}
}

//description
$tpl->setCurrentBlock('DESCRIPTION');
$tpl_data['LNG_NOTICES'] = $cms_lang['tpl_description'];
$tpl_data['NOTICES'] = empty($description) ? '' : htmlentities($description, ENT_COMPAT, 'UTF-8');
$tpl->setVariable($tpl_data);
$tpl->parseCurrentBlock();
unset($tpl_data);

//layout
$tpl->setCurrentBlock('TPL-CONF');
$tpl->setVariable('LNG_LAYOUT',$cms_lang['tpl_layout']);
$tpl->setCurrentBlock('TPL-CONF_SELECT_ENTRY');
if (!$idlay) 
	$idlay = '0';
if ($idlay && $idtpl) {
		$tpl->setVariable('ENTRY-VALUE','0');
		$tpl->setVariable('ENTRY-SELECTED','');
		$tpl->setVariable('ENTRY-TITLE',$cms_lang['gen_reset']);
		$tpl->parseCurrentBlock();
} elseif ($idlay == '0') {
		$tpl->setVariable('ENTRY-VALUE','0');
		$tpl->setVariable('ENTRY-SELECTED','selected="selected"');
		$tpl->setVariable('ENTRY-TITLE',$cms_lang['form_nothing']);
		$tpl->parseCurrentBlock();
} else {
		$tpl->setVariable('ENTRY-VALUE','0');
		$tpl->setVariable('ENTRY-SELECTED','');
		$tpl->setVariable('ENTRY-TITLE',$cms_lang['form_nothing']);
		$tpl->parseCurrentBlock();
}

if (is_array($lay['id'])) {
	foreach ($lay['id'] as $value) {
		if ($value == $idlay) {
			$tpl->setVariable('ENTRY-VALUE',$value);
			$tpl->setVariable('ENTRY-SELECTED','selected="selected"');
			$tpl->setVariable('ENTRY-TITLE',$lay[$value]['name']);
			$tpl->parseCurrentBlock();
		} else {
			$tpl->setVariable('ENTRY-VALUE',$value);
			$tpl->setVariable('ENTRY-SELECTED','');
			$tpl->setVariable('ENTRY-TITLE',$lay[$value]['name']);
			$tpl->parseCurrentBlock();
		}
	}
}

if (!empty($lay[$idlay]['description'])) {
	$tpl->setCurrentBlock('TPL-CONF_DESCRIPTION');
	$tpl_data['LNG_LAYOUT-DESCRIPTION'] = $cms_lang['tpl_description'];
	$tpl_data['LAYOUT-DESCRIPTION'] = htmlentities($lay[$idlay]['description']);
	$tpl->setVariable($tpl_data);
	$tpl->parseCurrentBlock();
	unset($tpl_data);
}



// wenn Layout gew�hlt
if ($idlay) {


	$list = browse_layout_for_containers($idlay);
	
	$tpl->setCurrentBlock('HIDDEN-FIELDS_FIELD');
	$tpl->setVariable('FIELD-NAME','changed');
	$tpl->setVariable('FIELD-VALUE','0');
	$tpl->parseCurrentBlock();
	$tpl->setVariable('FIELD-NAME','container');
	$tpl->setVariable('FIELD-VALUE',implode(',', $list['id']));
	$tpl->parseCurrentBlock();

	$temp_output_for_backend = ob_get_contents();
	ob_end_clean();
	ob_start();

	// Container auflisten

	if (is_array($list)) {

		include_once ($cfg_cms['path_base'].$cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/tpl_mod_config.php');


		natsort ($list['id']);
		foreach ($list['id'] as $value) {

			if (${'c'.$value})
				$mod_content_start = $mod_tpl_start;  
			else
				$mod_content_start = $mod_tpl_start_empty;  

			$mod_select=$mod_tpl_selectmod;
			$mod_select_entry=$mod_tpl_selectmod_entry;
			$mod_select_entries='';

			if (${'c'.$value} < 1) {
				$mod_select_entry = str_replace('{ENTRY-VALUE}','0',$mod_select_entry);
				$mod_select_entry = str_replace('{ENTRY-SELECTED}','selected="selected"',$mod_select_entry);
				$mod_select_entry = str_replace('{ENTRY-TITLE}',$cms_lang['form_nothing'],$mod_select_entry);
			} else {
				$mod_select_entry = str_replace('{ENTRY-VALUE}','0',$mod_select_entry);
				$mod_select_entry = str_replace('{ENTRY-SELECTED}','',$mod_select_entry);
				$mod_select_entry = str_replace('{ENTRY-TITLE}',$cms_lang['form_nothing'],$mod_select_entry);
			}

			$mod_select_entries =$mod_select_entry;	
			$mod_select_entry=$mod_tpl_selectmod_entry;				

			if (is_array($mod['id'])) {
				$_cat_arr = array();

				//build option select
				$first_run = true;

				foreach ($mod['id'] as $idmod) {
					
					//check perm
					if (!$perm->have_perm(15, 'mod', 0) && (strpos($mod[$idmod]['version'], 'dev') != false &&
						   $mod[$idmod]['version'] != '') && $idmod != ${'c'.$value})
						continue;
			

					//optgroup
					if (!$_cat_arr[$mod[$idmod]['cat']] && $mod[$idmod]['cat'] != '') {
						if (! $first_run) 
							$mod_select_entry = "</optgroup>".$mod_select_entry;
							$_cat_arr[$mod[$idmod]['cat']] = true;
							$mod_select_entry = '<optgroup label="'.$mod[$idmod]['cat'].'">'.$mod_select_entry;
							$first_run = false;
					}
		
					$mod[$idmod]['referer'] = (($mod[$idmod]['verbose'] != '') ?
																			$mod[$idmod]['verbose'] : $mod[$idmod]['name']);
					
					$mod[$idmod]['referer'] .= empty($mod[$idmod]['version']) ? '' :' (' . $mod[$idmod]['version'] . ')';
					
					// selected
					if ($idmod == ${'c'.$value}) {
						$mod_select_entry = str_replace('{ENTRY-VALUE}',$idmod,$mod_select_entry);
						$mod_select_entry = str_replace('{ENTRY-SELECTED}','selected="selected"',$mod_select_entry);
						$mod_select_entry = str_replace('{ENTRY-TITLE}',$mod[$idmod]['referer'],$mod_select_entry);
					} else {
						$mod_select_entry = str_replace('{ENTRY-VALUE}',$idmod,$mod_select_entry);
						$mod_select_entry = str_replace('{ENTRY-SELECTED}','',$mod_select_entry);
						$mod_select_entry = str_replace('{ENTRY-TITLE}',$mod[$idmod]['referer'],$mod_select_entry);
					}

					$mod_select_entries .=$mod_select_entry;	
					$mod_select_entry=$mod_tpl_selectmod_entry;
				}
			
				$mod_select_entries =$mod_select_entries.'</optgroup>';
			}

			$mod_select=str_replace('{SELECT-ENTRIES}',$mod_select_entries,$mod_select);
			$mod_content_start = str_replace('{MOD-SELECT}',$mod_select,$mod_content_start);
	
			$mod_content_start = str_replace('{IDLAY}','',$mod_content_start);
			$mod_content_start = str_replace('{VAL5}','',$mod_content_start);

			$mod_content_start = str_replace('{ACTIVE-SELECT_TITLE-TRUE}',$cms_lang['gen_mod_active'],$mod_content_start);
			$mod_content_start = str_replace('{ACTIVE-SELECT_TITLE-FALSE}',$cms_lang['gen_mod_deactive'],$mod_content_start);
			$mod_content_start = str_replace('{EDIT-SELECT_TITLE-TRUE}',$cms_lang['gen_mod_edit_allow'] ,$mod_content_start);
			$mod_content_start = str_replace('{EDIT-SELECT_TITLE-FALSE}',$cms_lang['gen_mod_edit_disallow'] ,$mod_content_start);
			$mod_content_start = str_replace('{SKIN}',$cfg_cms['skin'],$mod_content_start);
	
			$mod_content_start=str_replace('{LNG_MOD-INFO}',$cms_lang['gen_description'],$mod_content_start);
	
			$mod_content_start=str_replace('{MODCAT-ROW_DISABLED}',(($mod[${'c'.$value}]['cat']=='') ? 'dsplnone':''),$mod_content_start);
			$mod_content_start=str_replace('{LNG_MODCAT}',$cms_lang['gen_cat'],$mod_content_start);
			$mod_content_start=str_replace('{MODCAT}',$mod[${'c'.$value}]['cat'],$mod_content_start);
	
			$mod_content_start=str_replace('{MODORIG-ROW_DISABLED}',(($mod[${'c'.$value}]['name']=='') ? 'dsplnone':''),$mod_content_start);
			$mod_content_start=str_replace('{LNG_MODORIG}',$cms_lang['gen_original'],$mod_content_start);
			$mod_content_start=str_replace('{MODORIG}',$mod[${'c'.$value}]['name'],$mod_content_start);
	
			$mod_content_start=str_replace('{MODVERB-ROW_DISABLED}',(($mod[${'c'.$value}]['verbose']=='') ? 'dsplnone':''),$mod_content_start);
			$mod_content_start=str_replace('{LNG_MODVERB}',$cms_lang['gen_verbosename'],$mod_content_start);
			$mod_content_start=str_replace('{MODVERB}',$mod[${'c'.$value}]['verbose'],$mod_content_start);
	
			$mod_content_start=str_replace('{MODVERS-ROW_DISABLED}',(($mod[${'c'.$value}]['version']=='') ? 'dsplnone':''),$mod_content_start);
			$mod_content_start=str_replace('{LNG_MODVERS}',$cms_lang['gen_version'],$mod_content_start);
			$mod_content_start=str_replace('{MODVERS}',$mod[${'c'.$value}]['version'],$mod_content_start);
	
			$mod_content_start=str_replace('{IDMOD}',$mod[${'c'.$value}]['idmod'],$mod_content_start);
			
			$mod_content_start=str_replace('{CONTAINER-NAME}',((!empty($list[$value]['title'])) ? $list[$value]['title']:"$value. ".$cms_lang['tpl_container']),$mod_content_start);
			$mod_content_start=str_replace('{CONTAINER-TITLE}',((!empty($list[$value]['title'])) ? $list[$value]['title']:"$value. ".$cms_lang['tpl_container']),$mod_content_start);
			$mod_content_start=str_replace('{MOD-KEY}',$value,$mod_content_start);
	
			$mod_content_start=str_replace('{MOD_NOTACTIVE}',((${'cview'.$value} == '-1')  ? 'style="display:none !important;"':''),$mod_content_start);
	
			$mod_content_start=str_replace('{ACTIVE-SELECT_SELECTED-TRUE}',((${'cview'.$value} == '0' || !${'cview'.$value}) ? ' selected':''),$mod_content_start);
			$mod_content_start=str_replace('{ACTIVE-SELECT_SELECTED-FALSE}',((${'cview'.$value} == '-1') ? ' selected="selected"':''),$mod_content_start);
			$mod_content_start=str_replace('{EDIT-SELECT_SELECTED-TRUE}',((${'cedit'.$value} == '0' || !${'cedit'.$value}) ? ' selected="selected"':''),$mod_content_start);
			$mod_content_start=str_replace('{EDIT-SELECT_SELECTED-FALSE}',((${'cedit'.$value} == '-1') ? ' selected="selected"':''),$mod_content_start);
	
			$mod_content_start=str_replace('{MOD-CURSOR}',$modcursor,$mod_content_start);
			$mod_content_start=str_replace('{MOD-TITLE}',$modtitel,$mod_content_start);
			$mod_content_start=str_replace('{MOD-NAME}',$mod[${'c'.$value}]['referer'],$mod_content_start);

			$_container_name = !empty($list[$value]['title']) ? $list[$value]['title']:"$value. ".$cms_lang['tpl_container']."";
			if ($anchor == $_container_name.'_'.$value)
				echo '<a name="edit_container"></a>'.$mod_content_start;
			else 
				echo $mod_content_start;

			if (${'c'.$value}) {

				// Modulkonfiguration einlesen
				$input = $mod[${'c'.$value}]['input'];
	
				// Developer-Modul
				if (strpos($mod[${'c'.$value}]['version'], 'dev') != false && $mod[${'c'.$value}]['version'] != '') {
					$input = '<p align="center" class="errormsg">'.$cms_lang['tpl_devmessage']."</p>\n".$input;
				}
	
				$mod_tpl_conf = ${'cconfig'.$value};
				$mod_id = ${'c'.$value};
				$mod_conf = ( empty($mod_tpl_conf) ) ? $mod[$mod_id]['config']:$mod_tpl_conf;
				$tmp1 = preg_split("/&/", $mod_conf);
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
				if (is_array($mod[${'c'.$value}])) {
					foreach ($mod[${'c'.$value}] as $key4=>$value4) {
						$cms_mod['info'][$key4] = cms_stripslashes(urldecode($value4));
					}
				}
				
				$input = str_replace("MOD_VAR", "C".$value."MOD_VAR" , $input);
				
				eval(' ?>'.$input);
				unset($cms_mod['value'], $dedi_mod['value'], $cms_mod['info'], $dedi_mod['info']);
			
			}

			echo $mod_tpl_end;

		}

	} 
	
	// Outputbuffering wieder aufnehmen
	$temp_tpl_conf = ob_get_contents();
	ob_end_clean();
	ob_start();
	echo $temp_output_for_backend;
	unset($temp_output_for_backend);	
	
	
}

$tpl_data['FORM_ACTION'] = $sess->url("main.php?area=tpl_edit&idtpl=$idtpl_for_form#edit_container");
$tpl_data['ABORT'] = $sess->url('main.php?area=tpl');
$tpl_data['AREA_TITLE'] = $cms_lang['area_tpl_edit'];

$tpl->setCurrentBlock('BUTTONS_OPTION-ADVANCED');
$tpl->setVariable('LNG_OVERWRITE-ALL',$cms_lang['tpl_overwrite_all']);
$tpl->setVariable('LNG_ADVANCED',$cms_lang['tpl_advanced']);
$tpl->parseCurrentBlock();

$tpl->setCurrentBlock('BUTTONS');
$tpl->setVariable('BUTTON-SAVE_TITLE',$cms_lang['gen_save_titletext']);
$tpl->setVariable('BUTTON-SAVE_VALUE',$cms_lang['gen_save']);
$tpl->setVariable('BUTTON-APPLY_TITLE',$cms_lang['gen_apply_titletext']);
$tpl->setVariable('BUTTON-APPLY_VALUE',$cms_lang['gen_apply']);
$tpl->setVariable('BUTTON-CANCEL_TITLE',$cms_lang['gen_cancel_titletext']);
$tpl->setVariable('BUTTON-CANCEL_VALUE',$cms_lang['gen_cancel']);
$tpl->setVariable('BUTTON-CANCEL_ONCLICK-LOCATION',$tpl_data['ABORT']);
$tpl->parseCurrentBlock();
$tpl->setCurrentBlock('BUTTONS');


$tpl->setCurrentBlock('__global__');

$tpl_data['LNG_TPL-EDIT'] = $cms_lang['tpl_config'];
$tpl_data['LNG_TPL-NAME'] = $cms_lang['tpl_templatename'];
$tpl_data['TPL-NAME'] = empty($tplname) ? '' : $tplname;
$tpl_data['TPL-MOD_CONF'] = empty($temp_tpl_conf) ? '' : $temp_tpl_conf;

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
