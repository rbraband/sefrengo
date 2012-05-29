<?php
interface SF_INTERFACE_SqlItem
{
	/**
	 * Returns the id of the item.
	 * $tablename is optional if item contains
	 * more than one table.
	 * @param string $tablename
	 * @return integer
	 */
	public function getId($tablename = '');
	
	/**
	 * Returns the value of the given $field.
	 * $tablename is optional if item contains
	 * more than one table.
	 * @param string $field
	 * @param string $tablename
	 * @return mixed Returns a mixed value.
	 */
	public function getField($field, $tablename = '');
	
	/**
	 * Sets a $value to $field.
	 * $tablename is optional if item contains
	 * more than one table.
	 * @param string $field
	 * @param mixed $value
	 * @param string $tablename
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setField($field, $value, $tablename = '');
	
	/**
	 * Loads the item by $id.
	 * $tablename and fields are optional if item contains
	 * more than one table.
	 * @param integer $id
	 * @param string $tablename
	 * @param string|array $fields
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function loadById($id, $tablename = '', $fields = '*');
	
	/**
	 * Saves the current item with the
	 * data that is set before.
	 * $tablename is optional if item contains
	 * more than one table.
	 * @param integer $id
	 * @param string $tablename
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function save($tablename = '');
	
	/**
	 * Deletes the current item.
	 * $tablename is optional if item contains
	 * more than one table.
	 * @param string $tablename
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function delete($tablename = '');
	
}
?>