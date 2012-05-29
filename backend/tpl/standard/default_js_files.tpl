<script type="text/javascript" src="tpl/{SKIN}/js/init.sefrengo.js"></script>

<!-- BEGIN JS_DEBUG -->
	<script type="text/javascript" src="tpl/{SKIN}/js/lib/jquery.js"></script>
	<script type="text/javascript" src="tpl/{SKIN}/js/lib/jquery-ui.custom.min.js"></script>
	<script type="text/javascript" src="tpl/{SKIN}/js/lib/jquery.cookie.js"></script>
	<script type="text/javascript">
		SF.Config.debug = true;
	</script>
<!-- END JS_DEBUG -->

<!-- BEGIN JS_NODEBUG -->
	<script type="text/javascript" src="tpl/{SKIN}/js/lib/jquery.min.js"></script>
	<script type="text/javascript" src="tpl/{SKIN}/js/lib/jquery-ui.custom.min.js"></script>
	<script type="text/javascript" src="tpl/{SKIN}/js/lib/jquery.cookie.js"></script>
	<script type="text/javascript">
		SF.Config.debug = false;
	</script>
<!-- END JS_NODEBUG -->

<!-- BEGIN JS_CONFIG -->
	<script type="text/javascript">
		var $jqsf = jQuery;
		//var $jqsf = $.noConflict(true); // currently the jQuery plugins won't work in this mode 
	
		SF.Config.backend_dir = '{BACKEND_DIR}'; // e.g. /backend/
		SF.Config.js_dir = 'tpl/{SKIN}/js/';
		SF.Config.css_dir = 'tpl/{SKIN}/css/';
		SF.Config.img_dir = 'tpl/{SKIN}/img/';
	</script>
<!-- END JS_CONFIG -->

<!-- BEGIN JS_LANG -->
	<script type="text/javascript">
		SF.Lang = {JS_LANG};
	</script>
<!-- END JS_LANG -->

<!-- BEGIN JS_FILE_DEFAULT_PATH -->
	<script type="text/javascript" src="tpl/{SKIN}/js/{JS_FILE}"></script>
<!-- END JS_FILE_DEFAULT_PATH -->

<!-- BEGIN JS_FILE_CUSTOM_PATH -->
	<script type="text/javascript" src="{JS_FILE}"></script>
<!-- END JS_FILE_CUSTOM_PATH -->

<script type="text/javascript" src="tpl/{SKIN}/js/jquery.sefrengo.js"></script>

<script type="text/javascript">
function delete_confirm(name, lang) {
	return SF.Utils.deleteConfirm(name, lang);
}
</script>