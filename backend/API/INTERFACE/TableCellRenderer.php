<?php
interface SF_INTERFACE_TableCellRenderer
{
	/**
	 * Sets the renderer configuration
	 * @param array $renderer_config
	 * @return void
	 */
	public function setRendererConfig($renderer_config);
	
	/**
	 * Sets the configuration for the current field
	 * @param array $config_fields
	 * @return void
	 */
	public function setConfigFields($config_fields);
	
	/**
	 * Sets the current config field (the key of the assoziative config array)
	 * @param array $current_config_field
	 * @return void
	 */
	public function setCurrentConfigField($current_config_field);
	
	/**
	 * Sets the current item for the renderer
	 * @param SF_MODELS_AbstractSqlItem $item
	 * @return void
	 */
	public function setItem($item);
	
	/**
	 * Generates the tablecell output
	 * Note: Overwrites the function in SF_INTERFACE_VIEW
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function generate();
}
?>