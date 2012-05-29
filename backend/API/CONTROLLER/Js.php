<?php
$this->includeClass('CONTROLLER', 'Fm');

/**
 * Javascript based on Filemanager
 */
class SF_CONTROLLER_Js extends SF_CONTROLLER_Fm
{
	/**
	 * Stores the configuration (from the client settings) of this area 
	 * @var array
	 */
	protected $config_area = array(
		'config_file' => 'CONFIGURATION/FmConfiguration.php',
		'area_name' => 'js', // set area_name explicitly!
		'idclient' => 0,
		'idlang' => 0,
		'start_idlang' => 0,
		'multi_language_support' => TRUE,
		
		'temp_out_path' => '/upload/out',
		'temp_in_path' => '/upload/in',
		'download_archive_filetype' => 'zip',
		'extract_compressed_files' => array('tar', 'gz', 'bz2', 'tgz', 'tbz', 'zip', 'ar', 'deb'),
		
		'forbidden_directories' => array('.svn'),
		'forbidden_files' => array('htaccess', 'htpasswd'),
		'allowed_files' => array(), // invalidates 'forbidden_files'
		
		'allow_invalid_filenames' => 1,
		'allow_invalid_dirnames' => 1,
		
		// no settings from client configuration
		'viewtype' => 'compact',
		'items_per_page' => 50,
		'remove_root_actions' => array('copy_directory','download_directory','edit_directory','delete_directory'),
		'files_in_root' => TRUE,
		'enable_left_pane' => FALSE,
		
		'js_lang' => array()
	);
}
?>