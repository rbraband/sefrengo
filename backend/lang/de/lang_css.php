<?php
if(! defined('CMS_CONFIGFILE_INCLUDED'))
{
	die('NO CONFIGFILE FOUND');
}

$prefix = 'css';

$cms_lang[$prefix.'_area_index'] = 'Design &rsaquo; Stylesheet';

include_once($cfg_cms['path_base'].$cfg_cms['path_backend_rel'] .'lang/'.$cfg_cms['backend_lang'].'/lang_fm.php');
?>