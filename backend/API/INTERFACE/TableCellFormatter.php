<?php
interface SF_INTERFACE_TableCellFormatter
{
	/**
	 * Sets the current item for the renderer
	 * @param SF_MODELS_AbstractSqlItem $item
	 * @return void
	 */
	public function setItem($item);
	
	/**
	 * Formats the pattern with the given values
	 * @param string $pattern
	 * @param array $values Assoziatives array with array( 'fieldname' => 'value', ... );
	 * @return string Returns a formatted pattern as string
	 */
	public function format($pattern, $values);
	
	/**
	 * Modifies the $returnval. Switches action by the given $format_func and $fieldname.
	 * Otherwise it is return value directly, as given.
	 * @param string $returnval
	 * @param string $format_func
	 * @param string $fieldname
	 * @return string Returns the modified $returnval
	 */
	public function formatValue($returnval, $format_func, $fieldname = '');
}
?>