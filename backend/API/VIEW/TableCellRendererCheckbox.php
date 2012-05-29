<?php

$this->includeClass('VIEW', 'TableCellRenderer');

class SF_VIEW_TableCellRendererCheckbox extends SF_VIEW_TableCellRenderer
{
	/**
	 * Constructor sets the template filename
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->loadTemplatefile('tcr_checkbox.tpl');
	}
	
	/**
	 * Creates the checkbox and set it to the template
	 * @return boolean Returns TRUE on success. Otherwise returns FALSE.
	 */
	public function generate()
	{
		$tplvals = array();
		
		$tplvals['CHECK_NAME'] = $this->config_fields['renderer']['chk_name'].'[]';
		$tplvals['CHECK_ID'] = $this->config_fields['renderer']['chk_name'].'_'.$this->item->getId();
		$tplvals['CHECK_VALUE'] = $this->item->getId();
		$tplvals['CHECKED'] = '';

		$this->tpl->setVariable($tplvals);
		
		return parent::generate();
	}
	
	
}
?>