<?php

$this->includeClass('INTERFACE', 'ItemEditor');

abstract class SF_VIEW_AbstractItemEditor extends SF_LIB_ApiObject
										  implements SF_INTERFACE_ItemEditor
{
	/**
	 * WebRequest
	 * 
	 * @var SF_LIB_WebRequest
	 */
	protected $req;
	
	/**
	 * Default configuration for the editor
	 * Can be overwritten in the field configuration
	 * 
	 * @var array
	 */
	protected $config = array();
	
	/**
	 * Name of the editor.
	 * Usually it is the name of the HTML field
	 * 
	 * @var string
	 */
	protected $editor_name = '';

	/**
	 * Stores the item that is modified by this editor.
	 * 
	 * @var object
	 */
	protected $item;
	
	/**
	 * Constructor sets up {@link $req}
	 * 
	 * @return void
	 */
	public function __construct()
	{
		//init objects
		$this->req = sf_api('LIB', 'WebRequest');
	}
	
	/**
	 * Sets the editor configuration by merging
	 * it with the default configuration of the editor.
	 * 
	 * @see API/INTERFACES/SF_INTERFACE_ItemEditor#setConfig($config)
	 * @param array $config
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setConfig($config)
	{
		if(empty($config) || !is_array($config))
		{
			return FALSE;
		}
		
		$this->config = array_merge($this->config, $config);
		return TRUE;
	}
	
	/**
	 * Sets the editor name, that is usually the
	 * name of the HTML field in the editor output.
	 * 
	 * @see API/INTERFACES/SF_INTERFACE_ItemEditor#setEditorName($name)
	 * @param string $name
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setEditorName($name)
	{
		if($name == '')
		{
			return FALSE;
		}
		
		$this->editor_name = $name;
		
		return TRUE;
	}
	
	/**
	 * Set the item that is modified by this editor.
	 * 
	 * @see API/INTERFACES/SF_INTERFACE_ItemEditor#setItem($item)
	 * @param object $item
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setItem($item)
	{
		$this->item = $item;
		return TRUE;
	}
	
	/**
	 * Returns the value that the
	 * field validation use for checking.
	 * The configuration of the validator is
	 * set up in the form field configuration.
	 * 
	 * @see API/INTERFACES/SF_INTERFACE_ItemEditor#getValidationValue()
	 * @return string Returns the value for the validation
	 */
	public function getValidationValue()
	{
		return '';
	}
	
	/**
	 * If the editor is used to modify a
	 * specific field the return value of
	 * this function sets the database field.
	 * 
	 * @see API/INTERFACES/SF_INTERFACE_ItemEditor#getFieldValue()
	 * @return string Returns the value for the database field
	 */
	public function getFieldValue()
	{
		return '';
	}
	
	/**
	 * Transaction begin prepare the saving
	 * and test if saving is possible.
	 * 
	 * Notice: If the editor does several 
	 * things like store an image or text 
	 * file the save transaction functions 
	 * are the right place to deal for it.
	 * 
	 * @see API/INTERFACES/SF_INTERFACE_ItemEditor#onSaveItemBegin()
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function onSaveItemBegin()
	{
		return TRUE;
	}
	
	/**
	 * Transaction commit persits the 
	 * changes from the editor.
	 * 
	 * Notice: If the editor does several 
	 * things like store an image or text 
	 * file the save transaction functions 
	 * are the right place to deal for it.
	 * 
	 * @see API/INTERFACES/SF_INTERFACE_ItemEditor#onSaveItemCommit()
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function onSaveItemCommit()
	{
		return TRUE;
	}
	
	/**
	 * Transaction rollback reverts things
	 * that might changed in the begin
	 * function.
	 * 
	 * Notice: If the editor does several 
	 * things like store an image or text 
	 * file the save transaction functions 
	 * are the right place to deal for it.
	 * 
	 * @see API/INTERFACES/SF_INTERFACE_ItemEditor#onSaveItemRollback()
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function onSaveItemRollback()
	{
		return TRUE;
	}
	
}
?>