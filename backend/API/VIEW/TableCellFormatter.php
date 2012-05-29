<?php

$this->includeClass('INTERFACE', 'TableCellFormatter');

class SF_VIEW_TableCellFormatter extends SF_LIB_ApiObject
		implements SF_INTERFACE_TableCellFormatter
{
	/**
	 * Stores the current item
	 * @var SF_MODELS_AbstractSqlItem
	 */
	protected $item = null;
	
	/**
	 * Sets the current item for the renderer
	 * @param SF_MODELS_AbstractSqlItem $item
	 * @return void
	 */
	public function setItem($item)
	{
		$this->item = $item;
	}
	
	/**
	 * Formats the pattern with the given values
	 * @param string $pattern
	 * @param array $values Assoziatives array with array( 'fieldname' => 'value', ... );
	 * @return string Returns a formatted pattern as string
	 */
	public function format($pattern, $values)
	{
		// cache pattern as $output
		$output = $pattern;
		
		$matches = array();
		preg_match_all('/{([^{|}]*)}/', $pattern, $matches);
		for($i = 0, $len = count($matches[1]); $i < $len; $i++)
		{
			$tplvar = explode(':', $matches[1][$i]);
			$fieldname = $tplvar[0];
			$format_func = (count($tplvar) > 0) ? $tplvar[1] : '';

			if(array_key_exists($fieldname, $values))
			{
				// Do not merge this if statement with the directly above, becaus of the following else statement!
				if($format_func != '')
				{
					$values[$fieldname] = $this->formatValue($values[$fieldname], $format_func, $fieldname);
				}
				else
				{
					//$values[$fieldname] = $values[$fieldname];
				}
			}
			else
			{
				$values[$fieldname] = '';
			}
			
			//echo $matches[0][$i]." - ".$fieldname." - ".$values[$fieldname]." - ".$format_func."<br />";
			$output = str_replace($matches[0][$i], $values[$fieldname], $output);
		}
		$output = ($output == $pattern) ? '' : $output;	
		return $output;
	}
	
	/**
	 * Format the output by the given format $pattern. If $fieldname is empty the $this->current_fieldname
	 * is taken to replace the pattern. If pattern is found the $returnval will be replaced. Otherwise
	 * it is returned directly, as given.
	 * @param string $returnval
	 * @param string $format_func
	 * @param string $fieldname
	 * @return string Returns the modified $returnval
	 */
	public function formatValue($returnval, $format_func, $fieldname = '')
	{
		switch($format_func)
		{
			case 'date':
				$returnval = ($fieldname == 'created') ? $this->item->getCreatedDate() : $returnval;
				$returnval = ($fieldname == 'lastmodified') ? $this->item->getLastmodifiedDate() : $returnval;
				break;
			case 'time':
				$returnval = ($fieldname == 'created') ? $this->item->getCreatedTime() : $returnval;
				$returnval = ($fieldname == 'lastmodified') ? $this->item->getLastmodifiedTime() : $returnval;
				break;
			case 'author':
				$returnval = ($fieldname == 'created') ? $this->item->getCreatedAuthor('', 'username') : $returnval;
				$returnval = ($fieldname == 'lastmodified') ? $this->item->getLastmodifiedAuthor('', 'username') : $returnval;
				break;
			case 'tostring':
				$returnval = print_r($returnval, TRUE);
				break;
			case 'lang':
				$lng = sf_api('LIB', 'Lang');
				$langval = $lng->get($returnval);
				$returnval = ($langval == '') ? $returnval : $langval;
				break;
			case 'asInt':
				$returnval = (int) $returnval;
				break;
		}
		
		return htmlentities($returnval, ENT_COMPAT, 'UTF-8');
	}
}
?>