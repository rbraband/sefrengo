<?php 
/**
 * Frontend Controller to parse Page for frontend and backend onsite view
 * @author bjoern
 *
 * @important
 * $cms_mod: Wird die Variable nicht als globale Referenz gesetzt, findet das Contentflex
 * id Inhalte nicht mehr
 *
 */
class SF_CONTROLLER_FrontendPage extends SF_LIB_ApiObject
{
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
	 * Frontedn Parser Tools
	 * @var SF_LIB_FrontendPageParser
	 */
	protected $ft;

	/**
	 * Configuration of the controller
	 * @var array
	 */
	protected $controller_cfg = array(
		'ctr_name' => '',
		'ctr_fnc_called' => '',

		'cms_path' => '',
		'cms_log' => '',
		'sf_lang_stack' => '',
		'lang_global_startlang' => '',
		'langarray' => '',
		'startlang' => '',
		'register_globals' => FALSE,
	);

	/**
	 * Constructor sets up {@link $req}, {@link $cfg}, {@link lng}, {@link url}
	 * @return void
	 */
	public function __construct()
	{
		//init objects
		$this->req = sf_api('LIB', 'WebRequest');
		$this->lng = sf_api('LIB', 'Lang');
		$this->url = sf_api('LIB', 'UrlBuilder');
        $this->ft = sf_api('LIB', 'FrontendTools');
		$this->cfg = sf_api('LIB', 'Config');
	}

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
		$this->controller_cfg['cms_path'] = $GLOBALS['cms_path'];
		$this->_loadIncludes();
	}

	/**
	 * Frontend Controller Class
	 * @return void
	 */
	public function index()
	{
		$cms_path = $this->controller_cfg['cms_path'];
		$cms_db = $GLOBALS['cms_db'];
		$cfg_cms = $GLOBALS['cfg_cms'];
		$cfg_client = $GLOBALS['cfg_client'];//vars from project config
		$idcatside = FALSE;
		$idcat = FALSE;
		$lang = FALSE;
		$client = FALSE;
		$sf_frontend_output = sf_api('LIB', 'FrontendOutput');

		//set cfg db names
		$this->cfg->set('db', $cms_db);

        //lookup for backend view
        $view = $this->req->req('view');

        //get backend or frontend session
        $sid = FALSE;
        $sefrengo = FALSE;
        list($sid, $sefrengo) = $this->ft->getSessionIdsFromRequest($view);

		//generate frontend/ backend boolean vars

		$is_onpage = TRUE;
        $is_frontend = $is_backend = $is_backend_preview = $is_backend_edit = FALSE;
        list($is_frontend, $is_backend, $is_backend_preview, $is_backend_edit) = $this->ft->getViewModes($view, $sefrengo);

		$this->_assignGlobals('is_frontend', $is_frontend);
		$this->_assignGlobals('is_backend', $is_backend);

		$this->cfg->setVal('env', 'sid', $sid);
		$this->cfg->setVal('env', 'sefrengo', $sefrengo);
		$this->cfg->setVal('env', 'is_onpage', $is_onpage);
		$this->cfg->setVal('env', 'is_frontend', $is_frontend);
		$this->cfg->setVal('env', 'is_backend', $is_backend);
		$this->cfg->setVal('env', 'is_backend_preview', $is_backend_preview);
		$this->cfg->setVal('env', 'is_backend_edit', $is_backend_edit);

        //init log
		$cms_log = $this->_initAndGetLog();

        //init db
		$db = new DB_cms;
		$this->_assignGlobals('db', $db);
        $this->ft->setDb($db);
        $this->ft->setDbNames($cms_db);

		//$val_ct = new values_ct();
		//$this->_assignGlobals('val_ct', $val_ct);
		//$cfg_cms = array_merge($cfg_cms, $val_ct->get_cfg() );
		
		$valcol = sf_api('MODEL', 'ValueSqlCollection');
		$valcol->getByGroup('cfg');
		$cfg_cms = array_merge($cfg_cms, $valcol->getAssocKeyArray());
		$valcol->getByGroup('lang');
		$cms_lang = $valcol->getAssocKeyArray();

		
		$this->_assignGlobals('cfg_cms', $cfg_cms);
		$this->cfg->set('cms', $cfg_cms);
		$this->cfg->setVal('env', 'path_backend_http', $this->cfg->cms('path_base_http').$this->cfg->cms('path_backend_rel'));
		$this->cfg->setVal('env', 'path_backend', $this->cfg->cms('path_base').$this->cfg->cms('path_backend_rel'));

		$client = (int) $GLOBALS['client']; //idclient from projectconfig

		//$cfg_client = $val_ct->get_by_group('cfg_client', $client);
		$valcol->setIdclient($client);
		$valcol->getByGroup('cfg_client');
		$cfg_client = $valcol->getAssocKeyArray();
		$cfg_client['path_http'] = str_replace(array('{%http_host}', '{%request_uri}' ), array($_SERVER['HTTP_HOST'], $_SERVER['QUERY_STRING']), $cfg_client['path_http']);
		$cfg_client['path_http_edit'] = str_replace(array('{%http_host}', '{%request_uri}' ), array($_SERVER['HTTP_HOST'], $_SERVER['QUERY_STRING']), $cfg_client['path_http_edit']);
		unset($valcol);
		
		$this->_assignGlobals('cfg_client', $cfg_client);
		$this->cfg->set('client', $cfg_client);
		$sf_path_base = $this->cfg->cms('path_base');
		$sf_path_http_frontend = $is_frontend ? $this->cfg->client('path_http') : $this->cfg->client('path_http_edit');
		$this->cfg->setVal('env', 'path_frontend_http', $sf_path_http_frontend.$this->cfg->client('path_rel'));
		$this->cfg->setVal('env', 'path_frontend', $sf_path_base.$this->cfg->client('path_rel'));
		$this->cfg->setVal('env', 'path_frontend_fm_http',$sf_path_http_frontend.$this->cfg->client('path_rel').$this->cfg->client('path_fm_rel'));
		$this->cfg->setVal('env', 'path_frontend_fm', $sf_path_base.$this->cfg->client('path_rel').$this->cfg->client('path_fm_rel'));
		$this->cfg->setVal('env', 'path_frontend_css_http', $sf_path_http_frontend.$this->cfg->client('path_rel').$this->cfg->client('path_css_rel'));
		$this->cfg->setVal('env', 'path_frontend_css', $sf_path_base.$this->cfg->client('path_rel').$this->cfg->client('path_css_rel'));
		$this->cfg->setVal('env', 'path_frontend_js_http', $sf_path_http_frontend.$this->cfg->client('path_rel').$this->cfg->client('path_js_rel'));
		$this->cfg->setVal('env', 'path_frontend_js', $sf_path_base.$this->cfg->client('path_rel').$this->cfg->client('path_js_rel'));
		//init db cache
		$db->init_cache(/*init db_cache with $cfg_client and $cfg_cms*/);

		//init / found idlang
		$this->_initLang($db, $cms_db, $client);
		$sf_lang_stack = $this->controller_cfg['sf_lang_stack'];
		$lang_global_startlang = $this->controller_cfg['lang_global_startlang'];
		$langarray = $this->controller_cfg['langarray'];
		$startlang = $this->controller_cfg['startlang'];

        $this->_assignGlobals('sf_lang_stack', $sf_lang_stack);
        $this->_assignGlobals('lang_global_startlang', $lang_global_startlang);
        $this->_assignGlobals('langarray', $langarray);
        $this->_assignGlobals('startlang', $startlang);

		// init idactside, idcat, lang
    list($idcatside, $idcat, $lang) = $this->ft->getPageIdsFromRequest($startlang);

    $this->_assignGlobals('lang', $lang);

    $is_frontend_rewrite_no = ($cfg_client['url_rewrite'] == '0' && $is_frontend);
		$is_frontend_rewrite1 = ($cfg_client['url_rewrite'] == '1' && $is_frontend);
		$is_frontend_rewrite2 = ($cfg_client['url_rewrite'] == '2' && $is_frontend);
		$this->cfg->setVal('env', 'is_frontend_rewrite_no', $is_frontend_rewrite_no);
		$this->cfg->setVal('env', 'is_frontend_rewrite1', $is_frontend_rewrite1);
		$this->cfg->setVal('env', 'is_frontend_rewrite2', $is_frontend_rewrite2);
		

		//extract idcatside/ idcat from frontend rewrite url
		$_sf_rewrite_session = FALSE;
		if ($is_frontend_rewrite2 && ($idcatside < 1 && $idcat < 1))
		{
			include_once($cfg_cms['path_base'].$cfg_cms['path_backend_rel'].'inc/fnc.mod_rewrite.php');
			$_sf_rewrite_session = TRUE;
			$rewrite_lang = '';

			//is not startpage
			if ($_REQUEST['sf_rewrite'] != '')
			{
				list($idcatside, $idcat, $rewrite_lang) = $this->_getIdcatsideIdcatLangByRewriteUrl($db, $cfg_cms, $cfg_client, $cms_db, $client, $sf_lang_stack, $startlang);
				$lang = ((int) $rewrite_lang > 0) ? (int) $rewrite_lang : $lang;

				//idcatside and idcat can't be found
				if ($idcatside < 1 && $idcat < 1)
				{

					$idcatside = $this->ft->tryToRemapIdcatsideOrShow404();
				}
			}
		}

		$this->cfg->setVal('env', 'idclient', $client);
		$this->cfg->setVal('env', 'idlang', $lang);
		$this->cfg->setVal('env', 'idstartlang', $startlang);
		$this->cfg->setVal('env', 'idcatside', $idcatside);
		$this->cfg->setVal('env', 'idpage', $idcatside);
		$this->cfg->setVal('env', 'idcat', $idcat);
		$this->_assignGlobals('idcatside', $idcatside);
		$this->_assignGlobals('_sf_rewrite_session', $_sf_rewrite_session);
		$this->_assignGlobals('idcat', $idcat);
		$this->_assignGlobals('lang', $lang);
		
		//start session and auth
		$this->ft->startSession();

        $sess = $this->_getGlobal('sess');
        $auth = $this->_getGlobal('auth');
		$this->cfg->set('sess', $sess);
		$this->cfg->set('auth', $auth);
		
        // Sprache wechseln
        $sess->register('sid_idcatside');
		$sid_idcatside = $idcatside;
		$this->_assignGlobals('sid_idcatside', $sid_idcatside);

		$perm = new cms_perms($client, $lang);
		$this->_assignGlobals('perm', $perm);
		$this->cfg->set('perm', $perm);
        
		//assign logger client settings
		$cms_log->setIdclient($client);
		$cms_log->setIdlang($lang);
		$cms_log->setStorage('screen', $cfg_client['logs_storage_screen']);
		$cms_log->setStorage('logfile', $cfg_client['logs_storage_logfile']);
		$cms_log->setStorage('database', $cfg_client['logs_storage_database']);

		//get cms_lang strings
		$lang_charset = $sf_lang_stack[$lang]['charset'];
		$lang_dir = $cms_path.'lang/'.$cfg_cms['backend_lang'] . '/';
		$cms_lang = $this->ft->getFrontendLangFile($lang_dir, $cms_path);


		$this->_assignGlobals('lang_charset', $lang_charset);
		$this->_assignGlobals('lang_dir', $lang_dir);
		$this->_assignGlobals('cms_lang', $cms_lang);

		// if idcatside not set found start idcat
		if ($idcatside < 1)
		{
			$idcatside = $this->ft->getStartIdcatside();
			$this->_assignGlobals('idcatside', $idcatside);
			$this->_assignGlobals('idpage', $idcatside);
			$this->cfg->setVal('env', 'idcatside', $idcatside);
		}

		$this->cfg->setVal('env', 'is_https', (sf_get_server_protocol() == 'https'));

		//register globals
		if ($this->controller_cfg['register_globals'])
		{
			$types_to_register = array('GET','POST');
			foreach ($types_to_register as $global_type)
			{
				$arr = @${'_'.$global_type};
				if (@count($arr) > 0)
				{
					extract($arr, EXTR_SKIP);
				}
			}
		}

		$code = '';
		if ($is_backend)
		{
            //idcat is needed for have_perm()- call
            //wenn vom frontend aus eine Kategorie angelegt wird, ist noch keine $idcat vorhanden
            if( (int) $idcat < 1 && ! empty($idcatside))
			{
                $sql = "SELECT idcat FROM ". $cms_db['cat_side'] ." WHERE idcatside='". (int) $idcatside."'";
                $db->query($sql);
                if( $db->next_record() )
				{
                    $idcat = $db->f('idcat');
                    $GLOBALS['idcat'] =& $idcat;//todo
				}
            }
            // Modus einstellen: editieren/Vorschau/normal
			$sf_perm_edit_page = FALSE;
            if ($perm->have_perm(19, 'side', $idcatside, $idcat))
			{
                $sf_perm_edit_page = TRUE;
            }
			$this->cfg->setVal('env', 'view', $view);
			$this->cfg->setVal('env', 'perm_edit_page', $sf_perm_edit_page);

            //Generate cat and page informations
            $SF_catinfos = sf_api('LIB', 'Catinfos');
            $con_tree = $SF_catinfos->getCatinfoDataArray();
            $tlo_tree = $SF_catinfos->getParentDependanceDataArray();

			$this->_assignGlobals('SF_catinfos', $SF_catinfos);
			$this->_assignGlobals('con_tree', $con_tree);
			$this->_assignGlobals('tlo_tree', $tlo_tree);

            tree_level_order('0', 'catlist');
            $catlist = $GLOBALS['catlist'];
            $catlist_level = $GLOBALS['catlist_level'];

            $SF_pageinfos = sf_api('LIB', 'Pageinfos');
            $con_side = $SF_pageinfos->getPageinfoDataArray();
			$this->_assignGlobals('SF_pageinfos', $SF_pageinfos);
			$this->_assignGlobals('con_side', $con_side);

            // idcatside prüfen, da der User auch in einer Kategorie sein kann, wo es noch keine seite
            // und damit idcatside gibt.
			//catch advanced pageinfos for cuurent page
            if(! empty($idcatside) )
			{
                $con_side[$idcatside]['meta_author'] = $SF_pageinfos->getMetaAuthor($idcatside);
                $con_side[$idcatside]['meta_description'] = $SF_pageinfos->getMetaDescription($idcatside);
                $con_side[$idcatside]['meta_keywords'] = $SF_pageinfos->getMetaKeywords($idcatside);
                $con_side[$idcatside]['meta_robots'] = $SF_pageinfos->getMetaRobots($idcatside);
                $con_side[$idcatside]['meta_redirect'] = $SF_pageinfos->getMetaRedirectIsActive($idcatside);
                $con_side[$idcatside]['meta_redirect_url'] = $SF_pageinfos->getMetaRedirectUrl($idcatside);
                $con_side[$idcatside]['summary'] = $SF_pageinfos->getSummary($idcatside);
                $con_side[$idcatside]['author'] = $SF_pageinfos->getAuthor($idcatside);
                $con_side[$idcatside]['created'] = $SF_pageinfos->getCreatedTimestamp($idcatside);
                $con_side[$idcatside]['lastmodified'] = $SF_pageinfos->getLastmodifiedTimestamp($idcatside);
                $con_side[$idcatside]['is_https'] = $SF_pageinfos->getIsHttps($idcatside);
            }

			$idcat = $SF_pageinfos->getIdcat($idcatside);
			$idside = $SF_pageinfos->getIdside($idcatside);
			$this->cfg->setVal('env', 'idcat', $idcat);
			$this->cfg->setVal('env', 'idcatside', $idcatside);

			$this->_assignGlobals('idcat', $idcat);
			$this->_assignGlobals('idside', $idside);

			$arr = $this->ft->getIdtplIdtplconf($idcat, $idside, $lang);
			$idlay = $this->ft->getIdlay($arr['idtpl']);
			$this->cfg->setVal('env', 'idtpl', $arr['idtpl']);
			$this->cfg->setVal('env', 'idtplconf', $arr['idtplconf']);
			$this->cfg->setVal('env', 'idlay', $idlay);

            // Inhalt erstellen zum editieren der Seite
            if($this->cfg->env('view'))
			{
                // es existiert noch keine Seite in diesem Ordner
                if(!$idcatside)
				{
					$this->cfg->setVal('env', 'view', FALSE);
				}

                include($cms_path.'inc/fnc.tpl.php');
                include($cms_path.'inc/fnc.type.php');

                $sefrengotag_config = NULL;
                $GLOBALS['sefrengotag_config'] = $sefrengotag_config;
                $con_contype = NULL;
                $GLOBALS['con_contype'] = $con_contype;
                $con_typenumber = NULL;
                $GLOBALS['con_typenumber'] = $con_typenumber;
                $filetarget_is_hidden = NULL;
                $GLOBALS['filetarget_is_hidden'] = $filetarget_is_hidden;
                $type_container = NULL;
                $GLOBALS['con_typenumber'] = $type_container;
                $type_number = NULL;
                $GLOBALS['con_typenumber'] = $type_number;
                $type_typenumber = NULL;
                $GLOBALS['con_typenumber'] = $type_typenumber;

                $sf_content_manipulation = sf_api('LIB', 'FrontendPageContentManipulation');
                $data = $this->req->req('data');
                $action = (array_key_exists('action', $_GET)) ? $_GET['action']: $_POST['action'];
                $idsidelang = $con_side[$idcatside]['idsidelang'];

                // Content speichern
                if ($action == 'save' || $action == 'saveedit')
                {
                    //content f�r event (am Ende der if-Schleife) zwischenspeichern
                    $entry = $this->req->req('entry', FALSE);
                    /**
                     * Aufbau str $content
                     * idcontainer.idrepeat.idformfieldtype-idmodtag[,idformfieldtypeN-idmodtagN]
                     * 520.2.14-2,13-1,4-1,5-1,1-2,14-3 - bearbeiten
                     * 530.new.13-1 - neu einf�gen, Position wird �ber $entry bestimmt
                     *
                     */
                    $sf_content_string = $this->req->req('content', FALSE);
                    $con_content = explode (';', $sf_content_string);
                    foreach ($con_content as $value)
                    {
                        $con_config = explode ('.', $value);
                        $con_container = $con_config['0'];
                        $con_containernumber = $con_config['1'];
                        $con_content_type = explode (',', $con_config[2]);

                        //add space for a "new" repeat container
                        if (is_numeric($entry) || is_int($entry))
                        {
                            $sf_content_manipulation->addNewRepeatContainer($con_container, $entry, $idsidelang);
                        }

                        foreach ($con_content_type as $value3)
                        {
                            $value3 = explode ('-', $value3);
                            $con_formtypenumber = $value3['0'];
                            $con_idmodtag = $value3['1'];
                            $sf_field_content = $this->req->req('content_'.$con_container.'_'.$con_containernumber.'_'.$con_formtypenumber.'_'.$con_idmodtag);
                            $new_containernumber = (is_numeric($entry) || is_int($entry)) ? $entry+1 : $con_containernumber;
                            // $idcontainer, $idmodtag, $formtypenumber, $content, $idrepeat, $idsidelang
                            $sf_content_manipulation->save($con_container, $con_idmodtag, $con_formtypenumber, $sf_field_content, $new_containernumber, $idsidelang);
                        }

                        // Modulverdopplung minimieren, wenn Content leer ist.
                        $sf_content_manipulation->checkAndOptimizeRepeatContainer($con_container, $new_containernumber, $idsidelang);
                    }

                    // Event
                    fire_event('con_edit', array(
                        'path' => $cms_path,
                        'idcatside' => $idcatside,
                        'content' => $sf_content_string
                    ));
                }
                // delete, move up, move down content
                else if ($action == 'delete' || $action == 'move_up' || $action == 'move_down')
                {
                    $content = $this->req->req('content', FALSE);
                    $con_content = explode (';', $content);

                    foreach ($con_content as $value)
                    {
                        $con_config = explode ('.', $value);
                        $con_container = $con_config['0'];
                        $con_contnbr = $con_config['1'];
                        switch ($action)
                        {
                            case 'delete':
                                $sf_content_manipulation->delete($con_container, $con_contnbr, $idsidelang);
                                break;
                            case 'move_up':
                                $sf_content_manipulation->moveUp($con_container, $con_contnbr, $idsidelang);
                                break;
                            case 'move_down':
                                $sf_content_manipulation->moveDown($con_container, $con_contnbr, $idsidelang);
                                break;
                        }
                    }

                    $this->ft->redirect($idcatside);
                }

                //Content bearbeiten
                if ($action == 'edit' || $action == 'saveedit' || $action == 'new')
                {
					$this->sf_is_form_edit_mode = TRUE;
                    $code = $this->ft->getBackendEditForm($cms_path, $lang_charset, $cfg_cms, $idcatside, $lang, $GLOBALS['sess'], $cfg_client, $con_tree, $con_side, $cms_lang, $idside);
                }
                // normale Anzeige
                else
                {
                    // Template suchen
                    $container = $this->ft->getTemplateConfig($idcat, $idside, $lang);
                    $content = '';
                    if (count($container) > 0)
                    {
                        $idtpl = $container['idtpl'];
                        $idtplconf = $container['idtplconf'];
                        $GLOBALS['idtpl'] =& $idtpl;//TODO
                        $GLOBALS['idtplconf'] =& $idtplconf;//TODO
                        $GLOBALS['container'] =& $container;//TODO

                        //Generate content array
                        $content = $this->ft->getContentArray($idside, $lang);
                        $GLOBALS['content'] =& $content;//TODO

                        //Get layout
                        $sf_layoutarray = $this->ft->getLayoutArray($idtpl);
                        $layout = $sf_layoutarray['layout'];
                        $sf_doctype = $sf_layoutarray['doctype'];
                        $sf_doctype_autoinsert = $sf_layoutarray['doctype_autoinsert'];
                        $sf_slash_closing_tag = $sf_layoutarray['doctype_required_tag_postfix'];

                        $sf_frontend_output->setDoctype($sf_layoutarray['doctype']);
                        $sf_frontend_output->setCharset($sf_lang_stack[$lang]['charset']);
                        $sf_frontend_output->setMetaKeywords($con_side[$idcatside]['meta_keywords']);
                        $sf_frontend_output->setMetaDescription($con_side[$idcatside]['meta_description']);
                        $sf_frontend_output->setMetaAuthor($con_side[$idcatside]['meta_author']);
                        $sf_frontend_output->setMetaRobots($con_side[$idcatside]['meta_robots']);

                        $code = $this->ft->mapCMSPHPCACHEToPhp($layout);
                        // Container generieren
                        $list = $this->ft->extractCmsTags($code);

                        $GLOBALS['cms_mod'] =& $cms_mod;//TODO

                        if (is_array($list))
                        {
                            $search = array();
							$replace = array();
							$sf_frontend_output->setCmsLayTags($list);

                            foreach ($list as $cms_mod['container'])
                            {
                                $search[] = $cms_mod['container']['full_tag'];

                                //Head-Container
                                if ($cms_mod['container']['type'] == 'head')
                                {
                                    $backendcfg = array(	'is_backend' => $is_backend,
                                                            'is_https' => ($cfg_client['https'] == 1 && $con_side[$idcatside]['is_https'] == 1),
                                                            'cms_html_path' => $cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'],
                                                            'skin' => $cfg_cms['skin'],
                                                            'lang_gen_deletealert' => $cms_lang['gen_deletealert'],
                                                            'perm' => $perm,
                                                            'view' => $view,
                                                            'idcatside' => $idcatside,
                                                            'idcat' => $idcat,
                                                            'lang' => $lang
                                                        );

                                    $replace[] = $this->ft->getContainerHead($idlay, $sf_slash_closing_tag, $lang_charset, $sf_doctype, $backendcfg);
                                }
                                //Pageconfiglayer
                                else if ($cms_mod['container']['type'] == 'config')
                                {
                                    $replace[] = $this->ft->getContainerConfig($is_backend);
                                }
                                //Mod container
                                else
                                {
                                    $backendcfg = array(	'is_backend' => $is_backend,
                                                            'idcatside' => $idcatside,
                                                            'view' => $this->cfg->env('view'),
                                                        );
                                    $replace[] = $this->ft->getContainerContent($container, $cms_mod, $content, $backendcfg);
                                }
                            }

                            foreach ($search as $key=>$value)
                            {
                                $code = str_replace($value, $replace[$key], $code);
                            }
                        }
                    }
                    else
                    {
                        $code = $cms_lang['con_notemplate'];
                    }
					//echo $code;exit;
                    ob_start();
                    eval(' ?>'.$code);
                    $code = ob_get_contents();
                    ob_end_clean ();

                    $code = $this->ft->mapCMSPHPToPhp($code);
                }
            }

            // Seite ausgeben
            if ($code)
			{
                //redirect is active
                if($SF_pageinfos->getMetaRedirectIsActive($idcatside)  && $SF_pageinfos->getMetaRedirectUrl($idcatside) != '')
				{
                     $this->ft->redirect($SF_pageinfos->getMetaRedirectUrl($idcatside));
                }
                // throw out side
                else
				{
                    ob_start(); 
                    eval('?>'.$code);
                    $code = ob_get_contents();
                    ob_end_clean ();
                }
            }
            else
			{
                $this->ft->show404();
            }

		}
        //FRONTEND
		else
		{
			$cfg_client['session_enabled'] = $this->ft->isSessionDisabledByUseragent() ? 0 : $cfg_client['session_enabled'];
			$cfg_client['session_enabled'] = $this->ft->isSessionDisabledByIp() ? 0 : $cfg_client['session_enabled'];
			$this->cfg->setVal('client', 'session_enabled', $cfg_client['session_enabled']);
                
			if ($_sf_rewrite_session)
			{
                if ($sess->mode == 'get')
				{
                    $sess->mode = 'getrewrite';
                }
            }

            //Generate cat and page informations
            $SF_catinfos = sf_api('LIB', 'Catinfos');
            $con_tree = $SF_catinfos->getCatinfoDataArray();
            $tlo_tree = $SF_catinfos->getParentDependanceDataArray();

            $GLOBALS['SF_catinfos'] = $SF_catinfos;//todo
            $GLOBALS['con_tree'] = $con_tree;//todo
            $GLOBALS['tlo_tree'] = $tlo_tree;//todo

            tree_level_order('0', 'catlist');
            $catlist = $GLOBALS['catlist'];
            $catlist_level = $GLOBALS['catlist_level'];

            if(is_array($con_tree))
			{
                $SF_pageinfos = sf_api('LIB', 'Pageinfos');;
                $con_side = $SF_pageinfos->getPageinfoDataArray();
                $GLOBALS['SF_pageinfos'] = $SF_pageinfos;//todo
                $GLOBALS['con_side'] = $con_side;//todo
            }

            // $idcat und $idside ermitteln
            if (empty($idcat))  $idcat  = $con_side[$idcatside]['idcat'];
            if (empty($idside)) $idside = $con_side[$idcatside]['idside'];
            $GLOBALS['idcat'] = $idcat;//todo
            $GLOBALS['idside'] = $idside;//todo

			if ($cfg_client['url_rewrite'] == '2')
			{
                include_once($cfg_cms['path_base'].$cfg_cms['path_backend_rel'].'inc/fnc.mod_rewrite.php');
                rewriteGenerateMapping();
            }

            // Ausgabe beenden, wenn Kategorie oder Seite nicht online
            if($con_side[$idcatside]['online'] != 1 || $con_tree[$idcat]['visible'] != 1)
			{
				$idcatside = $this->ft->tryToRemapIdcatsideOrShow404();
				$idcat  = $SF_pageinfos->getIdcat($idcatside);
                $idside = $SF_pageinfos->getIdside($idcatside);
			}

            // get advanced sideinfos for this side
            // idcatside pr�fen, da der User auch in einer Kategorie sein kann, wo es noch keine seite
            // und damit idcatside gibt.
            // es gibt keine idsidelang, wenn der user nicht das recht hat, die seite zu sehen
            if(! empty($idcatside) && ! empty($con_side[$idcatside]['idsidelang']))
			{

				$con_side[$idcatside]['meta_author'] = $SF_pageinfos->getMetaAuthor($idcatside);
                $con_side[$idcatside]['meta_description'] = $SF_pageinfos->getMetaDescription($idcatside);
                $con_side[$idcatside]['meta_keywords'] = $SF_pageinfos->getMetaKeywords($idcatside);
                $con_side[$idcatside]['meta_robots'] = $SF_pageinfos->getMetaRobots($idcatside);
                $con_side[$idcatside]['meta_redirect'] = $SF_pageinfos->getMetaRedirectIsActive($idcatside);
                $con_side[$idcatside]['meta_redirect_url'] = $SF_pageinfos->getMetaRedirectUrl($idcatside);
                $con_side[$idcatside]['summary'] = $SF_pageinfos->getSummary($idcatside);
                $con_side[$idcatside]['author'] = $SF_pageinfos->getAuthor($idcatside);
                $con_side[$idcatside]['created'] = $SF_pageinfos->getCreatedTimestamp($idcatside);
                $con_side[$idcatside]['lastmodified'] = $SF_pageinfos->getLastmodifiedTimestamp($idcatside);
                $con_side[$idcatside]['is_https'] = $SF_pageinfos->getIsHttps($idcatside);
				$con_side[$idcatside]['protected'] = $SF_pageinfos->getIsProtected($idcatside);
            }
			$this->cfg->setVal('env', 'idcat', $idcat);
			$this->cfg->setVal('env', 'idcatside', $idcatside);

			$arr = $this->ft->getIdtplIdtplconf($idcat, $idside, $lang);
			$idlay = $this->ft->getIdlay($arr['idtpl']);
			$this->cfg->setVal('env', 'idtpl', $arr['idtpl']);
			$this->cfg->setVal('env', 'idtplconf', $arr['idtplconf']);
			$this->cfg->setVal('env', 'idlay', $idlay);

            // Inhalt aus der Datenbank suchen
            if ($auth->auth['uid'] != 'nobody' |
                    ($con_tree[$idcat]['visible'] == '1' && !empty($con_side[$idcatside]['online']) && $con_side[$idcatside]['online'] != '0') ) {

                if ( !empty($con_tree[$con_side[$idcatside]['idcat']]['idcat']) )
                {
                    $sf_item_code = sf_api('MODEL','CodeSqlItem');
					$sf_item_code->loadByIdcatside($idcatside);
					$code = $sf_item_code->getField('code');

                    if ($sf_item_code->getField('changed') == 1)
                    {
                        // Seite generieren weil keine Daten gefunden oder Daten ver�ndert
                        include($cms_path.'inc/fnc.type.php');

                        $container = $this->ft->getTemplateConfig($idcat, $idside, $lang);

                        if (count($container) > 0)
                        {
                            $idtpl = $container['idtpl'];
                            $idtplconf = $container['idtplconf'];
                            $GLOBALS['idtpl'] =& $idtpl;//TODO
                            $GLOBALS['idtplconf'] =& $idtplconf;//TODO
                            $GLOBALS['container'] =& $container;//TODO

                            //Generate content array
                            $content = $this->ft->getContentArray($idside, $lang);
                            $GLOBALS['content'] = $content;//TODO

                            //Get layout
                            $sf_layoutarray = $this->ft->getLayoutArray($idtpl);

                            $layout = $sf_layoutarray['layout'];
                            $sf_doctype = $sf_layoutarray['doctype'];
                            $sf_doctype_autoinsert = $sf_layoutarray['doctype_autoinsert'];
                            $sf_slash_closing_tag = $sf_layoutarray['doctype_required_tag_postfix'];
                            $sf_frontend_output->setDoctype($sf_layoutarray['doctype']);
                            $sf_frontend_output->setCharset($sf_lang_stack[$lang]['charset']);
                            $sf_frontend_output->setMetaKeywords($con_side[$idcatside]['meta_keywords']);
                            $sf_frontend_output->setMetaDescription($con_side[$idcatside]['meta_description']);
                            $sf_frontend_output->setMetaAuthor($con_side[$idcatside]['meta_author']);
                            $sf_frontend_output->setMetaRobots($con_side[$idcatside]['meta_robots']);

                            $layout = $this->ft->mapCMSPHPCACHEToPhp($layout);

                            // Container generieren
                            $list = $this->ft->extractCmsTags($layout);

                            //TODO: 2REMOVE - DEDI BACKWARD COMPATIBILITY
                            $GLOBALS['cms_mod'] =& $cms_mod;//TODO

                            //parse containers
							$search = array();
							$replace = array();
                            if (is_array($list))
                            {
                                $sf_frontend_output->setCmsLayTags($list);//TODO DOES NOT WORK WITHOUT CACHE!!!!

                                foreach ($list as $cms_mod['container'])
                                {
                                    $search[] = $cms_mod['container']['full_tag'];

                                    //Head-Container
                                    if ($cms_mod['container']['type'] == 'head')
                                    {
                                        $replace[] = $this->ft->getContainerHead($idlay, $sf_slash_closing_tag, $lang_charset, $sf_layoutarray['doctype']);
                                    }
                                    //Pageconfiglayer
                                    else if ($cms_mod['container']['type'] == 'config')
                                    {
                                        $replace[] = $this->ft->getContainerConfig($is_backend);
                                    }
                                    //Mod container
                                    else
                                    {
                                        $replace[] = $this->ft->getContainerContent($container, $cms_mod, $content);
                                    }
                                }

                                // Seite erstellen
                                $code = $layout;
                                foreach ($search as $key=>$value)
                                {
                                    $code = str_replace($value, $replace[$key], $code);
                                }
                            }
                            //no container found
                            else
                            {
                                $code = $layout;
                            }
                        //no template found
                        }
                        else
                        {
                            $code = $cms_lang['con_notemplate'];
                        }

                        //render cached php
                        ob_start();
                        eval(' ?>'.$code);
                        $code = ob_get_contents();
                        $sf_fo_export = $sf_frontend_output->_exportConfig();
                        $code = '<CMSPHP> $_sf_frontend_output_cfg_cache = '.$sf_fo_export.'; </CMSPHP>'.$code;
                        ob_end_clean ();

                        $code = $this->ft->mapCMSPHPToPhp($code);

                        // Delete Content Cache
                        sf_factoryCallMethod('UTILS', 'DbCache', null, null, 'flushByGroup', array('frontend', 'content'));

                        // Seite in die 'code'-Tabelle schreiben
                        if ($this->cfg->client('cache') == '1')
						{
                            $sf_item_code->setField('code', $code);
							$sf_item_code->setField('changed', '0');
							$sf_item_code->save();
                        }
                    }
                }
            }

            // Seite ausgeben
            if ($code)
			{
                // handle meta redirect
               //redirect is active
                if($SF_pageinfos->getMetaRedirectIsActive($idcatside)  && $SF_pageinfos->getMetaRedirectUrl($idcatside) != '')
				{
                     $this->ft->redirect($SF_pageinfos->getMetaRedirectUrl($idcatside));
                }
				else
				{
                    ob_start();
                    eval('?>'.$code);
                    $sf_frontend_output->_importConfig($_sf_frontend_output_cfg_cache);
                    $code = ob_get_contents ();
                    ob_end_clean ();
                    if ($this->cfg->env('send_header_404'))
					{
                        $sf_frontend_output->setHttpHeader('HTTP/1.1 404 Not Found');
						$sf_frontend_output->setHttpHeader('Status: 404 Not Found');
                    }
                }
            }
            else
            {
                $this->ft->show404();
            }
		}

        //$sf_frontend_output->addContentToHead('<script>alert("hello")</script>', $position = 'top');
        //$sf_frontend_output->changeContainerVisibility(520, FALSE);
        //$sf_frontend_output->changeContainerVisibility(510, FALSE);
        $output = $sf_frontend_output->parse($code, TRUE);

		if (! $this->sf_is_form_edit_mode)
		{
			$output = $this->ft->getMappedCmsFileUrlsToNamedUrls($output);
			$output = $this->ft->getMappedCmsLinks($output);
		}

		// event to manipulate the output
		$event = fire_event('frontend_code_generated', array('output' => $output), array('output'));
		$output = $event['output'];
		unset($event);

        //Logs auf Screen ausgeben, wenn Logs vorhanden
        if( count( $cms_log->getLogs() ) > 0 )
        {
            $log_output = sf_api('VIEW', 'LogOutput');
			$log_output->addItemsArray( $cms_log->getLogs() );
			
			$searches = array('</body>');
			foreach($searches as $search)
			{
				$pos = strpos($output, $search);
				if($pos !== FALSE)
				{
					// replace the last occurence of $search
					$output = substr_replace($output, ("\n".$log_output->show()."\n".$search), $pos, strlen($search));
					break; // exit
				}
			}
        }

        //handle charset - default is UTF-8
        if ( $sf_lang_stack[$lang]['charset'] == 'iso-8859-1')
        {
            $output = utf8_decode($output);
        }

        // Seite komprimieren und ausgeben
        if ($cfg_cms['gzip'] == '1')
        {
           // @ob_start('ob_gzhandler');
            eval($cfg_client['manipulate_output']);
           // @ob_end_flush();
        } 
        else
        {
            eval($cfg_client['manipulate_output']);
        }
	}

	protected function _loadIncludes()
	{
		// notwendige Dateien includen
		if (! is_file($this->controller_cfg['cms_path'].'inc/config.php'))
		{
			die('NO CONFIGFILE FOUND');
		}
		require_once ($this->controller_cfg['cms_path'].'inc/class.cms_debug.php');
		include_once ($this->controller_cfg['cms_path'].'external/phplib/prepend.php');
		include_once ($this->controller_cfg['cms_path'].'inc/class.values_ct.php');
		require_once ($this->controller_cfg['cms_path'].'inc/fnc.general.php');
		include($this->controller_cfg['cms_path'].'inc/class.user_perms.php');
	}

	protected function _initAndGetLog()
	{
		// Logger initialisieren
		$cms_log = sf_api('LIB', 'Logger');
		// Backend, oder Frontend?
		if (isset($sefrengo) && (isset($view))){
			$cms_log->setIsBackend(true);
		} else {
			$cms_log->setIsBackend(false);
		}

		$cms_log->setLogfilePath($cfg_client['log_path']);
		$cms_log->setLogfileSize($cfg_client['log_size']);
		$cms_log->setLogfileMailAddress($cfg_client['logfile_mailaddress']);
		$this->_assignGlobals('cms_log', $cms_log);

		// Weitere Einstellungen werden nach erfolgreicher DB Connection gesetzt

		return $cms_log;
	}

	protected function _initLang($db, $cms_db, $client)
	{
		// Sprache aushandeln
		$sql  = 'SELECT
					L.idlang, L.charset, L.name, L.iso_3166_code, L.is_start, L.rewrite_key, L.rewrite_mapping
				FROM
					' . $cms_db['lang'] . ' L
					LEFT JOIN '. $cms_db['clients_lang'] . ' CL USING(idlang)
				WHERE
					CL.idclient = ' . $client;
		$db->query($sql);

		$langarray = array();
		while ($db->next_record())
		{
			$tmp_idlang = $db->f('idlang');
			$sf_lang_stack[$tmp_idlang]['idlang'] = $tmp_idlang;
			$sf_lang_stack[$tmp_idlang]['charset'] = $db->f('charset');
			$sf_lang_stack[$tmp_idlang]['name'] = $db->f('name');
			$sf_lang_stack[$tmp_idlang]['iso_3166_code'] = $db->f('iso_3166_code');
			$sf_lang_stack[$tmp_idlang]['rewrite_key'] = $db->f('rewrite_key');
			$sf_lang_stack[$tmp_idlang]['rewrite_mapping'] = $db->f('rewrite_mapping');

			if ($db->f('is_start') == 1)
			{
				$lang_global_startlang = $tmp_idlang;
			}

			if (strlen($sf_lang_stack[$tmp_idlang]['iso_3166_code']) >= 2 )
			{
				$langarray[ $sf_lang_stack[$tmp_idlang]['iso_3166_code'] ] = $tmp_idlang;
			}

		}

		$neg = negotiateLanguage($langarray, 'xx');

		if ($neg != 'xx')
		{
			$startlang = $langarray[$neg];
		}
		else
		{
			$startlang = $lang_global_startlang;
		}

		$this->controller_cfg['sf_lang_stack'] = $sf_lang_stack;
		$this->controller_cfg['lang_global_startlang'] = $lang_global_startlang;
		$this->controller_cfg['langarray'] = $langarray;
		$this->controller_cfg['startlang'] = $startlang;

	}

	protected function _getIdcatsideIdcatLangByRewriteUrl($db, $cfg_cms, $cfg_client, $cms_db, $client, $sf_lang_stack, $startlang)
	{
		$idcat = 0;
		$idcatside = 0;
		$lang =0;

		$sf_rewrite_raw = mysql_escape_string($_REQUEST['sf_rewrite']);
		$sf_rw_pieces = explode('/', $sf_rewrite_raw);

		if(preg_match('/^[0-9abcdef]{32}$/', $sf_rw_pieces['0']))
		{
			$_GET['sid'] = $_POST['sid'] = $_REQUEST['sid'] = $sf_rw_pieces['0'];
		}


		//echo " AA ".$lang;
		//test of unique side
		$sql = "SELECT
					CS.idcatside, CS.idcat, SL.idlang
				FROM
					".$cms_db['cat_side']." CS
					LEFT JOIN ".$cms_db['side_lang']." SL USING(idside)
					LEFT JOIN ".$cms_db['clients_lang']." CL USING(idlang)
				WHERE
					CL.idclient = '$client'
					AND SL.rewrite_url = '".preg_replace('#^[0-9abcdef]{32}/#', '', $sf_rewrite_raw)."'
					AND SL.rewrite_use_automatic= '0'";
		$db->query($sql);
		if ($db->next_record())
		{
			//remember exception langswitch
			if (! is_numeric($_REQUEST['lang']))
			{
				$lang = $db->f('idlang');
			}
			else
			{
				$lang = (int) $_REQUEST['lang'];
			}

			$idcatside = $db->f('idcatside');
		}
		else
		{
			//sessionlookup and lang
			$with_short_startlang = ($cfg_client['url_langid_in_defaultlang'] != '1') ? true: false;

			if (preg_match('/^[0-9abcdef]{32}$/', $sf_rw_pieces['0']) )
			{
				$sf_rw_session = $sf_rw_pieces['0'];
				$sf_rw_lang = mysql_escape_string($sf_rw_pieces['1']);
				$sf_rw_pieces = array_slice($sf_rw_pieces, 2);
			}
			else
			{
				$sf_rw_session = '';
				$sf_rw_lang = mysql_escape_string($sf_rw_pieces['0']);
				$sf_rw_pieces = array_slice($sf_rw_pieces, 1);
			}

			//check lang
			$lang_exists_in_url = false;

			foreach ($sf_lang_stack AS $v)
			{
				//echo "{$v['rewrite_key']} == $sf_rw_lang <br>";
				if($v['rewrite_key'] == $sf_rw_lang )
				{
					$lang_exists_in_url = true;
					break;
				}
			}
			if (! $lang_exists_in_url)
			{
				//echo "IN";
				array_unshift($sf_rw_pieces, $sf_rw_lang);
				$sf_rw_lang = $sf_lang_stack[$startlang]['rewrite_key'];
			}

			// print_r($sf_rw_pieces);


			//page or cat
			$sf_rw_count = count($sf_rw_pieces);
			$sf_rw_is_page = ($sf_rw_pieces[$sf_rw_count-1] != '') ? true : false;
			if (! $sf_rw_is_page)
			{
				array_pop($sf_rw_pieces);
			}

			$sf_rw_pieces = array_reverse($sf_rw_pieces);


			//figure out lang - not jump in, if user change language
			$sql  = 'SELECT
						L.idlang
					FROM
						' . $cms_db['lang'] . ' L
						LEFT JOIN '. $cms_db['clients_lang'] . ' CL USING(idlang)
					WHERE
						CL.idclient = ' . $client .'
						AND  L.rewrite_key="'.$sf_rw_lang.'"';
			$db->query($sql);
			$db->next_record();
			$sf_rw_lang_id = $db->f('idlang');

			if (! is_numeric($_REQUEST['lang']))
			{
				$lang = $sf_rw_lang_id;
			}
			else
			{
				$lang = (int) $_REQUEST['lang'];
			}
			//echo " AA ".$lang;

			//get idcatside or idcat
			if ($sf_rw_is_page)
			{
				//echo "IN". $lang;
				//page
				$sf_rw_suffix = str_replace('.', '\.', $cfg_client['url_rewrite_suffix']);

				$v = preg_replace('#'.$sf_rw_suffix.'$#', '', $sf_rw_pieces['0']);

				$sql = "SELECT DISTINCT
							CS.idcatside, CS.idcat
						FROM
							".$cms_db['cat_side']." CS
							LEFT JOIN ".$cms_db['side_lang']." CL USING(idside)
						WHERE
							CL.idlang= '$sf_rw_lang_id'
							AND CL.rewrite_url = '".$v."'";

				$db->query($sql);
				$db->num_rows() ;
				//simple rewrite - allows shadow urls
				//if ($db->num_rows() == 1) {
				//	$db->next_record();
				//	$idcatside = $db->f('idcatside');
				//} else
				if ($db->num_rows() > 0)
				{
					while ($db->next_record())
					{
						$sf_rw_possibleidcats[$db->f('idcatside')] = $db->f('idcat');
					}

					array_shift($sf_rw_pieces);
					//print_r($sf_rw_pieces);echo '<br>';
					foreach($sf_rw_possibleidcats AS $k=>$v)
					{
						if(rewriteIdcatIsUniqueToPath($v, $sf_rw_lang_id, $sf_rw_pieces))
						{
							//echo "IN";
							$idcatside = $k;
							//$idcat = $v;
							break;
						}
					}
				}
			}
			else
			{
				//cat
				$v = preg_replace('#/$#', '', $v);

				$sql = "SELECT DISTINCT
							C.idcat, C.parent
						FROM
							".$cms_db['cat']." C
							LEFT JOIN ".$cms_db['cat_lang']." CL USING(idcat)
						WHERE
							CL.idlang = '$sf_rw_lang_id'
							AND rewrite_alias = '".$sf_rw_pieces['0']."'";
				$db->query($sql);
				//if ($db->num_rows() == 1) {
				//	$db->next_record();
				//	$idcat = $db->f('idcat');
				//} else
				if ($db->num_rows() > 0)
				{
					$sf_rw_possibleidcats = array();
					while ($db->next_record())
					{
						array_push($sf_rw_possibleidcats, $db->f('idcat') );
					}

					foreach ($sf_rw_possibleidcats AS $v)
					{
						if(rewriteIdcatIsUniqueToPath($v, $lang, $sf_rw_pieces))
						{
							$idcat = $v;
							break;
						}
					}
					//echo $idcat;
					//print_r($sf_rw_possibleidcats);exit;
				}

			}
		}

		return array($idcatside, $idcat, $lang);

	}

	protected function _assignGlobals($key, $val)
	{
		$GLOBALS[$key] = $val;
	}

	protected function _getGlobal($key)
	{
		return $GLOBALS[$key];
	}
}

?>