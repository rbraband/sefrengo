<?php

if (!defined('CMS_CONFIGFILE_INCLUDED')) 
{
    die('NO CONFIGFILE FOUND');
}

// Navigation
$cms_lang['nav_1_0'] = 'Redaktion';
$cms_lang['nav_1_1'] = 'Seiten';
$cms_lang['nav_1_2'] = 'Dateimanager';

$cms_lang['nav_2_0'] = 'Entwicklung';
$cms_lang['nav_2_1'] = 'Layouts';
$cms_lang['nav_2_2'] = 'Stylesheet';
$cms_lang['nav_2_3'] = 'Javascript';
$cms_lang['nav_2_4'] = 'Module';
$cms_lang['nav_2_5'] = 'Templates';

$cms_lang['nav_3_0'] = 'Administration';
$cms_lang['nav_3_1'] = 'Benutzer';
$cms_lang['nav_3_2'] = 'Gruppen';
$cms_lang['nav_3_3'] = 'Projekte';
$cms_lang['nav_3_4'] = 'System';
$cms_lang['nav_3_5'] = 'Plugins';
$cms_lang['nav_3_6'] = 'Logs';

$cms_lang['nav_4_0'] = 'Plugins';

$cms_lang['login_pleaselogin'] = 'Bitte geben Sie Ihren Benutzernamen & Ihr Kennwort ein.';
$cms_lang['login_username'] = 'Benutzername';
$cms_lang['login_password'] = 'Kennwort';
$cms_lang['login_invalidlogin'] = 'Entweder ist Ihr Benutzername oder Ihr Kennwort ungültig. Bitte versuchen Sie es nochmal!';
$cms_lang['login_logininuse'] = 'Ihr Account ist zur Zeit in Benutzung.<br>Bitte versuchen Sie es später nochmal!';
$cms_lang['login_challenge_fail'] = 'Ihre Challenge ist fehlgeschlagen.<br>Bitte versuchen Sie es nochmal!';

$cms_lang['login_nolang'] = 'Ihnen wurde noch keine Sprache zugewiesen.';
$cms_lang['login_nojs'] = 'Bitte aktivieren Sie Javascript in Ihrem Browser, damit das Backend korrekt funktioniert.';
$cms_lang['login_licence'] = '&copy; <a href="http://www.sefrengo.org" target="_blank">sefrengo.org</a>. This is free software, and you may redistribute it under the GPL V2. Sefrengo&reg; comes with absolutely no warranty; for details, see the <a href="license.html" target="_blank">license</a>.';

$cms_lang['logout_thanksforusingcms'] = 'Vielen Dank, dass sie "Sefrengo" benutzt haben. Sie werden in wenigen Sekunden zum Login weitergeleitet.';
$cms_lang['logout_youareloggedout'] = 'Sie sind jetzt abgemeldet.';
$cms_lang['logout_backtologin1'] = 'Hier kommen Sie wieder zur';
$cms_lang['logout_backtologin2'] = 'Anmeldung';

$cms_lang['area_mod'] = $cms_lang['nav_2_0'] . ' &rsaquo; Installierte ' . $cms_lang['nav_2_4'];
$cms_lang['area_mod_new'] = $cms_lang['nav_2_0'] . ' &rsaquo; Neues Modul';
$cms_lang['area_mod_edit'] = $cms_lang['nav_2_0'] . ' &rsaquo; Modul bearbeiten';
$cms_lang['area_mod_edit_sql'] = $cms_lang['nav_2_0'] . ' &rsaquo; Modul-SQL bearbeiten';
$cms_lang['area_mod_config'] = $cms_lang['nav_2_0'] . ' &rsaquo; Modul konfigurieren';
$cms_lang['area_mod_import'] = $cms_lang['nav_2_0'] . ' &rsaquo; Modul importieren';
$cms_lang['area_mod_duplicate'] = $cms_lang['nav_2_0'] . ' &rsaquo; Modul kopieren';
$cms_lang['area_mod_xmlimport'] = $cms_lang['nav_2_0'] . ' &rsaquo; Modul aus XML importieren';
$cms_lang['area_mod_xmlexport'] = $cms_lang['nav_2_0'] . ' &rsaquo; Modul als XML exportieren';
$cms_lang['area_mod_database'] = $cms_lang['area_mod_import'] . '&rsaquo; Datenbank';
$cms_lang['area_mod_repository'] = $cms_lang['area_mod_import'] . '&rsaquo; Repository';

$cms_lang['area_plug'] = $cms_lang['nav_3_0'] . ' &rsaquo; Installierte ' . $cms_lang['nav_3_5'];
$cms_lang['area_plug_new'] = $cms_lang['nav_3_0'] . ' &rsaquo; Neues Plugin';
$cms_lang['area_plug_new_import'] = $cms_lang['area_plug_new'] . ' importieren';
$cms_lang['area_plug_new_create'] = $cms_lang['area_plug_new'] . ' erzeugen';
$cms_lang['area_plug_edit'] = $cms_lang['nav_3_0'] . ' &rsaquo; Plugin bearbeiten';
$cms_lang['area_plug_edit_sql'] = $cms_lang['nav_3_0'] . ' &rsaquo; Plugin-Meta bearbeiten';
$cms_lang['area_plug_config'] = $cms_lang['nav_3_0'] . ' &rsaquo; Plugin konfigurieren';
$cms_lang['area_plug_import'] = $cms_lang['nav_3_0'] . ' &rsaquo; Plugin importieren';
$cms_lang['area_plug_folder'] = $cms_lang['area_plug_import'] . '&rsaquo; Verzeichnis';
$cms_lang['area_plug_repository'] = $cms_lang['area_plug_import'] . '&rsaquo; Repository';

$cms_lang['area_con'] = $cms_lang['nav_1_0'] . ' &rsaquo; ' . $cms_lang['nav_1_1'];
$cms_lang['area_con_configcat'] = $cms_lang['nav_1_0'] . ' &rsaquo; Ordner konfigurieren';
$cms_lang['area_con_configside'] = $cms_lang['nav_1_0'] . ' &rsaquo; Seite konfigurieren';
$cms_lang['area_con_edit'] = $cms_lang['nav_1_0'] . ' &rsaquo; Seite bearbeiten';

$cms_lang['area_upl'] = $cms_lang['nav_1_0'] . ' &rsaquo; ' . $cms_lang['nav_1_2'];

$cms_lang['area_lay'] = $cms_lang['nav_2_0'] . ' &rsaquo; ' . $cms_lang['nav_2_1'];
$cms_lang['area_lay_edit'] = $cms_lang['nav_2_0'] . ' &rsaquo; Layout bearbeiten';

$cms_lang['area_css'] = $cms_lang['nav_2_0'] . ' &rsaquo; ' . $cms_lang['nav_2_2'];
$cms_lang['area_css_edit'] = $cms_lang['nav_2_0'] . ' &rsaquo; ' . $cms_lang['nav_2_2'] . ' &rsaquo; CSS-Regel bearbeiten';
$cms_lang['area_css_edit_file'] = $cms_lang['nav_2_0'] . ' &rsaquo; ' . $cms_lang['nav_2_2'] . ' &rsaquo; CSS-Datei bearbeiten';
$cms_lang['area_css_new_file'] = $cms_lang['nav_2_0'] . ' &rsaquo; ' . $cms_lang['nav_2_2'] . ' &rsaquo; CSS-Datei anlegen';
$cms_lang['area_css_import'] = $cms_lang['nav_2_0'] . ' &rsaquo; ' . $cms_lang['nav_2_2'] . ' &rsaquo; CSS-Regeln importieren';

$cms_lang['area_js'] = $cms_lang['nav_2_0'] . ' &rsaquo; ' . $cms_lang['nav_2_3'];
$cms_lang['area_js_edit_file'] = $cms_lang['nav_2_0'] . ' &rsaquo; ' . $cms_lang['nav_2_3'] . ' &rsaquo; Javascript-Datei bearbeiten';
$cms_lang['area_js_import'] = $cms_lang['nav_2_0'] . ' &rsaquo; ' . $cms_lang['nav_2_3'] . ' &rsaquo; Javascript-Datei importieren';

$cms_lang['area_tpl'] = $cms_lang['nav_2_0'] . ' &rsaquo; ' . $cms_lang['nav_2_5'];
$cms_lang['area_tpl_edit'] = $cms_lang['nav_2_0'] . ' &rsaquo; Template bearbeiten';

$cms_lang['area_user'] = $cms_lang['nav_3_0'] . ' &rsaquo; ' . $cms_lang['nav_3_1'];
$cms_lang['area_user_edit'] = $cms_lang['area_user'] . ' bearbeiten';
$cms_lang['area_group'] = $cms_lang['nav_3_0'] . ' &rsaquo; ' . $cms_lang['nav_3_2'];
$cms_lang['area_group_edit'] = $cms_lang['nav_3_0'] . ' &rsaquo; Gruppe bearbeiten';
$cms_lang['area_group_config'] = $cms_lang['nav_3_0'] . ' &rsaquo; Gruppe konfigurieren';
$cms_lang['area_lang'] = $cms_lang['nav_3_0'] . ' &rsaquo; ' . $cms_lang['nav_3_3'];
$cms_lang['area_settings'] = $cms_lang['nav_3_0'] . ' &rsaquo; Systemeinstellungen';
$cms_lang['area_settings_general'] = $cms_lang['nav_3_0'] . ' &rsaquo; ' . $cms_lang['nav_3_4'];

$cms_lang['lay_action'] = 'Aktionen';
$cms_lang['lay_edit'] = 'Bearbeiten';
$cms_lang['lay_export'] = 'Layout exportieren';
$cms_lang['lay_defaultname'] = 'Neues Layout';
$cms_lang['lay_delete'] = 'Löschen';
$cms_lang['lay_import'] = 'Layout importieren';
$cms_lang['lay_layoutname'] = 'Layoutname';
$cms_lang['lay_description'] = 'Beschreibung';
$cms_lang['lay_doctype'] = 'Doctype';
$cms_lang['lay_doctype_autoinsert'] = 'Doctype automatisch an den Anfang des Layouts einfügen';
$cms_lang['lay_doctype_none'] = 'Keiner';
$cms_lang['lay_code'] = 'Quellcode';
$cms_lang['lay_new'] = 'Neues Layout';
$cms_lang['lay_nolayouts'] = 'Es gibt keine Layouts.';
$cms_lang['lay_cmshead'] = 'Meta-Dateien';
$cms_lang['lay_css'] = 'Stylesheet';
$cms_lang['lay_js'] = 'Javascript';
$cms_lang['lay_nofile'] = 'keine Datei';
$cms_lang['lay_duplicate'] = 'Layout duplizieren';
$cms_lang['lay_copy_of'] = 'Kopie von ';

$cms_lang['tpl_action'] = 'Aktionen';
$cms_lang['tpl_config'] = 'Template-Angaben';
$cms_lang['tpl_actions']['10'] = 'Neues Template';
$cms_lang['tpl_edit'] = 'Bearbeiten';
$cms_lang['tpl_defaultname'] = 'Neues Template';
$cms_lang['tpl_is_start'] = 'Als Starttemplate festlegen';
$cms_lang['tpl_delete'] = 'Löschen';
$cms_lang['tpl_templatename'] = 'Template-Name';
$cms_lang['tpl_description'] = 'Beschreibung';
$cms_lang['tpl_container'] = 'Container';
$cms_lang['tpl_notemplates'] = 'Es gibt keine Templates.';
$cms_lang['tpl_layout'] = 'Layout';
$cms_lang['tpl_duplicate'] = 'Template duplizieren';
$cms_lang['tpl_copy_of'] = 'Kopie von ';
$cms_lang['tpl_overwrite_all'] = 'Änderungen in den Templatekopien für die Ordner und Seiten übernehmen.';
$cms_lang['tpl_devmessage'] = 'Dies ist eine Entwicklerversion. Sie ist nicht für den produktiven Einsatz geeignet!';
$cms_lang['tpl_advanced'] = 'Erweitert';


$cms_lang['logfile_mail_subject']= 'Sefrengo: Maximale Dateigröße für Log-Datei Überschritten';
$cms_lang['logfile_mail_body'] = 'Die Log-Datei %s hat die angegebene Dateigröße von %d MB überschritten. Die Log-Datei wurde in %s umbenannt, um weiterhin Logs zu speichern. Bitte löschen Sie die umbenannte Datei aus dem Verzeichnis.';

$cms_lang['form_nothing'] = '--- kein ---';
$cms_lang['form_select'] = '--- bitte wählen ---';

$cms_lang['gen_back'] = 'Zurück';
$cms_lang['gen_abort'] = 'Abbrechen';
$cms_lang['gen_sort'] = 'Einträge sortieren';
$cms_lang['gen_default'] = 'Default';
$cms_lang['gen_welcome'] = 'Willkommen';
$cms_lang['gen_licence'] = 'Lizenz';
$cms_lang['gen_logout'] = 'Logout';
$cms_lang['gen_deletealert'] = 'Wirklich löschen?';
$cms_lang['gen_deletealert_name'] = 'Möchten Sie \\\'\{name\}\\\' wirklich löschen?';
$cms_lang['gen_mod_active'] = 'Modul ist aktiviert';
$cms_lang['gen_mod_deactive'] = 'Modul ist deaktiviert';
$cms_lang['gen_mod_edit_allow'] = 'Seiteninhalt kann editiert werden';
$cms_lang['gen_mod_edit_disallow'] = 'Seiteninhalt kann nicht editiert werden';
$cms_lang['gen_rights'] = 'Rechte';
$cms_lang['gen_overide'] = 'Übernahme';
$cms_lang['gen_reinstall'] = 'Reinstallation';
$cms_lang['gen_expand'] = 'Erweitert';
$cms_lang['gen_parent'] = 'Abhängigkeiten';
$cms_lang['gen_delete'] = 'Löschen';
$cms_lang['gen_update'] = 'Updaten';
$cms_lang['gen_fundamental'] = 'Grundeinstellung';
$cms_lang['gen_configuration'] = 'Konfiguration';
$cms_lang['gen_version'] = 'Version';
$cms_lang['gen_description'] = 'Beschreibung';
$cms_lang['gen_verbosename'] = 'Alternativ';
$cms_lang['gen_name'] = 'Name';
$cms_lang['gen_author'] = 'Author';
$cms_lang['gen_cat'] = 'Kategorie';
$cms_lang['gen_original'] = 'Original';
$cms_lang['gen_font'] = 'Schriftart';
$cms_lang['gen_errorfont'] = 'Schriftart für Fehlermeldungen';
$cms_lang['gen_inputformfont'] = 'Schriftart für die Eingabefelder';
$cms_lang['gen_picforsend'] = 'Bild für den Sendebutton';
$cms_lang['gen_select'] = 'Auswahlmöglichkeiten';
$cms_lang['gen_select_actions'] = 'Aktionen&hellip;';
$cms_lang['gen_select_view'] = 'Ansicht&hellip;';
$cms_lang['gen_select_change_to'] = 'Wechseln zu&hellip;';
$cms_lang['gen_logout_wide'] = 'Logout';
$cms_lang['gen_reset'] = 'Zurücksetzten';
$cms_lang['gen_loading'] = 'Laden';
$cms_lang['gen_update_view'] = 'Ansicht aktualisieren&hellip;';
$cms_lang['gen_show_params'] = 'Parameter anzeigen';
$cms_lang['gen_hide_params'] = 'Parameter ausblenden';
$cms_lang['gen_no_selection'] = 'Keine Auswahl';

$cms_lang['gen_save'] = 'Speichern';
$cms_lang['gen_apply'] = 'Übernehmen';
$cms_lang['gen_cancel'] = 'Abbrechen';

$cms_lang['gen_save_titletext'] = 'Daten speichern und zurück zur vorherigen Ansicht';
$cms_lang['gen_apply_titletext'] = 'Daten speichern und in dieser Ansicht bleiben';
$cms_lang['gen_cancel_titletext'] = 'Abbrechen und zurück zur vorherigen Ansicht';

$cms_lang['gen_permission_denied'] = 'Nicht genügend Rechte um die Aktion auszuführen!';

$cms_lang['gen_select'] = 'Auswählen: ';
$cms_lang['gen_select_all'] = 'Alle';
$cms_lang['gen_select_none'] = 'Keine';

$cms_lang['gen_search'] = 'Suche starten';
$cms_lang['gen_searchterms'] = 'Suchbegriffe';
$cms_lang['gen_advanced_search'] = 'Erweiterte Suche';
$cms_lang['gen_page'] = 'Seite ';
$cms_lang['gen_from'] = ' von ';
$cms_lang['gen_filter_show'] = 'Ansicht gefiltert nach ';
$cms_lang['gen_filter_reset'] = 'Zurücksetzen';

$cms_lang['gen_show_calendar'] = 'Kalender anzeigen';

//contentflex
$cms_lang['cf_add_first_pos'] = 'Als erstes Element einfügen';
$cms_lang['cf_insert_p1'] = 'Nach Element';
$cms_lang['cf_insert_p2'] = 'einfügen';

//type flyout menus
$cms_lang['link_extern'] = 'externer Link:';
$cms_lang['link_intern'] = 'oder interner Link:';
$cms_lang['link_blank'] = 'Link in einem neuen Browserfenster öffnen';
$cms_lang['link_self'] = 'Link im gleichen Browserfenster öffnen';
$cms_lang['link_parent'] = 'Link sprengt aktuelles Frame';
$cms_lang['link_top'] = 'Link sprengt alle Frames';
$cms_lang['link_edit'] = 'Link bearbeiten';

$cms_lang['type_text'] = 'Text';
$cms_lang['type_textarea'] = 'Textarea';
$cms_lang['type_wysiwyg'] = 'WYSIWYG';
$cms_lang['type_link'] = 'Link';
$cms_lang['type_link_name'] = 'Linkname';
$cms_lang['type_link_target'] = 'Zielfenster';
$cms_lang['type_file'] = 'Dateiauswahl';
$cms_lang['type_file_desc'] = 'Beschreibung';
$cms_lang['type_file_target'] = 'Zielfenster';
$cms_lang['type_image'] = 'Bild';
$cms_lang['type_image_desc'] = 'Bildbeschreibung';
$cms_lang['type_sourcecode'] = 'Sourcecode';
$cms_lang['type_select'] = 'Select';
$cms_lang['type_hidden'] = 'Hidden';
$cms_lang['type_checkbox'] = 'Checkbox';
$cms_lang['type_radio'] = 'Radio';
$cms_lang['type_date'] = 'Datum/ Zeit';

$cms_lang['type_typegroup'] = 'Content';
$cms_lang['type_container'] = 'Content';
$cms_lang['type_edit_container'] = 'Modul';
$cms_lang['type_edit_side'] = 'Seite';
$cms_lang['type_edit_folder'] = 'Ordner';
$cms_lang['type_save'] = 'speichern';
$cms_lang['type_edit'] = 'bearbeiten';
$cms_lang['type_new'] = 'neu';
$cms_lang['side_new'] = 'anlegen';
$cms_lang['side_config'] = 'konfigurieren';
$cms_lang['side_mode'] = 'Ansichtmodus';
$cms_lang['side_publish'] = 'publizieren';
$cms_lang['side_delete'] = 'löschen';
$cms_lang['side_edit'] = 'Seite bearbeiten';
$cms_lang['side_overview'] = 'Seitenübersicht';
$cms_lang['side_preview'] = 'Vorschau';
$cms_lang['type_delete'] = 'löschen';
$cms_lang['type_up'] = 'nach oben';
$cms_lang['type_down'] = 'nach unten';

$cms_lang['img_edit'] = 'Bild ändern';
$cms_lang['imgdescr_edit'] = 'Bildbeschreibung ändern';
$cms_lang['link_intern'] = 'interner Link';
$cms_lang['link_extern'] = 'externer Link';


$cms_lang['title_rp_popup'] = 'Rechte bearbeiten';


// 01xx = Con
$cms_lang['err_0101'] = 'Bitte prüfen Sie Ihre Formulareingaben';

// 02xx = Str
$cms_lang['err_0201'] = 'Der zu löschende Ordner hat noch Unterordner. Löschen nicht möglich.';
$cms_lang['err_0202'] = 'Es gibt noch Seiten in diesem Ordner. Löschen nicht möglich.';

// 03xx = Lay
$cms_lang['err_0301'] = 'Layout wird verwendet. Löschen nicht möglich.';
$cms_lang['err_0302'] = '<font color="black">Layout wurde erfolgreich kopiert.</font>';

// 05xx = Tpl
$cms_lang['err_0501'] = 'Template wird verwendet. Löschen nicht möglich.';

// 06xx = Dis
$cms_lang['err_0601'] = 'Seite konnte nicht erzeugt werden. Weisen Sie allen Ordnern ein Template zu.';

// 08xx = Lang
$cms_lang['err_0801'] = 'Es existiert bereits eine Sprache mit diesem Namen. Es wurde keine Sprache angelegt.';
$cms_lang['err_0802'] = 'Es gibt noch Seiten, die online sind oder Ordner, die sichtbar sind. Wenn sie diese Sprache bei diesem Projekt wirklich löschen wollen, dann setzten sie alle Seiten offline und schalten sie alle Ordner auf unsichtbar.<br> VORSICHT/BEMERKUNG: Löschen ist nicht rückgängig zu machen.';

// 09xx = Projekte
$cms_lang['err_0901'] = '';
$cms_lang['err_0902'] = '';

// 13xx = SQL-Schicht
$cms_lang['err_1310'] = 'Zu wenige Werte für ein INSERT-Query!';
$cms_lang['err_1320'] = 'Zu wenige Werte für eine UPDATE-Query!';
$cms_lang['err_1321'] = 'WHERE-Angabe fehlt. UPDATE wird nicht durchgeführt.';
$cms_lang['err_1330'] = 'Zu wenige Werte für eine DELETE-Query!';
$cms_lang['err_1331'] = 'WHERE-Angabe fehlt. DELETE wird nicht durchgeführt.';
$cms_lang['err_1340'] = 'Zu wenige Werte für eine SELECT-Query!';
$cms_lang['err_1350'] = 'Keine Parameter für die Erstellung von SQL-Daten übergeben!';
$cms_lang['err_1360'] = 'Datenbank Fehler. Die Daten betreffenden Datensätze sind inkonsitent!';

// 15xx = Repository
$cms_lang['err_1500'] = 'Repository Fehler. Funktion wird nicht ausgeführt!';
$cms_lang['err_1501'] = 'Repository Fehler. Datei existiert nicht!';
$cms_lang['err_1502'] = 'Repository Fehler. XML-Format unbekannt!';
$cms_lang['err_1503'] = 'Repository Fehler. XML-Format fehlerhaft!';
$cms_lang['err_1504'] = 'Repository Fehler. XML-Parser kann nicht gestartet werden!';
$cms_lang['err_1505'] = 'Repository Fehler. Kann Datei nicht schreiben!';
$cms_lang['err_1506'] = 'Repository Fehler. Kann Datei nicht lesen!';
$cms_lang['err_1507'] = 'Repository Fehler. Repository-Id doppelt!';
$cms_lang['err_1508'] = 'Repository Fehler. Repository kann Datenbank nicht lesen!';
$cms_lang['err_1509'] = 'Repository Fehler. Kann Klasse nicht laden!';
$cms_lang['err_1510'] = 'Repository Fehler. Kann Datei nicht löschen!';

// 17xx = Allgemeine Rechtefehler
$cms_lang['err_1701'] = 'Keine ausreichenden Rechte für diese Funktion!';

// 18xx = Konfigurationsfehler
$cms_lang['err_1800'] = 'Allgemeiner Konfigurationsfehler. Setup neu ausführen!';
$cms_lang['err_1801'] = 'Safe-Mode ist aktiviert. Die Funktion kann nicht ausgeführt werden!';


$cms_lang['err_rw_01'] = 'Dieser Alias enthält keine oder nicht erlaubte Zeichen! Erlaubte Zeichen sind: "a-z0-9_-.,"! Ein führender "/", sowie zwei oder mehr aufeinander folgende "/" sind ebenfalls nicht erlaubt!';
$cms_lang['err_rw_02'] = 'Dieser Alias wurde schon für eine andere Seite oder einen anderen Ordner vergeben!';
$cms_lang['err_rw_03'] = 'Dieser URL- Alias entspricht der URL einer anderen Seite oder eines anderen Ordners!';

$cms_lang['gen_copy_pagecontent_from_lang'] = "Sollen wirklich alle Inhalte aus der Sprache";
$cms_lang['gen_copy_pagecontent_from_lang2'] = "in die aktuelle Seite übernommen werden?";
$cms_lang['gen_copy_pagecontent_from_lang3'] = "Alle derzeitigen Inhalte der Seite werden gelöscht!";
$cms_lang['gen_copy_pagecontent_from_lang4'] = "Dieser Vorgang kann nicht rückgängig gemacht werden!";
?>
