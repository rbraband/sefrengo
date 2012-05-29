<?php

$this->includeClass('MODEL', 'AbstractSqlCollection');

class SF_MODEL_LogSqlCollection extends SF_MODEL_AbstractSqlCollection
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
		$this->setCfg('model', 'LogSqlItem');

		// then call parent constructor to get tablefields
		parent::__construct();
		
		// set fulltextsearchfields afterwards
		$this->setCfg(
			'fulltextsearchfileds', 
			array(
				//'message', // messages will be filtered manually by key
				'param'
			)
		);
	}
}
?>