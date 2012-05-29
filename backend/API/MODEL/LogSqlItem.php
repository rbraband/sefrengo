<?php
$this->includeClass('MODEL', 'AbstractSqlItem');

class SF_MODEL_LogSqlItem extends SF_MODEL_AbstractSqlItem
{
	/**
	 * Tablename
	 * @var string
	 */
	private $table_logs = '';

	/**
	 * Default Priorities
	 * @var array
	 */
	protected $priorities = array(
		'fatal' => 0, // Fatal error: system is unusable
		'error' => 1, // Error: error conditions
		'warning' => 2, // Warning: warning conditions
		'info' => 3, // Informational: informational messages
		'debug' => 4, // Debug: debug messages
		'trace' => 5 // Trace: more detailed information
	);
	/**
	 * Stores the different perms for the object.
	 * The default type can be overwritten.
	 * @var array
	 */
	protected $objperm = array(
		'default' => array(
			'type' => 'log'
		),
	);
	
	/**
	 * Constructor sets up the model with relevant tables and releated fields.
	 */
	public function __construct()
	{
		// call AbstractSqlItem constructor
		parent::__construct();
		
		// overwrite current_author with username and not id
		$this->itemcfg['current_author'] = $this->cfg->auth('uname');
		
		$this->table_logs = $this->cfg->db('logs');
		
		// set up table
		$this->_addTable($this->table_logs);
		
		$this->_addTableFields(
			$this->table_logs,
			array(
				'is_backend' => '',
				'priority' => '',
				'priorityname' => '',
				'type' => '',
				'message' => '',
			)
		);
		
		$this->_addSerializedFields(
			$this->table_logs,
			array(
				'param' => '',
			)
		);
		
		$this->_addDefaultFields($this->table_logs);
		
		$this->_addRowMapping(
			$this->table_logs,
			array(
				'id' => 'idlog'
			)
		);
		
		$this->_addDisabledFields(
			$this->table_logs,
			array(
				'groupkey' => TRUE
			)
		);
	}
	
	/**
	 * Returns the priorities as array
	 * @return array Returns the priorities as array
	 */
	public function getPriorities()
	{
		return $this->priorities;
	}
	
	/**
	 * Creates a String from all data of the LogItem
	 * @return String Returns all data of the LogItem
	 */
	public function __toString()
	{
		global $auth;
		
		$string = "";
		$string .= date("r")."\t";
		$string .= "PRIORITY: ".$this->getField('priorityname')."\t";
		$string .= "TYPE: ".$this->getField('type')."\t";
		$string .= "MESSAGE: ".$this->getField('message')."\t";
		if(count($this->getField('param')) > 0) {
			$params = str_replace(array("\n", "\r"), "", print_r($this->getField('param'), TRUE));
			$string .= "PARAMETER: ".$params."\t";	
		} 
		if(!empty($auth->auth['uname'])) {
			$string .= "AUTHOR: ".$auth->auth['uname']."\t";
		}
		$string .= "\r\n";
		return $string;
	}
}
?>