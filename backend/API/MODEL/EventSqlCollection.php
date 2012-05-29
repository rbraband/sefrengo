<?php

$this->includeClass('MODEL', 'AbstractSqlCollection');

class SF_MODEL_EventSqlCollection extends SF_MODEL_AbstractSqlCollection
{
	/**
	 * Constructor
	 * Set up the model and fulltext searchfields
	 * @return void
	 */
	public function __construct()
	{
		// first set up the model
		$this->setCfg('model_path', 'MODEL');
		$this->setCfg('model', 'EventSqlItem');

		// then call parent constructor to get tablefields
		parent::__construct();
	}
	
	/**
	 * Overwrites the creation of the client and lang clause.
	 * @see API/MODELS/SF_MODEL_AbstractSqlCollection#_getSqlClientLang($idclient, $idlang, $fields)
	 */
	protected function _getSqlClientLang($idclient, $idlang, $fields, $accept_id_zero = FALSE)
	{
		return parent::_getSqlClientLang(array($idclient, 0), array($idlang, 0), $fields); 
	}
}
?>