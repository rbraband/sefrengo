<?php
require_once('fnc.ck.php');
require_once('../../inc/config.php');
require_once('../../../'.$cms_path.'inc/inc.init_external.php');

//echo $o1->get() . '<br />';//Gibt "Moin, I am a simple Test" aus
//require_once('ResourceBrowser/API/GUI/class.SF_GUI_RessourceBrowser.php');
//require_once('ResourceBrowser/API/GUI/RESSOURCES/class.SF_GUI_RESSOURCES_Abstract.php');
//require_once('ResourceBrowser/API/GUI/RESSOURCES/class.SF_GUI_RESSOURCES_FileManager.php');
//require_once('ResourceBrowser/API/GUI/RESSOURCES/class.SF_GUI_RESSOURCES_InternalLink.php');
//require_once('ResourceBrowser/API/GUI/RESSOURCES/class.SF_GUI_RESSOURCES_RessourceItemPrototype.php');

$ck_editorname = $_GET['ck_editorname'];
$ck_conf = unserialize( base64_decode($_GET['ck_ser']) );
$ck_menu_items = ckBuildMenu($ck_conf['features'], $ck_conf['selectablestyles']);
$ck_session_string = $ck_conf['sess_name'].'='. $ck_conf['sess_id'] ;
//print_r($ck_conf);
//linkbrowser
$rb = $sf_factory->getObjectForced('GUI', 'RessourceBrowser');
$rb->setExtraUrlParmString($ck_session_string);

$res_file = $sf_factory->getObjectForced('GUI/RESSOURCES', 'FileManager');

//print_r(get_class_methods($res_file));

$res_file->setFiletypes( ckConfigStringToArray($ck_conf['filefiletypes']) );
$res_file->setFolderIds( ckConfigStringToArray($ck_conf['filefolders']) );
$with_subfolders = ($ck_conf['filesubfolders'] != 'false') ? true:false;
$res_file->setWithSubfoders($with_subfolders);
$rb->addRessource($res_file);

$res_links = $sf_factory->getObjectForced('GUI/RESSOURCES', 'InternalLink');
$rb->addRessource($res_links);

//$res_prototype = new SF_GUI_RESSOURCES_RessourceItemPrototype();
//$rb->addRessource($res_prototype);

$rb->setJSCallbackFunction('CKEDITOR.tools.callFunction', array('funcNum','picked_value') );

//imagebrowser
$rb_image = $sf_factory->getObjectForced('GUI', 'RessourceBrowser');
$rb_image->setExtraUrlParmString($ck_session_string);
$res_file_im = & $sf_factory->getObjectForced('GUI/RESSOURCES', 'FileManager');
$res_file_im->setFiletypes( ckConfigStringToArray(
								$ck_conf['imagefiletypes'] == 'true' || empty($ck_conf['imagefiletypes'])
									? 'jpg,jpeg,gif,png' : $ck_conf['imagefiletypes']) );
$res_file_im->setFolderIds( ckConfigStringToArray($ck_conf['imagefolders']) );
$with_subfolders = ($ck_conf['imagesubfolders'] != 'false') ? true:false;
$res_file_im->setWithSubfoders($with_subfolders);
$rb_image->addRessource($res_file_im);
$rb_image->setJSCallbackFunction('CKEDITOR.tools.callFunction', array('funcNum','picked_value') );


//print_r($ck_conf);
?>

CKEDITOR.config.width = '100%';
CKEDITOR.config.height = '400px';

CKEDITOR.config.skin = 'office2003';

CKEDITOR.config.defaultLanguage = 'de';

CKEDITOR.config.contentsCss = CKEDITOR.basePath + 'sefrengo/inc.ck_style_collector.php?<?php echo $ck_session_string .'&sf_idlay='. $ck_conf['sf_idlay'] ?>';

CKEDITOR.config.fullPage = false;
//CKEDITOR.config.extraPlugins = 'tabletools';

CKEDITOR.config.protectedSource.push( /<script[\s\S]*?\/script>/gi ); // <SCRIPT> tags.

CKEDITOR.config.toolbar_SefrengoDefault =
[
	['NewPage','Preview','Print'],
	['Cut','Copy','Paste','PasteText','PasteFromWord','RemoveFormat','Undo','Redo','Find','Replace'],
	'/',
	['Bold','Italic','Underline','Strike','Subscript','Superscript'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	['TextColor','BGColor'],
	['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
	['Link','Unlink','Anchor','Image','Table', 'HorizontalRule','SpecialChar'],
	'/',
	[<?php echo (! empty($ck_conf['selectablestyles']) ) ? "'Style',":"" ?>'Format','Font','Size'],
	['Source']
];

CKEDITOR.config.toolbar_<?php echo $ck_editorname ?> = <?php echo $ck_menu_items ?>;

// CKEDITOR.ENTER_P (1): new <p> paragraphs are created;
// CKEDITOR.ENTER_BR (2): lines are broken with <br> elements;
// CKEDITOR.ENTER_DIV (3): new <div> blocks are created.
CKEDITOR.config.enterMode = CKEDITOR.ENTER_P; 
CKEDITOR.config.shiftEnterMode = CKEDITOR.ENTER_BR;

CKEDITOR.config.keystrokes =
[
    [ CKEDITOR.ALT + 121 /*F10*/, 'toolbarFocus' ],
    [ CKEDITOR.ALT + 122 /*F11*/, 'elementsPathFocus' ],

    [ CKEDITOR.SHIFT + 121 /*F10*/, 'contextMenu' ],

    [ CKEDITOR.CTRL + 90 /*Z*/, 'undo' ],
    [ CKEDITOR.CTRL + 89 /*Y*/, 'redo' ],
    [ CKEDITOR.CTRL + CKEDITOR.SHIFT + 90 /*Z*/, 'redo' ],

    [ CKEDITOR.CTRL + 76 /*L*/, 'link' ],

    [ CKEDITOR.CTRL + 66 /*B*/, 'bold' ],
    [ CKEDITOR.CTRL + 73 /*I*/, 'italic' ],
    [ CKEDITOR.CTRL + 85 /*U*/, 'underline' ],

    [ CKEDITOR.ALT + 109 /*-*/, 'toolbarCollapse' ]
];

CKEDITOR.config.colorButton_colors = '000000,993300,333300,003300,003366,000080,333399,333333,800000,FF6600,808000,808080,008080,0000FF,666699,808080,FF0000,FF9900,99CC00,339966,33CCCC,3366FF,800080,999999,FF00FF,FFCC00,FFFF00,00FF00,00FFFF,00CCFF,993366,C0C0C0,FF99CC,FFCC99,FFFF99,CCFFCC,CCFFFF,99CCFF,CC99FF,FFFFFF';

CKEDITOR.config.font_names =
    'Arial/Arial, Helvetica, sans-serif;' +
	'Comic Sans MS;' +
	'Courier New;' +
    'Times New Roman/Times New Roman, Times, serif;' +
	'Tahoma;' +
    'Verdana';

CKEDITOR.config.fontSize_sizes = 'xx-small/xx-small;x-small/x-small;small/small;medium/medium;large/large;x-large/x-large;xx-large/xx-large';

// Highlight search results with blue on yellow.
CKEDITOR.config.find_highlight =
    {
        element : 'span',
        styles : { 'background-color' : '#ff0', 'color' : '#00f' }
    };
	
CKEDITOR.config.undoStackSize = 20;

CKEDITOR.config.filebrowserBrowseUrl = '<?php echo $rb->exportConfigURL().'&'.$ck_session_string ?>';
CKEDITOR.config.filebrowserImageBrowseLinkUrl = '<?php echo $rb->exportConfigURL().'&'.$ck_session_string ?>';
CKEDITOR.config.filebrowserImageBrowseUrl = '<?php echo $rb->exportConfigURL().'&'.$ck_session_string ?>';
