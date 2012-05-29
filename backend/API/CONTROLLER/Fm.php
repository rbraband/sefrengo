<?php
$this->includeClass('CONTROLLER', 'AbstractArea');

/**
 * Filemanager
 */
class SF_CONTROLLER_Fm extends SF_CONTROLLER_AbstractArea
{
	/**
	 * Stores the configuration (from the client settings) of this area 
	 * @var array
	 */
	protected $config_area = array(
		'config_file' => 'CONFIGURATION/FmConfiguration.php',
		'area_name' => 'fm', // set area_name explicitly!
		'idclient' => 0,
		'idlang' => 0,
		'start_idlang' => 0,
		'multi_language_support' => TRUE,
		
		'temp_out_path' => '/upload/out',
		'temp_in_path' => '/upload/in',
		'download_archive_filetype' => 'zip',
		'extract_compressed_files' => array('tar', 'gz', 'bz2', 'tgz', 'tbz', 'zip', 'ar', 'deb'),
		
		'forbidden_directories' => array('.svn'),
		'forbidden_files' => array('htaccess', 'htpasswd'),
		'allowed_files' => array(), // invalidates 'forbidden_files'
		
		'allow_invalid_filenames' => 1,
		'allow_invalid_dirnames' => 1,
		
		// no settings from client configuration
		'viewtype' => 'compact',
		'items_per_page' => 50,
		'remove_root_actions' => array('delimiter1','upload_file','create_file','copy_directory','download_directory','edit_directory','delete_directory'),
		'files_in_root' => FALSE,
		'enable_left_pane' => TRUE,
		
		'items_per_page' => 50,
		'delta_for_pager' => 2,
		'pager_link_class' => 'ajaxdeeplink',
		
		'js_lang' => array()
	);
	
	/**
	 * Directory Sql Item
	 * @var SF_MODEL_DirectorySqlItem
	 */
	protected $directory_sql_item;
	
	/**
	 * Directory Sql Collection
	 * @var SF_MODEL_DirectorySqlCollection
	 */
	protected $directory_sql_collection;
	
	/**
	 * Directory Sql Tree
	 * @var SF_MODEL_DirectorySqlTree
	 */
	protected $directory_sql_tree;
	
	/**
	 * File Sql Item
	 * @var SF_MODEL_FileSqlItem
	 */
	protected $file_sql_item;
	
	/**
	 * File Sql Collection
	 * @var SF_MODEL_FileSqlCollection
	 */
	protected $file_sql_collection;
	
	
	/**
	 * URL Parameter
	 * @var array
	 */
	protected $params = array(
		'area' => '',
		'order' => '',
		'ascdesc' => '',
		'searchterm' => '',
		//'page' => 1,
		'iddirectory' => 0
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
	 * {@link $directory_sql_item}, {@link $directory_sql_collection},
	 * {@link $directory_sql_tree}, {@link $file_sql_item},
	 * {@link $file_sql_collection}.
	 * @return void
	 */
	public function __construct()
	{
		// call abstract area constructor
		parent::__construct();

		$this->_initConfiguration();
		
		// set area configuration
		$this->config_area['perm_area'] = 'area_' . $this->config_area['area_name'];
		
		// perm check for the whole area
		$this->cfg->perm()->check($this->config_area['perm_area']);
		
		$this->_initLang();
		$this->_initModel();
		$this->_initParams();
	}
	
	/**
	 * Initialize default language
	 * @return void
	 */
	private function _initLang()
	{
		// add language vars for usage in JavaScript
		$this->config_area['js_lang'] = array(
			'close_confirm' => $this->lng->get($this->config_area['area_name'].'_js_close_confirm'),
			'upload_close_confirm' => $this->lng->get($this->config_area['area_name'].'_js_upload_close_confirm'),
			'scan_close_confirm' => $this->lng->get($this->config_area['area_name'].'_js_scan_close_confirm'),
			'error_loadinglayer' => $this->lng->get($this->config_area['area_name'].'_js_error_loadinglayer'),
			'error_nopreview' => $this->lng->get($this->config_area['area_name'].'_js_error_nopreview'),
			'delete_dir_confirm' => $this->lng->get($this->config_area['area_name'].'_js_delete_dir_confirm'),
			'delete_file_confirm' => $this->lng->get($this->config_area['area_name'].'_js_delete_file_confirm'),
			'delete_multi_confirm' => $this->lng->get($this->config_area['area_name'].'_js_delete_multi_confirm'),
		);
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
		
		// filemanager settings
		$this->config_area['temp_out_path'] = $this->cfg->env('path_backend').'upload/out';
		$this->config_area['temp_in_path'] = $this->cfg->env('path_backend').'upload/in';
		$this->config_area['multi_language_support'] = (bool) $this->cfg->client($this->config_area['area_name'].'_multi_language_support', $this->config_area['multi_language_support']);
		$this->config_area['idclient'] = $this->cfg->env('idclient');
		// choose current idlang or 0 as generic language
		$this->config_area['idlang'] = ($this->config_area['multi_language_support'] == TRUE) ? $this->cfg->env('idlang') : 0;
		$this->config_area['start_idlang'] = $this->cfg->getStartIdLang();
		
		$this->config_area['allow_invalid_filenames'] = (int) $this->cfg->client($this->config_area['area_name'].'_allow_invalid_filenames', $this->config_area['allow_invalid_filenames']);
		$this->config_area['allow_invalid_dirnames'] = (int) $this->cfg->client($this->config_area['area_name'].'_allow_invalid_dirnames', $this->config_area['allow_invalid_dirnames']);
		
		$this->config_area['forbidden_directories'] = explode(',', $this->cfg->client($this->config_area['area_name'].'_forbidden_directories', implode(',', $this->config_area['forbidden_directories'])));
		$this->config_area['forbidden_files'] = explode(',', $this->cfg->client($this->config_area['area_name'].'_forbidden_files', implode(',', $this->config_area['forbidden_files'])));
		$this->config_area['allowed_files'] = explode(',', $this->cfg->client($this->config_area['area_name'].'_allowed_files', implode(',', $this->config_area['allowed_files'])));
		
		$this->_setSystemLogMessage('init_configuration', $this->config_area);
	}
	
	/**
	 * Initialize model with items and collections
	 * @return void
	 */
	private function _initModel()
	{	
		$this->directory_sql_item = sf_api('MODEL', 'DirectorySqlItem');
		$this->directory_sql_collection = sf_api('MODEL', 'DirectorySqlCollection');
		$this->directory_sql_tree = sf_api('MODEL', 'DirectorySqlTree');
		$this->file_sql_item = sf_api('MODEL', 'FileSqlItem');
		$this->file_sql_collection = sf_api('MODEL', 'FileSqlCollection');

		// set idclient and idlang initially for correct loading
		$this->directory_sql_item->setIdclient( $this->config_area['idclient'] );
		$this->directory_sql_item->setIdlang( $this->config_area['idlang'] );
		$this->file_sql_item->setIdclient( $this->config_area['idclient'] );
		$this->file_sql_item->setIdlang( $this->config_area['idlang'] );
		
		// Enable or disable the multi language support and convert the language table
		if($this->config_area['multi_language_support'] == TRUE)
		{
			// duplicate the current metadata to all available langs of the client
			$bool1 = $this->file_sql_item->enableMultiLanguageSupport( $this->config_area['idclient'], $this->cfg->getIdLangs() );
			$bool2 = $this->directory_sql_item->enableMultiLanguageSupport( $this->config_area['idclient'], $this->cfg->getIdLangs() );
			if($bool1 == TRUE && $bool2 == TRUE)
			{
				$this->_setSystemLogMessage('enable_mls', array(), 'info');
			}
		}
		else
		{
			// use the language from the start lang as default
			$bool1 = $this->file_sql_item->disableMultiLanguageSupport( $this->config_area['idclient'], $this->config_area['start_idlang'] );
			$bool2 = $this->directory_sql_item->disableMultiLanguageSupport( $this->config_area['idclient'], $this->config_area['start_idlang'] );
			if($bool1 == TRUE && $bool2 == TRUE)
			{
				$this->_setSystemLogMessage('disable_mls', array(), 'info');
			}
		}
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
		$this->params['searchterm'] = $this->req->req('searchterm', $this->params['searchterm']);
		$this->params['page'] = (int) $this->req->req('page', $this->params['page']);
		$this->params['iddirectory'] = (int) $this->req->req('iddirectory', $this->params['iddirectory']);
		
		$this->_setSystemLogMessage('init_params', $this->params);
		
		// get view type from url, cookie or as default from settings
		$this->config_area['viewtype'] = (isset($_COOKIE[ $this->config_area['area_name'].'_viewtype' ])) ? $_COOKIE[ $this->config_area['area_name'].'_viewtype' ] : $this->config_area['viewtype'];
		$this->config_area['viewtype'] = $this->req->req('viewtype', $this->config_area['viewtype']);
		
		// set baseurivals
		$this->url->urlSetBase($this->controller_cfg['cms_basefile'], $this->params);
	}
	
	/**
	 * Generates and prints out the backend area with a leftpane and rightpane.
	 * The leftpane is empty (the directory tree is loaded from JavaScript).
	 * The rightpane contains the toolbar, some tables (head, directory, files).
	 * If no directories and files are found a table with an empty message is set.
	 * @see API/CONTROLLER/SF_CONTROLLER_AbstractArea#index()
	 * @return void 
	 */
	public function index()
	{		
		// get id
		$id = (int) $this->req->req('iddirectory', 0);
		
		// load data - if loading fails redirect to index page
		if ($id > 0 && !$this->directory_sql_item->loadById($id)) 
		{
			$this->http_header->redirect($this->url->urlGet(array('area' => $this->config_area['area_name'].'_index', 'iddirectory' => '') ) );
		}
		
		// if searchterm is set, always use the root directory to show search results
		if($this->params['searchterm'] != '')
		{
			$this->params['iddirectory'] = 0;
		}
		
		$html = sf_api('LIB', 'HtmlHelper');
		
		$attributes = array();
		$attributes['directory_table']['style'] = "margin:0;border-top:0;border-bottom:0;";
		$attributes['directory_table_tr']['class'] = "tblrbgcolorf2";
		
		$attributes['file_table']['style'] = "margin:0;border-top:0;border-bottom:0;";
		$attributes['file_table_tr']['class'] = "tblrbgcolors2";
		
		$attributes['nodata_table']['style'] = "margin:0;border-top:0;";
		$attributes['select_table']['style'] = "margin:0;border-top:0;";
		
		// table head
		$table_output = '';
		$table = sf_api('VIEW', 'TableFm');
		$table->loadTemplatefile('table_index.tpl');
		$table->setUrlBuilder($this->url);
		$table->setTableAttributes('style="margin-bottom:0;border-bottom:0;"');
		$this->url->urlAddModifyParams(array('area' => $this->config_area['area_name']));
		$config_fields = $this->_replaceTemplateVarsInConfigFields($this->config_fields['index_head']);
		
		// no pager for this controller
		$pager = array();
		//$pager['colspan'] = count($this->config_fields['index_head']);
		//$pager['count_all'] = $logcol->getCountAll();
		//$pager['items_per_page'] = $this->config_area['items_per_page'];
		//$pager['delta_for_pager'] = $this->config_area['delta_for_pager'];
		//$pager['link_class'] = $this->config_area['pager_link_class'];
		//$pager['current_page'] = $this->params['page'];
		//$pager['exclude_vars'] = array('render');
		//$pager['lang_page'] = $this->lng->get('gen_page');
		//$pager['lang_from'] = $this->lng->get('gen_from');
		$filter = array();
		if($this->params['searchterm'] != '')
		{
			$pager['colspan'] = count($this->config_fields['index_head']);
			$filter['filters'] = array($this->params['searchterm']);
			$filter['lang_filter_show'] = $this->lng->get('gen_filter_show');
			$filter['lang_filter_reset'] = $this->lng->get('gen_filter_reset');
			$filter['reset_url'] = $this->url->urlGet(array('searchterm' => ''));
		}
		$table->buildPager('top', $pager, $filter);
		
		$table->buildTableHead(
			$config_fields,
			array(
				'order' => $this->params['order'],
				'ascdesc' => $this->params['ascdesc']
			)
		);
		
		$table_output .= $table->get();
		
		$dircol = $this->directory_sql_collection;
		$dircol->setIdclient( $this->config_area['idclient'] );
		$dircol->setIdlang( $this->config_area['idlang'] );
		$dircol->setFreefilter('area', $this->config_area['area_name']);
		//$dircol->setLimitStart( ( ($this->params['page']-1)*$this->config_area['items_per_page']) );
		//$dircol->setLimitMax($this->config_area['items_per_page']);
		$order = ($this->params['order'] == '') ? 'name' : $this->params['order'];
		$dircol->setOrder($order, $this->params['ascdesc']);
		if($this->params['searchterm'] != '' && $this->params['iddirectory'] <= 0)
		{
			$dircol->setSearchterm($this->params['searchterm']);
		}
		else
		{
			$dircol->setFreefilter('parentid', $this->params['iddirectory']);
		}
		$dircol->generate();
		
		// fallback page does not exsist
		//if ($dircol->getCount() < 1 && $this->params['page'] > 1) 
		//{
		//	$this->http_header->redirect($this->url->urlGet(array('page' => 1) ) );
		//}
		
		$table = sf_api('VIEW', 'TableFm');
		$table->loadTemplatefile('table_index.tpl');
		$table->setUrlBuilder($this->url);
		$table->setTableAttributes($html->attributesArrayToHtmlString($attributes['directory_table']));
		$table = $this->_buildParentDirectoryRow($table);
		
		if($dircol->getCount() > 0)
		{
			$config_fields = $this->_replaceTemplateVarsInConfigFields($this->config_fields['index_directory']);
			$table->buildTableBody(
				$dircol,
				$config_fields,
				array(
					'tr_attributes' => $html->attributesArrayToHtmlString($attributes['directory_table_tr'])
				)
			);
		}
		
		$table_output .= $table->get();
		
		$filecol = $this->file_sql_collection;
		$filecol->setIdclient( $this->config_area['idclient'] );
		$filecol->setIdlang( $this->config_area['idlang'] );
		$filecol->setFreefilter('area', $this->config_area['area_name']);
		//$filecol->setLimitStart( ( ($this->params['page']-1)*$this->config_area['items_per_page']) );
		//$filecol->setLimitMax($this->config_area['items_per_page']);
		$order = ($this->params['order'] == '') ? 'filename' : $this->params['order'];
		$filecol->setOrder($order, $this->params['ascdesc']);
		if($this->params['searchterm'] != '' && $this->params['iddirectory'] <= 0)
		{
			$filecol->setSearchterm($this->params['searchterm']);
		}
		else
		{
			$filecol->setFreefilter('iddirectory', $this->params['iddirectory']);
		}
		$filecol->generate();
		
		if($filecol->getCount() > 0)
		{
			$table = sf_api('VIEW', 'TableFm');
			$table->loadTemplatefile('table_index.tpl');
			$table->setUrlBuilder($this->url);
			$table->setTableAttributes($html->attributesArrayToHtmlString($attributes['file_table']));
			$config_fields = $this->_replaceTemplateVarsInConfigFields($this->config_fields['index_files']);
			$table->buildTableBody(
				$filecol,
				$config_fields,
				array(
					'tr_attributes' => $html->attributesArrayToHtmlString($attributes['file_table_tr']),
					'viewtype' => $this->config_area['viewtype'],
					'detail' => array(
						'colspan' => count($this->config_fields['index_files']),
						'fields' => $this->_replaceTemplateVarsInConfigFields($this->config_fields['index_files_detail'])
					)
				)
			);
			$table_output .= $table->get();
		}
		
		// no data row
		if($dircol->getCount() <= 0 && $filecol->getCount() <= 0)
		{
			$table = sf_api('VIEW', 'TableFm');
			$table->loadTemplatefile('table_index.tpl');
			$table->setUrlBuilder($this->url);
			$table->setTableAttributes($html->attributesArrayToHtmlString($attributes['nodata_table']));
			
			$table->buildTableRowNoData(array(
				'colspan' => count($this->config_fields['index_files']),
				'nodata' => $this->lng->get($this->config_area['area_name'].'_filecollection_nodata')
			));
			
			//$table->buildPager('bottom', $pager);
			
			$table_output .= $table->get();
		}
		else
		{
			$table = sf_api('VIEW', 'TableFm');
			$table->loadTemplatefile('table_index.tpl');
			$table->setUrlBuilder($this->url);
			$table->setTableAttributes($html->attributesArrayToHtmlString($attributes['select_table']));
			
			$config_fields = $this->_replaceTemplateVarsInConfigFields($this->config_fields['index_footer']);
			$chk_name = $this->config_fields['index_directory']['checkbox']['renderer']['chk_name'].'[],';
			$chk_name .= $this->config_fields['index_files']['checkbox']['renderer']['chk_name'].'[]';
			
			$table->buildTableFooterSelectMultiple(
				$config_fields['action'],
				array(
					'colspan' => count($this->config_fields['index_compact_files']),
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
		$toolbarconfig = $this->_replaceTemplateVarsInToolbarConfig($this->config_toolbar['index']);
		$toolbarconfig['form']['action'] = $this->url->urlGet();
		
		// remove some actions if root directory
		if($this->params['iddirectory'] == 0 && array_key_exists('remove_root_actions', $this->config_area) == TRUE)
		{
			foreach($this->config_area['remove_root_actions'] as $action) 
			{
				unset($toolbarconfig[$action]);
			}
		}
		// if viewtype has changed store in cookie
		if($this->config_area['viewtype'] != $_COOKIE[ $this->config_area['area_name'].'_viewtype' ])
		{
			setcookie( $this->config_area['area_name'].'_viewtype', $this->config_area['viewtype']);
			$this->_setSystemLogMessage('setcookie_viewtype', array('viewtype' =>  $this->config_area['viewtype']));
		}
		// remove viewtype that is not selected
		if($this->config_area['viewtype'] == 'compact')
		{
			unset($toolbarconfig['viewtype_compact']);
		}
		else
		{
			unset($toolbarconfig['viewtype_detail']);
		}
		
		$toolbar = sf_api('VIEW', 'Toolbar');
		$toolbar->buildToolbarFromArray($toolbarconfig);
		
		// build tree
		$dirtree = $this->directory_sql_tree;
		$dirtree->setIdclient( $this->config_area['idclient'] );
		//$dirtree->setIdlang( $this->config_area['idlang'] );
		$dirtree->setArea($this->config_area['area_name']);
		$dirtree->generate();
		
		// breadcrumbs
		$breadcrumbs = $this->_buildBreadcrumbsFromRecursiveParents($dirtree->getParentsRecursive($this->params['iddirectory']));
		
		$render = $this->req->req('render', 'complete');
		$backend_area = sf_api('VIEW', 'BackendArea');
		
		// error or warning templates
		$msg = $this->_getMessage($this->req->req('msgcode'));
		if($msg !== FALSE)
		{
			//$msghash = $this->req->req('msghash');
			$moreinfo = array();
			//if($msghash !== FALSE)
			//{
			//	$moreinfo['url'] = $this->url->urlGet(array('area' => $this->config_area['area_name'].'_message_more_info', 'msghash' => $msghash) );
			//	$moreinfo['text'] = $this->lng->get($this->config_area['area_name'].'_more_info');
			//}
			
			$backend_area->addMessage($msg['type'], $msg['message'], $moreinfo);
		}
		
		if($render == 'complete')
		{
			$attributes['overlay']['class'] = 'sf_overlay';
			$attributes['main']['data-config'] = array(
				'leftpane_name' => $this->config_area['area_name']."_index",
				'loadurl' => $this->url->urlGet(array('area' => $this->config_area['area_name'].'_ajax_directory_tree'))
			);
			
			// overlay
			$overlay = sf_api('VIEW', 'Overlay');
			$overlay->loadTemplatefile('overlay.tpl');
			$overlay->addTemplateVar('OVERLAY_NAME', 'sf_overlay');
			$overlay->addTemplateVar('ATTRIBUTES', $html->attributesArrayToHtmlString($attributes['overlay']));
			$overlays .= $overlay->get();
			$backend_area->addTemplateVar('OVERLAY', $overlays);
			
			$backend_area->addCmsHeader($this->config_area['js_lang']);
			$backend_area->addFooter();
			
			$backend_area->addTemplateVar('MAIN_ATTRIBUTES', $html->attributesArrayToHtmlString($attributes['main']));

			if($this->config_area['enable_left_pane'] == TRUE)
			{
				$backend_area->addTemplateVar('LEFTPANE', '<!-- load tree with ajax -->', 'LEFTPANE');
			}
		}
		
		$backend_area->addTemplateVar('TOOLBAR', $toolbar, 'RIGHTPANE');
		$backend_area->addTemplateVar('BREADCRUMB', $breadcrumbs, 'RIGHTPANE');
		$backend_area->addTemplateVar('TITLE', $this->lng->get($this->config_area['area_name'].'_area_index'), 'RIGHTPANE.TITLE');
		$backend_area->addTemplateVar('RIGHTPANE', $table_output, 'RIGHTPANE');
		
		$backend_area->generate();
		return $backend_area->get();
	}
	
	/**
	 * Get the directory tree as unordered list to send for a ajax call
	 * @return void
	 */
	public function ajaxDirectoryTree()
	{
		$dirtree = $this->directory_sql_tree;
		$dirtree->setIdclient( $this->config_area['idclient'] );
		//$dirtree->setIdlang( $this->config_area['idlang'] );
		$dirtree->setArea($this->config_area['area_name']);
		//$order = ($this->params['order'] == '') ? 'name' : $this->params['order'];
		//$dirtree->setOrder($order, $this->params['ascdesc']);
		$dirtree->setOrder('name');
		$dirtree->generate();
		
		$html = sf_api('LIB', 'HtmlHelper');
		
		$attributes = array();
		$attributes['roottree']['class'] = "treeview directorytree";
		$attributes['roottree']['data-options'] = array('collapsed' => TRUE);
		
		$attributes['rootleaf']['class'] = "action directorytree_root ajaxdeeplink";
		$attributes['rootleaf']['rel'] = "rightpane";
		$attributes['rootleaf_active']['class'] = "action active directorytree_root ajaxdeeplink";
		$attributes['rootleaf_active']['rel'] = "rightpane";
		
		$attributes['leafs']['class'] = "action ajaxdeeplink";
		$attributes['leafs']['rel'] = "rightpane";
		$attributes['leafs_active']['class'] = "action active ajaxdeeplink";
		$attributes['leafs_active']['rel'] = "rightpane";
		
		$tree = sf_api('VIEW', 'Tree');
		$tree->setOptions(array(
			'roottree' => array(
				'attributes' => $html->attributesArrayToHtmlString($attributes['roottree'])
			),
			'rootleaf' => array(
				'text' => $this->lng->get($this->config_area['area_name'].'_base_directory'),
				'url' => $this->url->urlGet(array('area' => $this->config_area['area_name'].'_index', 'iddirectory' => '', 'searchterm' => '')),
				'attributes' => $html->attributesArrayToHtmlString($attributes['rootleaf']),
				'attributes_active' => $html->attributesArrayToHtmlString($attributes['rootleaf_active'])
			),
			'leafs' => array(
				// reference on method to use
				'text' => array(
					'object' => $this,
					'function' => 'getDirectoryNameById'
				),
				'url' => $this->url->urlGet(array('area' => $this->config_area['area_name'].'_index', 'iddirectory' => '{id}', 'searchterm' => '')),
				'attributes' => $html->attributesArrayToHtmlString($attributes['leafs']),
				'attributes_active' => $html->attributesArrayToHtmlString($attributes['leafs_active'])
			),
			'active_leaf' => $this->params['iddirectory']
		));
		$tree->setTreeModel($dirtree);
		$tree->buildTree(0);
		
		$tree->generate();
		return $tree->get();
	}
	
	/**
	 * Get more information for the given msghash in URL. 
	 * @return string
	 */
	public function messageMoreInfo()
	{
		$msghash = $this->req->req('msghash');
		$information = $this->_getVarFromSession($msghash, 'msg');
		
		//load data - if loading fails redirect to index page
		if ($msghash === FALSE || $information === FALSE) 
		{
			return $this->lng->get($this->config_area['area_name'].'_no_information_found');
		}
		else
		{
			print_r($information);
		}
	}
	
	/* ******************
	 *  DIRECTORY
	 * *****************/
	
	/**
	 * Backend area (only rightpane) with a
	 * form to create a directory.
	 * Redirect on succes to index page.
	 * @return void
	 */
	public function createDirectory()
	{
		// check perm for action
		if($this->directory_sql_item->hasPerm('create', $this->params['iddirectory']) == FALSE)
		{
			$this->_setUserLogMessage('permission_denied', array('id' => $this->params['iddirectory'], 'path' => $this->directory_sql_item->getRelativePath()), 'debug');
			$this->_indexRedirect('error_permission_denied');
		}
		
		$form = sf_api('VIEW', 'Form');
		
		// assign real form vals to config fields
		$config_fields = $this->_assignValuesToConfigFields(
			$this->config_fields['edit_directory'], 
			$this->directory_sql_item, 
			$form->wasSend()
		);
		
		// validate or save form
		$msg_string = '';
		if ($form->wasSend())
		{
			// check if invalid directorynames are allowed
			switch($this->config_area['allow_invalid_filenames'])
			{
				// no: check by normal validation
				case 0:
					break;
				// yes: disable validation
				case 1:
					unset($config_fields['name']['validation']['directoryname']);
					break;
				// correct directoryname: disable validation and correct directoryname in directory_sql_item
				case 2:
					unset($config_fields['name']['validation']['directoryname']);
					break;
			}
			
			// validate form
			$msg_string = $this->_validateConfigFields($config_fields);
			
			// validation errors found, so mark them as errors
			if($msg_string != '')
			{
				$msg_string = 'error_'.$msg_string;
			}
			// forbidden file extension
			else if($this->_isForbiddenDirectoryName($config_fields['name']['val']) == TRUE)
			{
				$msg_string = 'error_forbidden_directory_name';
			}
			// save
			else 
			{
				$directorydata = $this->_getItemFieldArrayFromConfigFields($config_fields);
				$directorydata['area'] = $this->config_area['area_name'];
				$directorydata['idclient'] = $this->config_area['idclient'];
				
				try
				{
					if($this->directory_sql_item->create($directorydata) == TRUE)
					{
						$msgcode = 'ok_save_directory_success';
						$directorydata['path'] = $this->directory_sql_item->getRelativePath();
						$this->_setUserLogMessage('save_directory_success', $directorydata);
						
						// redirect to tableview
						if ($form->saveWasPressed())
						{
							$this->_indexRedirect($msgcode);
						}
						// apply was pressed
						else
						{
							$this->url->urlAddModifyParams(array('msgcode' => $msgcode, 'id' => $this->directory_sql_item->getId()));
							$this->http_header->redirect($this->url->urlGet(array('area' => $this->config_area['area_name'].'_edit_directory') ) );
						}
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
						default:
							$code = 'info';
					}
					
					$lng = $this->lng->get($this->config_area['area_name'].'_'.$e->getMessage());
					$msg_string = $code.'_';
					$msg_string .= ($lng != '') ? $lng : $e->getMessage();
					
					$directorydata['path'] = $this->directory_sql_item->getRelativePath();
					$this->_setUserLogMessage($e->getMessage(), $directorydata, $code);
				}
			}
		}
		
		// build form
		$this->_buildFormFromConfigFields($config_fields, $form);
		
		$backend_area = sf_api('VIEW', 'BackendArea');
		$backend_area->addCmsHeader($this->config_area['js_lang']);
		$backend_area->addFooter();
		
		// error or warning templates
		$msg = $this->_getMessage($this->req->req('msgcode', $msg_string));
		if($msg !== FALSE)
		{
			$backend_area->addMessage($msg['type'], $msg['message']);
		}
		
		$backend_area->addTemplateVar('TITLE', $this->lng->get($this->config_area['area_name'].'_area_create_directory'), 'RIGHTPANE.TITLE');
		$backend_area->addTemplateVar('RIGHTPANE', $form, 'RIGHTPANE');
		
		$backend_area->generate();
		return $backend_area->get();
	}
	
	/**
	 * Backend area (only rightpane) with a
	 * form to edit directory.
	 * Redirect on succes to index page.
	 * @return void
	 */
	public function editDirectory()
	{
		// get id
		$id = (int) $this->req->req('id', 0);
		
		// load data - if loading fails redirect to index page
		if (! $this->directory_sql_item->loadById($id)) 
		{
			$this->_setSystemLogMessage('directory_is_not_loaded', array('id' => $id));
			$this->_indexRedirect('error_directory_is_not_loaded');
		}
	
		// check perm for action
		if($this->directory_sql_item->hasPerm('edit') == FALSE)
		{
			$this->_setUserLogMessage('permission_denied', array('id' => $id, 'path' => $this->directory_sql_item->getRelativePath()), 'debug');
			$this->_indexRedirect('error_permission_denied');
		}
		
		if($this->config_area['multi_language_support'] == TRUE)
		{
			$foreign_idlang = (int) $this->req->req('foreign_idlang', 0);
			if($foreign_idlang > 0)
			{
				$this->url->urlGet(array( 'foreign_idlang' => '' ));
				
				if($this->directory_sql_item->copyLanguageMetadata($foreign_idlang) == FALSE)
				{
					$this->_setUserLogMessage('copy_metadata_failed', array('id' => $id, 'path' => $this->directory_sql_item->getRelativePath(), 'foreign_idlang' => $foreign_idlang), 'debug');
					$msgcode = 'error_copy_metadata_failed';
				}
				else
				{
					$this->_setUserLogMessage('copy_metadata_success', array('id' => $id, 'path' => $this->directory_sql_item->getRelativePath(), 'foreign_idlang' => $foreign_idlang), 'debug');
					$msgcode = 'ok_copy_metadata_success';
				}

				$this->url->urlAddModifyParams(array('msgcode' => $msgcode, 'id' => $this->directory_sql_item->getId()));
				$this->http_header->redirect($this->url->urlGet(array('area' => $this->config_area['area_name'].'_edit_directory') ) );
			}
		}
		
		$form = sf_api('VIEW', 'Form');
		
		// assign real form vals to config fields
		$config_fields = $this->_assignValuesToConfigFields(
			$this->config_fields['edit_directory'], 
			$this->directory_sql_item, 
			$form->wasSend()
		);
		
		// validate or save form
		$msg_string = '';
		if ($form->wasSend())
		{
			// check if invalid directorynames are allowed
			switch($this->config_area['allow_invalid_filenames'])
			{
				// no: check by normal validation
				case 0:
					break;
				// yes: disable validation
				case 1:
					unset($config_fields['name']['validation']['directoryname']);
					break;
				// correct directoryname: disable validation and correct directoryname in directory_sql_item
				case 2:
					unset($config_fields['name']['validation']['directoryname']);
					break;
			}
			
			// validate form
			$msg_string = $this->_validateConfigFields($config_fields);
			
			// validation errors found, so mark them as errors
			if($msg_string != '')
			{
				$msg_string = 'error_'.$msg_string;
			}
			// forbidden file extension
			else if($this->_isForbiddenDirectoryName($config_fields['name']['val']) == TRUE)
			{
				$msg_string = 'error_forbidden_directory_name';
			}
			// save
			else 
			{
				$directorydata = $this->_getItemFieldArrayFromConfigFields($config_fields);
				$directorydata['area'] = $this->config_area['area_name'];
				$directorydata['idclient'] = $this->config_area['idclient'];
				
				try
				{
					if($this->directory_sql_item->edit($directorydata) == TRUE)
					{
						$msgcode = 'ok_update_directory_success';
						$directorydata['path'] = $this->directory_sql_item->getRelativePath();
						$this->_setUserLogMessage('update_directory_success', $directorydata);
						
						// redirect to tableview
						if ($form->saveWasPressed())
						{
							$this->_indexRedirect($msgcode);
						}
						// apply was pressed
						else
						{
							$this->url->urlAddModifyParams(array('msgcode' => $msgcode, 'id' => $this->directory_sql_item->getId()));
							$this->http_header->redirect($this->url->urlGet(array('area' => $this->config_area['area_name'].'_edit_directory') ) );
						}
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
						default:
							$code = 'info';
					}
					
					$lng = $this->lng->get($this->config_area['area_name'].'_'.$e->getMessage());
					$msg_string = $code.'_';
					$msg_string .= ($lng != '') ? $lng : $e->getMessage();
					
					$directorydata['path'] = $this->directory_sql_item->getRelativePath();
					$this->_setUserLogMessage($e->getMessage(), $directorydata, $code);
				}
			}
		}
		
		// build form
		$this->_buildFormFromConfigFields($config_fields, $form);
		
		// toolbar if MLS is enabled
		if($this->config_area['multi_language_support'] == TRUE)
		{
			$toolbarconfig = $this->_replaceTemplateVarsInToolbarConfig($this->config_toolbar['edit_directory']);
			$toolbarconfig['form']['action'] = $this->url->urlGet();
			$toolbarconfig['hidden_iddirectory']['value'] = $id;
			
			$langs = $this->cfg->getLangsForClient($this->config_area['idclient']);
			foreach($langs as $lang)
			{
				if($lang['is_current'] == false)
				{
					$toolbarconfig['actionbox']['values'][ $lang['idlang'] ] = $lang['name'];
				}
			}
			
			$toolbar = sf_api('VIEW', 'Toolbar');
			$toolbar->buildToolbarFromArray($toolbarconfig);
		}
		
		$backend_area = sf_api('VIEW', 'BackendArea');
		$backend_area->addCmsHeader($this->config_area['js_lang']);
		$backend_area->addFooter();
		
		// error or warning templates
		$msg = $this->_getMessage($this->req->req('msgcode', $msg_string));
		if($msg !== FALSE)
		{
			$backend_area->addMessage($msg['type'], $msg['message']);
		}
		
		if($this->config_area['multi_language_support'] == TRUE)
		{
			$backend_area->addTemplateVar('TOOLBAR', $toolbar, 'RIGHTPANE');
		}
		
		$backend_area->addTemplateVar('TITLE', $this->lng->get($this->config_area['area_name'].'_area_edit_directory'), 'RIGHTPANE.TITLE');
		$backend_area->addTemplateVar('RIGHTPANE', $form, 'RIGHTPANE');
		
		$backend_area->generate();
		return $backend_area->get();
	}
	
	/**
	 * Deletes the directory by id (retrieve from {@link $req})
	 * and redirect on success or failure to index page. 
	 * @return void
	 */
	public function deleteDirectory()
	{
		//get id
		$id = (int) $this->req->req('id', 0);
		
		//load data - if loading fails redirect to index page
		if (! $this->directory_sql_item->loadById($id)) 
		{
			$this->_setSystemLogMessage('directory_is_not_loaded', array('id' => $id));
			$this->_indexRedirect('error_directory_is_not_loaded');
		}
	
		// check perm for action
		if($this->directory_sql_item->hasPerm('delete') == FALSE)
		{
			$this->_setUserLogMessage('permission_denied', array('id' => $id, 'path' => $this->directory_sql_item->getRelativePath()), 'debug');
			$this->_indexRedirect('error_permission_denied');
		}

		try
		{
			if($this->directory_sql_item->delete() == TRUE)
			{
				$this->_setUserLogMessage('delete_directory_success', array('id' => $id, 'path' => $this->directory_sql_item->getRelativePath()));
				$this->_indexRedirect('ok_delete_directory_success');
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
				default:
					$code = 'info';
			}
			
			$msgcode = $code.'_'.$e->getMessage();
			
			$this->_setUserLogMessage($e->getMessage(), array('id' => $id, 'path' => $this->directory_sql_item->getRelativePath()), $code);
		}
		$this->_indexRedirect($msgcode);
	}

	/**
	 * Backend area (only rightpane) with a
	 * form to copy directory
	 * @return void
	 */
	public function copyDirectory()
	{
		//get id
		$id = (int) $this->req->req('id', 0);
		$parentid = (int) $this->req->req('parentid', $this->params['iddirectory']);
		
		//load data - if loading fails redirect to index page
		if (! $this->directory_sql_item->loadById($id)) 
		{
			$this->_setSystemLogMessage('directory_is_not_loaded', array('id' => $id));
			$this->_indexRedirect('error_directory_is_not_loaded');
		}
	
		// check perm for action
		if($this->directory_sql_item->hasPerm('copy') == FALSE)
		{
			$this->_setUserLogMessage('permission_denied', array('id' => $id, 'path' => $this->directory_sql_item->getRelativePath()), 'debug');
			$this->_indexRedirect('error_permission_denied');
		}
		
		$form = sf_api('VIEW', 'Form');
		
		// assign real form vals to config fields
		$config_fields = $this->_assignValuesToConfigFields(
			$this->config_fields['copy_directory'], 
			$this->directory_sql_item, 
			$form->wasSend()
		);
		
		$config_fields['source']['val'] = $this->directory_sql_item->getHtmlPath();
		
		// validate or save form
		$msg_string = '';
		if ($form->wasSend())
		{
			// check if invalid directorynames are allowed
			switch($this->config_area['allow_invalid_filenames'])
			{
				// no: check by normal validation
				case 0:
					break;
				// yes: disable validation
				case 1:
					unset($config_fields['name']['validation']['directoryname']);
					break;
				// correct directoryname: disable validation and correct directoryname in directory_sql_item
				case 2:
					unset($config_fields['name']['validation']['directoryname']);
					break;
			}
			
			// validate form
			$msg_string = $this->_validateConfigFields($config_fields);
			
			// validation errors found, so mark them as errors
			if($msg_string != '')
			{
				$msg_string = 'error_'.$msg_string;
			}
			// forbidden file extension
			else if($this->_isForbiddenDirectoryName($config_fields['name']['val']) == TRUE)
			{
				$msg_string = 'error_forbidden_directory_name';
			}
			// save
			else 
			{
				$directorydata = $this->_getItemFieldArrayFromConfigFields($config_fields);
				$directorydata['area'] = $this->config_area['area_name'];
				$directorydata['idclient'] = $this->config_area['idclient'];
				
				try
				{
					$move = $this->req->asBoolean('move');
					$success = FALSE;
					$msgcode = '';
					if($move == FALSE && $this->directory_sql_item->copy($directorydata) == TRUE)
					{
						$directorydata['path'] = $this->directory_sql_item->getRelativePath();
						$this->_setUserLogMessage('copy_directory_success', $directorydata);
						$msgcode = 'ok_copy_directory_success';
						$success = TRUE;
					}
					else if($move == TRUE && $this->directory_sql_item->move($directorydata) == TRUE)
					{
						$directorydata['path'] = $this->directory_sql_item->getRelativePath();
						$this->_setUserLogMessage('move_directory_success', $directorydata);
						$msgcode = 'ok_move_directory_success';
						$success = TRUE;
					}
					
					if($success == TRUE) 
					{
						// redirect to tableview
						if ($form->saveWasPressed())
						{
							$this->_indexRedirect($msgcode);
						}
						// apply was pressed
						else
						{
							$this->url->urlAddModifyParams(array('msgcode' => $msgcode, 'id' => $this->directory_sql_item->getId()));
							$this->http_header->redirect($this->url->urlGet(array('area' => $this->config_area['area_name'].'_copy_directory') ) );
						}
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
						default:
							$code = 'info';
					}
					
					$lng = $this->lng->get($this->config_area['area_name'].'_'.$e->getMessage());
					$msg_string = $code.'_';
					$msg_string .= ($lng != '') ? $lng : $e->getMessage();
					
					$directorydata['path'] = $this->directory_sql_item->getRelativePath();
					$this->_setUserLogMessage($e->getMessage(), $directorydata, $code);
				}
			}
		}
		
		// build directory tree
		$ignore_leafs = array($this->directory_sql_item->getId(), $this->directory_sql_item->getField('parentid'));
		$config_fields['parentid']['tree'] = $this->_getDirectoryChooserTree($parentid, $ignore_leafs, TRUE);
		
		// build form
		$this->_buildFormFromConfigFields($config_fields, $form);
		
		$backend_area = sf_api('VIEW', 'BackendArea');
		$backend_area->addCmsHeader($this->config_area['js_lang']);
		$backend_area->addFooter();
		
		// error or warning templates
		$msg = $this->_getMessage($this->req->req('msgcode', $msg_string));
		if($msg !== FALSE)
		{
			$backend_area->addMessage($msg['type'], $msg['message']);
		}
		
		$backend_area->addTemplateVar('TITLE', $this->lng->get($this->config_area['area_name'].'_area_copy_directory'), 'RIGHTPANE.TITLE');
		$backend_area->addTemplateVar('RIGHTPANE', $form, 'RIGHTPANE');
		
		$backend_area->generate();
		return $backend_area->get();
	}

	/**
	 * Creates an packed archive with files and subdirectories for the given iddirectory.
	 * @return void
	 */
	public function downloadDirectory()
	{
		//get id
		$id = (int) $this->req->req('id', 0);
		
		//load data - if loading fails redirect to index page
		if (! $this->directory_sql_item->loadById($id)) 
		{
			$this->_setSystemLogMessage('directory_is_not_loaded', array('id' => $id));
			$this->_indexRedirect('error_directory_is_not_loaded');
		}
	
		// check perm for action
		if($this->directory_sql_item->hasPerm('download') == FALSE)
		{
			$this->_setUserLogMessage('permission_denied', array('id' => $id, 'path' => $this->directory_sql_item->getRelativePath()), 'debug');
			$this->_indexRedirect('error_permission_denied');
		}
		
		$temp_out_path = $this->config_area['temp_out_path'].'/'.md5(time());
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$fsm->createDirectory($temp_out_path);
		
		$dirtree = $this->directory_sql_tree;
		$dirtree->setIdclient( $this->config_area['idclient'] );
		//$dirtree->setIdlang( $this->config_area['idlang'] );
		$dirtree->setArea($this->config_area['area_name']);
		$dirtree->setPermCheck($this->directory_sql_item->getObjectPermType(), $this->directory_sql_item->getObjectPermId('download'), TRUE);
		$dirtree->generate();
		
		$this->_prepareDownloadDirectory($id, $temp_out_path, $dirtree);
		
		$archive = sf_api('LIB', 'Archive');
		$archive::extract(
			$archive::read($temp_out_path),
			$archive::toArchive(
				$this->directory_sql_item->getField('name').'.'.$this->config_area['download_archive_filetype'],
				$archive::toOutput()
			)
		);
		
		$this->_setUserLogMessage('download_directory_success', array('id' => $id, 'path' => $this->directory_sql_item->getRelativePath()));
		
		$fsm->deleteDirectoryRecursive($temp_out_path);
	}
	

	/* ******************
	 *  FILE
	 * *****************/

	/**
	 * Backend area (only rightpane) with a
	 * form to create a file.
	 * Redirect on succes to index page.
	 * @return void
	 */
	public function createFile()
	{
		// check perm for action
		// uses iddirectory as id because perm type is directory and reset parent id
		if($this->file_sql_item->hasPerm('create', $this->params['iddirectory'], 0) == FALSE)
		{
			$this->_setUserLogMessage('permission_denied', array('id' => '0'), 'debug');
			$this->_indexRedirect('error_permission_denied');
		}
		
		$form = sf_api('VIEW', 'Form');
		
		// assign real form vals to config fields
		$config_fields = $this->_assignValuesToConfigFields(
			$this->config_fields['edit_file'], 
			$this->file_sql_item, 
			$form->wasSend()
		);
		
		// validate or save form
		$msg_string = '';
		if ($form->wasSend())
		{
			// check if invalid files are allowed
			switch($this->config_area['allow_invalid_filenames'])
			{
				// no: check by normal validation
				case 0:
					break;
				// yes: disable validation
				case 1:
					unset($config_fields['filename']['validation']['filename']);
					break;
				// correct filename: disable validation and correct filename in file_sql_item
				case 2:
					unset($config_fields['filename']['validation']['filename']);
					break;
			}
			
			// validate form
			$msg_string = $this->_validateConfigFields($config_fields);
			
			$fsm = sf_api('LIB', 'FilesystemManipulation');
			$extension = $fsm->getPathinfo($config_fields['filename']['val'], 'extension');
			unset($fsm);
			
			// validation errors found, so mark them as errors
			if($msg_string != '')
			{
				$msg_string = 'error_'.$msg_string;
			}
			// forbidden file extension
			else if($this->_isForbiddenFileExtension($extension) == TRUE)
			{
				$msg_string = 'error_forbidden_file_extension';
			}
			// save
			else 
			{
				$filedata = $this->_getItemFieldArrayFromConfigFields($config_fields);
				$filedata['area'] = $this->config_area['area_name'];
				$filedata['idclient'] = $this->config_area['idclient'];
				//$filedata['idlang'] = $this->config_area['idlang'];
				
				try
				{
					if($this->file_sql_item->create($filedata) == TRUE)
					{
						$msgcode = 'ok_save_file_success';
						$filedata['id'] = $this->file_sql_item->getId();
						$filedata['path'] = $this->file_sql_item->getRelativePath();
						$this->_setUserLogMessage('save_file_success', $filedata);
						
						// redirect to tableview
						if ($form->saveWasPressed())
						{
							$this->_indexRedirect($msgcode);
						}
						// apply was pressed
						else
						{
							$this->url->urlAddModifyParams(array('msgcode' => $msgcode, 'id' => $this->file_sql_item->getId()));
							$this->http_header->redirect($this->url->urlGet(array('area' => $this->config_area['area_name'].'_edit_file') ) );
						}
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
						default:
							$code = 'info';
					}
					
					$lng = $this->lng->get($this->config_area['area_name'].'_'.$e->getMessage());
					$msg_string = $code.'_';
					$msg_string .= ($lng != '') ? $lng : $e->getMessage();
					
					$filedata['path'] = $this->file_sql_item->getRelativePath();
					$this->_setUserLogMessage($e->getMessage(), $filedata, $code);
				}
			}
		}
		
		// build form
		$this->_buildFormFromConfigFields($config_fields, $form);
		
		$backend_area = sf_api('VIEW', 'BackendArea');
		$backend_area->addCmsHeader($this->config_area['js_lang']);
		$backend_area->addFooter();
	
		// error or warning templates
		$msg = $this->_getMessage($this->req->req('msgcode', $msg_string));
		if($msg !== FALSE)
		{
			$backend_area->addMessage($msg['type'], $msg['message']);
		}
		
		$backend_area->addTemplateVar('TITLE', $this->lng->get($this->config_area['area_name'].'_area_create_file'), 'RIGHTPANE.TITLE');
		$backend_area->addTemplateVar('RIGHTPANE', $form, 'RIGHTPANE');
		
		$backend_area->generate();
		return $backend_area->get();
	}
	
	/**
	 * Backend area (only rightpane) with a
	 * form to edit a file.
	 * Redirect on succes to index page.
	 * @return void
	 */
	public function editFile()
	{
		//get id
		$id = (int) $this->req->req('id', 0);
		
		//load data - if loading fails redirect to index page
		if (! $this->file_sql_item->loadById($id)) 
		{
			$this->_setSystemLogMessage('file_is_not_loaded', array('id' => $id));
			$this->_indexRedirect('error_file_is_not_loaded');
		}
		
		// check perm for action
		if($this->file_sql_item->hasPerm('edit') == FALSE)
		{
			$this->_setUserLogMessage('permission_denied', array('id' => $id, 'path' => $this->file_sql_item->getRelativePath()), 'debug');
			$this->_indexRedirect('error_permission_denied');
		}
		
		
		if($this->config_area['multi_language_support'] == TRUE)
		{
			$foreign_idlang = (int) $this->req->req('foreign_idlang', 0);
			if($foreign_idlang > 0)
			{
				$this->url->urlGet(array( 'foreign_idlang' => '' ));
				
				if($this->file_sql_item->copyLanguageMetadata($foreign_idlang) == FALSE)
				{
					$this->_setUserLogMessage('copy_metadata_failed', array('id' => $id, 'path' => $this->file_sql_item->getRelativePath(), 'foreign_idlang' => $foreign_idlang), 'debug');
					$msgcode = 'error_copy_metadata_failed';
				}
				else
				{
					$this->_setUserLogMessage('copy_metadata_success', array('id' => $id, 'path' => $this->file_sql_item->getRelativePath(), 'foreign_idlang' => $foreign_idlang), 'debug');
					$msgcode = 'ok_copy_metadata_success';
				}

				$this->url->urlAddModifyParams(array('msgcode' => $msgcode, 'id' => $this->file_sql_item->getId()));
				$this->http_header->redirect($this->url->urlGet(array('area' => $this->config_area['area_name'].'_edit_file') ) );
			}
		}
		
		$form = sf_api('VIEW', 'Form');
		
		// assign real form vals to config fields
		$config_fields = $this->_assignValuesToConfigFields(
			$this->config_fields['edit_file'], 
			$this->file_sql_item, 
			$form->wasSend()
		);
		
		// validate or save form
		$msg_string = $warning_string = '';
		if ($form->wasSend())
		{
			// check if invalid files are allowed
			switch($this->config_area['allow_invalid_filenames'])
			{
				// no: check by normal validation
				case 0:
					break;
				// yes: disable validation
				case 1:
					unset($config_fields['filename']['validation']['filename']);
					break;
				// correct filename: disable validation and correct filename in file_sql_item
				case 2:
					unset($config_fields['filename']['validation']['filename']);
					break;
			}

			// validate form
			$msg_string = $this->_validateConfigFields($config_fields);
			
			$fsm = sf_api('LIB', 'FilesystemManipulation');
			$extension = $fsm->getPathinfo($config_fields['filename']['val'], 'extension');
			unset($fsm);

			// validation errors found, so mark them as errors
			if($msg_string != '')
			{
				$msg_string = 'error_'.$msg_string;
			}
			// forbidden file extension
			else if($this->_isForbiddenFileExtension($extension) == TRUE)
			{
				$msg_string = 'error_forbidden_file_extension';
			}
			// save
			else 
			{
				$filedata = $this->_getItemFieldArrayFromConfigFields($config_fields);
				$filedata['area'] = $this->config_area['area_name'];
				$filedata['idclient'] = $this->config_area['idclient'];
				//$filedata['idlang'] = $this->config_area['idlang'];
				
				try
				{
					if($this->file_sql_item->edit($filedata) == TRUE)
					{
						$msgcode = 'ok_update_file_success';
						$filedata['path'] = $this->file_sql_item->getRelativePath();
						$this->_setUserLogMessage('update_file_success', $filedata);
						
						// redirect to tableview
						if ($form->saveWasPressed())
						{
							$this->_indexRedirect($msgcode);
						}
						// apply was pressed
						else
						{
							$this->url->urlAddModifyParams(array('msgcode' => $msgcode, 'id' => $this->file_sql_item->getId()));
							$this->http_header->redirect($this->url->urlGet(array('area' => $this->config_area['area_name'].'_edit_file') ) );
						}
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
						default:
							$code = 'info';
					}
					
					$lng = $this->lng->get($this->config_area['area_name'].'_'.$e->getMessage());
					$msg_string = $code.'_';
					$msg_string .= ($lng != '') ? $lng : $e->getMessage();
					
					$filedata['path'] = $this->file_sql_item->getRelativePath();
					$this->_setUserLogMessage($e->getMessage(), $filedata, $code);
				}
			}
		}
		
		// build form
		$this->_buildFormFromConfigFields($config_fields, $form);
		
		// toolbar if MLS is enabled
		if($this->config_area['multi_language_support'] == TRUE)
		{
			$toolbarconfig = $this->_replaceTemplateVarsInToolbarConfig($this->config_toolbar['edit_file']);
			$toolbarconfig['form']['action'] = $this->url->urlGet();
			$toolbarconfig['hidden_iddirectory']['value'] = $this->file_sql_item->getField('iddirectory');
			$toolbarconfig['hidden_idfile']['value'] = $id;
			
			$langs = $this->cfg->getLangsForClient($this->config_area['idclient']);
			foreach($langs as $lang)
			{
				if($lang['is_current'] == false)
				{
					$toolbarconfig['actionbox']['values'][ $lang['idlang'] ] = $lang['name'];
				}
			}
			
			$toolbar = sf_api('VIEW', 'Toolbar');
			$toolbar->buildToolbarFromArray($toolbarconfig);
		}
		
		$backend_area = sf_api('VIEW', 'BackendArea');
		$backend_area->addCmsHeader($this->config_area['js_lang']);
		$backend_area->addFooter();
		
		// error or warning templates
		$msg = $this->_getMessage($this->req->req('msgcode', $msg_string));
		if($msg !== FALSE)
		{
			$backend_area->addMessage($msg['type'], $msg['message']);
		}
		
		if($this->config_area['multi_language_support'] == TRUE)
		{
			$backend_area->addTemplateVar('TOOLBAR', $toolbar, 'RIGHTPANE');
		}
		$backend_area->addTemplateVar('TITLE', $this->lng->get($this->config_area['area_name'].'_area_edit_file'), 'RIGHTPANE.TITLE');
		$backend_area->addTemplateVar('RIGHTPANE', $form, 'RIGHTPANE');
		
		$backend_area->generate();
		return $backend_area->get();
	}
	
	/**
	 * Deletes the file by id (retrieve from {@link $req})
	 * and redirect on success or failure to index page. 
	 * @return void
	 */
	public function deleteFile()
	{
		//get id
		$id = (int) $this->req->req('id', 0);
		
		//load data - if loading fails redirect to index page
		if (! $this->file_sql_item->loadById($id)) 
		{
			$this->_setSystemLogMessage('file_is_not_loaded', array('id' => $id));
			$this->_indexRedirect('error_file_is_not_loaded');
		}
		
		// check perm for action
		if($this->file_sql_item->hasPerm('delete') == FALSE)
		{
			$this->_setUserLogMessage('permission_denied', array('id' => $id, 'path' => $this->file_sql_item->getRelativePath()), 'debug');
			$this->_indexRedirect('error_permission_denied');
		}

		try
		{
			if($this->file_sql_item->delete() == TRUE)
			{
				$this->_setUserLogMessage('delete_file_success', array('id' => $id, 'path' => $this->file_sql_item->getRelativePath()));
				$this->_indexRedirect('ok_delete_file_success');
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
				default:
					$code = 'info';
			}
			
			$msgcode = $code.'_'.$e->getMessage();
			
			$this->_setUserLogMessage($e->getMessage(), array('id' => $id, 'path' => $this->file_sql_item->getRelativePath()), $code);
		}
		$this->_indexRedirect($msgcode);
	}
	
	/**
	 * Backend area (only rightpane) with a form to copy file
	 * @return void
	 */
	public function copyFile()
	{
		//get id
		$id = (int) $this->req->req('id', 0);
		
		//load data - if loading fails redirect to index page
		if (! $this->file_sql_item->loadById($id)) 
		{
			$this->_setSystemLogMessage('file_is_not_loaded', array('id' => $id));
			$this->_indexRedirect('error_file_is_not_loaded');
		}
		
		// check perm for action
		if($this->file_sql_item->hasPerm('copy') == FALSE)
		{
			$this->_setUserLogMessage('permission_denied', array('id' => $id, 'path' => $this->file_sql_item->getRelativePath()), 'debug');
			$this->_indexRedirect('error_permission_denied');
		}
		
		$form = sf_api('VIEW', 'Form');
		
		// assign real form vals to config fields
		$config_fields = $this->_assignValuesToConfigFields(
			$this->config_fields['copy_file'], 
			$this->file_sql_item, 
			$form->wasSend()
		);
		
		$config_fields['source']['val'] = $this->file_sql_item->getHtmlPath();
		
		// validate or save form
		$msg_string = '';
		if ($form->wasSend())
		{
			// check if invalid files are allowed
			switch($this->config_area['allow_invalid_filenames'])
			{
				// no: check by normal validation
				case 0:
					break;
				// yes: disable validation
				case 1:
					unset($config_fields['filename']['validation']['filename']);
					break;
				// correct filename: disable validation and correct filename in file_sql_item
				case 2:
					unset($config_fields['filename']['validation']['filename']);
					break;
			}
			
			// validate form
			$msg_string = $this->_validateConfigFields($config_fields);
			
			$fsm = sf_api('LIB', 'FilesystemManipulation');
			$extension = $fsm->getPathinfo($config_fields['filename']['val'], 'extension');
			unset($fsm);

			// validation errors found, so mark them as errors
			if($msg_string != '')
			{
				$msg_string = 'error_'.$msg_string;
			}
			// forbidden file extension
			else if($this->_isForbiddenFileExtension($extension) == TRUE)
			{
				$msg_string = 'error_forbidden_file_extension';
			}
			// save
			else 
			{
				$filedata = $this->_getItemFieldArrayFromConfigFields($config_fields);
				$filedata['area'] = $this->config_area['area_name'];
				$filedata['idclient'] = $this->config_area['idclient'];
				
				try
				{
					$move = $this->req->asBoolean('move');
					$success = FALSE;
					$msgcode = '';
					if($move == FALSE && $this->file_sql_item->copy($filedata) == TRUE)
					{
						$filedata['path'] = $this->file_sql_item->getRelativePath();
						$this->_setUserLogMessage('copy_file_success', $filedata);
						$msgcode = 'ok_copy_file_success';
						$success = TRUE;
					}
					else if($move == TRUE && $this->file_sql_item->move($filedata) == TRUE)
					{
						$filedata['path'] = $this->file_sql_item->getRelativePath();
						$this->_setUserLogMessage('move_file_success', $filedata);
						$msgcode = 'ok_move_file_success';
						$success = TRUE;
					}
					
					if($success == TRUE) 
					{
						// redirect to tableview
						if ($form->saveWasPressed())
						{
							$this->_indexRedirect($msgcode);
						}
						// apply was pressed
						else
						{
							$this->url->urlAddModifyParams(array('msgcode' => $msgcode, 'id' => $this->file_sql_item->getId()));
							$this->http_header->redirect($this->url->urlGet(array('area' => $this->config_area['area_name'].'_copy_file') ) );
						}
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
						default:
							$code = 'info';
					}
					
					$lng = $this->lng->get($this->config_area['area_name'].'_'.$e->getMessage());
					$msg_string = $code.'_';
					$msg_string .= ($lng != '') ? $lng : $e->getMessage();
					
					$filedata['path'] = $this->file_sql_item->getRelativePath();
					$this->_setUserLogMessage($e->getMessage(), $filedata, $code);
				}
			}
		}
		
		// build directory tree
		$config_fields['iddirectory']['tree'] = $this->_getDirectoryChooserTree($this->params['iddirectory'], array(), $this->config_area['files_in_root']);
		
		// build form
		$this->_buildFormFromConfigFields($config_fields, $form);
		
		$backend_area = sf_api('VIEW', 'BackendArea');
		$backend_area->addCmsHeader($this->config_area['js_lang']);
		$backend_area->addFooter();
		
		// error or warning templates
		$msg = $this->_getMessage($this->req->req('msgcode', $msg_string));
		if($msg !== FALSE)
		{
			$backend_area->addMessage($msg['type'], $msg['message']);
		}
		
		$backend_area->addTemplateVar('TITLE', $this->lng->get($this->config_area['area_name'].'_area_copy_file'), 'RIGHTPANE.TITLE');
		$backend_area->addTemplateVar('RIGHTPANE', $form, 'RIGHTPANE');
		
		$backend_area->generate();
		return $backend_area->get();
	}
	
	/**
	 * Overlay content with special form to upload a file. 
	 * @return void
	 */
	public function uploadFile()
	{
		// check perm for action
		// uses iddirectory as id because perm type is directory and reset parent id
		if($this->file_sql_item->hasPerm('upload', $this->params['iddirectory'], 0) == FALSE)
		{
			$this->_setUserLogMessage('permission_denied', array('id' => $this->params['iddirectory'], 'path' => $this->file_sql_item->getRelativePath()), 'debug');
			return $this->lng->get($this->config_area['area_name'].'_permission_denied');
		}
		
		$overlay = sf_api('VIEW', 'Overlay');
		$overlay->loadTemplatefile('overlay_upload.tpl');
		
		$overlay->addTemplateVar('TITLE', $this->lng->get($this->config_area['area_name'].'_area_upload_file'));
		
		$skinpath = 'tpl/'.$this->cfg->cms('skin');
		$metadata = array();
		$metadata['uploader'] = $skinpath.'/swf/uploadify.swf';
		$metadata['script'] = urlencode($this->url->urlGet(array('area' => $this->config_area['area_name'].'_do_upload_file', 'msgcode' => '')) );
		$metadata['scriptData'] = array($this->cfg->sess()->name => $this->cfg->sess()->id);
		$metadata['cancelImg'] = $skinpath.'/img/but_delete.gif';
		//$metadata['buttonImg'] = $skinpath.'/img/bg_button_uploadify.gif';
		//$metadata['rollover'] = true;
		$metadata['width'] = 90;
		$metadata['height'] = 22;
		//$metadata['buttonText'] = $this->lng->get($this->config_area['area_name'].'_upload_browse');
		$metadata['hideButton'] = true;
		$metadata['wmode'] = 'transparent';
		$metadata['folder'] = $this->params['iddirectory'];
		$metadata['queueID'] = 'upload_file_queue';
		//$metadata['auto'] = true;
		$metadata['multi'] = true;
		$metadata['fileDataName'] = 'sf_upload';
		
		if(count($this->config_area['allowed_files']) > 0 && strlen($this->config_area['allowed_files'][0]) > 0)
		{
			$metadata['fileExt'] = $metadata['fileDesc'] = '*.'.implode(';*.', $this->config_area['allowed_files']);
		}

		$overlay->addMetadata($metadata);
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$max_filesize = $this->_convertBytes(ini_get('upload_max_filesize'));
		$overlay->addTemplateVar('TOTALSIZE', $fsm->readablizeBytes($max_filesize));
		unset($fsm);
		
		$overlay->addTemplateVar('UPLOAD_FORM_ACTION', $this->url->urlGet(array('area' => $this->config_area['area_name'].'_do_upload_file', 'msgcode' => '')));
		$overlay->addTemplateVar('DIRECTORYCHOOSER_VAL', (string)$this->params['iddirectory']);
		$overlay->addTemplateVar('DIRECTORYCHOOSER_TREE', $this->_getDirectoryChooserTree($this->params['iddirectory'], array(), $this->config_area['files_in_root']));
		
		$lang['LANG_TOTALSIZE'] = $this->lng->get($this->config_area['area_name'].'_upload_totalsize');
		$lang['LANG_BROWSE'] = $this->lng->get($this->config_area['area_name'].'_upload_browse');
		$lang['LANG_NOFILESSELECTED'] = $this->lng->get($this->config_area['area_name'].'_upload_nofilesselected');
		$lang['LANG_CLEARQUEUE'] = $this->lng->get($this->config_area['area_name'].'_upload_clearqueue');
		$lang['LANG_UPLOAD_MODE_HTML'] = $this->lng->get($this->config_area['area_name'].'_upload_mode_html');
		$lang['LANG_UPLOAD_MODE_HTML_TOOLTIP'] = $this->lng->get($this->config_area['area_name'].'_upload_mode_html_tooltip');
		$lang['LANG_UPLOAD_MODE_FLASH'] = $this->lng->get($this->config_area['area_name'].'_upload_mode_flash');
		$lang['LANG_UPLOAD_EXTRACT_FILES'] = $this->lng->get($this->config_area['area_name'].'_upload_extract_files');
		$lang['LANG_UPLOAD'] = $this->lng->get($this->config_area['area_name'].'_upload_upload');
		$lang['LANG_CANCEL'] = $this->lng->get($this->config_area['area_name'].'_upload_cancel');
		$lang['LANG_SHOW_MESSAGES'] = $this->lng->get($this->config_area['area_name'].'_upload_show_messages');
		$lang['LANG_MESSAGES_ALL'] = $this->lng->get($this->config_area['area_name'].'_upload_messages_all');
		$lang['LANG_MESSAGES_ERROR'] = $this->lng->get($this->config_area['area_name'].'_upload_messages_error');
		$lang['LANG_MESSAGES_WARNING'] = $this->lng->get($this->config_area['area_name'].'_upload_messages_warning');
		$lang['LANG_MESSAGES_OK'] = $this->lng->get($this->config_area['area_name'].'_upload_messages_ok');
		$lang['LANG_CLOSE'] = $this->lng->get($this->config_area['area_name'].'_upload_close');
		$overlay->addTemplateVar('', $lang);
		
		$overlay->generate();
		return $overlay->get();
	}
	
	/**
	 * Uploads a file and prints succuess or failure 
	 * @return void
	 */
	public function doUploadFile()
	{
		// name must be equal to $metadata['fileDataName'] in upload().
		$files = $this->req->files('sf_upload');
		
		if(!is_array($files['error']))
		{
			$files_tmp = array();
			$files_tmp['name'][0] = $files['name'];
			$files_tmp['type'][0] = $files['type'];
			$files_tmp['tmp_name'][0] = $files['tmp_name'];
			$files_tmp['error'][0] = $files['error'];
			$files_tmp['size'][0] = $files['size'];
			$files = $files_tmp;
			unset($files_tmp);
		}
		
		// default upload mode is flash
		$upload_mode = $this->req->req('upload_mode', 'flash');
		$extractfiles = $this->req->asBoolean('extractfiles');
		
		$filedata = array();
		$filedata['area'] = $this->config_area['area_name'];
		$filedata['idclient'] = $this->config_area['idclient'];
		$filedata['idlang'] = $this->config_area['idlang'];
		
		if($upload_mode == 'flash')
		{
			// folder param should be the iddirectory but is /localpath/backend/<id>
			$folder = explode('/', $this->req->req('folder', 0));
			$filedata['iddirectory'] = (int)$folder[ count($folder)-1 ];
		}
		else
		{
			$filedata['iddirectory'] = $this->req->req('destination', 0);
		}
		
		$catched_messages = array(
			'type' => 'upload_'.$upload_mode
		);
		$ok = $error = 0;
		
		// check perm for upload
		$have_perm['upload_directory'] = ($this->directory_sql_item->hasPerm('upload', $filedata['iddirectory']) == TRUE);
		// uses iddirectory as id because perm type is directory and reset parent id
		$have_perm['upload_file'] = ($this->file_sql_item->hasPerm('upload', $filedata['iddirectory'], 0) == TRUE);
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$archive = sf_api('LIB', 'Archive');
		
		foreach ($files['error'] as $key => $error)
		{
			if($have_perm['upload_file'] == FALSE)
			{
				if($upload_mode == 'flash')
				{
					$msg['error'] = $this->lng->get($this->config_area['area_name'].'_no_rights');
					return json_encode($msg);
				}
				else
				{
					$catched_messages[$files['name'][$key]] = 'error_no_rights';
					++$error;
				}
			}
			else if ($error == UPLOAD_ERR_OK)
			{
				$this->file_sql_item = sf_api('MODEL', 'FileSqlItem');
				
				$tmp_name = $files['tmp_name'][$key];
				$mimetype = $files['type'][$key];
				$filedata['filename'] = $files['name'][$key];
				$filedata['filesize'] = $files['size'][$key];
				$extension = $fsm->getPathinfo($filedata['filename'], 'extension');
				
				// check if file extension is forbidden
				if($this->_isForbiddenFileExtension($extension) == TRUE)
				{
					if($upload_mode == 'flash')
					{
						$msg['error'] = $this->lng->get($this->config_area['area_name'].'_forbidden_file_extension');
						return json_encode($msg);
					}
					else
					{
						$catched_messages[$filedata['filename']] = 'error_forbidden_file_extension';
						++$error;
						continue;
					}
				}
				
				try
				{
					if($extractfiles == TRUE && in_array($extension, $this->config_area['extract_compressed_files']))
					{
						$hash = md5(time());
						$temp_in_path = $this->config_area['temp_in_path'].'/'.$hash;
						$archive_name = $this->config_area['temp_in_path'].'/'.$hash.'.'.$extension;
						$fsm->createDirectory($temp_in_path);
						$fsm->moveUploadedFile($tmp_name, $archive_name);
						
						$archive_to_extract = $archive_name.'/';
						$archive::extract($archive_to_extract, $temp_in_path);
						
						// if any sub directory is extracted, check perm to upload directories 
						if($fsm->hasSubdirectories($temp_in_path) == TRUE && $have_perm['upload_directory'] == FALSE)
						{
							$result = array('error' => 1);
						}
						else
						{
							$result = $this->_doDirectoryScan(
								$temp_in_path,
								$filedata['iddirectory'],
								array(
									'recursive' => TRUE,
									'generate_thumbnails' => TRUE
								)
							);
						}
						
						$fsm->deleteFile($archive_name);
						$fsm->deleteDirectoryRecursive($temp_in_path);
						
						if($upload_mode == 'flash')
						{
							if($result['error'] <= 0)
							{
								$msg['ok'] = $this->lng->get($this->config_area['area_name'].'_upload_uncompress_file_success');
							}
							else
							{
								$msg['error'] = $this->lng->get($this->config_area['area_name'].'_upload_uncompress_file_failed');
							}
							return json_encode($msg);
						}
						else
						{
							if($result['error'] <= 0)
							{
								$catched_messages[$filedata['filename']] = 'ok_upload_uncompress_file_success';
								++$ok;
							}
							else
							{
								$catched_messages[$filedata['filename']] = 'error_upload_uncompress_file_failed';
								++$error;
							}
						}
					}
					else if($this->file_sql_item->upload($filedata, $tmp_name, $mimetype))
					{
						// send okay message to uploadify
						if($upload_mode == 'flash')
						{
							$msg['ok'] = $this->lng->get($this->config_area['area_name'].'_upload_file_success');
							//$msg['ok'] = $this->url->urlGet(array('area' => $this->config_area['area_name'].'_index', 'msgcode' => 'ok_'.$this->config_area['area_name'].'_upload_file_success', 'iddirectory' => $filedata['iddirectory'] ) );
							// exit the function, uploadify sends only one file per request 
							return json_encode($msg);
						}
						else
						{
							$catched_messages[$filedata['filename']] = 'ok_upload_file_success';
							++$ok;
						}
					}
				}
				catch(Exception $e)
				{
					if($upload_mode == 'flash')
					{
						$msg['error'] = $this->lng->get($this->config_area['area_name'].'_'.$e->getMessage());
						return json_encode($msg);
					}
					else
					{
						$catched_messages[$filedata['filename']] = 'error_'.$e->getMessage();
						++$error;
					}
				}
			}
			else
			{
				if($upload_mode == 'flash')
				{
					$msg['error'] = $this->lng->get($this->config_area['area_name'].'_upload_file_failed');
					return json_encode($msg);
				}
				else
				{
					$catched_messages[$files['name'][$key]] = 'error_upload_file_failed';
					++$error;
				}
			}
		}
		
		if($upload_mode == 'flash')
		{
			$msg['error'] = $this->lng->get($this->config_area['area_name'].'_upload_file_failed');
			return json_encode($msg);
			exit;
		}
		else
		{
			if($error > 0)
			{
				// store catched messages to session
				$msghash = md5(time()); 
				$this->_setVarToSession($msghash, $catched_messages, 'msg', TRUE);
				$msgcode = ($ok > 0) ? 'warning_some_uploads_failed' : 'error_all_uploads_failed';
			}
			else
			{
				$msghash = '';
				$msgcode = 'ok_upload_successful';
			}
			
			//print_r($catched_messages);
			
			$this->http_header->redirect($this->url->urlGet(array('area' => $this->config_area['area_name'].'_index', 'msghash' => $msghash, 'msgcode' => $msgcode) ) );
		}
		
	}
	
	/**
	 * Forces the download of a single file by id
	 * (retrieve from {@link $req}) and exits the script.
	 * @return void
	 */
	public function downloadFile()
	{
		//get id
		$id = (int) $this->req->req('id', 0);
		
		//load data - if loading fails redirect to index page
		if (! $this->file_sql_item->loadById($id)) 
		{
			$this->_setSystemLogMessage('file_is_not_loaded', array('id' => $id));
			$this->_indexRedirect('error_file_is_not_loaded');
		}
		
		// check perm for action
		if($this->file_sql_item->hasPerm('download') == FALSE)
		{
			$this->_setUserLogMessage('permission_denied', array('id' => $id, 'path' => $this->file_sql_item->getRelativePath()), 'debug');
			$this->_indexRedirect('error_permission_denied');
		}
		
		$this->_setUserLogMessage('download_file_success', array('id' => $id, 'path' => $this->file_sql_item->getRelativePath()));
		
		$dl = sf_api('LIB', 'Download');
		$dl->forceByPath($this->file_sql_item->getPath(), array(
			'content-disposition' => 'attachment'
		));
	}
	
	/**
	 * Forces the preview of a single file by id
	 * (retrieve from {@link $req}) and exits the script.
	 * Works similar to downloadFile().
	 * @return void
	 */
	public function previewFile()
	{
		//get id
		$id = (int) $this->req->req('id', 0);
		$thumb = (int) $this->req->req('thumb', -1);
		
		//load data - if loading fails redirect to index page
		if (! $this->file_sql_item->loadById($id)) 
		{
			$this->_setSystemLogMessage('file_is_not_loaded', array('id' => $id));
			return $this->lng->get($this->config_area['area_name'].'_error_file_is_not_loaded');
		}
		
		// check perm for action
		if($this->file_sql_item->hasPerm('view') == FALSE)
		{
			$this->_setUserLogMessage('permission_denied', array('id' => $id, 'path' => $this->file_sql_item->getRelativePath()), 'debug');
			$this->_indexRedirect('error_permission_denied');
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$extension = $fsm->getPathinfo($path, 'extension');
		
		$path = $this->file_sql_item->getPath();
		
		// Get thumbnail if wanted and possible
		if( $thumb > -1 && 
			$this->file_sql_item instanceof SF_MODEL_FileSqlItem &&
			$this->file_sql_item->isThumbnailGenerationPossible() == TRUE)
		{
			$thumbdata = $this->file_sql_item->getThumbnails($thumb);
			if(array_key_exists('path', $thumbdata) == TRUE)
			{
				$path = $thumbdata['path'];
			}
		}
		
		$dl = sf_api('LIB', 'Download');
		$dl->forceByPath(
			$path,
			array('content-disposition' => 'inline')
		);
	}
	

	/* ******************
	 *  MULTIPLE
	 * *****************/
	
	/**
	 * Backend area (only rightpane) with a
	 * form to download multiple files and directories
	 * @return void
	 */
	public function downloadMultiple()
	{
		$temp_out_path = $this->config_area['temp_out_path'].'/'.md5(time());
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$fsm->createDirectory($temp_out_path);
		
		$dirtree = $this->directory_sql_tree;
		$dirtree->setIdclient( $this->config_area['idclient'] );
		//$dirtree->setIdlang( $this->config_area['idlang'] );
		$dirtree->setArea($this->config_area['area_name']);
		$dirtree->setPermCheck($this->directory_sql_item->getObjectPermType(), $this->directory_sql_item->getObjectPermId('download'), TRUE);
		$dirtree->generate();
		
		$iddirectories = $this->req->req('d', array());
		foreach($iddirectories as $iddirectory)
		{
			if ($this->directory_sql_item->loadById($iddirectory)) 
			{
				$this->_prepareDownloadDirectory($iddirectory, $temp_out_path, $dirtree);
			}
		}
		
		$idfiles = $this->req->req('f', array());
		foreach($idfiles as $idfile)
		{
			if ($this->file_sql_item->loadById($idfile)) 
			{
				$fsm->copyFile($this->file_sql_item->getPath(), $temp_out_path.'/'.$this->file_sql_item->getField('filename'));
			}
		}
		
		$id = $this->req->req('iddirectory', 0);
		if($id > 0 && $this->directory_sql_item->loadById($id))
		{
			$archivename = $this->directory_sql_item->getField('name');
		}
		else
		{
			$archivename = $this->lng->get($this->config_area['area_name'].'_base_directory');
		}
		
		$archive = sf_api('LIB', 'Archive');
		$archive::extract(
			$archive::read($temp_out_path),
			$archive::toArchive(
				$archivename.'.'.$this->config_area['download_archive_filetype'],
				$archive::toOutput()
			)
		);
		
		$fsm->deleteDirectoryRecursive($temp_out_path);
	}
	
	/**
	 * Backend area (only rightpane) with a
	 * form to copy multiple files and directories
	 * @return void
	 */
	public function copyMultiple()
	{
		$backend_area = sf_api('VIEW', 'BackendArea');
		$backend_area->addCmsHeader($this->config_area['js_lang']);
		$backend_area->addFooter();
		
		$iddirectories = $this->req->req('d', array());
		$directories = $this->req->req('directories', '');
		if(count($iddirectories) == 0 && $directories != '')
		{
			$iddirectories = explode(',', $directories);
		}
		
		$idfiles = $this->req->req('f', array());
		$files = $this->req->req('files', '');
		if(count($idfiles) == 0 && $files != '')
		{
			$idfiles = explode(',', $files);
		}
		
		$destination = (int) $this->req->req('destination', $this->params['iddirectory']);
		
		if(count($iddirectories) <= 0 && count($idfiles) <= 0)
		{
			$this->_indexRedirect();
		}
		
		$form = sf_api('VIEW', 'Form');
		
		// assign real form vals to config fields
		$config_fields = $this->_assignValuesToConfigFields(
			$this->config_fields['copy_multiple'], 
			null, 
			$form->wasSend()
		);
		
		$config_fields['directories']['val'] = implode(',', $iddirectories);
		$config_fields['files']['val'] = implode(',', $idfiles);
		$config_fields['destination']['val'] = $destination;
		if(count($iddirectories) == 1)
		{
			$config_fields['selection']['val'] .= count($iddirectories).' '.$this->lng->get($this->config_area['area_name'].'_multiple_directory');
		}
		else if(count($iddirectories) > 1)
		{
			$config_fields['selection']['val'] .= count($iddirectories).' '.$this->lng->get($this->config_area['area_name'].'_multiple_directories');
		}
		
		if(count($idfiles) > 0 && count($iddirectories) > 0)
		{
			$config_fields['selection']['val'] .= $this->lng->get($this->config_area['area_name'].'_multiple_and');
		}
		
		if(count($idfiles) == 1)
		{
			$config_fields['selection']['val'] .= count($idfiles).' '.$this->lng->get($this->config_area['area_name'].'_multiple_file');
		}
		else if(count($idfiles) > 1)
		{
			$config_fields['selection']['val'] .= count($idfiles).' '.$this->lng->get($this->config_area['area_name'].'_multiple_files');
		}
		
		
		// validate or save form
		$msg_string = '';
		if ($form->wasSend())
		{
			// validate form
			$msg_string = $this->_validateConfigFields($config_fields);
			
			// validation errors found, so mark them as errors
			if($msg_string != '')
			{
				$msg_string = 'error_'.$msg_string;
			}
			// save
			else 
			{
				$catched_messages = array(
					'type' => 'copy_multiple'
				);
				$ok = $error = 0;
				$move = $this->req->asBoolean('move');
				
				$directorydata = array();
				$directorydata['area'] = $this->config_area['area_name'];
				$directorydata['idclient'] = $this->config_area['idclient'];
				$directorydata['parentid'] = $config_fields['destination']['val'];
				
				foreach($iddirectories as $iddirectory)
				{
					if (! $this->directory_sql_item->loadById($iddirectory)) 
					{
						$this->_setSystemLogMessage('directory_is_not_loaded', array('multiple' => TRUE, 'id' => $iddirectory));
						$catched_messages['directory'][$iddirectory] = 'error_directory_is_not_loaded';
						++$error;
						continue;
					}
		
					// check perm for action
					if($this->directory_sql_item->hasPerm('copy') == FALSE)
					{
						$this->_setUserLogMessage('permission_denied', array('multiple' => TRUE, 'id' => $iddirectory, 'path' => $this->directory_sql_item->getRelativePath()), 'debug');
						$catched_messages['directory'][$iddirectory] = 'error_permission_denied';
						++$error;
						continue;
					}
					
					try
					{
						if($move == FALSE && $this->directory_sql_item->copy($directorydata) == TRUE)
						{
							$directorydata['path'] = $this->directory_sql_item->getRelativePath();
							$this->_setUserLogMessage('copy_directory_success', $directorydata);
							$catched_messages['directory'][$iddirectory] = 'ok_copy_directory_success';
							++$ok;
						}
						else if($move == TRUE && $this->directory_sql_item->move($directorydata) == TRUE)
						{
							$directorydata['path'] = $this->directory_sql_item->getRelativePath();
							$this->_setUserLogMessage('move_directory_success', $directorydata);
							$catched_messages['directory'][$iddirectory] = 'ok_move_directory_success';
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
						$catched_messages['directory'][$iddirectory] = $code.'_'.$e->getMessage();
						$directorydata['path'] = $this->directory_sql_item->getRelativePath();
						$this->_setUserLogMessage($e->getMessage(), $directorydata, $code);
						++$error;
					}
				}
				
				$filedata = array();
				$filedata['area'] = $this->config_area['area_name'];
				$filedata['idclient'] = $this->config_area['idclient'];
				$filedata['iddirectory'] = $config_fields['destination']['val'];
				
				foreach($idfiles as $idfile)
				{
					if (! $this->file_sql_item->loadById($idfile)) 
					{
						$this->_setSystemLogMessage('file_is_not_loaded', array('multiple' => TRUE, 'id' => $idfile));
						$catched_messages['file'][$idfile] = 'error_file_is_not_loaded';
						++$error;
						continue;
					}
		
					// check perm for action
					if($this->file_sql_item->hasPerm('copy') == FALSE)
					{
						$this->_setUserLogMessage('permission_denied', array('multiple' => TRUE, 'id' => $idfile, 'path' => $this->file_sql_item->getRelativePath()), 'debug');
						$catched_messages['file'][$idfile] = 'error_permission_denied';
						++$error;
						continue;
					}
					
					try
					{
						if($move == FALSE && $this->file_sql_item->copy($filedata) == TRUE)
						{
							$filedata['path'] = $this->file_sql_item->getRelativePath();
							$this->_setUserLogMessage('copy_file_success', $filedata);
							$catched_messages['file'][$idfile] = 'ok_copy_file_success';
							++$ok;
						}
						else if($move == TRUE && $this->file_sql_item->move($filedata) == TRUE)
						{
							$filedata['path'] = $this->file_sql_item->getRelativePath();
							$this->_setUserLogMessage('move_file_success', $filedata);
							$catched_messages['file'][$idfile] = 'ok_move_file_success';
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
						$catched_messages['file'][$idfile] = $code.'_'.$e->getMessage();
						$filedata['path'] = $this->file_sql_item->getRelativePath();
						$this->_setUserLogMessage($e->getMessage(), $filedata, $code);
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
		}
		
		// build directory tree
		$config_fields['destination']['tree'] = $this->_getDirectoryChooserTree($destination, $iddirectories, $this->config_area['files_in_root']);
		
		// build form
		$this->_buildFormFromConfigFields($config_fields, $form);
		
		// error or warning templates
		$msg = $this->_getMessage($this->req->req('msgcode', $msg_string));
		if($msg !== FALSE)
		{
			$backend_area->addMessage($msg['type'], $msg['message']);
		}
		
		$backend_area->addTemplateVar('TITLE', $this->lng->get($this->config_area['area_name'].'_area_copy_multiple'), 'RIGHTPANE.TITLE');
		$backend_area->addTemplateVar('RIGHTPANE', $form, 'RIGHTPANE');
		
		$backend_area->generate();
		return $backend_area->get();
	}
	
	
	/**
	 * Backend area (only rightpane) with a
	 * form to delete multiple files and directories
	 * @return void
	 */
	public function deleteMultiple()
	{
		$iddirectories = $this->req->req('d', array());
		$idfiles = $this->req->req('f', array());
		
		if(count($iddirectories) <= 0 && count($idfiles) <= 0)
		{
			$this->_indexRedirect();
		}
		
		$catched_messages = array(
			'type' => 'delete_multiple'
		);
		$ok = $error = 0;
		
		foreach($iddirectories as $iddirectory)
		{
			if (! $this->directory_sql_item->loadById($iddirectory)) 
			{
				$this->_setSystemLogMessage('directory_is_not_loaded', array('multiple' => TRUE, 'id' => $iddirectory));
				$catched_messages['directory'][$iddirectory] = 'error_directory_is_not_loaded';
				++$error;
				continue;
			}
			
			// check perm for action
			if($this->directory_sql_item->hasPerm('delete') == FALSE)
			{
				$this->_setUserLogMessage('permission_denied', array('multiple' => TRUE, 'id' => $iddirectory, 'path' => $this->directory_sql_item->getRelativePath()), 'debug');
				$catched_messages['directory'][$iddirectory] = 'error_permission_denied';
				++$error;
				continue;
			}
			
			try
			{
				if($this->directory_sql_item->delete() == TRUE)
				{
					$this->_setUserLogMessage('delete_directory_success', array('multiple' => TRUE, 'id' => $iddirectory, 'path' => $this->directory_sql_item->getRelativePath()));
					$catched_messages['directory'][$iddirectory] = 'ok_delete_directory_success';
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
				$catched_messages['directory'][$iddirectory] = $code.'_'.$e->getMessage();
				$this->_setUserLogMessage($e->getMessage(), array('multiple' => TRUE, 'id' => $iddirectory, 'path' => $this->directory_sql_item->getRelativePath()), $code);
				++$error;
			}
		}
		
		foreach($idfiles as $idfile)
		{
			if (! $this->file_sql_item->loadById($idfile)) 
			{
				$this->_setSystemLogMessage('file_is_not_loaded', array('multiple' => TRUE, 'id' => $idfile));
				$catched_messages['file'][$idfile] = 'error_file_is_not_loaded';
				++$error;
				continue;
			}
		
			// check perm for action
			if($this->file_sql_item->hasPerm('delete') == FALSE)
			{
				$this->_setUserLogMessage('permission_denied', array('multiple' => TRUE, 'id' => $idfile, 'path' => $this->file_sql_item->getRelativePath()), 'debug');
				$catched_messages['file'][$idfile] = 'error_permission_denied';
				++$error;
				continue;
			}
			
			try
			{
				if($this->file_sql_item->delete() == TRUE)
				{
					$this->_setUserLogMessage('delete_file_success', array('multiple' => TRUE, 'id' => $idfile, 'path' => $this->file_sql_item->getRelativePath()));
					$catched_messages['file'][$idfile] = 'ok_delete_file_success';
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
				$catched_messages['file'][$idfile] = $code.'_'.$e->getMessage();
				$this->_setUserLogMessage($e->getMessage(), array('multiple' => TRUE, 'id' => $idfile, 'path' => $this->file_sql_item->getRelativePath()), $code);
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


	/* ******************
	 *  PROTECTED
	 * *****************/
	
	/**
	 * THIS FUNCTION IS DEPRECATED
	 * Remove the function when it isn't used by packed file upload
	 * in doUploadFile() anymore!
	 * 
	 * Iterates through the $source_path and checks if directories and
	 * files exists in database ($destination_iddirectory).
	 * If directory or file does not exist it will added to database.
	 * To synchronize between the $destination_iddirectory and
	 * corrosponding filesystem directory, $source_path must be an empty string.
	 * With $options['recursive'] = TRUE (default: FALSE) all sub directories
	 * and files are scaned.
	 * @param string $source_path
	 * @param integer $destination_iddirectory
	 * @param array $options
	 * @return array Returns array with number of errors and ok plus a counter array
	 */
	protected function _doDirectoryScan($source_path, $destination_iddirectory, $options)
	{
		if($destination_iddirectory == 0)
		{
			$destination['iddirectory'] = $destination_iddirectory;
			$destination['relative_path'] = '';
			$destination['path'] = substr($this->config_area['rootdirectory_path'], 0, -1);
		}
		else if($destination_iddirectory > 0)
		{
			if( $this->directory_sql_item->loadById($destination_iddirectory) === FALSE ||
				$this->directory_sql_item->getField('area') != $this->config_area['area_name'])
			{
				return FALSE;
			}
			$destination['iddirectory'] = $destination_iddirectory;
			$destination['relative_path'] = $this->directory_sql_item->getRelativePath();
			$destination['path'] = substr($this->directory_sql_item->getPath(), 0, -1);
		}
		else
		{
			return FALSE;
		}
		
		// set source path and options
		if($source_path == '')
		{
			$source['path'] = $destination['path'];
			$options['copy_file'] = (!array_key_exists('copy_file', $options)) ? FALSE : $options['copy_file'];
			$options['create_only_db'] = (!array_key_exists('create_only_db', $options)) ? TRUE : $options['create_only_db'];
			$options['generate_thumbnails'] = (!array_key_exists('generate_thumbnails', $options)) ? FALSE : $options['generate_thumbnails'];
		}
		else
		{
			$source['path'] =  $source_path;
			$options['copy_file'] = (!array_key_exists('copy_file', $options)) ? TRUE : $options['copy_file'];
			$options['create_only_db'] = (!array_key_exists('create_only_db', $options)) ? FALSE : $options['create_only_db'];
			$options['generate_thumbnails'] = (!array_key_exists('generate_thumbnails', $options)) ? TRUE : $options['generate_thumbnails'];
		}
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		if($fsm->isDir($source['path']) == FALSE)
		{
			return FALSE;
		}
		unset($fsm);
		
		$dir_iterator = new RecursiveDirectoryIterator($source['path']);
		
		if($options['recursive'] == TRUE)
		{
			$dir_iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
		}
		
		$directories = array();
		$added_directories = array();
		$thumbnails_for_files = array();
		$error = $ok = 0;
		$counter = array(
			'directories_added' => 0,
			'directories_equal' => 0,
			'files_added' => 0,
			'files_equal' => 0
		);
		
		$directorydata = array();
		$directorydata['area'] = $this->config_area['area_name'];
		$directorydata['idclient'] = $this->config_area['idclient'];
		$directorydata['idlang'] = $this->config_area['idlang'];
		
		foreach ($dir_iterator as $filename => $current)
		{
			if($current->isDir())
			{
				$found = FALSE;
				foreach($this->config_area['scan_ignore_directories'] as $ignore_directory)
				{
					if(stripos($current->getPathName(), $ignore_directory) !== FALSE)
					{
						$found = TRUE;
						break;
					}
				}
				if($found == TRUE)
				{
					//echo "Skip directory: ".$current->getPathName()."<br />\n";
					continue;
				}
				unset($found);
				
				$source['relative_path'] = str_replace(array($source['path'], '\\'), array('', '/'), $current->getPathName());
				$source['relative_path'] = substr($source['relative_path'], 1);
				$source['relative_parent_path'] = str_replace(array($source['path'], '\\'), array('', '/'), $current->getPath());
				$source['relative_parent_path'] = substr($source['relative_parent_path'], 1);
				
				$dirname = $destination['relative_path'].$source['relative_path'];
				$dirname .= (substr($dirname, -1) != '/') ? '/' : '';
				
				//echo "Directory: ".$current->getPath()." - ".$source['relative_path']."<br />\n";
				
				if($this->directory_sql_item->loadByDirname($dirname, $directorydata['area']) == FALSE)
				{
					$parent_dirname = $destination['relative_path'].$source['relative_parent_path'];
					$parent_dirname .= (substr($parent_dirname, -1) != '/') ? '/' : '';
					
					// get parentid if not already stored
					if(array_key_exists($source['relative_parent_path'], $directories))
					{
						$directorydata['parentid'] = $directories[ $source['relative_parent_path'] ];
					}
					else if($this->directory_sql_item->loadByDirname($parent_dirname, $directorydata['area']) == TRUE)
					{
						$directorydata['parentid'] = $this->directory_sql_item->getId();
					}
					else
					{
						$directorydata['parentid'] = $destination['iddirectory'];
					}
					
					$directorydata['dirname'] = $dirname;
					$directorydata['name'] = $current->getFilename();
					//$directorydata['status'] = 2;
					//print_r($directorydata);
					
					try
					{
						$new_directory = sf_api('MODEL', 'DirectorySqlItem');
						$success = FALSE;
						if($options['create_only_db'] == TRUE)
						{
							$success = $new_directory->createOnlyDb($directorydata);
						}
						else
						{
							$success = $new_directory->create($directorydata);
						}
						
						if($success == TRUE)
						{
							$directories[ $source['relative_path'] ] = $new_directory->getId();
							$added_directories[] = $new_directory->getId();
							++$ok;
							++$counter['directories_added'];
							//echo "Create directory: ".$directorydata['dirname']." - ".$directories[ $source['relative_path'] ]."<br />";
						}
						else
						{
							++$counter['directories_equal'];
						}
						unset($new_directory);
					}
					catch(Exception $e)
					{
						return $e->getMessage()."<br />";
						++$counter['directories_equal'];
						++$error;
					}
				}
				else
				{
					// status = 2: not in queue and ready worked
					//$this->directory_sql_item->setField('status', 2);
					//$this->directory_sql_item->save();
					
					++$counter['directories_equal'];
					
					$directories[ $source['relative_path'] ] = $this->directory_sql_item->getId();
				}
			}
		}
		//print_r($directories);
		//echo "<br />";
		
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		$filedata = array();
		$filedata['area'] = $this->config_area['area_name'];
		$filedata['idclient'] = $this->config_area['idclient'];
		$filedata['idlang'] = $this->config_area['idlang'];
		
		foreach ($dir_iterator as $filename => $current)
		{
			if($current->isFile())
			{
				$found = FALSE;
				foreach($this->config_area['scan_ignore_directories'] as $ignore_directory)
				{
					if(stripos($current->getPathName(), $ignore_directory) !== FALSE)
					{
						$found = TRUE;
						break;
					}
				}
				if($found == TRUE)
				{
					//echo "Skip directory: ".$current->getPathName()."<br />\n";
					continue;
				}
				unset($found);
				
				$found = FALSE;
				foreach($this->config_area['scan_ignore_files'] as $ignore_file)
				{
					if(stripos($current->getPathName(), $ignore_file) !== FALSE)
					{
						$found = TRUE;
						break;
					}
				}
				if($found == TRUE)
				{
					//echo "Skip file: ".$current->getPathName()."<br />\n";
					continue;
				}
				unset($found);
				
				$source['relative_path'] = str_replace(array($source['path'], '\\'), array('', '/'), $current->getPathName());
				$source['relative_path'] = substr($source['relative_path'], 1);
				$source['relative_parent_path'] = str_replace(array($source['path'], '\\'), array('', '/'), $current->getPath());
				$source['relative_parent_path'] = substr($source['relative_parent_path'], 1);
				
				$dirname = $destination['relative_path'].$source['relative_path'];
				
				//echo "File: ".$current->getPathName()."<br />\n";
				
				if(array_key_exists($source['relative_parent_path'], $directories))
				{
					$filedata['iddirectory'] = $directories[ $source['relative_parent_path'] ];
				}
				else
				{
					$filedata['iddirectory'] = $destination['iddirectory'];
				}
				
				$is_loaded = $this->file_sql_item->loadByFilename($current->getFilename(), $filedata['iddirectory']);
				
				$filedata['filename'] = $fsm->cleanFilename($current->getFilename());
				//$filedata['status'] = 2; // status = 2: not in queue and ready worked
				
				try
				{
					if($is_loaded == TRUE && strcmp($filedata['filename'], $current->getFilename()) != 0)
					{
						//print_r($filedata);
						if($this->file_sql_item->edit($filedata) == TRUE)
						{
							if($this->file_sql_item->isThumbnailGenerationPossible() == TRUE)
							{
								$thumbnails_for_files[] = $this->file_sql_item->getId();
							}
							
							++$ok;
							++$counter['files_equal'];
							//echo "Renamed file: ".$filedata['filename']."<br />";
						}
					}
					else if($is_loaded == FALSE)
					{
						//print_r($filedata);
						
						$new_file = sf_api('MODEL', 'FileSqlItem');
						if($new_file->addByDirectoryPath($filedata, $current->getPathName(), $options['copy_file'], $options['generate_thumbnails']) == TRUE)
						{
							if($new_file->isThumbnailGenerationPossible() == TRUE)
							{
								$thumbnails_for_files[] = $new_file->getId();
							}
							
							++$ok;
							++$counter['files_added'];
							//echo "Created file: ".$filedata['filename']."<br />";
						}
						unset($new_file);
					}
					else
					{
						// status = 2: not in queue and ready worked
						//$this->file_sql_item->setField('status', 2);
						//$this->file_sql_item->save();
						
						if($this->file_sql_item->isThumbnailGenerationPossible() == TRUE)
						{
							$thumbnails_for_files[] = $this->file_sql_item->getId();
						}
						
						++$counter['files_equal'];
					}
				}
				catch(Exception $e)
				{
					++$error;
				}
			}
		}
		
		return array(
			'error' => $error,
			'ok' => $ok,
			'counter' => $counter,
			'added_directories' => $added_directories,
			'thumbnails_for_files' => $thumbnails_for_files
		);
	}
	
	/**
	 * Builds the table row to go to parentdirectory.
	 * The new row is added to the given $table. 
	 * @param SF_VIEW_Table $table
	 * @return SF_VIEW_Table Returns the modified $table
	 */
	protected function _buildParentDirectoryRow($table)
	{
		// add link for parentdirectory
		if($this->params['iddirectory'] > 0)
		{
			$html = sf_api('LIB', 'HtmlHelper');
			$attributes = array();
			$attributes['actionicon']['class'] = 'ajaxdeeplink action';
			$attributes['actionicon']['rel'] = 'rightpane';
			$attributes['actionlink']['class'] = 'ajaxdeeplink action';
			$attributes['actionlink']['rel'] = 'rightpane';
			$attributes['tr']['class'] = 'tblrbgcolorf2';
			
			$fields = $this->config_fields['index_directory'];
			$fields['name']['renderer']['actions']['icon'] = array(
				'render_as' => 'icon',
				'text' => $this->lng->get($this->config_area['area_name'].'_goto_parentdirectory'),
				'url' => $this->url->urlGet(
					array(
						'area' => $this->config_area['area_name'].'_index',
						'iddirectory' => $this->directory_sql_item->getField('parentid')
					)
				),
				'icon' => 'but_folder_off2.gif',
				'attributes' => $html->attributesArrayToHtmlString($attributes['actionicon'])
			);
			$fields['name']['renderer']['actions']['link'] = array(
				'render_as' => 'text',
				'text' => $this->lng->get($this->config_area['area_name'].'_goto_parentdirectory'),
				'url' => $this->url->urlGet(
					array(
						'area' => $this->config_area['area_name'].'_index',
						'iddirectory' => $this->directory_sql_item->getField('parentid')
					)
				),
				'attributes' => $html->attributesArrayToHtmlString($attributes['actionlink'])
			);
			$fields['checkbox']['format'] = array('pattern' => ' ');
			$fields['description']['format'] = array('pattern' => '-');
			$fields['lastmodified']['format'] = array('pattern' => '-');
			
			unset(
				$fields['checkbox']['renderer'],
				$fields['name']['fieldname'][1],
				$fields['description']['fieldname'][1],
				$fields['action']['renderer']
			);
			
			$this->directory_sql_item->loadById($this->params['iddirectory']);
			
			$table->buildTableRow(
				$this->directory_sql_item,
				$fields,
				array(
					'tr_attributes' => $html->attributesArrayToHtmlString($attributes['tr'])
				)
			);
		}
		
		return $table;
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
					if($toolbarconfig[$id]['perm']['obj'] == 'directory')
					{
						$obj = $this->directory_sql_item;
					}
					else if($toolbarconfig[$id]['perm']['obj'] == 'file')
					{
						$obj = $this->file_sql_item;
					}
					
					$parent_id = (array_key_exists('parent_id', $toolbarconfig[$id]['perm'])) ? $toolbarconfig[$id]['perm']['parent_id'] : 0;
					
					// add has_perm property to config
					$toolbarconfig[$id]['perm']['has_perm'] = ($obj->hasPerm($toolbarconfig[$id]['perm']['name'], $toolbarconfig[$id]['perm']['id'], $parent_id) == TRUE);
				}
			}
		}

		$toolbarconfig = $this->lng->replaceLangInArray($toolbarconfig, $this->config_area['area_name']);
		$toolbarconfig = $this->url->replaceUrlInArray($toolbarconfig, $this->config_area['area_name'], array('{iddirectory}', '{id}'), array($this->params['iddirectory'], $this->params['iddirectory']));
		
		return $toolbarconfig;
	}
	
	/**
	 * Build the breadcrumb navigation view by given recursive $parents.
	 * @param array $parents
	 * @return SF_VIEW_Breadcrumb Returns the breadcrumb view object
	 */
	protected function _buildBreadcrumbsFromRecursiveParents($parents)
	{
		$html = sf_api('LIB', 'HtmlHelper');
		$attributes = array();
		$attributes['root']['class'] = 'ajaxdeeplink';
		$attributes['root']['rel'] = 'rightpane';
		$attributes['crumbs']['class'] = 'ajaxdeeplink';
		$attributes['crumbs']['rel'] = 'rightpane';
	
		$breadcrumbs = sf_api('VIEW', 'Breadcrumb');
		$parents = array_reverse($parents);
		$crumbs = array(
			// add root directory
			array(
				'text' => $this->lng->get($this->config_area['area_name'].'_base_directory'),
				'url' => $this->url->urlGet(array('area' => $this->config_area['area_name'].'_index', 'iddirectory' => '')),
				'attributes' => $html->attributesArrayToHtmlString($attributes['root'])
			)
		);
		
		$crumb_attributes = $html->attributesArrayToHtmlString($attributes['crumbs']);
		
		foreach($parents as $iddirectory)
		{
			if($this->directory_sql_item->loadByIdWithoutPath($iddirectory, '', 'name') == TRUE)
			{
				array_push($crumbs, array(
					'text' => $this->directory_sql_item->getField('name'),
					'url' => $this->url->urlGet(array('area' => $this->config_area['area_name'].'_index', 'iddirectory' => $iddirectory)),
					'attributes' => $crumb_attributes
				));
			}
		}
		$breadcrumbs->buildBreadcrumbsFromArray($crumbs, FALSE);
		return $breadcrumbs;
	}
	
	/**
	 * Get the directory name by given id.
	 * This function is used as callback function
	 * for tree views (e.g. function ajaxDirectoryTree() ). 
	 * @param integer $id
	 * @return Returns the directory name, otherwise FALSE.
	 */
	public function getDirectoryNameById($id)
	{
		if($this->directory_sql_item->loadByIdWithoutPath($id, '', 'name') != FALSE)
		{
			return $this->directory_sql_item->getField('name');
		}
		
		return FALSE;
	}
	
	
	/**
	 * Convert a shorthand byte value from a PHP configuration directive to an integer value
	 * @param string $value
	 * @return int
	 */
	protected function _convertBytes($value)
	{
		if ( is_numeric($value) )
		{
			return $value;
		}
		else
		{
			$value_length = strlen( $value );
			$qty = substr( $value, 0, $value_length - 1 );
			$unit = strtolower( substr( $value, $value_length - 1 ) );
			switch ( $unit ) {
				case 'k':
					$qty *= 1024;
					break;
				case 'm':
					$qty *= 1048576;
					break;
				case 'g':
					$qty *= 1073741824;
					break;
			}
			return $qty;
		}
	}
	
	/**
	 * Build the directory tree as directory chooser
	 * @param array $active_leaf
	 * @param array $ignore_leafs
	 * @return string Returns the ready build directory chooser
	 */
	protected function _getDirectoryChooserTree($active_leaf, $ignore_leafs = array(), $is_rootlink = TRUE)
	{
		// build directory tree
		$dirtree = $this->directory_sql_tree;
		$dirtree->setIdclient( $this->config_area['idclient'] );
		//$dirtree->setIdlang( $this->config_area['idlang'] );
		$dirtree->setArea($this->config_area['area_name']);
		$dirtree->setOrder('name');
		$dirtree->generate();
		
		$html = sf_api('LIB', 'HtmlHelper');
		
		$attributes = array();
		$attributes['roottree']['class'] = "treeview directorytree";
		//$attributes['roottree']['data-options'] = array('collapsed' => TRUE);
		
		$attributes['rootleaf']['class'] = "action directorytree_root";
		$attributes['rootleaf']['rel'] = "{id}";
		
		$attributes['leafs']['class'] = "action";
		$attributes['leafs']['rel'] = "{id}";
		$attributes['leafs_active']['class'] = "action active";
		$attributes['leafs_active']['rel'] = "{id}";
		
		$tree = sf_api('VIEW', 'Tree');
		$tree->setOptions(array(
			'roottree' => array(
				'attributes' => $html->attributesArrayToHtmlString($attributes['roottree'])
			),
			'rootleaf' => array(
				'text' => $this->lng->get($this->config_area['area_name'].'_base_directory'),
				'url' => (($is_rootlink === TRUE) ? '#' : ''),
				'attributes' => $html->attributesArrayToHtmlString($attributes['rootleaf'])
			),
			'leafs' => array(
				// reference on method to use
				'text' => array(
					'object' => $this,
					'function' => 'getDirectoryNameById'
				),
				'url' => '#',
				'attributes' => $html->attributesArrayToHtmlString($attributes['leafs']),
				'attributes_active' => $html->attributesArrayToHtmlString($attributes['leafs_active'])
			),
			'active_leaf' => $active_leaf,
			'ignore_leafs' => $ignore_leafs
		));
		$tree->setTreeModel($dirtree);
		$tree->buildTree(0);
		
		$tree->generate();
		return $tree->get();
	}
	
	
	/**
	 * Checks if $directoryname is set in forbidden directories.
	 * @param string $directoryname
	 * @return boolean Return TRUE if directory name is forbidden. Otherwise it returns FALSE.
	 */
	protected function _isForbiddenDirectoryName($directoryname)
	{
		foreach($this->config_area['forbidden_directories'] as $forbidden_directory)
		{
			if($forbidden_directory == $directoryname)
			{
				// found directoryname -> forbidden
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * If the allowed file extensions are set, the function checks
	 * if $fileextension is an allowed file extension. Otherwise it
	 * only checks, whether $fileextension is set in forbidden file
	 * extensions.
	 * @param string $fileextension
	 * @return boolean Return TRUE if file extension is forbidden. Otherwise it returns FALSE.
	 */
	protected function _isForbiddenFileExtension($fileextension)
	{
		if(count($this->config_area['allowed_files']) > 0 && strlen($this->config_area['allowed_files'][0]) > 0)
		{
			foreach($this->config_area['allowed_files'] as $allowed_file)
			{
				if($allowed_file == $fileextension)
				{
					// found filename -> allowed
					return FALSE;
				}
			}
			
			return TRUE;
		}
		
		foreach($this->config_area['forbidden_files'] as $forbidden_file)
		{
			if($forbidden_file == $fileextension)
			{
				// found filename -> forbidden
				return TRUE;
			}
		}
		return FALSE;
	}
	
	/**
	 * Iterates to the children of the given treemodel and selects the files of the directory.
	 * This function calls itself recursvely.
	 * @param integer $iddirectory Current iddirectory
	 * @param string $temp_out_path Path to the temporary download directory
	 * @param SF_MODEL_DirectorySqlTree $dirtree
	 * @return void
	 */
	protected function _prepareDownloadDirectory($iddirectory, $temp_out_path, $dirtree)
	{
		$this->_prepareDownloadDirectoryCopyFiles($iddirectory, $temp_out_path);
		
		$children = $dirtree->getChildren($iddirectory);
		
		foreach($children as $child)
		{
			if($dirtree->hasChildren($child))
			{
				$subtree = $this->_prepareDownloadDirectory($child, $temp_out_path, $dirtree);
			}
			
			$this->_prepareDownloadDirectoryCopyFiles($child, $temp_out_path);
		}
		
	}

	/**
	 * Creates a collection to select files for the given $iddirectory.
	 * The collection checks download perm. Found and accessable files will
	 * be copied to $temp_out_path. Directories are created only if one file exists.
	 * @param integer $iddirectory Current iddirectory
	 * @param string $temp_out_path Path to the temporary download directory
	 * @return void
	 */
	protected function _prepareDownloadDirectoryCopyFiles($iddirectory, $temp_out_path)
	{
		$filecol = $this->file_sql_collection;
		$filecol->reset(); // reset to get a fresh collection
		$filecol->setCfg('perm_nr', $this->file_sql_item->getObjectPermId('download'));
		$filecol->setPermCheckActive(TRUE);
		$filecol->setIdclient( $this->config_area['idclient'] );
		$filecol->setIdlang( $this->config_area['idlang'] );
		$filecol->setFreefilter('iddirectory', $iddirectory);
		$filecol->setFreefilter('area', $this->config_area['area_name']);
		$filecol->generate();

		if($filecol->getCount() > 0)
		{
			$fsm = sf_api('LIB', 'FilesystemManipulation');
			if($this->directory_sql_item->loadById($iddirectory) == TRUE)
			{
				$temp_out_path .= '/'.$this->directory_sql_item->getField('dirname');
				$fsm->createDirectory($temp_out_path, FALSE, TRUE); // 3. parameter (TRUE) = create recursively !
				
				$iter = $filecol->getItemsAsIterator();
				while($iter->valid())
				{
					$file = $iter->current();
					$fsm->copyFile($file->getPath(), $temp_out_path.$file->getField('filename'));
					$iter->next();
				}
			}
		}
	}
}
?>