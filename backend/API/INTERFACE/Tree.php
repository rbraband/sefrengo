<?php
interface SF_INTERFACE_Tree
{
	/**
	 * Generates the tree with previous set settings. 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function generate();
	
	/**
	 * Returns the parent id of the given child $id.
	 * @param integer $id
	 * @return integer
	 */
	public function getParent($id);
	
	/**
	 * Returns an array with all parents form the given $parent.
	 * @param integer $parent
	 * @param boolean $is_first
	 * @return array
	 */
	public function getParentsRecursive($parent, $is_first = TRUE);
	
	/**
	 * Checks if the given $parent has children.
	 * @param integer $parent
	 * @return boolean Returns TRUE if has children or FALSE if not.
	 */
	public function hasChildren($parent);
	
	/**
	 * Returns the nearst children to given $parent as array.
	 * @param integer $parent
	 * @return array
	 */
	public function getChildren($parent);
	
	/**
	 * Returns all children to given $parent as array.
	 * @param integer $parent
	 * @param boolean $is_first
	 * @return array
	 */
	public function getChildrenRecursive($parent, $is_first = TRUE);
}
?>