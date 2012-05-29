<?php
class SF_LIB_CatinfosCustom extends SF_LIB_ApiObject {

    protected $data = array( 'data' => array(),
					   'parent_dependace' => array()
    				 );
    				    					
    protected $config = array( 'idlang' => 0,
						 'check_frontend_prems' => false, 
						 'check_backend_prems' => false,    						
    					 'is_generated' => false
    					);
    protected $cache;
    protected $db;
	protected $cfg;
    
    public function __construct()
	{
    	$this->cache = sf_factoryGetObjectCache('UTILS', 'DbCache');
    	$this->db = sf_factoryGetObjectCache('DATABASE', 'Ado');
		$this->cfg = sf_api('LIB', 'Config');
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
		
		//check perm: user have perm to see pages with the protected flag
		$sql_hide_protected_cats = '';
        if (( $auth->auth['uid'] == 'nobody'))
		{
		  $sql_hide_protected_cats = 'AND (C.visible & 0x04) = 0x00';
		}
		
		//check perms for user with advanced frontend perms
		$check_frontendperms_in_cat = ($auth->auth['uid'] != 'nobody' && $this->config['check_frontend_prems']);		
		$check_backendperms_in_cat = $this->config['check_backend_prems'];
		
		//special handling for cat startpages if https is on
		$startpage_is_https = array();
		if ($this->cfg->client('https') == '1')
		{
			$sql = "SELECT DISTINCT
						D.idcat
					FROM
						".$this->cfg->db('cat_side')." D LEFT JOIN
						".$this->cfg->db('side_lang')." F USING(idside)
					WHERE 
						D.is_start = 1
						AND F.is_https = 1
						AND  F.idlang   = '".$this->config['idlang']."'
						";
			
			$rs_https_cats = $this->db->Execute($sql);
			
			 if ($rs_https_cats !== false) 
			 {
				 while (! $rs_https_cats->EOF ) 
				 {
				 	array_push($startpage_is_https, $rs_https_cats->fields['idcat']);
				 	$rs_https_cats->MoveNext();
				 }
			 }
		}
		
		$sql= "SELECT 
					B.idcat, B.parent, B.sortindex,
					C.rewrite_use_automatic, C.rewrite_alias, C.idcatlang, C.author, C.created, C.lastmodified, C.description,
					IF ( ((C.visible & 0x03) = 0x01) ,'1' ,'0') AS visible, 
					IF ( ((C.visible & 0x04) = 0x04) ,'1' ,'0') AS protected, 
					C.idtplconf, C.name
				FROM
					".$this->cfg->db('cat')." B LEFT JOIN
					".$this->cfg->db('cat_lang')." C USING(idcat) LEFT JOIN
					".$this->cfg->db('tpl_conf')." D USING(idtplconf) LEFT JOIN
					".$this->cfg->db('tpl')." E USING(idtpl)
				WHERE 
					C.idlang = '".$this->config['idlang']."'  
					$sql_hide_protected_cats
					ORDER BY parent, sortindex";
					
		//try cache - on success jump out with return true
		$cache_key = $sql 
                        . '|'.$this->config['check_frontend_prems']
                        . '|'.$this->config['check_backend_prems'];
						
		if ($data = $this->cache->getCacheEntry($cache_key))
		{
			$this->data = $data;
			return true;
		}					
		
		$rs = $this->db->Execute($sql);
		
        if ($rs === false) {
	       return true;
	    }
		
		while(! $rs->EOF ) {
			$idcat_loop = $rs->fields['idcat'];
		
			if ($check_frontendperms_in_cat)
			{
				if ($rs->fields['protected'] == 1 && ! $perm->have_perm(2, 'frontendcat', $idcat_loop) )
				{
				    $rs->MoveNext();
					continue;
				}
			} 
			
			if ($check_backendperms_in_cat)
			{
				if (! $perm->have_perm(1, 'cat', $idcat_loop) )
				{
				    $rs->MoveNext();
					continue;
				}
			}
			
			$link_loop = 'cms://idcat=' . $idcat_loop;
			
			$this->data['data'][$idcat_loop] = array(	'idcat' =>$idcat_loop,
														'link' =>$link_loop,
														'idcatlang' =>$rs->fields['idcatlang'],
														'rewrite_use_automatic' =>$rs->fields['rewrite_use_automatic'],
														'rewrite_alias' =>$rs->fields['rewrite_alias'],
														'author' =>$rs->fields['author'],
														'created' =>$rs->fields['created'],
														'lastmodified' =>$rs->fields['lastmodified'],
														'parent' =>$rs->fields['parent'],
														'visible' =>$rs->fields['visible'],
														'idtplconf' =>$rs->fields['idtplconf'],
														'name' =>$rs->fields['name'],
														'description' =>$rs->fields['description'],
														'https' => (in_array($idcat_loop, $startpage_is_https)) ? TRUE : FALSE
														);
			
			$this->data['parent_dependace'][$rs->fields['parent']][$rs->fields['sortindex']] = $idcat_loop;
			
			$rs->MoveNext();
		}
		
		//insert cache
		$this->cache->insertCacheEntry($cache_key, $this->data, 'frontend', 'tree');
		
		$this->config['is_generated'] = true;
		
		return true;
    }
    
    public function getCatinfoDataArray()
	{
    	return $this->data['data'];
    }
    
    public function getParentDependanceDataArray()
	{
    	return $this->data['parent_dependace'];
    } 
    
    public function getLink($idcat) { return $this->data['data'][$idcat]['link']; }
    public function getIdcatlang($idcat) { return $this->data['data'][$idcat]['idcatlang']; }
    public function getRewriteUseAutomaticRaw($idcat) { return $this->data['data'][$idcat]['rewrite_use_automatic']; }
    public function getRewriteAliasRaw($idcat) { return $this->data['data'][$idcat]['rewrite_alias']; }
    public function getIduser($idcat) { return $this->data['data'][$idcat]['author']; }
    public function getCreatedTimestamp($idcat) { return $this->data['data'][$idcat]['created']; }
    public function getLastmodifiedTimestamp($idcat) { return $this->data['data'][$idcat]['lastmodified']; }
    public function getParent($idcat) { return $this->data['data'][$idcat]['parent']; }
    public function getIsOnline($idcat) { return $this->data['data'][$idcat]['visible']; }
    public function getIsProtected($idcat) { return $this->data['data'][$idcat]['protected']; }
    public function getIdtplconf($idcat) { return $this->data['data'][$idcat]['idtplconf']; }
    public function getTitle($idcat) { return $this->data['data'][$idcat]['name']; }
    public function getDescription($idcat) { return $this->data['data'][$idcat]['description']; }
    public function getStartpageIsHttps($idcat) { return $this->data['data'][$idcat]['description']; }

    public function getRootParent($idcat)
	{
    	$idcat = (int) $idcat;
    	$parent = $this->data['data'][$idcat]['parent'];
    	if ($parent < 1 )
		{
    		return $idcat;
    	}

    	return $this->getRootParent($parent);	
    }
    
    public function getIdcatsideStartpage($idcat)
	{
    	$idcat = (int) $idcat;

    	$sql = "SELECT CS.idcatside
				FROM
					".$this->cfg->db('cat_side')." CS
				WHERE 
					CS.idcat = '".$idcat."'
					AND CS.is_start = 1";
		
		$rs = $this->db->Execute($sql);
		
		if ($rs === false) {
			return false;
		}
		
		if ($rs->EOF ) {
			return false;
		}

		return $rs->fields['idcatside'];
    }
    
    /**
     * Returns all child idcats of one cat as an array in a given order. If no idcat was found
     * returns empty array
     * 
     * Possible Options:
     * $options['order'] - Set the order. Possible values are sortindex, 
     * name, created, lastmodified. Default is sortindex.
     * $options['order_dir'] - Orderdir is ASC or DESC. Default is ASC.
     * $options['hide_online'] - (bool) true or false. Default is false
     * $options['hide_offline'] - (bool) true or false. Default is true
	 * $options['hide_idcats'] - (arr) with idcats or (bool) false. Default is false
	 * $options['limit'] - (int) limit of elements to output. Default is null - means all
	 * $options['startpos'] - (int) start element for return. Default is 0
	 *
     * @param int $idcat
     * @param arr $options 
     * @return arr 
     */
	public function getChilds($idcat, $options = array())
    {
    	//cast
    	$idcat = (int) $idcat;
    	$ret = array();
    	
    	//handle options
    	$options['order'] = (isset($options['order'])) ? $options['order'] : 'sortindex';
    	$options['order'] = (in_array($options['order'], array('sortindex', 'name', 'created', 'lastmodified'))) ? $options['order'] : 'sortindex';
    	$options['order_dir'] = (isset($options['order_dir'])) ?  strtoupper($options['order_dir']) : 'ASC';
    	$options['order_dir'] = ($options['order_dir'] == 'DESC') ?  'DESC' : 'ASC';
    	$options['hide_online'] = (isset($options['hide_online'])) ?  (bool) $options['hide_online'] : false;
    	$options['hide_offline'] = (isset($options['hide_offline'])) ?  (bool) $options['hide_offline'] : true;
		$options['hide_idcats'] = is_array($options['hide_idcats']) ? $options['hide_idcats'] : array();
		$options['limit'] = (isset($options['limit']) && !empty($options['limit'])) ? $options['limit'] : null;
		$options['startpos'] = (isset($options['startpos']) && !empty($options['startpos'])) ? $options['startpos'] : 0;

		$sql_hide_idcats = '';
		if (count($options['hide_idcats']) > 0)
		{
			foreach ($options['hide_idcats'] AS $k=>$v)
			{
				$options['hide_idcats'][$k] = (int) $v;
			}
			$sql_hide_idcats = "AND CL.idcat NOT IN (" . implode(',', array_unique($options['hide_idcats'])) . ")";
		}
    	
    	
    	$sql_order = 'C.sortindex';
    	switch($options['order'])
    	{
    		case 'sortindex':
    			$sql_order = 'C.'.$options['order'];
    			break;
    		case 'name':
    		case 'created':
    		case 'lastmodified':
    			$sql_order = 'CL.'.$options['order'];
    			break;
    	}
    	
    	$sql_order_dir = ($options['order_dir'] == 'ASC') ? 'ASC' : 'DESC';
    	
    	//get sql
    	$sql = "SELECT
					CL.idcat
				FROM
					".$this->cfg->db('cat')." C LEFT JOIN
					".$this->cfg->db('cat_lang')." CL USING(idcat)
				WHERE 
					C.parent = $idcat
					AND  CL.idlang   = '".$this->config['idlang']."'
					$sql_hide_idcats
				ORDER BY 
					$sql_order $sql_order_dir";				

	    $rs = $this->db->Execute($sql);
	    
	    if ($rs === false) 
	    {
	       return $ret;
	    }
		
		 while(! $rs->EOF ) 
		 {
			
			if ($options['hide_online'] && $this->getIsOnline($rs->fields['idcat']) == 1)
			{
				$rs->MoveNext();
				continue;
			}
			
			if ($options['hide_offline'] && $this->getIsOnline($rs->fields['idcat']) == 0)
			{
				$rs->MoveNext();
				continue;
			}
			
			if ( isset($this->data['data'][$rs->fields['idcat']]))
			{
				array_push($ret, $rs->fields['idcat']);
			}

			$rs->MoveNext();
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

	public function hasChilds($idcat, $options = array())
	{
		$options['hide_offline'] = (isset($options['hide_offline'])) ?  (bool) $options['hide_offline'] : TRUE;
		$options['hide_idcats'] = is_array($options['hide_idcats']) ? $options['hide_idcats'] : array();

		if (count($options['hide_idcats']) > 0)
		{
			foreach ($options['hide_idcats'] AS $k=>$v)
			{
				$options['hide_idcats'][$k] = (int) $v;
			}
			$options['hide_idcats'] = array_unique($options['hide_idcats']);
		}

		if (!array_key_exists($idcat, $this->data['parent_dependace']))
		{
			return FALSE;
		}

		$childs = $this->data['parent_dependace'][$idcat];
		$childs_out = array();
		foreach($childs AS $v)
		{
			if ($options['hide_offline'] && $this->getIsOnline($v) != 1)
			{
				continue;
			}

			if (in_array($v, $options['hide_idcats']))
			{
				continue;
			}

			array_push($childs_out, $v);
		}

		return ( count($childs_out) > 0);
	}
    
    /**
	 * Checks recursive if a idcat is a child of another idcat.
	 *
	 * @param Int $idcat_child Child folder-Id
	 * @param Int $idcat_parent Needed parent folder-Id
	 *
	 * @return bool
	 */
	public function isChildOf($idcat_child, $idcat_parent)
	{
		
		if( $idcat_child == $idcat_parent ||
			$this->getParent($idcat_child) == $idcat_parent ||
			$this->getRootparent($idcat_child) == $idcat_parent )
		{
			return TRUE;
		}
		else if( $idcat_child == $this->getRootparent($idcat_child) || $this->getRootparent($idcat_child) != 0 )
		{
			return FALSE;
		}

		return $this->isChildOf($idcat_child, $idcat_parent);
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
    
    public function getIsGenerated()
	{
    	return $this->config['is_generated'];
    }
    
    public function flushAll()
	{
    	$this->data = array( 'data' => array(),
					   'parent_dependace' => array()
    				 );
    				    					
    	$this->config = array( 'idlang' => 0, 
						 'check_frontend_prems' => false,    						
    					 'is_generated' => false
    					);
    }
} 

?>