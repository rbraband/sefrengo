<?php

abstract class SF_CONTROLLER_AbstractArea extends SF_LIB_ApiObject
{
	/**
	 * Logger
	 * @var SF_LIB_Logger
	 */
	protected $log;
	
	/**
	 * WebRequest
	 * @var SF_LIB_WebRequest
	 */
	protected $req;
	
	/**
	 * Configuration object
	 * @var SF_LIB_Config
	 */
	protected $cfg;
	
	/**
	 * Language Strings
	 * @var SF_LIB_Lang
	 */
	protected $lng;
	
	/**
	 * URL Builder
	 * @var SF_LIB_UrlBuilder
	 */
	protected $url;
	
	/**
	 * HTTP Header (for redirect)
	 * @var SF_LIB_HttpHeader
	 */
	protected $http_header;
	
	/**
	 * Configuration of the controller
	 * @var array
	 */
	protected $controller_cfg = array(
		'ctr_name' => '',
		'ctr_fnc_called' => '',
		
		'cms_basefile' => 'main.php',
		
		'session_variable_name' => 'var2session'
	);
	
	/**
	 * Constructor sets up {@link $req}, {@link $cfg}, {@link lng}, {@link url}
	 * @return void
	 */
	public function __construct()
	{
		// init objects
		$this->log = sf_api('LIB', 'Logger');
		$this->req = sf_api('LIB', 'WebRequest');
		$this->cfg = sf_api('LIB', 'Config');
		$this->lng = sf_api('LIB', 'Lang');
		$this->url = sf_api('LIB', 'UrlBuilder');
		$this->http_header = sf_api('LIB', 'HttpHeader');
	}
	
	/**
	 * Default index function that has to be defined in the subclass
	 * @return void
	 */
	abstract public function index();
	
	/**
	 * Store controllername and called function in {@link $controller_cfg}.
	 * @param string $controllername
	 * @param string $controller_function_called
	 * @return void
	 */
	public function setInitArea($controllername, $controller_function_called)
	{
		$this->controller_cfg['ctr_name'] =  strtolower($controllername);
		$this->controller_cfg['ctr_fnc_called'] =  strtolower($controller_function_called);
	}
	
	/**
	 * Add area to {@link url} parameter
	 * @return void
	 */
	public function initControllerCall()
	{
		//set subarea to linktpl
		$this->url->urlAddModifyParams(array('area' => $this->controller_cfg['ctr_name'].'_'.$this->controller_cfg['ctr_fnc_called']));
	}
	
	/**
	 * Iterate through $config_fields and set the matched values from $item.
	 * @global $sf_factory API-Factory to instanciate the editors
	 * @param array $config_fields Configuration of all fields as array 
	 * @param SF_INTERFACE_Item $item Item that uses the interface
	 * @param boolean $form_was_send
	 * @return array Returns the given $config_fields with added values.
	 */
	protected function _assignValuesToConfigFields($config_fields, $item, $form_was_send = FALSE)
	{
		global $sf_factory;
			
		//get form vals
		foreach($config_fields AS $k=>$v)
		{
			//show if type exists
			if (! array_key_exists('type', $v))
			{
				continue;
			}
			
			//filter type
			switch($v['type'])
			{
				case 'headline':
				case 'info':
					continue;
					break;
				case 'hidden':
				case 'text':
				case 'textarea':
				case 'cmslink':
				case 'directorychooser':
					$item_val = ($item === null) ? '' : $this->req->req($k, $item->getField($k));
					if ($item_val == '')
					{
						continue;
					}
					$config_fields[$k]['val'] = $item_val;
					break;
				case 'infofield':
					if($v['format'] == '')
					{
						$item_val = ($item === null) ? '' : $this->req->req($k, $item->getField($k));
					}
					else if($item !== null && $item->getField($k) != '')
					{
						// format created and lastmodified
						switch($k)
						{
							case 'created':
								$date = $item->getCreatedDate();
								$time = $item->getCreatedTime();
								$author = $item->getCreatedAuthor('', 'username');
								break;
							case 'lastmodified':
								$date = $item->getLastmodifiedDate();
								$time = $item->getLastmodifiedTime();
								$author = $item->getLastmodifiedAuthor('', 'username');
								break;
							default:
								$author = $time = $date = $this->req->req($k, $item->getField($k));
								break;
						}
						$item_val = str_replace('{date}', $date, $v['format']);
						$item_val = str_replace('{time}', $time, $item_val);
						$item_val = str_replace('{author}', $author, $item_val);
					}
					
					if ($item_val == '')
					{
						continue;
					}
					$config_fields[$k]['val'] = $item_val;
					break;
				// TODO This works just with one checkbox per line at the moment. Extend it the assign behavior to use multiple checkboxes. Remove this todo afterwards.
				// Note: Multiple checkboxes are already implemented in the API/VIEWS/Form.php and form_elements.tpl
				case 'checkbox':
					if ($form_was_send == TRUE && $this->req->req($k, FALSE) !== FALSE)
					{
						$config_fields[$k]['checked'] = array_key_exists($this->req->req($k), $config_fields[$k]['val']);
					}
					else
					{
						if ($item !== null && array_key_exists($item->getField($k), $config_fields[$k]['val']) == TRUE)
						{
							$config_fields[$k]['checked'] = TRUE;
						}
						else
						{
							$config_fields[$k]['checked'] = FALSE;
						}
					}
					break;
				case 'radio':
					if ($form_was_send && $this->req->req($k, FALSE) !== FALSE)
					{
						$config_fields[$k]['checked'] = ($this->req->req($k) == $config_fields[$k]['val']);
					}
					else
					{
						if ($item !== null && $item->getField($k) == $config_fields[$k]['val'])
						{
							$config_fields[$k]['checked'] = TRUE;
						}
						else
						{
							$config_fields[$k]['checked'] = FALSE;
						}
					}
					break;
				// TODO This works just with single selects at the moment. Extend it the assign behavior to use multiple selectes. Remove this todo afterwards.
				// Note: Multiple selects are already implemented in the API/VIEWS/Form.php and form_elements.tpl
				case 'select':
					$item_val = ($item === null) ? '' : $this->req->req($k, $item->getField($k));
					if ($item_val == '')
					{
						continue;
					}
					$config_fields[$k]['selected'] = $item_val;
					break;
					
				case 'rightspanel':
					$config = (array_key_exists('panel_config', $config_fields[$k])) ? $config_fields[$k]['panel_config'] : array();
					// formname is set in function _buildFormFromConfigFields()
					$config['formname'] = 'edit';
					
					$view = (array_key_exists('panel_view', $config_fields[$k])) ? $config_fields[$k]['panel_view'] : 'text';
					
					$panel_arr = $this->cfg->perm()->get_right_panel($item->getObjectPermType(), $item->getId(), $config, $view);
					// show panel only if generated and editing the item 
					if(empty($panel_arr) == FALSE && $item->getId() > 0)
					{
						$config_fields[$k]['panel'] = implode('', $panel_arr);
					}
					else
					{
						$config_fields[$k]['panel'] = null;
					}
					
					unset($config, $view, $panel_arr);
					break;
					
				case 'editor':
					// create editor if exists, store instance and set item if allowed
					if( $item !== null && array_key_exists('editor_type', $config_fields[$k]) &&
						$config_fields[$k]['editor_instance'] == null &&
						$sf_factory->classExists('VIEW', $config_fields[$k]['editor_type']))
					{
						$config_fields[$k]['editor_instance'] = sf_api('VIEW', $config_fields[$k]['editor_type']);
						
						// the editor must be an item editor (that deals only with one item)
						if($config_fields[$k]['editor_instance'] instanceof SF_INTERFACE_ItemEditor)
						{
							$config_fields[$k]['editor_config'] = (!array_key_exists('editor_config', $config_fields[$k])) ? array() : $config_fields[$k]['editor_config'];
							$config_fields[$k]['editor_instance']->setEditorName($k);
							$config_fields[$k]['editor_instance']->setConfig($config_fields[$k]['editor_config']);
							
							if($config_fields[$k]['editor_instance']->isItemAllowed($item))
							{
								$config_fields[$k]['editor_instance']->setItem($item);
							}
						}
						else
						{
							$config_fields[$k]['editor_instance'] = null;
						}
					}
					else
					{
						$config_fields[$k]['editor_instance'] = null;
					}
					
					break;
			}	
		}
		
		return $config_fields;
	}
	
	
	/**
	 * Iterate through $config_fields and checks given validation clauses for every field.
	 * If validation fails return the error string.
	 * @param array $config_fields Configuration of all fields as array 
	 * @return string Error string set in $config_fields
	 */
	protected function _validateConfigFields($config_fields)
	{
		$validator = sf_factoryGetObject('LIB', 'Validation');
		$error_string = '';
		
		foreach($config_fields AS $k=>$v)
		{
			//show if type exists
			if (! array_key_exists('type', $v) || ! array_key_exists('validation', $v))
			{
				continue;
			}
			
			//filter type
			switch($v['type'])
			{
				case 'headline':
				case 'info':
					continue(2);
					break;
			}
			
			//assign value to check from formtype
			$item_val = '';
			switch($v['type'])
			{
				case 'infofield':
				case 'hidden':
				case 'text':
				case 'textarea':
				case 'cmslink':
					$item_val = $config_fields[$k]['val'];
					break;
				case 'checkbox':
					$item_val = ($v['checked']) ? $config_fields[$k]['val']:'';
					break;
				case 'select':
					$item_val = $config_fields[$k]['selected'];
					break;
				case 'editor':
					if($config_fields[$k]['editor_instance'] != null && $config_fields[$k]['editor_instance']->isEditorAvailable() == TRUE)
					{
						$item_val = $config_fields[$k]['editor_instance']->getValidationValue();
					}
					break;
			}
			
			$v['title_replaced_lng'] = $this->lng->replaceLangInString($v['title'], $this->controller_cfg['ctr_name']); 
			
			//run validation
			foreach ($v['validation'] AS $k2=>$v2)
			{
				$v2['note_replaced_lng'] = $this->lng->replaceLangInString($v2['note'], $this->controller_cfg['ctr_name']); 
				
				switch ($k2)
				{
					case 'required':
						if (! $validator->required($item_val))
						{
							$error_string .= $v['title_replaced_lng'] . ': ' . $v2['note_replaced_lng'] . "<br />\n";
							break(2);
						}
					default:
						if (method_exists($validator, $k2))
						{
							if (array_key_exists('val', $v2))
							{
								if (! $validator->orEmpty($k2, $item_val, $v2['val']))
								{
									$error_string .= $v['title_replaced_lng'] . ': ' . $v2['note_replaced_lng'] . "<br />\n";
									break(2);
								}
							}
							else
							{
								if (! $validator->orEmpty($k2, $item_val))
								{
									$error_string .= $v['title_replaced_lng'] . ': ' . $v2['note_replaced_lng'] . "<br />\n";
									break(2);
								}
							}
						}
				}
			}	
		}
		
		return $error_string;
	}
	
	/**
	 * Iterate through $config_fields and build the formular in the given $form object.
	 * @param array $config_fields Configuration of all fields as array 
	 * @param SF_VIEW_Form $form Form object
	 * @return void
	 */
	protected function _buildFormFromConfigFields($config_fields, $form)
	{
		// add language variables
		$config_fields = $this->_replaceTemplateVarsInConfigFields($config_fields);
		
		$params = $this->url->urlGetParams();
		$params = $this->_manipulateLink('form_submit_hidden_parms', $params);
		$form->setFormStart(
			array(
				'action' => $this->url->urlGet(
					array(),
					array('params_skip')
				),
				'name' => 'edit'
			),
			$params
		);
		
		$form->buildFromConfigFields($config_fields);
		
		// search for action buttons
		$has_actionbutton = FALSE;
		
		foreach($config_fields as $config_field)
		{
			if( array_key_exists('type', $config_field) && $config_field['type'] == 'actionbuttons')
			{
				$has_actionbutton = TRUE;
			}
		}
		
		// add button tripple if no custom actionbuttons defined
		if($has_actionbutton == FALSE)
		{
			$params = array(
				'area' => $this->controller_cfg['ctr_name'].'_index'
			);
			$params = $this->_manipulateLink('form_cancel_url', $params);
			$form->addButtonTripple(
				array(
					'BUTTON_CANCEL_URL' => $this->url->urlGet($params)
				)
			);
		}
		
		$form->setFormEnd();
	}
	
	/**
	 * Sets the values from $config_fields to the given item
	 * @param array $config_fields Configuration of all fields as array 
	 * @param SF_INTERFACE_Item $item Item that uses the interface
	 * @return void
	 */
	protected function _setConfigFieldsToItem($config_fields, &$item)
	{
		foreach($config_fields AS $k=>$v)
		{
			//show if type exists
			if (! array_key_exists('type', $v))
			{
				continue;
			}
			
			//filter type
			switch($v['type'])
			{
				case 'headline':
				case 'info':
					continue;
					break;
				case 'text':
				case 'textarea':
				case 'cmslink':
				case 'directorychooser':
					$item->setField($k, $v['val']);
					break;
				case 'checkbox':
					if ($v['checked'])
					{
						$item->setField($k, $v['val']);
					}
					else
					{
						$item->setField($k, '');
					}
					break;
				case 'select':
					$item->setField($k, $v['selected']);
					break;
			}
		}
	}
	
	/**
	 * Generate an array with the schema array('fieldname' => 'value').
	 * Use the array to pass it to create or update operations in an item.
	 * @param array $config_fields Configuration of all fields as array 
	 * @return array Returns the array with the schema array('fieldname' => 'value').
	 */
	protected function _getItemFieldArrayFromConfigFields($config_fields)
	{
		$arr = array();
		foreach($config_fields AS $k=>$v)
		{
			//show if type exists
			if (! array_key_exists('type', $v))
			{
				continue;
			}
			
			//filter type
			switch($v['type'])
			{
				case 'headline':
				case 'info':
					continue;
					break;
				case 'hidden':
				case 'text':
				case 'textarea':
				case 'cmslink':
				case 'directorychooser':
					$arr[$k] = $v['val'];
					break;
				case 'checkbox':
					if ($v['checked'])
					{
						$arr[$k] = $v['val'];
					}
					else
					{
						$arr[$k] ='';
					}
					break;
				case 'select':
					$arr[$k] = $v['selected'];
					break;
				case 'editor':
					if($v['editor_instance'] != null && $v['editor_instance']->isEditorAvailable() == TRUE)
					{
						$arr[$k] = $v['editor_instance']->getFieldValue();
					}
					break;
			}
		}
		
		return $arr;
	}
	
	/**
	 * Replaces some variables like controller name in the
	 * field configuration ($fieldconfig) and create the
	 * url with {@link $url} for the set parameters.
	 * @param array $fieldconfig
	 * @return array Returns the modified $fieldconfig.
	 */
	protected function _replaceTemplateVarsInConfigFields($config_fields)
	{
		$config_fields = $this->lng->replaceLangInArray($config_fields, $this->controller_cfg['ctr_name']);
		$config_fields = $this->url->replaceUrlInArray($config_fields, $this->controller_cfg['ctr_name']);
		return $config_fields;
	}
	
	/**
	 * Manipulate links by $type and given $params.
	 * Overwrite it in subclasses.
	 * @param string $type
	 * @param array $params Parameter to manipulate
	 * @return array Returns the maniuplated $params
	 */
	protected function _manipulateLink($type, $params)
	{
		/*switch($type)
		{
			case 'form_hidden_params':
				break;
			case 'form_submit_redirect_url':
				break;
			case 'form_cancel_url':
				break;	
		}*/
		return $params;
	}
	
	/**
	 * Split $msg_string and return an array with type and message for further use.
	 * @param string $msg_string
	 * @return array|booelan Returns an array with keys type and message or on failure FALSE.
	 */
	protected function _getMessage($msg_string)
	{
		if($msg_string == '')
		{
			return FALSE;
		}
		
		$split = explode('_', $msg_string, 2);
		
		switch($split[0])
		{
			//case 'fatal':
			case 'error':
			case 'warning':
			//case 'notice':
			case 'info':
			//case 'debug':
			case 'ok':
				$lng = $this->lng->get($this->controller_cfg['ctr_name'].'_'.$split[1]);
				return array(
					'type' => $split[0], 
					'message' => (($lng != '') ? $lng : $split[1])
				);
				break;
		}
		
		return FALSE;
	}
	
	/**
	 * Stores $value by $key in the session object.
	 * Variables can be grouped by $group.
	 * If you want to clear all previous variables of the given group,
	 * set $cleargroup to TRUE. Afterwards the new variable will be the
	 * only one.
	 * @param string $key
	 * @param mixed $value
	 * @param string $group
	 * @param boolean $cleargroup
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _setVarToSession($key, $value, $group = 'default', $cleargroup = FALSE)
	{
		global $GLOBALS;
		
		$varname = $this->controller_cfg['session_variable_name'];
		$group = ($group === '') ? 'default' : $group;
		
		if($cleargroup == TRUE) // auto clear: || count($GLOBALS['var2session'][$group]) > 3)
		{
			$this->_clearGroupFromSession($group);
		}
		
		$GLOBALS[$varname][$group][$key] = $value;
		$this->cfg->sess()->register($varname);
	}
	
	/**
	 * Retrieves the $value from the session object by given $key.
	 * Use $group if the variable is stored in a specific group.
	 * If you want to clear all variables of the given group,
	 * set $cleargroup to TRUE. 
	 * @param string $key
	 * @param string $group
	 * @param boolean $cleargroup
	 * @return boolean|mixed Returns the stored value or FALSE on failure.
	 */
	protected function _getVarFromSession($key, $group = 'default', $cleargroup = FALSE)
	{
		global $GLOBALS;
		
		$varname = $this->controller_cfg['session_variable_name'];
		$group = ($group === '') ? 'default' : $group;
		
		if($this->cfg->sess()->is_registered($varname) !== FALSE &&
			array_key_exists($group, $GLOBALS[$varname]) === TRUE &&
			array_key_exists($key, $GLOBALS[$varname][$group]) === TRUE)
		{
			$value = $GLOBALS[$varname][$group][$key];
			
			if($cleargroup == TRUE)
			{
				$this->_clearGroupFromSession($group);
			}
			
			return $value;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Unregister a variable by $key from the session object.
	 * Use $group if the variable is stored in a specific group.
	 * @param string $key
	 * @param string $group
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _clearVarFromSession($key, $group = 'default')
	{
		global $GLOBALS;
		
		$varname = $this->controller_cfg['session_variable_name'];
		$group = ($group === '') ? 'default' : $group;
		
		if(array_key_exists($group, $GLOBALS[$varname]) === TRUE &&
			array_key_exists($key, $GLOBALS[$varname][$group]) === TRUE)
		{
			unset($GLOBALS[$varname][$group][$key]);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Unregister all variables of a specific group from the session object.
	 * @param string $group
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _clearGroupFromSession($group = 'default')
	{
		global $GLOBALS;
		
		$varname = $this->controller_cfg['session_variable_name'];
		$group = ($group === '') ? 'default' : $group;
		
		if(array_key_exists($group, $GLOBALS[$varname]) == TRUE)
		{
			unset($GLOBALS[$varname][$group]);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Redirect to the index area. The message code is optional.
	 * @param string $msgcode
	 * @return void
	 */
	protected function _indexRedirect($msgcode = '')
	{
		if(empty($msgcode) == FALSE)
		{
			$this->url->urlAddModifyParams(array('msgcode' => $msgcode));
		}
		$http_header = sf_api('LIB', 'HttpHeader');
		$http_header->redirect($this->url->urlGet(array('area' => $this->controller_cfg['ctr_name'].'_index') ));
	}
	
	
	/**
	 * Write a system log message to Logger
	 * @param string $message
	 * @param array $params - optional
	 * @param string $priority - optional, default: 'trace'
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _setSystemLogMessage($message, $params = array(), $priority = 'trace')
	{
		return $this->_setLogMessage($message, $params, $priority, 'sf_core');
	}
	
	/**
	 * Write a user log message to Logger
	 * @param string $message
	 * @param array $params - optional
	 * @param string $priority - optional, default: 'info'
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _setUserLogMessage($message, $params = array(), $priority = 'info')
	{
		return $this->_setLogMessage($message, $params, $priority, 'user');
	}
	
	/**
	 * Writes a log message to Logger
	 * @param string $message
	 * @param array $params
	 * @param string $priority
	 * @param string $type
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _setLogMessage($message, $params, $priority, $type)
	{
		$area = $this->controller_cfg['ctr_name'];
		
		if(isset($this->config_area) == TRUE && array_key_exists('area_name', $this->config_area) == TRUE)
		{
			$area = $this->config_area['area_name'];
		}
		
		$return = TRUE;
		switch($priority)
		{
			case 'fatal':
				$return = $this->log->fatal($type, $area.'_'.$message, $params);
				break;
			case 'error':
				$return = $this->log->error($type, $area.'_'.$message, $params);
				break;
			case 'warning':
				$return = $this->log->warning($type, $area.'_'.$message, $params);
				break;
			case 'info':
				$return = $this->log->info($type, $area.'_'.$message, $params);
				break;
			case 'debug':
				$return = $this->log->debug($type, $area.'_'.$message, $params);
				break;
			case 'trace':
				$return = $this->log->trace($type, $area.'_'.$message, $params);
				break;
			default:
				$return = $this->log->info($type, $area.'_'.$message, $params);
		}
		return $return;
	}
}
?>