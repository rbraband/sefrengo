<?PHP
// File: $Id: fnc.plug.php 666 2012-03-20 22:46:56Z bjoern $
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
// + Revision: $Revision: 666 $
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
include_once('inc/fnc.libary.php');

function plug_copy($idplug, $from, $into) {
    global $db, $auth, $cms_db, $cfg_cms, $rep, $perm, $cms_log;
    if (!$from) $from = '0';
    if (!$into) $into = '0';
    $ident = "idclient='" . $from . "'";
    $sql = "SELECT * FROM $cms_db[plug] WHERE $ident AND idplug='$idplug'";
    $db->query($sql);
    if ($db->next_record()) {
        $name = make_string_dump($db->f('name'));
        //$verbose = make_string_dump($db->f('verbose'));
        $description = make_string_dump($db->f('description'));
        $version = make_string_dump($db->f('version'));
        $cat = make_string_dump($db->f('cat'));
        $index_file = make_string_dump($db->f('index_file'));
        $root_name = make_string_dump($db->f('root_name'));
        $config = make_string_dump($db->f('config'));
        $rep_id = make_string_dump($db->f('repository_id'));
        $repid = ($rep_id != '') ? $rep_id : $rep->gen_new_plug($name);
        $source_id = $db->f('source_id');
        $is_install = (bool) $db->f('is_install');
        $is_multi = (bool) $rep->plug_execute($idplug, 'multi_client');
        if ($from != $into) {
            $ident2 = "idclient='" . $into . "'";
            $sql1 = "SELECT source_id, is_install from " . $cms_db['plug'] . " WHERE " . $ident2 . " AND source_id='" . $idplug . "'";
            $db->query($sql1);
            if ($db->next_record()) {
                $is_install2 = (bool) $db->f('is_install');
                return '1613';
            } else {
                $sql2 = "INSERT INTO
					     ".$cms_db['plug']."
						 (name, description, version, cat, root_name, index_file, config, idclient, is_install, author,
						  created, lastmodified, repository_id, source_id, verbose)
						  VALUES ('$name', '$description', '$version', '$cat', '$root_name', '$index_file', '$config',
						  '$into', '" . intval($is_multi) . "', '" . $auth->auth['uid'] . "', '" . time() . "', '
						  " . time() . "', '$repid', '$idplug', '$verbose')";
                $db->query($sql2);
              //Userrecht des Plugins in neues Plugin kopieren
              $new_idplug = $db->insert_id(); 
            	$perm->xcopy_perm($idplug, 'plug', $new_idplug, 'plug', 0xFFFFFFFF, 0, 0, true);
              $install_client = ($is_multi && $into >= 1) ? $into : false;
            } 
            $install = (($install_client && !$is_install) || (!$is_multi && !$is_install) && $into >= 1) ? $into : false;
        } else {
            $rep->plug_execute($idplug, 'uninstall', '', 'meta');
            // Event
            fire_event('plug_uninstall_sql', array(
				'idplug' => $idplug,
				'name' => $name,
				'description' => $description,
				'version' => $version,
				'cat' => $cat,
				'repository_id' => $repid,
				'source_id' => $idplug,
				'root_name' => $root_name,
				'index_file' => $index_file,
				'config' => $config,
				'is_install' => $is_install,
				'is_multi' => $is_multi
			));
            $return = '1607';
            $update = true;
        } 
        if ($install || $update) { 
            $rep->plug_execute($idplug, 'install', '', 'meta');
            $rep->plug_execute($idplug, 'install', '', 'module');
            $sql = "UPDATE " . $cms_db['plug'] . " SET is_install = '1' WHERE $ident AND idplug = '$idplug'\n";
            $db->query($sql);
            // Event
            fire_event('plug_install_sql', array(
				'idplug' => $idplug,
				'name' => $name,
				'description' => $description,
				'version' => $version,
				'cat' => $cat,
				'repository_id' => $repid,
				'source_id' => $idplug,
				'root_name' => $root_name,
				'index_file' => $index_file,
				'config' => $config,
				'is_install' => $is_install,
				'is_multi' => $is_multi				
			));
            $return_install = '1609';
        }
        if ($install_client) {
            $ident_client = "idclient='" . $into . "'";
            $rep->plug_execute($idplug, 'install', 'client', 'meta');
            $sql = "UPDATE " . $cms_db['plug'] . " SET is_install = '1' WHERE $ident_client AND idplug = '$new_idplug'\n";
            $db->query($sql);
			//Log
			$cms_log->info('user', 'plug_install_client', array('idplug' => $new_idplug, 'name' => $name, 'version' => $version, 'idclient' => $into, 'clientname' => getClientNameByIdclient($into)));
            // Event
            fire_event('plug_install_client', array(
				'idplug' => $new_idplug,
				'name' => $name,
				'description' => $description,
				'version' => $version,
				'cat' => $cat,
				'idclient' => $into,
				'author' => $auth->auth['uid'],
				'created' => time(),
				'lastmodified' => time(),
				'repository_id' => $repid,
				'source_id' => $idplug,
				'root_name' => $root_name,
				'index_file' => $index_file,
				'config' => $config,
				'is_install' => $is_install,
				'is_multi' => $is_multi
			));
            $return_install = '1609';
        }
        if ($return_install != '') return $return_install;
        // Event
        if ($from != '0') {
            $eventname = 'plug_export';
            $return = '1614';
        } else {
            $eventname = 'plug_import';
            $return = '1602';
        } 
		//Log
		$cms_log->info('user', $eventname, array('idplug' => $idplug, 'name' => $name, 'version' => $version));
		// Event
		fire_event($eventname, array(
			'idplug' => $idplug,
			'name' => $name,
			'description' => $description,
			'version' => $version,
			'cat' => $cat,
			'idclient' => $into,
			'author' => $auth->auth['uid'],
			'created' => time(),
			'lastmodified' => time(),
			'repository_id' => $repid,
			'source_id' => $idplug,
			'root_name' => $root_name,
			'index_file' => $index_file,
			'config' => $config,
			'is_install' => $is_install,
			'is_multi' => $is_multi
		));
        return $return;
    } 
} 
function plug_save_config($idplug, $config, $plug_config_overwrite_all) {
    global $db, $cms_db, $perm;
    if ($plug_config_overwrite_all) {
    // todo:insert a full configuration module for plugin
    }
} 
function plug_save($idplug, $name, $description, $plugversion, $plugcat, $idclient, $repid = '', $sql_install = '', $sql_uninstall = '', $sql_update = '', $root_name = 'hold_old_data', $index_file = 'hold_old_data') {
    global $db, $auth, $cms_db, $cfg_cms, $cms_lang, $cfg_client, $rep, $perm, $cms_log; 

    //ATTENTION!!! make idplug global / necessary for apply header
    global $idplug;   
   
   
    // Eintrag in 'plug' Tabelle
    if ($name == '') $name = $cms_lang['plug_defaultname'];
    set_magic_quotes_gpc($name);
    set_magic_quotes_gpc($description);
    set_magic_quotes_gpc($plugversion);
    set_magic_quotes_gpc($plugcat);
    set_magic_quotes_gpc($root_name);
    set_magic_quotes_gpc($index_file);
    remove_magic_quotes_gpc($sql_install);
    remove_magic_quotes_gpc($sql_uninstall);
    remove_magic_quotes_gpc($sql_update);
    $root_name = str_replace('plugins/', '', $root_name);
    if ($root_name == 'name_des_verzeichnisses') $root_name = strtolower($name);
    $repositoryid = ($repid == '') ? $rep->gen_new_plug($name) : $repid;
    if (!$idplug) {
        // plugin existiert noch nicht
        // todo:formcheck name, version usw.
        $root_name = $root_name == 'hold_old_data' ? '' : $root_name;
        $index_file = $index_file == 'hold_old_data' ? '' : $index_file;
        $sql = "INSERT INTO
			   " . $cms_db['plug'] . "
			   (name, description, version, cat, author, created, lastmodified, repository_id, root_name, index_file,
			   idclient)
			   VALUES
			   ('$name', '$description', '$plugversion', '$plugcat', '" . $auth->auth['uid'] . "', '" . time() . "', '
			   " . time() . "', '$repositoryid', '$root_name', '$index_file', '$idclient')";
        $db->query($sql); 
        $idplug = $last_id = $db->insert_id();
        if ($rep->_plug_init($idplug)) $return = '1612';
        else $return = (true !== (plug_new($root_name, $index_file) && $rep->_plug_init($idplug))) ? '1613' : '1612';
		//Log
		$cms_log->info('user', 'plug_new', array('idplug' => $idplug, 'name' => $name, 'version' => $plugversion));
		// Event
        fire_event('plug_new', array(
			'idplug' => $idplug,
			'name' => $name,
			'description' => $description,
			'version' => $plugversion,
			'cat' => $plugcat,
			'idclient' => $idclient,
			'author' => $auth->auth['uid'],
			'created' => time(),
			'lastmodified' => time(),
			'repository_id' => $repositoryid,
			'install_sql' => $sql_install,
			'uninstall_sql' => $sql_uninstall,
			'update_sql' => $sql_update,
			'source_id' => '0',
			'root_name' => $root_name,
			'index_file' => $index_file,
			'errno' => $return
		));
    } else {
        $rep->plug_execute($idplug, 'this', 'update', 'install', $rep->decode_sql($sql_install));
        $rep->plug_execute($idplug, 'this', 'update', 'uninstall', $rep->decode_sql($sql_uninstall));
        $rep->plug_execute($idplug, 'this', 'update', 'update', $rep->decode_sql($sql_update));
        $root_name = $root_name == 'hold_old_data' ? 'root_name' : "'$root_name'";
        $index_file = $index_file == 'hold_old_data' ? 'index_file' : "'$index_file'";
        $sql = "UPDATE
			   " . $cms_db['plug'] . "
			   SET
			   name='$name', description='$description', version = '$plugversion', cat = '$plugcat', author='
			   " . $auth->auth['uid'] . "', lastmodified='" . time() . "', root_name=$root_name, repository_id = '$repositoryid',
			   index_file=$index_file WHERE idplug=$idplug OR source_id=$idplug";
        $db->query($sql); 
	    //todo:checken in wie weit die rechte der installierten Plugins betroffen sind!
	    // Rechte setzen
	    if ($perm->have_perm('6', 'plug', $idplug)) {
	        global $cms_gruppenids, $cms_gruppenrechte, $cms_gruppenrechtegeerbt, $cms_gruppenrechteueberschreiben;
	        $perm->set_group_rights('plug', $idplug, $cms_gruppenids, $cms_gruppenrechte, $cms_gruppenrechtegeerbt, $cms_gruppenrechteueberschreiben, '',0x00038AFD);
	    }
    	
		//Log
		$cms_log->info('user', 'plug_edit', array('idplug' => $idplug, 'name' => $name, 'version' => $plugversion));
    	// Event
        fire_event('plug_edit', array(
			'idplug' => $idplug,
			'name' => $name,
			'description' => $description,
			'version' => $plugversion,
			'cat' => $plugcat,
			'idclient' => $idclient,
			'author' => $auth->auth['uid'],
			'lastmodified' => time(),
			'repository_id' => $repositoryid,
			'install_sql' => $sql_install,
			'uninstall_sql' => $sql_uninstall,
			'update_sql' => $sql_update,
			'source_id' => '0',
			'root_name' => $root_name,
			'index_file' => $index_file
		));
        $return = '1612';
    } 
    
    return $return;
}
function plug_new($root_name, $index_file = '') {
    require_once('Archive/Tar.php');
    $tar = new Archive_Tar($cfg_cms['path_base'].$cfg_cms['path_backend_rel'] . "tpl/pluginvorlage.tar");
    $tmp_plugmeta = $tar->extractInString('pluginvorlage.php');
    $tmp_plugmeta = str_replace('{pluginname}', $root_name, $tmp_plugmeta);
    if (!$tar->extractModify($cfg_cms['path_base'].$cfg_cms['path_backend_rel'] . 'plugins/'. $root_name . '/', '/')) return '-1';
    if (true != ($write = lib_write_file($cfg_cms['path_base'].$cfg_cms['path_backend_rel'] . 'plugins/'. $root_name . '/' .$root_name . '_meta.php', $tmp_plugmeta))) return '-2';
    if ('-1' == ($delete = lib_delete_file($cfg_cms['path_base'].$cfg_cms['path_backend_rel'] . 'plugins/'. $root_name . '/pluginvorlage.php'))) return '-3';
    if ($index_file != '' && !lib_check_file($cfg_cms['path_base'].$cfg_cms['path_backend_rel'] . 'plugins/'. $root_name . '/' . $index_file)) {
        $content = "<?PHP\n/*\n * Plugin-Indexfile\n */\n?".">\n";
        if (true != (lib_write_file($cfg_cms['path_base'].$cfg_cms['path_backend_rel'] . 'plugins/'. $root_name . '/' . $index_file, $content))) return '-4';
    }
    return true;
}
function plug_delete($idplug, $idclient) {
    global $db, $client, $cms_db, $cfg_cms, $rep, $perm, $cms_log;
    $ident = "idclient='" . $idclient . "'";
    $sql1 = "SELECT name, description, version, cat, author, created, lastmodified, repository_id, index_file, source_id, is_install, root_name from " . $cms_db['plug'] . " WHERE $ident AND idplug='$idplug'";
    $db->query($sql1);
    if ($db->next_record()) {
        $is_online = (bool) $db->f('is_install');
        $name = make_string_dump($db->f('name'));
        $description = make_string_dump($db->f('description'));
        $version = make_string_dump($db->f('version'));
        $cat = make_string_dump($db->f('cat'));
        $author = make_string_dump($db->f('author'));
        $created = make_string_dump($db->f('created'));
        $lastmodified = make_string_dump($db->f('lastmodified'));
        $repository_id = make_string_dump($db->f('repository_id'));
        $index_file = make_string_dump($db->f('index_file'));
        $author = make_string_dump($db->f('author'));
        $source = make_string_dump($db->f('source_id'));
        $root_dir = make_string_dump($db->f('root_name'));
        $is_multi = (bool) $rep->plug_execute($idplug, 'multi_client');
        if ($source >= 1) {
            $sql2 = "SELECT is_install, idclient from " . $cms_db['plug'] . " WHERE idplug='$source'";
            $db->query($sql2);
            if ($db->next_record()) {
                $is_online2 = (bool) $db->f('is_install');
                $idclient2 = $db->f('idclient');
                $is_multi2 = (bool) $rep->plug_execute($source, 'multi_client');
                $ident2 = "idclient='" . $idclient2 . "'";
                $is_online3 = $rep->plug_count('all', $source);
                $sql4 = "UPDATE " . $cms_db['plug'] . " SET is_install = '0' WHERE $ident2 AND idplug = '$source'\n";
            }
        }
        $is_online4 = $rep->plug_count('all', $idplug);
        if ($is_online4 == 0) {
            $sql = "DELETE FROM " . $cms_db['plug'] . " WHERE $ident AND idplug='$idplug'";
            $db->query($sql);
    		    // Rechte l�schen
    		    $perm->delete_perms($idplug, 'plug', 0, 0, 0, true);
            // Uninstall Client
            if ($is_online && $is_multi2 && $is_online2) {
                $rep->plug_execute($source, 'uninstall', 'client', 'meta');
				//Log
				$cms_log->info('user', 'plug_uninstall_client', array('idplug' => $idplug, 'name' => $name, 'version' => $version, 'idclient' => $idclient, 'clientname' => getClientNameByIdclient($idclient)));
                // Event
                fire_event('plug_uninstall_client', array(
					'idplug' => $idplug,
					'name' => $name,
					'description' => $description,
					'version' => $version,
					'cat' => $cat,
					'idclient' => $idclient,
					'author' => $author,
					'created' => $created,
					'lastmodified' => $lastmodified,
					'repository_id' => $repository_id,
					'source_id' => $source,
					'root_name' => $root_dir,
					'index_file' => $index_file,
					'is_install' => $is_online,
					'is_multi' => $is_multi
				));
                $return = '1607';
            }
            // Uninstall Self
            elseif ($is_online && $source == '') {
                $db->query($sql4);
                $rep->plug_execute($idplug, 'uninstall', '', 'meta');
                // Event
                fire_event('plug_uninstall_sql', array(
					'idplug' => $idplug,
					'name' => $name,
					'description' => $description,
					'version' => $version,
					'cat' => $cat,
					'repository_id' => $repository_id,
					'source_id' => $source,
					'root_name' => $root_dir,
					'index_file' => $index_file,
					'config' => $config,
					'is_install' => $is_online,
					'is_multi' => $is_multi
				));
                $return = '1607';
            }
            // Delete Self
            elseif ($idclient == 0 && $root_dir != '') {
                $rep->_remove($rep->_plugin_dir . $root_dir);
                // Event
                fire_event('plug_removed', array(
					'idplug' => $idplug,
					'name' => $name,
					'description' => $description,
					'version' => $version,
					'cat' => $cat,
					'idclient' => $idclient,
					'author' => $author,
					'created' => $created,
					'lastmodified' => $lastmodified,
					'repository_id' => $repository_id,
					'source_id' => $source,
					'root_name' => $root_dir,
					'index_file' => $index_file,
					'is_install' => $is_online,
					'is_multi' => $is_multi
				));
            }
            // Uninstall Parent
            if ($is_online2 && $is_online3 == 1 && $sql4 != '') {
                $db->query($sql4);
                $rep->plug_execute($source, 'uninstall', '', 'meta');
                // Event
                fire_event('plug_uninstall_sql', array(
					'idplug' => $idplug,
					'name' => $name,
					'description' => $description,
					'version' => $version,
					'cat' => $cat,
					'repository_id' => $repository_id,
					'source_id' => $source,
					'root_name' => $root_dir,
					'index_file' => $index_file,
					'config' => $config,
					'is_install' => $is_online,
					'is_multi' => $is_multi
				));
            }
			//Log
			$cms_log->info('user', 'plug_delete', array('idplug' => $idplug, 'name' => $name, 'version' => $version));
            // Event
            fire_event('plug_delete', array(
				'idplug' => $idplug,
				'name' => $name,
				'description' => $description,
				'version' => $version,
				'cat' => $cat,
				'idclient' => $idclient,
				'author' => $author,
				'created' => $created,
				'lastmodified' => $lastmodified,
				'repository_id' => $repository_id,
				'source_id' => $source,
				'root_name' => $root_dir,
				'index_file' => $index_file,
				'is_install' => $is_online,
				'is_multi' => $is_multi
			));
        } else $return = '1601';
    } else $return = '1613';
    return $return;
} 
function plug_download($idplug, $idclient, $_zip = false, $_name = false, $_ext = false) {
    global $rep, $cfg_cms, $sess, $cms_log;
    $plugin = $rep->plug_data($idplug, $idclient);
    // todo:zlib abfrage + popup
    if (is_array($plugin)) {
        // Event
        $zip = false;
        require_once("Archive/Tar.php");
        $xmlstring = $rep->plug_generate($plugin);
        $name = ($_name == '') ? $plugin['root_name'] : trim($_name);
        $zip  = ($cfg_cms['gzip_enabled'] == true && $_zip == true) ? true : false;
        if ($zip === true && ($_ext == 'tar' || $_ext == 'tgz')) $_ext = 'tgz';
        elseif ($zip === false && ($_ext == 'tar' || $_ext == 'tgz')) $_ext = 'tar';
        else $_ext = 'cmsplugin';
        $_mtype = ($zip === true) ? 'application/x-compressed' : 'application/x-tar';
        $file = $cfg_cms['path_base'].$cfg_cms['path_backend_rel'] . 'upload/' . 'out/' . $name . '.' . $_ext; 
        $download = $cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'] . 'upload/' . 'out/' . $name . '.' . $_ext;
        lib_write_file($cfg_cms['path_base'].$cfg_cms['path_backend_rel'] . 'plugins/' . $name . '/' . $name . '.cmsplug', $xmlstring);
        if (false == ($tar = new Archive_Tar($file, $zip))) return '1615';
        elseif ($tar == '-1' && $zip == true) return plug_download($idplug, $idclient, false, $_name, $_ext);
        $old_working_dir = getcwd();
        chdir($cfg_cms['path_base'].$cfg_cms['path_backend_rel'] . 'plugins/');
		$tar->setIgnoreRegexp("#CVS|\.svn#");
        $tar->create(trim($name));
        chdir($old_working_dir);
        ob_end_clean();
        header('Content-Type: ' . $_mtype);
        header('Content-Transfer-Encoding: binary');
        header('Content-Description: Download Data');
        header('Content-Length: ' . filesize($file));
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        //old function: header('Location:' . $download);
        readfile($file);
		//Log
		$cms_log->info('user', 'plug_download', array('idplug' => $idplug, 'name' => $name));
        //Event
		fire_event('plug_download', array(
			'idplug' => $idplug,
			'name' => $name,
			'idclient' => $idclient
		));
        lib_delete_file($file);
        exit;
    }
} 
function plug_upload($idclient = '0', $override = false) {
    global $rep, $cfg_cms, $sess, $s_upload, $db, $cms_db, $cms_log;
    if (is_array($s_upload) && $override) {
        $tmp_file = $s_upload;
        $returncode = $s_upload['returncode'];
        if ($sess->is_registered('s_upload')) $sess->unregister('s_upload');
    } else $tmp_file = lib_get_upload('plug_upload_file');
		//Es wurde keine Datei hochgeladen
    if (!is_array($tmp_file) || $tmp_file['error'] == '-1' || $tmp_file['error'] == '-2') {
			if ($tmp_file['code'] >= 1) {
					//if (lib_test_safemode()) return array('1801', false);
					if ($tmp_file['code'] == 1) return array('0708', false);
					if ($tmp_file['code'] == 2) return array('0709', false);
					if ($tmp_file['code'] == 3) return array('0710', false);
					if ($tmp_file['code'] == 4) return array('0711', false);
			} else return array('1604', false);
		}
    if (!($file_content = $rep->_file($tmp_file['copy']))) {
        lib_delete_file($tmp_file['copy']);
        if (lib_test_safemode()) return array('1801', false); else return array('1600', false);
    }
    require_once("Archive/Tar.php");
    if (false === ($tar = new Archive_Tar($tmp_file['copy']))) {
        $return =  '1615';
        if ($tar->_error_message != '') $error = $tar->_error_message;      
        return array($return, $error);
    }
    elseif ($tar == '-1') return array('1621', false);
    $tmp_plugin = $tar->listContent();
    if ($tmp_plugin[0]['filename'] == '' || $tmp_plugin[0]['typeflag'] != 5) return array('1604', false);
    $name = substr($tmp_plugin[0]['filename'], -1) == '/' ? substr($tmp_plugin[0]['filename'], 0, -1) : $tmp_plugin[0]['filename'];
    $tmp_cmsplug = $tar->extractInString($name. '/' . $name . '.cmsplug');
    //todo: 2remove
    if (empty($tmp_cmsplug)) $tmp_cmsplug = $tar->extractInString($name. '/' . $name . '.dediplug');
    list($idplug, $xml_array) = $rep->plug_import($tmp_cmsplug, $idclient, $override);
    if ($idplug == '-1' || $idplug == '-2') {
        $s_upload = $tmp_file;
        $s_upload['returncode']  = $idplug;
        $s_upload['plugversion'] = $xml_array['version'];
        $s_upload['plugname']    = $xml_array['name'];
        list($type,$rid,$uid)    = explode(":",$xml_array['repository_id']);
        $s_upload['plugrepid']   = $type.':'.$rid;
        $sess->register('s_upload');
        return array('1617', false);
    } elseif ($idplug == '-3') {
        $s_upload = $tmp_file;
        $s_upload['returncode']  = $idplug;
        $s_upload['plugversion'] = $xml_array['version'];
        $s_upload['plugname']    = $xml_array['name'];
        list($type,$rid,$uid)    = explode(":",$xml_array['repository_id']);
        $s_upload['plugrepid']   = $type.':'.$rid;
        $sess->register('s_upload');
        return array('1619', false);
    } elseif ($idplug == '-4') {
        lib_delete_file($tmp_file['copy']);
        return array('1603', false);
    } elseif (!$idplug || !is_array($xml_array)) {
        lib_delete_file($tmp_file['copy']);
        return array('1600', false);
    }
    if (!$tar->extract($cfg_cms['path_base'].$cfg_cms['path_backend_rel'] . 'plugins/')) {
        // check error
        if ($tar->_error_message != '') $error = $tar->_error_message;  
        // force delete the plugin
        plug_delete($idplug, $idclient);
        return array('1615', $error);
  	}
    // get the new Plugin data
  	$plugin = $rep->plug_data($idplug, $idclient);

	//Log
	$cms_log->info('user', 'plug_upload', array('idplug' => $plugin['idplug'], 'name' => $plugin['name'], 'version' => $plugin['version']));
    // Event
    fire_event('plug_upload', $plugin);
    if ($override && $returncode == '-1' || $returncode == '-2') {
        
        if((bool) $plugin['is_install']) {
          $rep->plug_execute($idplug, 'update', '', 'meta');
          if((bool) $rep->plug_execute($idplug, 'multi_client') && $idclient == '0') {
            global $client;
            $sql = "SELECT idclient from " . $cms_db['plug'] . " WHERE source_id='$idplug' AND is_install='1'";
            $db->query($sql);
            while ($db->next_record()) {
              $client = $db->f('idclient');
              $rep->plug_execute($idplug, 'update', 'client', 'meta');
              // Event
              fire_event('plug_update_client', $plugin);
            }
          }
        }
		//Log
		$cms_log->info('user', 'plug_update', array('idplug' => $plugin['idplug'], 'name' => $plugin['name'], 'version' => $plugin['version']));
        // Event
        fire_event('plug_update', $plugin);
    } elseif ($override && $returncode == '-3') {
        if((bool) $plugin['is_install']) { 
          $_recurse = array();
          global $client;
          if((bool) $rep->plug_execute($idplug, 'multi_client') && $idclient == '0') { 
            $sql = "SELECT idclient from " . $cms_db['plug'] . " WHERE source_id='$idplug' AND is_install='1'";
            $db->query($sql);
            while ($db->next_record()) {
              array_push($_recurse, $db->f('idclient'));
              // Event
              fire_event('plug_reinstall_client', $plugin);
            }
          }
          reset($_recurse);
          foreach($_recurse as $_k => $_v) {
            $client = $_v;
            $rep->plug_execute($idplug, 'uninstall', 'client', 'meta');
          }
          $rep->plug_execute($idplug, 'uninstall', '', 'meta');
          $rep->plug_execute($idplug, 'install', '', 'meta');
          foreach($_recurse as $_k => $_v) {
            $client = $_v;
            $rep->plug_execute($idplug, 'install', 'client', 'meta');
          }
        }
		//Log
		$cms_log->info('user', 'plug_reinstall', array('idplug' => $plugin['idplug'], 'name' => $plugin['name'], 'version' => $plugin['version']));
        // Event
        fire_event('plug_reinstall', $plugin);
    }
    
    lib_delete_file($tmp_file['copy']);
    return array('1612', false);
} 
// Repository
function plug_update($idplug, $repid, $plugname, $description, $plugversion, $plugcat, $sql_install, $sql_uninstall, $sql_update, $idclient) {
    global $cfg_cms, $db, $cms_db, $rep, $cms_log;
    plug_save($idplug, $plugname, $description, $plugversion, $plugcat, $idclient, $repid, $sql_install, $sql_uninstall, $sql_update);
    if ($idclient > 0 && $sql_update != '') {
        $error = $rep->bulk_sql($sql_update); 
        // Event
        fire_event('plug_repository_update_sql', array(
			'idplug' => $idplug,
			'name' => $plugname,
			'repository_id' => $repid,
			'update_sql' => $sql_update
		));
    } 
	//Log
	$cms_log->info('user', 'plug_repository_update', array('idplug' => $idplug, 'name' => $plugname, 'version' => $plugversion));
    // Event
    fire_event('plug_repository_update', array(
		'idplug' => $idplug,
		'name' => $plugname,
		'description' => $description,
		'version' => $plugversion,
		'cat' => $plugcat,
		'idclient' => $idclient,
		'repository_id' => $repid,
		'install_sql' => $sql_install,
		'uninstall_sql' => $sql_uninstall,
		'update_sql' => $sql_update
	));
    return '0405';
} 
function plug_install($repid, $plugname, $description, $plugversion, $plugcat, $sql_install, $sql_uninstall, $sql_update, $idclient, $root_name, $index_file) {
    global $cms_log;
	
	plug_save('', $plugname, $description, $plugversion, $plugcat, $idclient, $repid, $sql_install, $sql_uninstall, $sql_update, $root_name, $index_file);
	//Log
	$cms_log->info('user', 'plug_repository_install', array('name' => $plugname, 'version' => $plugversion));
    // Event
    fire_event('plug_repository_install', array(
		'name' => $plugname,
		'description' => $description,
		'version' => $plugversion,
		'cat' => $plugcat,
		'idclient' => $idclient,
		'repository_id' => $repid,
		'install_sql' => $sql_install,
		'uninstall_sql' => $sql_uninstall,
		'update_sql' => $sql_update,
		'root_name' => $root_name,
		'index_file' => $index_file
	));
    return '0406';
} 

function getClientNameByIdclient($idclient) {
	global $cms_db, $db;
	
	$sql = 'SELECT name FROM ' . $cms_db['clients'] . ' WHERE idclient=' . $idclient;
	$db->query($sql);
	$db->next_record();
	
	return $db->f('name');
}
?>
