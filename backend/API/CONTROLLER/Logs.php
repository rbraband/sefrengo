<?php
$this->includeClass('CONTROLLER', 'AbstractArea');

/**
 * Logs
 */
class SF_CONTROLLER_Logs extends SF_CONTROLLER_AbstractArea
{
	/**
	 * Stores the configuration (from the client settings) of this area 
	 * @var array
	 */
	protected $config_area = array(
		'config_file' => 'CONFIGURATION/LogsConfiguration.php',
		'area_name' => 'logs', // set area_name explicitly!
		'idclient' => 0,
		'idlang' => 0,
		
		// no settings from client configuration
		'items_per_page' => 50,
		'delta_for_pager' => 2,
		'pager_link_class' => 'ajaxdeeplink',
		
		// params will be used as $params
		'filter_params_default' => array(
			'searchterm' => '',
			'period_from' => '',
			'period_to' => '',
			'priority' => array(),
			'type' => array(),
			'created_author' => '',
			'is_backend' => 0
		),
		'filter_params' => array(),
		
		'suffix_overleap_file' => '.old',
		
		'js_lang' => array()
	);
	
	/**
	 * Logs Sql Item
	 * @var SF_MODEL_LogSqlItem
	 */
	protected $log_sql_item;
	
	/**
	 * Logs Sql Collection
	 * @var SF_MODEL_LogsSqlCollection
	 */
	protected $log_sql_collection;
	
	
	/**
	 * URL Parameter
	 * @var array
	 */
	protected $params = array(
		'area' => '',
		'order' => '',
		'ascdesc' => '',
		'page' => 1
		// plus $config_area['filter_params'] -> see _initParams()
	);
	
	/**
	 * Configuration for the toolbar
	 * The order of the arrays reprensents the view order.
	 * Placed in an external configuration file.
	 * @var array
	 */
	protected $config_toolbar = array();
	
	/**
	 * Configuration for the tables and forms
	 * Placed in an external configuration file.
	 * @var array
	 */
	protected $config_fields = array();
	
	/**
	 * Constructor set main values to {@link $controller_cfg},
	 * collect {@link $params} from {@link $req} and instantiate
	 * {@link $log_sql_item}, {@link $log_sql_collection}.
	 * @return void
	 */
	public function __construct()
	{
		// call abstract area constructor
		parent::__construct();
		
		// set area configuration
		$this->config_area['perm_area'] = 'area_' . $this->config_area['area_name'];

		// perm check for the whole area
		$this->cfg->perm()->check($this->config_area['perm_area']);
		
		$this->_initConfiguration();
		$this->_initLang();
		$this->_initModel();
		$this->_initParams();
	}
	
	/**
	 * Initialize area configuration
	 * @return void
	 */
	private function _initConfiguration()
	{
		// load configuration
		$this->cfg->loadCustomByFile($this->config_area['config_file'], $this->config_area['area_name'], 'api');
		$this->config_toolbar = $this->cfg->custom('toolbar', $this->config_toolbar, $this->config_area['area_name']);
		$this->config_fields = $this->cfg->custom('fields', $this->config_fields, $this->config_area['area_name']);
		
		$this->config_area['idclient'] = $this->cfg->env('idclient');
		$this->config_area['idlang'] = $this->cfg->env('idlang');
	}
	
	/**
	 * Initialize default language
	 * @return void
	 */
	private function _initLang()
	{
		// add language vars for usage in JavaScript
		$this->config_area['js_lang'] = array(
			'delete_multi_confirm' => $this->lng->get($this->config_area['area_name'].'_js_delete_multi_confirm'),
		);
	}
	
	/**
	 * Initialize model with items and collections
	 * @return void
	 */
	private function _initModel()
	{	
		$this->log_sql_item = sf_api('MODEL', 'LogSqlItem');
		$this->log_sql_collection = sf_api('MODEL', 'LogSqlCollection');

		// set idclient and idlang initially for correct loading
		$this->log_sql_item->setIdclient( $this->config_area['idclient'] );
		$this->log_sql_item->setIdlang( $this->config_area['idlang'] );
	}

	/**
	 * Initialize used parameter
	 * @return void
	 */
	private function _initParams()
	{
		// get common request params
		$this->params['area'] = $this->req->req('area', $this->config_area['area_name']);
		$this->params['order'] = $this->req->req('order', $this->params['order']);
		$this->params['ascdesc'] = $this->req->req('ascdesc', $this->params['ascdesc']);
		$this->params['page'] = (int) $this->req->req('page', $this->params['page']);
		
		// add filter to params and filter_params
		foreach($this->config_area['filter_params_default'] as $field => $val)
		{
			$this->params[$field] = $this->req->req($field, $val);
			$this->config_area['filter_params'][$field] = $this->params[$field];
		}
		
		// set baseurivals
		$this->url->urlSetBase($this->controller_cfg['cms_basefile'], $this->params);
	}

	
	/**
	 * 
	 * @see API/CONTROLLER/SF_CONTROLLER_AbstractArea#index()
	 * @return void 
	 */
	public function index()
	{		
		$html = sf_api('LIB', 'HtmlHelper');
		
		$attributes = array();
		$attributes['logs_table']['style'] = "margin:0;border-top:0;border-bottom:0;";
		$attributes['logs_table_tr']['class'] = "tblrbgcolors2";
		
		$attributes['nodata_table']['style'] = "margin:0;border-top:0;";
		$attributes['select_table']['style'] = "margin:0;border-top:0;";
		
		// initialize collection
		$logcol = $this->log_sql_collection;
		$logcol->setIdclient( $this->config_area['idclient'] );
		//$logcol->setIdlang( $this->config_area['idlang'] );
		$logcol->setLimitStart( ( ($this->params['page']-1)*$this->config_area['items_per_page']) );
		$logcol->setLimitMax($this->config_area['items_per_page']);
		$order = ($this->params['order'] == '') ? 'created' : $this->params['order'];
		$ascdesc = ($this->params['ascdesc'] == '') ? 'desc' : $this->params['ascdesc'];
		$logcol->setOrder($order, $ascdesc);
		
		$filter_display = array(); // filter display for table head
		// get toolbar config for langfiles
		$tmp_toolbarconfig = $this->_replaceTemplateVarsInToolbarConfig($this->config_fields['toolbar_search']);
		
		// special handling for period
		if($this->config_area['filter_params']['period_from'] != '' && $this->config_area['filter_params']['period_to'] != '')
		{
			$from_ts = strtotime($this->config_area['filter_params']['period_from']);
			$from['d'] = date('d', $from_ts);
			$from['m'] = date('m', $from_ts);
			$from['y'] = date('Y', $from_ts);
			$to_ts = strtotime($this->config_area['filter_params']['period_to']);
			$to['d'] = date('d', $to_ts);
			$to['m'] = date('m', $to_ts);
			$to['y'] = date('Y', $to_ts);
			$logcol->setDaterange($from, $to);
			$filter_display['filters']['period'] = $tmp_toolbarconfig['period']['title'].': '.$this->config_area['filter_params']['period_from'].' - '.$this->config_area['filter_params']['period_to'];
		}
		
		foreach($this->config_area['filter_params'] as $field => $val)
		{
			// map fieldname
			switch($field)
			{
				case 'priority': $field_db = 'priorityname'; break;
				default: $field_db = $field;
			}
			
			if($field == 'searchterm' && $val != '')
			{
				$searchterm = strtolower($val);
				$log_messages = $this->lng->getAll('logs_messages_');
				
				// filter messages that contains the searchterm
				foreach ($log_messages as $key => $value)
				{
					unset ($log_messages[$key]);
					$key = str_replace('logs_messages_', '', $key);
					
					// wildcard search in values and keys
					if( strpos(strtolower($value), $searchterm) !== FALSE ||
						strpos(strtolower($key), $searchterm) !== FALSE)
					{
						$log_messages[$key] = $value;
					}
		        }
				
				// get only keys to search in message field
				$log_messages_keys = array_keys($log_messages);
				
				// TODO Search in messages and params at the same time (not possible at this time).
				// Therefore an OR connection between two filter must be implemented in the collection.
				
				// search in messages
				if(count($log_messages_keys) > 0)
				{
					$logcol->setFreefilter('message', $log_messages_keys, 'IN');
				}
				// search in params (defined in collection)
				else
				{
					$logcol->setSearchterm($this->params['searchterm']);
				}
				
				$filter_display['filters'][$field] = $tmp_toolbarconfig[$field]['title'].': "'.$val.'"';
			}
			else if($field == 'created_author' && $val != '')
			{
				$logcol->setFreefilter($field_db, $val.'%', 'LIKE');
				$filter_display['filters'][$field] = $tmp_toolbarconfig[$field]['title'].': "'.$val.'"';
			}
			else if($field == 'period_from' || $field == 'period_to')
			{
				continue; // skip; special handling for period before loop
			}
			else if($field == 'is_backend' && $val == '-1')
			{
				continue; // skip if default value ('-1')
			}
			else if(is_array($val) == TRUE && count($val) > 0)
			{
				$logcol->setFreefilter($field_db, $val, 'IN');
				$filter_display['filters'][$field] = $tmp_toolbarconfig[$field]['title'].': "'.implode(', ', $val).'"';
			}
			else if((is_string($val) == TRUE && $val != '') || (is_numeric($val) == TRUE && $val != 0))
			{
				$logcol->setFreefilter($field_db, $val, '=');
				$filter_display['filters'][$field] = $tmp_toolbarconfig[$field]['title'].': "'.$val.'"';
			}
		}
		unset($tmp_toolbarconfig);
		
		if(count($filter_display['filters']) > 0)
		{
			$filter_display['lang_filter_show'] = $this->lng->get('gen_filter_show');
			$filter_display['lang_filter_reset'] = $this->lng->get('gen_filter_reset');
			$filter_display['reset_url'] = $this->url->urlGet($this->config_area['filter_params_default']);
		}
		
		$logcol->generate();
		
		// fallback page does not exsist
		if ($logcol->getCount() < 1 && $this->params['page'] > 1) 
		{
			$this->http_header->redirect($this->url->urlGet(array('page' => 1) ) );
		}
		
		// table head
		$table_output = '';
		$table = sf_api('VIEW', 'Table');
		$table->loadTemplatefile('table_index.tpl');
		$table->setUrlBuilder($this->url);
		$table->setTableAttributes('style="margin-bottom:0;border-bottom:0;"');
		$this->url->urlAddModifyParams(array('area' => $this->config_area['area_name']));
		$config_fields = $this->_replaceTemplateVarsInConfigFields($this->config_fields['index_head']);
		
		$pager = array();
		$pager['colspan'] = count($this->config_fields['index_head']);
		$pager['count_all'] = $logcol->getCountAll();
		$pager['items_per_page'] = $this->config_area['items_per_page'];
		$pager['delta_for_pager'] = $this->config_area['delta_for_pager'];
		$pager['link_class'] = $this->config_area['pager_link_class'];
		$pager['current_page'] = $this->params['page'];
		$pager['exclude_vars'] = array('render');
		$pager['lang_page'] = $this->lng->get('gen_page');
		$pager['lang_from'] = $this->lng->get('gen_from');
		
		if($logcol->getCount() > 0)
		{
			$table->buildPager('top', $pager, $filter_display);
		}
		else if(count($filter_display['filters']) > 0)
		{
			$pager['current_page'] = 0;
			$table->buildPager('top', $pager, $filter_display);
		}
		
		$table->buildTableHead(
			$config_fields,
			array(
				'order' => $this->params['order'],
				'ascdesc' => $this->params['ascdesc']
			)
		);
		
		$table_output .= $table->get();

		// table body		
		$table = sf_api('VIEW', 'TableFm');
		$table->loadTemplatefile('table_index.tpl');
		$table->setUrlBuilder($this->url);
		$table->setTableAttributes($html->attributesArrayToHtmlString($attributes['logs_table']));
		
		if($logcol->getCount() > 0)
		{
			$config_fields = $this->_replaceTemplateVarsInConfigFields($this->config_fields['index_body']);
			$table->buildTableBody(
				$logcol,
				$config_fields,
				array(
					'tr_attributes' => $html->attributesArrayToHtmlString($attributes['logs_table_tr'])
				)
			);
		}
		
		$table_output .= $table->get();
		
		// no data row
		if($logcol->getCount() <= 0)
		{
			$table = sf_api('VIEW', 'Table');
			$table->loadTemplatefile('table_index.tpl');
			$table->setUrlBuilder($this->url);
			$table->setTableAttributes($html->attributesArrayToHtmlString($attributes['nodata_table']));
			
			$table->buildTableRowNoData(array(
				'colspan' => count($this->config_fields['index_body']),
				'nodata' => $this->lng->get($this->config_area['area_name'].'_collection_nodata')
			));
			
			//$table->buildPager('bottom', $pager);
			
			$table_output .= $table->get();
		}
		else
		{
			$table = sf_api('VIEW', 'Table');
			$table->loadTemplatefile('table_index.tpl');
			$table->setUrlBuilder($this->url);
			$table->setTableAttributes($html->attributesArrayToHtmlString($attributes['select_table']));
			
			$config_fields = $this->_replaceTemplateVarsInConfigFields($this->config_fields['index_footer']);
			$chk_name = $this->config_fields['index_body']['checkbox']['renderer']['chk_name'].'[]';
			
			$pager['colspan'] = count($this->config_fields['index_footer']) + 1;
			$table->buildPager('bottom', $pager);
			
			$table->buildTableFooterSelectMultiple(
				$config_fields['action'],
				array(
					'colspan' => '0',//count($this->config_fields['index_body']),
					'select' => $this->lng->get('gen_select'),
					'select_all' => array(
						'lang' => $this->lng->get('gen_select_all'),
						'chk_name' => $chk_name
					),
					'select_none' => array(
						'lang' => $this->lng->get('gen_select_none'),
						'chk_name' => $chk_name
					)
				)
			);
			
			//$table->buildPager('bottom', $pager);
			
			$table_output .= $table->get();
		}
		
		// toolbar
		$toolbarconfig = $this->config_toolbar['index'];
		if( array_key_exists('search', $toolbarconfig) &&
			array_key_exists('type', $toolbarconfig['search']) &&
			$toolbarconfig['search']['type'] == 'adv_search')
		{
			$config_fields = $this->config_fields['toolbar_search'];
			foreach($config_fields as $field => $val)
			{
				switch($val['type'])
				{
					case 'datepicker':
						$config_fields[$field]['val']['from'] = $this->config_area['filter_params'][$field.'_from'];
						$config_fields[$field]['val']['to'] = $this->config_area['filter_params'][$field.'_to'];
						break;
					case 'select':
						$db = sf_api('LIB', 'Ado');
						$sql = 'SELECT DISTINCT
									'.(($field == 'priority') ? 'priorityname' : $field).' as '.$field.'
								FROM '.
									$this->cfg->db('logs').'
								ORDER BY
									'.$field.' ASC;';
						//echo $sql.'<br />';
						$rs = $db->Execute($sql);
						
						if ($rs !== FALSE)
						{
							while (! $rs->EOF) 
							{
								$config_fields[$field]['val'][$rs->fields[$field]] = $rs->fields[$field];
								$rs->MoveNext();
							}
							$rs->Close();
						}
						unset($db, $rs);
						$config_fields[$field]['selected'] = $this->config_area['filter_params'][$field];
						break;
					case 'text':
						$config_fields[$field]['val'] = $this->config_area['filter_params'][$field];
						break;
					case 'radio':
						$config_fields[$field]['checked'] = $this->config_area['filter_params'][$field];
						break;
				}
			}
			$toolbarconfig['search']['tabs'] = $config_fields;
		}
		
		$toolbarconfig = $this->_replaceTemplateVarsInToolbarConfig($toolbarconfig);
		$toolbarconfig['form']['action'] = $this->url->urlGet();
		
		$toolbar = sf_api('VIEW', 'Toolbar');
		$toolbar->buildToolbarFromArray($toolbarconfig);
		
		$render = $this->req->req('render', 'complete');
		$backend_area = sf_api('VIEW', 'BackendArea');
		
		// error or warning templates
		$msg = $this->_getMessage($this->req->req('msgcode'));
		if($msg !== FALSE)
		{
			//$msghash = $this->req->req('msghash');
			$moreinfo = array();
			if($msghash !== FALSE)
			{
				$moreinfo['url'] = $this->url->urlGet(array('area' => $this->config_area['area_name'].'_message_more_info', 'msghash' => $msghash) );
				$moreinfo['text'] = $this->lng->get($this->config_area['area_name'].'_more_info');
			}
			
			$backend_area->addMessage($msg['type'], $msg['message'], $moreinfo);
		}

		if($render == 'complete')
		{
			$backend_area->addCmsHeader($this->config_area['js_lang']);
			$backend_area->addFooter();
		}
		
		$backend_area->addTemplateVar('TOOLBAR', $toolbar, 'RIGHTPANE');
		$backend_area->addTemplateVar('TITLE', $this->lng->get($this->config_area['area_name'].'_area_index'), 'RIGHTPANE.TITLE');
		$backend_area->addTemplateVar('RIGHTPANE', $table_output, 'RIGHTPANE');
		
		$backend_area->generate();
		return $backend_area->get();
	}
	
	
	/**
	 * Backend area (only rightpane) with a form to delete multiple logs
	 * @return void
	 */
	public function deleteMultiple()
	{
		$idlogs = $this->req->req('l', array());
		
		if(count($idlogs) <= 0)
		{
			$this->_indexRedirect();
		}
		
		$catched_messages = array(
			'type' => 'delete_multiple'
		);
		$ok = $error = 0;
		
		foreach($idlogs as $idlog)
		{
			if (! $this->log_sql_item->loadById($idlog)) 
			{
				$this->_setSystemLogMessage('log_is_not_loaded', array('multiple' => TRUE, 'idlog' => $idlog));
				$catched_messages['log'][$idlog] = 'error_log_is_not_loaded';
				++$error;
				continue;
			}
			
			try
			{
				if($this->log_sql_item->delete() == TRUE)
				{
					// no log message for successful deletion -> otherwise you could never delete all messages
					//$this->_setUserLogMessage('delete_log_success', array('multiple' => TRUE, 'idlog' => $idlog));
					$catched_messages['log'][$idlog] = 'ok_delete_log_success';
					++$ok;
				}
			}
			catch(Exception $e)
			{
				switch($e->getCode())
				{
					// 0 = fatal, 1 = error
					case 0:
					case 1:
						$code = 'error';
						break;
					// 2 = warning
					case 2:
						$code = 'warning';
						break;
				}
				$catched_messages['log'][$idlog] = $code.'_'.$e->getMessage();
				$this->_setUserLogMessage($e->getMessage(), array('multiple' => TRUE, 'idlog' => $idlog), $code);
				++$error;
			}
		}

		if($error > 0)
		{
			// store catched messages to session
			$msghash = md5(time()); 
			$this->_setVarToSession($msghash, $catched_messages, 'msg', TRUE);
			$msgcode = ($ok > 0) ? 'warning_some_actions_failed' : 'error_all_actions_failed';
		}
		else
		{
			$msghash = '';
			$msgcode = 'ok_action_successful';
		}
		
		$this->http_header->redirect($this->url->urlGet(array('area' => $this->config_area['area_name'].'_index', 'msghash' => $msghash, 'msgcode' => $msgcode) ) );
	}

	public function logfileBe()
	{
		$this->params['logfile'] = 'backend';
		return $this->viewLogfile();
	}

	public function logfileFe()
	{
		$this->params['logfile'] = 'frontend';
		return $this->viewLogfile();
	}

	protected function viewLogfile()
	{
		//redirect if user is denied to view logs from logfile, but is allowed to view database
		if ($this->cfg->perm()->have_perm(3, 'area_logs', 0) == FALSE)
		{
			$this->_indexRedirect('error_permission_denied');
			exit;
		}
		
		$form = sf_api('VIEW', 'Form');
		$logfile = sf_api('MODEL', 'FileSqlItem');
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$is_overleap = $this->req->req('overleap', FALSE); // overleap file
		
		$path = '';
		switch($this->params['logfile'])
		{
			case 'backend':
				$path = $this->cfg->env('path_backend').$this->cfg->cms('log_path');
				break;
				
			case 'frontend':
				// Does any config file from to client exists? Then include to get the log_path-setting
				if(file_exists($this->cfg->env('path_frontend').'cms/inc/config.php')) {
					include $this->cfg->env('path_frontend').'cms/inc/config.php';
				}
				
				$path = $this->cfg->env('path_frontend').$cfg_client['log_path'];
				break;
		}

		// show / use overleap file -> add to file path and url
		if($is_overleap == TRUE)
		{
			$this->url->urlAddModifyParams(array('overleap' => $is_overleap));
			$path .= $this->config_area['suffix_overleap_file'];
		}
		
		$path = $fsm->utf8_decode($path);
		
		if($form->wasSend() == TRUE)
		{
			if($this->req->req('delete_check', FALSE) === FALSE)
			{
				$msgcode = 'warning_delete_file_not_checked';
			}
			else if($fsm->deleteFile($path) == TRUE)
			{
				$this->_setUserLogMessage('delete_logfile_success', array('path' => $path));
				$msgcode = 'ok_delete_logfile_success';
			}
			else
			{
				$this->_setUserLogMessage('delete_logfile_failed', array('path' => $path));
				$msgcode = 'error_delete_logfile_failed';
			}
			
			$this->url->urlAddModifyParams(array('msgcode' => $msgcode));
			$this->http_header->redirect($this->url->urlGet());
			unset($msgcode);
		}
			
		// assign real form vals to config fields
		$config_fields = $this->_assignValuesToConfigFields(
			$this->config_fields['view_logfile'], 
			null, 
			$form->wasSend()
		);
		
		$config_fields['path']['val'] = $path;
		$config_fields['content']['val'] = '';
		$config_fields['filesize']['val'] = 0;
		$config_fields['lastmodified']['val'] = 0;
		
		if(file_exists($path) == TRUE)
		{
			$config_fields['content']['val'] = $fsm->readContentFromFile($path);
			$config_fields['filesize']['val'] = $fsm->readablizeBytes(filesize($path));
			$config_fields['lastmodified']['val'] = filemtime($path);
			if(array_key_exists('format', $config_fields['lastmodified']))
			{
				$date = date($this->cfg->cms('format_date'), $config_fields['lastmodified']['val']);
				$time = date($this->cfg->cms('format_time'), $config_fields['lastmodified']['val']);
				$config_fields['lastmodified']['val'] = str_replace(array('{date}', '{time}'), array($date, $time), $config_fields['lastmodified']['format']);
			}
		}
		
		
		// validate or save form
		$msg_string = $warning_string = '';
		if ($form->wasSend())
		{
			// validate form
			$msg_string = $this->_validateConfigFields($config_fields);
		}
		// check if overleap file exists and set message
		else if($is_overleap == FALSE && $fsm->fileExists($path.$this->config_area['suffix_overleap_file']) == TRUE)
		{
			$msg_string = 'warning_overleap_logfile_exists';
		}
		else if($is_overleap == TRUE)
		{
			$msg_string = 'info_return_to_logfile';
		}
		
		
		// build form
		$this->_buildFormFromConfigFields($config_fields, $form);
		
		// toolbar
		$toolbarconfig = $this->_replaceTemplateVarsInToolbarConfig($this->config_toolbar['index']);
		unset($toolbarconfig['search']);
		//$toolbarconfig['form']['action'] = $this->url->urlGet();
		$toolbar = sf_api('VIEW', 'Toolbar');
		$toolbar->buildToolbarFromArray($toolbarconfig);
		
		$backend_area = sf_api('VIEW', 'BackendArea');
		$backend_area->addCmsHeader();
		$backend_area->addFooter();
		
		// error or warning templates
		$msg = $this->_getMessage($this->req->req('msgcode', $msg_string));
		if($msg !== FALSE)
		{
			// replace the url to overleap logfile
			$msg['message'] = str_replace(
				array('{overleap_url}', '{area_url}'),
				array($this->url->urlGet(array('overleap' => '1')), $this->url->urlGet(array('overleap' => '0'))),
				$msg['message']
			);
			
			$backend_area->addMessage($msg['type'], $msg['message']);
		}

		$backend_area->addTemplateVar('TOOLBAR', $toolbar, 'RIGHTPANE');
		$title = $this->lng->get($this->config_area['area_name'].'_area_logfile_fe');
		if($this->params['logfile'] == 'backend')
		{
			$title = $this->lng->get($this->config_area['area_name'].'_area_logfile_be');
		}
		$backend_area->addTemplateVar('TITLE', $title, 'RIGHTPANE.TITLE');
		$backend_area->addTemplateVar('RIGHTPANE', $form, 'RIGHTPANE');
		
		$backend_area->generate();
		return $backend_area->get();
	}
	
	/**
	 * Replaces some variables like controller name in the
	 * toolbar configuration ($toolbarconfig) and create the
	 * url with {@link $url} for the set parameters.
	 * @param array $toolbarconfig
	 * @return array Returns the modified $toolbarconfig.
	 */
	protected function _replaceTemplateVarsInToolbarConfig($toolbarconfig)
	{
		foreach($toolbarconfig as $id => $element)
		{
			foreach($element as $field => $value)
			{
				if($field == 'type' && $value == 'search')
				{
					$toolbarconfig[$id]['value'] = $this->req->req($toolbarconfig[$id]['name'], '');
					continue;
				}
			
				// check for perm
				if($field == 'perm' && array_key_exists('obj', $toolbarconfig[$id]['perm']))
				{
					$obj = null;
					if($toolbarconfig[$id]['perm']['obj'] == 'log')
					{
						$obj = $this->log_sql_item;
					}
					
					$parent_id = (array_key_exists('parent_id', $toolbarconfig[$id]['perm'])) ? $toolbarconfig[$id]['perm']['parent_id'] : 0;
					
					// add has_perm property to config
					$toolbarconfig[$id]['perm']['has_perm'] = ($obj->hasPerm($toolbarconfig[$id]['perm']['name'], $toolbarconfig[$id]['perm']['id'], $parent_id) == TRUE);
				}
			}
		}
		
		$toolbarconfig = $this->_replaceTemplateVarsInConfigFields($toolbarconfig);
		
		return $toolbarconfig;
	}
}
?>	