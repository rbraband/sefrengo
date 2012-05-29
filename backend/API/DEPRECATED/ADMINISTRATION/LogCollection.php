<?php
class SF_ADMINISTRATION_LogCollection extends SF_LIB_ApiObject {
	
	var $items = array();
	var $count_all = 0;
	var $count = 0;
	var $db;
	var $conf = array('searchterm' => false,
						'limit_start' => false,
						'limit_max' => false,
						'order_by' => false,
						'order_direction' => false,
						'userfilter' => false,
	);
	
	
	function SF_ADMINISTRATION_LogCollection() {
		$this->db = sf_factoryGetObjectCache('DATABASE', 'Ado');
	}
	
	function setLimitMax($max) {
		$this->conf['limit_max'] = (int) $max;
	}
	function setLimitStart($start) {
		$this->conf['limit_start'] = (int) $start;
	}
	function setOrder($order, $direction = 'ASC') {
		$this->conf['order_by'] = $order;
		$this->conf['order_direction'] = ($direction == 'DESC') ? 'DESC':'ASC';
	}
	function setFilter($field, $value) {
		$this->conf['userfilter'][$field] = $value;
	}
	function setFilterClient($is_backend, $client) {
		$this->conf['userfilter']['client'][] = array(
			'is_backend' => $is_backend,
			'client' => $client
		);
	}
	

	function generate() {
		global $cms_db;
		
		//generate filter
		$sql_where = $this->createSqlWhereFromFilter($this->conf['userfilter']);
		
		//generate order
		$sql_order = '';
		if ($this->conf['order_by'] != '') {
			$sql_order = ' ORDER BY L.'.$this->conf['order_by'] . ' '. $this->conf['order_direction'];
		}

		// generate limit
		$sql_limit = '';
		if ($this->conf['limit_start'] || $this->conf['limit_max']) {
			if ($this->conf['limit_max']) {
				$sql_limit = ' LIMIT '. (int) $this->conf['limit_start'].', '. (int) $this->conf['limit_max'];
			} else if ($this->conf['limit_start']) {
				$sql_limit = ' LIMIT '. (int) $this->conf['limit_start'];
			}
		}
		
		//set sql
		$sql = "SELECT DISTINCT L.idlog 
				FROM
					".$cms_db['logs']." L
					$sql_where
					$sql_order
					$sql_limit
				";
		//echo $sql;
		$rs = $this->db->Execute($sql);
		
		if ($rs === false) {
			return false;
		}
		
		if ($rs->EOF ) {
			return false;
		}
		
		while (! $rs->EOF) {
			$item = sf_factoryGetObject('ADMINISTRATION', 'LogItem');
			
			if ($item->loadByIdlog($rs->fields['idlog'])) {
				array_push($this->items, $item);
			}
			$rs->MoveNext();
		}
		
		$this->count = count($this->items);
		
		return true;
	}
	
	function &get() {
		$iter = sf_factoryGetObject('UTILS', 'ArrayIterator');
		$iter->loadByRef($this->items);
		return $iter;
	}
	
	function getCountAll() { 
		if ($this->count_all < 1) {
			$this->_countAll();
		}
		
		return $this->count_all;
	}
	
	function getCount() { return $this->count;}
	function reset() { return false; /*TODO*/}
	
	function _countAll() {
		global $cms_db;
		
		//generate filter
		$sql_where = $this->createSqlWhereFromFilter($this->conf['userfilter']);
		
		//generate order
		$sql_order = '';
		if ($this->conf['order_by'] != '') {
			$sql_order = ' ORDER BY L.'.$this->conf['order_by'] . ' '. $this->conf['order_direction'];
		}
		
		//set sql
		$sql = "SELECT DISTINCT COUNT(L.idlog) AS countme
				FROM
					".$cms_db['logs']." L
					$sql_where
					$sql_order
				";
		$rs = $this->db->Execute($sql);
		
		if ($rs === false) {
			return false;
		}
		
		if ($rs->EOF ) {
			return false;
		}
		
		$this->count_all = $rs->fields['countme'];
		return true;
	}
	
	
	function createSqlWhereFromFilter($filter) {
		$sql_where = array();
		if (!empty($filter)) {			
			//startdate
			if(isset($filter['startdate']) && $filter['startdate'] != "" &&
				isset($filter['starttime']) && $filter['starttime'] != "") {
				$sql_where[] = ' L.created >= '.strtotime($filter['startdate']." ".$filter['starttime']);
			}
			//enddate
			if(isset($filter['enddate']) && $filter['enddate'] != "" &&
				isset($filter['endtime']) && $filter['endtime'] != "") {
				$sql_where[] = ' L.created <= '.strtotime($filter['enddate']." ".$filter['endtime']);
			}
			//priority
			if(isset($filter['priority']) && !empty($filter['priority']) && !in_array("all", $filter['priority'])) {
				$priority = (is_array($filter['priority'])) ? implode("','", $filter['priority']) : $filter['priority'];
				$sql_where[] = ' L.priority IN (\''. $priority .'\')';
			}
			//type
			if(isset($filter['type']) && !empty($filter['type']) && !in_array("all", $filter['type'])) {
				$type = (is_array($filter['type'])) ? implode("','", $filter['type']) : $filter['type'];
				$sql_where[] = ' L.type IN (\''. $type .'\')';
			}
			//author
			if(isset($filter['author']) && !empty($filter['author'])) {
				$sql_where[] = $this->generateLikeSearchForFields($filter['author'], array('L.author'));
			}
			//client
			if(isset($filter['client']) && !empty($filter['client']) && !in_array("all", $filter['client'])) {
				$clients = array();
				foreach($filter['client'] as $tmp) {
					$clients[] = ' (L.is_backend = \''.$tmp['is_backend'].'\' && L.client = \''.$tmp['client'].'\')';
				}
				if(count($clients) > 0) {
					$sql_where[] = ' ('.implode(' || ', $clients).')';
				}
			}
		}
		$sql_where = (!empty($sql_where)) ? " WHERE " . implode(" && ", $sql_where) : "";
		
		return $sql_where;
	}
	
	/**
	 * Generates an search query for the searchterm in the given fields.
	 * The search term is divided as spaces into separate terms. 
	 * @param String $searchterm
	 * @param array $fields
	 * @return String search query
	 */
	function generateLikeSearchForFields($searchterm, $fields) {
		$searchterm = trim($searchterm);
		$sql_search = '';
		if ($searchterm != '') {
			$term = mysql_real_escape_string($searchterm);
			$pieces = explode(' ', $term);			
			$sql_search_array = array();
			foreach ($pieces AS $word) {
				if (trim($word) == '') {
					continue;
				}
				$sql_search_array_single = array();
				foreach ($fields AS $field) {
					array_push($sql_search_array_single, $field." LIKE '%".$word."%'");
				}
				array_push($sql_search_array, ' ( ' .implode(' OR ', $sql_search_array_single) .' ) ');
			}
			$sql_search = implode(' AND ' ,$sql_search_array);
		}
		return $sql_search;
	}
}

?>