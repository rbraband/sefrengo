<?php
$this->includeClass('CONTROLLER', 'AbstractArea');

/**
 * DirectoryScan
 */
class SF_CONTROLLER_Ds  extends SF_CONTROLLER_AbstractArea
{
	/**
	 * Database object
	 * @var SF_LIB_Ado
	 */
	protected $db;
	
	/**
	 * Stores the configuration (from the client settings) of this area 
	 * @var array
	 */
	protected $config_area = array(
		'area_name' => 'ds', // set area_name explicitly!
		'parent_area_name' => '',
		'idclient' => 0,
		'idlang' => 0,
		'start_idlang' => 0,
		'multi_language_support' => TRUE,
		
		'forbidden_directories' => array('.svn'),
		'forbidden_files' => array('htaccess', 'htpasswd'),
		'allowed_files' => array(), // invalidates 'forbidden_files'
		'thumb_extension' => '_cms_thumb',
		'thumb_numberof' => 1,
		
		'remove_files_404' => TRUE,
		'delete_ignore_404' => TRUE,
		'remove_empty_directories' => FALSE,
		
		'allow_invalid_filenames' => 1,
		'allow_invalid_dirnames' => 1,
		
		'max_count_scandir' => 10,
		'max_count_scanfile' => 2,
		'max_count_scanthumb' => 10,
		'extend_scantime' => 60,
		'safemode_off' => TRUE,
		
		// area dependent settings
		'enable_thumb_generation' => TRUE,
		'files_in_root' => FALSE,
	);
	
	/**
	 * URL Parameter
	 * @var array
	 */
	protected $params = array(
		'area' => '',
		'parent_area' => '',
		'iddirectory' => 0,
		'nosubdirscan' => FALSE,
		'updatethumbs' => FALSE,
		'action' => '',
	);
	
	/**
	 * Stores data about the progress of the scan
	 * @var array
	 */
	protected $progress = array(
		'next_url' => '',
		'active_step' => 0,
		
		'directories_found' => 0,
		'directories_done' => 0,
		'files_found' => 0,
		'files_done' => 0,
		'thumbs_found' => 0,
		'thumbs_done' => 0,
		
		'directories_edited' => array(),
		'directories_error' => array(),
		'files_edited' => array(),
		'files_error' => array(),
	);
	
	
	/**
	 * Constructor set main values to {@link $config_area},
	 * collect {@link $params} from {@link $req} and checks
	 * the permission for directory scan.
	 * @return void
	 */
	public function __construct()
	{
		// call abstract area constructor
		parent::__construct();
		
		// init objects
		$this->db = sf_api('LIB', 'Ado');
		
		$this->_initParams();
		$this->_checkPerm();
		$this->_initConfiguration();
	}

	/**
	 * Initialize used parameter
	 * @return void
	 */
	private function _initParams()
	{
		// get common request params
		$this->params['area'] = $this->req->req('area', $this->config_area['area_name']);
		$this->params['parent_area'] = $this->req->req('parent_area', $this->params['parent_area']);
		$this->params['iddirectory'] = (int) $this->req->req('iddirectory', $this->params['iddirectory']);
		$this->params['nosubdirscan'] = $this->req->asBoolean('nosubdirscan', $this->params['nosubdirscan']);
		$this->params['updatethumbs']  = $this->req->asBoolean('updatethumbs', $this->params['updatethumbs']);
		$this->params['action'] = $this->req->req('action', $this->config_area['action']);

		$this->_setSystemLogMessage('init_params', $this->params);
		
		// set baseurivals
		$this->url->urlSetBase($this->controller_cfg['cms_basefile'], $this->params);
	}
	
	/**
	 * Check permission for directory scan
	 * @return void
	 */
	private function _checkPerm()
	{
		// set area configuration
		$this->config_area['parent_area_name'] = $this->params['parent_area'];
		$this->config_area['perm_area'] = 'area_' + $this->config_area['parent_area_name'];
		
		// perm check for the whole area
		$this->cfg->perm()->check($this->config_area['perm_area']);
		
		// check specific permission for directory scan
		$directory = sf_api('MODEL', 'DirectorySqlItem');
		if($directory->hasPerm('scan', $this->params['iddirectory']) == FALSE)
		{
			echo $this->lng->get($this->config_area['area_name'].'_permission_denied');
			exit;
		}
		unset($directory);
	}
	
	/**
	 * Initialize area configuration
	 * @return void
	 */
	private function _initConfiguration()
	{		
		// directory scan settings
		$this->config_area['multi_language_support'] = (bool) $this->cfg->client($this->config_area['parent_area_name'].'_multi_language_support', $this->config_area['multi_language_support']);
		$this->config_area['idclient'] = $this->cfg->env('idclient');
		// choose current idlang or 0 as generic language
		$this->config_area['idlang'] = ($this->config_area['multi_language_support'] == TRUE) ? $this->cfg->env('idlang') : 0;
		$this->config_area['start_idlang'] = $this->cfg->getStartIdLang();
	
		$this->config_area['remove_empty_directories'] = (bool) $this->cfg->client($this->config_area['parent_area_name'].'_remove_empty_directories', $this->config_area['remove_empty_directories']);
		$this->config_area['remove_files_404'] = (bool) $this->cfg->client($this->config_area['parent_area_name'].'_remove_files_404', $this->config_area['remove_files_404']);	
		$this->config_area['delete_ignore_404'] = (bool) $this->cfg->client($this->config_area['parent_area_name'].'_delete_ignore_404', $this->config_area['delete_ignore_404']);	
		
		$this->config_area['allow_invalid_filenames'] = (int) $this->cfg->client($this->config_area['parent_area_name'].'_allow_invalid_filenames', $this->config_area['allow_invalid_filenames']);
		$this->config_area['allow_invalid_dirnames'] = (int) $this->cfg->client($this->config_area['parent_area_name'].'_allow_invalid_dirnames', $this->config_area['allow_invalid_dirnames']);

		$this->config_area['max_count_scandir'] = $this->cfg->client('max_count_scandir', $this->config_area['max_count_scandir']);
		$this->config_area['max_count_scanfile'] = $this->cfg->client('max_count_scanfile', $this->config_area['max_count_scanfile']);
		$this->config_area['max_count_scanthumb'] = $this->cfg->client('max_count_scanthumb', $this->config_area['max_count_scanthumb']);
		$this->config_area['extend_scantime'] = $this->cfg->client('extend_scantime', $this->config_area['extend_scantime']);
		$this->config_area['safemode_off'] = (strtolower(get_cfg_var("safe_mode")) == 'off');
		
		// add thumbnails to scan ignore list
		$file = sf_api('MODEL', 'FileSqlItem');
		$thumbnail_settings = $file->getGlobalThumbnailSettings();
		unset($file);
		$this->config_area['thumb_numberof'] = (array_key_exists('sizes', $thumbnail_settings) == TRUE) ? count($thumbnail_settings['sizes']) : $this->config_area['thumb_numberof'];
		$this->config_area['thumb_extension'] = (array_key_exists('thumb_extension', $thumbnail_settings) == TRUE) ? $thumbnail_settings['thumb_extension'] : $this->config_area['thumb_extension'];
		
		$this->config_area['forbidden_directories'] = explode(',', $this->cfg->client($this->config_area['parent_area_name'].'_forbidden_directories', implode(',', $this->config_area['forbidden_directories'])));
		$this->config_area['forbidden_files'] = explode(',', $this->cfg->client($this->config_area['parent_area_name'].'_forbidden_files', implode(',', $this->config_area['forbidden_files'])));
		$this->config_area['allowed_files'] = explode(',', $this->cfg->client($this->config_area['parent_area_name'].'_allowed_files', implode(',', $this->config_area['allowed_files'])));
		
		// area dependent settings
		$this->config_area['enable_thumb_generation'] = ($this->config_area['parent_area_name'] == 'fm') ? TRUE : FALSE;
		$this->config_area['files_in_root'] = ($this->config_area['parent_area_name'] == 'fm') ? FALSE : TRUE;
		
		$this->_setSystemLogMessage('init_configuration', $this->config_area);
	}
	
	/**
	 * Creates the overlay with configuration interface
	 * and empty statistic table. The JavaScript function
	 * will initialize and start the scan. 
	 * @return string Returns the HTML Output
	 */
	public function index()
	{
		$overlay = sf_api('VIEW', 'Overlay');
		$overlay->loadTemplatefile('overlay_scandirectory.tpl');
		
		$overlay->addTemplateVar('TITLE', $this->lng->get($this->config_area['area_name'].'_area_index'));
		$lang['FORM_URL'] = $this->url->urlGet(array('area' => $this->config_area['area_name'].'_scan_directories'));
		$lang['CLOSE_URL'] = $this->url->urlGet(array('area' => $this->config_area['parent_area_name'].'_index', 'iddirectory' => $this->params['iddirectory'], 'parent_area' => '') );
		$lang['LANG_NOSUBDIRSCAN'] = $this->lng->get($this->config_area['area_name'].'_nosubdirscan');
		$lang['LANG_START'] = $this->lng->get($this->config_area['area_name'].'_start');
		$lang['LANG_CLOSE'] = $this->lng->get($this->config_area['area_name'].'_close');
		$lang['LANG_CANCEL'] = $this->lng->get($this->config_area['area_name'].'_cancel');
		$lang['LANG_TOTAL'] = $this->lng->get($this->config_area['area_name'].'_total');
		$lang['LANG_DONE'] = $this->lng->get($this->config_area['area_name'].'_done');
		$lang['LANG_OPEN'] = $this->lng->get($this->config_area['area_name'].'_open');
		$lang['LANG_DIRECTORIES'] = $this->lng->get($this->config_area['area_name'].'_directories');
		$lang['LANG_FILES'] = $this->lng->get($this->config_area['area_name'].'_files');

		if($this->config_area['enable_thumb_generation'] == TRUE)
		{
			$lang['LANG_UPDATETHUMBS'] = $this->lng->get($this->config_area['area_name'].'_updatethumbs');
			$lang['LANG_THUMBNAILS'] = $this->lng->get($this->config_area['area_name'].'_thumbnails');
		}
		
		$overlay->addTemplateVar('', $lang);
		
		$overlay->generate();
		return $overlay->get();
	}

	/**
	 * Setup the first step to scan directories.
	 * If function is called with iddirectory for firsttime,
	 * prepares all directories and files for the scan. Every
	 * next call with iddirectory = -1 it retrieves directories
	 * that are not finished by status from the DB.
	 * Scan single directory with function {@link _doScanDirectory}.
	 * If scan is finished set the next url (for JS frontend) for
	 * file scan. Otherwise recall this function again.
	 * @return string Returns the {@link $progress} as JSON 
	 */
	public function scanDirectories()
	{
		$iddirectory = $this->params['iddirectory'];
		$directory_queue = array();
		
		// first call: delete scan flags in directories and files
		if($iddirectory >= 0)
		{
			$this->_clearDirectoryStatus(0x04);
			$this->_clearFileStatus(0x04);
			$directory_queue = array( $iddirectory );
		}
		// $iddirectory < 0: the scan process has been started, but not finished
		else
		{
			// get the directory list of directories to scan from the database
			$directory_queue = $this->_getIddirectoriesByStatus(0x30, 0x74);
		}
		
		$this->_setSystemLogMessage('scan_directories_queue', $directory_queue);

		// start scanning the directories
		$i = 0;
		while ($i < count($directory_queue))
		{
			$current_iddirectory = (int) $directory_queue[$i];
			$new_directories = $this->_doScanDirectory($current_iddirectory);
			
			// if subdir scan is disabled -> clear status and delete $new_directories
			if($this->params['nosubdirscan'] == TRUE)
			{
				for($j = 0; $j < count($new_directories); $j++) {
					$this->_clearDirectoryStatus(0x8F, $new_directories[$j]);
				}
				
				$new_directories = array();
			}
			
			// set user rights for new found directories
			// --> is already done in DirectorySqlItem->createOnlyDb() (see $this->_doScanDirectory())
			/*if (count($this->progress['directories_edited']) > 0) {
				$sourcerights = ($current_iddirectory == 0) ? 'area_fm': 'directory';
				$this->cfg->perm()->copy_perm(
					$current_iddirectory,
					$sourcerights,
					$this->progress['directories_edited'],
					0, // group
					$this->config_area['idlang'], // lang
					FALSE // ignore lang
				);
			}*/
			
			if(count($new_directories) > 0)
			{
				$directory_queue = array_merge($directory_queue, $new_directories);
			} 
			
			$i++;
			
			// extend script time out if possible
			if($this->config_area['safemode_off'] == TRUE)
			{
				set_time_limit($this->config_area['extend_scantime']);
			}
			// or stop scanning if max limit for scanning is reached
			else if($i > $this->config_area['max_count_scandir'])
			{
				break;
			}
		}

		// if all directories are done ...
		if($i >= count($directory_queue))
		{
			$this->progress['active_step'] = 1;
			$this->progress['next_url'] = $this->url->urlGet(array('area' => $this->config_area['area_name'].'_scan_files', 'iddirectory' => 0));
			// delete missing directories
			$this->_deleteDirectoriesNotFound();
			// clear the directory scan status
			$this->_clearDirectoryStatus(0xAF);
		}
		else
		{
			$this->progress['active_step'] = 0;
			$this->progress['next_url'] = $this->url->urlGet(array('area' => $this->config_area['area_name'].'_scan_directories', 'iddirectory' => -1));
		}
		
		$this->_setSystemLogMessage('scan_directories_progress', $this->progress);
		
		return json_encode($this->progress);
	}

	/**
	 * Setup the second step to scan files.
	 * Retrieves the marked directories by status from DB
	 * and scan every directory for files in function
	 * {@link _doScanFiles}.
	 * If scan is finished set the next url (for JS frontend) for
	 * thumbnail scan. Otherwise recall this function again.
	 * @return string Returns the {@link $progress} as JSON 
	 */
	public function scanFiles()
	{
		$directory_queue = array();
		// get the directory list of directories to scan from the database
		$directory_queue = $this->_getIddirectoriesByStatus(0x20, 0x24);
		
		// first call: scan root directory for files, if enabled 
		if($this->config_area['files_in_root'] == TRUE && $this->params['iddirectory'] >= 0)
		{
			array_unshift($directory_queue, $iddirectory);
		}
		
		$this->_setSystemLogMessage('scan_files_queue', $directory_queue);

		// start scanning the directories
		$i = 0; $max = count($directory_queue);
		while ($i < $max)
		{
			$current_iddirectory = (int) $directory_queue[$i];
			$this->_doScanFiles($current_iddirectory);
			
			// set user rights for new found directories
			// set user rights for new found files
			if(count($this->progress['files_edited']) > 0)
			{
				$this->cfg->perm()->xcopy_perm(
					$current_iddirectory,
					'directory',
					$this->progress['files_edited'],
					'file',
					0x01B50000,
					0,
					$this->config_area['idlang'],
					FALSE
				);
			}
			
			$i++;
			
			// extend script time out if possible
			if($this->config_area['safemode_off'] == TRUE)
			{
				set_time_limit($this->config_area['extend_scantime']);
			}
			// or stop scanning if max limit for scanning is reached
			else if($i > $this->config_area['max_count_scanfile'])
			{
				break;
			}
		}
		
		//$this->progress['files_done'] = $this->progress['files_found'];
		
		// if all files are done ...
		if($i >= count($directory_queue))
		{
			// delete missing files
			$this->_deleteFilesNotFound();
			// clear the directory scan status
			$this->_clearDirectoryStatus(0x8F);
			
			// go to thumbnail generation
			if($this->config_area['enable_thumb_generation'] == TRUE)
			{
				$this->progress['active_step'] = 2;
				$this->progress['next_url'] = $this->url->urlGet(array('area' => $this->config_area['area_name'].'_scan_thumbnails', 'iddirectory' => -1));
			}
			// no thumbnail generation -> jump to finish step
			else
			{
				$this->progress['active_step'] = 3;
				unset($this->progress['next_url']);
				// clear the file scan status
				$this->_clearFileStatus(0xCF);
			}
		}
		else
		{
			$this->progress['active_step'] = 1;
			$this->progress['next_url'] = $this->url->urlGet(array('area' => $this->config_area['area_name'].'_scan_files', 'iddirectory' => -1));
		}
		
		$this->_setSystemLogMessage('scan_files_progress', $this->progress);
		
		return json_encode($this->progress);
	}
	
	/**
	 * Setup the third step to scan thumbnails and generate them.
	 * Retrieves marked files by status and scan every file in
	 * function {@link _doScanThumbnails}.
	 * If scan is finished the next url (for JS frontend) is unset.
	 * Otherwise recall this function again.
	 * Note: The finished files are multiply with the defined number
	 * of thumbnails to get the real number of thumbnails (and not files).  
	 * @return string Returns the {@link $progress} as JSON 
	 */
	public function scanThumbnails()
	{
		$files_queue = array();
		// get the file list of files to scan from the database
		$files_queue = $this->_getIduplByStatus(32, 36);
		
		$this->_setSystemLogMessage('scan_thumbnails_queue', $files_queue);
		
		// start scanning the directories
		$i = 0; $max = (count($files_queue) * $this->config_area['thumb_numberof']);
		while ($i < $max)
		{
			$current_idupl = (int) $files_queue[$i];
			
			if($this->_doScanThumbnails($current_idupl) == TRUE)
			{
				//$this->progress['thumbs_done']++;
			}
			
			// clear the file scan status
			$this->_clearFileStatus(0xCF, $current_idupl);
			
			$i++;
			
			// extend script time out if possible
			if($this->config_area['safemode_off'] == TRUE)
			{
				set_time_limit($this->config_area['extend_scantime']);
			}
			// or stop scanning if max limit for scanning is reached
			else if(($i * $this->config_area['thumb_numberof']) > $this->config_area['max_count_scanthumb'])
			{
				break;
			}
		}
		
		$this->progress['thumbs_found'] = $max;
		$this->progress['thumbs_done'] = ($i * $this->config_area['thumb_numberof']);
		
		// if all files are done ...
		if($i >= count($files_queue))
		{
			$this->progress['active_step'] = 3;
			unset($this->progress['next_url']);
			// clear the file scan status
			$this->_clearFileStatus(0xCF);
		}
		else
		{
			$this->progress['active_step'] = 2;
			$this->progress['next_url'] = $this->url->urlGet(array('area' => $this->config_area['area_name'].'_scan_thumbnails', 'iddirectory' => -1));
		}
		
		$this->_setSystemLogMessage('scan_thumbnails_progress', $this->progress);
		
		return json_encode($this->progress);
	}
	

	/* ******************
	 *  PROTECTED
	 * *****************/

	/**
	 * Scan a single directory by given $iddirectory.
	 * Check if directory has a physical directory and
	 * status is cleaned before. Add the scan status to
	 * directories.
	 * Iterate through the filesystem. Each directory is validated
	 * for directory name. Try to change the name accordingly to
	 * client setting.
	 * If the directory can not be found in DB, insert it right away.
	 * Otherwise do nothing and increase the counter.
	 * Remove directory if empty and client setting is enabled.
	 * Finally remove the scan status for the current directory. 
	 * @param int $iddirectory >= 0
	 * @return boolean Returns TRUE on success. Otherwise returns FALSE.
	 */
	protected function _doScanDirectory($iddirectory)
	{
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$validator = sf_api('LIB', 'Validation');
			
		$this->progress['directories_edited'] = array();
		$new_directories = array();		
		$content_found    = FALSE;
		
		$directory = sf_api('MODEL', 'DirectorySqlItem');
		$directory->setIdclient( $this->config_area['idclient'] );
		$directory->setIdlang( $this->config_area['idlang'] );
		
		// fake settings for root directory
		if($iddirectory == 0)
		{
			$directory->setField('iddirectory', 0);
			$directory->setField('area', $this->config_area['parent_area_name']);
			$directory->setField('status', 0);
			$directory->setField('dirname', '');
		}
		// load data - if loading fails return empty array
		else if($directory->loadById($iddirectory) == FALSE) 
		{
			return $new_directories;
		}

		// Given path is not a known directory -> delete
		if($fsm->isDir($directory->getPath()) == FALSE)
		{
			try
			{
				// deletes the directories and files recursively only in the DB
				if($directory->deleteOnlyDb() == FALSE)
				{
					// error deleting
					$this->_setSystemLogMessage('delete_directory_failed', array('iddirectory' => $iddirectory, 'path' => $directory->getRelativePath()));
					return $new_directories;
				}
			}
			catch(Exception $e)
			{
				// error deleting
				$this->_setSystemLogMessage('delete_directory_failed', array('iddirectory' => $iddirectory, 'path' => $directory->getRelativePath()));
				return $new_directories;
			}
		}
		else if(($directory->getField('status') & 0x40) != 0x40)
		{
			$this->progress['directories_done']++;
			
			// get current iddirectory 
			$current_iddirectory = $directory->getId();
			
			// set directories to scanning status
			$sql = 'UPDATE
						'.$this->cfg->db('directory').'
					SET
						status = (status | 0x30),
						lastmodified = lastmodified
					WHERE
						idclient = '.$this->config_area['idclient'].'
						AND (status & 0x64) = 0x00';
			
			// if no subdirectory scan wanted restrict status to current dir
			if($this->params['nosubdirscan'] == TRUE)
			{
				$sql .= ' AND iddirectory = '.$current_iddirectory;
			}
			// if current directory is not root, restrict status to current dir and children
			else if($current_iddirectory != 0)
			{
				$sql .= ' AND (parentid = '.$current_iddirectory.' OR iddirectory = '.$current_iddirectory.')';
			}
			
			$sql .= ';';
			
			//echo $sql."<br />";
			$rs = $this->db->Execute($sql);
			unset($sql, $rs);
			
			// fill fields with default values for new directory
			$directorydata = array();
			$directorydata['area'] = $this->config_area['parent_area_name'];
			$directorydata['idclient'] = $this->config_area['idclient'];
			$directorydata['idlang'] = $this->config_area['idlang'];
			$directorydata['parentid'] = $current_iddirectory;
			// set status for file scanning in new directories
			$directorydata['status'] = ($directory->getField('status') | 0x30);
			
			// get dirs and files; convert to ISO-8859-1 before used in filesystem function
			$handle = opendir($fsm->utf8_decode( $directory->getPath() ));
			while(FALSE !== ($file = readdir($handle)))
			{
				// check if $file is on ignore list of directories and files
				if( $this->_isForbiddenDirectoryName($file) == FALSE &&
					//$this->_isForbiddenFileExtension($file) == FALSE &&
					$file != '.' && $file != '..' )
				{
					$onefile = $fsm->utf8_decode($directory->getPath()).$file;
					if(is_dir($onefile))
					{
						// no invalid directory names allowed -> skip and go to next
						if( $validator->directoryname($file) == FALSE &&
							$this->config_area['allow_invalid_dirnames'] == 0)
						{
							//echo $file."<br>";
							$this->progress['directories_error'][] = $onefile;
							continue;
						}
						// try to correct the directoryname
						else if($validator->directoryname($file) == FALSE &&
								$this->config_area['allow_invalid_dirnames'] == 2)
						{
							$file_new = $fsm->utf8_decode($fsm->cleanFilename($file));
							$onefile_new = $fsm->utf8_decode($directory->getPath()).$file_new;
							
							// renaming of file failed
							if($fsm->renameDirectory($onefile, $onefile_new) == FALSE)
							{
								//echo $file."<br>";
								$this->progress['directories_error'][] = $onefile;
								
								// break and go to next file
								continue;
							}
							
							$file = $file_new;
							$onefile = $onefile_new;
						}
						
						$new_directory = sf_api('MODEL', 'DirectorySqlItem');
						$new_directory->setIdclient($directorydata['idclient']);
						$new_directory->setIdlang($directorydata['idlang']);
						
						$dirname = $directory->getField('dirname').$fsm->utf8_encode($file).'/';
						
						// check if new directory exists in DB -> no: insert and mark for subscan
						if($new_directory->loadByDirname($dirname, $directorydata['area']) == FALSE)
						{
							try
							{
								//$directorydata['dirname'] = $dirname;
								$directorydata['name'] = $fsm->utf8_encode($file);
								$directorydata['description'] = ''; // set for table directory_lang
								
								if($new_directory->createOnlyDb($directorydata) == TRUE)
								{
									$this->progress['directories_found']++;
									
									// add new dir to editlist, to copy perms later
									$this->progress['directories_edited'][] = $new_directory->getId();
									
									// add new dir to new scan list
									$new_directories[] = $new_directory->getId();
								}
								else
								{
									// error creating directory
									$this->progress['directories_error'][] = $onefile;
								}
							}
							catch(Exception $e)
							{
								//echo $e->getMessage()."<br />";
								$this->progress['directories_error'][] = $onefile;
							}
						}
						// directory loaded successfully and already exists in DB -> only mark for subscan 
						else
						{
							$this->progress['directories_found']++;
							
							// save directory id to remove the scan marks later
							if($new_directory->getId() != $current_iddirectory)
							{
								// add new dir to new scan list
								$new_directories[] = $new_directory->getId();   
							}
						}
					}
					else if(is_file($onefile) &&
							($this->config_area['files_in_root'] == TRUE || ($this->config_area['files_in_root'] == FALSE && $current_iddirectory != 0)) &&
							$this->_isForbiddenFileExtension($fsm->getPathinfo($file, 'extension')) == FALSE &&
							stripos($fsm->utf8_encode($file), $this->config_area['thumb_extension']) === FALSE)
					{
						$this->progress['files_found']++;
					}
					
					$content_found = TRUE;
				}
				// count files or anything else than directories ...
				else if($file != '.' && $file != '..')
				{
					$content_found = TRUE;
				}
			}
  			closedir($handle);
			
			$count_directories = count($new_directories) + count($this->progress['directories_error']);
			
			// delete directory because no files or directories were found and flag is set to true/1
			if( $content_found == FALSE &&
				$count_directories == 0 && 
				$this->config_area['remove_empty_directories'] == TRUE)
			{
				try
				{
					// delete empty directory in DB and filesystem
					$directory->delete();
				}
				catch(Exception $e)
				{
					//echo $e->getMessage()."<br />";
				}
			}
			
			// delete scan marks of current directory
			$this->_clearDirectoryStatus(0xEF, $current_iddirectory);
			$this->_setDirectoriesStatus(0x40, $current_iddirectory, FALSE);
		}
		
		return $new_directories;
	}

	/**
	 * Scans a single directory for files by given $iddirectory.
	 * Check if directory has a physical directory.
	 * Set add the scan status to all files of the directory.
	 * Iterate through the filesystem. Each file is validated
	 * for filename. Try to change the name accordingly to client setting.
	 * If the file can not be found in DB, insert a new file right away.
	 * Otherwise check for changes in filetime or filesize and
	 * update the information in DB.
	 * Finally remove the scan status for the current directory
	 * and all files. 
	 * @param int $iddirectory > 0
	 * @return boolean Returns TRUE on success. Otherwise returns FALSE.
	 */
	protected function _doScanFiles($iddirectory)
	{
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$validator = sf_api('LIB', 'Validation');
		
		$this->progress['files_edited'] = array();
		$new_files = array();		
		$found_files    = array();
		
		$directory = sf_api('MODEL', 'DirectorySqlItem');
		$directory->setIdclient( $this->config_area['idclient'] );
		$directory->setIdlang( $this->config_area['idlang'] );
		
		// fake settings for root directory
		if($this->config_area['files_in_root'] == TRUE && $iddirectory == 0)
		{
			$directory->setField('iddirectory', 0);
			$directory->setField('area', $this->config_area['parent_area_name']);
			$directory->setField('status', 0);
			$directory->setField('dirname', '');
		}
		// load data - if loading fails return empty array
		else if($directory->loadById($iddirectory) == FALSE) 
		{
			return $new_files;
		}
		
		// Given path is not a known directory -> delete
		if($fsm->isDir($directory->getPath()) == FALSE)
		{
			try
			{
				// deletes the directories and files recursively only in the DB
				if($directory->deleteOnlyDb() == FALSE)
				{
					// error deleting
					$this->_setSystemLogMessage('delete_directory_failed', array('iddirectory' => $iddirectory, 'path' => $directory->getRelativePath()));
					return $new_files;
				}
			}
			catch(Exception $e)
			{
				// error deleting
				$this->_setSystemLogMessage('delete_directory_failed', array('iddirectory' => $iddirectory, 'path' => $directory->getRelativePath()));
				return $new_files;
			}
			
			return $new_files;
		}
		
		// get current iddirectory 
		$current_iddirectory = $directory->getId();
		
		// set the all files in current directory to be scanned
		$sql = 'UPDATE
					'.$this->cfg->db('upl').'
				SET
					status = (status | 0x10),
					lastmodified = lastmodified
				WHERE
					iddirectory = '.$current_iddirectory.'
					AND idclient = '.$this->config_area['idclient'].'
					AND (status & 0x04) = 0x00';
		$sql .= ';';
		
		//echo $sql."<br />";
		$rs = $this->db->Execute($sql);
		unset($sql, $rs);
		
		// fill fields with default values for new file
		$filedata = array();
		$filedata['area'] = $this->config_area['parent_area_name'];
		$filedata['idclient'] = $this->config_area['idclient'];
		$filedata['idlang'] = $this->config_area['idlang'];
		$filedata['iddirectory'] = $current_iddirectory;
		
		// get dirs and files; convert to ISO-8859-1 before used in filesystem function
		$handle = opendir($fsm->utf8_decode( $directory->getPath() ));
		while(FALSE !== ($file = readdir($handle)))
		{
			// check if $file is on ignore list of directories and files or is a thumbnail
			if( //$this->_isForbiddenDirectoryNameName($file) == FALSE &&
				$this->_isForbiddenFileExtension($fsm->getPathinfo($file, 'extension')) == FALSE &&
				stripos($fsm->utf8_encode($file), $this->config_area['thumb_extension']) === FALSE && 
				$file != '.' && $file != '..' )
			{
				$onefile = $fsm->utf8_decode($directory->getPath()).$file;
				
				// perform further actions only for files, continue other 
				if(is_file($onefile) == FALSE)
				{
					continue;
				}
				
				// no invalid files allowed -> skip and go to next
				if( $validator->filename($file) == FALSE &&
					$this->config_area['allow_invalid_filenames'] == 0)
				{
					// jb_todo: forbidden file type ?? collect ??
					// jb_todo: filename not valid  ?? collect ??
					$this->progress['files_error'][] = $onefile;
					continue;
				}
				// try to correct the filename
				else if($validator->filename($file) == FALSE &&
						$this->config_area['allow_invalid_filenames'] == 2)
				{
					$file_new = $fsm->utf8_decode($fsm->cleanFilename($file));
					$onefile_new = $fsm->utf8_decode($directory->getPath()).$file_new;
					
					// renaming of file failed
					if($fsm->renameFile($onefile, $onefile_new) == FALSE)
					{
						// jb_todo: forbidden file type ?? collect ??
						// jb_todo: filename not valid  ?? collect ??
						$this->progress['files_error'][] = $onefile;
						
						// break and go to next file
						continue;
					}
					
					$file = $file_new;
					$onefile = $onefile_new;
				}
				
				$this->progress['files_done']++;
				
				$filedata['filename'] = $fsm->utf8_encode($file);
				$filedata['filesize'] = filesize($fsm->utf8_decode($onefile));
				$filedata['created'] = filemtime($fsm->utf8_decode($onefile));
				
				$new_file = sf_api('MODEL', 'FileSqlItem');
				$new_file->setIdclient($filedata['idclient']);
				$new_file->setIdlang($filedata['idlang']);
				
				// check if new file exists in DB -> no: insert and mark for subscan
				if($new_file->loadByFilename($filedata['filename'], $filedata['iddirectory']) == FALSE)
				{
					// load filetype (or insert if not exists) to calculate file status
					$filetype_data = array();
					$filetype_data['filetype'] = $fsm->getPathinfo($filedata['filename'], 'extension');
					$filetype = sf_api('MODEL', 'FiletypeSqlItem');
					if($filetype->loadByFiletype($filetype_data, TRUE) == FALSE)
					{
						// TODO loading and inserting filetype fails -> do some error handling
					}
					
					$filedata['status'] = ($directory->getField('status') | $filetype->getField('status'));
					$filedata['status'] = ($filedata['status'] & 0xEF);
					$filedata['status'] = ($filedata['status'] | 0x20);
					
					try
					{
						if($new_file->createOnlyDb($filedata) == TRUE)
						{
							$this->progress['files_found']++;
							
							// add new file to editlist, to copy perms later
							$this->progress['files_edited'][] = $new_file->getId();
							
							$new_files[] = $new_file->getId();
						}
						else
						{
							// error creating file
							$this->progress['files_error'][] = $onefile;
						}
					}
					catch(Exception $e)
					{
						//echo $e->getMessage()."<br />";
						$this->progress['files_error'][] = $onefile;
					}
					
				}
				else if( $filedata['filesize'] != $new_file->getField('filesize')
					  || $filedata['created'] != $new_file->getField('created')
					  || $this->params['updatethumbs'] == TRUE)
				{
					if($this->params['updatethumbs'] == TRUE)
					{
						switch($new_file->getField('idfiletype'))
						{
							case 3:
							case 4:
							case 12:
							case 13:
								$filedata['filesize'] = -2;
								break;
							default:
								break;
						}
					}
					
					$filedata['status'] = $new_file->getField('status');
					$filedata['status'] = ($filedata['status'] & 0xEF);
					$filedata['status'] = ($filedata['status'] | 0x20);
					
					try
					{	
						if($new_file->edit($filedata) == FALSE)
						{
							// error creating file
							$this->progress['files_error'][] = $onefile;
						}
					}
					catch(Exception $e)
					{
						//echo $e->getMessage()."<br />";
						$this->progress['files_error'][] = $onefile;
					}
					
					$found_files[] = $new_file->getId();
				}
				// count file also if last else-if condition is false
				else
				{
					$found_files[] = $new_file->getId();	
				}
			}
		}
		
		closedir($handle);
		
		// delete all scanned marks of found files
		if(count($found_files) > 0)
		{
			$sql = 'UPDATE
						'.$this->cfg->db('upl').'
					SET
						status = (status & 0xEF),
						lastmodified = lastmodified
					WHERE
						idupl IN ('.implode(',', $found_files).')
						AND idclient = '.$this->config_area['idclient'];
			$sql .= ';';
			
			//echo $sql."<br />";
			$rs = $this->db->Execute($sql);
			unset($sql, $rs);
		}
		
		// delete scannfile mark of current dir
		$sql = 'UPDATE
					'.$this->cfg->db('directory').'
				SET
					status = (status & 0xDF),
					lastmodified = lastmodified
				WHERE
					iddirectory = '.$current_iddirectory.'
					AND idclient = '.$this->config_area['idclient'];
		$sql .= ';';
		
		//echo $sql."<br />";
		$rs = $this->db->Execute($sql);
		unset($sql, $rs);
		
		return $new_files;
	}

	/**
	 * Scan a single file by given $idupl.
	 * If file exists and thumbnail generation is possible,
	 * generate thumbnails for file.
	 * Update filesize, filetime and imagesize afterwards. Even
	 * the thumbnail generation fails.  
	 * @param int $idupl > 0
	 * @return boolean Returns TRUE on success. Otherwise returns FALSE.
	 */
	protected function _doScanThumbnails($idupl)
	{
		$file = sf_api('MODEL', 'FileSqlItem');
		$file->setIdclient( $this->config_area['idclient'] );
		$file->setIdlang( $this->config_area['idlang'] );
		
		// load file
		if($file->loadById((int) $idupl) == FALSE)
		{
			return FALSE;
		}
		
		$bool = TRUE;
		// generate thumbnail if possible
		if( $file->isThumbnailGenerationPossible() == FALSE ||
			$file->generateThumbnails() == FALSE) 
		{
			// failed, but return after updating filedata
			$bool = FALSE;
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		$filedata = array();
		$filedata['filesize'] = filesize($fsm->utf8_decode( $file->getPath() ));
		$filedata['created'] = filemtime($fsm->utf8_decode( $file->getPath() ));
		$imgsize  = getimagesize($fsm->utf8_decode( $file->getPath() ));
		$filedata['pictwidth'] = $imgsize[0];
		$filedata['pictheight'] = $imgsize[1];
		
		try
		{	
			if($file->edit($filedata) == FALSE)
			{
				// error creating file
				return FALSE;
			}
		}
		catch(Exception $e)
		{
			//echo $e->getMessage()."<br />";
			return FALSE;
		}
		
		// $bool = success/fail of thumbnail generation 
		return $bool;
	}
	
	
	/**
	 * Checks if $directoryname is set in forbidden directories.
	 * @param string $directoryname
	 * @return boolean Return TRUE if directory name is forbidden. Otherwise it returns FALSE.
	 */
	protected function _isForbiddenDirectoryName($directoryname)
	{
		foreach($this->config_area['forbidden_directories'] as $forbidden_directory)
		{
			if($forbidden_directory == $directoryname)
			{
				// found directoryname -> forbidden
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * If the allowed file extensions are set, the function checks
	 * if $fileextension is an allowed file extension. Otherwise it
	 * only checks, whether $fileextension is set in forbidden file
	 * extensions.
	 * @param string $fileextension
	 * @return boolean Return TRUE if file extension is forbidden. Otherwise it returns FALSE.
	 */
	protected function _isForbiddenFileExtension($fileextension)
	{
		if(count($this->config_area['allowed_files']) > 0 && strlen($this->config_area['allowed_files'][0]) > 0)
		{
			foreach($this->config_area['allowed_files'] as $allowed_file)
			{
				if($allowed_file == $fileextension)
				{
					// found filename -> allowed
					return FALSE;
				}
			}
			
			return TRUE;
		}
		
		foreach($this->config_area['forbidden_files'] as $forbidden_file)
		{
			if($forbidden_file == $fileextension)
			{
				// found filename -> forbidden
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Retrieves a collection of directories by given $status and $mask.
	 * @param integer $status The result of (status & $mask)
	 * @param integer $mask Modify the current status
	 * @return array Returns an array with found directories as DirectorySqlItem.
	 */
	protected function _getDirectoriesByStatus($status, $mask)
	{
		$dircol = sf_api('MODEL', 'DirectorySqlCollection');
		$dircol->setIdclient($this->config_area['idclient']);
		$dircol->setIdlang($this->config_area['idlang']);
		$dircol->setFreefilter('area', $this->config_area['parent_area_name']);
		$dircol->setFreefilter('(status & '.$mask.')', $status);
		$dircol->generate();
		
		return $dircol->getItemsAsArray();
	}

	/**
	 * Retrieves a collection of directories by given $status and $mask.
	 * Note: This function returns only the ids of the results.
	 * @param integer $status The result of (status & $mask)
	 * @param integer $mask Modify the current status
	 * @return array Returns an array with the IDs of found directories.
	 */
	protected function _getIddirectoriesByStatus($status, $mask)
	{
		$dircol = sf_api('MODEL', 'DirectorySqlCollection');
		$dircol->setIdclient($this->config_area['idclient']);
		$dircol->setIdlang($this->config_area['idlang']);
		$dircol->setFreefilter('area', $this->config_area['parent_area_name']);
		$dircol->setFreefilter('(status & '.$mask.')', $status);
		$dircol->generate(TRUE); // TRUE = generate only IDs, no items
		
		return $dircol->getIdsAsArray();
	}
	
	/**
	 * Modify directory status by given $mask and update them.
	 * @param integer $mask Modify the current status
	 * @param integer $iddirectory >= 0 (optional)
	 * @param booelan $parents Update directories where $iddirectory is parentid
	 * @return boolean Returns TRUE on success. Otherwise returns FALSE.
	 */
	protected function _setDirectoriesStatus($mask, $iddirectory = -1, $parents = FALSE)
	{
		$sql = 'UPDATE
					'.$this->cfg->db('directory').'
				SET
					status = (status | ' . $mask . '),
					lastmodified = lastmodified
				WHERE
					idclient = '.$this->config_area['idclient'];
		
		// if directory is set, restrict update
		if($parents == FALSE && $iddirectory != -1)
		{
			$sql .= ' AND iddirectory = '.$iddirectory;
		}
		else if($parents == TRUE && $iddirectory != -1)
		{
			$sql .= ' AND (parentid = ' . $iddirectory . ' OR iddirectory = ' . $iddirectory . ')';
		}
		
		$sql .= ';';
		
		//echo $sql."<br />";
		$rs = $this->db->Execute($sql);
		
		if ($rs === FALSE || $rs->EOF) 
		{
			return FALSE;
		}
		
		return TRUE;
	}

	/**
	 * Modify directory status by given $mask and clear them.
	 * If $iddirectory is set limit the query to given file.
	 * @param integer $mask Modify the current status
	 * @param integer $iddirectory >= 0 (optional)
	 * @return boolean Returns TRUE on success. Otherwise returns FALSE.
	 */
	protected function _clearDirectoryStatus($mask, $iddirectory = -1)
	{
		$sql = 'UPDATE
					'.$this->cfg->db('directory').'
				SET
					status = (status & '.$mask.'),
					lastmodified = lastmodified
				WHERE
					idclient = '.$this->config_area['idclient'];
		
		if($iddirectory != -1)
		{
			$sql .= ' AND iddirectory = '.$iddirectory;
		}
		
		$sql .= ';';
		
		//echo $sql."<br />";
		$rs = $this->db->Execute($sql);
		
		if ($rs === FALSE || $rs->EOF) 
		{
			return FALSE;
		}
		
		return TRUE;
	}

	/**
	 * Retrieves directories by status and delete each item 
	 * in DB by iterating through the collection.
	 * @return void
	 */
	protected function _deleteDirectoriesNotFound()
	{
		// get all the directories to be deleted to delete all files in cms_upl, ignore system dirs
		$directories = $this->_getDirectoriesByStatus(0x30, 0x74);
		
		$arrayobject = new ArrayObject($directories);
		$iterator = $arrayobject->getIterator();
		
		while($iterator->valid())
		{
			try
			{
				//echo $iterator->current()->getPath()."<br />";
				$iterator->current()->deleteOnlyDb();
			}
			catch(Exception $e)
			{
				//echo $e->getMessage()."<br />";
			}
			
			$iterator->next();
		}
	}

	/**
	 * Retrieves a collection of files by given $status and $mask and 
	 * return only the ids of the results.
	 * @param integer $status The result of (status & $mask)
	 * @param integer $mask Modify the current status
	 * @return array Returns an array with the IDs of found files.
	 */
	protected function _getIduplByStatus($status, $mask)
	{
		$filecol = sf_api('MODEL', 'FileSqlCollection');
		$filecol->setIdclient($this->config_area['idclient']);
		$filecol->setIdlang($this->config_area['idlang']);
		$filecol->setFreefilter('area', $this->config_area['parent_area_name']);
		$filecol->setFreefilter('(status & '.$mask.')', $status);
		$filecol->generate(TRUE); // TRUE = generate only IDs, no items
		
		return $filecol->getIdsAsArray();
	}

	/**
	 * Modify file status by given $mask and clear them.
	 * If $idupl is set limit the query to given file.
	 * @param integer $mask Modify the current status
	 * @param integer $idupl >= 0 (optional)
	 * @return boolean Returns TRUE on success. Otherwise returns FALSE.
	 */
	protected function _clearFileStatus($mask, $idupl = -1)
	{
		$sql = 'UPDATE
					'.$this->cfg->db('upl').'
				SET
					status = (status & '.$mask.'),
					lastmodified = lastmodified
				WHERE
					idclient = '.$this->config_area['idclient'];
		
		if($idupl != -1)
		{
			$sql .= ' AND idupl = '.$idupl;
		}
		
		$sql .= ';';
		
		//echo $sql."<br />";
		$rs = $this->db->Execute($sql);
		
		if ($rs === FALSE || $rs->EOF) 
		{
			return FALSE;
		}
		
		return TRUE;
	}

	/**
	 * Delete files if client setting remove_files_404 is TRUE.
	 * Otherwise only update the status and delete nothing.
	 * @return boolean Returns TRUE on success. Otherwise returns FALSE.
	 */
	protected function _deleteFilesNotFound()
	{
		// check for files not updated to remove them if config-value is set to do so
		if($this->config_area['remove_files_404'] == TRUE)
		{
			$sql = 'DELETE u, ul
				FROM
					'.$this->cfg->db('upl').' AS u
				LEFT JOIN
					'.$this->cfg->db('upl_lang').' AS ul
					ON u.idupl = ul.idupl
				WHERE
					(u.status & 0x14) = 0x10
					AND u.idclient = '.$this->config_area['idclient'].'
					AND u.area = "'.$this->config_area['parent_area_name'].'"';
		}
		// otherwise delete all scan marks even if remove_file_404 contains false
		else
		{
			$sql = 'UPDATE
					'.$this->cfg->db('upl').'
				SET
					status = (status & 0xEF),
					lastmodified = lastmodified
				WHERE
					idclient = '.$this->config_area['idclient'].'
					AND u.area = '.$this->config_area['parent_area_name'];
		}
		$sql .= ';';
		
		//echo $sql."<br />";
		$rs = $this->db->Execute($sql);
		
		if ($rs === FALSE || $rs->EOF) 
		{
			return FALSE;
		}
		
		return TRUE;
	}
}
?>