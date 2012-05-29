<?php
if(! defined('CMS_CONFIGFILE_INCLUDED')){
	die('NO CONFIGFILE FOUND');
}

$prefix = (isset($prefix) == TRUE) ? $prefix : 'fm';


$cms_lang[$prefix.'_area_index'] = (isset($cms_lang[$prefix.'_area_index']) == TRUE) ? $cms_lang[$prefix.'_area_index'] : 'Redaktion &rsaquo; Dateimanager';

$cms_lang[$prefix.'_area_create_directory'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Verzeichnis anlegen';
$cms_lang[$prefix.'_area_edit_directory'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Verzeichnis bearbeiten';
$cms_lang[$prefix.'_area_delete_directory'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Verzeichnis l&ouml;schen';
$cms_lang[$prefix.'_area_copy_directory'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Verzeichnis kopieren';
$cms_lang[$prefix.'_area_download_directory'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Verzeichnis herunterladen';

$cms_lang[$prefix.'_area_create_file'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Datei anlegen';
$cms_lang[$prefix.'_area_edit_file'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Datei bearbeiten';
$cms_lang[$prefix.'_area_delete_file'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Datei l&ouml;schen';
$cms_lang[$prefix.'_area_copy_file'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Datei kopieren';
$cms_lang[$prefix.'_area_upload_file'] = 'Datei hochladen';
$cms_lang[$prefix.'_area_download_file'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Datei herunterladen';
$cms_lang[$prefix.'_area_import_files'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Dateien importieren';

$cms_lang[$prefix.'_area_delete_multiple'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Auswahl l&ouml;schen';
$cms_lang[$prefix.'_area_copy_multiple'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Auswahl kopieren';
$cms_lang[$prefix.'_area_download_multiple'] = $cms_lang[$prefix.'_area_index'].' &rsaquo; Auswahl herunterladen';


// messages (errors, warnings, ok)
$cms_lang[$prefix.'_permission_denied'] = $cms_lang['gen_permission_denied'];
$cms_lang[$prefix.'_more_info'] = 'Weitere Informationen'; 
$cms_lang[$prefix.'_no_information_found'] = 'Keine weiteren Informationen (mehr) vorhanden.';
$cms_lang[$prefix.'_no_type_found'] = 'Unbekanntes Format zur Aufbereitung der Daten vorhanden.';

$cms_lang[$prefix.'_missing_name'] = 'Kein Verzeichnisname vorhanden!';
$cms_lang[$prefix.'_forbidden_directory_name'] = 'Die Verzeichnisname ist unzul&auml;ssig!';
$cms_lang[$prefix.'_invalid_name_correction_failed'] = 'Der Verzeichnisname ist ung&uuml;ltig und konnte nicht korrigiert werden!';
$cms_lang[$prefix.'_directory_exists_in_destination'] = 'Es existiert bereits ein Verzeichnis mit diesem Namen!';
$cms_lang[$prefix.'_save_directory_to_db_failed'] = 'Das Verzeichnis konnte nicht gespeichert werden!';
$cms_lang[$prefix.'_directory_is_not_loaded'] = 'Das Verzeichnis konnte nicht gefunden werden!';
$cms_lang[$prefix.'_save_sub_directories_to_db_failed'] = 'Die Pfade f&uuml;r die Unterverzeichnisse konnten nicht umbenannt werden!';
$cms_lang[$prefix.'_missing_destination_parent_directory'] = 'Kein oder ung&uuml;ltiges Zielverzeichnis angegeben!';
$cms_lang[$prefix.'_destination_is_a_child_of_source'] = 'Das Zielverzeichnis darf kein Unterverzeichnis des Quellverzeichnisses sein!';
$cms_lang[$prefix.'_create_directory_failed'] = 'Das Verzeichnis konnte nicht erstellt werden!';
$cms_lang[$prefix.'_rename_directory_failed'] = 'Das Verzeichnis konnte nicht umbenannt werden!';
$cms_lang[$prefix.'_copy_directory_failed'] = 'Das Verzeichnis konnte nicht kopiert werden!';
$cms_lang[$prefix.'_move_directory_failed'] = 'Das Verzeichnis konnte nicht verschoben werden!';
$cms_lang[$prefix.'_delete_directory_failed'] = 'Das Verzeichnis konnte nicht gel&ouml;scht werden!';
$cms_lang[$prefix.'_delete_directory_from_db_failed'] = 'Das Verzeichnis konnte nicht gel&ouml;scht werden!';
$cms_lang[$prefix.'_save_directory_success'] = 'Das Verzeichnis wurde erfolgreich gespeichert!';
$cms_lang[$prefix.'_update_directory_success'] = 'Das Verzeichnis wurde erfolgreich aktualisiert!';
$cms_lang[$prefix.'_copy_directory_success'] = 'Das Verzeichnis wurde erfolgreich kopiert!';
$cms_lang[$prefix.'_move_directory_success'] = 'Das Verzeichnis wurde erfolgreich verschoben!';
$cms_lang[$prefix.'_delete_directory_success'] = 'Das Verzeichnis wurde erfolgreich gel&ouml;scht!';

$cms_lang[$prefix.'_missing_upload'] = 'Kein Upload vorhanden!';
$cms_lang[$prefix.'_move_uploaded_file_failed'] = 'Der Upload wurde nicht abgelegt!';
$cms_lang[$prefix.'_forbidden_file_extension'] = 'Die Dateinamenserweiterung ist unzul&auml;ssig!';
$cms_lang[$prefix.'_file_exists_in_destination'] = 'Es existiert bereits eine Datei mit diesem Namen!';
$cms_lang[$prefix.'_missing_iddirectory'] = 'Kein Verzeichnis zum Speichern angeben!';
$cms_lang[$prefix.'_missing_filename'] = 'Kein Dateiname vorhanden!';
$cms_lang[$prefix.'_invalid_filename_correction_failed'] = 'Der Dateiname ist ung&uuml;ltig und konnte nicht korrigiert werden!';
$cms_lang[$prefix.'_write_content_to_file_failed'] = 'Der Inhalt konnte nicht in der Datei gespeichert werden!';
$cms_lang[$prefix.'_save_file_to_db_failed'] = 'Die Datei konnte nicht gespeichert werden!';
$cms_lang[$prefix.'_file_is_not_loaded'] = 'Die Datei konnte nicht gefunden werden!';
$cms_lang[$prefix.'_copy_file_failed'] = 'Die Datei konnte nicht kopiert werden!';
$cms_lang[$prefix.'_equal_directories'] = 'Das Quell- und Zielverzeichnis d&uuml;rfen nicht identisch sein!';
$cms_lang[$prefix.'_move_file_failed'] = 'Die Datei konnte nicht verschoben werden!';
$cms_lang[$prefix.'_delete_file_failed'] = 'Die Datei konnte nicht gel&ouml;scht werden!';
$cms_lang[$prefix.'_delete_file_from_db_failed'] = 'Die Datei konnte nicht gel&ouml;scht werden!';
$cms_lang[$prefix.'_save_file_success'] = 'Die Datei wurde erfolgreich gespeichert!';
$cms_lang[$prefix.'_update_file_success'] = 'Die Datei wurde erfolgreich aktualisiert!';
$cms_lang[$prefix.'_copy_file_success'] = 'Die Datei wurde erfolgreich kopiert!';
$cms_lang[$prefix.'_move_file_success'] = 'Die Datei wurde erfolgreich verschoben!';
$cms_lang[$prefix.'_delete_file_success'] = 'Die Datei wurde erfolgreich gel&ouml;scht!';
$cms_lang[$prefix.'_upload_file_success'] = 'Die Datei wurde erfolgreich hochgeladen!';
$cms_lang[$prefix.'_upload_file_failed'] = 'Der Upload ist fehlgeschlagen!';
$cms_lang[$prefix.'_upload_successful'] = 'Der Upload war erfolgreich!';
$cms_lang[$prefix.'_some_uploads_failed'] = 'Der Upload ist teilweise fehlgeschlagen! Es sind Fehler aufgetreten.';
$cms_lang[$prefix.'_all_uploads_failed'] = 'Der Upload ist vollst&auml;ndig fehlgeschlagen! Es sind Fehler aufgetreten.';
$cms_lang[$prefix.'_upload_uncompress_file_success'] = 'Datei erfolgreich hochgeladen und entpackt!';
$cms_lang[$prefix.'_upload_uncompress_file_failed'] = 'Datei erfolgreich hochgeladen, aber Fehler beim Entpacken der Datei!';
$cms_lang[$prefix.'_generate_thumbnail_failed'] = 'Die Vorschaubilder konnten nicht generiert werden!';
$cms_lang[$prefix.'_copy_thumbnail_failed'] = 'Die Vorschaubilder konnten nicht kopiert werden!';
$cms_lang[$prefix.'_move_thumbnail_failed'] = 'Die Vorschaubilder konnten nicht verschoben werden!';
$cms_lang[$prefix.'_delete_thumbnail_failed'] = 'Die Vorschaubilder konnten nicht gel&ouml;scht werden!';

$cms_lang[$prefix.'_copy_metadata_success'] = 'Die Metadaten wurden erfolgreich &uuml;bernommen!';
$cms_lang[$prefix.'_copy_metadata_failed'] = 'Die Metadaten konnten nicht &uuml;bernommen werden!';

$cms_lang[$prefix.'_add_rights_failed'] = 'Die Rechte konnten nicht angelegt werden!';
$cms_lang[$prefix.'_copy_rights_failed'] = 'Die Rechte konnten nicht &uuml;bernommen werden!';
$cms_lang[$prefix.'_delete_rights_failed'] = 'Die Rechte konnten nicht gel&ouml;scht werden!';

$cms_lang[$prefix.'_all_actions_failed'] = 'Aktion vollst&auml;ndig fehlgeschlagen! Es sind Fehler aufgetreten.';
$cms_lang[$prefix.'_some_actions_failed'] = 'Aktion teilweise fehlgeschlagen! Es sind Fehler aufgetreten.';
$cms_lang[$prefix.'_action_successful'] = 'Aktion erfolgreich ausgef&uuml;hrt! Es sind keine Fehler aufgetreten.';

// field names
$cms_lang[$prefix.'_directory_info'] = 'Verzeichnis-Informationen';
$cms_lang[$prefix.'_directory_id'] = 'Aktuelles Verzeichnis';
$cms_lang[$prefix.'_directory_edit_rights'] = 'Verzeichnisrechte';
$cms_lang[$prefix.'_directory_parentid'] = '&Uuml;bergeordnetes Verzeichnis';
$cms_lang[$prefix.'_directory_source'] = 'Quellverzeichnis';
$cms_lang[$prefix.'_directory_destination'] = 'Zielverzeichnis';
$cms_lang[$prefix.'_directory_new_name'] = 'Neuer Verzeichnisname';
$cms_lang[$prefix.'_directory_name'] = 'Verzeichnisname';
$cms_lang[$prefix.'_directory_description'] = 'Beschreibung';
$cms_lang[$prefix.'_directory_created'] = 'Erstellt';
$cms_lang[$prefix.'_directory_lastmodified'] = 'Ge&auml;ndert';
$cms_lang[$prefix.'_directory_move'] = 'Verzeichnis verschieben';
$cms_lang[$prefix.'_directory_move_label'] = ' Quellverzeichnis nach dem Kopieren l&ouml;schen? (entspricht verschieben)';

$cms_lang[$prefix.'_file_info'] = 'Datei-Informationen';
$cms_lang[$prefix.'_file_id'] = 'Aktuelle Datei';
$cms_lang[$prefix.'_file_edit_rights'] = 'Dateirechte';
$cms_lang[$prefix.'_file_iddirectory'] = 'Zielverzeichnis';
$cms_lang[$prefix.'_file_source'] = 'Quelldatei';
$cms_lang[$prefix.'_file_new_filename'] = 'Neuer Dateiname';
$cms_lang[$prefix.'_file_filename'] = 'Dateiname';
$cms_lang[$prefix.'_file_title'] = 'Titel';
$cms_lang[$prefix.'_file_description'] = 'Beschreibung';
$cms_lang[$prefix.'_file_content'] = 'Datei-Inhalt';
$cms_lang[$prefix.'_file_created'] = 'Erstellt';
$cms_lang[$prefix.'_file_lastmodified'] = 'Ge&auml;ndert';
$cms_lang[$prefix.'_file_filesize'] = 'Dateigr&ouml;&szlig;e';
$cms_lang[$prefix.'_file_image_dimension'] = 'Bildgr&ouml;&szlig;e';
$cms_lang[$prefix.'_file_thumbnail'] = 'Vorschaubild';
$cms_lang[$prefix.'_file_move'] = 'Datei verschieben';
$cms_lang[$prefix.'_file_move_label'] = ' Quelldatei nach dem Kopieren l&ouml;schen? (entspricht verschieben)';

$cms_lang[$prefix.'_multiple_selection'] = 'Auswahl';
$cms_lang[$prefix.'_multiple_directory'] = 'Verzeichnis';
$cms_lang[$prefix.'_multiple_directories'] = 'Verzeichnisse';
$cms_lang[$prefix.'_multiple_and'] = ' und ';
$cms_lang[$prefix.'_multiple_file'] = 'Datei';
$cms_lang[$prefix.'_multiple_files'] = 'Dateien';
$cms_lang[$prefix.'_multiple_destination'] = 'Zielverzeichnis';
$cms_lang[$prefix.'_multiple_move'] = 'Auswahl verschieben';
$cms_lang[$prefix.'_multiple_move_label'] = ' Auswahl nach dem Kopieren l&ouml;schen? (entspricht verschieben)';



// validation
$cms_lang[$prefix.'_validation_error_required'] = 'Das Feld darf nicht leer sein!';
$cms_lang[$prefix.'_validation_error_numeric'] = 'Nur Zahlenwerte sind erlaubt!';
$cms_lang[$prefix.'_validation_error_numericNoZero'] = 'Nur Zahlenwerte gr&ouml;&szlig;er Null sind erlaubt!';
$cms_lang[$prefix.'_validation_error_directoryname'] = 'Verzeichnisname enth&auml;lt unzul&auml;ssige Zeichen!';
$cms_lang[$prefix.'_validation_error_filename'] = 'Dateiname enth&auml;lt unzul&auml;ssige Zeichen!';



// actions
$cms_lang[$prefix.'_goto_parentdirectory'] = '.. (Verzeichnis aufw&auml;rts)';
$cms_lang[$prefix.'_create_directory'] = 'Verzeichnis anlegen';
$cms_lang[$prefix.'_scan_directory'] = 'Verzeichnis mit Datenbank abgleichen';
$cms_lang[$prefix.'_download_directory'] = 'Verzeichnis packen und herunterladen';
$cms_lang[$prefix.'_edit_directory'] = 'Verzeichnis bearbeiten';
$cms_lang[$prefix.'_copy_directory'] = 'Verzeichnis kopieren oder verschieben';
$cms_lang[$prefix.'_delete_directory'] = 'Verzeichnis l&ouml;schen';
$cms_lang[$prefix.'_create_current_directory'] = 'Verzeichnis anlegen';
$cms_lang[$prefix.'_scan_current_directory'] = 'Aktuelles Verzeichnis mit Datenbank abgleichen';
$cms_lang[$prefix.'_download_current_directory'] = 'Aktuelles Verzeichnis packen und herunterladen';
$cms_lang[$prefix.'_edit_current_directory'] = 'Aktuelles Verzeichnis bearbeiten';
$cms_lang[$prefix.'_copy_current_directory'] = 'Aktuelles Verzeichnis kopieren oder verschieben';
$cms_lang[$prefix.'_delete_current_directory'] = 'Aktuelles Verzeichnis l&ouml;schen';
$cms_lang[$prefix.'_viewtype_compact'] = 'Zur Kompaktansicht wechseln';
$cms_lang[$prefix.'_viewtype_detail'] = 'Zur Detailansicht wechseln';

$cms_lang[$prefix.'_upload_file'] = 'Datei hochladen';
$cms_lang[$prefix.'_create_file'] = 'Datei anlegen';
$cms_lang[$prefix.'_preview_file'] = 'Datei &ouml;ffnen';
$cms_lang[$prefix.'_download_file'] = 'Datei herunterladen';
$cms_lang[$prefix.'_edit_file'] = 'Datei bearbeiten';
$cms_lang[$prefix.'_copy_file'] = 'Datei kopieren oder verschieben';
$cms_lang[$prefix.'_delete_file'] = 'Datei l&ouml;schen';

$cms_lang[$prefix.'_download_multiple'] = 'Ausgew&auml;hlte Verzeichnisse und Dateien herunterladen';
$cms_lang[$prefix.'_copy_multiple'] = 'Ausgew&auml;hlte Verzeichnisse und Dateien kopieren oder verschieben';
$cms_lang[$prefix.'_delete_multiple'] = 'Ausgew&auml;hlte Verzeichnisse und Dateien l&ouml;schen';


$cms_lang[$prefix.'_action'] = 'Aktionen';
$cms_lang[$prefix.'_base_directory'] = 'Basisverzeichnis';
$cms_lang[$prefix.'_filecollection_nodata'] = 'Keine Dateien und Verzeichnisse vorhanden.';
$cms_lang[$prefix.'_upload_totalsize'] = 'Gesamtgr&ouml;&szlig;e';
$cms_lang[$prefix.'_upload_mode_html'] = 'Zum Einzeldatei-Upload wechseln';
$cms_lang[$prefix.'_upload_mode_html_tooltip'] = 'Wechseln Sie zum Einzeldatei-Upload, wenn Sie mit dem aktuellen Upload Probleme haben.';
$cms_lang[$prefix.'_upload_mode_flash'] = 'Zum Mehrdateien-Upload wechseln';
$cms_lang[$prefix.'_upload_browse'] = 'Durchsuchen';
$cms_lang[$prefix.'_upload_clearqueue'] = 'Liste leeren';
$cms_lang[$prefix.'_upload_extract_files'] = 'Komprimierte Dateien (zip, tar) falls m&ouml;glich entpacken';
$cms_lang[$prefix.'_upload_upload'] = 'Hochladen';
$cms_lang[$prefix.'_upload_cancel'] = 'Abbrechen';
$cms_lang[$prefix.'_upload_nofilesselected'] = 'Keine Dateien ausgew&auml;hlt. Klicken Sie auf "'.$cms_lang[$prefix.'_upload_browse'].'".';

$cms_lang[$prefix.'_upload_show_messages'] = 'Zeige Meldungen: ';
$cms_lang[$prefix.'_upload_messages_all'] = 'Alle';
$cms_lang[$prefix.'_upload_messages_error'] = 'Fehler';
$cms_lang[$prefix.'_upload_messages_warning'] = 'Warnungen';
$cms_lang[$prefix.'_upload_messages_ok'] = 'Erfolge';
$cms_lang[$prefix.'_upload_close'] = 'Schlie&szlig;en';


$cms_lang[$prefix.'_js_close_confirm'] = 'Es werden gerade Aktionen durchgef\u00fchrt. Das vorzeitige Schlie\u00dfen kann zu unerwarteten Fehlern f\u00fchren.\nM\u00f6chten Sie die laufende Aktion abbrechen und das Fenster schlie\u00dfen?';
$cms_lang[$prefix.'_js_upload_close_confirm'] = 'Es werden gerade Dateien hochgeladen. Wird das Fenster geschlossen, sind die Dateien unvollst\u00e4ndig.\nM\u00f6chten Sie das Hochladen abbrechen und das Fenster schlie\u00dfen?';
$cms_lang[$prefix.'_js_scan_close_confirm'] = 'Der Verzeichnisabgleich l\u00e4uft gerade. Das vorzeitige Schlie\u00dfen kann zu einem unerwarteten Ergebnis f\u00fchren.\nM\u00f6chten Sie den Verzeichnisabgleich abbrechen und das Fenster schlie\u00dfen?';
$cms_lang[$prefix.'_js_error_loadinglayer'] = 'Fehler beim Laden des Layers!';
$cms_lang[$prefix.'_js_error_nopreview'] = 'F\u00fcr das Objekt ist keine Vorschau m\u00f6glich!';
$cms_lang[$prefix.'_js_delete_dir_confirm'] = 'M\u00f6chten Sie das Verzeichnis \\\'\{name\}\\\' wirklich l\u00f6schen?';
$cms_lang[$prefix.'_js_delete_file_confirm'] = 'M\u00f6chten Sie die Datei \\\'\{name\}\\\' wirklich l\u00f6schen?';
$cms_lang[$prefix.'_js_delete_multi_confirm'] = 'M\u00f6chten Sie die ausgew\u00e4hlten Verzeichnisse und Dateien wirklich l\u00f6schen?';

$cms_lang[$prefix.'_switch_language'] = 'Metadaten &uuml;bernehmen aus&hellip;';
$cms_lang[$prefix.'_switch_language_confirm'] = 'Sollen wirklich alle Metadaten aus der Sprache \'{selection}\' f&uuml;r die aktuelle Sprache &uuml;bernommen werden? Alle derzeitigen Metadaten der Datei werden gel&ouml;scht! Dieser Vorgang kann nicht r&uuml;ckg&auml;ngig gemacht werden!';

?>