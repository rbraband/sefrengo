<?php

$this->includeClass('MODEL', 'LogSqlItem');
$this->includeClass('VIEW', 'TableCellFormatter');

class SF_VIEW_TableCellFormatterLogs extends SF_VIEW_TableCellFormatter
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
			// overwritten from parent function
			case 'lang':
				$lng = sf_api('LIB', 'Lang');
				$langval = $lng->get('logs_messages_'.$returnval);
				
				if($this->item instanceof SF_MODEL_LogSqlItem)
				{
					$params = $this->item->getField('param');
					
					if( is_array($params) ) 
					{
						foreach($params as $key => $val)
						{
							$langval = str_replace('{'.$key.'}', $val, $langval);
						}
					}
				}
				
				$returnval = ($langval == '') ? $returnval : $langval;
				break;
				
			default:
				$returnval = parent::formatValue($returnval, $format_func, $fieldname);
		}
		
		return $returnval;
	}
}
?>