<?php

$this->includeClass('INTERFACE', 'Filesystem');
$this->includeClass('MODEL', 'AbstractSqlItem');

class SF_MODEL_DirectorySqlItem extends SF_MODEL_AbstractSqlItem
								 implements SF_INTERFACE_Filesystem
{
	/**
	 * Stores all selected dirnames in an
	 * static array. So every item can use it.
	 * @var array
	 */
	protected static $dirname_cache = array();
	
	/**
	 * Stores all client settings in an static array.
	 * So every item can use it.
	 * The default values will be overwritten by
	 * client configuration in constructor function.
	 * @var array
	 */
	protected static $client_settings = array(
		'is_default' => TRUE, // flag is deleted in constructor
		'allow_invalid_dirnames' => 1,
	);
		
	/**
	 * Stores the table name
	 * @var string
	 */
	protected $table_directory = '';
	protected $table_directory_lang = '';
	
	/**
	 * Stores the root directories by area
	 * @var array
	 */
	protected $rootdirectory = array();
	
	/**
	 * Stores dirname of the parent directory
	 * @var string
	 */
	private $parentdirectory = '';
	
	/**
	 * Stores the default directory seperator
	 * @var string
	 */
	private $directory_seperator = '/';
	
	/**
	 * Stores the different perms for the object.
	 * The default type can be overwritten.
	 * @var array
	 */
	protected $objperm = array(
		'default' => array(
			'type' => 'directory'
		),
		'view' => array(
			'permid' => 1
		),
		'create' => array(
			'permid' => 2
		),
		'edit' => array(
			'permid' => 3
		),
		'delete' => array(
			'permid' => 5
		),
		'setperms' => array(
			'permid' => 6
		),
		'import' => array(
			'permid' => 7
		),
		'export' => array(
			'permid' => 8
		),
		'upload' => array(
			'permid' => 9
		),
		'download' => array(
			'permid' => 10
		),
		'scan' => array(
			'permid' => 11
		),
		'copy' => array(
			'permid' => 12
		)
	);
	
	/**
	 * Constructor
	 * Set up the model with relevant tables and releated fields.
	 * @return void
	 */
	public function __construct()
	{
		// call AbstractSqlItem constructor
		parent::__construct();
		
		// set root directories
		$this->_setRootdirectoryForArea();
		
		// set table names
		$this->table_directory = $this->cfg->db('directory');
		$this->table_directory_lang = $this->cfg->db('directory_lang');
		
		// table directory
		$this->_addTable($this->table_directory);
		$this->_addTableFields(
			$this->table_directory,
			array(
				'name' => '',
				'dirname' => '',
				'parentid' => '',
				'status' => '',
				'area' => ''
			)
		);
		$this->_addDefaultFields($this->table_directory);
		$this->_addRowMapping(
			$this->table_directory,
			array(
				'id' => 'iddirectory'
			)
		);
		$this->_addDisabledFields(
			$this->table_directory,
			array(
				'idlang' => TRUE,
				'ip' => TRUE,
				'groupkey' => TRUE
			)
		);
		
		// table directory_lang
		$this->_addTable($this->table_directory_lang);
		$this->_addTableFields(
			$this->table_directory_lang,
			array(
				'iddirectory' => '',
				'description' => ''
			)
		);
		$this->_addForeignkeys(
			$this->table_directory_lang,
			array(
				'iddirectory' => array(
					'table' => $this->table_directory,
					'foreignkey' => 'id'
				)
			)
		);
		$this->_addDefaultFields($this->table_directory_lang);
		$this->_addRowMapping(
			$this->table_directory_lang,
			array(
				'id' => 'iddirectorylang'
			)
		);
		$this->_addDisabledFields(
			$this->table_directory_lang,
			array(
				'ip' => TRUE,
				'groupkey' => TRUE
			)
		);

		// set client settings if not yet set
		if(self::$client_settings['is_default'] == TRUE)
		{
			self::$client_settings['allow_invalid_dirnames'] = (int) $this->cfg->client('allow_invalid_dirnames', self::$client_settings['allow_invalid_dirnames']);
			unset(self::$client_settings['is_default']);
		}
	}

	/**
	 * Returns the absolute path to the directory.
	 * The value is available, after the directory is loaded.
	 * Use the parameter $area to get another root directory.
	 * @param string $area
	 * @param string $parentdirectory 
	 * @param string $directory
	 * @return string Returns the path. If no path available returns an empty string.
	 */
	public function getPath($area = '', $parentdirectory = FALSE, $directory = '')
	{
		$area = ($area === '') ? $this->getField('area') : $area;
		
		return $this->rootdirectory[$area]['path'].$this->getRelativePath($parentdirectory, $directory);
	}

	/**
	 * Returns the relative path to the directory.
	 * The value is available, after the directory is loaded.
	 * Use the parameter $area to get another root directory.
	 * @param string $parentdirectory 
	 * @param string $directory
	 * @return string Returns the path. If no path available returns an empty string.
	 */
	public function getRelativePath($parentdirectory = FALSE, $directory = '')
	{
		$parentdirectory = ($parentdirectory === FALSE) ? $this->parentdirectory : $parentdirectory;
		$directory = ($directory === '') ? $this->getField('name') : $directory;
		
		// remove directory seperator on empty $parentdirectory and $directory
		if($parentdirectory === '' && $directory === FALSE)
		{
			return '';
		}
		
		return $parentdirectory.$directory.$this->directory_seperator;
	}

	/**
	 * Returns the html path to the directory.
	 * The value is available, after the directory is loaded.
	 * Use the parameter $area to get another root directory.
	 * @param string $area 
	 * @param string $parentdirectory
	 * @param string $directory 
	 * @return string Returns the path. If no path available returns an empty string.
	 */
	public function getHtmlPath($area = '', $parentdirectory = FALSE, $directory = '')
	{
		$area = ($area === '') ? $this->getField('area') : $area;
		
		return $this->rootdirectory[$area]['htmlpath'].$this->getRelativePath($parentdirectory, $directory);
	}
	
	/**
	 * Creates a directory by the given fields
	 * @see API/INTERFACES/SF_INTERFACE_Filesystem#create($fields)
	 * @param array $fields 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function create($fields)
	{
		if(array_key_exists('name', $fields) == FALSE)
		{
			throw sf_exception('error', 'missing_name');
			return FALSE;
		};
		
		if(array_key_exists('parentid', $fields))
		{
			$this->parentdirectory = $this->_getDirnameByIddirectory($fields['parentid']);
		}
		else
		{
			$this->parentdirectory = '';
		}
		
		// check perm
		if($this->hasPerm('create', $this->parentdirectory) == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		// check if invalid directorynames are allowed
		$sanitize_name = $this->_sanitizeDirectoryname($fields['name']);
		if($sanitize_name === FALSE)
		{
			throw sf_exception('error', 'validation_error_directoryname');
			return FALSE;
		}
		$fields['name'] = $sanitize_name;
		unset($sanitize_name);
		
		$path = $this->getPath($fields['area'], FALSE, $fields['name']);

		if($fsm->isDir($path))
		{
			throw sf_exception('error', 'directory_exists_in_destination', array('path' => $path));
			return FALSE;
		}
		
		if($fsm->createDirectory($path) == FALSE)
		{
			throw sf_exception('error', 'create_directory_failed', array('path' => $path));
			return FALSE;
		}
		
		if(array_key_exists('dirname', $fields) == FALSE || $fields['dirname'] == '')
		{
			$fields['dirname'] = $this->parentdirectory.$fields['name'].$this->directory_seperator;
		}
		unset($fields['id']);
		
		foreach($fields as $field => $value)
		{
			$this->setField($field, $value);
		}
		// set idclient for directory_lang seperatly
		if(array_key_exists('idclient', $fields))
		{
			$this->setField('idclient', $fields['idclient'], $this->table_directory_lang);
		}
		
		if($this->save() == FALSE)
		{
			// try to remove directory
			$fsm->deleteDirectory($path);
			
			throw sf_exception('error', 'save_directory_to_db_failed', array('dirname' => $fields['dirname']));
			return FALSE;
		}
		
		// copy userrights from parent folder if parent is not root
		if($this->permsExists(TRUE) == FALSE)
		{
			$this->copyPerms(
				$fields['parentid'],
				0, // group
				$fields['idlang'], // lang
				TRUE // ignore lang
			);  
		}
		
		return TRUE;
	}
	
	/**
	 * Creates a directory by the given fields only in database
	 * @param array $fields 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function createOnlyDb($fields)
	{
		if(array_key_exists('name', $fields) == FALSE)
		{
			throw sf_exception('error', 'missing_name');
			return FALSE;
		};
		
		if(array_key_exists('parentid', $fields))
		{
			$this->parentdirectory = $this->_getDirnameByIddirectory($fields['parentid']);
		}
		else
		{
			$this->parentdirectory = '';
		}
		
		// check perm
		if($this->hasPerm('create', $this->parentdirectory) == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		// check if invalid directorynames are allowed
		$sanitize_name = $this->_sanitizeDirectoryname($fields['name']);
		if($sanitize_name === FALSE)
		{
			throw sf_exception('error', 'validation_error_directoryname', array('name' => $fields['name']));
			return FALSE;
		}
		// special case for auto correction:
		// try to correct the directoryname -> rename the physical file
		else if(self::$client_settings['allow_invalid_dirnames'] == 2)
		{
			$oldname = $this->getPath($fields['area'], '', $fields['name']);
			$newname = $this->getPath($fields['area'], '', $sanitize_name);
			
			if($fsm->renameDirectory($oldname, $newname) == FALSE)
			{
				throw sf_exception('error', 'invalid_name_correction_failed', array('newname' => $newname, 'oldname' => $oldname));
				return FALSE;
			}
		}
		$fields['name'] = $sanitize_name;
		unset($sanitize_name);
		
		// -- always create a new dirname, in case of sanitizing the name
		//if(array_key_exists('dirname', $fields) == FALSE || $fields['dirname'] == '')
		//{
			$fields['dirname'] = $this->parentdirectory.$fields['name'].$this->directory_seperator;
		//}
		unset($fields['id']);
		
		foreach($fields as $field => $value)
		{
			$this->setField($field, $value);
		}
		// set idclient for directory_lang seperatly
		if(array_key_exists('idclient', $fields))
		{
			$this->setField('idclient', $fields['idclient'], $this->table_directory_lang);
		}
		
		if($this->save() == FALSE)
		{
			throw sf_exception('error', 'save_directory_to_db_failed', array('dirname' => $fields['dirname']));
			return FALSE;
		}
		
		// copy userrights from parent folder if parent is not root
		if($this->permsExists(TRUE) == FALSE)
		{
			$this->copyPerms(
				$fields['parentid'],
				0, // group
				$fields['idlang'], // lang
				TRUE // ignore lang
			);  
		}
		
		return TRUE;
	}
	
	/**
	 * Edits a directory by given $fields.
	 * If the directory name changed it is renamed in filesystem.
	 * The dirname is also updated in sub-directories of the tree.
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
			throw sf_exception('error', 'directory_is_not_loaded');
			return FALSE;
		};
		
		// check perm
		if($this->hasPerm('edit') == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$changename = $changenamewarning = FALSE;
		
		if(array_key_exists('name', $fields) == TRUE)
		{
			// check if invalid directorynames are allowed
			$sanitize_name = $this->_sanitizeDirectoryname($fields['name']);
			if($sanitize_name === FALSE)
			{
				throw sf_exception('error', 'validation_error_directoryname');
				return FALSE;
			}
			$fields['name'] = $sanitize_name;
			unset($sanitize_name);
			
			// rename file if different and get new fileextension
			if($this->getField('name') != $fields['name'])
			{
				$oldname = $this->getPath();
				$newname = $this->getPath('', FALSE, $fields['name']);
				$oldnamerelative = $this->getRelativePath();
				$newnamerelative = $this->getRelativePath(FALSE, $fields['name']);
				
				if($fsm->isDir($newname) == FALSE)
				{
					$changename = $fsm->renameDirectory($oldname, $newname);
					if($changename == FALSE)
					{
						$changenamewarning = 'rename_directory_failed';
					}
				}
				else
				{
					$changenamewarning = 'directory_exists_in_destination';
				}
			}
		}
		// always unset. if name has changed it will updated
		// in _updateDirnameForSubdirectories()
		// or if not changed, no change/save to DB required
		unset($fields['dirname']);
		
		foreach($fields as $field => $value)
		{
			$this->setField($field, $value);
		}
		// set idclient for directory_lang seperatly
		if(array_key_exists('idclient', $fields))
		{
			$this->setField($fields['idclient'], $value, $this->table_directory_lang);
		}
		
		if($this->save() == FALSE)
		{
			// if save fails revert the changes
			if($changename == TRUE)
			{
				$fsm->renameDirectory($newname, $oldname);
			}
			
			throw sf_exception('error', 'save_directory_to_db_failed', array('dirname' => $fields['dirname']));
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
				0xFFFFFFFF // bitmask
			);
		}
		
		if($changename == TRUE)
		{
			// update dirname for sub-directories in the tree
			if($this->_updateDirnameForSubdirectories($oldnamerelative, $newnamerelative) == FALSE)
			{
				throw sf_exception('error', 'save_sub_directories_to_db_failed', array('oldname' => $oldnamerelative, 'newname' => $newnamerelative));
				return FALSE;
			}
		}
		
		// send warning that the file exists
		if($changenamewarning != FALSE)
		{
			throw sf_exception('warning', $changenamewarning);
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Copies a directory with all sub-directories and files
	 * to new destination directory.
	 * The idparent in $fiels must be different from the current
	 * value idparent, but may not a iddirectory of a sub-directory
	 * (in case of recursion).
	 * @see API/INTERFACES/SF_INTERFACE_Filesystem#copy($fields)
	 * @param array $fields 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function copy($fields)
	{
		if($this->getId() <= 0)
		{
			throw sf_exception('error', 'directory_is_not_loaded');
			return FALSE;
		};
		
		if(!array_key_exists('parentid', $fields))
		{
			throw sf_exception('error', 'missing_destination_parent_directory');
			return FALSE;
		}
		
		// check perm
		if($this->hasPerm('copy') == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		// check if new parent directory is a sub directory
		$tree = sf_api('MODEL', 'DirectorySqlTree');
		$tree->setIdclient($this->getField('idclient'));
		//$tree->setIdlang($this->getField('idlang'));
		$tree->setArea($this->getField('area'));
		$tree->generate();
		
		$children_recursive = $tree->getChildrenRecursive($this->getId());
		$children = $tree->getChildren($this->getId());
		
		if(in_array($fields['parentid'], $children_recursive) == TRUE)
		{
			throw sf_exception('error', 'destination_is_a_child_of_source');
			return FALSE;
		}
		unset($tree, $children_recursive);
		
		// take the current name if nothing is set
		if(!array_key_exists('name', $fields))
		{
			$fields['name'] = $this->getField('name');
		}
		
		// check if invalid directorynames are allowed
		$sanitize_name = $this->_sanitizeDirectoryname($fields['name']);
		if($sanitize_name === FALSE)
		{
			throw sf_exception('error', 'validation_error_directoryname');
			return FALSE;
		}
		$fields['name'] = $sanitize_name;
		unset($sanitize_name);
		
		$parentdirectory = $this->_getDirnameByIddirectory($fields['parentid']);
		$parentdirectory = ($parentdirectory === FALSE) ? '' : $parentdirectory;
		
		$source = $this->getPath();
		$dest = $this->getPath('', $parentdirectory, $fields['name']);
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		if($fields['parentid'] == $this->getField('parentid') || $fsm->isDir($dest))
		{
			throw sf_exception('error', 'directory_exists_in_destination', array('source' => $source, 'dest' => $dest));
			return FALSE;
		}
		
		if($fsm->copyDirectory($source, $dest) == FALSE)
		{
			throw sf_exception('error', 'copy_directory_failed', array('source' => $source, 'dest' => $dest));
			return FALSE;
		}
		
		// clone this object to hold the data 
		$newitem = clone $this;
		foreach($fields as $field => $value)
		{
			$newitem->setField($field, $value);
		}
		// reset ids to save as new item
		$newitem->setField('id', 0, $this->table_directory);
		$newitem->setField('id', 0, $this->table_directory_lang);
		$newitem->setField('iddirectory', 0, $this->table_directory_lang);
		// update dirname manually
		$newitem->setField('dirname', $this->getRelativePath($parentdirectory, $fields['name']));
		
		// save table_directory first ...
		if($newitem->save($this->table_directory) == FALSE)
		{
			// try to delete file from destination
			$fsm->deleteDirectory($dest);
			
			throw sf_exception('error', 'save_directory_to_db_failed', array('dirname' => $fields['dirname']));
			return FALSE;
		}
		
		// ... and copy all available language afterwards
		$tablefields = $this->getMappedFieldsForTable($this->table_directory_lang);
		$insert_fields = implode(",", $tablefields);
		
		// reset iddirectorylang
		$index = array_search($this->mapFieldToRow('id', $this->table_directory_lang), $tablefields);
		if($index !== FALSE) $tablefields[$index] = '0';
		
		// set iddirectory to new item id
		$index = array_search($this->mapFieldToRow('id', $this->table_directory), $tablefields);
		if($index !== FALSE) $tablefields[$index] = $newitem->getId();
		
		$select_fields = implode(",", $tablefields);
		
		$sql = "INSERT INTO
					".$this->table_directory_lang."
					(".$insert_fields.")
					SELECT ".$select_fields."
						FROM ".$this->table_directory_lang."
						WHERE ".$this->mapFieldToRow('id', $this->table_directory)." = '".$this->getId()."';";
		
		//echo $sql."<br />";
		
		$rs = $this->db->Execute($sql);

		if($rs === FALSE)
		{
			// try to delete file from destination
			$fsm->deleteDirectory($dest);
			
			throw sf_exception('error', 'save_directory_to_db_failed', array('dirname' => $fields['dirname']));
			return FALSE;
		}
		
		$rs->Close();
		
		
		// copy rights
		if($this->copyPerms($newitem->getId(), 0, $fields['idlang'], TRUE) == FALSE)
		{
			throw sf_exception('warning', 'copy_rights_failed', array('source' => $source, 'dest' => $dest));
			return FALSE;
		}
		
		// after creating the root directory copy the sub directories and files 
		try
		{
			// copy files of directory
			$filecol = sf_api('MODEL', 'FileSqlCollection');
			$filecol->setIdclient($this->getField('idclient'));
			$filecol->setIdlang($this->getField('idlang'));
			$filecol->setFreefilter('area',  $this->getField('area'));
			$filecol->setFreefilter('iddirectory', $this->getId());
			$filecol->generate();
			
			if($filecol->getCount() > 0)
			{
				$iter = $filecol->getItemsAsIterator();
				while($iter->valid()) {
					$file = $iter->current();
					$filedata = array(
						'iddirectory' => $newitem->getId()
					);
					//echo "copy file ".$file->getField('filename')." from ".$file->getField('iddirectory')." to ".$newitem->getId()."<br />";
					$file->copy($filedata);
				    
				    $iter->next();
				}
			}
			unset($filecol);
			
			// copy sub directories
			foreach($children as $childdir)
			{
				$dir = new self();
				$dir->loadById($childdir);
				$dirdata = array(
					'parentid' => $newitem->getId()
				);
				//echo "copy dir ".$dir->getField('filename')." from ".$dir->getId()." to ".$newitem->getId()."<br />";
				$dir->copy($dirdata);
			}
		}
		catch(Exception $e)
		{
			// TODO collect the recursive exeptions and create a new one in the top item
			echo $e->getMessage()."<br />";
		}
		
		return TRUE;
	}
	
	/**
	 * Move directory with all sub-directories and files
	 * to new destination directory.
	 * The idparent in $fiels must be different from the current
	 * value idparent, but may not a iddirectory of a sub-directory
	 * (in case of loosing data).
	 * @see API/INTERFACES/SF_INTERFACE_Filesystem#move($fields)
	 * @param array $fields 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function move($fields)
	{
		if($this->getId() <= 0)
		{
			throw sf_exception('error', 'directory_is_not_loaded');
			return FALSE;
		};
		
		if(!array_key_exists('parentid', $fields))
		{
			throw sf_exception('error', 'missing_destination_parent_directory');
			return FALSE;
		}
		
		// check perm
		if($this->hasPerm('copy') == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		// check if new parent directory is a sub directory
		$tree = sf_api('MODEL', 'DirectorySqlTree');
		$tree->setIdclient($this->getField('idclient'));
		//$tree->setIdlang($this->getField('idlang'));
		$tree->setArea($this->getField('area'));
		$tree->generate();
		$childs = $tree->getChildrenRecursive($this->getId());
		if(in_array($fields['parentid'], $childs) == TRUE)
		{
			throw sf_exception('error', 'destination_is_a_child_of_source');
			return FALSE;
		}
		unset($tree);
		
		// take the current name if nothing is set
		if(!array_key_exists('name', $fields))
		{
			$fields['name'] = $this->getField('name');
		}
		
		// check if invalid directorynames are allowed
		$sanitize_name = $this->_sanitizeDirectoryname($fields['name']);
		if($sanitize_name === FALSE)
		{
			throw sf_exception('error', 'validation_error_directoryname');
			return FALSE;
		}
		$fields['name'] = $sanitize_name;
		unset($sanitize_name);
		
		$parentdirectory = $this->_getDirnameByIddirectory($fields['parentid']);
		$parentdirectory = ($parentdirectory === FALSE) ? '' : $parentdirectory;
		
		$source = $this->getPath();
		$dest = $this->getPath('', $parentdirectory, $fields['name']);
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		if($fields['parentid'] == $this->getField('parentid') || $fsm->isDir($dest))
		{
			throw sf_exception('error', 'directory_exists_in_destination', array('source' => $source, 'dest' => $dest));
			return FALSE;
		}
		
		if($fsm->moveDirectory($source, $dest) == FALSE)
		{
			throw sf_exception('error', 'move_directory_failed', array('source' => $source, 'dest' => $dest));
			return FALSE;
		}
		
		unset($fields['dirname']);
		foreach($fields as $field => $value)
		{
			$this->setField($field, $value);
		}
		
		if($this->save() == FALSE)
		{
			throw sf_exception('error', 'save_directory_to_db_failed', array('dirname' => $fields['dirname']));
			return FALSE;
		}
		
		// move rights -> nothing to do
		
		// update dirname for sub-directories in the tree
		if($this->_updateDirnameForSubdirectories($this->getRelativePath(), $this->getRelativePath($parentdirectory, $fields['name'])) == FALSE)
		{
			throw sf_exception('error', 'save_sub_directories_to_db_failed', array('dirname' => $fields['dirname']));
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Delete a directory
	 * @see API/MODELS/SF_MODEL_AbstractSqlItem#delete($tablename)
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function delete($tablename = '')
	{
		if($this->getId() <= 0)
		{
			throw sf_exception('error', 'directory_is_not_loaded');
			return FALSE;
		};
		
		// check perm
		if($this->hasPerm('delete') == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		//first delete sub files and directories
		try
		{
			// delete files of directory
			$filecol = sf_api('MODEL', 'FileSqlCollection');
			$filecol->setIdclient($this->getField('idclient'));
			$filecol->setIdlang($this->getField('idlang'));
			$filecol->setFreefilter('area', $this->getField('area'));
			$filecol->setFreefilter('iddirectory', $this->getId());
			$filecol->generate();
			
			if($filecol->getCount() > 0)
			{
				$iter = $filecol->getItemsAsIterator();
				while($iter->valid()) {
					$file = $iter->current();
					//echo "delete file ".$file->getField('filename')."<br />";
					$file->delete();
					
					$iter->next();
				}
			}
			unset($filecol);
			
			// delete subdirectories
			$dircol = sf_api('MODEL', 'DirectorySqlCollection');
			$dircol->setIdclient($this->getField('idclient'));
			$dircol->setIdlang($this->getField('idlang'));
			$dircol->setFreefilter('area', $this->getField('area'));
			$dircol->setFreefilter('parentid', $this->getId());
			$dircol->generate();
			
			if($dircol->getCount() > 0)
			{
				$iter = $dircol->getItemsAsIterator();
				while($iter->valid()) {
					$directory = $iter->current();
					//echo "delete directory ".$directory->getField('dirname')."<br />";
					$directory->delete();
					
					$iter->next();
				}
			}
			unset($dircol);
		}
		catch(Exception $e)
		{
			// TODO collect the recursive exeptions and create a new one in the top item
			//echo $e->getMessage()."<br />";
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		// delete directory
		// if is_dir() check fails no exception is thrown. It's usefull to delete only the sql item. 
		if($fsm->isDir( $this->getPath() ) == TRUE && $fsm->deleteDirectory( $this->getPath() ) == FALSE)
		{
			throw sf_exception('error', 'delete_directory_failed', array('dirname' => $fields['dirname']));
			return FALSE;
		}
		
		// delete sql item 
		if(parent::delete($tablename) == FALSE)
		{
			throw sf_exception('error', 'delete_directory_from_db_failed', array('dirname' => $fields['dirname']));
			return FALSE;
		}
		
		// delete rights
		if($this->deleteAllPerms() == FALSE)
		{
			throw sf_exception('warning', 'delete_rights_failed', array('dirname' => $this->getField('dirname')));
			return FALSE;
		}
		
		return TRUE;
	}

	/**
	 * Delete a directory in database, but not in filesystem
	 * @param string $tablename
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function deleteOnlyDb($tablename = '')
	{
		if($this->getId() <= 0)
		{
			throw sf_exception('error', 'directory_is_not_loaded');
			return FALSE;
		};
		
		// check perm
		if($this->hasPerm('delete') == FALSE)
		{
			throw sf_exception('error', 'permission_denied');
			return FALSE;
		}
		
		//first delete sub files and directories
		try
		{
			// delete files of directory
			$filecol = sf_api('MODEL', 'FileSqlCollection');
			$filecol->setIdclient($this->getField('idclient'));
			$filecol->setIdlang($this->getField('idlang'));
			$filecol->setFreefilter('area', $this->getField('area'));
			$filecol->setFreefilter('iddirectory', $this->getId());
			$filecol->generate();
			
			if($filecol->getCount() > 0)
			{
				$iter = $filecol->getItemsAsIterator();
				while($iter->valid()) {
					$file = $iter->current();
					//echo "delete file ".$file->getField('filename')."<br />";
					$file->delete();
					
					$iter->next();
				}
			}
			unset($filecol);
			
			// delete subdirectories
			$dircol = sf_api('MODEL', 'DirectorySqlCollection');
			$dircol->setIdclient($this->getField('idclient'));
			$dircol->setIdlang($this->getField('idlang'));
			$dircol->setFreefilter('area', $this->getField('area'));
			$dircol->setFreefilter('parentid', $this->getId());
			$dircol->generate();
			
			if($dircol->getCount() > 0)
			{
				$iter = $dircol->getItemsAsIterator();
				while($iter->valid()) {
					$directory = $iter->current();
					//echo "delete directory ".$directory->getField('dirname')."<br />";
					$directory->delete();
					
					$iter->next();
				}
			}
			unset($dircol);
		}
		catch(Exception $e)
		{
			// TODO collect the recursive exeptions and create a new one in the top item
			//echo $e->getMessage()."<br />";
		}
		
		// delete sql item 
		if(parent::delete($tablename) == FALSE)
		{
			throw sf_exception('error', 'delete_directory_from_db_failed', array('dirname' => $fields['dirname']));
			return FALSE;
		}
		
		// delete rights
		if($this->deleteAllPerms() == FALSE)
		{
			throw sf_exception('warning', 'delete_rights_failed', array('dirname' => $this->getField('dirname')));
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Overwrite the function to load the item, the language data and
	 * retrieve the parentdirectory to parentid.
	 * @see API/MODELS/SF_MODEL_AbstractSqlItem#loadById($id, $tablename)
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function loadById($id, $tablename = '', $fields = '*')
	{
		$bool = TRUE;
		
		// only load this table, if the table contains the requested fields
		if(($tablename == '' || $tablename == $this->table_directory)
			&& ($fields == '*' || ($fields != '*' && $this->isFieldInTable($fields, $this->table_directory) == TRUE)))
		{
			$bool = parent::loadById($id, $this->table_directory, $fields);
			
			// on error, abort immediately
			if($bool == FALSE)
			{
				return FALSE;
			}
		}
		
		// only load this table, if the table contains the requested fields
		if(($tablename == '' || $tablename == $this->table_directory_lang)
			&& ($fields == '*' || ($fields != '*' && $this->isFieldInTable($fields, $this->table_directory_lang) == TRUE)))
		{
			// $id = iddirectory -> it is corrected in _getLoadSql()
			$bool = parent::loadById($id, $this->table_directory_lang, $fields);
		}
		
		// load information about parent directory
		$this->parentdirectory = $this->_getDirnameByIddirectory($this->getField('parentid'));
		
		return $bool;
	}
	
	/**
	 * The function is similar to loadById(),
	 * but doesn't get the parent path.
	 * So this function uses less SQL statements and is faster.
	 * Attention: Use the functions getPath(), getRelativePath()
	 * and getHtmlPath() might not work correctly!
	 * @param $id
	 * @param $tablename
	 * @param $fields
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function loadByIdWithoutPath($id, $tablename = '', $fields = '*')
	{
		return parent::loadById($id, $tablename, $fields);
	}
	
	/**
	 * Load a directory item by field dirname.
	 * Use $area to get only one result.
	 * @param string $dirname
	 * @param string $area
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function loadByDirname($dirname, $area)
	{
		if($dirname == '' || $area == '')
		{
			return FALSE;
		}
		
		$where = array();
		$where['dirname'] = mysql_real_escape_string($dirname);
		$where['area'] = mysql_real_escape_string($area);
		$where['idclient'] = $this->itemcfg['idclient'];
		//$where['idlang'] = $this->itemcfg['idlang']; // not in table_directory
		
		return $this->_load($this->table_directory, $where);
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
		else if(array_key_exists($iddirectory, self::$dirname_cache) == TRUE)
		{
			return self::$dirname_cache[$iddirectory]; 
		}
		
		$directory = sf_api('MODEL', 'DirectorySqlItem');
		$directory->setIdclient( $this->getField('idclient') );
		$directory->setIdlang( $this->getField('idclient') );
		
		if($directory->loadById((int)$iddirectory, '', array('dirname')))
		{
			self::$dirname_cache[$iddirectory] = $directory->getField('dirname');
			return self::$dirname_cache[$iddirectory];
		}
		
		unset($directory);
		return FALSE;
	}
	
	/**
	 * Replace the dirname with $dest in all items
	 * where $source is found. 
	 * @param string $source 
	 * @param string $dest 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _updateDirnameForSubdirectories($source, $dest)
	{
		$sql = "UPDATE
					".$this->table_directory."
				SET
					dirname = REPLACE(dirname, '".$source."', '".$dest."')
				WHERE
					dirname LIKE '".$source."%';";
		//echo $sql;
		$rs = $this->db->Execute($sql);

		if($rs === FALSE)
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Overwrite the creation of the load SQL statement
	 * to write own where clause for language data.
	 * @see API/MODELS/SF_MODEL_AbstractSqlItem#_getLoadSql($tablename, $fields, $where)
	 */
	protected function _getLoadSql($tablename, $fields, $where)
	{
		if($tablename == $this->table_directory_lang)
		{
			$where = "iddirectory='".$where['id']."' ";
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
		if($tablename == $this->table_directory_lang)
		{
			$foreignkey = 'iddirectory';
		}
		
		$sql = parent::_getDeleteSql($tablename, $where, $foreignkey);
		//echo $sql."<br />";
		return $sql;
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
		$sql = "SELECT COUNT(".$this->mapFieldToRow('id', $this->table_directory_lang).") AS countme 
				FROM ".$this->table_directory_lang."
				WHERE
					idclient = '".$idclient."'
					AND idlang = '0';";
		
		$rs = $this->db->Execute($sql);
		
		if($rs === false || $rs->EOF) {
			return false;
		}
		$rs->Close();
		
		// abort, if nothing to do
		if($rs->fields['countme'] <= 0)
		{
			return FALSE;
		}
		
		$sql = "SELECT *
				FROM ".$this->table_directory_lang."
				WHERE
					idclient = '".$idclient."'
					AND idlang = '0';";
		
		$rs = $this->db->Execute($sql);
	
		if($rs === FALSE || $rs->EOF )
		{
			return FALSE;
		}
		
		while(! $rs->EOF) 
		{
			// remove numeric fields and iddirectorylang
			foreach($rs->fields as $key => $val)
			{
				if(is_numeric($key) || $key == $this->mapFieldToRow('id', $this->table_directory_lang))
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
				
				$this->db->AutoExecute($this->table_directory_lang, $rs->fields, 'INSERT');
			}
			
			$rs->MoveNext();
		}
		$rs->Close();
		
		// delete all rows with idlang = 0 
		$sql = "DELETE FROM ".$this->table_directory_lang."
				WHERE
					idclient = '".$idclient."'
					AND idlang = '0';";
		
		$rs = $this->db->Execute($sql);

		if($rs === FALSE)
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
		$sql = "SELECT COUNT(".$this->mapFieldToRow('id', $this->table_directory_lang).") AS countme 
				FROM ".$this->table_directory_lang."
				WHERE
					idclient = '".$idclient."'
					AND idlang > '0';";
		
		$rs = $this->db->Execute($sql);
		
		if($rs === false || $rs->EOF) {
			return false;
		}
		$rs->Close();
		
		// abort, if nothing to do
		if($rs->fields['countme'] <= 0)
		{
			return FALSE;
		}
		
		$sql = "SELECT *
				FROM ".$this->table_directory_lang."
				WHERE
					idclient = '".$idclient."'
					AND idlang = '".$start_idlang."';";
		
		$rs = $this->db->Execute($sql);

		if($rs === FALSE || $rs->EOF )
		{
			return FALSE;
		}
		
		while(! $rs->EOF) 
		{
			// remove numeric fields and iddirectorylang
			foreach($rs->fields as $key => $val)
			{
				if(is_numeric($key))
				{
					unset($rs->fields[$key]);
				}
			}
			
			$rs->fields['idlang'] = '0';
			$this->db->AutoExecute($this->table_directory_lang, $rs->fields, 'UPDATE', $this->mapFieldToRow('id', $this->table_directory_lang)." = '".$rs->fields[ $this->mapFieldToRow('id', $this->table_directory_lang) ]."'");
			
			$rs->MoveNext();
		}
		$rs->Close();
		
		// delete all rows with idlang = 0 
		$sql = "DELETE FROM ".$this->table_directory_lang."
				WHERE
					idclient = '".$idclient."'
					AND idlang > '0';";
		
		$rs = $this->db->Execute($sql);

		if($rs === FALSE)
		{
			return FALSE;
		}
		
		$rs->Close();
		
		return TRUE;
	}
	
	/**
	 * Copies the language metadata (table table_directory_lang) from a
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
				FROM ".$this->table_directory_lang."
				WHERE
					".$this->mapFieldToRow('id', $this->table_directory)." = '".$this->getId()."'
					AND idlang = '".$foreign_idlang."'
				LIMIT 0, 1;"; // use iddirectory and idclient
		//echo $sql."<br />";
		
		$rs = $this->db->Execute($sql);
	
		if($rs === FALSE || $rs->EOF )
		{
			return FALSE;
		}
		
		// delete all rows with idlang = 0 
		$sql = "DELETE FROM ".$this->table_directory_lang."
				WHERE
					".$this->mapFieldToRow('id', $this->table_directory)." = '".$this->getId()."'
					AND idlang = '".$to_idlang."';"; // use iddirectory and idclient
		//echo $sql."<br />";
		
		$rs2 = $this->db->Execute($sql);

		if($rs2 === FALSE)
		{
			return FALSE;
		}
		
		$rs2->Close();
		
		while(! $rs->EOF) 
		{
			// remove numeric fields and iddirectorylang
			foreach($rs->fields as $key => $val)
			{
				if(is_numeric($key) || $key == $this->mapFieldToRow('id', $this->table_directory_lang))
				{
					unset($rs->fields[$key]);
				}
			}
			
			$rs->fields['idlang'] = $to_idlang;
			$this->db->AutoExecute($this->table_directory_lang, $rs->fields, 'INSERT');
			
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
	 * client setting 'allow_invalid_dirnames':
	 * 	0 = returns FALSE
	 * 	1 = does nothing and returns the given $directoryname
	 * 	2 = sanitize the $directoryname and returns it 
	 * @param string $directoryname
	 * @return string|boolean Returns the sanitized $directoryname or FALSE.
	 */
	protected function _sanitizeDirectoryname($directoryname)
	{
		// check if invalid directorynames are allowed
		switch(self::$client_settings['allow_invalid_dirnames'])
		{
			// no: return FALSE
			case 0:
				$validator = sf_api('LIB', 'Validation');
				if($validator->directoryname($directoryname) == FALSE)
				{
					$directoryname = FALSE;
				}
				unset($validator);
				break;
			// yes: use current filename
			case 1:
				//$directoryname = $directoryname;
				break;
			// correct filename: use the corrected filename
			case 2:
				$fsm = sf_api('LIB', 'FilesystemManipulation');
				$directoryname = $fsm->cleanDirectoryname($directoryname);
				unset($fsm);
				break;
		}
		return $directoryname;
	} 
}
?>