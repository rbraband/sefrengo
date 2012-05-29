<?php
interface SF_INTERFACE_Filesystem
{
	/**
	 * Creates the item by given $fields
	 * 
	 * @param array $fields Array with schema array('fieldname' => 'value')
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function create($fields);
	
	/**
	 * Edits the item by given $fields
	 * 
	 * @param array $fields Array with schema array('fieldname' => 'value')
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function edit($fields);
	
	/**
	 * Deletes the item
	 * 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function delete();
	
	/**
	 * Copies the item by given $fields
	 * 
	 * @param array $fields Array with schema array('fieldname' => 'value')
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function copy($fields);
	
	/**
	 * Moves the item by given $fields
	 * 
	 * @param array $fields Array with schema array('fieldname' => 'value')
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function move($fields);
	
}
?>