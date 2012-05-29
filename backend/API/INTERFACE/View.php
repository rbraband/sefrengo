<?php
interface SF_INTERFACE_View
{
	/**
	 * Loads a template from a file and generates internal lists for blocks and variables. 
	 * @param string $filename file to load
	 * @param boolean $remove_unknown_variables if TRUE, not substituted placeholders in a block will be removed 
	 * @param boolean $remove_empty_blocks if TRUE, not touched blocks will be removed. Blocks can be touched with HTML_Template_IT::touchBlock()
	 * @return Returns TRUE on success, FALSE on failure. 
	 */
	public function loadTemplatefile($filename, $remove_unknown_variables, $remove_empty_blocks);
	
	/**
	 * Add a template variable with $name and $content.
	 * $block locates where the variable is set in the template.
	 * @param string $name variable name 
	 * @param string|array|SF_INTERFACE_View $content
	 * @param string $block
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	public function addTemplateVar($name, $content, $block = '__global__');
	
	/**
	 * Run through the set template variables and call the 
	 * generate method of the view templates and add the
	 * strings or array. 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	public function generate();
	
	/**
	 * Return the generated template.
	 * If the view is generated once the cached instance
	 * is returned.
	 * @param boolean $clear_cache
	 * @return string Returns the generated view.
	 */
	public function get($clear_cache = FALSE);
}
?>