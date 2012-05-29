<?PHP
// File: $Id: inc.con.php 600 2011-12-19 01:13:48Z bjoern $
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

/******************************************************************************
 1. Ben�tigte Funktionen und Klassen includieren
******************************************************************************/

include('inc/fnc.con.php');
/**
 * Class to check if a doublet/ clone Page of an idcatside exists
 *
 *  (c) 2004 by Bj�rn Brockmann
 */
class cmsPageDoubletAudit{

	var $idsideStore = array();

	/**
	 * Construtor, generates data for idatsidecheking
	 */
	function  cmsPageDoubletAudit(){
		global $db, $cms_db, $lang;

		$sql ="SELECT
					CS.idcatside, SL.idside
					FROM ". $cms_db['cat_side'] . " CS
					LEFT JOIN " . $cms_db['side_lang']. " SL USING(idside)
					WHERE SL.idlang = $lang
					ORDER BY CS.idside";
		$db->query($sql);

		while($db->next_record()){
			if(! is_array($this->idsideStore[$db->f('idside')]))
				$this->idsideStore[$db->f('idside')] = array();

			array_push($this->idsideStore[$db->f('idside')], $db->f('idcatside'));
		}
		//print_r($this->idsideStore);
	}

	/**
	 * Checks if a doublet/ clone of one idcatside exists
	 *
	 * @parms int idside
	 * @return bool true/ false
	 */
	function pageDoubletExists($idside){

		if(! is_int($idside))
			return false;

		return (count($this->idsideStore[$idside]) > 1);
	}
}

$sefrengoPDA = new cmsPageDoubletAudit;

// CatInfo-Objekt erstellen
$SF_catinfos = sf_factoryGetObjectCache('PAGE', 'Catinfos');
$SF_catinfos->setIdlang($lang);
$SF_catinfos->setCheckBackendperms(TRUE);
$SF_catinfos->generate();

// PageInfo-Objekt erstellen
$SF_pageinfos = sf_factoryGetObjectCache('PAGE', 'Pageinfos');
$SF_pageinfos->setIdlang($lang);
$SF_pageinfos->setCheckBackendperms(TRUE);
$SF_pageinfos->generate();

/******************************************************************************
 2. Eventuelle Actions/ Funktionen abarbeiten
******************************************************************************/

if ($change_show_tree == 'delete_cache') {
	 $action = 'delete_cache';
} else if ($change_show_tree == 'regenerate_rewrite_urls') {
	$action = 'regenerate_rewrite_urls';
}

if ($action && $view && $perm->have_perm(3, 'area_frontend', 3) ) {
	switch($action) {
		case 'side_delete':  // Seite löschen
			con_delete_side ($idcat, $idside);
			$cms_log->info('user', 'con_side_delete', array('idcat' => $idcat, 'catname' => $SF_catinfos->getTitle($idcat), 'idside' => $idside, 'pagename' => $SF_pageinfos->getTitleByIdside($idside)));
			fire_event('con_side_delete', array('idcat' => $idcat, 'idside' => $idside));
			header ('HTTP/1.1 302 Moved Temporarily');
			header ('Location:'.$sess->urlRaw($cfg_client['path_http_edit'] . $cfg_client['path_rel'].$cfg_client['contentfile'].'?lang='.$lang.'&idcat='.$idcat.'&view='.$view));
			exit;
			break;
		case 'cat_delete':  // Ordner löschen
			// Event
			$errno = con_delete_cat ($idcat);
			if(empty($errno)) {
				$cms_log->info('user', 'con_cat_delete',  array('idcat' => $idcat, 'catname' => $SF_catinfos->getTitle($idcat)));
			}
			fire_event('con_cat_delete', array('idcat' => $idcat,'errno' => $errno));
			header ('HTTP/1.1 302 Moved Temporarily');
			header ('Location:'.$sess->urlRaw($cfg_client['path_http_edit'] . $cfg_client['path_rel'].$cfg_client['contentfile'].'?lang='.$lang.'&view='.$view));
			exit;
			break;
		case 'side_visible':  // Seite online/offline schalten
			// change JB
			$bitmask = (($online & 0x02) == 0x02) ? 0xFC: 0xFC;
			$bit_to_clear = (($online & 0x01) == 0x01 || ($online & 0x05) == 0x05);
			$change = ($bit_to_clear) ? ($online & $bitmask): (($online & $bitmask) | 0x01);
			con_visible_side ($idside, $lang, $change);
			if ($bit_to_clear) {
				$cms_log->info('user', 'con_side_offline', array('idside' => $idside, 'pagename' => $SF_pageinfos->getTitleByIdside($idside)));
				fire_event('con_side_offline', array('idside' => $idside));
			} else {
				$cms_log->info('user', 'con_side_online', array('idside' => $idside, 'pagename' => $SF_pageinfos->getTitleByIdside($idside)));
				fire_event('con_side_online', array('idside' => $idside));
			}
			header ('HTTP/1.1 302 Moved Temporarily');
			header ('Location:'.$sess->urlRaw($cfg_client['path_http_edit'] . $cfg_client['path_rel'].$cfg_client['contentfile'].'?lang='.$lang.'&idcatside='.$idcatside.'&view='.$view));
			exit;
			break;

	}
}
$perm->check('area_con');
switch($action) {
	case 'side_start':  // Seite als Startseite festlegen
		con_make_start ($idcatside, !$is_start);
		$cms_log->info('user', 'con_side_start', array('idcatside' => $idcatside, 'pagename' => $SF_pageinfos->getTitle($idcatside)));
		fire_event('con_side_start', array('idcatside' => $idcatside));
		break;
	case 'side_delete':  // Seite löschen
		$pagename = $SF_pageinfos->getTitleByIdside($idside);
		con_delete_side ($idcat, $idside);
		$cms_log->info('user', 'con_side_delete', array('idcat' => $idcat, 'catname' => $SF_catinfos->getTitle($idcat), 'idside' => $idside, 'pagename' => $pagename));
		fire_event('con_side_delete', array('idcat' => $idcat, 'idside' => $idside));
		break;
	case 'side_visible':  // Seite online/offline schalten
		// change JB
		$bitmask = 0xFC;
		$bit_to_clear = (($online & 0x01) == 0x01 || ($online & 0x05) == 0x05);
		$change = ($bit_to_clear) ? ($online & $bitmask): (($online & $bitmask) | 0x01);
		con_visible_side ($idside, $lang, $change);
		if ($bit_to_clear) {
			$cms_log->info('user', 'con_side_offline', array('idside' => $idside, 'pagename' => $SF_pageinfos->getTitleByIdside($idside)));
			fire_event('con_side_offline', array('idside' => $idside));
		} else {
			$cms_log->info('user', 'con_side_online', array('idside' => $idside, 'pagename' => $SF_pageinfos->getTitleByIdside($idside)));
			fire_event('con_side_online', array('idside' => $idside));
		}
		break;
	case 'delete_cache':  // Cache löschen
		con_delete_cache($lang);
		$cms_log->info('user', 'con_cache_delete');
		fire_event('con_cache_delete', array());
		break;
	case 'regenerate_rewrite_urls':
		con_delete_cache($lang);
		include_once($cfg_cms['path_base'].$cfg_cms['path_backend_rel'].'inc/fnc.mod_rewrite.php');
		rewriteAutoForAll($lang);
		break;
	case 'cat_delete':  // Ordner l�schen
		// Event
		$errno = con_delete_cat ($idcat);
		$catname = $SF_catinfos->getTitle($idcat);
		if(empty($errno)) {
			$cms_log->info('user', 'con_cat_delete',  array('idcat' => $idcat, 'catname' => $catname));
		}
		fire_event('con_cat_delete', array('idcat' => $idcat,'errno' => $errno));
		break;
	case 'cat_visible':  // Ordner online schalten
		//test if offline
		$bit_to_clear = (($visible & 0x01) == 0x01);
		//make offline or online
		$change = ($bit_to_clear) ? ($visible & 0xFE): ($visible | 0x01);
		con_visible_cat ($idcat, $lang, $change);
		if ($bit_to_clear) {
			$cms_log->info('user', 'con_cat_offline', array('idcat' => $idcat, 'catname' => $SF_catinfos->getTitle($idcat)));
			fire_event('con_cat_offline', array('idcat' => $idcat));
		} else {
			$cms_log->info('user', 'con_cat_online', array('idcat' => $idcat, 'catname' => $SF_catinfos->getTitle($idcat)));
			fire_event('con_cat_online', array('idcat' => $idcat));
		}
		break;
	case 'cat_lock':  // Ordner sperren
		con_lock ('cat', $idcat, $lock);
		if ($lock == '1') {
			$cms_log->info('user', 'con_cat_lock', array('idcat' => $idcat, 'catname' => $SF_catinfos->getTitle($idcat)));
			fire_event('con_cat_lock', array('idcat' => $idcat));
		} else {
			$cms_log->info('user', 'con_cat_unlock', array('idcat' => $idcat, 'catname' => $SF_catinfos->getTitle($idcat)));
			fire_event('con_cat_unlock', array('idcat' => $idcat));
		}
		break;
	case 'side_lock':  // Seite sperren
//		con_lock ('side', $idcatside, $lock);
		con_lock ('side', $idside, $lock);
		if ($lock == '1') {
			$cms_log->info('user', 'con_side_lock', array('idside' => $idside, 'pagename' => $SF_pageinfos->getTitleByIdside($idside)));
			fire_event('con_side_lock', array('idside' => $idside));
		} else {
			$cms_log->info('user', 'con_side_unlock', array('idside' => $idside, 'pagename' => $SF_pageinfos->getTitleByIdside($idside)));
			fire_event('con_side_unlock', array('idside' => $idside));
		}
		break;
	case 'expand':  // Ordner aufklappen
		con_expand ($idcat, $expanded);
		break;
	case 'sideup': // Seite eins nach oben schieben
		con_move_side ('up',$idcat,$idside,$sortindex);
		break;
	case 'sidedown': // Seite eins nach unten schieben
		con_move_side ('down',$idcat,$idside,$sortindex);
		break;
	case 'sidetop': // Seite ganz nach oben schieben
		con_move_top_bottom ('top',$idcat,$idcatside,$sortindex);
		break;
	case 'sidebottom': // Seite ganz nach unten schieben
		con_move_top_bottom ('bottom',$idcat,$idcatside,$sortindex);
		break;
	case 'quicksort': // schnelles Umsortieren
		con_quick_sort ($quicksort,$idcat);
		break;
	case 'movecat': // verschieben des Ordners
		con_move_cat ($idcat, $target, $client);
		break;
	case 'catupdown': // umsortieren der Ordner
		con_sort_cat ($dir,$idcat,$sortindex,$parent,$client);
		break;
}

/******************************************************************************
 3. Eventuelle Dateien zur Darstellung includieren
******************************************************************************/

include('inc/inc.header.php');


/******************************************************************************
 4. Bildschirmausgabe aufbereiten und ausgeben
******************************************************************************/
// Aufbau des Arrays $con_tree
// jb_todo: sperren category
$sql = "SELECT
         B.idcat, B.parent, B.sortindex, C.idcatlang, C.author, C.created, C.lastmodified,
		 C.visible, C.idtplconf, C.name, E.name AS tplname, F.idcat AS expanded
		FROM "       . $cms_db['cat']        . " B
		 LEFT JOIN " . $cms_db['cat_lang']   . " C USING(idcat)
		 LEFT JOIN " . $cms_db['tpl_conf']   . " D USING(idtplconf)
		 LEFT JOIN " . $cms_db['tpl']        . " E USING(idtpl)
		 LEFT JOIN " . $cms_db['cat_expand'] . " F ON B.idcat = F.idcat AND F.idusers = '" . $auth->auth['uid'] . "'
		WHERE B.idclient = $client
		 AND  C.idlang   = $lang
		ORDER BY B.parent, B.sortindex";
$db->query($sql);
while($db->next_record()) {
	$idcat_loop = $db->f('idcat');

	$con_tree[$idcat_loop]['link']         = $sess->url($cfg_client['path_http_edit'] . $cfg_client['path_rel'].$cfg_client['contentfile']."?lang=$lang&idcat=".$idcat_loop."&view=preview");
	$con_tree[$idcat_loop]['idcat']        = $idcat_loop;
	$con_tree[$idcat_loop]['idcatlang']    = $db->f('idcatlang');
	$con_tree[$idcat_loop]['author']       = $db->f('author');
	$con_tree[$idcat_loop]['created']      = $db->f('created');
	$con_tree[$idcat_loop]['lastmodified'] = $db->f('lastmodified');
	$con_tree[$idcat_loop]['parent']       = $db->f('parent');
	$con_tree[$idcat_loop]['visible']      = $db->f('visible');
	$con_tree[$idcat_loop]['idtplconf']    = $db->f('idtplconf');
	$con_tree[$idcat_loop]['name']         = htmlentities($db->f('name'), ENT_COMPAT, 'UTF-8');
	$con_tree[$idcat_loop]['tplname']      = $db->f('tplname');
	$con_tree[$idcat_loop]['sortindex']    = $db->f('sortindex');
	$con_tree[$idcat_loop]['offline']      = (($db->f('visible') & 0x04) == 0x04);  // change JB: ($db->f('perm') == '0');

	$tlo_tree['expanded'][$idcat_loop]               = $db->f('expanded');
	$tlo_tree[$db->f('parent')][$db->f('sortindex')] = $idcat_loop;
}

// neuen Hauptordner anlegen
if ($change_show_tree == 'new_folder') {
	header ('HTTP/1.1 302 Moved Temporarily');
	header ('Location: '.$sess->urlRaw('main.php?area=con_configcat&parent=0&idtplconf=0'));
	exit;
} else if ($change_show_tree == 'publish') {
	$action = 'all_publish';
	unset($change_show_tree);
}

// Ordner, der angezeigt werden soll, bestimmen
if (!isset($show_tree)) {
	$sess->register('show_tree');
	$show_tree = '0';
}
if (isset($change_show_tree))                  $show_tree = $change_show_tree;
//if (!$perm->have_perm(1, 'area_con', 0)) $show_tree = '0';
if (!$con_tree[$show_tree])                    $show_tree = '0';

// Ordner sortieren
if ($show_tree == '0') {
	tree_level_order('0', 'catlist');
} else {
	if ($perm -> have_perm(1, 'cat', $show_tree)) {
		$catlist[] = $show_tree;
	}
	if ($tlo_tree['expanded'][$show_tree] == $show_tree || !$perm -> have_perm(1, 'cat', $show_tree)) {
		tree_level_order($show_tree, 'catlist', 'false', '1');
	}
}

$tpl->loadTemplatefile('con_main.tpl');

$tpl_data['AREA'] = $cms_lang['area_con'];
$tpl_data['SKIN'] = $cfg_cms['skin'];		
$tpl_data['FOOTER_LICENSE'] = $cms_lang['login_licence'];

if(! empty($errno)){
	$tpl->setCurrentBlock('ERRORMESSAGE');
	$tpl_error['ERRORMESSAGE'] = $cms_lang['err_' . $errno];
	$tpl->setVariable($tpl_error);
	$tpl->parseCurrentBlock();
}

//Generate selectbox ACTIONS...
$tpl -> setCurrentBlock('SELECT_ACTIONS');

$show_action_select = false;
// neuen Ordner erstellen
if ($perm->have_perm(2, 'area_con', $show_tree)){
	$tpl_folderlist['ACTIONS_VALUE'] = 'new_folder';
	$tpl_folderlist['ACTIONS_ENTRY'] = $cms_lang['con_folder_new'];
	$tpl_folderlist['ACTIONS_SELECTED'] = '';
	$tpl->setVariable($tpl_folderlist);
	$tpl->parseCurrentBlock();
	unset($tpl_folderlist);
	$show_action_select = true;
}




// Cache l�schen
if ( $perm ->is_admin() ){
	$tpl_folderlist['ACTIONS_VALUE'] = 'delete_cache';
	$tpl_folderlist['ACTIONS_ENTRY'] = $cms_lang['con_delete_cache'];
	$tpl_folderlist['ACTIONS_SELECTED'] = '';
	$tpl->setVariable($tpl_folderlist);
	$tpl->parseCurrentBlock();
	unset($tpl_folderlist);
	$show_action_select = true;
}

// regenerate rewrite urls
if ($cfg_client['url_rewrite'] == '2'){
	$tpl_folderlist['ACTIONS_VALUE'] = 'regenerate_rewrite_urls';
	$tpl_folderlist['ACTIONS_ENTRY'] = $cms_lang['con_regenerate_rewrite_urls'];
	$tpl_folderlist['ACTIONS_SELECTED'] = '';
	$tpl->setVariable($tpl_folderlist);
	$tpl->parseCurrentBlock();
	unset($tpl_folderlist);
	$show_action_select = true;
}

if($show_action_select){
	$tpl -> setCurrentBlock('FORM_SELECT_ACTIONS');
	$entry['FORM_URL_ACTIONS'] = $sess->url('main.php');
	$entry['LANG_SELECT_ACTIONS'] = $cms_lang['gen_select_actions'];
	$tpl->setVariable($entry);
	$tpl->parseCurrentBlock();
	unset($entry, $show_action_select);
}



//Generate Selectbox VIEW...
$tpl -> setCurrentBlock('SELECT_FOLDERLIST');

// alle Ordner anzeigen
$tpl_folderlist['FOLDERLIST_VALUE'] = '0';
$tpl_folderlist['FOLDERLIST_ENTRY'] = $cms_lang['con_folder_view'];
$tpl_folderlist['FOLDERLIST_SELECTED'] = '';
$tpl->setVariable($tpl_folderlist);
$tpl->parseCurrentBlock();
unset($tpl_folderlist);

// Leerzeile
$tpl_folderlist['FOLDERLIST_VALUE'] = '';
$tpl_folderlist['FOLDERLIST_ENTRY'] = '---------------------------------------';
$tpl_folderlist['FOLDERLIST_SELECTED'] = '';
$tpl->setVariable($tpl_folderlist);
$tpl->parseCurrentBlock();
unset($tpl_folderlist);

// Ordner auflisten
tree_level_order('0', 'folderlist', 'true');
if (is_array($folderlist)) {
	foreach ($folderlist as $a) {
		$spaces = '';
		if (! $perm -> have_perm(1, 'cat', $a)) continue;

		for ($i=0; $i<$folderlist_level[$a]; $i++) $spaces = $spaces.'&nbsp;&nbsp;';
		$tpl_folderlist['FOLDERLIST_VALUE'] = $a;
		$tpl_folderlist['FOLDERLIST_ENTRY'] = $spaces.'&nbsp;-&nbsp;'.$con_tree[$a]['name'];

		$catmove_list[$a]['level'] = $folderlist_level[$a];
		$catmove_list[$a]['name'] = $tpl_folderlist['FOLDERLIST_ENTRY'];
		$catmove_list[$a]['idcat'] = $a;


		if ($show_tree == $a) $tpl_folderlist['FOLDERLIST_SELECTED'] = 'selected';
		else $tpl_folderlist['FOLDERLIST_SELECTED'] = '';
		$tpl->setVariable($tpl_folderlist);
		$tpl->parseCurrentBlock();
	}
	unset($tpl_folderlist);
}

//Generate Selectbox VIEW...
$tpl -> setCurrentBlock('FORM_SELECT_VIEW');
$entry['LANG_SELECT_VIEW'] = $cms_lang['gen_select_view'];
$entry['FORM_URL_VIEW'] = $sess->url('main.php');
$tpl->setVariable($entry);
$tpl->parseCurrentBlock();
unset($entry);



//Generate Selectbox CHANGE TO...
//print_r( $perm->get_group() );
if($perm->have_perm(9, 'area_con',0) || $perm->have_perm(25, 'area_con',0)
   || $perm->is_any_perm_set('cat', 9, $perm->get_group(), $lang)
   || $perm->is_any_perm_set('cat', 25, $perm->get_group(), $lang)
   || $perm->is_any_perm_set('side', 25, $perm->get_group(), $lang)) {
	$tpl -> setCurrentBlock('SELECT_CHANGE_TO');
	// Webseite publizieren
	if ($sort){
		$tpl_folderlist['CHANGE_TO_VALUE'] = '';
		$tpl_folderlist['CHANGE_TO_ENTRY'] = $cms_lang['con_view_normal'];
		$tpl_folderlist['CHANGE_TO_SELECTED'] = '';
		$tpl->setVariable($tpl_folderlist);
		$tpl->parseCurrentBlock();
		unset($tpl_folderlist);
	} else{
		$tpl_folderlist['CHANGE_TO_VALUE'] = 'true';
		$tpl_folderlist['CHANGE_TO_ENTRY'] = $cms_lang['con_sort'];
		$tpl_folderlist['CHANGE_TO_SELECTED'] = '';
		$tpl->setVariable($tpl_folderlist);
		$tpl->parseCurrentBlock();
		unset($tpl_folderlist);
	}
	$tpl -> setCurrentBlock('FORM_CHANGE_TO');
	$entry['FORM_URL_CHANGE_TO'] = $sess->url('main.php');
	$entry['LANG_CHANGE_TO'] = $cms_lang['gen_select_change_to'];
	$tpl->setVariable($entry);
	$tpl->parseCurrentBlock();
	unset($entry);
}


// expand/collapse all folders
$tpl->setCurrentBlock('TREE-HEAD');								
$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
$tpl->setVariable('CAT',$show_tree);
$tpl->setVariable('LINK-TITLE-EXPAND',$cms_lang['con_allexpanded']);
$tpl->setVariable('LINK-TITLE-COLLAPSE',$cms_lang['con_nooneexpanded']);
$tpl->setVariable('LANG_ACTIONS',$cms_lang['con_action']);
$tpl->setVariable('LANG_STRUCTURE_AND_SIDE',$cms_lang['con_structureandsides']);
$tpl->parseCurrentBlock();

$tpl->setCurrentBlock('__global__');
$tpl->setVariable($tpl_data);
unset($tpl_data);

//check cats if startpage is https
$startpage_in_cat_is_https = array();
if ($cfg_client['https'] == '1')
{
	$sql = "SELECT DISTINCT
				D.idcat
			FROM
				".$cms_db['cat_side']." D LEFT JOIN
				".$cms_db['side_lang']." F USING(idside)
			WHERE 
				D.is_start = 1
				AND F.is_https = 1
				AND  F.idlang   = '".$lang."'
				";
	
	$db->query($sql);

	 while ($db->next_record()) 
	 {
	 	array_push($startpage_in_cat_is_https, $db->f('idcat'));
	 }
}


// Ordner
if (is_array($catlist)) {
	// Aufbau des Arrays $con_side
	$sql = "SELECT A.idcatside, A.idcat, A.sortindex, changed
         	FROM "       . $cms_db['cat_side']. " A
             LEFT JOIN " . $cms_db['cat']     . " B USING(idcat)
             LEFT JOIN " . $cms_db['code']    . " C ON A.idcatside = C.idcatside AND idlang = $lang
            WHERE B.idclient = $client
            ORDER BY idcatside";
	$db->query($sql);
	while($db->next_record()) {
		$con_side[$db->f('idcat')][$db->f('idcatside')]['idcatside'] = $db->f('idcatside');
		$con_side[$db->f('idcat')][$db->f('idcatside')]['idcat'] = $db->f('idcat');
		if ($db->f('changed') == '2' && $perm->have_perm('19', 'side', $db->f('idcatside'), $db->f('idcat'))) {
			$con_side[$db->f('idcat')][$db->f('idcatside')]['status'] = 'true';
			$con_tree[$db->f('idcat')]['status'] = 'true';
		}
		// Sortindex einf�gen
		if($db->f('sortindex') > 0) {
			if(!empty($sidelist[$db->f('idcat')][$db->f('sortindex')])) {
				array_push($sidelist[$db->f('idcat')], $db->f('idcatside'));
				$reindex[$db->f('idcat')] = 1;
			} else $sidelist[$db->f('idcat')][$db->f('sortindex')] = $db->f('idcatside');
		} elseif (is_array($sidelist[$db->f('idcat')])) {
			array_push($sidelist[$db->f('idcat')], $db->f('idcatside'));
			$reindex[$db->f('idcat')] = 1;
		} else {
			$sidelist[$db->f('idcat')] = array($db->f('idcatside'));
			$reindex[$db->f('idcat')] = 1;
		}
	}

//	// neue Seiten einsortieren
//	if($reindex) reindex_sort($sidelist);

	// Seiten publizieren
	if ($action == 'side_publish' && $perm->have_perm('23', 'side', $idcatside, $idcat)) con_publish($idcatside, 'side');
	if ($action == 'cat_publish'  && $perm->have_perm('7', 'cat', $idcat)) con_publish($idcat, 'cat');
	if ($action == 'all_publish'  && $perm->have_perm('7', 'area_con', 0) && $perm->have_perm('23', 'area_con', 0)) con_publish($show_tree);

	// Ordner anzeigen
	foreach ($catlist as $a) {

		//darf Ordner sehen?
		if(! $perm->have_perm(1, 'cat', $con_tree[$a]['idcat']) ) continue;

		$padding=0;
		for ($i='0'; $i<$catlist_level[$a]; $i++) $padding = $padding + 20;

		// Hauptordner parsen
		if ($catlist_level[$a] == '0' && $catlist['0'] != $a) {
			$tpl->parse('MAIN-FOLDER');
		}

		// Ordner auf-/zuklappen
		if ($con_tree[$a]['idcat'] == $idcat) {
			$tpl->touchBlock('FOLDER_EXPAND-ANCHOR');
		}
		$tpl_cat_values['FOLDER-PADDING-LEFT'] = $padding;

		// Link Expand �ffnen
		if ($tlo_tree['expanded'][$a] != '') {
			$tpl->setCurrentBlock('FOLDER_BUTTON-EXPAND_COLLAPSE');								
			$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
			$tpl->setVariable('LINK-HREF','main.php?area=con&action=expand&idcat='.$con_tree[$a]['idcat'].'&expanded=1');
			$tpl->setVariable('LINK-TITLE',$cms_lang['con_noexpanded']);
			$tpl->parseCurrentBlock();
		// Link Expand schliessen
		} else {
			$tpl->setCurrentBlock('FOLDER_BUTTON-EXPAND_EXPAND');								
			$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
			$tpl->setVariable('LINK-HREF','main.php?area=con&action=expand&idcat='.$con_tree[$a]['idcat'].'&expanded=0');
			$tpl->setVariable('LINK-TITLE',$cms_lang['con_expanded']);
			$tpl->parseCurrentBlock();
		}
			
		// Ordner konfigurieren Infotextpopup/ Link
		// konfiguriert
		if ($con_tree[$a]['tplname']) {
			$tpl->setCurrentBlock('FOLDER_BUTTON-CONFIG_INFO');								
			$tpl->setVariable('LNG_FOLDERINFO',$cms_lang['con_category_information']);		
			$tpl->setVariable('TOOLINFO-TITLE',$cms_lang['con_cat_config']);		
			$tpl->setVariable('LNG_IDCAT',$cms_lang['con_idcat']);		
			$tpl->setVariable('LNG_TPL',$cms_lang['con_template']);		
			$tpl->setVariable('IDCAT',$a);
			$tpl->setVariable('TPL-NAME',$con_tree[$a]['tplname']);

			$tpl->setVariable('LNG_EDITOR',$cms_lang['con_author']);
			$tpl->setVariable('EDITOR',$con_tree[$a]['author']);
			$tpl->parseCurrentBlock();
			
			// Konfigurationslink
			if($perm->have_perm(3, 'cat', $con_tree[$a]['idcat'])){
					$tpl->setCurrentBlock('FOLDER_BUTTON-CONFIG_ON');								
					$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
					$tpl->setVariable('LINK-HREF','main.php?area=con_configcat&idcat='.$con_tree[$a]['idcat'].'&idtplconf='.$con_tree[$a]['idtplconf']);
					$tpl->setVariable('LINK-TITLE',$cms_lang['con_cat_config']);
					$tpl->parseCurrentBlock();
			} else { 
					$tpl->setCurrentBlock('FOLDER_BUTTON-CONFIG_OFF');								
					$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
					$tpl->setVariable('LINK-TITLE',$cms_lang['con_cat_config']);
					$tpl->parseCurrentBlock();
			}

		// unkonfiguriert
		} else {
			$con_catinfo = "<strong>".$cms_lang['con_template'].":</strong><font color=#AF0F0F> ".$cms_lang['con_unconfigured']."</font>";
			$folder_popup = "'$con_catinfo','".$cms_lang['con_category_information']."', 'Id: $a', 'folderinfo'";

			// Konfigurationslink
			if($perm->have_perm(3, 'cat', $con_tree[$a]['idcat'])){
					$tpl->setCurrentBlock('FOLDER_BUTTON-CONFIG_NON');								
					$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
					$tpl->setVariable('LINK-HREF','main.php?area=con_configcat&idcat='.$con_tree[$a]['idcat'].'&idtplconf='.$con_tree[$a]['idtplconf']);
					$tpl->setVariable('LINK-TITLE',$cms_lang['con_cat_config']);
					$tpl->parseCurrentBlock();
			} else{
					$tpl->setCurrentBlock('FOLDER_BUTTON-CONFIG_NON-OFF');								
					$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
					$tpl->setVariable('LINK-TITLE',$cms_lang['con_cat_config']);
					$tpl->parseCurrentBlock();
			}
		}


		// Ordnername
		$tpl_cat_values['CAT_NAME'] = $con_tree[$a]['name'];

		// Ordner: Seiten ordnen
		if ($sort) {
			if($perm->have_perm(9, 'cat', $con_tree[$a]['idcat'])){

				$qs_url = "main.php?area=con&amp;sort=true&amp;action=quicksort&amp;idcat=$a";
				$mv_url = "main.php?area=con&amp;action=movecat&amp;idcat=$a";

				if(count($con_side[$a]) > 1) {
					$tpl->setCurrentBlock('FOLDER_ACTION-SELECT_HEADLINE-ENTRY-SORT');								
					$tpl->setVariable('LNG_ENTRY-HEADLINE',$cms_lang['con_quicksort']);
					$tpl->parseCurrentBlock();
					$tpl->setCurrentBlock('FOLDER_ACTION-SELECT_ENTRY-SORT');	
					$tpl->setVariable('FIELD-TITLE',$cms_lang['con_sidename_up']);
					$tpl->setVariable('FIELD-VALUE',$sess->urlRaw($qs_url.'amp;quicksort=title:ASC').'#catanchor');
					$tpl->parseCurrentBlock();
					$tpl->setVariable('FIELD-TITLE',$cms_lang['con_sidename_down']);
					$tpl->setVariable('FIELD-VALUE',$sess->urlRaw($qs_url.'&amp;quicksort=title:DESC').'#catanchor');
					$tpl->parseCurrentBlock();
					$tpl->setVariable('FIELD-TITLE',$cms_lang['con_created_up']);
					$tpl->setVariable('FIELD-VALUE',$sess->urlRaw($qs_url.'&amp;quicksort=created:ASC').'#catanchor');
					$tpl->parseCurrentBlock();
					$tpl->setVariable('FIELD-TITLE',$cms_lang['con_created_down']);
					$tpl->setVariable('FIELD-VALUE',$sess->urlRaw($qs_url.'&amp;quicksort=created:DESC').'#catanchor');
					$tpl->parseCurrentBlock();
					$tpl->setVariable('FIELD-TITLE',$cms_lang['con_changed_up']);
					$tpl->setVariable('FIELD-VALUE',$sess->urlRaw($qs_url.'&amp;quicksort=lastmodified:ASC').'#catanchor');
					$tpl->parseCurrentBlock();
					$tpl->setVariable('FIELD-TITLE',$cms_lang['con_changed_down']);
					$tpl->setVariable('FIELD-VALUE',$sess->urlRaw($qs_url.'&amp;quicksort=lastmodified:DESC').'#catanchor');
					$tpl->parseCurrentBlock();
				}

				$headline_out_ot=false;
				foreach ($catmove_list as $b)
				{
					$optspaces = '';
					if($b['idcat'] == $a) {
						$hideit = 1;
						$showit = $b['level'];
					} else if ($b['level'] <= $showit) {
						$hideit = 0; $showit=0;
				  }
					//	echo $b['name']. " reallevel: ".$b['level'] ."showit: $showit <br>";
					if($hideit != 1 && $perm->have_perm(9, 'cat', $b['idcat'])  && $b['idcat']!=$con_tree[$a]['parent']) {

						if ($headline_out_ot==false) {

							$headline_out_ot=true;

							$tpl->setCurrentBlock('FOLDER_ACTION-SELECT_HEADLINE-ENTRY-MOVE');								
							$tpl->setVariable('LNG_ENTRY-HEADLINE',$cms_lang['con_move_cat']);
							$tpl->parseCurrentBlock();
	
							$tpl->setCurrentBlock('FOLDER_ACTION-SELECT_ENTRY-MOVE');	
							if($catlist_level[$a] != '0' && $perm->have_perm(9, 'area_con', 0) ){
								$tpl->setVariable('FIELD-TITLE',$cms_lang['con_rootfolder']);
								$tpl->setVariable('FIELD-VALUE',$sess->urlRaw($mv_url."&amp;target=0"));
								$tpl->parseCurrentBlock();
							}						

						}

						$tpl->setVariable('FIELD-TITLE',$b['name']);
						$tpl->setVariable('FIELD-VALUE',$sess->urlRaw($mv_url.'&amp;target='.$b['idcat']). '#catanchor');
						$tpl->parseCurrentBlock();
					}
				}
				
				$hideit=0; $showit=0;
				// Ordner: nach ganz oben verschieben
				if ($con_tree[$a]['sortindex'] > 1) {
					$tpl->setCurrentBlock('FOLDER_BUTTON-SORT_TOP');								
					$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
					$tpl->setVariable('LINK-HREF','main.php?area=con&action=catupdown&dir=top&amp;sort=true&idcat='.$a.'&sortindex='.$con_tree[$a]['sortindex'].'&parent='.$con_tree[$a]['parent']);
					$tpl->setVariable('LINK-TITLE',$cms_lang['con_cattop']);
					$tpl->parseCurrentBlock();
				} else {
					$tpl->setCurrentBlock('FOLDER_BUTTON-SORT_TOP-OFF');								
					$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
					$tpl->parseCurrentBlock();
				}

				// Ordner: nach ganz unten verschieben
				if (end($tlo_tree[$con_tree[$a]['parent']]) != $a) {
					$tpl->setCurrentBlock('FOLDER_BUTTON-SORT_BOTTOM');								
					$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
					$tpl->setVariable('LINK-HREF','main.php?area=con&action=catupdown&dir=bottom&amp;sort=true&idcat='.$a.'&sortindex='.$con_tree[$a]['sortindex'].'&parent='.$con_tree[$a]['parent']);
					$tpl->setVariable('LINK-TITLE',$cms_lang['con_catbottom']);
					$tpl->parseCurrentBlock();
				} else {
					$tpl->setCurrentBlock('FOLDER_BUTTON-SORT_BOTTOM-OFF');								
					$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
					$tpl->parseCurrentBlock();
				}
				
				// Ordner: nach oben verschieben
				if ($con_tree[$a]['sortindex'] > 1)  {
					$tpl->setCurrentBlock('FOLDER_BUTTON-SORT_UP');								
					$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
					$tpl->setVariable('LINK-HREF','main.php?area=con&action=catupdown&dir=up&amp;sort=true&sortindex='.$con_tree[$a]['sortindex'].'&idcat='.$a.'&parent='.$con_tree[$a]['parent']);
					$tpl->setVariable('LINK-TITLE',$cms_lang['con_catup']);
					$tpl->parseCurrentBlock();
				} else {
					$tpl->setCurrentBlock('FOLDER_BUTTON-SORT_UP-OFF');								
					$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
					$tpl->parseCurrentBlock();
				}
				
				// Ordner: nach unten verschieben
				if (end($tlo_tree[$con_tree[$a]['parent']]) != $a)  {
					$tpl->setCurrentBlock('FOLDER_BUTTON-SORT_DOWN');								
					$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
					$tpl->setVariable('LINK-HREF','main.php?area=con&action=catupdown&dir=down&amp;sort=true&sortindex='.$con_tree[$a]['sortindex'].'&idcat='.$a.'&parent='.$con_tree[$a]['parent']);
					$tpl->setVariable('LINK-TITLE',$cms_lang['con_catdown']);
					$tpl->parseCurrentBlock();
				} else {
					$tpl->setCurrentBlock('FOLDER_BUTTON-SORT_DOWN-OFF');								
					$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
					$tpl->parseCurrentBlock();
				}
			}
		// Normale Ansicht
		} else{
			$cat_actions = '';

			// Ordner: neue Seite erstellen
			if ($con_tree[$a]['idtplconf'] != '0' && $perm -> have_perm(18, 'cat', $con_tree[$a]['idcat']) ) {
				$tpl->setCurrentBlock('FOLDER_BUTTON-NEWSIDE_ON');
				$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
				$tpl->setVariable('LINK-HREF','main.php?area=con_configside&idcat='.$con_tree[$a]['idcat'].'&idtplconf=0');
				$tpl->setVariable('LINK-TITLE', $cms_lang['con_actions']['20']);
				$tpl->parseCurrentBlock();
			} else {
				$tpl->setCurrentBlock('FOLDER_BUTTON-NEWSIDE_OFF');
				$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
				$tpl->parseCurrentBlock();
			}

			// Ordner: anlegen/ kopieren
			if ($perm -> have_perm(2, 'cat', $con_tree[$a]['idcat']) ){
				$tpl->setCurrentBlock('FOLDER_BUTTON-NEWCAT_ON');
				$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
				$tpl->setVariable('LINK-HREF','main.php?area=con_configcat&parent='.$con_tree[$a]['idcat'].'&idtplconf=0');
				$tpl->setVariable('LINK-TITLE', $cms_lang['con_folder_new']);
				$tpl->parseCurrentBlock();
				$tpl->setCurrentBlock('FOLDER_BUTTON-COPYCAT_ON');
				$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
				$tpl->setVariable('LINK-HREF','main.php?area=con_copycat&idcat='.$con_tree[$a]['idcat']);
				$tpl->setVariable('LINK-TITLE',  $cms_lang['con_folder_dupl']);
				$tpl->parseCurrentBlock();
			}	else {
				$tpl->setCurrentBlock('FOLDER_BUTTON-NEWCAT_OFF');
				$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
				$tpl->parseCurrentBlock();
				$tpl->setCurrentBlock('FOLDER_BUTTON-COPYCAT_OFF');
				$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
				$tpl->parseCurrentBlock();
			}

			// Ordner: online/offline/publish schalten
			if ($perm->have_perm( 7, 'cat', $con_tree[$a]['idcat'])) {
				$tmp_link = 'main.php?area=con&action=cat_visible&idcat='.$con_tree[$a]['idcat'].'&visible='.$con_tree[$a]['visible'];
				$tmp_link2 = 'main.php?area=con&action=cat_publish&idcat='.$a;
				$tmp_descr = $cms_lang['con_folder_visible'][$con_tree[$a]['visible']];
				$tmp_descr2 = $cms_lang['con_publish'];

				if (((int)$con_tree[$a]['visible'] & 0x03) == 0x00) {
					if ($cfg_client['publish'] == '1' && $con_tree[$a]['status'] == 'true') {
						$tmp_descr = $tmp_descr2;
						$tmp_link = $tmp_link2;
						$tpl->setCurrentBlock('FOLDER_BUTTON-PUBLISH_OFF-PUBLISH');
					} else  
						$tpl->setCurrentBlock('FOLDER_BUTTON-PUBLISH_OFFLINE');
				} else {
					if ($cfg_client['publish'] == '1' && $con_tree[$a]['status'] == 'true') {
						$tmp_descr = $tmp_descr2;
						$tmp_link = $tmp_link2;
						$tpl->setCurrentBlock('FOLDER_BUTTON-PUBLISH_ON-PUBLISH');
					} else 
						$tpl->setCurrentBlock('FOLDER_BUTTON-PUBLISH_ONLINE');
				}
				$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
				$tpl->setVariable('LINK-HREF',$tmp_link);
				$tpl->setVariable('LINK-TITLE',$tmp_descr);
				$tpl->parseCurrentBlock();
				unset($tmp_link);
				unset($tmp_pic);
				unset($tmp_descr);
			} else {
				$tpl->setCurrentBlock('FOLDER_BUTTON-PUBLISH_OFF');
				$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
				$tpl->parseCurrentBlock();
			}

			// Ordner: l�schen
			if ($perm -> have_perm(5, 'cat', $con_tree[$a]['idcat'])) {
				$tpl->setCurrentBlock('FOLDER_BUTTON-DELETE_ON');
				$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
				$tpl->setVariable('LINK-HREF',$sess->url('main.php?area=con&action=cat_delete&idcat='.$con_tree[$a]['idcat']));
				$tpl->setVariable('LINK-TITLE',$cms_lang['con_folder_delete']);
				$tpl->parseCurrentBlock();
			}	else {
				$tpl->setCurrentBlock('FOLDER_BUTTON-DELETE_OFF');
				$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
				$tpl->parseCurrentBlock();
			}

			// Ordner: sperren
			if ($perm -> have_perm(8, 'cat', $con_tree[$a]['idcat'])) {
				if ($con_tree[$a]['offline']) {
					$lock_val = 0;
					$lock_text = $cms_lang['con_unlock'];
					$tpl->setCurrentBlock('FOLDER_BUTTON-LOCK_UNLOCK');
				} else {
					$lock_val  = 1;
					$lock_text = $cms_lang['con_lock'];
					$tpl->setCurrentBlock('FOLDER_BUTTON-LOCK_LOCK');
				}
				$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);
				$tpl->setVariable('LINK-HREF','main.php?area=con&action=cat_lock&idcat='.$con_tree[$a]['idcat'].'&lock='.$lock_val);
				$tpl->setVariable('LINK-TITLE',$lock_text);
				$tpl->parseCurrentBlock();
			} else {
				$tpl->setCurrentBlock('FOLDER_BUTTON-LOCK_OFF');
				$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
				$tpl->parseCurrentBlock();
			}

			// Ordner: Vorschau
			$catlink = $con_tree[$a]['link'];
			if (in_array($a, $startpage_in_cat_is_https))
			{
				$catlink =  str_replace('http://', 'https://', $con_tree[$a]['link']);
			}
			$tpl->setCurrentBlock('FOLDER_BUTTON-PREVIEW');
			$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);
			$tpl->setVariable('LINK-HREF',$catlink);
			$tpl->setVariable('LINK-TITLE',$cms_lang['con_preview']);
			$tpl->parseCurrentBlock();
	}

		// Tabellenfarbwerte
		if ($con_tree[$a]['offline']) {
        	$tpl_cat_values['TABLE_COLOR']     = 'tblrbgcolorf1';
            $tpl_cat_values['TABLE_OVERCOLOR'] = 'tblrbgcolorover';
		} else {
            $tpl_cat_values['TABLE_COLOR']     = 'tblrbgcolorf2';
            $tpl_cat_values['TABLE_OVERCOLOR'] = 'tblrbgcolorover';
		}
		
		unset($cat_actions);

		$padding = $padding + 40;
		$tpl -> setCurrentBlock('SIDES');

		// Seiten anzeigen
		if ($tlo_tree['expanded'][$a] != '' && $con_side[$a]) {
			ksort($sidelist[$a]);
			$tmp_count = count($con_side[$a]);
			$sql = "SELECT
					 D.idcatside, D.idcat, E.idside, D.is_start, D.sortindex, J.username,
					 F.created, F.lastmodified, F.title, F.summary, F.online, F.meta_redirect, F.meta_redirect_url, F.is_https,
					 F.idsidelang, F.idtplconf, H.name, I.changed
					FROM "       . $cms_db['cat_side'] . " D
					 LEFT JOIN " . $cms_db['side']     . " E USING(idside)
                     LEFT JOIN " . $cms_db['side_lang']. " F USING(idside)
					 LEFT JOIN " . $cms_db['tpl_conf'] . " G using(idtplconf)
					 LEFT JOIN " . $cms_db['tpl']      . " H using(idtpl)
					 LEFT JOIN " . $cms_db['code']     . " I ON D.idcatside = I.idcatside AND F.idlang=I.idlang
                     LEFT JOIN " . $cms_db['users']    . " J ON F.author=J.user_id
                    WHERE D.idcat        = $a
					 AND  E.idclient     = $client
					 AND  F.idlang       = $lang
					ORDER BY D.sortindex";
			$db->query($sql);
			$format_datetime = $cfg_cms['format_date'].' '.$cfg_cms['format_time'];
			while($db->next_record()) {
				if ( $perm->have_perm(17, 'side', $db->f('idcatside'), $db->f('idcat'))) {
						$tmp_side['idcatside']    = $db->f('idcatside');
						$tmp_side['link']         = $sess->url(($db->f('is_https') == 1 && $cfg_client['https'] == 1 ? str_replace('http://', 'https://', $cfg_client['path_http_edit'] . $cfg_client['path_rel']): $cfg_client['path_http_edit'] . $cfg_client['path_rel']).$cfg_client['contentfile']."?lang=$lang&idcatside=".$db->f('idcatside')."&view=preview");
						$tmp_side['idcat']        = $db->f('idcat');
						$tmp_side['idside']       = $db->f('idside');
						$tmp_side['is_start']     = $db->f('is_start');
						$tmp_side['author']       = $db->f('username');
						$tmp_side['created']      = date($format_datetime, $db->f('created'));
						$tmp_side['lastmodified'] = date($format_datetime, $db->f('lastmodified'));
						$tmp_side['name']         = htmlentities($db->f('title'), ENT_COMPAT, 'UTF-8');
						$tmp_side['online']       = (int) $db->f('online');
						$tmp_side['meta_redirect']= $db->f('meta_redirect');
						$tmp_side['meta_redirect_url'] = $db->f('meta_redirect_url');
						$tmp_side['summary']      = htmlentities(stripslashes($db->f('summary')), ENT_COMPAT, 'UTF-8');
						$tmp_side['idsidelang']   = $db->f('idsidelang');
						$tmp_side['idtplconf']    = $db->f('idtplconf');
						$tmp_side['tplname']      = htmlentities($db->f('name'), ENT_COMPAT, 'UTF-8');
						// offline ist "Seite gesperrt"
						$tmp_side['offline']      = (($db->f('online') & 0x04) == 0x04);  // change JB: ($db->f('perm') == '0');
						$tmp_side['sortindex']    = $db->f('sortindex');

						// Seite: konfigurieren
						$tplname = ($tmp_side['idtplconf'] != '0') ? $tmp_side['tplname'] : $cms_lang['gen_default'];

						$tpl_side_values['SIDE-PADDING-LEFT'] = $padding;

						// Popupinformationen Seite

						if (!$tmp_side['tplname']) {
							$tmp_side['tplname'] = $con_tree[$a]['tplname'];
							$tpl->setVariable('TPL-CLASS','class="tplfolder"');
						}
						
						$con_summary = str_replace("\n", "<br>", $tmp_side['summary']);
						$con_summary = str_replace("\r", '', $con_summary);
#						$con_summary = str_replace("'", "\'", $con_summary);//'

						$tpl->setCurrentBlock('SIDE_BUTTON-CONFIG_INFO');								

						if ($tmp_side['summary'] != '') {
							$tpl->setVariable('LNG_SUMMARY',$cms_lang['con_notices']);
							$tpl->setVariable('SUMMARY',$con_summary);					
						}
						if ($tmp_side['meta_redirect'] == '1') {
							$tpl->setVariable('LNG_REDIRECT',$cms_lang['con_metaredirect_url']);
							$tpl->setVariable('REDIRECT',$tmp_side['meta_redirect_url']);
						}

						$tpl->setVariable('TOOLINFO-TITLE',$cms_lang['con_actions']['30']);		
						$tpl->setVariable('LNG_CREATED',$cms_lang['con_created']);
						$tpl->setVariable('LNG_MODIFIED',$cms_lang['con_lastmodified']);		
						$tpl->setVariable('LNG_EDITOR',$cms_lang['con_author']);
						$tpl->setVariable('LNG_SIDEINFO',$cms_lang['con_side_information']);		
						$tpl->setVariable('LNG_IDCATSIDE',$cms_lang['con_idcatside']);		
						$tpl->setVariable('LNG_TPL',$cms_lang['con_template']);		
						$tpl->setVariable('CREATED',$tmp_side['created']);
						$tpl->setVariable('MODIFIED',$tmp_side['lastmodified']);
						$tpl->setVariable('EDITOR',$tmp_side['author']);
						$tpl->setVariable('IDCATSIDE',$tmp_side['idcatside']);
						$tpl->setVariable('TPL-NAME',$tmp_side['tplname']);
						$tpl->parseCurrentBlock();

						// Seite: konfigurieren
						if($perm->have_perm(20, 'side', $tmp_side['idcatside'], $tmp_side['idcat'])) {
							$tmp_side_idtplconf = ($tmp_side['tplname']) ? $tmp_side['idtplconf']: '0';
							if($sefrengoPDA->pageDoubletExists( (int) $tmp_side['idside'])) {
								$tpl->setCurrentBlock('SIDE_BUTTON-CONFIG_CLONE');								
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
								$tpl->setVariable('LINK-HREF','main.php?area=con_configside&idside='.$tmp_side['idside'].'&idcat='.$con_tree[$a]['idcat'].'&idcatside='.$tmp_side['idcatside'].'&idtplconf='.$tmp_side_idtplconf);
								$tpl->setVariable('LINK-TITLE',$cms_lang['con_actions']['30']);
								$tpl->parseCurrentBlock();
						} else {
								$tpl->setCurrentBlock('SIDE_BUTTON-CONFIG_ON');								
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
								$tpl->setVariable('LINK-HREF','main.php?area=con_configside&idside='.$tmp_side['idside'].'&idcat='.$con_tree[$a]['idcat'].'&idcatside='.$tmp_side['idcatside'].'&idtplconf='.$tmp_side_idtplconf);
								$tpl->setVariable('LINK-TITLE',$cms_lang['con_actions']['30']);
								$tpl->parseCurrentBlock();
							}
						} else {
							if($sefrengoPDA->pageDoubletExists( (int) $tmp_side['idside'])) {
								$tpl->setCurrentBlock('SIDE_BUTTON-CONFIG_CLONE-OFF');								
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
								$tpl->setVariable('LINK-TITLE',$cms_lang['con_actions']['30']);
								$tpl->parseCurrentBlock();
							}	else {
								$tpl->setCurrentBlock('SIDE_BUTTON-CONFIG_OFF');								
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
								$tpl->setVariable('LINK-TITLE',$cms_lang['con_actions']['30']);
								$tpl->parseCurrentBlock();
							}
						}

						// Seite: bearbeiten
						$tpl->setCurrentBlock('SIDE_NAME');								
						$tpl->setVariable('LINK-HREF',$sess->url('main.php?area=con_editframe&idcatside='.$tmp_side['idcatside']));
						$tpl->setVariable('LINK-TITLE',$cms_lang['con_editside']);
						$tpl->setVariable('LINK-NAME',$tmp_side['name']);
						$tpl->parseCurrentBlock();

						// Anker
						if ($tmp_side['idcatside'] == $idcatside || $tmp_side['idside'] == $idside) 
							$tpl->touchBlock('SIDE_ANCHOR');

						// Seite: Aktionen
						// Sortieransicht
						if ($sort) {
							if($perm->have_perm(25, 'side', $tmp_side['idcatside'], $tmp_side['idcat'])){
								$sort_actions = '';

								// Seite: nach ganz oben verschieben
								if ($sidelist[$a]['1'] != $tmp_side['idcatside']) {
									$tpl->setCurrentBlock('SIDE_BUTTON-SORT_TOP');								
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
									$tpl->setVariable('LINK-HREF','main.php?area=con&action=sidetop&amp;sort=true&idcat='.$a.'&idcatside='.$tmp_side['idcatside'].'&sortindex='.$tmp_side['sortindex']);
									$tpl->setVariable('LINK-TITLE',$cms_lang['con_sideup']);
									$tpl->parseCurrentBlock();
								} elseif ($tmp_count > 1) {
									$tpl->setCurrentBlock('SIDE_BUTTON-SORT_UP-TOP');								
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
									$tpl->parseCurrentBlock();
								}
								
								// Seite: nach ganz unten verschieben
								if (end($sidelist[$a]) != $tmp_side['idcatside']) {
									$tpl->setCurrentBlock('SIDE_BUTTON-SORT_BOTTOM');								
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
									$tpl->setVariable('LINK-HREF','main.php?area=con&action=sidebottom&amp;sort=true&idcat='.$a.'&idcatside='.$tmp_side['idcatside'].'&sortindex='.$tmp_side['sortindex']);
									$tpl->setVariable('LINK-TITLE',$cms_lang['con_sidedown']);
									$tpl->parseCurrentBlock();
								} elseif ($tmp_count > 1) {
									$tpl->setCurrentBlock('SIDE_BUTTON-SORT_BOTTOM-OFF');								
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
									$tpl->parseCurrentBlock();
								}
								
								// Seite: nach oben verschieben
								if ($sidelist[$a]['1'] != $tmp_side['idcatside']) {
									$tpl->setCurrentBlock('SIDE_BUTTON-SORT_UP');								
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
									$tpl->setVariable('LINK-HREF','main.php?area=con&action=sideup&amp;sort=true&sortindex='.$tmp_side['sortindex'].'&idcat='.$tmp_side['idcat'].'&idside='.$tmp_side['idside']);
									$tpl->setVariable('LINK-TITLE',$cms_lang['con_sideup']);
									$tpl->parseCurrentBlock();
								} elseif ($tmp_count > 1) {
									$tpl->setCurrentBlock('SIDE_BUTTON-SORT_UP-OFF');								
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
									$tpl->parseCurrentBlock();
								}

								// Seite: nach unten verschieben
								if (end($sidelist[$a]) != $tmp_side['idcatside']) {
									$tpl->setCurrentBlock('SIDE_BUTTON-SORT_DOWN');								
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
									$tpl->setVariable('LINK-HREF','main.php?area=con&action=sidedown&amp;sort=true&sortindex='.$tmp_side['sortindex'].'&idcat='.$tmp_side['idcat'].'&idside='.$tmp_side['idside']);
									$tpl->setVariable('LINK-TITLE',$cms_lang['con_sidedown']);
									$tpl->parseCurrentBlock();
								} elseif ($tmp_count > 1) {
									$tpl->setCurrentBlock('SIDE_BUTTON-SORT_DOWN-OFF');								
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
									$tpl->parseCurrentBlock();
								}
								
								$tpl_side_values['SIDE_ACTIONS'] = $sort_actions;
								unset($sort_actions);
							}
						// Standardansicht
						} else {
							$side_actions = '';

							// Seite: Startseite festlegen
							if($perm->have_perm(28, 'side', $tmp_side['idcatside'], $tmp_side['idcat'])) {
								if ($tmp_side['is_start'] == '1') {
									$tpl->setCurrentBlock('SIDE_BUTTON-STARTPAGE_IS');								
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
									$tpl->setVariable('LINK-TITLE',$cms_lang['con_actions']['10'][$tmp_side['is_start']]);
									$tpl->parseCurrentBlock();
								} else {
									$tpl->setCurrentBlock('SIDE_BUTTON-STARTPAGE_IS-NOT');								
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
									$tpl->setVariable('LINK-HREF','main.php?area=con&action=side_start&idcatside='.$tmp_side['idcatside'].'&is_start='.$tmp_side['is_start']);
									$tpl->setVariable('LINK-TITLE',$cms_lang['con_actions']['10'][$tmp_side['is_start']]);
									$tpl->parseCurrentBlock();
								}
							}	else {
									$tpl->setCurrentBlock('SIDE_BUTTON-STARTPAGE_OFF');								
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);		
									$tpl->parseCurrentBlock();
							}
							
							// Seite: online/offline/publish schalten
							if ($perm->have_perm(23, 'side', $tmp_side['idcatside'], $tmp_side['idcat'])) {
								$tmp_link   = 'main.php?area=con&action=side_visible&idside=' . $tmp_side['idside'] . '&idcat=' . $tmp_side['idcat'] . '&online=' . $tmp_side['online'];
								$tmp_link2  = 'main.php?area=con&action=side_publish&idcatside='.$tmp_side['idcatside'].'&idcat='.$a;
								$tmp_descr  = $cms_lang['con_side_visible'][$tmp_side['online']];
								$tmp_descr2 = $cms_lang['con_publish'];

								if (($tmp_side['online'] & 0x03) == 0x00) {
									// ist gesch�tzt,
									if ($cfg_client['publish'] == '1' && $con_side[$a][$tmp_side['idcatside']]['status'] == 'true') {
										$tmp_descr = $tmp_descr2;
										$tmp_link = $tmp_link2;
										$tpl->setCurrentBlock('SIDE_BUTTON-PUBLISH_OFF-PUBLISH');
									} else 	
										$tpl->setCurrentBlock('SIDE_BUTTON-PUBLISH_OFFLINE');

								} else {
									// online oder zeitgesteuert
									if (((int)$con_tree[$a]['visible'] & 0x03) == 0x00) {
										if ($cfg_client['publish'] == '1' && $con_side[$a][$tmp_side['idcatside']]['status'] == 'true') {
											$tmp_descr = $tmp_descr2;
											$tmp_link = $tmp_link2;
											$tpl->setCurrentBlock('SIDE_BUTTON-PUBLISH_OFF-PUBLISH');
										} else 
											$tpl->setCurrentBlock('SIDE_BUTTON-PUBLISH_ONOFFLINE');

									} else {
										if ($cfg_client['publish'] == '1' && $con_side[$a][$tmp_side['idcatside']]['status'] == 'true') {
											$tmp_descr = $tmp_descr2;
											$tmp_link = $tmp_link2;
											$tpl->setCurrentBlock('SIDE_BUTTON-PUBLISH_ON-PUBLISH');
										} else {
					             if (((int)$tmp_side['online'] & 0x02) == 0x02) 
												$tpl->setCurrentBlock('SIDE_BUTTON-PUBLISH_TIME');
										   else
 												$tpl->setCurrentBlock('SIDE_BUTTON-PUBLISH_ONLINE');
										}
									}
								}
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
								$tpl->setVariable('LINK-HREF',$tmp_link);
								$tpl->setVariable('LINK-TITLE',$tmp_descr);
								$tpl->parseCurrentBlock();
								unset($tmp_link);
								unset($tmp_pic);
								unset($tmp_description);
							} else {
								$tpl->setCurrentBlock('SIDE_BUTTON-PUBLISH_OFF');
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
								$tpl->parseCurrentBlock();
							}
							// Seite: bearbeiten
							if($perm->have_perm(19, 'side', $tmp_side['idcatside'], $tmp_side['idcat'])) {
								$tpl->setCurrentBlock('SIDE_BUTTON-EDIT_ON');
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
								$tpl->setVariable('LINK-HREF',$sess->url('main.php?area=con_editframe&idcatside='.$tmp_side['idcatside']));
								$tpl->setVariable('LINK-TITLE',$cms_lang['con_editside']);
								$tpl->parseCurrentBlock();
							} else {
								$tpl->setCurrentBlock('SIDE_BUTTON-EDIT_OFF');
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
								$tpl->parseCurrentBlock();
							}

							//Seite koieren
							if( $perm->have_perm(18, 'cat', $tmp_side['idcat']) ) {
								$tpl->setCurrentBlock('SIDE_BUTTON-COPY_ON');
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
								$tpl->setVariable('LINK-HREF',$sess->url('main.php?area=con_copyside&idcatside='.$tmp_side['idcatside'].'&idcat='.$tmp_side['idcat']));
								$tpl->setVariable('LINK-TITLE',$cms_lang['con_side_dupl']);
								$tpl->parseCurrentBlock();
							} else {
								$tpl->setCurrentBlock('SIDE_BUTTON-COPY_OFF');
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
								$tpl->parseCurrentBlock();
							}


							// Seite: l�schen
							if($perm -> have_perm(21, 'side', $tmp_side['idcatside'], $tmp_side['idcat']))
								if($sefrengoPDA->pageDoubletExists( (int) $tmp_side['idside'])){
 									$tpl->setCurrentBlock('SIDE_BUTTON-DELETE_ON-CLONE');
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
									$tpl->setVariable('LINK-HREF',$sess->url('main.php?action=side_delete&idcat='.$tmp_side['idcat'].'&idside='.$tmp_side['idside']).'#catanchor');
									$tpl->setVariable('LINK-TITLE',$cms_lang['con_actions']['40']);
									$tpl->parseCurrentBlock();
								} else {
									$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
 									$tpl->setCurrentBlock('SIDE_BUTTON-DELETE_ON');
									$tpl->setVariable('LINK-HREF',$sess->url('main.php?action=side_delete&idcat='.$tmp_side['idcat'].'&idside='.$tmp_side['idside']));
									$tpl->setVariable('LINK-TITLE',$cms_lang['con_actions']['40']);
									$tpl->parseCurrentBlock();
								} 
							else {
								$tpl->setCurrentBlock('SIDE_BUTTON-DELETE_OFF');
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
								$tpl->parseCurrentBlock();
							}
							// Seite: sch�tzen
							if ($perm -> have_perm(24, 'side', $tmp_side['idcatside'], $tmp_side['idcat'])) {
				      	if ($tmp_side['offline']) {
									$lock_val = 0;
									$lock_text = $cms_lang['con_unlock_side'];
									$tpl->setCurrentBlock('SIDE_BUTTON-LOCK_UNLOCK');
								} else {
									$lock_val  = 1;
									$lock_text = $cms_lang['con_lock_side'];
									$tpl->setCurrentBlock('SIDE_BUTTON-LOCK_LOCK');
								}
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
								$tpl->setVariable('LINK-HREF','main.php?action=side_lock&idcatside='.$tmp_side['idcatside'].'&idside='.$tmp_side['idside'].'&lock='.$lock_val);
								$tpl->setVariable('LINK-TITLE',$lock_text);
								$tpl->parseCurrentBlock();
							} else {
								$tpl->setCurrentBlock('SIDE_BUTTON-LOCK_OFF');
								$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
								$tpl->parseCurrentBlock();
							}

							// Seite: Vorschau
							$tpl->setCurrentBlock('SIDE_BUTTON-PREVIEW');
							$tpl->setVariable('CMS-SKIN',$cfg_cms['skin']);				
							$tpl->setVariable('LINK-HREF',$tmp_side['link']);
							$tpl->setVariable('LINK-TITLE',$cms_lang['con_preview']);
							$tpl->parseCurrentBlock();
							$tpl_side_values['SIDE_ACTIONS'] = '';
							unset($side_actions);
						}

						// Tabellenfarbwerte
						if ($con_tree[$a]['offline'] || $tmp_side['offline']) {
							$tpl_side_values['TABLE_COLOR'] = 'tblrbgcolors1';
							$tpl_side_values['TABLE_OVERCOLOR'] = 'tblrbgcolorover';
						} else {
							$tpl_side_values['TABLE_COLOR'] = 'tblrbgcolors2';
							$tpl_side_values['TABLE_OVERCOLOR'] = 'tblrbgcolorover';
						}
						// Seitentemplate parsen
						$tpl->setCurrentBlock('SIDES');
						$tpl->setVariable($tpl_side_values);
						$tpl->parseCurrentBlock();
						unset($tpl_side_values, $tmp_side);
					}
				}
			}

		// Ordnertemplate parsen
		$tpl->setCurrentBlock('FOLDER');
		$tpl->setVariable($tpl_cat_values);
		$tpl->parse('FOLDER');
		unset($tpl_cat_values);
		

	}

// Es gibt keine Ordner
} else {
	$tpl->setCurrentBlock('EMPTY');
	$tpl_empty_values['LANG_NOCATS'] = $cms_lang['con_nofolder'];
	$tpl->setVariable($tpl_empty_values);
	$tpl->parse('EMPTY');
	unset($tpl_empty_values);
}

include('inc/inc.footer.php');
?>
