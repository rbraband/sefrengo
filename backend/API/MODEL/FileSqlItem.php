<?php

$this->includeClass('INTERFACE', 'Filesystem');
$this->includeClass('MODEL', 'AbstractSqlItem');

class SF_MODEL_FileSqlItem extends SF_MODEL_AbstractSqlItem
							implements  SF_INTERFACE_Filesystem
{
	/**
	 * Stores all selected dirnames in an
	 * static array. So every item can use it.
	 * @var array
	 */
	protected static $dirname_cache = array();
	
	/**
	 * Stores all selected filetypes in an
	 * static array. So every item can use it.
	 * @var array
	 */
	protected static $filetype_cache = array();
	
	/**
	 * Stores all thumbnail settings in a
	 * static array. So every item can use it.
	 * The default values will be overwritten by
	 * client configuration in constructor function.
	 * @var array
	 */
	protected static $thumbnail_settings = array(
		'is_default' => TRUE, // flag is deleted in constructor
		'generate_thumb' => array('gif','jpg','jpeg','png'),
		'thumb_extension' => '_cms_thumb',
		'thumb_size' => '100',
		'thumb_aspect_ratio' => '1',
		'more_thumb_size' => array(),//array('200', '300', '400'),
		'more_thumb_aspect_ratio' => array(),//array('0', '2', '3'),
	);
	
	/**
	 * Stores all other client settings (except
	 * thumbnail settings) in an static array.
	 * So every item can use it.
	 * The default values will be overwritten by
	 * client configuration in constructor function.
	 * @var array
	 */
	protected static $client_settings = array(
		'is_default' => TRUE, // flag is deleted in constructor
		'allow_invalid_filenames' => 1,
	);
	
	/**
	 * Stores the table name
	 * @var string
	 */
	protected $table_upl = '';
	protected $table_upl_lang = '';
	
	/**
	 * Stores data from the file in the filesystem 
	 * @var array
	 */
	protected $filesystem = array(
		'dirname' => '',
		'content' => ''
	);
	
	/**
	 * Stores the root directories by area
	 * @var array
	 */
	protected $rootdirectory = array();
	
	/**
	 * Stores the different perms for the object.
	 * The default type can be overwritten.
	 * @var array
	 */
	protected $objperm = array(
		'default' => array(
			'type' => 'file'
		),
		'view' => array(
			'permid' => 17
		),
		'create' => array(
			'permid' => 18,
			// check perm for directory (not file!) where to create the file 
			'type' => 'directory'
		),
		'edit' => array(
			'permid' => 19
		),
		'delete' => array(
			'permid' => 21
		),
		'setperms' => array(
			'permid' => 22
		),
		'import' => array(
			'permid' => 23
		),
		'export' => array(
			'permid' => 24
		),
		'upload' => array(
			'permid' => 25,
			// check perm for directory (not file!) where to upload the file
			'type' => 'directory' 
		),
		'download' => array(
			'permid' => 26
		),
		'copy' => array(
			'permid' => 28
		)
	);
	
	/**
	 * Stores the registered item editors
	 * @var array
	 */
	protected $item_editors = array();
	
	
	/**
	 * Constructor sets up the model with relevant tables and releated fields.
	 * @return void
	 */
	public function __construct()
	{
		// call AbstractSqlItem constructor
		parent::__construct();
		
		// set root directories
		$this->_setRootdirectoryForArea();
		
		// set table names
		$this->table_upl = $this->cfg->db('upl');
		$this->table_upl_lang = $this->cfg->db('upl_lang');
		
		// table upl
		$this->_addTable($this->table_upl);
		$this->_addTableFields(
			$this->table_upl,
			array(
				'filename' => '',
				'iddirectory' => '',
				'idfiletype' => '',
				'pictwidth' => '',
				'pictheight' => '',
				'pictcolor' => '',
				'pictthumbwidth' => '',
				'pictthumbheight' => '',
				'filesize' => '',
				'area' => '',
				'status' => ''
			)
		);
		$this->_addSerializedFields(
			$this->table_upl,
			array(
				'configdata' => ''
			)
		);
		$this->_addDefaultFields($this->table_upl);
		$this->_addRowMapping(
			$this->table_upl,
			array(
				'id' => 'idupl'
			)
		);
		$this->_addDisabledFields(
			$this->table_upl,
			array(
				'idlang' => TRUE,
				'ip' => TRUE,
				'groupkey' => TRUE
			)
		);
		
		
		// table upl_lang
		$this->_addTable($this->table_upl_lang);
		$this->_addTableFields(
			$this->table_upl_lang,
			array(
				'idupl' => '',
				'title' => '',
				'description' => ''
			)
		);
		$this->_addForeignkeys(
			$this->table_upl_lang,
			array(
				'idupl' => array(
					'table' => $this->table_upl,
					'foreignkey' => 'id'
				)
			)
		);
		$this->_addDefaultFields($this->table_upl_lang);
		$this->_addRowMapping(
			$this->table_upl_lang,
			array(
				'id' => 'idupllang'
			)
		);
		$this->_addDisabledFields(
			$this->table_upl_lang,
			array(
				'ip' => TRUE,
				'groupkey' => TRUE
			)
		);
		
		// set thumbnail settings if not yet set from client configuration 
		if(self::$thumbnail_settings['is_default'] == TRUE)
		{
			// get default thumbnail settings and clear it
			$thumbnail_settings = self::$thumbnail_settings;
			self::$thumbnail_settings = array();
			
			$thumbnail_settings['generate_thumb'] = explode(',', $this->cfg->client('generate_thumb', implode(',', $thumbnail_settings['generate_thumb'])));
			$thumbnail_settings['thumb_extension'] = $this->cfg->client('thumbext', $thumbnail_settings['thumb_extension']);
			$thumbnail_settings['thumb_size'] = $this->cfg->client('thumb_size', $thumbnail_settings['thumb_size']);
			// note the underscore between $client_cfg['thumb_aspectratio'] and 'thumb_aspect_ratio'!
			$thumbnail_settings['thumb_aspect_ratio'] = $this->cfg->client('thumb_aspectratio', $thumbnail_settings['thumb_aspect_ratio']);
			$thumbnail_settings['more_thumb_size'] = explode(',', $this->cfg->client('more_thumb_size', implode(',', $thumbnail_settings['more_thumb_size'])));
			$thumbnail_settings['more_thumb_aspect_ratio'] = explode(',', $this->cfg->client('more_thumb_aspect_ratio', implode(',', $thumbnail_settings['more_thumb_aspect_ratio'])));
			
			unset($thumbnail_settings['is_default']);
			
			$this->setGlobalThumbnailSettings($thumbnail_settings);
		}

		// set client settings if not yet set
		if(self::$client_settings['is_default'] == TRUE)
		{
			self::$client_settings['allow_invalid_filenames'] = (int) $this->cfg->client('allow_invalid_filenames', self::$client_settings['allow_invalid_filenames']);
			unset(self::$client_settings['is_default']);
		}
	}
	
	/**
	 * Returns the absolute path to the file.
	 * The value is available, after the file is loaded.
	 * Use the parameter $area to get another root directory.
	 * @param string $area
	 * @param string $directory 
	 * @param string $filename
	 * @return string Returns the path. If no path available returns an empty string.
	 */
	public function getPath($area = '', $directory = '', $filename = '')
	{
		$area = ($area === '') ? $this->getField('area') : $area;
		
		return $this->rootdirectory[$area]['path'].$this->getRelativePath($directory, $filename);
	}

	/**
	 * Returns the relative path to the file.
	 * The value is available, after the directory is loaded.
	 * Use the parameter $area to get another root directory.
	 * @param string $parentdirectory 
	 * @param string $directory
	 * @return string Returns the path. If no path available returns an empty string.
	 */
	public function getRelativePath($directory = '', $filename = '')
	{
		$directory = ($directory === '') ? $this->filesystem['dirname'] : $directory;
		$filename = ($filename === '') ? $this->getField('filename') : $filename;
		
		return $directory.$filename;
	}

	/**
	 * Returns the html path to the file.
	 * The value is available, after the file is loaded.
	 * Use the parameter $area to get another root directory.
	 * @param string $area 
	 * @param string $directory 
	 * @param string $filename 
	 * @return string Returns the path. If no path available returns an empty string.
	 */
	public function getHtmlPath($area = '', $directory = '', $filename = '')
	{
		$area = ($area === '') ? $this->getField('area') : $area;
		
		return $this->rootdirectory[$area]['htmlpath'].$this->getRelativePath($directory, $filename);
	}
	
	/**
	 * Reads the content of the file to the given length.
	 * With $clearcache the file is read again. 
	 * @param integer $length default is 204800 Bytes = 200 KB
	 * @param boolean $clearcache
	 * @return string Returns the content of file in filesystem
	 */
	public function getContent($length = -1, $clearcache = FALSE)
	{
		$length = ($length == -1) ? 204800 : (int)$length; 
		
		if($clearcache == TRUE)
		{
			$this->filesystem['content'] = '';
		}
		
		if($this->filesystem['content'] == '')
		{
			$fsm = sf_api('LIB', 'FilesystemManipulation');
			$this->filesystem['content'] = $fsm->readContentFromFile($this->getPath(), $length);
			unset($fsm);
		}
		
		return $this->filesystem['content'];
	}
	
	/**
	 * Set cached content of the file directly.
	 * Use this function carefully!
	 * Possible use in external editors.
	 * @param string $content
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setContent($content)
	{
		$this->filesystem['content'] = $content;
		
		return TRUE;
	}
	
	/**
	 * Moves an uploaded file from temporary directory to
	 * destination directory. Insert a new file in the DB.
	 * The parameter $fields contains the metadata information
	 * about the file.
	 * Note: The function retrieves the fileextension automatically
	 * from file.
	 * @param array $fields 
	 * @param string $upload_tmp_name Temporary filename when uploaded a file with HTTP POST 
	 * @param string $upload_mimetype Mimetype of the uploaded file 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function upload($fields, $upload_tmp_name, $upload_mimetype)
	{
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		if($fsm->isUploadedFile($upload_tmp_name) == FALSE || $upload_tmp_name == '')
		{
			throw sf_exception('error', 'missing_upload');
			return FALSE;
		};
		
		if(array_key_exists('iddirectory', $fields) == FALSE || $fields['iddirectory'] < 0)
		{
			throw sf_exception('error', 'missing_iddirectory');
			return FALSE;
		};
		
		if(array_key_exists('filename', $fields) == FALSE)
		{
			throw sf_exception('error', 'missing_filename');
			return FALSE;
		};
		
		// check perm: uses iddirectory because perm type is directory and reset parent id
		if($this->hasPerm('upload', $fields['iddirectory'], 0) == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		// check if invalid filenames are allowed
		$sanitize_filename = $this->_sanitizeFilename($fields['filename']);
		if($sanitize_filename === FALSE)
		{
			throw sf_exception('error', 'validation_error_filename');
			return FALSE;
		}
		$fields['filename'] = $sanitize_filename;
		unset($sanitize_filename);
		
		$this->filesystem['dirname'] = $this->_getDirnameByIddirectory( $fields['iddirectory'] );
		$path = $this->getPath($fields['area'], '', $fields['filename']);
		$fields['idfiletype'] = $this->_getIdfiletypeByFiletype( $fsm->getPathinfo($fields['filename'], 'extension'), $upload_mimetype);
		
		if($fsm->fileExists($path) == TRUE)
		{
			throw sf_exception('error', 'file_exists_in_destination', array('path' => $path));
			return FALSE;
		}
		
		if($fsm->moveUploadedFile($upload_tmp_name, $this->getPath($fields['area'], '', $fields['filename'])) === FALSE)
		{
			throw sf_exception('error', 'move_uploaded_file_failed', array('path' => $path));
			return FALSE;
		}
		
		unset($fields['id'], $fields['idupl'], $fields['idupllang']);
		
		foreach($fields as $field => $value)
		{
			$this->setField($field, $value);
		}
		// set idclient for upl_lang seperatly
		if(array_key_exists('idclient', $fields))
		{
			$this->setField('idclient', $fields['idclient'], $this->table_upl_lang);
		}
		
		if($this->save() == FALSE)
		{
			// remove file, if save in db fails
			$fsm->deleteFile($this->getPath($fields['area'], '', $fields['filename']));
			
			throw sf_exception('error', 'save_file_to_db_failed', array('path' => $this->getPath($fields['area'], '', $fields['filename'])));
			return FALSE;
		}
		
		// set userright after upload
		$this->_addUserRights($fields);
		
		// generate thumbnails if possible
		if($this->isThumbnailGenerationPossible() == TRUE)
		{
			if($this->_generateThumbnails() == FALSE)
			{
				$this->_deleteThumbnails();
				throw sf_exception('warning', 'generate_thumbnail_failed', array('path' => $this->getPath($fields['area'], '', $fields['filename'])));
				return FALSE;
			}
		}
		
		unset($fsm);
		
		return TRUE;
	}
	
	/**
	 * Copy an file from a directory path to destination directory.
	 * Insert a new file in the DB.
	 * The parameter $fields contains the metadata information
	 * about the file.
	 * Note: The function retrieves the file extension automatically
	 * from file.
	 * @param array $fields 
	 * @param string $source_path
	 * @param boolean $copy_file
	 * @param boolean $generate_thumbnails
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function addByDirectoryPath($fields, $source_path, $copy_file = TRUE, $generate_thumbnails = TRUE)
	{
		if($source_path == '')
		{
			throw sf_exception('error', 'missing_upload');
			return FALSE;
		};
		
		if(array_key_exists('iddirectory', $fields) == FALSE || $fields['iddirectory'] < 0)
		{
			throw sf_exception('error', 'missing_iddirectory');
			return FALSE;
		};
		
		if(array_key_exists('filename', $fields) == FALSE)
		{
			throw sf_exception('error', 'missing_filename');
			return FALSE;
		};

		// check perm: uses iddirectory because perm type is directory and reset parent id
		if($this->hasPerm('upload', $fields['iddirectory'], 0) == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		// check if invalid filenames are allowed
		$sanitize_filename = $this->_sanitizeFilename($fields['filename']);
		if($sanitize_filename === FALSE)
		{
			throw sf_exception('error', 'validation_error_filename');
			return FALSE;
		}
		$fields['filename'] = $sanitize_filename;
		unset($sanitize_filename);
		
		if(array_key_exists('dirname', $fields) == FALSE || $fields['dirname'] == '')
		{
			$this->filesystem['dirname'] = $this->_getDirnameByIddirectory( $fields['iddirectory'] );
		}
		else
		{
			$this->filesystem['dirname'] = $fields['dirname'];
		}
		$path = $this->getPath($fields['area'], '', $fields['filename']);
		$fields['idfiletype'] = $this->_getIdfiletypeByFiletype( $fsm->getPathinfo($fields['filename'], 'extension'));
		
		if($copy_file == TRUE)
		{
			if($fsm->fileExists($path) == TRUE)
			{
				throw sf_exception('error', 'file_exists_in_destination', array('path' => $path));
				return FALSE;
			}
			
			if($fsm->copyFile($source_path, $path) === FALSE)
			{
				throw sf_exception('error', 'copy_file_failed', array('path' => $path));
				return FALSE;
			}
		}
		
		unset($fields['id'], $fields['idupl'], $fields['idupllang']);
		
		foreach($fields as $field => $value)
		{
			$this->setField($field, $value);
		}
		// set idclient for upl_lang seperatly
		if(array_key_exists('idclient', $fields))
		{
			$this->setField('idclient', $fields['idclient'], $this->table_upl_lang);
		}
		
		if($this->save() == FALSE)
		{
			// remove file, if save in db fails
			$fsm->deleteFile($this->getPath($fields['area'], '', $fields['filename']));
			
			throw sf_exception('error', 'save_file_to_db_failed', array('path' => $this->getPath($fields['area'], '', $fields['filename'])));
			return FALSE;
		}
		
		// set userright after upload
		$this->_addUserRights($fields);
	
		// generate thumbnails if possible
		if($generate_thumbnails == TRUE && $this->isThumbnailGenerationPossible() == TRUE)
		{
			if($this->_generateThumbnails() == FALSE)
			{
				$this->_deleteThumbnails();
				throw sf_exception('warning', 'generate_thumbnail_failed', array('path' => $this->getPath($fields['area'], '', $fields['filename'])));
			}
		}
		
		unset($fsm);
		
		return TRUE;
	}
	
	/**
	 * Creates a new file in the DB and the filesystem
	 * and puts content into it.
	 * The parameter $fields contains the metadata information
	 * about the file.
	 * Note: The function retrieves the file extension automatically
	 * from file.
	 * @see API/INTERFACES/SF_INTERFACE_Filesystem#create($fields)
	 * @param array $fields
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function create($fields)
	{
		if(array_key_exists('iddirectory', $fields) == FALSE || $fields['iddirectory'] < 0)
		{
			throw sf_exception('error', 'missing_iddirectory');
			return FALSE;
		};
		
		if(array_key_exists('filename', $fields) == FALSE)
		{
			throw sf_exception('error', 'missing_filename');
			return FALSE;
		};

		// check perm: uses iddirectory as id because perm type is directory and reset parent id
		if($this->hasPerm('create', $fields['iddirectory'], 0) == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		// check if invalid filenames are allowed
		$sanitize_filename = $this->_sanitizeFilename($fields['filename']);
		if($sanitize_filename === FALSE)
		{
			throw sf_exception('error', 'validation_error_filename');
			return FALSE;
		}
		$fields['filename'] = $sanitize_filename;
		unset($sanitize_filename);
		
		$this->filesystem['dirname'] = $this->_getDirnameByIddirectory( $fields['iddirectory'] );
		$path = $this->getPath($fields['area'], '', $fields['filename']);
		$fields['idfiletype'] = $this->_getIdfiletypeByFiletype( $fsm->getPathinfo($fields['filename'], 'extension') );
		
		if($fsm->fileExists($path) == TRUE)
		{
			throw sf_exception('error', 'file_exists_in_destination', array('path' => $path));
			return FALSE;
		}
		
		unset($fields['id'], $fields['idupl'], $fields['idupllang']);
		
		foreach($fields as $field => $value)
		{
			$this->setField($field, $value);
		}
		// set idclient for upl_lang seperatly
		if(array_key_exists('idclient', $fields))
		{
			$this->setField('idclient', $fields['idclient'], $this->table_upl_lang);
		}
		
		// begin save editor content
		if($this->callOnSaveItemBegin() == FALSE)
		{
			throw sf_exception('error', 'write_content_to_file_failed', array('path' => $path));
			return FALSE;
		}
		
		// save fields in db
		if($this->save() == FALSE)
		{
			// rollback save editor content
			if($this->callOnSaveItemRollback() == FALSE)
			{
				//echo "callOnSaveItemRollback failed";
			}
			
			// remove file, if save in db fails
			$fsm->deleteFile($this->getPath($fields['area'], '', $fields['filename']));
			
			throw sf_exception('error', 'save_file_to_db_failed', array('path' => $this->getPath($fields['area'], '', $fields['filename'])));
			return FALSE;
		}
		
		// set userright after upload
		$this->_addUserRights($fields);
		
		// commit save editor content
		if($this->callOnSaveItemCommit() == FALSE)
		{
			//echo "callOnSaveItemCommit failed";
		}
		
		unset($fsm);
		
		return TRUE;
	}
	
	/**
	 * Creates a file by the given fields only in database
	 * @param array $fields 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function createOnlyDb($fields)
	{
		if(array_key_exists('iddirectory', $fields) == FALSE || $fields['iddirectory'] < 0)
		{
			throw sf_exception('error', 'missing_iddirectory');
			return FALSE;
		};
		
		if(array_key_exists('filename', $fields) == FALSE)
		{
			throw sf_exception('error', 'missing_filename');
			return FALSE;
		};

		// check perm: uses iddirectory as id because perm type is directory and reset parent id
		if($this->hasPerm('create', $fields['iddirectory'], 0) == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		// check if invalid filenames are allowed
		$sanitize_filename = $this->_sanitizeFilename($fields['filename']);
		if($sanitize_filename === FALSE)
		{
			throw sf_exception('error', 'validation_error_filename', array('filename' => $fields['filename']));
			return FALSE;
		}
		// special case for auto correction:
		// try to correct the filename -> rename the physical file
		else if(self::$client_settings['allow_invalid_filenames'] == 2)
		{
			$this->filesystem['dirname'] = $this->_getDirnameByIddirectory( $fields['iddirectory'] );
			$oldname = $this->getPath($fields['area'], '', $fields['filename']);
			$newname = $this->getPath($fields['area'], '', $sanitize_filename);
			
			if($fsm->renameFile($oldname, $newname) == FALSE)
			{
				throw sf_exception('error', 'invalid_filename_correction_failed', array('newname' => $newname, 'oldname' => $oldname));
				return FALSE;
			}
		}
		$fields['filename'] = $sanitize_filename;
		unset($sanitize_filename);
		
		$fields['idfiletype'] = $this->_getIdfiletypeByFiletype( $fsm->getPathinfo($fields['filename'], 'extension') );
		
		unset($fields['id'], $fields['idupl'], $fields['idupllang']);
		
		foreach($fields as $field => $value)
		{
			$this->setField($field, $value);
		}
		// set idclient for upl_lang seperatly
		if(array_key_exists('idclient', $fields))
		{
			$this->setField('idclient', $fields['idclient'], $this->table_upl_lang);
		}
		
		// begin save editor content
		if($this->callOnSaveItemBegin() == FALSE)
		{
			throw sf_exception('error', 'write_content_to_file_failed', array('path' => $path));
			return FALSE;
		}
		
		// save fields in db
		if($this->save() == FALSE)
		{
			// rollback save editor content
			if($this->callOnSaveItemRollback() == FALSE)
			{
				//echo "callOnSaveItemRollback failed";
			}
				
			throw sf_exception('error', 'save_file_to_db_failed', array('path' => $this->getPath($fields['area'], '', $fields['filename'])));
			return FALSE;
		}
		
		// set userright after upload
		$this->_addUserRights($fields);
		
		// commit save editor content
		if($this->callOnSaveItemCommit() == FALSE)
		{
			//echo "callOnSaveItemCommit failed";
		}
		
		unset($fsm);
		
		return TRUE;
	}
	
	/**
	 * Edits a file by given $fields.
	 * If the filename changed it is renamed in filesystem.
	 * Also calls item editor onSaveItem function
	 * that handles saving the content to filesystem.
	 * Global variables needed to set group rights if user has permission 'setperms'.
	 * @see API/INTERFACES/SF_INTERFACE_Filesystem#edit($fields)   
	 * @global string $cms_gruppenids Comma sperated list of group ids
	 * @global string $cms_gruppenrechte Comma seperated list of rights for each group 
	 * @global string $cms_gruppenrechtegeerbt Comma seperated list if bequeathed rights 
	 * @global string $cms_gruppenrechteueberschreiben Comma seperated list of overwritten group rights 
	 * @param array $fields
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function edit($fields)
	{
		if($this->getId() <= 0)
		{
			throw sf_exception('error', 'file_is_not_loaded');
			return FALSE;
		};

		// check perm
		if($this->hasPerm('edit') == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$changecontent = $changename = $changenamewarning = FALSE;
		
		// prepare save editor content
		if($this->callOnSaveItemBegin() == FALSE)
		{
			//echo "callOnSaveItemBegin failed";
		}
		
		if(array_key_exists('filename', $fields) == TRUE)
		{
			// check if invalid filenames are allowed
			$sanitize_filename = $this->_sanitizeFilename($fields['filename']);
			if($sanitize_filename === FALSE)
			{
				throw sf_exception('error', 'validation_error_filename');
				return FALSE;
			}
			$fields['filename'] = $sanitize_filename;
			unset($sanitize_filename);
			
			// rename file if different and get new fileextension
			if(strcmp($fields['filename'], $this->getField('filename')) != 0)
			{
				$oldname = $this->getPath();
				$newname = $this->getPath('', '', $fields['filename']);
				
				if($fsm->fileExistsCase($newname) != 1)
				{
					$changename = $fsm->renameFile($oldname, $newname);
					
					$oldext = $fsm->getPathinfo($this->getField('filename'), 'extension');
					$newext = $fsm->getPathinfo($fields['filename'], 'extension');
					// get new idfiletype if different
					if($oldext != $newext)
					{
						$fields['idfiletype'] = $this->_getIdfiletypeByFiletype($newext);
					}
				}
				else
				{
					$changenamewarning = 'file_exists_in_destination';
				}
			}
		}
		
		foreach($fields as $field => $value)
		{
			$this->setField($field, $value);
		}
		// set idclient for upl_lang seperatly
		if(array_key_exists('idclient', $fields))
		{
			$this->setField($fields['idclient'], $value, $this->table_upl_lang);
		}
		
		if($this->save() == FALSE)
		{
			// if save fails revert the changes
			if($changename == TRUE)
			{
				$fsm->renameFile($newname, $oldname);
			}
			
			// rollback save editor content
			if($this->callOnSaveItemRollback() == FALSE)
			{
				//echo "callOnSaveItemRollback failed";
			}
			
			throw sf_exception('error', 'save_file_to_db_failed', array('newname' => $newname, 'oldname' => $oldname));
			return FALSE;
		}
		
		// set rights
		if($this->hasPerm('setperms') == TRUE)
		{
			global $cms_gruppenids, $cms_gruppenrechte, $cms_gruppenrechtegeerbt, $cms_gruppenrechteueberschreiben;
			
			$this->setGroupRights(
				$cms_gruppenids,
				$cms_gruppenrechte,
				$cms_gruppenrechtegeerbt,
				$cms_gruppenrechteueberschreiben,
				'', // lang
				0xFFFFFFFF, // bitmask
				$this->getField('iddirectory') // parentid
			);
		}
		
		// commit save editor content
		if($this->callOnSaveItemCommit() == FALSE)
		{
			//echo "callOnSaveItemCommit failed";
		}
		
		// send warning that the file exists
		if(is_string($changenamewarning))
		{
			throw sf_exception('warning', 'file_exists_in_destination', array('filename' => $newname));
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Copy file to new destination directory.
	 * @see API/INTERFACES/SF_INTERFACE_Filesystem#copy($fields)
	 * @param array $fields 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function copy($fields)
	{
		if($this->getId() <= 0)
		{
			throw sf_exception('error', 'file_is_not_loaded');
			return FALSE;
		};
		
		if(array_key_exists('iddirectory', $fields) == FALSE)
		{
			throw sf_exception('error', 'missing_iddirectory');
			return FALSE;
		};

		// check perm with source directory as parent_id
		if($this->hasPerm('copy') == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		// take the current name if nothing is set
		if(!array_key_exists('filename', $fields))
		{
			$fields['filename'] = $this->getField('filename');
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		// check if invalid filenames are allowed
		$sanitize_filename = $this->_sanitizeFilename($fields['filename']);
		if($sanitize_filename === FALSE)
		{
			throw sf_exception('error', 'validation_error_filename');
			return FALSE;
		}
		$fields['filename'] = $sanitize_filename;
		unset($sanitize_filename);
		
		$source = $this->getPath();
		$dest = $this->getPath('', $this->_getDirnameByIddirectory( $fields['iddirectory'] ), $fields['filename']);
	
		if($fsm->fileExists($dest) == TRUE)
		{
			throw sf_exception('error', 'file_exists_in_destination', array('source' => $source, 'dest' => $dest));
			return FALSE;
		}
		
		if($fsm->copyFile($source, $dest) == FALSE)
		{
			throw sf_exception('error', 'copy_file_failed', array('source' => $source, 'dest' => $dest));
			return FALSE;
		}
		
		$copywarning = FALSE;
		// copy thumbnails if possible
		if($this->isThumbnailGenerationPossible() == TRUE)
		{
			if($this->_copyThumbnails($fields['iddirectory'], $fields['filename']) == FALSE)
			{
				$copywarning = 'copy_thumbnail_failed';
			}
		}
		
		// clone this object to hold the data 
		$newitem = clone $this;
		// reset ids to save as new item
		$newitem->setField('id', 0, $this->table_upl);
		$newitem->setField('id', 0, $this->table_upl_lang);
		$newitem->setField('idupl', 0, $this->table_upl_lang);
		// update iddirectory and filename
		$newitem->setField('iddirectory', $fields['iddirectory']);
		$newitem->setField('filename', $fields['filename']);
				
		// save table_upl first ...
		if($newitem->save($this->table_upl) == FALSE)
		{
			// try to delete file from destination
			$fsm->deleteFile($dest);
			
			throw sf_exception('error', 'save_file_to_db_failed', array('filename' => $this->getField('filename')));
			return FALSE;
		}
		
		// ... and copy all available language afterwards
		$tablefields = $this->getMappedFieldsForTable($this->table_upl_lang);
		$insert_fields = implode(",", $tablefields);
		
		// reset idupllang
		$index = array_search($this->mapFieldToRow('id', $this->table_upl_lang), $tablefields);
		if($index !== FALSE) $tablefields[$index] = '0';
		
		// set idupl to new item id
		$index = array_search($this->mapFieldToRow('id', $this->table_upl), $tablefields);
		if($index !== FALSE) $tablefields[$index] = $newitem->getId();
		
		$select_fields = implode(",", $tablefields);
		
		$sql = "INSERT INTO
					".$this->table_upl_lang."
					(".$insert_fields.")
					SELECT ".$select_fields."
						FROM ".$this->table_upl_lang."
						WHERE ".$this->mapFieldToRow('id', $this->table_upl)." = '".$this->getId()."';";
				
		//echo $sql."<br />";
		
		$rs = $this->db->Execute($sql);

		if ($rs === FALSE)
		{
			// try to delete file from destination
			$fsm->deleteFile($dest);
			
			throw sf_exception('error', 'save_file_to_db_failed', array('filename' => $this->getField('filename')));
			return FALSE;
		}
		
		$rs->Close();
		
		// copy rights
		if($this->copyPerms($newitem->getId(), 0, $fields['idlang'], FALSE) == FALSE)
		{
			throw sf_exception('warning', 'copy_rights_failed', array('source' => $source, 'dest' => $dest));
			return FALSE;
		}
		
		// send warning if thumbnails can't copied
		if(is_string($copywarning))
		{
			throw sf_exception('warning', $copywarning, array('source' => $source, 'dest' => $dest));
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Move file to destination and delete the file
	 * from source.
	 * @see API/INTERFACES/SF_INTERFACE_Filesystem#move($fields)
	 * @param array $fields 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function move($fields)
	{
		if($this->getId() <= 0)
		{
			throw sf_exception('error', 'file_is_not_loaded');
			return FALSE;
		};
		
		if(array_key_exists('iddirectory', $fields) == FALSE)
		{
			throw sf_exception('error', 'missing_iddirectory');
			return FALSE;
		};

		// check perm with source directory as parent_id
		if($this->hasPerm('copy') == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		if($this->getField('iddirectory') == $fields['iddirectory'])
		{
			throw sf_exception('error', 'equal_directories');
			return FALSE;
		}
		
		// take the current name if nothing is set
		if(!array_key_exists('filename', $fields))
		{
			$fields['filename'] = $this->getField('filename');
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		// check if invalid filenames are allowed
		$sanitize_filename = $this->_sanitizeFilename($fields['filename']);
		if($sanitize_filename === FALSE)
		{
			throw sf_exception('error', 'validation_error_filename');
			return FALSE;
		}
		$fields['filename'] = $sanitize_filename;
		unset($sanitize_filename);
		
		$source = $this->getPath();
		$dest = $this->getPath('', $this->_getDirnameByIddirectory( $fields['iddirectory'] ), $fields['filename']);
		
		if($fsm->fileExists($dest) == TRUE)
		{
			throw sf_exception('error', 'file_exists_in_destination', array('source' => $source, 'dest' => $dest));
			return FALSE;
		}
		
		// move file
		if($fsm->moveFile($source, $dest) == FALSE)
		{
			throw sf_exception('error', 'move_file_failed', array('source' => $source, 'dest' => $dest));
			return FALSE;
		}
		
		$movewarning = FALSE;
		// move thumbnails if possible
		if($this->isThumbnailGenerationPossible() == TRUE)
		{
			if($this->_moveThumbnails($fields['iddirectory'], $fields['filename']) == FALSE)
			{
				$movewarning = 'move_thumbnail_failed';
			}
		}
		
		// update database
		$this->setField('iddirectory', $fields['iddirectory']);
		$this->setField('filename', $fields['filename']);
		
		if($this->save() == FALSE)
		{
			// try to move the file back
			$fsm->moveFile($dest, $source);
			
			throw sf_exception('error', 'save_file_to_db_failed', array('filename' => $this->getField('filename')));
			return FALSE;
		}
		
		// move rights -> nothing to do
		
		// send warning if thumbnails can't moved
		if(is_string($movewarning))
		{
			throw sf_exception('warning', $movewarning, array('source' => $source, 'dest' => $dest));
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Delete a file
	 * @see API/MODELS/SF_MODEL_AbstractSqlItem#delete($tablename)
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function delete($tablename = '')
	{
		if($this->getId() <= 0)
		{
			throw sf_exception('error', 'file_is_not_loaded');
			return FALSE;
		};

		// check perm with source directory as parent_id
		if($this->hasPerm('delete') == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		// delete file
		// if file_exists() check fails no exception is thrown. It's usefull to delete only the sql item. 
		if($fsm->fileExists($this->getPath()) == TRUE && $fsm->deleteFile( $this->getPath() ) == FALSE)
		{
			throw sf_exception('error', 'delete_file_failed', array('filename' => $this->getField('filename')));
			return FALSE;
		}
		
		$deletewarning = FALSE;
		// delete thumbnails if possible
		if($this->isThumbnailGenerationPossible() == TRUE)
		{
			if($this->_deleteThumbnails() == FALSE)
			{
				$deletewarning = 'delete_thumbnail_failed';
			}
		}
		
		// delete sql item 
		if(parent::delete($tablename) == FALSE)
		{
			throw sf_exception('error', 'delete_file_from_db_failed', array('filename' => $this->getField('filename')));
			return FALSE;
		}
		
		// delete rights
		if($this->deleteAllPerms() == FALSE)
		{
			throw sf_exception('warning', 'delete_rights_failed', array('filename' => $this->getField('filename')));
			return FALSE;
		}
		
		// send warning if thumbnails can't deleted
		if(is_string($deletewarning))
		{
			throw sf_exception('warning', $deletewarning, array('path' => $this->getPath($fields['area'], '', $fields['filename'])));
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Delete a file in database, but not in filesystem
	 * @param string $tablename
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function deleteOnlyDb($tablename = '')
	{
		if($this->getId() <= 0)
		{
			throw sf_exception('error', 'file_is_not_loaded');
			return FALSE;
		};

		// check perm with source directory as parent_id
		if($this->hasPerm('delete') == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		// delete sql item 
		if(parent::delete($tablename) == FALSE)
		{
			throw sf_exception('error', 'delete_file_from_db_failed', array('filename' => $this->getField('filename')));
			return FALSE;
		}
		
		// delete rights
		if($this->deleteAllPerms() == FALSE)
		{
			throw sf_exception('warning', 'delete_rights_failed', array('filename' => $this->getField('filename')));
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Overwrite the function to load the item and
	 * the language data.
	 * @see API/MODELS/SF_MODEL_AbstractSqlItem#loadById($id, $tablename)
	 */
	public function loadById($id, $tablename = '', $fields = '*')
	{
		$bool = TRUE;
		
		// only load this table, if the table contains the requested fields
		if(($tablename == '' || $tablename == $this->table_upl)
			&& ($fields == '*' || ($fields != '*' && $this->isFieldInTable($fields, $this->table_upl) == TRUE)))
		{
			$bool = parent::loadById($id, $this->table_upl, $fields);
			
			// on error, abort immediately
			if($bool == FALSE)
			{
				return FALSE;
			}
		}
		
		// only load this table, if the table contains the requested fields
		if(($tablename == '' || $tablename == $this->table_upl_lang)
			&& ($fields == '*' || ($fields != '*' && $this->isFieldInTable($fields, $this->table_upl_lang) == TRUE)))
		{
			// $id = idupl -> it is corrected in _getLoadSql()
			$bool = parent::loadById($id, $this->table_upl_lang, $fields);
		}
		
		// load information about directory
		$this->filesystem['dirname'] = $this->_getDirnameByIddirectory( $this->getField('iddirectory') );
		
		return $bool;
	}
	
	/**
	 * Load a file item by field filename.
	 * Use $iddirectory to get only one result.
	 * @param string $filename
	 * @param string $iddirectory
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function loadByFilename($filename, $iddirectory)
	{
		$iddirectory = (int) $iddirectory;
		
		if ($filename == '' || $iddirectory < 0)
		{
			return FALSE;
		}
		
		$where = array();
		$where['filename'] = mysql_real_escape_string($filename);
		$where['iddirectory'] = $iddirectory;
		$where['idclient'] = $this->itemcfg['idclient'];
		//$where['idlang'] = $this->itemcfg['idlang']; // not in table_upl
		
		$bool = TRUE;
		$bool = $this->_load($this->table_upl, $where);
		if($bool == FALSE)
		{
			return FALSE;
		}
		$bool = $this->loadById($this->getId(), $this->table_upl_lang);
		return $bool;
	}
	
	/**
	 * Checks by given name if the object has the specific permission.
	 * Overwrite function and set parent_id with current iddirectory.
	 * @param string $name
	 * @param string $id
	 * @param integer $parent_id
	 * @return booelan Returns TRUE if has perm. Otherwise returns FALSE.
	 */
	public function hasPerm($name, $id = FALSE, $parent_id = FALSE)
	{
		if($parent_id === FALSE)
		{
			$parent_id = $this->getField('iddirectory');
		}
		
		return parent::hasPerm($name, $id, $parent_id);
	}
	
	
	/**
	 * Overwrite the creation of the load SQL statement
	 * to write own where clause for language data.
	 * @see API/MODELS/SF_MODEL_AbstractSqlItem#_getLoadSql($tablename, $fields, $where)
	 */
	protected function _getLoadSql($tablename, $fields, $where)
	{
		if($tablename == $this->table_upl_lang)
		{
			$where = "idupl='".$where['id']."' ";
			//$where .= "AND idclient = '".$this->itemcfg['idclient']."' ";
			$where .= "AND idlang = '".$this->itemcfg['idlang']."' ";
		}
		
		$sql = parent::_getLoadSql($tablename, $fields, $where);
		//echo $sql."<br />";
		return $sql;
	}
	
	/**
	 * Overwrite the creation of the delete SQL statement
	 * to use the foreign key for deletion in language table.
	 * @see API/MODELS/SF_MODEL_AbstractSqlItem#_getDeleteSql($tablename, $fields, $where, $foreignkey)
	 */
	protected function _getDeleteSql($tablename, $where, $foreignkey = '')
	{
		if($tablename == $this->table_upl_lang)
		{
			$foreignkey = 'idupl';
		}
		
		$sql = parent::_getDeleteSql($tablename, $where, $foreignkey);
		//echo $sql."<br />";
		return $sql;
	}
	
	
	/**
	 * Retrieve the root directories from the client
	 * configuration for the different areas.
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _setRootdirectoryForArea()
	{
		$this->rootdirectory['css']['path'] = $this->cfg->env('path_frontend_css');
		$this->rootdirectory['css']['htmlpath'] = $this->cfg->env('path_frontend_css_http');
		
		$this->rootdirectory['js']['path'] = $this->cfg->env('path_frontend_js');
		$this->rootdirectory['js']['htmlpath'] = $this->cfg->env('path_frontend_js_http');
		
		$this->rootdirectory['fm']['path'] = $this->cfg->env('path_frontend_fm');
		$this->rootdirectory['fm']['htmlpath'] = $this->cfg->env('path_frontend_fm_http');
		
		return TRUE;
	}
	
	/**
	 * Get the dirname to the given iddirectory
	 * @param integer $iddirectory
	 * @return string|boolean Returns the dirname to the iddirectory. If no directory found or error occurs returns FALSE.
	 */
	protected function _getDirnameByIddirectory($iddirectory)
	{
		if($iddirectory === FALSE)
		{
			return FALSE;
		}
		if(array_key_exists($iddirectory, self::$dirname_cache) == TRUE)
		{
			return self::$dirname_cache[$iddirectory]; 
		}
		
		$directory = sf_api('MODEL', 'DirectorySqlItem');
		$directory->setIdclient( $this->getField('idclient') );
		$directory->setIdlang( $this->getField('idlang') );
		
		if($directory->loadById((int)$iddirectory, '', array('dirname')))
		{
			self::$dirname_cache[$iddirectory] = $directory->getField('dirname');
			return self::$dirname_cache[$iddirectory];
		}
		
		unset($directory);
		return FALSE;
	}
	
	/**
	 * Get idfiletype for filetype.
	 * If $mimetype is not empty and $filetype is not found, then insert as new filetype.
	 * @param string $filetype 
	 * @param string $mimetype
	 * @return integer|boolean Returns the idfiletype. If no filetype found or error occurs returns FALSE.
	 */
	protected function _getIdfiletypeByFiletype($filetype, $mimetype = '')
	{
		if(array_key_exists($filetype, self::$filetype_cache) == TRUE)
		{
			return self::$filetype_cache[$filetype]; 
		}
		
		$ft_data = array();
		$ft_data['filetype'] = $filetype;
		$ft_data['mimetype'] = $mimetype;
		
		$ft = sf_api('MODEL', 'FiletypeSqlItem');
		if($ft->loadByFiletype($ft_data, TRUE) == TRUE)
		{
			self::$filetype_cache[$filetype] = $ft->getId();
			return self::$filetype_cache[$filetype];
		}
		return FALSE;
	}
	
	/**
	 * Add the user rights/permission for the item if perms exists.
	 * @param Array $fields
	 * @return Boolean|null Returns TRUE on success. Otherwise returns FALSE. And returns 'null' if no rights added.
	 */
	protected function _addUserRights($fields)
	{
		// set userright after upload
		// 1. if userrights are existing ... no changes
		// 2. no userrights are existing ... xcopy directory-rights to the file, reset the directory bits
		if ($this->permsExists() == FALSE)
		{
			// Note: If file is in root directory (iddirectory == 0) then use area rights (e.g. area_fm, area_css, ...). Otherwise use object perm for upload.
			$permname = ((int)$fields['iddirectory'] == 0) ? 'area_'.$fields['area'] : $this->objperm['upload']['type'];

			// copy userrights from directory
			$bool = $this->cfg->perm()->xcopy_perm(
				$fields['iddirectory'],
				$permname,
				$this->getId(),
				$this->objperm['default']['type'],
				0x01B50000,
				0,
				0,
				TRUE
			);
			if($bool == FALSE)
			{
				throw sf_exception('warning', 'add_rights_failed', array('path' => $this->getPath($fields['area'], '', $fields['filename'])));
				return FALSE;
			}
			return TRUE;
		}
		return null;
	}
	
	/* ******************
	 *  THUMBNAIL GENERATION
	 * *****************/
	
	/**
	 * Returns the thumbnail settings from the static array
	 * @return array Returns the static array
	 */
	public function getGlobalThumbnailSettings()
	{
		return self::$thumbnail_settings;
	}

	/**
	 * Stores the thumbnail settings in a static array.
	 * So every item uses the same settings.
	 * @param array $settings
	 */
	public function setGlobalThumbnailSettings($settings)
	{
		// merge sizes
		$sizes = array();
		if(strlen($settings['more_thumb_size'][0]) > 0)
		{
			$sizes = $settings['more_thumb_size'];
		} 
		array_unshift($sizes, $settings['thumb_size']);
		unset($settings['more_thumb_size'], $settings['thumb_size']);
		$settings['sizes'] = $sizes;
		
		// merge aspect ratio
		$aspect_ratio = array();
		if(strlen($settings['more_thumb_aspect_ratio'][0]) > 0)
		{
			$aspect_ratio = $settings['more_thumb_aspect_ratio'];
		} 
		array_unshift($aspect_ratio, $settings['thumb_aspect_ratio']);
		unset($settings['more_thumb_aspect_ratio'], $settings['thumb_aspect_ratio']);
		$settings['aspect_ratio'] = $aspect_ratio;
		
		self::$thumbnail_settings = $settings;
	}
	
	/**
	 * Returns an array with thumbnail data for the given $thumbindex.
	 * If no $thumbindex given returns an array with data for all available
	 * thumbnails.
	 * If no thumbnails available the function returns an empty array.
	 * 
	 * @param integer $thumbindex optional
	 * @return array Returns an array with data for all thumbnails or only for $thumbindex
	 */
	public function getThumbnails($thumbindex = -1)
	{
		$thumbnails = array();
		
		// file not loaded
		if($this->getId() <= 0)
		{
			return $thumbnails;
		}
		
		$configdata = $this->getField('configdata');
		
		if(is_array($configdata) == TRUE && array_key_exists('thumbs', $configdata) == TRUE)
		{
			$thumbnails = $configdata['thumbs'];
			
			if($thumbindex > count($thumbnails))
			{
				return $thumbnails; // empty array
			}
			
			// add some additional data to thumbnail array
			for($i=0; $i<count($thumbnails); $i++)
			{
				$thumbnails[$i]['html_path'] = $this->getHtmlPath('', '', $this->_getThumbnailFilename($i));
				$thumbnails[$i]['path'] = $this->getPath('', '', $this->_getThumbnailFilename($i));
				$thumbnails[$i]['img_attr'] = ' src="'.$thumbnails[$i]['html_path'].'"  width="'.$thumbnails[$i]['width'].'" height="'.$thumbnails[$i]['height'].'" ';
		
				if($thumbindex > -1 && $i == $thumbindex)
				{
					return $thumbnails[$i];
				}
			}
		}
		
		return $thumbnails;
	}
	
	/**
	 * Checks if the current item has thumbnails.
	 * Uses the static thumbnail settings and check the file extension
	 * @return boolean Returns TRUE if thumbnails possible or FALSE if not.
	 */
	public function isThumbnailGenerationPossible()
	{
		// file not loaded
		if($this->getId() <= 0)
		{
			return FALSE;
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$extension = $fsm->getPathinfo($this->getField('filename'), 'extension');
		
		// wrong file extension
		if(in_array($extension, self::$thumbnail_settings['generate_thumb']) == FALSE)
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Checks if thumbnail generation is possible and start the generation.
	 * @param boolean $delete_thumbnails Try to delete thumbnails first 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function generateThumbnails($delete_thumbnails = TRUE)
	{
		if($this->isThumbnailGenerationPossible() == TRUE)
		{
			// try to delete thumbnails first 
			if($delete_thumbnails == TRUE)
			{
				$this->_deleteThumbnails(); 
			}
			
			if($this->_generateThumbnails() == FALSE)
			{
				return FALSE;
			}
		}
		
		return TRUE;
	} 
	
	/**
	 * Generates all thumbnails for the given file item.
	 * Function retrieves settings for the thumbnails from
	 * static thumbnail settings.
	 * 
	 * The first size and proportion is set to files
	 * pictthumbwidth and pictthumbheight field.
	 * For this and each further thumbnail are stored in
	 * the configdata field and gets
	 * an index in the thumbnail filename.
	 * 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _generateThumbnails()
	{		
		$source = $this->getPath();
		$thumb_filenames = $this->_getThumbnailFilename();
		
		$sizes = self::$thumbnail_settings['sizes'];
		$aspect_ratio = self::$thumbnail_settings['aspect_ratio'];
		
		$im = sf_api('LIB', 'ImageManipulation');
		$configdata = $this->getField('configdata');
		$success = array();
		
		foreach($sizes as $index => $size)
		{
			$dest = $this->getPath('', '', $thumb_filenames[$index]);
			
			switch ($aspect_ratio[$index])
			{
				case '0':
					$success[$index] = $im->resize($source, $size, $size, $dest);	
					break;
				case '2':
					$success[$index] = $im->scaleByWidth($source, $size, $dest);
					break;
				case '3':
					$success[$index] = $im->scaleByHeight($source, $size, $dest);
					break;
				case '4': // quadratic thumbnail
					$success[$index] = $im->zoomCropSquare($source, $size, $dest);
					break;
				case '1':
				default:
					$success[$index] = $im->scaleByLength($source, $size, $dest);
					break;
			}
			
			if($success[$index] == TRUE)
			{
				// refresh thumbnail config data
				if($index == 0)
				{
					unset($configdata['thumbs']);
				}
				
				$configdata['thumbs'][$index] = array(
					'width' => $im->getWidth($dest),
					'height' => $im->getHeight($dest)
				);
			
				// set extra thumbnail size for first index
				if($index == 0)
				{
					$this->setField('pictthumbwidth', $configdata['thumbs'][$index]['width']);
					$this->setField('pictthumbheight', $configdata['thumbs'][$index]['height']);
				}
			}
		}
		
		// any generation succeed -> save and return TRUE
		if(in_array(TRUE, $success))
		{
			// set configdata to file item
			$this->setField('configdata', $configdata);
			
			$this->save();
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Copies the thumbnails for the current file from the source to
	 * destination. The destination is retrieved by the iddirectory.
	 * 
	 * @param integer $dest_iddirectory
	 * @param string $dest_filename
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _copyThumbnails($dest_iddirectory, $dest_filename)
	{
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		$thumb_filenames = $this->_getThumbnailFilename();
		$thumb_dest_filenames = $this->_getThumbnailFilename(-1, $dest_filename);
		$configdata = $this->getField('configdata');
		$success = array();
		
		foreach($configdata['thumbs'] as $index => $thumb)
		{
			$source = $this->getPath('', '', $thumb_filenames[$index]);
			$dest = $this->getPath('', $this->_getDirnameByIddirectory($dest_iddirectory), $thumb_dest_filenames[$index]);
			
			$success[$index] = $fsm->copyFile($source, $dest);
		}
		
		if(in_array(FALSE, $success))
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Moves the thumbnails for the current file from the source to
	 * destination. The destination is retrieved by the iddirectory.
	 * 
	 * @param integer $dest_iddirectory
	 * @param string $dest_filename
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _moveThumbnails($dest_iddirectory, $dest_filename)
	{
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		$thumb_filenames = $this->_getThumbnailFilename();
		$thumb_dest_filenames = $this->_getThumbnailFilename(-1, $dest_filename);
		$configdata = $this->getField('configdata');
		$success = array();
		
		foreach($configdata['thumbs'] as $index => $thumb)
		{
			$source = $this->getPath('', '', $thumb_filenames[$index]);
			$dest = $this->getPath('', $this->_getDirnameByIddirectory($dest_iddirectory), $thumb_dest_filenames[$index]);
			
			$success[$index] = $fsm->moveFile($source, $dest);
		}
		
		if(in_array(FALSE, $success))
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Deletes all thumbnails for the current file from filesystem
	 * and configdata field.
	 * 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _deleteThumbnails()
	{
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		$thumb_filenames = $this->_getThumbnailFilename();
		$configdata = $this->getField('configdata');
		$success = array();
		
		if( is_array($configdata) &&
			array_key_exists('thumbs', $configdata) == TRUE &&
			is_array($configdata['thumbs']) == TRUE)
		{
			foreach($configdata['thumbs'] as $index => $thumb)
			{
				$dest = $this->getPath('', '', $thumb_filenames[$index]);
				$success[$index] = $fsm->deleteFile($dest);
				
				if($success[$index] == TRUE)
				{
					unset($configdata['thumbs'][$index]);
				}
			}
		}
		
		$this->setField('configdata', $configdata);
		
		if(in_array(FALSE, $success))
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Returns a specific filename for the given $thumbindex.
	 * If no $thumbindex given returns an array with all thumbnail
	 * filenames.
	 * 
	 * @param integer $thumbindex optional
	 * @param string $filename Alternative filename (optinal)
	 * @return array|string Returns an array with all thumbnail filenames or
	 * one filename for the given index
	 */
	protected function _getThumbnailFilename($thumbindex = -1, $filename = '')
	{
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$filename = ($filename == '') ? $this->getField('filename') : $filename;
		$extension = $fsm->getPathinfo($filename, 'extension');
		
		$sizes = self::$thumbnail_settings['sizes'];
		$thumb_extension = self::$thumbnail_settings['thumb_extension'];
		$filenames = array();
		
		for($i=0; $i<count($sizes); $i++)
		{
			// first thumb extension = _cms_thumb; other extensions = _cms_thumb_<index>
			$thumb_addition = ($i == 0) ? $thumb_extension : $thumb_extension.'_'.$i;
			$filenames[$i] = str_replace('.'.$extension, $thumb_addition.'.'.$extension, $filename);
			
			if($thumbindex > -1 && $i == $thumbindex)
			{
				return $filenames[$i];
			}
		}
		
		return $filenames;
	}
	
	
	/* ******************
	 *  ITEM EDITOR HANDLING
	 * *****************/
	
	/**
	 * Register a new item editor by name to list.
	 * Checks if editor is type safe.
	 * 
	 * @param string $name
	 * @param SF_INTERFACE_ItemEditor $editor
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function addItemEditor($name, $editor)
	{
		if($name != '' && $editor instanceof SF_INTERFACE_ItemEditor)
		{
			$this->item_editors[$name] = $editor;
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Removes a new item editor by name from list.
	 * Checks if editor is type safe.
	 * 
	 * @param string $name
	 * @param SF_INTERFACE_ItemEditor $editor
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function removeItemEditor($name, $editor)
	{
		if(array_key_exists($name, $this->item_editors) == TRUE && $editor instanceof SF_INTERFACE_ItemEditor)
		{
			unset($this->item_editors[$name]);
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Calls function onSaveItemBegin() on
	 * every registered item editor.
	 * If one editor returns false, other editors
	 * won't called.
	 * 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function callOnSaveItemBegin()
	{
		foreach($this->item_editors as $name => $editor)
		{
			if($editor->onSaveItemBegin() == FALSE)
			{
				// break on first error -> other editors won't called
				return FALSE;
			}
		}
		return TRUE;
	}
	
	/**
	 * Calls function onSaveItemCommit() on
	 * every registered item editor.
	 * If one editor returns false, other editors
	 * won't called.
	 * 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function callOnSaveItemCommit()
	{
		foreach($this->item_editors as $name => $editor)
		{
			if($editor->onSaveItemCommit() == FALSE)
			{
				// break on first error -> other editors won't called
				return FALSE;
			}
		}
		return TRUE;
	}
	
	/**
	 * Calls function onSaveItemRollback() on
	 * every registered item editor.
	 * If one editor returns false, other editors
	 * won't called.
	 * 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function callOnSaveItemRollback()
	{
		foreach($this->item_editors as $name => $editor)
		{
			if($editor->onSaveItemRollback() == FALSE)
			{
				// break on first error -> other editors won't called
				return FALSE;
			}
		}
		return TRUE;
	}
	
	
	/* ******************
	 *  MULTI LANGUAGE SUPPORT 
	 * *****************/
	
	/**
	 * Enables the multi language support (MLS) for the item.
	 * First check if a conversion of the language table is necessary.
	 * If so, duplicates the metadata with idlang = 0 the of given
	 * $target_langs. After all deletes the idlang = 0 entries for
	 * the given $idclient.
	 * 
	 * @param integer $idclient
	 * @param array $target_langs One dimensional array
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function enableMultiLanguageSupport( $idclient, $target_langs )
	{
		if(! is_array($target_langs) )
		{
			return FALSE;
		}
		
		// count before if switch is really necessary
		$sql = "SELECT COUNT(".$this->mapFieldToRow('id', $this->table_upl_lang).") AS countme 
				FROM ".$this->table_upl_lang."
				WHERE
					idclient = '".$idclient."'
					AND idlang = '0';";
		
		$rs = $this->db->Execute($sql);
		
		if ($rs === false || $rs->EOF) {
			return false;
		}
		$rs->Close();
		
		// abort, if nothing to do
		if($rs->fields['countme'] <= 0)
		{
			return FALSE;
		}
		
		$sql = "SELECT *
				FROM ".$this->table_upl_lang."
				WHERE
					idclient = '".$idclient."'
					AND idlang = '0';";
		
		$rs = $this->db->Execute($sql);
	
		if ($rs === FALSE || $rs->EOF )
		{
			return FALSE;
		}
		
		while (! $rs->EOF) 
		{
			// remove numeric fields and idupllang
			foreach($rs->fields as $key => $val)
			{
				if(is_numeric($key) || $key == $this->mapFieldToRow('id', $this->table_upl_lang))
				{
					unset($rs->fields[$key]);
				}
			}
			
			// insert for every target lang with new idlang
			foreach($target_langs as $lang)
			{
				if($lang <= 0)
				{
					continue;
				}
				
				$rs->fields['idlang'] = $lang;
				
				$this->db->AutoExecute($this->table_upl_lang, $rs->fields, 'INSERT');
			}
			
			$rs->MoveNext();
		}
		$rs->Close();
		
		// delete all rows with idlang = 0 
		$sql = "DELETE FROM ".$this->table_upl_lang."
				WHERE
					idclient = '".$idclient."'
					AND idlang = '0';";
		
		$rs = $this->db->Execute($sql);

		if ($rs === FALSE)
		{
			return FALSE;
		}
		
		$rs->Close();
		
		return TRUE;
	}
	
	/**
	 * Disables the multi language support (MLS) for the item.
	 * First check if a conversion of the language table is necessary.
	 * If so, updates the metadata with idlang = $start_idlang to 
	 * idlang = 0. After all deletes all rows with idlang > 0 for
	 * the given $idclient.
	 * 
	 * @param integer $idclient
	 * @param integer $start_idlang Idlang that is kept and used for all languages
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function disableMultiLanguageSupport( $idclient, $start_idlang )
	{
		if(! is_numeric($start_idlang) )
		{
			return FALSE;
		}
		
		// count before if switch is really necessary
		$sql = "SELECT COUNT(".$this->mapFieldToRow('id', $this->table_upl_lang).") AS countme 
				FROM ".$this->table_upl_lang."
				WHERE
					idclient = '".$idclient."'
					AND idlang > '0';";
		
		$rs = $this->db->Execute($sql);
		
		if ($rs === false || $rs->EOF) {
			return false;
		}
		$rs->Close();
		
		// abort, if nothing to do
		if($rs->fields['countme'] <= 0)
		{
			return FALSE;
		}
		
		$sql = "SELECT *
				FROM ".$this->table_upl_lang."
				WHERE
					idclient = '".$idclient."'
					AND idlang = '".$start_idlang."';";
		
		$rs = $this->db->Execute($sql);

		if ($rs === FALSE || $rs->EOF )
		{
			return FALSE;
		}
		
		while (! $rs->EOF) 
		{
			// remove numeric fields and idupllang
			foreach($rs->fields as $key => $val)
			{
				if(is_numeric($key))
				{
					unset($rs->fields[$key]);
				}
			}
			
			$rs->fields['idlang'] = '0';
			$this->db->AutoExecute($this->table_upl_lang, $rs->fields, 'UPDATE', $this->mapFieldToRow('id', $this->table_upl_lang)." = '".$rs->fields[ $this->mapFieldToRow('id', $this->table_upl_lang) ]."'");
			
			$rs->MoveNext();
		}
		$rs->Close();
		
		// delete all rows with idlang = 0 
		$sql = "DELETE FROM ".$this->table_upl_lang."
				WHERE
					idclient = '".$idclient."'
					AND idlang > '0';";
		
		$rs = $this->db->Execute($sql);

		if ($rs === FALSE)
		{
			return FALSE;
		}
		
		$rs->Close();
		
		return TRUE;
	}
	
	/**
	 * Copies the language metadata (table cms_upl_lang) from a
	 * foreign idlang to the current idlang. Therfore the item must
	 * be loaded previous.
	 * 
	 * @param integer $foreign_idlang
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function copyLanguageMetadata( $foreign_idlang )
	{
		if(! is_numeric($foreign_idlang) )
		{
			return FALSE;
		}
		
		if($this->getId() <= 0)
		{
			return FALSE;
		};
		
		$to_idlang = $this->getField('idlang');
		
		$sql = "SELECT *
				FROM ".$this->table_upl_lang."
				WHERE
					".$this->mapFieldToRow('id', $this->table_upl)." = '".$this->getId()."'
					AND idlang = '".$foreign_idlang."'
				LIMIT 0, 1;"; // use idupl and idclient
		//echo $sql."<br />";
		
		$rs = $this->db->Execute($sql);
	
		if ($rs === FALSE || $rs->EOF )
		{
			return FALSE;
		}
		
		// delete all rows with idlang = 0 
		$sql = "DELETE FROM ".$this->table_upl_lang."
				WHERE
					".$this->mapFieldToRow('id', $this->table_upl)." = '".$this->getId()."'
					AND idlang = '".$to_idlang."';"; // use idupl and idclient
		//echo $sql."<br />";
		
		$rs2 = $this->db->Execute($sql);

		if ($rs2 === FALSE)
		{
			return FALSE;
		}
		
		$rs2->Close();
		
		while (! $rs->EOF) 
		{
			// remove numeric fields and idupllang
			foreach($rs->fields as $key => $val)
			{
				if(is_numeric($key) || $key == $this->mapFieldToRow('id', $this->table_upl_lang))
				{
					unset($rs->fields[$key]);
				}
			}
			
			$rs->fields['idlang'] = $to_idlang;
			$this->db->AutoExecute($this->table_upl_lang, $rs->fields, 'INSERT');
			
			$rs->MoveNext();
		}
		$rs->Close();
		
		
		return TRUE;
	}
	
	
	/* ******************
	 *  HELPER FUNCTIONS
	 * *****************/
	
	/**
	 * Modify the given $filename accordingly to the
	 * client setting 'allow_invalid_filenames':
	 * 	0 = returns FALSE
	 * 	1 = does nothing and returns the given $filename
	 * 	2 = sanitize the $filename and returns it 
	 * @param string $filename
	 * @return string|boolean Returns the sanitized $filename or FALSE.
	 */
	protected function _sanitizeFilename($filename)
	{
		// check if invalid filenames are allowed
		switch(self::$client_settings['allow_invalid_filenames'])
		{
			// no: return FALSE
			case 0:
				$validator = sf_api('LIB', 'Validation');
				if($validator->filename($filename) == FALSE)
				{
					$filename = FALSE;
				}
				unset($validator);
				break;
			// yes: use current filename
			case 1:
				//$filename = $filename;
				break;
			// correct filename: use the corrected filename
			case 2:
				$fsm = sf_api('LIB', 'FilesystemManipulation');
				$filename = $fsm->cleanFilename($filename);
				unset($fsm);
				break;
		}
		return $filename;
	} 
}
?>