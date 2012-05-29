<?php
if(! defined('CMS_CONFIGFILE_INCLUDED')){
	die('NO CONFIGFILE FOUND');
}

$cms_lang['clients_new_client']		= 'New project';
$cms_lang['clients_submit']			= 'Submit';
$cms_lang['clients_clients']			= 'Projects';
$cms_lang['clients_headline']			= 'Projects/languages';
$cms_lang['clients_desc']				= 'Description';
$cms_lang['clients_actions']			= 'Action';
$cms_lang['clients_lang_edit']			= 'Edit/rename language';
$cms_lang['clients_lang_delete']		= 'Delete language';
$cms_lang['clients_abort']				= 'Cancel';
$cms_lang['clients_lang_new']			= 'New language';
$cms_lang['clients_charset']			= 'Charset';
$cms_lang['clients_collapse']			= 'Collapse';
$cms_lang['clients_expand']			= 'Expand';
$cms_lang['clients_make_new_lang']		= 'Create new language';
$cms_lang['clients_modify']			= 'Edit project name';
$cms_lang['clients_config']			= 'Project settings/ configure project';
$cms_lang['clients_delete']			= 'Delete project';
$cms_lang['clients_client']			= 'Project';
$cms_lang['clients_client_desc']		= 'Project description';
$cms_lang['clients_client_path']		= 'Project path';
$cms_lang['clients_client_url']		= 'Project URL';
$cms_lang['clients_client_directory']	= 'Create directory';
$cms_lang['clients_client_start_lang']	= 'primary language';
$cms_lang['clients_lang_desc']			= 'Language description';
$cms_lang['clients_lang_charset']		= 'Charset';

//Erfolgsmeldungen
$cms_lang['success_delete_lang']	= 'Language successfully deleted.';
$cms_lang['success_delete_client']	= 'Project successfully deleted from database. Please remove the project files in the file system!';
$cms_lang['success_new_lang']		= 'Language successfully created.';
$cms_lang['success_new_client']	= 'New project successfully created.';

//Errors
$cms_lang['err_cant_make_path']	= "Directory could not be created. Please check the permission for the file system and avoid duplicated directory names.";
$cms_lang['err_cant_extract_tar']	= "Failed to open project template. TAR-archive maybe corrupt?";


// Clients config

$cms_lang['set_session_frontend_lifetime']		= 'Lebenszeit Session Frontend';
$cms_lang['set_session_frontend_enabled']		= 'Frontend Session Support';

$cms_lang['setuse_pathes']					= 'Pfad und Dateiangaben';
$cms_lang['setuse_path']						= 'Pfad zum Frontend';
$cms_lang['setuse_html_path']					= 'HTML-Pfad zum Frontend';
$cms_lang['setuse_contentfile']					= 'Name der Frontenddatei';
$cms_lang['setuse_space']						= 'Platzhalter f&uuml;r Bilder';

$cms_lang['setuse_general']					= 'Allgemeine Einstellungen';
$cms_lang['setuse_publish']						= '&Auml;nderungen erst nach Freigabe publizieren';
$cms_lang['setuse_edit_mode']					= 'Edit-Modus 0=Visuell 1=Vis.-Cont. 2=Cont.-Vis. 3=Content';
$cms_lang['setuse_wysiwyg_applet']				= 'WYSIWYG Applet 0=nie, 1=kein IE, 2=immer, 3=kein IE + Mozilla';
$cms_lang['setuse_default_layout']				= 'Layoutvorlage';
$cms_lang['setuse_errorpage']					= '404 Fehlerseite bei nicht existierender idcatside/ idcat als idcatside';
$cms_lang['setuse_loginpage']					= 'idcatside f&uuml;r Login-Timeoutseite';
$cms_lang['setuse_cache']						= 'Frontendseiten cachen';
$cms_lang['setuse_session_frontend_domain']		= 'Session Frontend Domain';
$cms_lang['setuse_url_rewrite']					= 'Apache mod_rewrite Support';
$cms_lang['setuse_url_langid_in_defaultlang']	= 'ID der Standardsprache in URL zeigen';
$cms_lang['setuse_url_rewrite_basepath']		= 'Basepath bei UrlRewrite=2. Variablen: {%http_host}';
$cms_lang['setuse_url_rewrite_404']				= '404 Fehlerseite bei UrlRewrite=2. Variablen: {%http_host}, {%request_uri} oder idcatside';
$cms_lang['setuse_url_rewrite_suffix']			= 'URL Rewrite Seiten Suffix';
$cms_lang['setuse_session_disabled_useragents']	= 'Useragents f&uuml;r die keine Session erzeugt wird (eine pro Zeile)';
$cms_lang['setuse_session_disabled_ips']		= 'IPs f&uuml;r die keine Session erzeugt wird (eine pro Zeile)';
$cms_lang['setuse_manipulate_output']			= 'Ausgabe manipulieren';
$cms_lang['setuse_https_active']				= 'HTTPS Unterst&uuml;tzung f&uuml;r Seiten aktivieren (1=ja, 0=nein)';

$cms_lang['setuse_filemanager']				= 'Einstellungen Dateimanager';
$cms_lang['setuse_upl_path']					= 'Startverzeichnis Dateimanager';
$cms_lang['setuse_upl_htmlpath']				= 'Startverzeichnis HTML-Pfad';
$cms_lang['setuse_fm_multi_language_support']	= 'Mehrsprachige Verwaltung (1=ja, 0=nein)';
$cms_lang['setuse_fm_forbidden_files']			= 'Verbotene Dateiendungen';
$cms_lang['setuse_fm_forbidden_directories']	= 'Verbotene Verzeichnisnamen';
$cms_lang['setuse_thumb_size']					= 'Gr&ouml;&szlig;e der Vorschaubilder';
$cms_lang['setuse_thumb_aspectratio']			= 'Proportionen beibehalten (0=nein, 1=ja, 2=Y&nbsp;skaliert, 3=X&nbsp;skaliert, 4=Quadratisch&nbsp;zugeschnitten)';
$cms_lang['setuse_more_thumb_size']				= 'Gr&ouml;&szlig;en weiterer Vorschaubilder (kommasepariert)';
$cms_lang['setuse_more_thumb_aspect_ratio']		= 'Weiterere Proportionen beibehalten (kommasepariert; 0=nein, 1=ja, 2=Y&nbsp;skaliert, 3=X&nbsp;skaliert, 4=Quadratisch&nbsp;zugeschnitten)';
$cms_lang['setuse_thumb_ext']					= 'Dateikennung f&uuml;r generierte Thumbnails';
$cms_lang['setuse_generate_thumb']				= 'Thumbnails generieren f&uuml;r (falls m&ouml;glich)';
$cms_lang['setuse_fm_delete_ignore_404']		= 'Fehlermeldung beim L&ouml;schen von fehlenden Dateien ignorieren (1=ja, 0=nein)';
$cms_lang['setuse_fm_remove_files_404']			= 'Verwaiste Dateieintr&auml;ge bei Datenbankabgleich l&ouml;schen (1=ja, 0=nein)';
$cms_lang['setuse_fm_remove_empty_directories']	= 'Leere Dateiverzeichnisse beim Abgleichen l&ouml;schen (1=ja, 0=nein)';
$cms_lang['setuse_fm_allow_invalid_dirnames']	= 'Verzeichnisse mit ung&uuml;ltigem Namen zulassen (1=ja, 0=nein, 2=falls m&ouml;glich korrigieren)';
$cms_lang['setuse_fm_allow_invalid_filenames']	= 'Dateien mit ung&uuml;ltigem Dateinamen zulassen (1=ja, 0=nein, 2=falls m&ouml;glich korrigieren)';
$cms_lang['setuse_fm_allowed_files']			= 'Erlaubte Dateiendungen (entkr&auml;ftet "'.$cms_lang['setuse_fm_forbidden_files'].'")';

$cms_lang['setuse_stylesheet']				= 'Einstellungen Stylesheet';
$cms_lang['setuse_css_multi_language_support']	= $cms_lang['setuse_fm_multi_language_support'];
$cms_lang['setuse_css_forbidden_files']			= $cms_lang['setuse_fm_forbidden_files'];
$cms_lang['setuse_css_forbidden_directories']	= $cms_lang['setuse_fm_forbidden_directories'];
$cms_lang['setuse_css_delete_ignore_404']		= $cms_lang['setuse_fm_delete_ignore_404'];
$cms_lang['setuse_css_remove_files_404']		= $cms_lang['setuse_fm_remove_files_404'];
$cms_lang['setuse_css_remove_empty_directories']= $cms_lang['setuse_fm_remove_empty_directories'];
$cms_lang['setuse_css_allow_invalid_dirnames']	= $cms_lang['setuse_fm_allow_invalid_dirnames'];
$cms_lang['setuse_css_allow_invalid_filenames']	= $cms_lang['setuse_fm_allow_invalid_filenames'];
$cms_lang['setuse_css_allowed_files']			= $cms_lang['setuse_fm_allowed_files'];

$cms_lang['setuse_javascript']				= 'Einstellungen Javascript';
$cms_lang['setuse_js_multi_language_support']	= $cms_lang['setuse_fm_multi_language_support'];
$cms_lang['setuse_js_forbidden_files']			= $cms_lang['setuse_fm_forbidden_files'];
$cms_lang['setuse_js_forbidden_directories']	= $cms_lang['setuse_fm_forbidden_directories'];
$cms_lang['setuse_js_delete_ignore_404']		= $cms_lang['setuse_fm_delete_ignore_404'];
$cms_lang['setuse_js_remove_files_404']			= $cms_lang['setuse_fm_remove_files_404'];
$cms_lang['setuse_js_remove_empty_directories']	= $cms_lang['setuse_fm_remove_empty_directories'];
$cms_lang['setuse_js_allow_invalid_dirnames']	= $cms_lang['setuse_fm_allow_invalid_dirnames'];
$cms_lang['setuse_js_allow_invalid_filenames']	= $cms_lang['setuse_fm_allow_invalid_filenames'];
$cms_lang['setuse_js_allowed_files']			= $cms_lang['setuse_fm_allowed_files'];


$cms_lang['set_meta']						= 'Metaangaben vorkonfigurieren';
$cms_lang['set_meta_description']				= 'Seitenbeschreibung';
$cms_lang['set_meta_keywords']					= 'Suchbegriffe';
$cms_lang['set_meta_robots']					= 'Suchmaschinenanweisung';

$cms_lang['set_db_cache']					= 'Datenbank-Cache konfigurieren';
$cms_lang['set_db_cache_enabled']				= 'Datenbank-Cache benutzen';
$cms_lang['set_db_cache_name']					= 'Datenbank-Cache name';
$cms_lang['set_db_cache_groups']				= 'Cache-Gruppen konfigurieren (in sec.)';
$cms_lang['set_db_cache_group_default']			= 'Cache-Gruppe "Default"';
$cms_lang['set_db_cache_group_standard']		= 'Cache-Gruppe "Standard"';
$cms_lang['set_db_cache_group_frontend']		= 'Cache-Gruppe "Frontend"';
$cms_lang['set_db_cache_items']					= 'Cache-Items konfigurieren (in sec.)';
$cms_lang['set_db_cache_item_tree']				= 'Cache-Item "Frontend-Ordner & Seitenstruktur"';
$cms_lang['set_db_cache_item_content']			= 'Cache-Item "Frontend-Seitencontent"';

$cms_lang['set_logs_storage']				= 'Logging im Frontend';
$cms_lang['set_logs_storage_screen']			= 'Auf Bildschirm ausgeben';
$cms_lang['set_logs_storage_logfile']			= 'In Logdatei speichern';
$cms_lang['set_logs_storage_database']			= 'In Datenbank speichern';


?>