<?php
if(! defined('CMS_CONFIGFILE_INCLUDED')){
	die('NO CONFIGFILE FOUND');
}

$prefix = (isset($prefix) == TRUE) ? $prefix : 'logs';

$cms_lang[$prefix.'_area_index'] = 'Administration &rsaquo; Logs aus Datenbank';
$cms_lang[$prefix.'_area_logfile_be'] = 'Administration &rsaquo; Backend-Logdatei';
$cms_lang[$prefix.'_area_logfile_fe'] = 'Administration &rsaquo; Frontend-Logdatei';

$cms_lang[$prefix.'_screen']	= 'Log-Ausgabe';

$cms_lang[$prefix.'_area']	= 'Ansicht&hellip;';
$cms_lang[$prefix.'_log_db'] = 'Datenbank-Log';
$cms_lang[$prefix.'_log_fe'] = 'Frontend-Logdatei';
$cms_lang[$prefix.'_log_be'] = 'Backend-Logdatei';

$cms_lang[$prefix.'_filter'] = $prefix.' filtern';
$cms_lang[$prefix.'_filter_submit'] = 'Anwenden';
$cms_lang[$prefix.'_filter_close'] = 'Abbrechen';
$cms_lang[$prefix.'_filter_show'] = 'Ansicht gefiltert nach';
$cms_lang[$prefix.'_filter_reset'] = 'Zur&uuml;cksetzen';

$cms_lang[$prefix.'_created'] = 'Datum';
$cms_lang[$prefix.'_period'] = 'Zeitraum';
$cms_lang[$prefix.'_period_from'] = 'Von';
$cms_lang[$prefix.'_period_to'] = 'Bis';
$cms_lang[$prefix.'_priority'] = 'Priorit&auml;t';
$cms_lang[$prefix.'_type'] = 'Typ';
$cms_lang[$prefix.'_message'] = 'Meldung';
$cms_lang[$prefix.'_author'] = 'Benutzer';
$cms_lang[$prefix.'_client'] = 'Projekt';
$cms_lang[$prefix.'_is_backend'] = 'Backend';
$cms_lang[$prefix.'_action'] = 'Aktionen';

$cms_lang[$prefix.'_is_backend_logs'] = 'Logs aus Backend';
$cms_lang[$prefix.'_is_backend_hide'] = 'Ausblenden (Nur Frontend)';
$cms_lang[$prefix.'_is_backend_show'] = 'Anzeigen (Nur Backend)';
$cms_lang[$prefix.'_is_backend_default'] = 'Standard (Frontend und Backend)';

$cms_lang[$prefix.'_file_path'] = 'Dateipfad';
$cms_lang[$prefix.'_file_size'] = 'Dateigr&ouml;&szlig;e';
$cms_lang[$prefix.'_file_lastmodified'] = 'Letzte &Auml;nderung';
$cms_lang[$prefix.'_file_content'] = 'Datei-Inhalt';

$cms_lang[$prefix.'_options'] = 'Anzeigeoptionen';
$cms_lang[$prefix.'_colorize'] = 'Zeilen einfärben';
$cms_lang[$prefix.'_limit'] = 'Anzahl der Zeilen';

$cms_lang[$prefix.'_noauthor'] = '<i>Unbekannt</i>';

$cms_lang[$prefix.'_page'] = 'Seite';
$cms_lang[$prefix.'_from'] = 'von';

$cms_lang[$prefix.'_collection_nodata'] = 'Keine Eintr&auml;ge vorhanden.';


// messages (errors, warnings, ok)
$cms_lang[$prefix.'_permission_denied'] = $cms_lang['gen_permission_denied'];
$cms_lang[$prefix.'_success'] = 'Die ausgew&auml;hlten Logs wurden erfolgreich gel&ouml;scht.';
$cms_lang[$prefix.'_error'] = 'Beim L&ouml;schen ist ein Fehler aufgetreten.';

$cms_lang[$prefix.'_js_delete_multi_confirm'] = 'M\u00f6chten Sie die ausgew\u00e4hlten Eintr\u00e4ge wirklich l\u00f6schen?';


/*
 * Logkeys with parameters (as array)
 * 
 * @example
 * message: mylog 
 * parameter: array("key1" => "Hello", "key2" => "World");
 * 
 * lang: $cms_lang[$prefix.'_messages']['mylog'] = 'We say: {key1} {key2}!';
 */

//$cms_lang[$prefix.'_messages']['mylog']	= 'We say: {key1} {key2}!';

// $type = login
//$cms_lang[$prefix.'_messages']['login_fail_input'] = 'Login mit "{username}" ist fehlgeschlagen';
$cms_lang[$prefix.'_messages']['login_success'] = 'Benutzer erfolgreich eingeloggt';
$cms_lang[$prefix.'_messages']['logout_success'] = 'Benutzer erfolgreich ausgeloggt';

// $type = content
$cms_lang[$prefix.'_messages']['con_cache_delete'] = 'Cache geleert';
$cms_lang[$prefix.'_messages']['con_cat_new'] = 'Ordner "{catname}" (idcat: {idcat}) angelegt';
$cms_lang[$prefix.'_messages']['con_cat_edit'] = 'Ordner "{catname}" (idcat: {idcat}) bearbeitet';
$cms_lang[$prefix.'_messages']['con_cat_delete'] = 'Ordner "{catname}" (idcat: {idcat}) gel&ouml;scht';
$cms_lang[$prefix.'_messages']['con_cat_online'] = 'Ordner "{catname}" (idcat: {idcat}) online geschaltet';
$cms_lang[$prefix.'_messages']['con_cat_offline'] = 'Ordner "{catname}" (idcat: {idcat}) offline geschaltet';
$cms_lang[$prefix.'_messages']['con_cat_lock'] = 'Ordner "{catname}" (idcat: {idcat}) gesperrt';
$cms_lang[$prefix.'_messages']['con_cat_unlock'] = 'Ordner "{catname}" (idcat: {idcat}) entsperrt';
$cms_lang[$prefix.'_messages']['con_side_new'] = 'Seite "{pagename}" (idside: {idside}) angelegt';
$cms_lang[$prefix.'_messages']['con_side_edit'] = 'Seite "{pagename}" (idside: {idside}) bearbeitet';
$cms_lang[$prefix.'_messages']['con_side_start'] = 'Seite "{pagename}" (idcatside: {idcatside}) als Startseite festgelegt';
$cms_lang[$prefix.'_messages']['con_side_delete'] = 'Seite "{pagename}" (idside: {idside}) im Ordner "{catname}" (idcat: {idcat}) gel&ouml;scht';
$cms_lang[$prefix.'_messages']['con_side_online'] = 'Seite "{pagename}" (idside: {idside}) online geschaltet';
$cms_lang[$prefix.'_messages']['con_side_offline'] = 'Seite "{pagename}" (idside: {idside}) offline geschaltet';
$cms_lang[$prefix.'_messages']['con_side_lock'] = 'Seite "{pagename}" (idside: {idside}) gesperrt';
$cms_lang[$prefix.'_messages']['con_side_unlock'] = 'Seite "{pagename}" (idside: {idside}) entsperrt';

// $type = filemanager
$cms_lang[$prefix.'_messages']['upl_createdir'] = 'Verzeichnis "{newdirname}" (iddirectory: {iddirectory}) angelegt';
$cms_lang[$prefix.'_messages']['upl_editdir'] = 'Verzeichnis "{newdirname}" (iddirectory: {iddirectory}) bearbeitet';
$cms_lang[$prefix.'_messages']['upl_deletedir'] = 'Verzeichnis (iddirectory: {iddirectory}) gel&ouml;scht';
$cms_lang[$prefix.'_messages']['upl_uploadfile'] = 'Datei hochgeladen';
$cms_lang[$prefix.'_messages']['upl_editfile'] = 'Datei idupl: {idupl} bearbeitet';
$cms_lang[$prefix.'_messages']['upl_deletefile'] = 'Datei idupl: {idupl} gel&ouml;scht';
$cms_lang[$prefix.'_messages']['upl_copyfile'] = 'Datei idupl: {idupl} kopiert';
$cms_lang[$prefix.'_messages']['upl_movefile'] = 'Datei idupl: {idupl} verschoben';

// $type = fm
$cms_lang[$prefix.'_messages']['fm_enable_mls'] = 'Mehrsprachige Verwaltung erfolgreich aktiviert';
$cms_lang[$prefix.'_messages']['fm_disable_mls'] = 'Mehrsprachige Verwaltung erfolgreich deaktiviert';

// copy fm for css and js
foreach($cms_lang[$prefix.'_messages'] as $key => $value)
{
	if(strpos($key, 'fm_') === 0)
	{
		$key = str_replace('fm_', 'css_', $key);
		$cms_lang[$prefix.'_messages'][$key] = $value;
		
		$key = str_replace('css_', 'js_', $key);
		$cms_lang[$prefix.'_messages'][$key] = $value;
	}
}

// $type = layout
$cms_lang[$prefix.'_messages']['lay_new'] = 'Layout "{name}" (idlay: {idlay}) angelegt';
$cms_lang[$prefix.'_messages']['lay_edit'] = 'Layout "{name}" (idlay: {idlay}) bearbeitet';
$cms_lang[$prefix.'_messages']['lay_import'] = 'Layout "{name}" (idlay: {idlay}) vom Pool importiert';
$cms_lang[$prefix.'_messages']['lay_export'] = 'Layout "{name}" (idlay: {idlay}) in den Pool exportiert';
$cms_lang[$prefix.'_messages']['lay_delete'] = 'Layout "{name}" (idlay: {idlay}) gel&ouml;scht';

// $type = stylesheet
$cms_lang[$prefix.'_messages']['css_editfile'] = 'Stylesheet "{filename}" (idcssfile: {idcssfile}) bearbeitet';

// $type = javascript
$cms_lang[$prefix.'_messages']['js_uploadfile'] = 'JavaScript-Datei idjsfile: {idjsfile} hochgeladen';
$cms_lang[$prefix.'_messages']['js_deletefile'] = 'JavaScript-Datei idjsfile: {idjsfile} gel&ouml;scht';
$cms_lang[$prefix.'_messages']['js_import'] = 'JavaScript-Datei idjsfile: {idjsfile} importiert';
$cms_lang[$prefix.'_messages']['js_export'] = 'JavaScript-Datei idjsfile: {idjsfile} exportiert';
$cms_lang[$prefix.'_messages']['js_editfile'] = 'JavaScript-Datei "{filename}" (idjsfile: {idjsfile}) bearbeitet';

// $type = module
$cms_lang[$prefix.'_messages']['mod_new'] = 'Modul "{name} {version}" [Alternativ: "{verbose}"] (idmod: {idmod}) angelegt';
$cms_lang[$prefix.'_messages']['mod_edit'] = 'Modul "{name} {version}" [Alternativ: "{verbose}"] (idmod: {idmod}) bearbeitet';
$cms_lang[$prefix.'_messages']['mod_delete'] = 'Modul "{name} {version}" [Alternativ: "{verbose}"] (idmod: {idmod}) gel&ouml;scht';
$cms_lang[$prefix.'_messages']['mod_import'] = 'Modul "{name} {version}" [Alternativ: "{verbose}"] (idmod: {idmod}) importiert';
$cms_lang[$prefix.'_messages']['mod_export'] = 'Modul "{name} {version}" [Alternativ: "{verbose}"] (idmod: {idmod}) exportiert';
$cms_lang[$prefix.'_messages']['mod_upload'] = 'Modul "{name} {version}" (idmod: {idmod}) hochgeladen';
$cms_lang[$prefix.'_messages']['mod_download'] = 'Modul "{name} {version}" (idmod: {idmod}) heruntergeladen';
$cms_lang[$prefix.'_messages']['mod_local_update'] = 'Modul "{name} {version}" (idmod: {idmod}) aus dem Pool (lokal) aktualisiert';
$cms_lang[$prefix.'_messages']['mod_repository_update'] = 'Modul "{name} {version}" (idmod: {idmod}) aus dem Repository aktualisiert';
$cms_lang[$prefix.'_messages']['mod_repository_import'] = 'Modul "{name} {version}" (idmod: {idmod}) aus dem Repository importiert';

// $type = template
$cms_lang[$prefix.'_messages']['tpl_new'] = 'Template "{name}" (idtpl: {idtpl}) angelegt';
$cms_lang[$prefix.'_messages']['tpl_edit'] = 'Template "{name}" (idtpl: {idtpl}) bearbeitet';
$cms_lang[$prefix.'_messages']['tpl_delete'] = 'Template "{name}" (idtpl: {idtpl}) gel&ouml;scht';

// $type = user
$cms_lang[$prefix.'_messages']['user_create'] = 'Benutzer "{username}" (iduser: {iduser}) angelegt';
$cms_lang[$prefix.'_messages']['user_update'] = 'Benutzer "{username}" (iduser: {iduser}) bearbeitet';
$cms_lang[$prefix.'_messages']['user_activate'] = 'Benutzer "{username}" (iduser: {iduser}) aktiviert';
$cms_lang[$prefix.'_messages']['user_deactivate'] = 'Benutzer "{username}" (iduser: {iduser}) deaktiviert';
$cms_lang[$prefix.'_messages']['user_delete'] = 'Benutzer "{username}" (iduser: {iduser}) gel&ouml;scht';

// $type = group
$cms_lang[$prefix.'_messages']['group_edit'] = 'Gruppe "{name}" (idgroup: {idgroup}) bearbeitet';
$cms_lang[$prefix.'_messages']['group_delete'] = 'Gruppe "{name}" (idgroup: {idgroup}) gel&ouml;scht';
$cms_lang[$prefix.'_messages']['group_activate'] = 'Gruppe "{name}" (idgroup: {idgroup}) aktiviert';
$cms_lang[$prefix.'_messages']['group_deactivate'] = 'Gruppe "{name}" (idgroup: {idgroup}) deaktiviert';
$cms_lang[$prefix.'_messages']['group_activate_lang'] = 'Gruppe "{groupname}" (idgroup: {idgroup}) für Sprache "{langname}" (idlang: {idlang}) aktiviert';
$cms_lang[$prefix.'_messages']['group_deactivate_lang'] = 'Gruppe "{groupname}" (idgroup: {idgroup}) für Sprache "{langname}" (idlang: {idlang}) deaktiviert';
$cms_lang[$prefix.'_messages']['group_save_perms'] = 'Rechte der Gruppe "{groupname}" (idgroup: {idgroup}) für Sprache "{langname}" (idlang: {idlang}) bearbeitet';

// $type = client
$cms_lang[$prefix.'_messages']['clients_new_client'] = 'Projekt "{name}" (idclient: {idclient}) angelegt';
$cms_lang[$prefix.'_messages']['clients_rename_client'] = 'Projekt "{name}" (idclient: {idclient}) bearbeitet';
$cms_lang[$prefix.'_messages']['clients_delete_client'] = 'Projekt "{name}" (idclient: {idclient}) gel&ouml;scht';
$cms_lang[$prefix.'_messages']['clients_new_lang'] = 'Sprache "{name}" (idlang: {idlang}) im Projekt "{clientname}" (idclient: {idclient}) angelegt';
$cms_lang[$prefix.'_messages']['clients_rename_lang'] = 'Sprache "{name}" (idlang: {idlang}) im Projekt "{clientname}" (idclient: {idclient}) bearbeitet';
$cms_lang[$prefix.'_messages']['clients_delete_lang'] = 'Sprache "{name}" (idlang: {idlang}) im Projekt "{clientname}" (idclient: {idclient}) gel&ouml;scht';
$cms_lang[$prefix.'_messages']['clients_make_start_lang'] = 'Sprache "{name}" (idlang: {idlang}) im Projekt "{clientname}" (idclient: {idclient}) als globale Startsprache gesetzt';


// $type = plugin
$cms_lang[$prefix.'_messages']['plug_new'] = 'Plugin "{name} {version}" (idplug: {idplug}) angelegt';
$cms_lang[$prefix.'_messages']['plug_edit'] = 'Plugin "{name} {version}" (idplug: {idplug}) bearbeitet';
$cms_lang[$prefix.'_messages']['plug_delete'] = 'Plugin "{name} {version}" (idplug: {idplug}) gel&ouml;scht';
$cms_lang[$prefix.'_messages']['plug_import'] = 'Plugin "{name} {version}" (idplug: {idplug}) importiert';
$cms_lang[$prefix.'_messages']['plug_export'] = 'Plugin "{name} {version}" (idplug: {idplug}) exportiert';
$cms_lang[$prefix.'_messages']['plug_upload'] = 'Plugin "{name} {version}" (idplug: {idplug}) hochgeladen';
$cms_lang[$prefix.'_messages']['plug_download'] = 'Plugin "{name}" (idplug: {idplug}) heruntergeladen';
$cms_lang[$prefix.'_messages']['plug_install_client'] = 'Plugin "{name} {version}" (idplug: {idplug}) f&uuml;r das Projekt "{clientname}" (idclient: {idclient}) installiert';
$cms_lang[$prefix.'_messages']['plug_uninstall_client'] = 'Plugin "{name} {version}" (idplug: {idplug}) f&uuml;r das Projekt "{clientname}" (idclient: {idclient}) deinstalliert';
$cms_lang[$prefix.'_messages']['plug_update'] = 'Plugin "{name} {version}" (idplug: {idplug}) aktualisiert';
$cms_lang[$prefix.'_messages']['plug_reinstall'] = 'Plugin "{name} {version}" (idplug: {idplug}) re-installiert';
$cms_lang[$prefix.'_messages']['plug_repository_update'] = 'Plugin "{name} {version}" (idplug: {idplug}) aus dem Repository aktualisiert';
$cms_lang[$prefix.'_messages']['plug_repository_install'] = 'Plugin "{name}" (idplug: {idplug}) aus dem Repository importiert';


?>
