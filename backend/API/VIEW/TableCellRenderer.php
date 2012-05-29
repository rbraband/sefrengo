<?php

$this->includeClass('INTERFACE', 'TableCellRenderer');
$this->includeClass('VIEW', 'AbstractView');

class SF_VIEW_TableCellRenderer extends SF_VIEW_AbstractView
		implements SF_INTERFACE_TableCellRenderer
{
	/**
	 * Renderer configuration
	 * @var array
	 */
	protected $renderer_config = array();
	
	/**
	 * Configuration for the current field
	 * @var array
	 */
	protected $config_fields = array();
	
	/**
	 * Name of the current config field (key of the associative config_fields)
	 * @var string
	 */
	protected $current_config_field = '';
	
	/**
	 * Instance of the current item
	 * @var SF_MODEL_AbstractSqlItem
	 */
	protected $item = null;
	
	/**
	 * Constructor sets the template filename
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->loadTemplatefile('tcr_default.tpl');
	}
	
	/**
	 * Sets the renderer configuration
	 * @param array $renderer_config
	 * @return void
	 */
	public function setRendererConfig($renderer_config)
	{
		$this->renderer_config = $renderer_config;
	}
	
	/**
	 * Sets the configuration for the current field
	 * @param array $config_fields
	 * @return void
	 */
	public function setConfigFields($config_fields)
	{
		$this->config_fields = $config_fields;
	}
	
	/**
	 * Sets the current config field (the key of the assoziative config array)
	 * @param array $current_config_field
	 * @return void
	 */
	public function setCurrentConfigField($current_config_field)
	{
		$this->current_config_field = $current_config_field;
	}
	
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
	 * Run through the set template variables and call the 
	 * generate method of the view templates and add the
	 * strings or array. 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	public function generate()
	{
		if($this->item != null)
		{
			$values = $this->_getValues($this->config_fields);

			$this->tpl->setCurrentBlock('TABLE_CELL');
			$this->tpl->setVariable('TABLE_CELL_VALUE', $this->_formatValues($this->config_fields, $values));
			$this->tpl->parseCurrentBlock();
		}
		
		$this->tpl->parse();
		$this->generated_view = $this->tpl->get();
		return TRUE;
	}
	
	/**
	 * Retrieves all values from the listed fieldnames in the $config_fields array
	 * @param array $config_fields
	 * @return array Returns the values as associative array as array('fieldname' => 'value', ...)
	 */
	protected function _getValues($config_fields)
	{
		foreach ($config_fields['fieldname'] AS $fieldname)
		{
			$values[ $fieldname ] = $this->item->getField( $fieldname );
		}
		
		return $values;
	}
	
	/**
	 * If pattern exists in $config_fields, a TableCellFormater is created
	 * and the value is formated. The array $values is used as base for formatting.
	 * @param array $config_fields Extract of the $config_fields for one field
	 * @param array $values An associative array as array('fieldname' => 'value', ...)
	 * @return string Returns the ready formated values as string
	 */
	protected function _formatValues($config_fields, $values)
	{
		if( array_key_exists('format', $config_fields) == TRUE &&
			array_key_exists('pattern', $config_fields['format']) == TRUE)
		{
			$formatter = null;
			
			if(sf_api_exists('VIEW', $config_fields['format']['classname']) == TRUE)
			{
				$formatter = sf_api('VIEW', $config_fields['format']['classname']);
			}
			else if(sf_api_exists('VIEW', 'TableCellFormatter') == TRUE)
			{
				$formatter = sf_api('VIEW', 'TableCellFormatter');
			}

			if($formatter instanceof SF_INTERFACE_TableCellFormatter)
			{
				$formatter->setItem($this->item);
				$value = $formatter->format($config_fields['format']['pattern'], $values);
			}
		}
		else
		{
			$value = implode(' ', $values);
			$value = htmlentities($value, ENT_COMPAT, 'UTF-8');
		}
		
		return $value;
	}
	
}
?>