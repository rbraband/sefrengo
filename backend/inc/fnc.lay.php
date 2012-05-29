<?PHP
// File: $Id: fnc.lay.php 425 2011-07-31 16:10:34Z holger $
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
// + Revision: $Revision: 425 $
// +----------------------------------------------------------------------+
// + Description:
// +----------------------------------------------------------------------+
// + Changes: 
// +----------------------------------------------------------------------+
// + ToDo:
// +----------------------------------------------------------------------+

function lay_edit_layout($idlay, $name, $description, $code, $doctype, $doctype_autoinsert, $idclient) {
	global $db, $client, $auth, $cms_db, $cfg_cms, $css, $js, $cms_lang, $cfg_client, $perm, $cms_log;

	// Eintrag in 'lay' Tabelle
	if ($name == '') $name = $cms_lang['lay_defaultname'];
	set_magic_quotes_gpc($name);
	set_magic_quotes_gpc($description);
	set_magic_quotes_gpc($code);

	// Layout existiert noch nicht - neu erzeugen
	if (!$idlay) {
		$sql = "INSERT INTO
					".$cms_db['lay']."
					(name, description, deletable, code, doctype, doctype_autoinsert, idclient, author, created, lastmodified)
				VALUES
					('$name', '$description', '1', '$code', '$doctype', '$doctype_autoinsert', 
						'$idclient', '".$auth->auth['uid']."', '".time()."', '".time()."')";
		$db->query($sql);

		// neue Layout-ID suchen
		$sql = "SELECT MAX(idlay) AS idlay FROM ".$cms_db['lay'];
		$db->query($sql);
		$db->next_record();
		$idlay = $db->f('idlay');
		
		$eventname = 'lay_new';

	// Layout existiert - updaten
	} else {
		// hat sich das Layout ge�ndert?
		$sql = "SELECT code FROM ".$cms_db['lay'] ." WHERE idlay='$idlay'";
		$db->query($sql);
		$db->next_record();
		$code_old = $db->f('code');
		set_magic_quotes_gpc($code_old);
			$sql = "UPDATE ". $cms_db['lay']."
				SET
					name='$name', 
					description='$description', 
					code='$code',
					doctype='$doctype',
					doctype_autoinsert='$doctype_autoinsert',
					author='".$auth->auth['uid']."', lastmodified='".time()."'
				WHERE
					idlay='$idlay'";
			$db->query($sql);
			$change = TRUE;

		//rechte setzen
		if ($perm->have_perm('6', 'lay', $idlay)) {
			global $cms_gruppenids, $cms_gruppenrechte, $cms_gruppenrechtegeerbt, $cms_gruppenrechteueberschreiben;
			$perm->set_group_rights( 'lay', $idlay, $cms_gruppenids, $cms_gruppenrechte, $cms_gruppenrechtegeerbt, $cms_gruppenrechteueberschreiben, '', 0xF5);
		}
		
		$eventname = 'lay_edit';
	}

	// welche CSS-Dateien werden benutzt?
	$sql = "SELECT B.idupl, A.sortindex FROM $cms_db[lay_upl] A LEFT JOIN $cms_db[upl] B USING(idupl) LEFT JOIN $cms_db[filetype] C ON B.idfiletype=C.idfiletype WHERE idlay='$idlay' AND C.filetype='css'";
	$db->query($sql);
	while ($db->next_record()) $tmp_files['css'][$db->f('idupl')] = $db->f('sortindex');
	if (!is_array($tmp_files['css'])) $tmp_files['css']['0']='0';
	if (!is_array($css)) $css['0'] = '0';

	// benutzte CSS-Dateien in lay_upl schreiben
	$css = array_flip($css); // change key and values
	foreach ($css as $idupl => $sortindex)
	{
		if ($idupl != '0') {
			if (!array_key_exists($idupl,$tmp_files['css'])) {
				$sql = "INSERT INTO $cms_db[lay_upl] (idlay, idupl, sortindex) VALUES ('$idlay', '$idupl', $sortindex)";
				$db->query($sql);
				$change = TRUE;
			}
			else if(array_key_exists($idupl,$tmp_files['css']) && $tmp_files['css'][$idupl] != $sortindex) {
				$sql = "UPDATE $cms_db[lay_upl] SET sortindex = '$sortindex' WHERE idupl='$idupl' AND idlay='$idlay'";
				$db->query($sql);
				$change = TRUE;
			}
		}
	}

	// unbenutze CSS-Dateien aus lay_upl l�schen
	foreach ($tmp_files['css'] as $idupl => $sortindex)
	{
		if (!array_key_exists($idupl,$css)) {
			$sql = "DELETE FROM $cms_db[lay_upl] WHERE idupl='$idupl' AND idlay='$idlay'";
			$db->query($sql);
			$change = TRUE;
		}
	}

	// welche JS-Dateien werden benutzt?
	$sql = "SELECT B.idupl, A.sortindex FROM $cms_db[lay_upl] A LEFT JOIN $cms_db[upl] B USING(idupl) LEFT JOIN $cms_db[filetype] C ON B.idfiletype=C.idfiletype WHERE idlay='$idlay' AND C.filetype='js'";
	$db->query($sql);
	while ($db->next_record()) $tmp_files['js'][$db->f('idupl')] = $db->f('sortindex');
	if (!is_array($tmp_files['js'])) $tmp_files['js']['0']='0';
	if (!is_array($js)) $js['0'] = '0';

	// benutzte JS-Dateien in lay_upl schreiben
	$js = array_flip($js); // change key and values
	foreach ($js as $idupl => $sortindex)
	{
		if ($idupl != '0') {
			if (!array_key_exists($idupl,$tmp_files['js'])) {
				$sql = "INSERT INTO $cms_db[lay_upl] (idlay, idupl, sortindex) VALUES ('$idlay', '$idupl', $sortindex)";
				$db->query($sql);
				$change = TRUE;
			}
			else if(array_key_exists($idupl,$tmp_files['js']) && $tmp_files['js'][$idupl] != $sortindex) {
				$sql = "UPDATE $cms_db[lay_upl] SET sortindex = '$sortindex' WHERE idupl='$idupl' AND idlay='$idlay'";
				$db->query($sql);
				$change = TRUE;
			}
		}
	}
	
	// unbenutze JS-Dateien aus lay_upl l�schen
	foreach ($tmp_files['js'] as $idupl => $sortindex)
	{
		if (!array_key_exists($idupl,$js)) {
			$sql = "DELETE FROM $cms_db[lay_upl] WHERE idupl='$idupl' AND idlay='$idlay'";
			$db->query($sql);
			$change = TRUE;
		}
	}

	if ($change == TRUE) {
		// Status der 'code' Tabelle �ndern
		$list = get_idtplconf_by_using_type($idlay, 'lay');
		$list = get_idcode_by_idtplconf($list);
		change_code_status($list, '1');
		unset($list);
	}
	
	//Log
	$cms_log->info('user', $eventname, array('idlay' => $idlay, 'name' => $name));
	// Event
	fire_event($eventname, array(
		'idlay' => $idlay,
		'name' => $name,
		'description' => $description,
		'code' => $code,
		'doctype' => $doctype,
		'doctype_autoinsert' => $doctype_autoinsert,
		'js' => $js,
		'css' => $css,
		'author' => $auth->auth['uid']
	));
	
	return $idlay;
}


function lay_copy($idlay, $from, $into)
{
	global $db, $cms_db, $auth, $perm, $cms_log;

	if (!$from) $from='0';
	if (!$into) $into='0';

	// Layout kopieren
	$sql = "SELECT * FROM $cms_db[lay] WHERE idlay='$idlay'";
	$db->query($sql);
	if ($db->next_record()) {
		$name = make_string_dump($db->f('name'));
		$description = make_string_dump($db->f('description'));
		$code = make_string_dump($db->f('code'));
		$doctype = make_string_dump($db->f('doctype'));
		$doctype_autoinsert = make_string_dump($db->f('doctype_autoinsert'));

		$sql = "INSERT INTO
					".$cms_db['lay'] ."
					(name, description, code, doctype, doctype_autoinsert, idclient, author, created, lastmodified)
				VALUES
					('$name', '$description', '$code', '$doctype', '$doctype_autoinsert', '$into', '".$auth->auth['uid']."',
					'".time()."', '".time()."')";
		$db->query($sql);

		//set perms
		// get last insert id
		$sql = "SELECT MAX(idlay) AS idlay FROM ".$cms_db['lay'];
		$db->query($sql);
		$db->next_record();
		$last_insert_id = $db->f('idlay');

		$perm->xcopy_perm($idlay, 'lay', $last_insert_id, 'lay', 0xFFFFFFFF, 0, 0, true);  // make new userright


		// Event
		if ($from != '0') $eventname = 'lay_export';
		else $eventname = 'lay_import';
		
		//Log
		$cms_log->info('user', $eventname, array('idlay' => $idlay, 'name' => $name));
		//Event
		fire_event($eventname, array(
			'idlay' => $idlay,
			'name' => $name,
			'description' => $description,
			'code' => $code,
			'doctype' => $doctype,
			'doctype_autoinsert' => $doctype_autoinsert,
			'author' => $auth->auth['uid']
		));
		
		return '0302';
	}
}

function lay_delete_layout($idlay) {
	global $db, $client, $cms_db, $perm, $cms_log;

	// Wird Layout noch verwendet?
	$sql = "SELECT * FROM ". $cms_db['tpl'] ." WHERE idlay='$idlay'";
	$db->query($sql);
	if ($db->affected_rows()) return '0301';
	else {
		//Daten vor dem L�schen f�r das Event laden
		$sql = "SELECT * FROM $cms_db[lay] WHERE idlay='$idlay'";
		$db->query($sql);
		if ($db->next_record()) {
			$name = make_string_dump($db->f('name'));
			$description = make_string_dump($db->f('description'));
			$code = make_string_dump($db->f('code'));
			$doctype = make_string_dump($db->f('doctype'));
			$doctype_autoinsert = make_string_dump($db->f('doctype_autoinsert'));
			$author = make_string_dump($db->f('author'));
		} else {
			$name = "";
			$description = "";
			$code = "";
			$doctype = "";
			$doctype_autoinsert = "";
			$author = "";
		}
		
		// Layout l�schen
		$sql = "DELETE FROM ". $cms_db['lay'] ." WHERE idlay='$idlay'";
		$db->query($sql);

		// Eintr�ge aus lay_upl l�schen
		$sql = "DELETE FROM ". $cms_db['lay_upl'] ." WHERE idlay='$idlay'";
		$db->query($sql);

		//delete perm
		$perm->delete_perms($idlay, 'lay');
		
		//Log
		$cms_log->info('user', 'lay_delete', array('idlay' => $idlay, 'name' => $name));
		// Event
		fire_event('lay_delete', array(
			'idlay' => $idlay,
			'name' => $name,
			'description' => $description,
			'code' => $code,
			'doctype' => $doctype,
			'doctype_autoinsert' => $doctype_autoinsert,
			'author' => $author
		));
	}
}
?>