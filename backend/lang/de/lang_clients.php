<?php
if(! defined('CMS_CONFIGFILE_INCLUDED'))
{
	die('NO CONFIGFILE FOUND');
}

$cms_lang['clients_new_client'] = 'Neues Projekt';
$cms_lang['clients_submit'] = 'Abschicken';
$cms_lang['clients_clients'] = 'Projekte';
$cms_lang['clients_headline'] = 'Projekte/ Sprachen';
$cms_lang['clients_desc'] = 'Beschreibung';
$cms_lang['clients_actions'] = 'Aktionen';
$cms_lang['clients_lang_edit'] = 'Sprache bearbeiten/ umbenennen';
$cms_lang['clients_lang_delete'] = 'Sprache löschen';
$cms_lang['clients_abort'] = 'Abbrechen';
$cms_lang['clients_lang_new'] = 'Neue Sprache';
$cms_lang['clients_charset'] = 'Sprachcodierung';
$cms_lang['clients_collapse'] = 'Zuklappen';
$cms_lang['clients_expand'] = 'Aufklappen';
$cms_lang['clients_make_new_lang'] = 'Neue Sprache anlegen';
$cms_lang['clients_modify'] = 'Projektnamen ändern';
$cms_lang['clients_config'] = 'Projekteinstellungen/ Projekt konfigurieren';
$cms_lang['clients_delete'] = 'Projekt löschen';
$cms_lang['clients_client'] = 'Projekt';
$cms_lang['clients_client_desc'] = 'Projektbeschreibung';
$cms_lang['clients_client_path'] = 'Projektpfad';
$cms_lang['clients_client_url'] = 'Projekturl';
$cms_lang['clients_client_directory'] = 'Verzeichnis anlegen';
$cms_lang['clients_client_start_lang'] = 'Startsprache';

$cms_lang['clients_client_desc'] = "Beschreibung";
$cms_lang['clients_client_charset'] = "Sprachcodierung";
$cms_lang['clients_client_auto_startlang'] = "Automatische Standardsprache (Browserabhängig)";
$cms_lang['clients_client_prefix_rewrite'] = "Kurzzeichen für URL- Rewrite";
$cms_lang['clients_client_urlfilter'] = "URL- Filter für URL- Rewrite";

$cms_lang['clients_lang_desc'] = 'Sprachbeschreibung';
$cms_lang['clients_lang_charset'] = 'Sprachcodierung';

//Erfolgsmeldungen
$cms_lang['success_delete_lang'] = 'Sprache erfolgreich gelöscht.';
$cms_lang['success_delete_client'] = 'Projekt erfolgreich aus der Datenbank gelöscht. Bitte löschen Sie das physikalische Verzeichnis im Dateisystem noch nachträglich von Hand';
$cms_lang['success_new_lang'] = 'Sprache erfolgreich angelegt.';
$cms_lang['success_new_client'] = 'Neues Projekt erfolgreich angelegt.';

//Errors
$cms_lang['err_cant_make_path'] = "Verzeichnis konnte nicht erstellt werden. Bitte überprüfen Sie die Rechte des Dateisystems. Dieser Fehler kann auch auftreten, wenn das entsprechende Verzeichnis schon existiert.";
$cms_lang['err_cant_extract_tar'] = "Die Projektvorlage konnte nicht entpackt werden. TAR- Archiv defekt?";


// Clients config

// PATHES
$cms_lang['setuse_pathes_title'] = 'Pfadangaben';
$cms_lang['setuse_path_http'] = 'HTTP Basispfad zum Frontend (absolut), Variablen: {%http_host}';
$cms_lang['setuse_path_http_edit'] = 'HTTP Basispfad zum Frontend in der Backend- Onsite- Ansicht (absolut), Variablen: {%http_host}';
$cms_lang['setuse_path_rel'] = 'Serverpfad zum Frontend (relativ)';
$cms_lang['setuse_contentfile'] = 'Name der Fontenddatei';
$cms_lang['setuse_path_fm_rel'] = 'Serverpfad zum Dateimanager (relativ)';
$cms_lang['setuse_path_css_rel'] = 'Serverpfad CSS Dateien (relativ)';
$cms_lang['setuse_path_js_rel'] = 'Serverpfad Javascript Dateien (relativ)';
$cms_lang['setuse_page_start'] = 'Idcatside Startseite (wenn leer, wird die Startseite des ersten Ordners genutzt)';
$cms_lang['setuse_page_404'] = '404 Fehlerseite, Variablen: {%http_host}, {%request_uri} oder idcatside';
$cms_lang['setuse_page_timeout'] = 'Login-Timeoutseite, Variablen: {%http_host}, {%request_uri} oder idcatside';
$cms_lang['setuse_https'] = 'HTTPS Unterstützung für Seiten aktivieren (0=nein, 1=ja)';

// MOD_REWRITE
$cms_lang['setuse_mod_rewrite_title'] = 'mod_rewite';
$cms_lang['setuse_url_rewrite'] = 'Aktivieren (0=nein, 1=numerische URLs, 2=gesprochene URLs)';
$cms_lang['setuse_url_langid_in_defaultlang'] = 'Id der Standardsprache in URL zeigen (bei mod_rewrite=2)';
$cms_lang['setuse_url_rewrite_suffix'] = 'Seiten Suffix (bei mod_rewrite=2)';

// SESSION
$cms_lang['setuse_session_title'] = 'Session';
$cms_lang['setuse_session_enabled'] = 'Session aktivieren (0=nein, 1=ja)';
$cms_lang['setuse_session_lifetime'] = 'Session Lebenszeit in Minuten';
$cms_lang['setuse_session_frontend_domain'] = 'Session Frontend Domain';
$cms_lang['setuse_session_disabled_useragents'] = 'Useragents für die keine Session erzeugt wird (eine pro Zeile)';
$cms_lang['setuse_session_disabled_ips'] = 'IPs für die keine Session erzeugt wird (eine pro Zeile)';

// CACHE
$cms_lang['setuse_cache_title'] = 'Cache';
$cms_lang['setuse_cache'] = 'Frontendseiten cachen (0=nein, 1=ja)';

// FILEMANAGER
$cms_lang['setuse_fm_title'] = 'Dateimanager';
$cms_lang['setuse_upl_path'] = 'Startverzeichnis Dateimanager';
$cms_lang['setuse_upl_htmlpath'] = 'Startverzeichnis HTML-Pfad';
$cms_lang['setuse_fm_multi_language_support']	= 'Mehrsprachige Verwaltung (0=nein, 1=ja)';
$cms_lang['setuse_fm_forbidden_files'] = 'Verbotene Dateiendungen';
$cms_lang['setuse_fm_forbidden_directories'] = 'Verbotene Verzeichnisnamen';
$cms_lang['setuse_thumb_size'] = 'Größe der Vorschaubilder';
$cms_lang['setuse_thumb_aspectratio'] = 'Proportionen beibehalten (0=nein, 1=ja, 2=Y&nbsp;skaliert, 3=X&nbsp;skaliert, 4=Quadratisch&nbsp;zugeschnitten)';
$cms_lang['setuse_more_thumb_size'] = 'Größen weiterer Vorschaubilder (kommasepariert)';
$cms_lang['setuse_more_thumb_aspect_ratio'] = 'Weiterere Proportionen beibehalten (kommasepariert; 0=nein, 1=ja, 2=Y&nbsp;skaliert, 3=X&nbsp;skaliert, 4=Quadratisch&nbsp;zugeschnitten)';
$cms_lang['setuse_thumb_ext'] = 'Dateikennung für generierte Thumbnails';
$cms_lang['setuse_generate_thumb'] = 'Thumbnails generieren für (falls möglich)';
$cms_lang['setuse_fm_delete_ignore_404'] = 'Fehlermeldung beim Löschen von fehlenden Dateien ignorieren (0=nein, 1=ja)';
$cms_lang['setuse_fm_remove_files_404'] = 'Verwaiste Dateieinträge bei Datenbankabgleich löschen (0=nein, 1=ja)';
$cms_lang['setuse_fm_remove_empty_directories']	= 'Leere Dateiverzeichnisse beim Abgleichen löschen (0=nein, 1=ja)';
$cms_lang['setuse_fm_allow_invalid_dirnames'] = 'Verzeichnisse mit ungültigem Namen zulassen (0=nein, 1=ja, 2=falls möglich korrigieren)';
$cms_lang['setuse_fm_allow_invalid_filenames'] = 'Dateien mit ungültigem Dateinamen zulassen (0=nein, 1=ja, 2=falls möglich korrigieren)';
$cms_lang['setuse_fm_allowed_files'] = 'Erlaubte Dateiendungen (entkräftet "'.$cms_lang['setuse_fm_forbidden_files'].'")';

// STYLESHEET
$cms_lang['setuse_css_title']	= 'Stylesheet';
$cms_lang['setuse_css_multi_language_support'] = $cms_lang['setuse_fm_multi_language_support'];
$cms_lang['setuse_css_forbidden_files']	= $cms_lang['setuse_fm_forbidden_files'];
$cms_lang['setuse_css_forbidden_directories']	= $cms_lang['setuse_fm_forbidden_directories'];
$cms_lang['setuse_css_delete_ignore_404']	= $cms_lang['setuse_fm_delete_ignore_404'];
$cms_lang['setuse_css_remove_files_404'] = $cms_lang['setuse_fm_remove_files_404'];
$cms_lang['setuse_css_remove_empty_directories'] = $cms_lang['setuse_fm_remove_empty_directories'];
$cms_lang['setuse_css_allow_invalid_dirnames'] = $cms_lang['setuse_fm_allow_invalid_dirnames'];
$cms_lang['setuse_css_allow_invalid_filenames']	= $cms_lang['setuse_fm_allow_invalid_filenames'];
$cms_lang['setuse_css_allowed_files']	= $cms_lang['setuse_fm_allowed_files'];

// JAVASCRIPT
$cms_lang['setuse_js_title'] = 'Javascript';
$cms_lang['setuse_js_multi_language_support']	= $cms_lang['setuse_fm_multi_language_support'];
$cms_lang['setuse_js_forbidden_files'] = $cms_lang['setuse_fm_forbidden_files'];
$cms_lang['setuse_js_forbidden_directories'] = $cms_lang['setuse_fm_forbidden_directories'];
$cms_lang['setuse_js_delete_ignore_404'] = $cms_lang['setuse_fm_delete_ignore_404'];
$cms_lang['setuse_js_remove_files_404']	= $cms_lang['setuse_fm_remove_files_404'];
$cms_lang['setuse_js_remove_empty_directories']	= $cms_lang['setuse_fm_remove_empty_directories'];
$cms_lang['setuse_js_allow_invalid_dirnames']	= $cms_lang['setuse_fm_allow_invalid_dirnames'];
$cms_lang['setuse_js_allow_invalid_filenames'] = $cms_lang['setuse_fm_allow_invalid_filenames'];
$cms_lang['setuse_js_allowed_files'] = $cms_lang['setuse_fm_allowed_files'];

// LOGGING
$cms_lang['set_logs_title'] = 'Logging';
$cms_lang['set_logs_storage_screen'] = 'Auf Bildschirm ausgeben';
$cms_lang['set_logs_storage_logfile'] = 'In Logdatei speichern';
$cms_lang['set_logs_storage_database'] = 'In Datenbank speichern';

// MISC
$cms_lang['setuse_misc_title']	= 'Sonstiges';
$cms_lang['setuse_manipulate_output']	= 'Frontendausgabe manipulieren';

// LANGUAGE SETTINGS
$cms_lang['set_meta'] = 'Metaangaben vorkonfigurieren';
$cms_lang['set_meta_description'] = 'Seitenbeschreibung';
$cms_lang['set_meta_keywords'] = 'Suchbegriffe';
$cms_lang['set_meta_robots'] = 'Suchmaschinenanweisung';


?>