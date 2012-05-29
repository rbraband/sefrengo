<?php
$this->includeClass('MODEL', 'AbstractSqlItem');

class SF_MODEL_FiletypeSqlItem extends SF_MODEL_AbstractSqlItem
{
	/**
	 * Tablename
	 * @var string
	 */
	private $table_filetype = '';
	
	/**
	 * Constructor sets up the model with relevant tables and releated fields.
	 */
	public function __construct()
	{
		// call AbstractSqlItem constructor
		parent::__construct();
		
		$this->table_filetype = $this->cfg->db('filetype');
		
		// table directory
		$this->_addTable($this->table_filetype);
		$this->_addTableFields(
			$this->table_filetype,
			array(
				'filetype' => '',
				'description' => '',
				'filetypepict' => '',
				'status' => '',
				'filetypegroup' => '',
				'mimetype' => '',
				'addinfo_names' => '',
			)
		);
		$this->_addDefaultFields($this->table_filetype);
		$this->_addRowMapping(
			$this->table_filetype,
			array(
				'id' => 'idfiletype'
			)
		);
		$this->_addDisabledFields(
			$this->table_filetype,
			array(
				'idlang' => TRUE,
				'ip' => TRUE,
				'groupkey' => TRUE
			)
		);
	}
	
	/**
	 * Loads the filetype by $fields['filetype'].
	 * If $auto_insert = TRUE the filetype will be created,
	 * if it does not exists.
	 * @param array $fields 
	 * @param boolean $auto_insert Insert new filetype automatically (Default: FALSE)
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function loadByFiletype($fields, $auto_insert = FALSE)
	{
		if(array_key_exists('filetype', $fields) == FALSE)
		{
			//throw sf_exception('error', 'missing_filetype');
			return FALSE;
		};
		
		$fields['filetype'] = mysql_real_escape_string($fields['filetype']);
		
		$success = $this->_load($this->table_filetype, array('filetype' => $fields['filetype']));
		
		// if load fails it may not be in DB, so insert as new filetype
		if($auto_insert == TRUE && $success == FALSE)
		{
			return $this->_insertNewFiletype($fields);
		}
		
		return $success;
	}
	
	/**
	 * Insert the new filetype by $fields['filetype'].
	 * @param array $fields
	 * @return boolean Returns TRUE on success. Otherwise returns FALSE.
	 */
	protected function _insertNewFiletype($fields)
	{
		if(array_key_exists('filetype', $fields) == FALSE)
		{
			//throw sf_exception('error', 'missing_filetype');
			return FALSE;
		};
		
		$this->setField('filetype', $fields['filetype']);
		
		if(array_key_exists('mimetype', $fields) == TRUE && $fields['mimetype'] != '')
		{
			$this->setField('mimetype', $fields['mimetype']);
		};
		
		return $this->save(); 
	}
}
?>