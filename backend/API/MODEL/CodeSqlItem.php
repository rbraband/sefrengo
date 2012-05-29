<?php
$this->includeClass('MODEL', 'AbstractSqlItem');

class SF_MODEL_CodeSqlItem extends SF_MODEL_AbstractSqlItem
{
	/**
	 * Tablename
	 * @var string
	 */
	private $table_code = '';
	
	/**
	 * Constructor sets up the model with relevant tables and releated fields.
	 */
	public function __construct()
	{
		// call AbstractSqlItem constructor
		parent::__construct();
		
		$this->table_code = $this->cfg->db('code');
		
		// table directory
		$this->_addTable($this->table_code);
		$this->_addTableFields(
			$this->table_code,
			array(
				'idcatside' => '',
				'code' => '',
				'changed' => '1',
			)
		);
		$this->_addDefaultFields($this->table_code);
		$this->_addRowMapping(
			$this->table_code,
			array(
				'id' => 'idcode'
			)
		);
		$this->_addDisabledFields(
			$this->table_code,
			array(
				'idclient' => TRUE,
				'created' => TRUE,
				'created_author' => TRUE,
				'lastmodified' => TRUE,
				'lastmodified_author' => TRUE,
				'ip' => TRUE,
				'groupkey' => TRUE
			)
		);
	}
	
	/**
	 * Loads the code by idcatside.
	 * If $auto_insert = TRUE the filetype will be created,
	 * if it does not exists.
	 * @param int $idcatside
	 * @param int $idlang  (Default: FALSE - load current language)
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function loadByIdcatside($idcatside, $idlang = FALSE)
	{
		$idcatside = (int) $idcatside;
		$idlang = (int) $idlang;

		if ($idcatside < 1)
		{
			return FALSE;
		}

		if ($idlang < 1)
		{
			$idlang = $this->cfg->env('idlang');
		}

		$this->setField('idcatside', $idcatside);
		$this->setField('idlang', $idlang);
				
		$success = $this->_load($this->table_code, array('idcatside' => $idcatside, 'idlang' => $idlang, ));
		
		return $success;
	}
}
?>