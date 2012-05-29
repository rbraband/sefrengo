<?php

$this->includeClass('VIEW', 'AbstractView');

class SF_VIEW_Table extends SF_VIEW_AbstractView
{
	/**
	 * Set the URL Builder and overwrite
	 * the standard URL builder (set in AbstractView::__construct)
	 * @param SF_LIB_UrlBuilder $urlbuilder
	 * @return void
	 */
	public function setUrlBuilder($urlbuilder)
	{
		$this->url = $urlbuilder;
	}
	
	/**
	 * Set attributes for the table
	 * @param string $attributes
	 * @return void
	 */
	public function setTableAttributes($attributes)
	{
		$tplvals['TABLE_ATTRIBUTES'] = $attributes;
		$this->tpl->setVariable($tplvals); 
	}
	
	/**
	 * Build the pager row and add it to the table.
	 * If $pager_options is an empty array no pager is generated.
	 * If $filter_options is an empty array no filter reset button is generated. 
	 * @param string $position (top or bottom)
	 * @param array $pager_options
	 * @param array $filter_options
	 * @return void
	 */
	public function buildPager($position, $pager_options = array(), $filter_options = array())
	{
		if(count($filter_options) > 0 && count($filter_options['filters']) > 0)
		{
			$this->tpl->setCurrentBlock('FILTER_RESET');
			
			$tmp['LANG_FILTER_SHOW'] = $filter_options['lang_filter_show'];
			$tmp['LANG_FILTER_RESET'] = $filter_options['lang_filter_reset'];
			$tmp['RESET_URL'] = $filter_options['reset_url'];
			$tmp['SET_FILTERS'] = implode("; ", $filter_options['filters']);
			
			$this->tpl->setVariable($tmp);
			$this->tpl->parseCurrentBlock();
			unset($tmp);
		}
		
		if( (array_key_exists('colspan', $pager_options) == TRUE && count($pager_options) > 1) ||
			(array_key_exists('colspan', $pager_options) == FALSE && count($pager_options) > 0))
		{
			$this->tpl->setCurrentBlock('PAGER');
			
			$pager = sf_api('VIEW', 'Pager');
			$pager->setTotalItems($pager_options['count_all']);
			$pager->setItemsPerPage($pager_options['items_per_page']);
			$pager->setDelta($pager_options['delta_for_pager']);
			$pager->setCurrentPage($pager_options['current_page']);
			$pager->setExecludeVars($pager_options['exclude_vars']);
			$pager->setLinkClass($pager_options['link_class']);
			$pager->generate();
			
			$tmp['LANG_PAGE'] = $pager_options['lang_page'];
			$tmp['LANG_FROM'] = $pager_options['lang_from'];
			$tmp['PAGER_LINKS'] = $pager->getLinks();
			$tmp['CHANGEPAGE_CURRENT'] = $pager_options['current_page'];
			$tmp['CHANGEPAGE_MAX'] = $pager->getCountPages();
			
			$this->tpl->setVariable($tmp);
			$this->tpl->parseCurrentBlock();
		}
		unset($tmp);
		
		if($position == 'top')
		{
			$this->tpl->setCurrentBlock('PAGER_TOP');
			$tmp['PAGER_TOP_COLSPAN'] = $pager_options['colspan'];
			$this->tpl->setVariable($tmp);
		}
		else
		{
			$this->tpl->setCurrentBlock('PAGER_BOTTOM');
			$tmp['PAGER_BOTTOM_COLSPAN'] = $pager_options['colspan'];
			$this->tpl->setVariable($tmp);
		}
		$this->tpl->parseCurrentBlock();
	}
	
	
	/**
	 * Build tablehead for the indexpage
	 * @param array $field configarray
	 * @param array $order
	 * @return void
	 */
	public function buildTableHead($field, $order)
	{
		//build tablehead
		$sep_head = '';
		$sep_body = '';
		foreach ($field AS $item)
		{
			if (! array_key_exists('fieldname', $item) || ! is_array($item['fieldname']))
			{
				continue;
			}
			
			$sep_h = array_key_exists('multifieldseparator_head', $item) ? $item['multifieldseparator_head']: $sep_head;
			$sep_b = array_key_exists('multifieldseparator_head', $item) ? $item['multifieldseparator_head']: $sep_body;
			
			$max = count($item['fieldname']);
			$i=0;
			foreach ($item['fieldname'] AS $k=>$v)
			{
				++$i;
				
				if($this->lng->get($item['lang_head'][$k]) != "")
				{
					$tplvals['TABLE_HEAD_TITLE'] = $this->lng->get($item['lang_head'][$k]);
				}
				else
				{
					$tplvals['TABLE_HEAD_TITLE'] = $item['lang_head'][$k];
				}
				
				$tplvals['TABLE_HEAD_SEPARATOR'] = '';
				if ($i < $max)
				{
					$tplvals['TABLE_HEAD_SEPARATOR'] = $sep_h;
				}
				
				if($item['lang_head'][$k] == '' || $v == '')
				{
					$this->tpl->setCurrentBlock('TABLE_HEAD_NAME');
				}
				else
				{
					if($order['order'] == $v) 
					{
						$tplvals['TABLE_HEAD_ORDER'] = strtolower($order['ascdesc']);
					}
					
					if($order['ascdesc'] == '')
					{
						$orderfield = $v;
						$orderdir = 'ASC';
					}
					else if(strtoupper($order['ascdesc']) == 'ASC')
					{
						$orderfield = $v;
						$orderdir = 'DESC';
					}
					else
					{
						$orderfield = '';
						$orderdir = '';
					}
					
					$tplvals['TABLE_HEAD_URL'] = $this->url->urlGet(array('order' => $orderfield, 'ascdesc' => $orderdir));
					
					$this->tpl->setCurrentBlock('TABLE_HEAD_LINK');
				}
				
				$this->tpl->setVariable($tplvals);
				$this->tpl->parseCurrentBlock();
				$tplvals = null;
			}
			
			$this->tpl->setCurrentBlock('TABLE_HEAD_COLUMN');
			$tplvals['TABLE_HEAD_ATTRIBUTES'] = $item['attributes_head'];
			$this->tpl->setVariable($tplvals);
			$this->tpl->parseCurrentBlock();
			$tplvals = null;
		}
		
		$this->tpl->setCurrentBlock('TABLE_HEAD');
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Build tablebody for the indexpage
	 * @param SF_INTERFACE_Collection $collection
	 * @param array $field
	 * @param array $options
	 * @return void
	 */
	public function buildTableBody($collection, $field, $options = array())
	{
		$iter = $collection->getItemsAsIterator();
		while($iter->valid())
		{
			$itemobject = $iter->current();
			
			$this->buildTableRow($itemobject, $field, $options);
			
			// add extra row for detail view
			if(array_key_exists('viewtype', $options) && $options['viewtype'] == 'detail')
			{
				$this->buildTableRowDetail($itemobject, $field, $options);
			}
			
			$this->tpl->setCurrentBlock('ENTRIES');
			$this->tpl->parseCurrentBlock();
			
			$iter->next();
		}
	}
	
	/**
	 * Build one table row with data from $itemobject and
	 * table cells by $fields. 
	 * @param SF_INTERFACE_SqlItem $itemobject Get the table data
	 * @param array $field Fiels as array. Every field represents a table cell
	 * @param array $options
	 * @return boolean Returns TRUE if row is added to table. Otherwise returns FALSE.
	 */
	public function buildTableRow($itemobject, $config_fields, $options = array())
	{
		// check object perm -> should be checked by fetching objects in the collecion
		/*if(array_key_exists('objectperm', $field) && is_array($field))
		{
			// 'perm_or_area' and 'type' are mandatory if 'objectperm' array is set
			if(!array_key_exists('perm_or_area', $field['objectperm']) || !array_key_exists('type', $field['objectperm']))
			{
				return FALSE;
			}
			
			$field['objectperm']['id'] = (array_key_exists('id', $field['objectperm'])) ? (string)$field['objectperm']['id'] : '0';
			$field['objectperm']['parent_id'] = (array_key_exists('parent_id', $field['objectperm'])) ? (int)$field['objectperm']['parent_id'] : 0;
			
			// if no permission, return nothing
			if($this->cfg->perm()->have_perm($field['objectperm']['perm_or_area'], $field['objectperm']['type'], $field['objectperm']['id'], $field['objectperm']['parent_id']) == FALSE)
			{
				return FALSE;
			}
		}*/
		
		// build tablerow for body
		foreach ($config_fields AS $config_field => $config)
		{
			++$cellnr;
			if (! array_key_exists('fieldname', $config) || ! is_array($config['fieldname']))
			{
				continue;
			}
			
			// apply table cell renderer (tcr)
			if (sf_api_exists('VIEW', $config['renderer']['classname']) == TRUE)
			{
				$renderer = sf_api('VIEW', $config['renderer']['classname']);
			}
			else if (sf_api_exists('VIEW', 'TableCellRenderer') == TRUE)
			{
				$renderer = sf_api('VIEW', 'TableCellRenderer');
			}

			// check if TableCellRenderer instance
			if(($renderer instanceof SF_INTERFACE_TableCellRenderer) == FALSE)
			{
				continue;
			}
			
			$renderer->setCurrentConfigField($config_field);
			$renderer->setRendererConfig($config['renderer']);
			$renderer->setConfigFields($config);
			$renderer->setItem($itemobject);
			
			$this->tpl->setCurrentBlock('TABLE_BODY_COLUMN');
			$this->tpl->setVariable('TABLE_BODY_ATTRIBUTES', $config['attributes_body']);
			$this->tpl->setVariable('TABLE_BODY_CONTENT', $renderer->get());
			$this->tpl->parseCurrentBlock();
		}
		
		$this->tpl->setCurrentBlock('ENTRY');
		$tmp['TR_ATTRIBUTES'] = $options['tr_attributes'];
		$this->tpl->setVariable($tmp);
		$this->tpl->parseCurrentBlock();
		unset($tmp);
		
		return TRUE;
	}
	
	/**
	 * Build one table row with detailed data from $itemobject.
	 * Note: This needs an specific implementation in the child class of this Table class.
	 * @param SF_INTERFACE_SqlItem $itemobject Get the table data
	 * @param array $field Fiels as array. Every field represents a table cell
	 * @param array $options
	 * @return boolean Returns TRUE if row is added to table. Otherwise returns FALSE.
	 */
	public function buildTableRowDetail($itemobject, $field, $options = array())
	{
		// Implement the functionality in child class
		return TRUE;
	}
	
	/**
	 * Parse the table row if no data is set
	 * @param array $options
	 * @return void
	 */
	public function buildTableRowNoData($options)
	{
		$this->tpl->setCurrentBlock('EMPTY');
		$tmp['COLSPAN'] = $options['colspan'];
		$tmp['LANG_NODATA'] = $options['nodata'];
		$this->tpl->setVariable($tmp);
		$this->tpl->parse('EMPTY');
		unset($tmp);
	}
	
	/**
	 * Builds and adds the footer with the links to select all or none
	 * checkboxes and the multiple actions.
	 * @param array $actions
	 * @param array $options
	 * @return void
	 */
	public function buildTableFooterSelectMultiple($actions, $options)
	{
		$this->tpl->setCurrentBlock('FOOTER_SELECT_MULTIPLE');
		$tmp['COLSPAN'] = $options['colspan'];
		$tmp['LANG_SELECT'] = $options['select'];
		$tmp['SELECT_ALL'] = $options['select_all']['chk_name'];
		$tmp['LANG_SELECT_ALL'] = $options['select_all']['lang'];
		$tmp['SELECT_NONE'] = $options['select_none']['chk_name'];
		$tmp['LANG_SELECT_NONE'] = $options['select_none']['lang'];
		$tmp['TABLE_BODY_ATTRIBUTES'] = $actions['attributes_body'];
		
		// apply table cell renderer (tcr)
		if(array_key_exists('renderer', $actions) && array_key_exists('classname', $actions['renderer']))
		{
			if (sf_api_exists('VIEW', $actions['renderer']['classname']) == TRUE)
			{
				$renderer = sf_api('VIEW', $actions['renderer']['classname']);
				$renderer->setRendererConfig($actions['renderer']);
				$renderer->setConfigFields($actions);
				
				$tmp['ACTIONS'] = $renderer->get();
			}
			else if (sf_api_exists('VIEW', 'TableCellRenderer') == TRUE)
			{
				$renderer = sf_api('VIEW', 'TableCellRenderer');
				$renderer->setRendererConfig($item['renderer']);
				$renderer->setConfigFields($item);
				
				$tmp['ACTIONS'] = $renderer->get();
			}
			else
			{
				$tmp['ACTIONS'] = '';
			}
		}
		
		$this->tpl->setVariable($tmp);
		$this->tpl->parse('FOOTER_SELECT_MULTIPLE');
		unset($tmp);
	}
	
	/**
	 * Set the generated template.
	 * @see API/VIEW/SF_VIEW_AbstractView#generate()
	 * @return boolean
	 */
	public function generate()
	{
		$this->generated_view = $this->tpl->get();
		return TRUE;
	}
}

?>