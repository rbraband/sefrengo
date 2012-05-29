<?php
if(! defined('CMS_CONFIGFILE_INCLUDED'))
{
	die('NO CONFIGFILE FOUND');
}

$prefix = (isset($prefix) == TRUE) ? $prefix : 'logs';

$cms_lang[$prefix.'_area_index'] = 'Administration &rsaquo; Logs aus Datenbank';
$cms_lang[$prefix.'_area_logfile_be'] = 'Administration &rsaquo; Backend-Logdatei';
$cms_lang[$prefix.'_area_logfile_fe'] = 'Administration &rsaquo; Frontend-Logdatei';

$cms_lang[$prefix.'_area']	= 'Ansicht&hellip;';
$cms_lang[$prefix.'_log_db'] = 'Datenbank-Log';
$cms_lang[$prefix.'_log_fe'] = 'Frontend-Logdatei';
$cms_lang[$prefix.'_log_be'] = 'Backend-Logdatei';

$cms_lang[$prefix.'_filter_show'] = 'Ansicht gefiltert nach';
$cms_lang[$prefix.'_filter_reset'] = 'Zurücksetzen';

$cms_lang[$prefix.'_searchterm'] = 'Suchbegriff';
$cms_lang[$prefix.'_created'] = 'Datum';
$cms_lang[$prefix.'_period'] = 'Zeitraum';
$cms_lang[$prefix.'_period_from'] = 'Von';
$cms_lang[$prefix.'_period_to'] = 'Bis';
$cms_lang[$prefix.'_priority'] = 'Priorität';
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
$cms_lang[$prefix.'_file_size'] = 'Dateigröße';
$cms_lang[$prefix.'_file_lastmodified'] = 'Letzte Änderung';
$cms_lang[$prefix.'_file_content'] = 'Datei-Inhalt';
$cms_lang[$prefix.'_file_delete'] = 'Logdatei löschen';
$cms_lang[$prefix.'_file_delete_question'] = 'Soll die Logdatei unwiederruflich gelöscht werden?';

$cms_lang[$prefix.'_noauthor'] = '<i>Unbekannt</i>';

$cms_lang[$prefix.'_page'] = 'Seite';
$cms_lang[$prefix.'_from'] = 'von';

$cms_lang[$prefix.'_collection_nodata'] = 'Keine Einträge vorhanden.';

$cms_lang[$prefix.'_delete_multiple'] = 'Ausgewählte Log-Einträge löschen';

// messages (errors, warnings, ok)
$cms_lang[$prefix.'_permission_denied'] = $cms_lang['gen_permission_denied'];

$cms_lang[$prefix.'_log_is_not_loaded'] = 'Der Log-Eintrag konnte nicht gefunden werden!';
$cms_lang[$prefix.'_delete_log_success'] = 'Der Log-Eintrag wurde erfolgreich gelöscht!';

$cms_lang[$prefix.'_delete_file_not_checked'] = 'Die Datei wurde nicht gelöscht! Aktivieren Sie das Kontrollkästchen und versuchen Sie es erneut.';
$cms_lang[$prefix.'_delete_logfile_success'] = 'Die Logdatei wurde erfolgreich gelöscht!';
$cms_lang[$prefix.'_delete_logfile_failed'] = 'Die Logdatei konnte nicht gelöscht werden! Möglicherweise sind die Rechte falsch gesetzt oder die Datei existiert nicht.';
$cms_lang[$prefix.'_overleap_logfile_exists'] = 'Die ursprüngliche Logdatei war zu groß und wurde daher umbenannt. Sie sollten die alte Datei zeitnah löschen. <a href="{overleap_url}">Zur alten Logdatei wechseln &rsaquo;</a>';
$cms_lang[$prefix.'_return_to_logfile'] = 'Löschen Sie diese alte Logdatei zeitnah um Speicherplatz für weitere Logs zu schaffen. <a href="{area_url}">Jetzt nicht &ndash; zur aktuellen Logdatei wechseln &rsaquo;</a>';

$cms_lang[$prefix.'_all_actions_failed'] = 'Aktion vollständig fehlgeschlagen! Es sind Fehler aufgetreten.';
$cms_lang[$prefix.'_some_actions_failed'] = 'Aktion teilweise fehlgeschlagen! Es sind Fehler aufgetreten.';
$cms_lang[$prefix.'_action_successful'] = 'Aktion erfolgreich ausgeführt! Es sind keine Fehler aufgetreten.';

$cms_lang[$prefix.'_js_delete_multi_confirm'] = 'M\u00f6chten Sie die ausgew\u00e4hlten Eintr\u00e4ge wirklich l\u00f6schen?';


/*
 * Logkeys with parameters (as array)
 * 
 * @example
 * message: mylog 
 * parameter: array("key1" => "Hello", "key2" => "World");
 * 
 * lang: $cms_lang[$prefix.'_messages_mylog'] = 'We say: {key1} {key2}!';
 */

//$cms_lang[$prefix.'_messages_mylog']	= 'We say: {key1} {key2}!';

// $type = login
//$cms_lang[$prefix.'_messages_login_fail_input'] = 'Login fehlgeschlagen';
$cms_lang[$prefix.'_messages_login_success'] = 'Benutzer erfolgreich eingeloggt';
$cms_lang[$prefix.'_messages_logout_success'] = 'Benutzer erfolgreich ausgeloggt';

// $type = content
$cms_lang[$prefix.'_messages_con_cache_delete'] = 'Cache geleert';
$cms_lang[$prefix.'_messages_con_cat_new'] = 'Ordner "{catname}" (idcat:{idcat}) angelegt';
$cms_lang[$prefix.'_messages_con_cat_edit'] = 'Ordner "{catname}" (idcat:{idcat}) bearbeitet';
$cms_lang[$prefix.'_messages_con_cat_delete'] = 'Ordner "{catname}" (idcat:{idcat}) gelöscht';
$cms_lang[$prefix.'_messages_con_cat_online'] = 'Ordner "{catname}" (idcat:{idcat}) online geschaltet';
$cms_lang[$prefix.'_messages_con_cat_offline'] = 'Ordner "{catname}" (idcat:{idcat}) offline geschaltet';
$cms_lang[$prefix.'_messages_con_cat_lock'] = 'Ordner "{catname}" (idcat:{idcat}) gesperrt';
$cms_lang[$prefix.'_messages_con_cat_unlock'] = 'Ordner "{catname}" (idcat:{idcat}) entsperrt';
$cms_lang[$prefix.'_messages_con_side_new'] = 'Seite "{pagename}" (idside:{idside}) angelegt';
$cms_lang[$prefix.'_messages_con_side_edit'] = 'Seite "{pagename}" (idside:{idside}) bearbeitet';
$cms_lang[$prefix.'_messages_con_side_start'] = 'Seite "{pagename}" (idcatside: {idcatside}) als Startseite festgelegt';
$cms_lang[$prefix.'_messages_con_side_delete'] = 'Seite "{pagename}" (idside:{idside}) im Ordner "{catname}" (idcat:{idcat}) gelöscht';
$cms_lang[$prefix.'_messages_con_side_online'] = 'Seite "{pagename}" (idside:{idside}) online geschaltet';
$cms_lang[$prefix.'_messages_con_side_offline'] = 'Seite "{pagename}" (idside:{idside}) offline geschaltet';
$cms_lang[$prefix.'_messages_con_side_lock'] = 'Seite "{pagename}" (idside:{idside}) gesperrt';
$cms_lang[$prefix.'_messages_con_side_unlock'] = 'Seite "{pagename}" (idside:{idside}) entsperrt';

// $type = fm
$cms_lang[$prefix.'_messages_fm_enable_mls'] = 'Mehrsprachige Verwaltung erfolgreich aktiviert';
$cms_lang[$prefix.'_messages_fm_disable_mls'] = 'Mehrsprachige Verwaltung erfolgreich deaktiviert';

$cms_lang[$prefix.'_messages_fm_permission_denied'] = 'Nicht genügend Rechte um die Aktion auszuführen!';
$cms_lang[$prefix.'_messages_fm_more_info'] = 'Weitere Informationen'; 
$cms_lang[$prefix.'_messages_fm_no_information_found'] = 'Keine weiteren Informationen (mehr) vorhanden.';
$cms_lang[$prefix.'_messages_fm_no_type_found'] = 'Unbekanntes Format zur Aufbereitung der Daten vorhanden.';

$cms_lang[$prefix.'_messages_fm_missing_name'] = 'Kein Verzeichnisname vorhanden!';
$cms_lang[$prefix.'_messages_fm_forbidden_directory_name'] = 'Die Verzeichnisname "{name}" (iddirectory:{id}) ist unzulässig!';
$cms_lang[$prefix.'_messages_fm_invalid_name_correction_failed'] = 'Der Verzeichnisname "{name}" (iddirectory:{id}) ist ungültig und konnte nicht korrigiert werden!';
$cms_lang[$prefix.'_messages_fm_directory_exists_in_destination'] = 'Es existiert bereits ein Verzeichnis mit diesem Namen!';
$cms_lang[$prefix.'_messages_fm_save_directory_to_db_failed'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) konnte nicht gespeichert werden!';
$cms_lang[$prefix.'_messages_fm_directory_is_not_loaded'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) konnte nicht gefunden werden!';
$cms_lang[$prefix.'_messages_fm_save_sub_directories_to_db_failed'] = 'Die Pfade für die Unterverzeichnisse konnten nicht umbenannt werden!';
$cms_lang[$prefix.'_messages_fm_missing_destination_parent_directory'] = 'Kein oder ungültiges Zielverzeichnis angegeben!';
$cms_lang[$prefix.'_messages_fm_destination_is_a_child_of_source'] = 'Das Zielverzeichnis darf kein Unterverzeichnis des Quellverzeichnisses sein!';
$cms_lang[$prefix.'_messages_fm_create_directory_failed'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) konnte nicht erstellt werden!';
$cms_lang[$prefix.'_messages_fm_rename_directory_failed'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) konnte nicht umbenannt werden!';
$cms_lang[$prefix.'_messages_fm_copy_directory_failed'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) konnte nicht kopiert werden!';
$cms_lang[$prefix.'_messages_fm_move_directory_failed'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) konnte nicht verschoben werden!';
$cms_lang[$prefix.'_messages_fm_delete_directory_failed'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) konnte nicht gelöscht werden!';
$cms_lang[$prefix.'_messages_fm_delete_directory_from_db_failed'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) konnte nicht gelöscht werden!';
$cms_lang[$prefix.'_messages_fm_save_directory_success'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) wurde erfolgreich gespeichert!';
$cms_lang[$prefix.'_messages_fm_update_directory_success'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) wurde erfolgreich aktualisiert!';
$cms_lang[$prefix.'_messages_fm_copy_directory_success'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) wurde erfolgreich kopiert!';
$cms_lang[$prefix.'_messages_fm_move_directory_success'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) wurde erfolgreich verschoben!';
$cms_lang[$prefix.'_messages_fm_delete_directory_success'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) wurde erfolgreich gelöscht!';
$cms_lang[$prefix.'_messages_fm_download_directory_success'] = 'Das Verzeichnis "{path}" (iddirectory:{id}) wurde erfolgreich heruntergeladen!';

$cms_lang[$prefix.'_messages_fm_missing_upload'] = 'Kein Upload vorhanden!';
$cms_lang[$prefix.'_messages_fm_move_uploaded_file_failed'] = 'Der Upload wurde nicht abgelegt!';
$cms_lang[$prefix.'_messages_fm_forbidden_file_extension'] = 'Die Dateinamenserweiterung "{path}" (idupl:{id}) ist unzulässig!';
$cms_lang[$prefix.'_messages_fm_file_exists_in_destination'] = 'Es existiert bereits eine Datei mit diesem Namen "{filename}" im Verzeichnis "{path}"!';
$cms_lang[$prefix.'_messages_fm_missing_iddirectory'] = 'Kein Verzeichnis zum Speichern angeben!';
$cms_lang[$prefix.'_messages_fm_missing_filename'] = 'Kein Dateiname vorhanden!';
$cms_lang[$prefix.'_messages_fm_invalid_filename_correction_failed'] = 'Der Dateiname "{path}" (idupl:{id}) ist ungültig und konnte nicht korrigiert werden!';
$cms_lang[$prefix.'_messages_fm_write_content_to_file_failed'] = 'Der Inhalt konnte nicht in der Datei "{path}" (idupl:{id}) gespeichert werden!';
$cms_lang[$prefix.'_messages_fm_save_file_to_db_failed'] = 'Die Datei "{path}" (idupl:{id}) konnte nicht gespeichert werden!';
$cms_lang[$prefix.'_messages_fm_file_is_not_loaded'] = 'Die Datei "{path}" (idupl:{id}) konnte nicht gefunden werden!';
$cms_lang[$prefix.'_messages_fm_copy_file_failed'] = 'Die Datei "{path}" (idupl:{id}) konnte nicht kopiert werden!';
$cms_lang[$prefix.'_messages_fm_equal_directories'] = 'Das Quell- und Zielverzeichnis dürfen nicht identisch sein!';
$cms_lang[$prefix.'_messages_fm_move_file_failed'] = 'Die Datei "{path}" (idupl:{id}) konnte nicht verschoben werden!';
$cms_lang[$prefix.'_messages_fm_delete_file_failed'] = 'Die Datei "{path}" (idupl:{id}) konnte nicht gelöscht werden!';
$cms_lang[$prefix.'_messages_fm_delete_file_from_db_failed'] = 'Die Datei "{path}" (idupl:{id}) konnte nicht gelöscht werden!';
$cms_lang[$prefix.'_messages_fm_save_file_success'] = 'Die Datei "{path}" (idupl:{id}) wurde erfolgreich gespeichert!';
$cms_lang[$prefix.'_messages_fm_update_file_success'] = 'Die Datei "{path}" (idupl:{id}) wurde erfolgreich aktualisiert!';
$cms_lang[$prefix.'_messages_fm_copy_file_success'] = 'Die Datei "{path}" (idupl:{id}) wurde erfolgreich kopiert!';
$cms_lang[$prefix.'_messages_fm_move_file_success'] = 'Die Datei "{path}" (idupl:{id}) wurde erfolgreich verschoben!';
$cms_lang[$prefix.'_messages_fm_delete_file_success'] = 'Die Datei "{path}" (idupl:{id}) wurde erfolgreich gelöscht!';
$cms_lang[$prefix.'_messages_fm_download_file_success'] = 'Die Datei "{path}" (idupl:{id}) wurde erfolgreich heruntergeladen!';
$cms_lang[$prefix.'_messages_fm_upload_file_success'] = 'Die Datei "{path}" (idupl:{id}) wurde erfolgreich hochgeladen!';
$cms_lang[$prefix.'_messages_fm_upload_file_failed'] = 'Der Upload ist fehlgeschlagen!';
$cms_lang[$prefix.'_messages_fm_upload_successful'] = 'Der Upload war erfolgreich!';
$cms_lang[$prefix.'_messages_fm_some_uploads_failed'] = 'Der Upload ist teilweise fehlgeschlagen! Es sind Fehler aufgetreten.';
$cms_lang[$prefix.'_messages_fm_all_uploads_failed'] = 'Der Upload ist vollständig fehlgeschlagen! Es sind Fehler aufgetreten.';
$cms_lang[$prefix.'_messages_fm_upload_uncompress_file_success'] = 'Datei erfolgreich hochgeladen und entpackt!';
$cms_lang[$prefix.'_messages_fm_upload_uncompress_file_failed'] = 'Datei erfolgreich hochgeladen, aber Fehler beim Entpacken der Datei!';
$cms_lang[$prefix.'_messages_fm_generate_thumbnail_failed'] = 'Die Vorschaubilder konnten nicht generiert werden!';
$cms_lang[$prefix.'_messages_fm_copy_thumbnail_failed'] = 'Die Vorschaubilder konnten nicht kopiert werden!';
$cms_lang[$prefix.'_messages_fm_move_thumbnail_failed'] = 'Die Vorschaubilder konnten nicht verschoben werden!';
$cms_lang[$prefix.'_messages_fm_delete_thumbnail_failed'] = 'Die Vorschaubilder konnten nicht gelöscht werden!';

$cms_lang[$prefix.'_messages_fm_copy_metadata_success'] = 'Die Metadaten wurden erfolgreich übernommen!';
$cms_lang[$prefix.'_messages_fm_copy_metadata_failed'] = 'Die Metadaten konnten nicht übernommen werden!';

$cms_lang[$prefix.'_messages_fm_add_rights_failed'] = 'Die Rechte konnten nicht angelegt werden!';
$cms_lang[$prefix.'_messages_fm_copy_rights_failed'] = 'Die Rechte konnten nicht übernommen werden!';
$cms_lang[$prefix.'_messages_fm_delete_rights_failed'] = 'Die Rechte konnten nicht gelöscht werden!';

$cms_lang[$prefix.'_messages_fm_all_actions_failed'] = 'Aktion vollständig fehlgeschlagen! Es sind Fehler aufgetreten.';
$cms_lang[$prefix.'_messages_fm_some_actions_failed'] = 'Aktion teilweise fehlgeschlagen! Es sind Fehler aufgetreten.';
$cms_lang[$prefix.'_messages_fm_action_successful'] = 'Aktion erfolgreich ausgeführt! Es sind keine Fehler aufgetreten.';

// copy fm for css and js
foreach($cms_lang as $key => $value)
{
	if(strpos($key, $prefix.'_messages_fm_') === 0)
	{
		$key = str_replace('fm_', 'css_', $key);
		$cms_lang[$key] = $value;
		
		$key = str_replace('css_', 'js_', $key);
		$cms_lang[$key] = $value;
	}
}

// $type = layout
$cms_lang[$prefix.'_messages_lay_new'] = 'Layout "{name}" (idlay:{idlay}) angelegt';
$cms_lang[$prefix.'_messages_lay_edit'] = 'Layout "{name}" (idlay:{idlay}) bearbeitet';
$cms_lang[$prefix.'_messages_lay_import'] = 'Layout "{name}" (idlay:{idlay}) vom Pool importiert';
$cms_lang[$prefix.'_messages_lay_export'] = 'Layout "{name}" (idlay:{idlay}) in den Pool exportiert';
$cms_lang[$prefix.'_messages_lay_delete'] = 'Layout "{name}" (idlay:{idlay}) gelöscht';

// $type = module
$cms_lang[$prefix.'_messages_mod_new'] = 'Modul "{name} {version}" [Alternativ: "{verbose}"] (idmod:{idmod}) angelegt';
$cms_lang[$prefix.'_messages_mod_edit'] = 'Modul "{name} {version}" [Alternativ: "{verbose}"] (idmod:{idmod}) bearbeitet';
$cms_lang[$prefix.'_messages_mod_delete'] = 'Modul "{name} {version}" [Alternativ: "{verbose}"] (idmod:{idmod}) gelöscht';
$cms_lang[$prefix.'_messages_mod_import'] = 'Modul "{name} {version}" [Alternativ: "{verbose}"] (idmod:{idmod}) importiert';
$cms_lang[$prefix.'_messages_mod_export'] = 'Modul "{name} {version}" [Alternativ: "{verbose}"] (idmod:{idmod}) exportiert';
$cms_lang[$prefix.'_messages_mod_upload'] = 'Modul "{name} {version}" (idmod:{idmod}) hochgeladen';
$cms_lang[$prefix.'_messages_mod_download'] = 'Modul "{name} {version}" (idmod:{idmod}) heruntergeladen';
$cms_lang[$prefix.'_messages_mod_local_update'] = 'Modul "{name} {version}" (idmod:{idmod}) aus dem Pool (lokal) aktualisiert';
$cms_lang[$prefix.'_messages_mod_repository_update'] = 'Modul "{name} {version}" (idmod:{idmod}) aus dem Repository aktualisiert';
$cms_lang[$prefix.'_messages_mod_repository_import'] = 'Modul "{name} {version}" (idmod:{idmod}) aus dem Repository importiert';

// $type = template
$cms_lang[$prefix.'_messages_tpl_new'] = 'Template "{name}" (idtpl:{idtpl}) angelegt';
$cms_lang[$prefix.'_messages_tpl_edit'] = 'Template "{name}" (idtpl:{idtpl}) bearbeitet';
$cms_lang[$prefix.'_messages_tpl_delete'] = 'Template "{name}" (idtpl:{idtpl}) gelöscht';

// $type = user
$cms_lang[$prefix.'_messages_user_create'] = 'Benutzer "{username}" (iduser:{iduser}) angelegt';
$cms_lang[$prefix.'_messages_user_update'] = 'Benutzer "{username}" (iduser:{iduser}) bearbeitet';
$cms_lang[$prefix.'_messages_user_activate'] = 'Benutzer "{username}" (iduser:{iduser}) aktiviert';
$cms_lang[$prefix.'_messages_user_deactivate'] = 'Benutzer "{username}" (iduser:{iduser}) deaktiviert';
$cms_lang[$prefix.'_messages_user_delete'] = 'Benutzer "{username}" (iduser:{iduser}) gelöscht';

// $type = group
$cms_lang[$prefix.'_messages_group_edit'] = 'Gruppe "{name}" (idgroup:{idgroup}) bearbeitet';
$cms_lang[$prefix.'_messages_group_delete'] = 'Gruppe "{name}" (idgroup:{idgroup}) gelöscht';
$cms_lang[$prefix.'_messages_group_activate'] = 'Gruppe "{name}" (idgroup:{idgroup}) aktiviert';
$cms_lang[$prefix.'_messages_group_deactivate'] = 'Gruppe "{name}" (idgroup:{idgroup}) deaktiviert';
$cms_lang[$prefix.'_messages_group_activate_lang'] = 'Gruppe "{groupname}" (idgroup:{idgroup}) für Sprache "{langname}" (idlang:{idlang}) aktiviert';
$cms_lang[$prefix.'_messages_group_deactivate_lang'] = 'Gruppe "{groupname}" (idgroup:{idgroup}) für Sprache "{langname}" (idlang:{idlang}) deaktiviert';
$cms_lang[$prefix.'_messages_group_save_perms'] = 'Rechte der Gruppe "{groupname}" (idgroup:{idgroup}) für Sprache "{langname}" (idlang:{idlang}) bearbeitet';

// $type = client
$cms_lang[$prefix.'_messages_clients_new_client'] = 'Projekt "{name}" (idclient:{idclient}) angelegt';
$cms_lang[$prefix.'_messages_clients_rename_client'] = 'Projekt "{name}" (idclient:{idclient}) bearbeitet';
$cms_lang[$prefix.'_messages_clients_delete_client'] = 'Projekt "{name}" (idclient:{idclient}) gelöscht';
$cms_lang[$prefix.'_messages_clients_new_lang'] = 'Sprache "{name}" (idlang:{idlang}) im Projekt "{clientname}" (idclient:{idclient}) angelegt';
$cms_lang[$prefix.'_messages_clients_rename_lang'] = 'Sprache "{name}" (idlang:{idlang}) im Projekt "{clientname}" (idclient:{idclient}) bearbeitet';
$cms_lang[$prefix.'_messages_clients_delete_lang'] = 'Sprache "{name}" (idlang:{idlang}) im Projekt "{clientname}" (idclient:{idclient}) gelöscht';
$cms_lang[$prefix.'_messages_clients_make_start_lang'] = 'Sprache "{name}" (idlang:{idlang}) im Projekt "{clientname}" (idclient:{idclient}) als globale Startsprache gesetzt';


// $type = plugin
$cms_lang[$prefix.'_messages_plug_new'] = 'Plugin "{name} {version}" (idplug:{idplug}) angelegt';
$cms_lang[$prefix.'_messages_plug_edit'] = 'Plugin "{name} {version}" (idplug:{idplug}) bearbeitet';
$cms_lang[$prefix.'_messages_plug_delete'] = 'Plugin "{name} {version}" (idplug:{idplug}) gelöscht';
$cms_lang[$prefix.'_messages_plug_import'] = 'Plugin "{name} {version}" (idplug:{idplug}) importiert';
$cms_lang[$prefix.'_messages_plug_export'] = 'Plugin "{name} {version}" (idplug:{idplug}) exportiert';
$cms_lang[$prefix.'_messages_plug_upload'] = 'Plugin "{name} {version}" (idplug:{idplug}) hochgeladen';
$cms_lang[$prefix.'_messages_plug_download'] = 'Plugin "{name}" (idplug:{idplug}) heruntergeladen';
$cms_lang[$prefix.'_messages_plug_install_client'] = 'Plugin "{name} {version}" (idplug:{idplug}) für das Projekt "{clientname}" (idclient:{idclient}) installiert';
$cms_lang[$prefix.'_messages_plug_uninstall_client'] = 'Plugin "{name} {version}" (idplug:{idplug}) für das Projekt "{clientname}" (idclient:{idclient}) deinstalliert';
$cms_lang[$prefix.'_messages_plug_update'] = 'Plugin "{name} {version}" (idplug:{idplug}) aktualisiert';
$cms_lang[$prefix.'_messages_plug_reinstall'] = 'Plugin "{name} {version}" (idplug:{idplug}) re-installiert';
$cms_lang[$prefix.'_messages_plug_repository_update'] = 'Plugin "{name} {version}" (idplug:{idplug}) aus dem Repository aktualisiert';
$cms_lang[$prefix.'_messages_plug_repository_install'] = 'Plugin "{name}" (idplug:{idplug}) aus dem Repository importiert';


// $type = logs
$cms_lang[$prefix.'_messages_logs_delete_logfile_success'] = 'Die Logdatei "{path}" wurde erfolgreich gelöscht!';

?>
