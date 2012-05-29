<?php

$this->includeClass('VIEW', 'TableCellRenderer');

class SF_VIEW_TableCellRendererAction extends SF_VIEW_TableCellRenderer
{
	/**
	 * Constructor sets the template filename
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->loadTemplatefile('tcr_action.tpl');
	}
	
	/**
	 * Generates the action icon or text wrapped in a link
	 * @return boolean Returns TRUE on success. Otherwise returns FALSE. 
	 */
	public function generate()
	{
		foreach($this->renderer_config['actions'] as $actionname => $action)
		{
			$tplvals = array();
			$tplvals['SKIN_IMG_PATH'] = $this->getSkinImgPath();
			
			// modify actions in subclasses
			$action = $this->_modifyAction($action);
			
			if( (array_key_exists('perm', $action) == FALSE || 
				(array_key_exists('perm', $action) == TRUE && $this->item->hasPerm($action['perm']) == 1)) &&
				array_key_exists('render_as', $action) == TRUE)
			{
				switch($action['render_as'])
				{
					case 'icon':
						$block = (array_key_exists('url', $action) == TRUE) ? 'ICON_LINK' : 'ICON_NOLINK';
						$this->_buildAction($block, $action);
						break;
						
					case 'text':
						$block = (array_key_exists('url', $action) == TRUE) ? 'TEXT_LINK' : 'TEXT_NOLINK';
						$this->_buildAction($block, $action);
						break;
						
					case 'toolinfotable':
						$this->_buildToolinfoTable($action);
						break;
				}
			}
		}

		return parent::generate();
	}

	/**
	 * Modifies the action array with custom implementation in subclasses
	 * @param array $action
	 * @return array Returns the modified $action array
	 */
	protected function _modifyAction($action)
	{
		// overwrite and implement in subclasses
		return $action;
	}
	
	/**
	 * Builds an action as text or icon, both with or without a link.
	 * @param string $block Block name in the corrosponding template file
	 * @param array $action
	 * @return void
	 */
	protected function _buildAction($block, $action)
	{
		$this->tpl->setCurrentBlock($block);
		
		if(array_key_exists('text', $action) == TRUE)
		{
			if($action['text'] == 'USE_FIELDNAME')
			{
				$action['text'] = ($this->item != null) ? $this->item->getField( $action['fieldname'] ) : $action['fieldname'];
			}
			
			$tplvals[$block.'_TEXT'] = $action['text'];
		}
		
		if(array_key_exists('title', $action) == TRUE)
		{
			$tplvals[$block.'_TITLE'] = (array_key_exists('suppress_title', $action) && $action['suppress_title'] == TRUE) ? '' : $action['title'];
		}
		// use text, if no title given
		else if(array_key_exists('title', $action) == FALSE ||
				array_key_exists('text', $action) == TRUE)
		{
			$tplvals[$block.'_TITLE'] = (array_key_exists('suppress_title', $action) && $action['suppress_title'] == TRUE) ? '' : $action['text'];
		}
		
		if(array_key_exists('url', $action) == TRUE)
		{
			$tplvals[$block.'_URL'] = ($this->item != null) ? str_replace('{id}', $this->item->getId(), $action['url']) : $action['url'];
		}
		
		if(array_key_exists('attributes', $action) == TRUE)
		{
			$tplvals[$block.'_ATTRIBUTES'] = $action['attributes'];
		}
		
		if(array_key_exists('icon', $action) == TRUE)
		{
			$tplvals[$block.'_IMGSRC'] = $this->getSkinImgPath().$action['icon'];
		}
		
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Creates the tooltip info table from the given
	 * $action array.
	 * @param array $action
	 * @return void
	 */
	protected function _buildToolinfoTable($action)
	{
		foreach ($action AS $key => $item)
		{
			if($key == 'table_class' || $key == 'render_as')
			{
				continue;
			}
			
			if($key == 'lang_head')
			{
				$tplvals['TOOLINFO_HEAD_NAME'] = $item;
				$tables = $this->item->getTablenames();
				$tplvals['TOOLINFO_HEAD_IDNAME'] = $this->item->mapFieldToRow('id', $tables[0]);
				$tplvals['TOOLINFO_HEAD_ID'] = $this->item->getId();
				$this->tpl->setCurrentBlock('TOOLINFO_HEAD');
				$this->tpl->setVariable($tplvals);
				$this->tpl->parseCurrentBlock();
				$tplvals = null;
				
				continue;
			}
			
			$row = $this->_buildToolinfoTableRow($item);
			
			if(!empty($row))
			{
				$this->tpl->setCurrentBlock('TOOLINFO_ROW');
				$tplvals['TOOLINFO_ROW_NAME'] = $row['name'];
				$tplvals['TOOLINFO_ROW_VALUE'] = $row['value'];
				$tplvals['TOOLINFO_ROW_ATTRIBUTES'] = $row['attributes'];
				$this->tpl->setVariable($tplvals);
				$this->tpl->parseCurrentBlock();
				$tplvals = null;
			}
		}
		
		$tplvals['TOOLINFO_TABLE_CLASS'] = $action['table_class'];
		$this->tpl->setCurrentBlock('TOOLINFO');
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
		$tplvals = null;
	}

	/**
	 * Generates one row with name, value and attributes for the toolinfo
	 * @param array $item Part of the $action
	 * @return array Returns an array with array('name' => '', 'value' => '', 'attributes' => '')
	 */
	protected function _buildToolinfoTableRow($config_fields)
	{
		$fieldname = $config_fields['fieldname'];
		$row = array();
		$row['name'] = $config_fields['text'];
		$row['attributes'] = $config_fields['attributes_row'];
		
		$values = array();
		if(is_array($fieldname) == TRUE)
		{
			foreach ($fieldname as $field)
			{
				$values[$field] = $this->item->getField($field);
			}
		}
		else
		{
			$values = array($fieldname => $this->item->getField($fieldname));
		}
		
		$row['value'] = $this->_formatValues($config_fields, $values);
		return $row;
	}
}
?>