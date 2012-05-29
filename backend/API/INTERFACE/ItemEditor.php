<?php
interface SF_INTERFACE_ItemEditor
{
	/**
	 * Sets the editor configuration by merging
	 * it with the default configuration of the editor.
	 * 
	 * @param array $config
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setConfig($config);
	
	/**
	 * Sets the editor name, that is usually the
	 * name of the HTML field in the editor output.
	 * 
	 * @param string $name
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setEditorName($name);
	
	/**
	 * Set the item that is modified by this editor.
	 * 
	 * @param object $item
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setItem($item);
	
	/**
	 * Checks if the item fullfills the object type.
	 * Use the instanceof keyword to do the check.
	 * 
	 * @param object $item
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function isItemAllowed($item);
	
	/**
	 * Checks if editor is available for the given item.
	 * In this case the file extension must accord to the 
	 * editor configuration. 
	 * 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function isEditorAvailable();
	
	/**
	 * Build the editor and return the HTML output to include
	 * in the page.
	 * To convert the attributes into HTML attributes use the
	 * HTML Helper Library.
	 * 
	 * @param array $attributes
	 * @return string Returns the output of the editor
	 */
	public function getEditor($attributes = array());

	/**
	 * Returns the value that the
	 * field validation use for checking.
	 * The configuration of the validator is
	 * set up in the form field configuration.
	 * 
	 * @return string Returns the value for the validation
	 */
	public function getValidationValue();
	
	/**
	 * If the editor is used to modify a
	 * specific field the return value of
	 * this function sets the database field.
	 * 
	 * @return string Returns the value for the database field
	 */
	public function getFieldValue();
	
	/**
	 * Transaction begin prepare the saving
	 * and test if saving is possible.
	 * 
	 * Notice: If the editor does several 
	 * things like store an image or text 
	 * file the save transaction functions 
	 * are the right place to deal for it.
	 * 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function onSaveItemBegin();
	
	/**
	 * Transaction commit persits the 
	 * changes from the editor.
	 * 
	 * Notice: If the editor does several 
	 * things like store an image or text 
	 * file the save transaction functions 
	 * are the right place to deal for it.
	 * 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function onSaveItemCommit();
	
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
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function onSaveItemRollback();
}
?>