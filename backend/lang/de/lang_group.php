<?php
if(! defined('CMS_CONFIGFILE_INCLUDED'))
{
	die('NO CONFIGFILE FOUND');
}
// include language files for group configurations from lang_user_perms.php
include_once($cfg_cms['path_base'].$cfg_cms['path_backend_rel'] .'lang/'.$cfg_cms['backend_lang'].'/lang_user_perms.php');

$cms_lang['group_new'] = 'Neue Gruppe';
$cms_lang['group_on'] = 'Gruppe aktivieren';
$cms_lang['group_off'] = 'Gruppe deaktivieren';
$cms_lang['group_back'] = 'Zurück';
$cms_lang['group_nocat'] = 'Diese Sprache besitzt noch keine Ordnerstruktur.';
$cms_lang['group_langconfig'] = 'Rechte in dieser Sprache konfigurieren';
$cms_lang['group_langon'] = 'Sprache hinzufügen';
$cms_lang['group_langoff'] = 'Sprache entfernen';
$cms_lang['group_edit'] = 'Gruppe bearbeiten';
$cms_lang['group_config'] = 'Gruppe konfigurieren';
$cms_lang['group_delete'] = 'Gruppe löschen';
$cms_lang['group_new'] = 'Neue Gruppe';
$cms_lang['group_nogroups'] = 'Es wurden noch keine Gruppen angelegt.';
$cms_lang['group_actions'] = 'Aktionen';
$cms_lang['group_new'] = 'Neue Gruppe';
$cms_lang['group_name'] = 'Gruppenname';
$cms_lang['group_description'] = 'Gruppenbeschreibung';

$cms_lang['group_access_area_granted'] = 'Zugriff auf Bereich erlaubt';
$cms_lang['group_access_area_denied'] = 'Zugriff auf Bereich verboten';

$cms_lang['err_group_existname'] = 'Dieser Gruppenname existiert schon';
$cms_lang['err_group_noname'] = 'Es wurde kein Gruppenname angegeben';
$cms_lang['err_group_incorrectcharacter'] = 'Der Gruppenname darf nur aus alphanumerischen Zeichen bestehen ([0-9a-zA-Z])';
?>