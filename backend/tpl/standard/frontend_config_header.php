<?PHP
$frontend_header='<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<title>Sefrengo {SF-VERSION}</title>
	
	<link rel="stylesheet" type="text/css" href="tpl/{SKIN}/css/sefrengo-theme/jquery-ui.custom.css">
	<link rel="stylesheet" type="text/css" href="tpl/{SKIN}/css/styles.css">
	
	<script type="text/javascript" src="tpl/{SKIN}/js/init.sefrengo.js"></script>
	<script type="text/javascript" src="tpl/{SKIN}/js/lib/jquery.min.js"></script>
	<script type="text/javascript" src="tpl/{SKIN}/js/lib/jquery-ui.custom.min.js"></script>
	<script type="text/javascript">
		var $jqsf = jQuery;
		//var $jqsf = $.noConflict(true); // currently the jQuery plugins won\'t work in this mode 
		
		SF.Config.debug = false;
		SF.Config.backend_dir = \'{BACKEND_DIR}\'; // e.g. /backend/
		SF.Config.js_dir = \'tpl/{SKIN}/js/\';
		SF.Config.css_dir = \'tpl/{SKIN}/css/\';
		SF.Config.img_dir = \'tpl/{SKIN}/img/\';
	</script>
	<script type="text/javascript" src="tpl/{SKIN}/js/jquery.sefrengo.js"></script>

</head>
<body id="con-edit2">
';
?>