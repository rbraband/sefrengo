<?php
if(! defined('CMS_CONFIGFILE_INCLUDED'))
{
    die('NO CONFIGFILE FOUND');
}

// Texte für Rechtepanel
$cms_lang['panel_grouphead'] = "Rechte für Gruppen und Benutzer";
$cms_lang['panel_rechte'] = "Rechte";
$cms_lang['panel_granted'] = "Erlaubt";
$cms_lang['panel_denied'] = "Verboten";
$cms_lang['panel_savebutton'] = "Übernehmen";
$cms_lang['panel_addbutton'] = "Neue User/Gruppen";
$cms_lang['panel_closebutton'] = "Abbrechen";
$cms_lang['panel_closebutton2'] = "Okay";
$cms_lang['panel_usergroups'] = "Benutzergruppen";
$cms_lang['panel_user'] = "Benutzer";
$cms_lang['panel_reste_perms'] = "Vorhandene Rechte zurücksetzen";
$cms_lang['panel_inherit_perms'] = "Rechte vom Vorgänger erben";

// area_frontend
$cms_lang['group_area_frontend'] = 'Bereich Frontend';
$cms_lang['group_area_frontend_2'] = 'Geschützte Ordner im Frontend anzeigen';
$cms_lang['group_area_frontend_18'] = 'Geschützte Seiten im Frontend anzeigen';
$cms_lang['group_area_frontend_19'] = 'Darf interaktive Inhalte bearbeiten';
//frontendcat
$cms_lang['group_frontendcat_2'] = 'Geschützte Ordner im Frontend anzeigen';
$cms_lang['group_frontendcat_18'] = 'Geschützte Seiten im Frontend anzeigen';
$cms_lang['group_frontendcat_19'] = 'Darf interaktive Inhalte bearbeiten';
//frontendpage
$cms_lang['group_frontendpage_18'] = 'Geschützte Seite im Frontend anzeigen';
$cms_lang['group_frontendpage_19'] = 'Darf interaktive Inhalte bearbeiten';

// area_backend
$cms_lang['group_area_backend'] = 'Bereich Backend';
$cms_lang['group_area_backend_1'] = 'Backend anzeigen';

//area_con
$cms_lang['group_area_con'] = 'Bereich Redaktion / Seiten';
$cms_lang['group_area_con_1'] = 'Kategorie anzeigen';
$cms_lang['group_area_con_2'] = 'Kategorie anlegen';
$cms_lang['group_area_con_3'] = 'Kategorie bearbeiten';
$cms_lang['group_area_con_5'] = 'Kategorie löschen';
$cms_lang['group_area_con_6'] = 'Kategorie Backendrechte vergeben';
$cms_lang['group_area_con_7'] = 'Kategorie online/ offline schalten/ publizieren';
$cms_lang['group_area_con_8'] = 'Kategorie schützen';
$cms_lang['group_area_con_9'] = 'Kategorie sortieren';
$cms_lang['group_area_con_11'] = 'Kategorietemplate konfigurieren';
$cms_lang['group_area_con_14'] = 'Kategorie + Seite Frontendrechte vergeben';
$cms_lang['group_area_con_15'] = 'Kategorie Alias vergeben';
$cms_lang['group_area_con_17'] = 'Seite anzeigen';
$cms_lang['group_area_con_18'] = 'Seite anlegen';
$cms_lang['group_area_con_19'] = 'Seite bearbeiten';
$cms_lang['group_area_con_20'] = 'Seite konfigurieren';
$cms_lang['group_area_con_21'] = 'Seite löschen';
$cms_lang['group_area_con_22'] = 'Seite Backendrechte vergeben';
$cms_lang['group_area_con_23'] = 'Seite online/ offline schalten / publizieren';
$cms_lang['group_area_con_24'] = 'Seite schützen';
$cms_lang['group_area_con_25'] = 'Seite sortieren';
$cms_lang['group_area_con_26'] = 'Seitentemplate auswählen';
$cms_lang['group_area_con_27'] = 'Seitentemplate konfigurieren';
$cms_lang['group_area_con_28'] = 'Seite Startseite festlegen';
$cms_lang['group_area_con_29'] = 'Metaangaben bearbeiten';
$cms_lang['group_area_con_30'] = 'Seite verschieben/ klonen';
$cms_lang['group_area_con_31'] = 'Seiten URL vergeben';
//cat
$cms_lang['group_cat_1'] = $cms_lang['group_area_con_1'];
$cms_lang['group_cat_2'] = $cms_lang['group_area_con_2'];
$cms_lang['group_cat_3'] = $cms_lang['group_area_con_3'];
$cms_lang['group_cat_5'] = $cms_lang['group_area_con_5'];
$cms_lang['group_cat_6'] = $cms_lang['group_area_con_6'];
$cms_lang['group_cat_7'] = $cms_lang['group_area_con_7'];
$cms_lang['group_cat_8'] = $cms_lang['group_area_con_8'];
$cms_lang['group_cat_9'] = $cms_lang['group_area_con_9'];
$cms_lang['group_cat_11'] = $cms_lang['group_area_con_11'];
$cms_lang['group_cat_14'] = $cms_lang['group_area_con_14'];
$cms_lang['group_cat_15'] = $cms_lang['group_area_con_15'];
$cms_lang['group_cat_17'] = $cms_lang['group_area_con_17'];
$cms_lang['group_cat_18'] = $cms_lang['group_area_con_18'];
$cms_lang['group_cat_19'] = $cms_lang['group_area_con_19'];
$cms_lang['group_cat_20'] = $cms_lang['group_area_con_20'];
$cms_lang['group_cat_21'] = $cms_lang['group_area_con_21'];
$cms_lang['group_cat_22'] = $cms_lang['group_area_con_22'];
$cms_lang['group_cat_23'] = $cms_lang['group_area_con_23'];
$cms_lang['group_cat_24'] = $cms_lang['group_area_con_24'];
$cms_lang['group_cat_25'] = $cms_lang['group_area_con_25'];
$cms_lang['group_cat_26'] = $cms_lang['group_area_con_26'];
$cms_lang['group_cat_27'] = $cms_lang['group_area_con_27'];
$cms_lang['group_cat_28'] = $cms_lang['group_area_con_28'];
$cms_lang['group_cat_29'] = $cms_lang['group_area_con_29'];
$cms_lang['group_cat_30'] = $cms_lang['group_area_con_30'];
$cms_lang['group_cat_31'] = $cms_lang['group_area_con_31'];
//side
$cms_lang['group_side_17'] = $cms_lang['group_area_con_17'];
$cms_lang['group_side_19'] = $cms_lang['group_area_con_19'];
$cms_lang['group_side_20'] = $cms_lang['group_area_con_20'];
$cms_lang['group_side_21'] = $cms_lang['group_area_con_21'];
$cms_lang['group_side_22'] = $cms_lang['group_area_con_22'];
$cms_lang['group_side_23'] = $cms_lang['group_area_con_23'];
$cms_lang['group_side_24'] = $cms_lang['group_area_con_24'];
$cms_lang['group_side_25'] = $cms_lang['group_area_con_25'];
$cms_lang['group_side_26'] = $cms_lang['group_area_con_26'];
$cms_lang['group_side_27'] = $cms_lang['group_area_con_27'];
$cms_lang['group_side_28'] = $cms_lang['group_area_con_28'];
$cms_lang['group_side_29'] = $cms_lang['group_area_con_29'];
$cms_lang['group_side_30'] = $cms_lang['group_area_con_30'];
$cms_lang['group_side_31'] = $cms_lang['group_area_con_31'];

// area_lay
$cms_lang['group_area_lay'] = 'Bereich Design / Layouts';
$cms_lang['group_area_lay_1'] = 'Layout anzeigen';
$cms_lang['group_area_lay_2'] = 'Layout erstellen';
$cms_lang['group_area_lay_3'] = 'Layout bearbeiten';
$cms_lang['group_area_lay_5'] = 'Layout löschen';
$cms_lang['group_area_lay_6'] = 'Layoutrechte vergeben';
$cms_lang['group_area_lay_7'] = 'Layout importieren';
$cms_lang['group_area_lay_8'] = 'Layout exportieren';
$cms_lang['group_lay_1'] = $cms_lang['group_area_lay_1'];
$cms_lang['group_lay_3'] = $cms_lang['group_area_lay_3'];
$cms_lang['group_lay_5'] = $cms_lang['group_area_lay_5'];
$cms_lang['group_lay_6'] = $cms_lang['group_area_lay_6'];
$cms_lang['group_lay_7'] = $cms_lang['group_area_lay_7'];
$cms_lang['group_lay_8'] = $cms_lang['group_area_lay_8'];

// area_css
$cms_lang['group_area_css'] = 'Bereich Design / Stylesheet';
$cms_lang['group_area_css_1'] = 'CSS-Datei anzeigen';
$cms_lang['group_area_css_2'] = 'CSS-Datei erstellen';
$cms_lang['group_area_css_3'] = 'CSS-Datei bearbeiten';
$cms_lang['group_area_css_5'] = 'CSS-Datei löschen';
$cms_lang['group_area_css_6'] = 'CSS-Dateirechte vergeben';
$cms_lang['group_area_css_8'] = 'CSS-Datei downloaden';
$cms_lang['group_area_css_9'] = 'CSS-Datei uploaden';
$cms_lang['group_area_css_13'] = 'CSS-Datei importieren';
$cms_lang['group_area_css_14'] = 'CSS-Datei exportieren';
$cms_lang['group_area_css_17'] = 'CSS-Regel anzeigen';
$cms_lang['group_area_css_18'] = 'CSS-Regel erstellen';
$cms_lang['group_area_css_19'] = 'CSS-Regel bearbeiten';
$cms_lang['group_area_css_21'] = 'CSS-Regel löschen';
$cms_lang['group_area_css_22'] = 'CSS-Regel Rechte vergeben';
$cms_lang['group_area_css_29'] = 'CSS-Regel importieren';
$cms_lang['group_area_css_30'] = 'CSS-Regel exportieren';
$cms_lang['group_css_file_1'] = $cms_lang['group_area_css_1'];
$cms_lang['group_css_file_2'] = $cms_lang['group_area_css_2'];
$cms_lang['group_css_file_3'] = $cms_lang['group_area_css_3'];
$cms_lang['group_css_file_5'] = $cms_lang['group_area_css_5'];
$cms_lang['group_css_file_6'] = $cms_lang['group_area_css_6'];
$cms_lang['group_css_file_8'] = $cms_lang['group_area_css_8'];
$cms_lang['group_css_file_9'] = $cms_lang['group_area_css_9'];
$cms_lang['group_css_file_13'] = $cms_lang['group_area_css_13'];
$cms_lang['group_css_file_14'] = $cms_lang['group_area_css_14'];
$cms_lang['group_css_file_17'] = $cms_lang['group_area_css_17'];
$cms_lang['group_css_file_18'] = $cms_lang['group_area_css_18'];
$cms_lang['group_css_file_19'] = $cms_lang['group_area_css_19'];
$cms_lang['group_css_file_21'] = $cms_lang['group_area_css_21'];
$cms_lang['group_css_file_22'] = $cms_lang['group_area_css_22'];
$cms_lang['group_css_file_29'] = $cms_lang['group_area_css_29'];
$cms_lang['group_css_file_30'] = $cms_lang['group_area_css_30'];

// area_js
$cms_lang['group_area_js'] = 'Bereich Design / Javascript';
$cms_lang['group_area_js_1'] = 'JS-Datei anzeigen';
$cms_lang['group_area_js_2'] = 'JS-Datei erstellen';
$cms_lang['group_area_js_3'] = 'JS-Datei bearbeiten';
$cms_lang['group_area_js_5'] = 'JS-Datei löschen';
$cms_lang['group_area_js_6'] = 'JS-Dateirechte vergeben';
$cms_lang['group_area_js_8'] = 'JS-Datei downloaden';
$cms_lang['group_area_js_9'] = 'JS-Datei uploaden';
$cms_lang['group_area_js_13'] = 'JS-Datei importieren';
$cms_lang['group_area_js_14'] = 'JS-Datei exportieren';
$cms_lang['group_js_file_1'] = $cms_lang['group_area_js_1'];
$cms_lang['group_js_file_2'] = $cms_lang['group_area_js_2'];
$cms_lang['group_js_file_3'] = $cms_lang['group_area_js_3'];
$cms_lang['group_js_file_5'] = $cms_lang['group_area_js_5'];
$cms_lang['group_js_file_6'] = $cms_lang['group_area_js_6'];
$cms_lang['group_js_file_8'] = $cms_lang['group_area_js_8'];
$cms_lang['group_js_file_9'] = $cms_lang['group_area_js_9'];
$cms_lang['group_js_file_13'] = $cms_lang['group_area_js_13'];
$cms_lang['group_js_file_14'] = $cms_lang['group_area_js_14'];

// area_mod
$cms_lang['group_area_mod'] = 'Bereich Design / Module';
$cms_lang['group_area_mod_1'] = 'Modul anzeigen';
$cms_lang['group_area_mod_2'] = 'Modul anlegen';
$cms_lang['group_area_mod_3'] = 'Modul bearbeiten';
$cms_lang['group_area_mod_4'] = 'Modul konfigurieren';
$cms_lang['group_area_mod_5'] = 'Modul löschen';
$cms_lang['group_area_mod_6'] = 'Modulrechte vergeben';
$cms_lang['group_area_mod_7'] = 'Modul importieren';
$cms_lang['group_area_mod_8'] = 'Modul exportieren';
$cms_lang['group_area_mod_9'] = 'Modul uploaden';
$cms_lang['group_area_mod_10'] = 'Modul downloaden';
$cms_lang['group_area_mod_11'] = 'Repository betreten';
$cms_lang['group_area_mod_12'] = 'Modul aus Repository updaten';
$cms_lang['group_area_mod_13'] = 'Modul aus Repository downloaden';
$cms_lang['group_area_mod_14'] = 'Modul aus Repository importieren';
$cms_lang['group_area_mod_15'] = 'Sieht DEV-Module im Repository';
$cms_lang['group_mod_1'] = $cms_lang['group_area_mod_1'];
$cms_lang['group_mod_2'] = $cms_lang['group_area_mod_2'];
$cms_lang['group_mod_3'] = $cms_lang['group_area_mod_3'];
$cms_lang['group_mod_4'] = $cms_lang['group_area_mod_4'];
$cms_lang['group_mod_5'] = $cms_lang['group_area_mod_5'];
$cms_lang['group_mod_6'] = $cms_lang['group_area_mod_6'];
$cms_lang['group_mod_7'] = $cms_lang['group_area_mod_7'];
$cms_lang['group_mod_8'] = $cms_lang['group_area_mod_8'];
$cms_lang['group_mod_10'] = $cms_lang['group_area_mod_10'];
$cms_lang['group_mod_11'] = $cms_lang['group_area_mod_11'];
$cms_lang['group_mod_12'] = $cms_lang['group_area_mod_12'];
$cms_lang['group_mod_13'] = $cms_lang['group_area_mod_13'];
$cms_lang['group_mod_14'] = $cms_lang['group_area_mod_14'];
$cms_lang['group_mod_15'] = $cms_lang['group_area_mod_15'];

// area_plug
$cms_lang['group_area_plug'] = 'Bereich Administration / Plugins';
$cms_lang['group_area_plug_1'] = 'Plugin anzeigen';
$cms_lang['group_area_plug_2'] = 'Plugin anlegen';
$cms_lang['group_area_plug_3'] = 'Plugin bearbeiten';
$cms_lang['group_area_plug_4'] = 'Plugin konfigurieren - Client';
$cms_lang['group_area_plug_5'] = 'Plugin löschen';
$cms_lang['group_area_plug_6'] = 'Pluginrechte vergeben';
$cms_lang['group_area_plug_7'] = 'Plugin importieren';
$cms_lang['group_area_plug_8'] = 'Plugin exportieren';
$cms_lang['group_area_plug_9'] = 'Plugin uploaden';
$cms_lang['group_area_plug_10']    = 'Plugin downloaden';
$cms_lang['group_area_plug_11']    = 'Repository betreten';
$cms_lang['group_area_plug_12']    = 'Plugin aus Repository updaten';
$cms_lang['group_area_plug_13']    = 'Plugin aus Repository downloaden';
$cms_lang['group_area_plug_14']    = 'Plugin aus Repository importieren';
$cms_lang['group_area_plug_15']    = 'Sieht DEV-Plugins im Repository';
$cms_lang['group_area_plug_16']    = 'Sieht Plugin-Konfiguration';
$cms_lang['group_area_plug_17']    = 'Sieht Plugin-Setup';
$cms_lang['group_area_plug_18']    = 'Plugin konfigurieren - Generell';
$cms_lang['group_plug_1'] = $cms_lang['group_area_plug_1'];
$cms_lang['group_plug_3'] = $cms_lang['group_area_plug_3'];
$cms_lang['group_plug_4'] = $cms_lang['group_area_plug_4'];
$cms_lang['group_plug_5'] = $cms_lang['group_area_plug_5'];
$cms_lang['group_plug_6'] = $cms_lang['group_area_plug_6'];
$cms_lang['group_plug_7'] = $cms_lang['group_area_plug_7'];
$cms_lang['group_plug_8'] = $cms_lang['group_area_plug_8'];
$cms_lang['group_plug_10'] = $cms_lang['group_area_plug_10'];
$cms_lang['group_plug_12'] = $cms_lang['group_area_plug_12'];
$cms_lang['group_plug_16'] = $cms_lang['group_area_plug_16'];
$cms_lang['group_plug_17'] = $cms_lang['group_area_plug_17'];
$cms_lang['group_plug_18'] = $cms_lang['group_area_plug_18'];

$cms_lang['group_area_rep'] = 'Bereich Administration / Repository';
$cms_lang['group_area_rep_1'] = 'Repository anzeigen';
$cms_lang['group_area_rep_2'] = 'Repository User';
$cms_lang['group_area_rep_3'] = 'Repository bearbeiten';
$cms_lang['group_area_rep_4'] = 'Repository konfigurieren';
$cms_lang['group_area_rep_5'] = 'Repository löschen';
$cms_lang['group_area_rep_6'] = 'Repositoryrechte vergeben';
$cms_lang['group_area_rep_7'] = 'Repository importieren';
$cms_lang['group_area_rep_8'] = 'Repository exportieren';
$cms_lang['group_area_rep_9'] = 'Repository uploaden';
$cms_lang['group_area_rep_10'] = 'Repository downloaden';
$cms_lang['group_area_rep_11'] = 'Repository betreten';
$cms_lang['group_area_rep_12'] = 'Repository signieren';
$cms_lang['group_area_rep_13'] = 'Repository prüfen';
$cms_lang['group_rep_1'] = $cms_lang['group_area_rep_1'];
$cms_lang['group_rep_2'] = $cms_lang['group_area_rep_2'];
$cms_lang['group_rep_3'] = $cms_lang['group_area_rep_3'];
$cms_lang['group_rep_4'] = $cms_lang['group_area_rep_4'];
$cms_lang['group_rep_5'] = $cms_lang['group_area_rep_5'];
$cms_lang['group_rep_6'] = $cms_lang['group_area_rep_6'];
$cms_lang['group_rep_7'] = $cms_lang['group_area_rep_7'];
$cms_lang['group_rep_8'] = $cms_lang['group_area_rep_8'];
$cms_lang['group_rep_10'] = $cms_lang['group_area_rep_10'];
$cms_lang['group_rep_11'] = $cms_lang['group_area_rep_11'];
$cms_lang['group_rep_12'] = $cms_lang['group_area_rep_12'];
$cms_lang['group_rep_13'] = $cms_lang['group_area_rep_13'];

// area_fm
$cms_lang['group_area_fm'] = 'Bereich Redaktion / Dateimanager';
$cms_lang['group_area_fm_1'] = 'Verzeichnis anzeigen';
$cms_lang['group_area_fm_2'] = 'Verzeichnis erstellen';
$cms_lang['group_area_fm_3'] = 'Verzeichnis bearbeiten';
$cms_lang['group_area_fm_5'] = 'Verzeichnis löschen';
$cms_lang['group_area_fm_6'] = 'Verzeichnisrechte vergeben';
$cms_lang['group_area_fm_7'] = 'Verzeichnis importieren';
$cms_lang['group_area_fm_8'] = 'Verzeichnis exportieren';
$cms_lang['group_area_fm_9'] = 'Verzeichnis uploaden';
$cms_lang['group_area_fm_10'] = 'Verzeichnis downloaden';
$cms_lang['group_area_fm_11'] = 'Verzeichnis abgleichen';
$cms_lang['group_area_fm_12'] = 'Verzeichnis kopieren';
$cms_lang['group_area_fm_17'] = 'Datei anzeigen';
$cms_lang['group_area_fm_18'] = 'Datei erstellen';
$cms_lang['group_area_fm_19'] = 'Datei bearbeiten';
$cms_lang['group_area_fm_21'] = 'Datei löschen';
$cms_lang['group_area_fm_22'] = 'Dateirechte vergeben';
$cms_lang['group_area_fm_23'] = 'Datei importieren';
$cms_lang['group_area_fm_24'] = 'Datei exportieren';
$cms_lang['group_area_fm_25'] = 'Datei uploaden';
$cms_lang['group_area_fm_26'] = 'Datei downloaden';
$cms_lang['group_area_fm_28'] = 'Datei kopieren';
$cms_lang['group_directory_1'] = $cms_lang['group_area_fm_1'];
$cms_lang['group_directory_2'] = $cms_lang['group_area_fm_2'];
$cms_lang['group_directory_3'] = $cms_lang['group_area_fm_3'];
$cms_lang['group_directory_5'] = $cms_lang['group_area_fm_5'];
$cms_lang['group_directory_6'] = $cms_lang['group_area_fm_6'];
$cms_lang['group_directory_7'] = $cms_lang['group_area_fm_7'];
$cms_lang['group_directory_8'] = $cms_lang['group_area_fm_8'];
$cms_lang['group_directory_9'] = $cms_lang['group_area_fm_9'];
$cms_lang['group_directory_10'] = $cms_lang['group_area_fm_10'];
$cms_lang['group_directory_11'] = $cms_lang['group_area_fm_11'];
$cms_lang['group_directory_12'] = $cms_lang['group_area_fm_12'];
$cms_lang['group_directory_17'] = $cms_lang['group_area_fm_17'];
$cms_lang['group_directory_18'] = $cms_lang['group_area_fm_18'];
$cms_lang['group_directory_19'] = $cms_lang['group_area_fm_19'];
$cms_lang['group_directory_21'] = $cms_lang['group_area_fm_21'];
$cms_lang['group_directory_22'] = $cms_lang['group_area_fm_22'];
$cms_lang['group_directory_23'] = $cms_lang['group_area_fm_23'];
$cms_lang['group_directory_24'] = $cms_lang['group_area_fm_24'];
$cms_lang['group_directory_25'] = $cms_lang['group_area_fm_25'];
$cms_lang['group_directory_26'] = $cms_lang['group_area_fm_26'];
$cms_lang['group_directory_28'] = $cms_lang['group_area_fm_28'];
$cms_lang['group_file_17'] = $cms_lang['group_area_fm_17'];
$cms_lang['group_file_18'] = $cms_lang['group_area_fm_18'];
$cms_lang['group_file_19'] = $cms_lang['group_area_fm_19'];
$cms_lang['group_file_21'] = $cms_lang['group_area_fm_21'];
$cms_lang['group_file_22'] = $cms_lang['group_area_fm_22'];
$cms_lang['group_file_23'] = $cms_lang['group_area_fm_23'];
$cms_lang['group_file_24'] = $cms_lang['group_area_fm_24'];
//$cms_lang['group_file_25'] = $cms_lang['group_area_fm_25'];
$cms_lang['group_file_26'] = $cms_lang['group_area_fm_26'];
$cms_lang['group_file_28'] = $cms_lang['group_area_fm_28'];

// area_tpl
$cms_lang['group_area_tpl']= 'Bereich Design / Templates';
$cms_lang['group_area_tpl_1']= 'Template anzeigen';
$cms_lang['group_area_tpl_2']= 'Template anlegen';
$cms_lang['group_area_tpl_3']= 'Template bearbeiten';
$cms_lang['group_area_tpl_5']= 'Template löschen';
$cms_lang['group_area_tpl_6']= 'Templaterechte vergeben';
$cms_lang['group_area_tpl_12']= 'Starttemplate festlegen';
$cms_lang['group_tpl_1'] = $cms_lang['group_area_tpl_1'];
$cms_lang['group_tpl_3'] = $cms_lang['group_area_tpl_3'];
$cms_lang['group_tpl_5'] = $cms_lang['group_area_tpl_5'];
$cms_lang['group_tpl_6'] = $cms_lang['group_area_tpl_6'];
$cms_lang['group_tpl_12'] = $cms_lang['group_area_tpl_12'];

// area_clients
$cms_lang['group_area_clients'] = 'Bereich Administration / Projekte';
$cms_lang['group_area_clients_1'] = 'Projekt anzeigen';
$cms_lang['group_area_clients_2'] = 'Projekt anlegen';
$cms_lang['group_area_clients_3'] = 'Projekt bearbeiten';
$cms_lang['group_area_clients_4'] = 'Projekt konfigurieren';
$cms_lang['group_area_clients_5'] = 'Projekt löschen';
$cms_lang['group_area_clients_6'] = 'Projektrechte vergeben';
$cms_lang['group_area_clients_17'] = 'Sprache anzeigen';
$cms_lang['group_area_clients_18'] = 'Sprache anlegen';
$cms_lang['group_area_clients_19'] = 'Sprache bearbeiten';
$cms_lang['group_area_clients_21'] = 'Sprache konfigurieren';
$cms_lang['group_area_clients_22'] = 'Sprache löschen';
$cms_lang['group_area_clients_28'] = 'Startsprache festlegen';

$cms_lang['group_clients_1'] = $cms_lang['group_area_clients_1'];
$cms_lang['group_clients_3'] = $cms_lang['group_area_clients_3'];
$cms_lang['group_clients_4'] = $cms_lang['group_area_clients_4'];
$cms_lang['group_clients_5'] = $cms_lang['group_area_clients_5'];
$cms_lang['group_clients_6'] = $cms_lang['group_area_clients_6'];
$cms_lang['group_clients_17'] = $cms_lang['group_area_clients_17'];
$cms_lang['group_clients_18'] = $cms_lang['group_area_clients_18'];
$cms_lang['group_clients_19'] = $cms_lang['group_area_clients_19'];
$cms_lang['group_clients_21'] = $cms_lang['group_area_clients_21'];
$cms_lang['group_clients_22'] = $cms_lang['group_area_clients_22'];
$cms_lang['group_clients_28'] = $cms_lang['group_area_clients_28'];

$cms_lang['group_clientlangs_17']= $cms_lang['group_area_clients_17'];
$cms_lang['group_clientlangs_19']= $cms_lang['group_area_clients_19'];
$cms_lang['group_clientlangs_21']= $cms_lang['group_area_clients_21'];
$cms_lang['group_clientlangs_22']= $cms_lang['group_area_clients_22'];
$cms_lang['group_clientlangs_28']= $cms_lang['group_area_clients_28'];

//area users
$cms_lang['group_area_user'] = 'Bereich Administration / Benutzer';

//area groups
$cms_lang['group_area_group'] = 'Bereich Administration / Gruppen';

// area_settings
$cms_lang['group_area_settings']= 'Bereich Administration / System';
$cms_lang['group_area_settings_1']= 'Systemeinstellungen anzeigen / bearbeiten';

// area_plugin
$cms_lang['group_area_plugin'] = 'Bereich Plugins';

// area_logs
$cms_lang['group_area_logs'] = 'Bereich Administration / Logs';
$cms_lang['group_area_logs_1'] = 'Logs aus Datenbank anzeigen';
$cms_lang['group_area_logs_2'] = 'Logs aus Datenbank löschen';
$cms_lang['group_area_logs_3'] = 'Log-Dateien anzeigen';
$cms_lang['group_area_logs_4'] = 'Log-Dateien löschen';
?>
