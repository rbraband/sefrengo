<?php
$this->includeClass('MODEL', 'AbstractSqlItem');

class SF_MODEL_EventSqlItem extends SF_MODEL_AbstractSqlItem
{
	/**
	 * Tablename
	 * @var string
	 */
	private $table_values = '';
	
	/**
	 * Constructor sets up the model with relevant tables and releated fields.
	 */
	public function __construct()
	{
		// call AbstractSqlItem constructor
		parent::__construct();
		
		$this->table_values = $this->cfg->db('event');
		
		// table directory
		$this->_addTable($this->table_values);
		$this->_addTableFields(
			$this->table_values,
			array(
				'name' => '',
				'code' => '',
				'sortindex' => '',
				'title' => '',
				'description' => '',
				'reference_type' => '',
				'reference_id' => '',
				'reference_name' => '',
			)
		);
		$this->_addDefaultFields($this->table_values);
		$this->_addRowMapping(
			$this->table_values,
			array(
				'id' => 'idevent'
			)
		);
		$this->_addDisabledFields(
			$this->table_values,
			array(
				'ip' => TRUE,
				'groupkey' => TRUE
			)
		);
	}
}
?>