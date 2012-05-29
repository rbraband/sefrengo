<?php

$this->includeClass('INTERFACE', 'Tree');
$this->includeClass('VIEW', 'AbstractView');

class SF_VIEW_Tree extends SF_VIEW_AbstractView
{
	/**
	 * Tree model to build the tree for
	 * @var SF_INTERFACE_Tree
	 */
	protected $treemodel;
	
	/**
	 * Options for the view
	 * @var array
	 */
	protected $options = array(
		'roottree' => array(
			'attributes' => ''
		),
		'trees' => array(
			'attributes' => ''
		),
		'rootleaf' => array(
			//'text' => 'root',
			//'url' => 'url'
			'attributes' => '',
			'position' => 'outside'
		),
		'leafs' => array(
			//'text' => array(
			//	'object' => $this,
			//	'function' => 'callbackfunction'
			//),
			//'url' => 'url',
			'attributes' => ''
		),
		'ignore_leafs' => array(),
		'active_leaf' => 0
	);
	
	/**
	 * Constructor sets the template filename
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->loadTemplatefile('tree.tpl');
	}
	
	/**
	 * Set the options as array
	 * @param array $options 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setOptions($options)
	{
		if(! is_array($options))
		{
			return FALSE;
		}
		
		// merge deeply
		foreach($options as $name => $option)
		{
			if(is_array($option))
			{
				$options[$name] = array_merge($this->options[$name], $options[$name]);
			}
		}
		$this->options = array_merge($this->options, $options);
		
		return TRUE;
	}
	
	/**
	 * Set the tree as model
	 * @param SF_INTERFACE_Tree $treemodel 
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function setTreeModel($treemodel)
	{
		if(! ($treemodel instanceof SF_INTERFACE_Tree))
		{
			return FALSE;
		}
		
		$this->treemodel = $treemodel;
		
		return TRUE;
	}
	
	/**
	 * Builds a tree from a tree model
	 * @param integer $id
	 * @param boolean $is_root
	 * @return string Returns the ready build tree
	 */
	public function buildTree($id, $is_root = TRUE)
	{
		$children = $this->treemodel->getChildren($id);
		
		$tpl_raw['root_link'] = $this->tpl->blocklist['ROOT_LINK'];
		$tpl_raw['root_nolink'] = $this->tpl->blocklist['ROOT_NOLINK'];
		$tpl_raw['leaf'] = $this->tpl->blocklist['LEAF_NOLINK'];
		if($this->options['leafs']['url'] != '')
		{
			$tpl_raw['leaf'] = $this->tpl->blocklist['LEAF_LINK'];
		}
		$tpl_raw['tree'] = $this->tpl->blocklist['TREE'];
		$leafs = '';
		foreach($children as $child)
		{
			if(in_array($child, $this->options['ignore_leafs']))
			{
				continue;
			}
			
			$subtree = '';
			if($this->treemodel->hasChildren($child))
			{
				$subtree = $this->buildTree($child, FALSE);
			}
			
			$leafs .= $this->_buildLeaf($tpl_raw['leaf'], $child, $subtree);
		}
		
		$treeoptions = $this->options['trees'];
		if($is_root == TRUE && $this->options['rootleaf']['position'] == 'outside')
		{
			$treeoptions = $this->options['roottree'];
		}
		
		$tplvals['{LEAFS}'] = $leafs;
		$tplvals['{TREE_ATTRIBUTES}'] = (array_key_exists('attributes', $treeoptions)) ? $treeoptions['attributes'] : '';
		$keys = array_keys($tplvals);
		$output = str_replace($keys, $tplvals, $tpl_raw['tree']);
		$tplvals = null;
		
		// root leaf inside the tree -> use current output as subtree
		if($is_root == TRUE && $this->options['rootleaf']['position'] == 'inside')
		{
			$tplvals['{LEAFS}'] = $this->_buildLeaf($tpl_raw['leaf'], 0, $output, TRUE);
			$tplvals['{TREE_ATTRIBUTES}'] = (array_key_exists('attributes', $this->options['roottree'])) ? $this->options['roottree']['attributes'] : '';
			$keys = array_keys($tplvals);
			$output = str_replace($keys, $tplvals, $tpl_raw['tree']);
		}
		// root leaf outside the tree -> append current output
		else if($is_root == TRUE && $this->options['rootleaf']['position'] == 'outside')
		{
			$attributes = (array_key_exists('attributes', $this->options['rootleaf'])) ? $this->options['rootleaf']['attributes'] : '';
			if($id == $this->options['active_leaf'])
			{
				$attributes = (array_key_exists('attributes_active', $this->options['rootleaf'])) ? $this->options['rootleaf']['attributes_active'] : $attributes;
			}
			
			$tplvals['{TEXT}'] = (array_key_exists('text', $this->options['rootleaf'])) ? $this->options['rootleaf']['text'] : '';;
			$tplvals['{ATTRIBUTES}'] = str_replace('{id}', 0, $attributes);
			$tplvals['{SUBTREE}'] = $output;
			
			// if url is set, then create link
			if(array_key_exists('url', $this->options['rootleaf']) === TRUE && $this->options['rootleaf']['url'] != "")
			{
				$tplvals['{URL}'] = $this->options['rootleaf']['url'];
				$keys = array_keys($tplvals);
				$output = str_replace($keys, $tplvals, $tpl_raw['root_link']);
			}
			else
			{
				$keys = array_keys($tplvals);
				$output = str_replace($keys, $tplvals, $tpl_raw['root_nolink']);
			}
			
		}
		
		$this->generated_view = $output;
		
		return $output;
	}
	
	/**
	 * Set the generated template.
	 * @see API/VIEWS/SF_VIEW_AbstractView#generate()
	 * @return boolean
	 */
	public function generate()
	{
		return TRUE;
	}
	
	/**
	 * Build a leaf with id and subtree.
	 * Used options from {@link $options}.
	 * @param string $tpl Leaf template
	 * @param integer $id
	 * @param string $subtree Ready build subtree
	 * @param boolean $is_root
	 * @return string Returns the ready leaf with subtrees (if set) 
	 */
	protected function _buildLeaf($tpl, $id, $subtree, $is_root = FALSE)
	{
		$options = $this->options['leafs'];
		
		if($is_root == TRUE)
		{
			$options = $this->options['rootleaf'];
		}
		
		$url = (array_key_exists('url', $options)) ? $options['url'] : '';
		$text = (array_key_exists('text', $options)) ? $options['text'] : '';
		$attributes = (array_key_exists('attributes', $options)) ? $options['attributes'] : '';
		if($id == $this->options['active_leaf'])
		{
			$attributes = (array_key_exists('attributes_active', $options)) ? $options['attributes_active'] : $attributes;
		}
		
		$tplvals['{LEAF_URL}'] = str_replace('{id}', $id, $url);
		$tplvals['{LEAF_TEXT}'] = $id;
		// callback function
		if(is_array($text) === TRUE)
		{
			$tplvals['{LEAF_TEXT}'] = call_user_func(array($text['object'], $text['function']), $id);
		}
		else if(is_string($text) === TRUE)
		{
			$tplvals['{LEAF_TEXT}'] = $text;
		}
		$tplvals['{LEAF_ATTRIBUTES}'] = str_replace('{id}', $id, $attributes);
		$tplvals['{SUBTREE}'] = $subtree;
		
		$keys = array_keys($tplvals);
		return str_replace($keys, $tplvals, $tpl);
	}
}
?>