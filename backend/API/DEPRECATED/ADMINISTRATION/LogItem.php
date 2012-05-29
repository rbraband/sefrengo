<?php
class SF_ADMINISTRATION_LogItem extends SF_LIB_ApiObject {
	
	var $data = array(
		'idlog' => 0
	);
	var $dirty = false;
	var $db;
	
	/**
	 * Constructor initialize the database object
	 */
	function SF_ADMINISTRATION_LogItem() {
		$this->db = sf_factoryGetObjectCache('DATABASE', 'Ado');
	}
	
	
	/**
	 * Load the LogItem by idlog
	 * @param int $idlog
	 * @return Boolean If loading was successful return true, otherwise false
	 */
	function loadByIdlog($idlog) {
		$idlog = (int) $idlog;

		if ($idlog < 1) {
			return false;
		}
		
		return $this->_load('idlog', array($idlog));
	}
	
	
	/**
	 * Get the idlog of the LogItem
	 * @return int Returns the idlog
	 */
	function getIdlog() {
		return $this->data['idlog'];
	}
	
	/**
	 * Retuns the priority of the LogItem
	 * 
	 * @return int Returns the priority
	 */
	function getPriority() {
		return $this->data['priority'];
	}
	
	/**
	 * Retuns the priority name of the LogItem
	 * @return String Returns the priority name
	 */
	function getPriorityName() {
		return $this->data['priorityname'];
	}
	
	/**
	 * Retuns the type of the LogItem
	 * @return int Returns the type
	 */
	function getType() {
		return $this->data['type'];
	}
	
	/**
	 * Retuns the message of the LogItem
	 * @return String Returns the message
	 */
	function getMessage() {
		return $this->data['message'];
	}
	
	/**
	 * Retuns the parameters of the LogItem
	 * @return Array Returns the parameters as Array
	 */
	function getParam() {
		return unserialize($this->data['param']);
	}
	
	/**
	 * Retuns a boolean if the LogItem is a frontend or backend item
	 * @return Boolean Returns if it is a backend LogItem
	 */
	function getIsBackend() {
		return (bool) $this->data['is_backend'];
	}
	
	/**
	 * Retuns the author
	 * @return String Returns the author as user id
	 */
	function getAuthor() {
		return $this->data['author'];
	}
	
	/**
	 * Retuns the name of the client
	 * @return String Returns the name of the client
	 */
	function getClient() {
		return $this->data['client'];
	}
	
	/**
	 * Retuns the timestamp of the creation
	 * @return int Returns the timestamp of the creation
	 */
	function getCreated() {
		return $this->data['created'];
	}
	
	/**
	 * Set if the LogItem is set from backend or frontend
	 * @param int $is_backend true if backend, otherwise false for frontend
	 */
	function setIsBackend($is_backend) {
		$this->data['is_backend'] = (int) $is_backend;
	}
	
	/**
	 * Set the priority of the LogItem
	 * @param int $priority priority of the LogItem
	 */
	function setPriority($priority) {
		$this->data['priority'] = (int) $priority;
		$this->dirty = true;
	}
	
	/**
	 * Set the priority name of the LogItem
	 * @param String $priorityname priority name of the LogItem
	 */
	function setPriorityName($priorityname) {
		$this->data['priorityname'] = $priorityname;
		$this->dirty = true;
	}
	
	/**
	 * Set the type of the LogItem
	 * @param String $type type of the LogItem
	 */
	function setType($type) {
		$this->data['type'] = $type;
		$this->dirty = true;
	}

	/**
	 * Set the message of the LogItem
	 * @param String $message message of the LogItem
	 */
	function setMessage($message) {
		$this->data['message'] = $message;
		$this->dirty = true;
	}
	
	/**
	 * Set the parameters of the LogItem
	 * @param Array $param parameters of the LogItem
	 */
	function setParam($param) {
		$this->data['param'] = serialize($param);
		$this->dirty = true;
	}

	/**
	 * Set the id of the client
	 * @param int $client name of the client (for frontend)
	 */
	function setClient($client) {
		$this->data['client'] = $client;
		$this->dirty = true;
	}

	/**
	 * Set the the created timestamp
	 * @param Number $created
	 */
	function setCreated($created) {
		$this->data['created'] = $created;
		$this->dirty = true;
	}
	
	
	/**
	 * Saves the LogItem into the DB
	 * @return bool Returns true, if the LogItem is successfully saved. Returns false, if saving failed.
	 */
	function save() {
		global $cms_db, $auth;
		
		if (!$this->dirty) {
			return false;
		}
		
		$current_time = time();
		$current_author = (!empty($auth->auth['uname'])) ? $auth->auth['uname'] : '';
				
		if ($this->dirty) {
			//update record - is not needed for LogItem  
			if ($this->data['idlog'] > 0) {
				$this->data['created'] = $current_time;
				$this->data['author'] = $current_author;
				
				$this->db->AutoExecute($cms_db['logs'], $this->data, 'UPDATE', "idlog = '".$this->data['idlog']."'");
			
			//insert record
			} else {
				$this->data['created'] = $current_time;
				$this->data['author'] = $current_author;
				
				//idlog not needed
				unset($this->data['idlog']);
				
				$this->db->AutoExecute($cms_db['logs'], $this->data, 'INSERT');
				$this->data['idlog'] = $this->db->Insert_ID();
			}
		}
				
		$this->dirty = false;
		
		return true;
	}
	
	/**
	 * Deletes the LogItem
	 * @return bool Returns true, if the LogItem is successfully deleted. Returns false, if deletion failed.
	 */
	function delete() {
		global $cms_db;
		
		$sql = "DELETE FROM
						".$cms_db['logs']."
					WHERE
						idlog = '".$this->data['idlog']."'";
		$this->db->Execute($sql);
		
		if ($this->db->Affected_Rows() > 0) {
			$this->data['idlog'] = false;
			return true;
		}
		
		return false;
	}
	

	/**
	 * Creates a String from all data of the LogItem
	 * @return String Returns all data of the LogItem
	 */
	function toString() {
		global $auth;
		
		$string = "";
		$string .= date("r")."\t";
		$string .= "PRIORITY: ".$this->data['priorityname']."\t";
		$string .= "TYPE: ".$this->data['type']."\t";
		$string .= "MESSAGE: ".$this->data['message']."\t";
		if(count($this->data['param']) > 0) {
			$params = str_replace(array("\n", "\r"), "", print_r($this->data['param'], true));
			$string .= "PARAMETER: ".$params."\t";	
		} 
		if(!empty($auth->auth['uname'])) {
			$string .= "AUTHOR: ".$auth->auth['uname']."\t";
		}
		$string .= "\r\n";
		return $string;
	}
	
	/**
	 * Creates a XML String from all data of the LogItem
	 * @return String Returns all data of the LogItem
	 */
	function toXMLString() {
		
		
	}
	

	/*
	 * PRIVATE
	 */
	 
	function _load($what, $args) {
		global $cms_db;
		
		$sql_where = '';
		switch ($what) {
			case 'idlog':
				$sql_where = "L.idlog = '".$args[0]."'";
				break;
			default:
				return false;
		}
		
		$sql = "SELECT L.*
				FROM
					".$cms_db['logs']." L
				WHERE 
					$sql_where";
					
		$rs = $this->db->Execute($sql);
		
		if ($rs === false || $rs->EOF) {
			return false;
		}
		$this->data = array (
			'idlog' => $rs->fields['idlog'],
			'is_backend' => $rs->fields['is_backend'],
			'priority' => $rs->fields['priority'],
			'priorityname' => $rs->fields['priorityname'],
			'type' => $rs->fields['type'],
			'message' => $rs->fields['message'],
			'param' => $rs->fields['param'],
			'author' => $rs->fields['author'],
			'client' => $rs->fields['client'],
			'created' => $rs->fields['created']
		);
		
		$this->dirty = false;
		return true;
	}
}

?>