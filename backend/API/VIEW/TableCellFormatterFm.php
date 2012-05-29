<?php

$this->includeClass('VIEW', 'TableCellFormatter');

class SF_VIEW_TableCellFormatterFm extends SF_VIEW_TableCellFormatter
{
	/**
	 * Modifies the $returnval. Switches action by the given $format_func and $fieldname 
	 * @param string $returnval
	 * @param string $format_func
	 * @param string $fieldname
	 * @return string Returns the modified $returnval
	 */
	public function formatValue($returnval, $format_func, $fieldname = '')
	{
		switch($format_func)
		{
			case 'filesize':
				$fsm = sf_api('LIB', 'FilesystemManipulation');
				$returnval = $fsm->readablizeBytes($returnval);
				break;
				
			default:
				$returnval = parent::formatValue($returnval, $format_func, $fieldname);
		}
		
		return $returnval;
	}
}
?>