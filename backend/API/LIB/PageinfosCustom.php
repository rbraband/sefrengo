<?php

/**
 * Contains often used informations like pagetitetel, metatags, url,.. of all 
 * pages in one lang of one project.
 */
class SF_LIB_PageinfosCustom extends SF_LIB_ApiObject
{

    protected $data = array( 'data' => array()
    					);

    					
    protected $config = array( 'idlang' => 0,
						 'check_frontend_prems' => false,
						 'check_backend_prems' => false,
    					 'is_generated' => false
    					);
    protected $cache;
    protected $db;
	protected $cfg;
    
    /**
     * Constructor, init common values.
     */
    public function __construct()
    {
        $this->cache = sf_factoryGetObjectCache('UTILS', 'DbCache');
        $this->db = sf_factoryGetObjectCache('DATABASE', 'Ado');
		$this->cfg = sf_api('LIB', 'Config');

		//set singelton
        $this->_API_setObjectIsSingleton(true);
    }
    
    public function setIdlang($idlang)
    {
    	$this->config['idlang'] = (int) $idlang;
    }
    
    public function setCheckFrontendperms($boolean)
    {
    	$this->config['check_frontend_prems'] = (boolean) $boolean;
    }
    
    public function setCheckBackendperms($boolean)
    {
    	$this->config['check_backend_prems'] = (boolean) $boolean;
    }

    public function generate()
    {
		$auth = $this->cfg->authObj();
		$perm = $this->cfg->perm();
    	
    	//check dependencies 
    	if ( $this->config['idlang'] < 1 || $this->config['is_generated'])
		{
    		return false;
    	}

		$this->catinfos = sf_api('LIB', 'Catinfos');
		if ($this->catinfos->getIdlang() != $this->getIdlang())
		{
			$this->catinfos = sf_api('LIB', 'CatinfosCustom');
			$this->catinfos->setIdlang( $this->getIdlang() );
			$this->catinfos->generate();
		}
		
		$catinfos = $this->catinfos->getCatinfoDataArray();
			
		//check perm: user have perm to see pages with the protected flag
		//$sql_hide_protected_pages = ( $perm->have_perm(2, 'area_frontend', 0) || $this->config['check_frontend_prems']) ? '': 'AND (F.online & 0x04) = 0x00';
		$sql_hide_protected_pages = '';
        if (( $auth->auth['uid'] == 'nobody'))
		{
		  $sql_hide_protected_pages = 'AND (F.online & 0x04) = 0x00';
		} 
		
		
		//check perms for user with advanced frontend perms
		$check_frontendperms_in_page = ($auth->auth['uid'] != 'nobody' && $this->config['check_frontend_prems']);		
		$check_backendperms_in_page = $this->config['check_backend_prems'];		
		
		$sql = "SELECT
					D.idcatside, D.idcat, D.sortindex, D.is_start,
					E.idside,
					IF ((F.online & 0x03) = 0x01 OR ((F.online & 0x02) = 0x02 AND (UNIX_TIMESTAMP(NOW()) BETWEEN F.start AND F.end)) ,'1' ,'0') AS online,
					IF ( ((F.online & 0x04) = 0x04) ,'1' ,'0') AS protected,
					F.title, F.start, F.rewrite_use_automatic, F.rewrite_url, F.end, F.idsidelang, F.created, F.lastmodified,
					F.idtplconf, F.is_https
				FROM
					".$this->cfg->db('cat_side')." D LEFT JOIN
					".$this->cfg->db('side')." E USING(idside) LEFT JOIN
					".$this->cfg->db('side_lang')." F USING(idside)
				WHERE 
					D.idcat IN (". implode(',', array_keys($catinfos)). ")
					AND  F.idlang   = '".$this->config['idlang']."'
					$sql_hide_protected_pages
					ORDER BY D.idcatside";

		//try cache - on success jump out with return true
		$cache_key = $sql 
						.'|'.$this->config['link_extra_urlstring'] 
						.'|'.implode(',', $perm->get_group())
                        .'|'.$this->config['check_frontend_prems']
                        .'|'.$this->config['check_backend_prems'];
						
		$data = false;
		if ($data = $this->cache->getCacheEntry($cache_key))
		{
			$this->data = $data;
			return true;
		}					

	    $rs = $this->db->Execute($sql);
	    
	    if ($rs === false) 
	    {
	       return true;
	    }
		
		 while(! $rs->EOF ) 
		 {
			$idcatside_loop = $rs->fields['idcatside'];
			$idcat_loop = $rs->fields['idcat'];
			if ($check_frontendperms_in_page) 
			{
				if ($rs->fields['protected'] == 1 && ! $perm->have_perm(18, 'frontendpage', $idcatside_loop, $idcat_loop) ) 
				{
				    $rs->MoveNext();
					continue;
				}
			}
			
			if ($check_backendperms_in_page) 
			{
				if (! $perm->have_perm(17, 'side', $idcatside_loop, $idcat_loop) ) 
				{
				    $rs->MoveNext();
					continue;
				}
			}
			
			$link_loop = 'cms://idcatside='. $idcatside_loop;
							
			$this->data['data'][$idcatside_loop] = array( 'link'               =>$link_loop,
															'idcat'              =>$idcat_loop,
															'sortindex'          =>$rs->fields['sortindex'],
															'idside'             =>$rs->fields['idside'],
															'name'               =>$rs->fields['title'],
															'created'            =>$rs->fields['created'],
															'lastmodified'       =>$rs->fields['lastmodified'],
															'online'             =>$rs->fields['online'],
															'idsidelang'         =>$rs->fields['idsidelang'],
															'is_start'           =>$rs->fields['is_start'],
															'idtplconf'          =>$rs->fields['idtplconf'],
															'rewrite_use_automatic' =>$rs->fields['rewrite_use_automatic'],
															'rewrite_url'          =>$rs->fields['rewrite_url'],
															'user_protected'     =>$rs->fields['user_protected']
														);
			$rs->MoveNext();
		}
		
		//insert cache
		$this->cache->insertCacheEntry($cache_key, $this->data, 'frontend', 'tree');
		
		return true;
    }


	public function getLink($idcatside) { return $this->data['data'][$idcatside]['link']; }
    public function getIdcat($idcatside) { return $this->data['data'][$idcatside]['idcat']; }
    public function getRewriteUseAutomatic($idcatside) { return $this->data['data'][$idcatside]['rewrite_use_automatic']; }
    public function getRewriteUrlRaw($idcatside) { return $this->data['data'][$idcatside]['rewrite_url']; }
    public function getSortindex($idcatside) { return $this->data['data'][$idcatside]['sortindex']; }
    public function getCreatedTimestamp($idcatside) { return $this->data['data'][$idcatside]['created']; }
    public function getLastmodifiedTimestamp($idcatside) { return $this->data['data'][$idcatside]['lastmodified']; }
    public function getParent($idcatside) { return $this->getIdcat($idcatside); }
    public function getIsOnline($idcatside) { return $this->data['data'][$idcatside]['online']; }
    public function getIsProtected($idcatside) { return $this->data['data'][$idcatside]['user_protected']; }
    public function getIdtplconf($idcatside) { return $this->data['data'][$idcatside]['idtplconf']; }
    public function getTitle($idcatside) { return $this->data['data'][$idcatside]['name']; }
    public function getIsStart($idcatside) { return $this->data['data'][$idcatside]['is_start']; }
	public function getIdside($idcatside) { return $this->data['data'][$idcatside]['idside']; }
	public function getIdsidelang($idcatside) { return $this->data['data'][$idcatside]['idsidelang']; }
	
    public function getTitleByIdside($idside)
	{
    	foreach($this->data['data'] as $idcatside => $data)
		{
    		if($data['idside'] === $idside)
			{
    			return $data['name'];
    		}
    	}
    	return false;
	}
    

	/**
     * Returns the metadescription of a given idcatside
     * 
     * @param int $idcatside
     * @return str
     */
	public function getMetaDescription($idcatside) { return $this->_getMetaValFormSql($idcatside, 'meta_description'); }

	/**
     * Returns the MetaKeywords of a given idcatside
     * 
     * @param int $idcatside
     * @return  str
     */
	public function getMetaKeywords($idcatside) { return $this->_getMetaValFormSql($idcatside, 'meta_keywords'); }

	/**
     * Returns the MetaAuthor of a given idcatside
     * 
     * @param int $idcatside
     * @return str 
     */
	public function getMetaAuthor($idcatside) { return $this->_getMetaValFormSql($idcatside, 'meta_author'); }

	/**
     * Returns the MetaRobot Settings of a given idcatside
     *
     * @param int $idcatside
     * @return str
     */
	public function getMetaRobots($idcatside) { return $this->_getMetaValFormSql($idcatside, 'meta_robots'); }

	/**
     * Returns if a meta redirect of a page ist active
     *
     * @param int $idcatside
     * @return bool 
     */
    public function getMetaRedirectIsActive($idcatside) { return $this->_getMetaValFormSql($idcatside, 'meta_redirect'); }

	/**
     * Returns the redirect url of a page
     *
     * @param int $idcatside
     * @return str for an url or int for a idcatside
     */
    public function getMetaRedirectUrl($idcatside) { return $this->_getMetaValFormSql($idcatside, 'meta_redirect_url'); }

	/**
     * Returns the Summary/ Notice field of a given idcatside
     *
     * @param int $idcatside
     * @return str
     */
    public function getSummary($idcatside) { return $this->_getMetaValFormSql($idcatside, 'summary'); }

	/**
     * Returns the Author id of one page
     *
     * @param int $idcatside
     * @return int
     */
    public function getAuthor($idcatside) { return $this->_getMetaValFormSql($idcatside, 'author'); }

	/**
     * Returns if the page is only https reachable
     *
     * @param int $idcatside
     * @return int 0 for false and 1 for true
     */
    public function getIsHttps($idcatside) { return $this->_getMetaValFormSql($idcatside, 'is_https'); }
    
    /**
     * Returns all idcatsides of one cat as an array in a given order.
     * 
     * Possible Options:
     * $options['order'] - Set the order. Possible values are idcatside, sortindex, is_start, 
     * name, created, lastmodified, start, end, idside. Default is sortindex.
     * $options['order_dir'] - Orderdir is ASC or DESC. Default is ASC.
     * $options['show_startpage'] - (bool) true or false. Default is true
     * $options['hide_online'] - (bool) true or false. Default is false
     * $options['hide_offline'] - (bool) true or false. Default is true
	 * $options['hide_idcatsides'] - (arr) with idcatsides or (bool) false. Default is false
	 * $options['mincount'] - (int) or (bool) false. Results will be trashed if (int) mincount is not reached. Default is false
	 * $options['limit'] - (int) limit of elements to output. Default is null - means all
	 * $options['startpos'] - (int) start element for return. Default is 0
     * 
     * @param int $idcat
     * @param arr $options 
     * @return arr 
     */
    public function getIdcatsidesByIdcat($idcat, $options = array())
    {
    	//cast
    	$idcat = (int) $idcat;
    	$ret = array();
    	 
    	//handle options
    	$options['order'] = (isset($options['order'])) ? $options['order'] : 'sortindex';
    	$options['order'] = (in_array($options['order'], array('sortindex', 'is_start', 'name', 'created', 'lastmodified', 'start', 'end', 'idside'))) ? $options['order'] : 'sortindex';
    	$options['show_startpage'] = (isset($options['show_startpage'])) ?  $options['show_startpage'] : FALSE;
        $options['show_startpage'] = ($options['show_startpage'] == 'gt1' || $options['show_startpage'] == TRUE ) ?  $options['show_startpage'] : FALSE;
    	$options['order_dir'] = (isset($options['order_dir'])) ?  strtoupper($options['order_dir']) : 'ASC';
    	$options['order_dir'] = ($options['order_dir'] == 'DESC') ?  'DESC' : 'ASC';
    	$options['hide_online'] = (isset($options['hide_online'])) ?  (bool) $options['hide_online'] : FALSE;
    	$options['hide_offline'] = (isset($options['hide_offline'])) ?  (bool) $options['hide_offline'] : TRUE;
        $options['hide_idcatsides'] = is_array($options['hide_idcatsides']) ? $options['hide_idcatsides'] : array();
	$options['mincount'] = (isset($options['mincount'])) ? (int) $options['mincount'] : FALSE;
        $options['limit'] = (isset($options['limit']) && !empty($options['limit'])) ? $options['limit'] : null;
	$options['startpos'] = (isset($options['startpos']) && !empty($options['startpos'])) ? $options['startpos'] : 0;

        $sql_hide_idcatsides = '';
        if (count($options['hide_idcatsides']) > 0)
        {
                foreach ($options['hide_idcatsides'] AS $k=>$v)
                {
                        $options['hide_idcatsides'][$k] = (int) $v;
                }
                $sql_hide_idcatsides = "AND D.idcatside NOT IN (" . implode(',', array_unique($options['hide_idcatsides'])) . ")";
        }
    	
    	
    	$sql_order = 'D.sortindex';
    	switch($options['order'])
    	{
    		case 'idcatside':
    		case 'sortindex':
    		case 'is_start':
    			$sql_order = 'D.'.$options['order'];
    			break;
    		case 'name':
    			$sql_order = 'F.title';
    			break;
    		case 'created':
    		case 'lastmodified':
    		case 'start':
    		case 'end':
    			$sql_order = 'F.'.$options['order'];
    			break;
    		case 'idside':
    			$sql_order = 'E.'.$options['order'];
    			break;
    	}
    	
    	$sql_order_dir = ($options['order_dir'] == 'ASC') ? 'ASC' : 'DESC';
    	
    	//get sql
    	$sql = "SELECT
					D.idcatside
				FROM
					".$this->cfg->db('cat_side')." D LEFT JOIN
					".$this->cfg->db('side')." E USING(idside) LEFT JOIN
					".$this->cfg->db('side_lang')." F USING(idside)
				WHERE 
					D.idcat = $idcat
					AND  F.idlang   = '".$this->config['idlang']."'
                                        $sql_hide_idcatsides
				ORDER BY 
					$sql_order $sql_order_dir";				

	    $rs = $this->db->Execute($sql);
	    
	    if ($rs === false) 
	    {
	       return $ret;
	    }

		$pos_startpage = -1;
		while(! $rs->EOF ) 
		 {
			if ($options['hide_online'] && $this->getIsOnline($rs->fields['idcatside']) == 1)
			{
				$rs->MoveNext();
				continue;
			}
			
			if ($options['hide_offline'] && $this->getIsOnline($rs->fields['idcatside']) == 0)
			{
				$rs->MoveNext();
				continue;
			}

			if (! $options['show_startpage'] && $this->getIsStart($rs->fields['idcatside']) == 1)
			{
				$rs->MoveNext();
				continue;
			}
			
			if ( $this->getIdcat($rs->fields['idcatside']) > 0)
			{
				if ($this->getIsStart($rs->fields['idcatside']) == 1)
				{
					$pos_startpage = count($ret);
				}

				array_push($ret, $rs->fields['idcatside']);
			}
							
			$rs->MoveNext();
		}

		if (count($ret) < 2 && $options['show_startpage'] == 'gt1' && $pos_startpage >= 0)
		{
			return array();
		}
		 
		// Limit and Startpos
		if( is_numeric($options['limit']) 
			|| !empty($options['startpos']) ) 
		{
			$count = count($ret);
			
			if( $count >= $options['limit'] 
				 || !empty($options['startpos']) ) 
			{
				if( $count > $options['limit'] ) 
				{
					$count = $options['limit'];
				}
				 
				$ret_tmp = array();
				for( $i = $options['startpos']; $i < $count; ++$i )
				{
					$ret_tmp[] = $ret[$i];
				}
				
				$ret = $ret_tmp;
			}
		} 
    	
    	return $ret;
    }
    
	/**
	 * Checks recursive if the given idcatside is a child of the givent $idcat
	 *
	 * @param int $idcatside pageid to check
	 * @param int $idcat_parent needed parent folder-id
	 *
	 * @return bool
	 */
	public function isChildOf($idcatside, $idcat_parent)
	{
		if( $this->getIdcat($idcatside) == $idcat_parent ||
			$this->catinfos->getParent($this->getIdcat($idcatside)) == $idcat_parent ||
			$this->catinfos->getRootparent($this->getIdcat($idcatside)) == $idcat_parent )
		{
			return true;
		}
		else if( $this->getIdcat($idcatside) == $this->catinfos->getRootparent($this->getIdcat($idcatside)) ||
				 $this->catinfos->getRootparent($this->getIdcat($idcatside)) != 0 )
		{
			return false;
		}

		return $this->catinfos->isChildOf($this->catinfos->getIdcat(), $idcat_parent);
	}
    
    public function getPageinfoDataArray()
    {
    	return $this->data['data'];
    }

	public function getIdlang()
	{
    	return $this->config['idlang'];
    }

    public function getCheckFrontendperms()
	{
    	return $this->config['check_frontend_prems'];
    }

    public function getCheckBackendperms()
	{
    	return $this->config['check_backend_prems'];
    }


    
    /*
     * PRIVATE METHODS STARTS HERE
     */
    
    /**
     * Returns the sqlfield of the given idcatside
     * 
     * @param int $idcatside
     * @param str $sqlfield Must be a value of the side_lang
     * @return str
     */
	protected function _getMetaValFormSql($idcatside, $sqlfield)
	{
		static $cache = array();		
		$ret = FALSE;
     	
     	//cast
     	$idcatside = (int) $idcatside;
     	$sqlfield = addslashes($sqlfield);
     	if ($idcatside < 1 || $sqlfield == '')
     	{
     		return $ret;
     	}

		if (array_key_exists($idcatside, $cache))
		{
			return $cache[$idcatside][$sqlfield];
		}

     	//run sql
    	$sql = "SELECT
					D.idcatside,
					F.meta_author, F.meta_description, F.meta_keywords, F.meta_robots, F.meta_redirect,
					F.meta_redirect_url, F.summary, F.author, F.is_https
				FROM
					".$this->cfg->db('cat_side')." D LEFT JOIN
					".$this->cfg->db('side')." E USING(idside) LEFT JOIN
					".$this->cfg->db('side_lang')." F USING(idside)
				WHERE 
					D.idcatside = $idcatside
					AND  F.idlang   = '".$this->config['idlang']."' ";				

	    $rs = $this->db->Execute($sql);
	    
	    if ($rs === false) 
	    {
	       return $ret;
	    }
		if (! $rs->EOF )  
		{
			$cache[$idcatside]['meta_author'] = $rs->fields['meta_author'];
			$cache[$idcatside]['meta_description'] = $rs->fields['meta_description'];
			$cache[$idcatside]['meta_keywords'] = $rs->fields['meta_keywords'];
			$cache[$idcatside]['meta_robots'] = $rs->fields['meta_robots'];
			$cache[$idcatside]['meta_redirect'] = $rs->fields['meta_redirect'] == 1 ? TRUE : FALSE;
			$cache[$idcatside]['meta_redirect_url'] = $rs->fields['meta_redirect_url'];
			$cache[$idcatside]['summary'] = $rs->fields['summary'];
			$cache[$idcatside]['author'] = $rs->fields['author'];
			$cache[$idcatside]['is_https'] = $rs->fields['is_https'];

			$ret= $cache[$idcatside][$sqlfield];
		}
		
		return $ret;
	}

} 

?>