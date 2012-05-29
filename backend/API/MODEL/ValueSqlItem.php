<?php
$this->includeClass('MODEL', 'AbstractSqlItem');

class SF_MODEL_ValueSqlItem extends SF_MODEL_AbstractSqlItem
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
		
		$this->table_values = $this->cfg->db('values');
		
		// table directory
		$this->_addTable($this->table_values);
		$this->_addTableFields(
			$this->table_values,
			array(
				'group_name' => '',
				'description' => '',
				'key1' => '',
				'key2' => '',
				'key3' => '',
				'key4' => '',
				'value' => '',
				'conf_sortindex' => '',
				'conf_desc_langstring' => '',
				'conf_head_langstring' => '',
				'conf_input_type' => '',
				'conf_input_type_val' => '',
				'conf_head_langstring' => '',
				'conf_input_type_langstring' => '',
				'conf_visible' => '',
			)
		);
		$this->_addDefaultFields($this->table_values);
		$this->_addRowMapping(
			$this->table_values,
			array(
				'id' => 'idvalues'
			)
		);
		$this->_addDisabledFields(
			$this->table_values,
			array(
				'created' => TRUE,
				'created_author' => TRUE,
				'lastmodified' => TRUE,
				'lastmodified_author' => TRUE,
				'ip' => TRUE,
				'groupkey' => TRUE
			)
		);
	}

	public function loadByGroupAndKeys($group, $keys)
	{
		$fields = $keys;
		$fields['group_name'] = $group;

		if( $this->isFieldInTable(array_keys($fields), $this->table_values, TRUE) === FALSE)
		{
			return FALSE;
		}
		
		foreach($fields as $key => $value)
		{
			$fields[$key] = mysql_real_escape_string($value);
		}
		
		return $this->_load($this->table_values, $fields);
	}
}
?>