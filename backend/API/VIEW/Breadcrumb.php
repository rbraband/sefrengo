<?php

$this->includeClass('VIEW', 'AbstractView');

class SF_VIEW_Breadcrumb extends SF_VIEW_AbstractView
{
	/**
	 * Constructor sets the template filename
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->loadTemplatefile('breadcrumb.tpl');
	}
	
	/**
	 * Builds the breadcrumbnavigation from the given array
	 * @param array $crumbs 
	 * @param boolean $linklastcrumb
	 * @return void
	 */
	public function buildBreadcrumbsFromArray($crumbs, $link_last_crumb = TRUE)
	{
		foreach($crumbs as $num => $crumb)
		{
			if(count($crumbs) == $num+1 && $link_last_crumb == FALSE)
			{
				$crumb['url'] = '';
			}
			
			$this->addBreadcrumb($crumb['text'], $crumb['url'], $crumb['attributes']);
			
			if(count($crumbs) > $num+1)
			{
				$this->addDelimiter();
			}
		}
	}
	
	/**
	 * Adds a bread crumb with text and an optional url
	 * @param string $text 
	 * @param string $url 
	 * @return void
	 */
	public function addBreadcrumb($text, $url = '', $attributes = '')
	{
		if($url != '')
		{
			$this->tpl->setCurrentBlock('CRUMB_LINK');
			$tplvals['LINK_URL'] = $url;
			$tplvals['LINK_ATTRIBUTES'] = $this->_addCssClassToAttributes($attributes, 'action');
		}
		else
		{
			$this->tpl->setCurrentBlock('CRUMB_NOLINK');
		}
		$tplvals['LINK_TEXT'] = $text;
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
		$this->tpl->setCurrentBlock('CRUMBS');
		$this->tpl->parseCurrentBlock();
	}
	
	/**
	 * Adds an delimiter
	 * @return void
	 */
	public function addDelimiter()
	{
		$this->tpl->setCurrentBlock('CRUMB_DELIMITER');
		$tplvals['DELIMITER_ATTRIBUTES'] = ' ';
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
		$this->tpl->setCurrentBlock('CRUMBS');
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