<?php
interface SF_INTERFACE_Collection
{
	/**
	 * Generates the collection with previous set settings.
	 * @param boolean $load_ids_only Loads only the IDs of the items, not the items itself
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function generate($load_ids_only = FALSE);
	
	/**
	 * Returns all IDs from the loaded items as array.
	 * @return array 
	 */
	public function getIdsAsArray();
	
	/**
	 * Returns the generated collection as array.
	 * @return array 
	 */
	public function getItemsAsArray();
	
	/**
	 * Returns the generated collection as PHP5 iterator.
	 * @return Iterator
	 */
	public function getItemsAsIterator();
	
	/**
	 * Returns the number of all possible items
	 * @return integer
	 */
	public function getCountAll();
	
	/**
	 * Returns the number of current loaded items
	 * @return integer
	 */
	public function getCount();
	
	/**
	 * Resets the collection to default state.
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function reset();
}
?>