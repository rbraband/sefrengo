<?php

$this->includeClass('VIEW', 'AbstractView');

class SF_VIEW_Toolbar extends SF_VIEW_AbstractView
{
	/**
	 * Constructor sets the template filename
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->loadTemplatefile('toolbar.tpl');
	}
	
	/**
	 * Builds the whole toolbar from the given array
	 * @param array $toolbar
	 * @return void
	 */
	public function buildToolbarFromArray($toolbar)
	{
		$need_form_end = FALSE;
		foreach($toolbar as $action)
		{
			if(array_key_exists('type', $action) == FALSE)
			{
				continue;
			}
			
			// check perm: 'has_perm' is mandatory if 'perm' array is set
			if( array_key_exists('perm', $action) && is_array($action['perm']) &&
				(array_key_exists('has_perm', $action['perm']) == FALSE || $action['perm']['has_perm'] == FALSE)
			  )
			{
				continue;
			}
			
			switch($action['type'])
			{
				case 'search':
					$this->addSearchField($action['name'], $action['value'], $action['attributes']);
					break;
				case 'adv_search':
					$this->addAdvSearchField($action['name'], $action['value'], $action['attributes'], $action['tabs']);
					break;
				case 'actionbox':
					$this->addActionbox($action['name'], $action['values'], $action['onchange_confirm'], $action['attributes']);
					break;
				case 'hidden':
					$this->addHiddenField($action['name'], $action['value'], $action['attributes']);
					break;
				case 'icon':
					$this->addIcon($action['icon'], $action['text'], $action['url'], $action['attributes']);
					break;
				case 'link':
					$this->addLink($action['text'], $action['url'], $action['icon'], $action['attributes']);
					break;
				case 'text':
					$this->addText($action['text'], $action['icon'], $action['attributes']);
					break;
				case 'delimiter':
					$this->addDelimiter($action['attributes']);
					break;
				case 'form':
					$this->addFormStart($action['action']);
					$need_form_end = TRUE;
					break;
			}
		}
		if($need_form_end == TRUE)
		{
			$this->addFormEnd();
		}
	}
	
	/**
	 * Adds the formular start tag with action paramter
	 * @param $action
	 * @return void
	 */
	public function addFormStart($action)
	{
		$this->tpl->setCurrentBlock('FORM_START');
		$tplvals['FORM_ACTION'] = $action;
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Adds the formular end tag
	 * @param $action
	 * @return void
	 */
	public function addFormEnd()
	{
		$this->tpl->setCurrentBlock('FORM_END');
		$tplvals['DUMMY'] = "dummy";
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Adds a search field 
	 * @param string $name
	 * @param string $value
	 * @param string $attributes
	 * @return void
	 */
	public function addSearchField($name, $value = '', $attributes = '')
	{
		$this->tpl->setCurrentBlock('SEARCHFIELD');
		$tplvals['NAME'] = $name;
		$tplvals['VALUE'] = $value;
		$tplvals['ATTRIBUTES'] = $attributes;
		$tplvals['SEARCH'] = $this->lng->get('gen_search');
		$tplvals['SEARCHTERMS'] = $this->lng->get('gen_searchterms');
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
		$this->tpl->setCurrentBlock('ACTION');
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Adds an advanced search field 
	 * @param string $name
	 * @param string $value
	 * @param string $attributes
	 * @return void
	 */
	public function addAdvSearchField($name, $value = '', $attributes = '', $tabs = array())
	{
		$this->tpl->setCurrentBlock('ADV_SEARCHFIELD');
		$tplvals['NAME'] = $name;
		$tplvals['VALUE'] = $value;
		$tplvals['ATTRIBUTES'] = $attributes;
		$tplvals['SEARCH'] = $this->lng->get('gen_search');
		$tplvals['SEARCHTERMS'] = $this->lng->get('gen_searchterms');
		$tplvals['ADVANCED_SEARCH'] = $this->lng->get('gen_advanced_search');
		
		$form = sf_api('VIEW', 'Form');
		$form->loadTemplatefile('form_elements_advsearch.tpl');
		$form->buildFromConfigFields($tabs, 'logs');
		$tplvals['TABS'] = $form->get();

		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
		$this->tpl->setCurrentBlock('ACTION');
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Adds a actionbox that submits the formular on select
	 * @param string $name 
	 * @param array $values array with value, text
	 * @param string $onchange_confirm Question prompt before sending form
	 * @param string $attributes
	 * @return void
	 */
	public function addActionbox($name, $values, $onchange_confirm = '', $attributes = '')
	{
		foreach($values as $value => $text)
		{
			$this->tpl->setCurrentBlock('ACTIONBOX_OPTION');
			$tplvals['TEXT'] = $text;
			$tplvals['VALUE'] = $value;
			$this->tpl->setVariable($tplvals);
			$this->tpl->parseCurrentBlock();
			$tplvals = null;
		}
		
		$this->tpl->setCurrentBlock('ACTIONBOX');
		$tplvals['NAME'] = $name;
		$tplvals['ONCHANGE_CONFIRM'] = $onchange_confirm;
		$tplvals['ATTRIBUTES'] = $attributes;
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
		$this->tpl->setCurrentBlock('ACTION');
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Adds a hidden field 
	 * @param string $name 
	 * @param string $value 
	 * @param string $attributes
	 * @return void
	 */
	public function addHiddenField($name, $value = '', $attributes = '')
	{
		$this->tpl->setCurrentBlock('HIDDENFIELD');
		$tplvals['NAME'] = $name;
		$tplvals['VALUE'] = $value;
		$tplvals['ATTRIBUTES'] = $attributes;
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
		$this->tpl->setCurrentBlock('ACTION');
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Adds an icon. If $url is empty an icon without link is generated.
	 * @param string $icon 
	 * @param string $text 
	 * @param string $url
	 * @param string $attributes
	 * @return void
	 */
	public function addIcon($icon, $text, $url = '', $attributes = '')
	{
		if($url == '')
		{
			$this->tpl->setCurrentBlock('ICON_NOLINK');
		}
		else
		{
			$this->tpl->setCurrentBlock('ICON_LINKED');
			$tplvals['URL'] = $url;
		}
		
		$tplvals['IMGPATH'] = $this->getSkinImgPath();
		$tplvals['SRC'] = $icon;
		$tplvals['TEXT'] = $text;
		$tplvals['ATTRIBUTES'] = $this->_addCssClassToAttributes($attributes, 'action');
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
		$this->tpl->setCurrentBlock('ACTION');
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Adds a link.
	 * @param string $url 
	 * @param string $text 
	 * @param string $icon 
	 * @param string $attributes
	 * @return void
	 */
	public function addLink($text, $url, $icon = '', $attributes = '')
	{
		if($icon == '')
		{
			$this->tpl->setCurrentBlock('LINK');
		}
		else
		{
			$this->tpl->setCurrentBlock('LINK_ICON');
			$tplvals['IMGPATH'] = $this->getSkinImgPath();
			$tplvals['SRC'] = $icon;
		}
		$tplvals['URL'] = $url;
		$tplvals['TEXT'] = $text;
		$tplvals['ATTRIBUTES'] = $this->_addCssClassToAttributes($attributes, 'action');
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
		$this->tpl->setCurrentBlock('ACTION');
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Adds a text.
	 * @param string $url
	 * @param string $icon
	 * @param string $attributes
	 * @return void
	 */
	public function addText($text, $icon = '', $attributes = '')
	{
		if($icon == '')
		{
			$this->tpl->setCurrentBlock('TEXT');
		}
		else
		{
			$this->tpl->setCurrentBlock('TEXT_ICON');
			$tplvals['IMGPATH'] = $this->getSkinImgPath();
			$tplvals['SRC'] = $icon;
		}
		$tplvals['TEXT'] = $text;
		$tplvals['ATTRIBUTES'] = $attributes;
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
		$this->tpl->setCurrentBlock('ACTION');
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Adds an delimiter
	 * @param string $attributes
	 * @return void
	 */
	public function addDelimiter($attributes = ' ')
	{
		$this->tpl->setCurrentBlock('DELIMITER');
		$tplvals['ATTRIBUTES'] = (empty($attributes)) ? ' ' : $attributes;
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
		$this->tpl->setCurrentBlock('ACTION');
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Set the generated template.
	 * @see API/VIEWS/SF_VIEW_AbstractView#generate()
	 * @return boolean
	 */
	public function generate()
	{
		$this->generated_view = $this->tpl->get();
		return TRUE;
	}
	
	
	/**
	 * Adds an cssclass to the class attribute
	 * @param string $attributes
	 * @param string $cssclass
	 * @return string Returns the modified attributes parameter
	 */
	protected function _addCssClassToAttributes($attributes, $cssclass)
	{
		if($attributes != '')
		{
			if(strpos($attributes, 'class=') === FALSE)
			{
				$attributes .= ' class="'.$cssclass.'"';
			}
			else
			{
				$attributes = str_replace('class="', 'class="'.$cssclass.' ', $attributes);
			}
		}
		else
		{
			$attributes = ' class="'.$cssclass.'"';
		}
		
		return $attributes;
	}
}
?>