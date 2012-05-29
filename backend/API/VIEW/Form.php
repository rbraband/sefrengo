<?php

$this->includeClass('VIEW', 'AbstractView');

class SF_VIEW_Form extends SF_VIEW_AbstractView
{
	protected $formcfg = array(
					'tpl_path' => '',
					'tpl_file' => '',
					'formname' => 'formname',
				);
	
	public function __construct()
	{
		parent::__construct();
		
		$this->loadTemplatefile('form_elements.tpl');
	}
	
	public function buildFromConfigFields($config_fields)
	{
		foreach($config_fields AS $k=>$v)
		{
			if (! array_key_exists('type', $v))
			{
				continue;
			}
			
			switch($v['type'])
			{
				case 'headline':
					$this->addHeadline($v['title']);
					break;
				case 'info':
					$this->addInfo($v['title'], $v['val']);
					break;
				case 'infofield':
					$this->addInfo($v['title'], $v['val']);
					break;
				case 'hidden':
					$this->addHidden($k, $v['val']);
					break;
				case 'text':
					$this->addText($v['title'], $k, $v['val'], $v['attributes']);
					break;
				case 'textarea':
					$this->addTextarea($v['title'], $k, $v['val'], $v['attributes']);
					break;
				case 'checkbox':
					$this->addCheckbox($v['title'], $k, $v['val'], $v['checked'], $v['attributes']);
					break;
				case 'radio':
					$this->addRadio($v['title'], $k, $v['val'], $v['checked'], $v['attributes']);
					break;
				case 'select':
					$this->addSelect($v['title'], $k, $v['val'], $v['selected'], $v['attributes']);
					break;
				case 'datepicker':
					$this->addDatepicker($v['title'], $k, $v['val'], $v['label'], $v['attributes']);
					break;
				case 'cmslink':
					$this->addCmsLink($v['title'], $k, $v['val'], $v['attributes']);
					break;
				case 'directorychooser':
					$this->addDirectoryChooser($v['title'], $k, $v['val'], $v['tree'], $v['attributes']);
					break;
				case 'rightspanel':
					if($v['panel'] != null)
					{
						$this->addRightsPanel($v['title'], $k, $v['val'], $v['panel'], $v['attributes']);
					}
					break;
				case 'editor':
					if($v['editor_instance'] != null && $v['editor_instance']->isEditorAvailable() == TRUE)
					{
						$v['editor_attributes'] = (!array_key_exists('editor_attributes', $v)) ? array() : $v['editor_attributes'];
						
						$this->addEditor($v['title'], $v['editor_instance']->getEditor($v['editor_attributes']), $v['attributes']);
					}
					break;
				case 'actionbuttons':
					$this->addActionButtons($v['title'], $k, $v['buttons'], $v['attributes']);
					break;
			}
		}
	}
	
				
	public function setFormStart($attr = array(), $hidden = array())
	{
		
		$def = array(
						'name' => $this->formcfg['formname'],
						'action' => '',
						'method' => 'post'	
					);
		$attr = array_merge($def, $attr);
		$this->formcfg['formname'] = $attr['name'];
		
		$vals['FORM_START_ATTRIBUTES'] = $this->_attrArray2Str($attr);
		$this->_parseBlock('FORM_START', $vals);
		
		foreach ($hidden AS $k=>$v)
		{
			$this->addHidden($k, $v);
		}
		
		$this->addHidden('gui_form_was_send', 'yes');
	}
	
	public function setFormEnd()
	{
		$this->_touchBlock('FORM_END');
	}
	
	public function addHeadline($title)
	{
		$vals['HEADLINE_TITLE'] = $title;
		$this->_parseBlock('HEADLINE', $vals);
	}
	
	public function addInfo($title, $value)
	{
		$vals['INFO_TITLE'] = $title;
		$vals['INFO_VAL'] = $value;
		$this->_parseBlock('INFO', $vals);
	}
	
	public function addHidden($name, $value)
	{
		$vals['HIDDEN_NAME'] = $name;
		$vals['HIDDEN_ID'] = $name;
		$vals['HIDDEN_VAL'] = $this->_entities($value);
		$this->_parseBlock('HIDDEN', $vals);
	}
	
	public function addText($title, $name, $value, $attr = array())
	{
		$adef = array(
						'size' => '50',
						'maxlength' => '254'
					);
		
		$attr = array_merge($adef, $attr);
		
		$vals['TEXT_TITLE'] = $title;
		$vals['TEXT_NAME'] = $name;
		$vals['TEXT_ID'] = $name;
		$vals['TEXT_VAL'] = $this->_entities($value);
		$vals['TEXT_ATTRIBUTES'] = $this->_attrArray2Str($attr);
		$this->_parseBlock('TEXT', $vals);
	}
	
	public function addTextarea($title, $name, $value, $attr = array())
	{
		$adef = array(
						'cols' => '45',
						'rows' => '3'
					);
		
		$attr = array_merge($adef, $attr);
		
		$vals['TEXTAREA_TITLE'] = $title;
		$vals['TEXTAREA_NAME'] = $name;
		$vals['TEXTAREA_ID'] = $name;
		$vals['TEXTAREA_VAL'] = $this->_entities($value);
		$vals['TEXTAREA_ATTRIBUTES'] = $this->_attrArray2Str($attr);
		$this->_parseBlock('TEXTAREA', $vals);
	}
	
	/*public function addCheckbox($title, $name, $value, $label, $checked = FALSE, $attr = array())
	{
		$adef = array(
						'style' => 'float:left;width:30px;',
					);
		
		$attr = array_merge($adef, $attr);
		
		$vals['CHECKBOX_TITLE'] = $title;
		$vals['CHECKBOX_NAME'] = $name;
		$vals['CHECKBOX_ID'] = $name;
		$vals['CHECKBOX_VAL'] = $this->_entities($value);
		$vals['CHECKBOX_LABEL'] = $label;
		if ($checked)
		{
			$attr['checked'] = 'checked';
		}
		$vals['CHECKBOX_ATTRIBUTES'] = $this->_attrArray2Str($attr);
		$this->_parseBlock('CHECKBOX', $vals);
	}*/
	
	
	public function addCheckbox($title, $name, $fields = array(), $checked = array(), $attr = array())
	{
		if(is_array($checked) == FALSE)
		{
			$firstkey = 0;
			foreach($fields as $key => $val)
			{
				$firstkey = $key;
				break;
			}
			$checked = array($firstkey => $checked);
		}
		
		$vals['CHECKBOX_TITLE'] = $title;
		$vals['CHECKBOX_ID'] = $name;
		$vals['CHECKBOX_ATTRIBUTES'] = $this->_attrArray2Str($attr);

		$i = 0; $addition = (count($fields) > 1) ? '[]' : '';
		foreach($fields AS $k=>$v)
		{
			$field['CHECKBOX_FIELDS_NAME'] = $name.$addition;
			$field['CHECKBOX_FIELDS_ID'] = $name.'_'.$i++;
			$field['CHECKBOX_FIELDS_LABEL'] = $v;
			$field['CHECKBOX_FIELDS_VAL'] = $this->_entities($k);
			$field['CHECKBOX_FIELDS_ATTRIBUTES'] = $vals['CHECKBOX_ATTRIBUTES'];
			$field['CHECKBOX_FIELDS_CHECKED'] = (in_array($k, $checked) && $checked[$k] == TRUE) ? 'checked="checked"' : '';
			$this->_parseBlock('CHECKBOX_FIELDS', $field, FALSE, FALSE);
		}
		
		$this->_parseBlock('CHECKBOX', $vals);
	}
	
	public function addRadio($title, $name, $fields = array(), $checked = '', $attr = array())
	{
		$vals['RADIO_TITLE'] = $title;
		$vals['RADIO_ID'] = $name;
		$vals['RADIO_ATTRIBUTES'] = $this->_attrArray2Str($attr);
		
		$i = 0;
		foreach($fields AS $k=>$v)
		{
			$field['RADIO_FIELDS_NAME'] = $name;
			$field['RADIO_FIELDS_ID'] = $name.'_'.$i++;
			$field['RADIO_FIELDS_LABEL'] = $v;
			$field['RADIO_FIELDS_VAL'] = $this->_entities($k);
			$field['RADIO_FIELDS_ATTRIBUTES'] = $vals['RADIO_ATTRIBUTES'];
			$field['RADIO_FIELDS_CHECKED'] = ($checked != null && $k == $checked) ? 'checked="checked"' : '';
			$this->_parseBlock('RADIO_FIELDS', $field, FALSE, FALSE);
		}
		
		$this->_parseBlock('RADIO', $vals);
	}
	
	public function addSelect($title, $name, $options = array(), $selected = array(), $attr = array())
	{
		// convert string to array
		$selected = (is_string($selected) == TRUE) ? array($selected) : $selected;
		
		$vals['SELECT_TITLE'] = $title;
		$vals['SELECT_NAME'] = (in_array('multiple', $attr) == TRUE) ? $name.'[]' : $name;
		$vals['SELECT_ID'] = $name;
		foreach($options AS $k=>$v)
		{
			$ovals['SELECT_OPTIONS_VAL'] = $this->_entities($k);
			$ovals['SELECT_OPTIONS_TITLE'] = $v;
			$ovals['SELECT_OPTIONS_SELECTED'] = (in_array($k, $selected) == TRUE) ? 'selected="selected"' : '';
			$this->_parseBlock('SELECT_OPTIONS', $ovals, FALSE, FALSE);
		}
		
		$vals['SELECT_ATTRIBUTES'] = $this->_attrArray2Str($attr);
		$this->_parseBlock('SELECT', $vals);
	}

	public function addDatepicker($title, $name, $fields = array(), $label = array(), $attr = array())
	{
		$vals['DATEPICKER_TITLE'] = $title;
		$vals['DATEPICKER_NAME'] = $name;
		$vals['DATEPICKER_ID'] = $name;
		
		foreach($fields AS $k=>$v)
		{
			$field['DATEPICKER_FIELDS_LABEL'] = (array_key_exists($k, $label) == TRUE) ? $label[$k] : '';
			$field['DATEPICKER_FIELDS_NAME'] = $name.'_'.$k;
			$field['DATEPICKER_FIELDS_ID'] = $name.'_'.$k;
			$field['DATEPICKER_FIELDS_VAL'] = $this->_entities($v);
			$field['DATEPICKER_FIELDS_ATTRIBUTES'] = (array_key_exists($k, $attr) == TRUE) ? $this->_attrArray2Str($attr[$k]) : '';
			$this->_parseBlock('DATEPICKER_FIELDS', $field, FALSE, FALSE);
		}

		$this->_parseBlock('DATEPICKER', $vals);
	}
	
	public function addCmsLink($title, $name, $value, $attr = array())
	{
		global $db, $cms_db, $client, $lang;
		
		$adef = array(
						'style' => 'width:600px',
						'readonly' => 'readonly'
					);
		
		$attr = array_merge($adef, $attr);

	    $match = array();
	    if (preg_match_all('#^cms://(idcatside|idcat)=(\d+)$#', $value, $match)) {
	        $content = $value;
	        $is_page = $match['1']['0'] == 'idcatside';
	        $id = $match['2']['0'];
	        $pathway_string = '';
	
	        if ($is_page) {
	            $sql = "SELECT 
						CS.idcat, SL.title
					FROM 
						" . $cms_db['cat_side'] . "  CS 
						LEFT JOIN " . $cms_db['side'] . "  S USING(idside) 
						LEFT JOIN " . $cms_db['side_lang'] . "  SL USING(idside)
					WHERE 
						CS.idcatside = '" . $id . "'
						AND SL.idlang='$lang'";
	            $db->query($sql);
	            if ($db->next_record()) {
	                $pathway_string = $db->f('title');
	                $id = $db->f('idcat');
	            } 
	        } 
	        $control = 0;
	        while ($id > 0 && ++$control < 50) {
	            $sql = "SELECT CL.name, C.parent
						FROM 
							" . $cms_db['cat_lang'] . " CL
							LEFT JOIN " . $cms_db['cat'] . " C USING(idcat)
						WHERE 
							CL.idcat = '" . $id . "'
							AND CL.idlang = '" . $lang . "'
						LIMIT 1";
	            $db->query($sql);
	            if (! $db->next_record()) {
	                break;
	            } 
	            $id = $db->f('parent');
	            $pathway_string = $db->f('name') . '/' . $pathway_string;
	        } 
	
	        $sf_link_intern = htmlentities($pathway_string, ENT_COMPAT, 'UTF-8');
	    } else {
	        $sf_link_intern = '';
	    } 
	
	    $rb = $GLOBALS['sf_factory']->getObjectForced('GUI', 'RessourceBrowser');
	    $res_links = $GLOBALS['sf_factory']->getObjectForced('GUI/RESSOURCES', 'InternalLink');
	    $rb->addRessource($res_links);
	    $rb->setJSCallbackFunction('sf_getLink' . $name, array('picked_name', 'picked_value'));
	    $rb_url = $rb->exportConfigURL();
		
		
		$vals['CMSLINK_TITLE'] = $title;
		$vals['CMSLINK_NAME'] = $name;
		$vals['CMSLINK_ID'] = $name;
		$vals['CMSLINK_VAL'] = $value;
		$vals['CMSLINK_VAL_INTERN'] = $sf_link_intern;
		$vals['CMSLINK_RB_URL'] = $rb_url;
		$vals['CMSLINK_FORMNAME'] = 'edit';
		$vals['CMSLINK_ATTRIBUTES'] = $this->_attrArray2Str($attr);
		
		$this->_parseBlock('CMSLINK', $vals);
	}
	
	public function addDirectoryChooser($title, $name, $value, $tree, $attr = array())
	{
		$vals['DIRECTORYCHOOSER_TITLE'] = $title;
		$vals['DIRECTORYCHOOSER_NAME'] = $name;
		$vals['DIRECTORYCHOOSER_ID'] = $name;
		$vals['DIRECTORYCHOOSER_VAL'] = $value;
		$vals['DIRECTORYCHOOSER_TREE'] = $tree;
		$vals['DIRECTORYCHOOSER_ATTRIBUTES'] = $this->_attrArray2Str($attr);
		$this->_parseBlock('DIRECTORYCHOOSER', $vals);
	}
	
	public function addRightsPanel($title, $name, $value, $panel, $attr = array())
	{
		$vals['RIGHTSPANEL_TITLE'] = $title;
		$vals['RIGHTSPANEL_NAME'] = $name;
		$vals['RIGHTSPANEL_ID'] = $name;
		$vals['RIGHTSPANEL_PANEL'] = $panel;
		$vals['RIGHTSPANEL_ATTRIBUTES'] = $this->_attrArray2Str($attr);
		$this->_parseBlock('RIGHTSPANEL', $vals);
	}
	
	public function addEditor($title, $editor, $attr = array())
	{
		$vals['EDITOR_TITLE'] = $title;
		$vals['EDITOR_EDITOR'] = $editor;
		$vals['EDITOR_ATTRIBUTES'] = $this->_attrArray2Str($attr);
		$this->_parseBlock('EDITOR', $vals);
	}
	
	public function addActionButtons($title, $name, $buttons = array(), $attr = array())
	{
		
		$vals['ACTIONBUTTONS_TITLE'] = $title;
		
		foreach($buttons as $k => $v)
		{
			$block = '';
			switch($v['type'])
			{
				case 'submit': $block = 'SUBMIT_BUTTON'; break;
				case 'reset': $block = 'RESET_BUTTON'; break;
				case 'custom': $block = 'CUSTOM_BUTTON'; break;
			}
			if($block != '')
			{
				$button = array();
				$button[$block.'_NAME'] = $v['name'];
				$button[$block.'_TEXT'] = $v['text'];
				$button[$block.'_VAL'] = $v['val'];
				$button[$block.'_ATTRIBUTES'] = $this->_attrArray2Str($v['attributes']);
				$this->_parseBlock($block, $button, FALSE, FALSE);
			}
		}
		
		$vals['ACTIONBUTTONS_ATTRIBUTES'] = $this->_attrArray2Str($attr);
		$this->_parseBlock('ACTIONBUTTONS', $vals);
	}
	
	public function addButtonTripple($vals = array())
	{
		$def = array(
						'BUTTON_TRIPPLE_SUBMIT_VALUE' => $this->lng->get('gen_save'),
						'BUTTON_TRIPPLE_APPLY_VALUE' => $this->lng->get('gen_apply'),
						'BUTTON_TRIPPLE_CANCEL_VALUE' => $this->lng->get('gen_cancel'),
						'BUTTON_TRIPPLE_SUBMIT_TEXT' => $this->lng->get('gen_save'),
						'BUTTON_TRIPPLE_APPLY_TEXT' => $this->lng->get('gen_apply'),
						'BUTTON_TRIPPLE_CANCEL_TEXT' => $this->lng->get('gen_cancel'),
						'BUTTON_CANCEL_URL' => '',
						
					);
					
		$vals = array_merge($def, $vals);
		$this->_parseBlock('BUTTON_TRIPPLE', $vals);
	}
	
	public function wasSend()
	{
		return (isset($_REQUEST['gui_form_was_send']) && $_REQUEST['gui_form_was_send'] == 'yes');
	}
	
	public function applyWasPressed()
	{
		return (isset($_REQUEST['apply']) );
	}
	
	public function saveWasPressed()
	{
		return (isset($_REQUEST['save']) );
	}
	
	protected function _attrArray2Str($attr = array())
	{
		$html = sf_api('LIB', 'HtmlHelper');
		return $html->attributesArrayToHtmlString($attr);
	}
	
	protected function _parseBlock($block, $vals, $clearing = TRUE, $collect = TRUE)
	{
		$this->tpl->setCurrentBlock($block);
		$this->tpl->setVariable($vals);
		$this->tpl->parseCurrentBlock();
		
		if ($collect)
		{
			$this->generated_view .= $this->tpl->get($block);
		}
		
		if ($clearing)
		{
			$this->tpl->blockdata[$block] = '';
		} 
	}
	
	protected function _touchBlock($block)
	{
		$this->generated_view .= $this->tpl->blocklist[$block];
	}
	
	protected function _entities($val)
	{
		return htmlentities($val, ENT_COMPAT, 'UTF-8');	
	}
	
	public function generate()
	{
		return TRUE;
	}
}

?>