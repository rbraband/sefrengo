<?php
if(! defined('CMS_CONFIGFILE_INCLUDED'))
{
	die('NO CONFIGFILE FOUND');
}

$cms_lang['set_action'] = 'Aktionen';
$cms_lang['set_submit'] = 'Speichern';
$cms_lang['set_cancel'] = 'Abbrechen';
$cms_lang['set_edit'] = 'Bearbeiten';

// PATHES
$cms_lang['set_path_title'] = 'Pfadangaben';
$cms_lang['set_path_base'] = 'Basispfad (absolut)';
$cms_lang['set_path_base_http'] = 'HTTP Basispfad, Variablen: {%http_host}';
$cms_lang['set_path_backend_rel'] = 'Serverpfad zum Backend (relativ)';

// TEMPLATES AND LANGUAGES
$cms_lang['set_tpl_lang_title'] = 'Templates und Sprachen';
$cms_lang['set_backend_lang'] = 'Sprache des Backends (de=deutsch, en=englisch, fr=französisch)';
$cms_lang['set_skin'] = 'Skin';

// SESSION
$cms_lang['set_session_title'] = 'Session';
$cms_lang['set_session_backend_lifetime'] = 'Lebenszeit Session in Minuten';
$cms_lang['set_session_backend_domain'] = 'Session Domain';

// DATE AND TIME
$cms_lang['set_datetime_title'] = 'Datum und Zeit';
$cms_lang['set_format_date'] = 'Datumsformat';
$cms_lang['set_format_time'] = 'Zeitformat';

// CACHE
$cms_lang['set_cache_title'] = 'Cache';
$cms_lang['set_backend_cache'] = 'Browsern das Cachen von Backendseiten verbieten';
$cms_lang['set_gzip'] = 'Alle Seiten gzip komprimieren';
$cms_lang['set_db_cache_enabled'] = 'Datenbank-Cache benutzen';
$cms_lang['set_db_optimice_tables_enable'] = 'Datenbank Optimierung benutzen';

// FILEMANAGER
$cms_lang['set_fm_title'] = 'Dateimanager';
$cms_lang['set_chmod_value'] = 'CHMOD Basiswert (oktal, z.B. 777)';
$cms_lang['set_chmod_enabled'] = 'CHMOD auf Uploads anwenden (0=nein, 1=ja)';

// LOGGING
$cms_lang['set_logging_title'] = 'Logging';
$cms_lang['set_logs_storage_screen'] = 'Auf Bildschirm ausgeben';
$cms_lang['set_logs_storage_logfile'] = 'In Logdatei speichern';
$cms_lang['set_logs_storage_database'] = 'In Datenbank speichern';

// MISC
$cms_lang['set_misc_title'] = 'Sonstiges';
$cms_lang['set_manipulate_output'] = 'Backendausgabe manipulieren';
$cms_lang['set_paging_items_per_page'] = 'Angezeigte Einträge pro Seite, wenn Paging unterstützt wird';
$cms_lang['set_enable_code_editor'] = 'Quellcode-Editor mit Syntaxhervorhebung verwenden';

?>