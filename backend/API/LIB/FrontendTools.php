<?php
class SF_LIB_FrontendTools extends SF_LIB_ApiObject
{	
	public function __construct() 
	{
        //set singelton
        $this->_API_setObjectIsSingleton(TRUE);
        
        //db object
        $this->db = $GLOBALS['db'];
        
        //db names
        $this->dbnames = $GLOBALS['cms_db'];
        $this->req = sf_api('LIB', 'WebRequest');
		$this->cfg = sf_api('LIB', 'Config');
	}

    public function setDb($db)
    {
        $this->db = $db;
    }

    public function setDbNames($cms_db)
    {
        $this->dbnames = $cms_db;
    }

	function getIdtplIdtplconf($idcat, $idside, $lang)
	{
		$idcat = (int) $idcat;
    	$idside = (int) $idside;
    	$lang = (int) $lang;

		$idtpl = FALSE;
		$idtplconf = FALSE;

    	//search idtplconf and idtpl
		//first look for a page template
		$sql = "SELECT
					A.idtplconf, B.idtpl
				FROM
					".$this->dbnames['side_lang'] ." A
					LEFT JOIN ". $this->dbnames['tpl_conf']." B USING(idtplconf)
				WHERE
					A.idside='$idside'
					AND A.idlang='$lang'
					AND A.idtplconf!='0'";

		$this->db->query($sql);

		//no page template found, use the cat template
		if (!$this->db->affected_rows())
		{
			$sql = "SELECT
						A.idtplconf, B.idtpl
					FROM
						". $this->dbnames['cat_lang']." A
						LEFT JOIN ".$this->dbnames['tpl_conf']." B USING(idtplconf)
					WHERE
						A.idcat='$idcat'
						AND A.idlang='$lang'
						AND A.idtplconf!='0'";
			$this->db->query($sql);
		}

		//idtpconf and idtpl was found, generate templateconfig
		if ($this->db->next_record())
		{
			$idtpl = $this->db->f('idtpl');
			$idtplconf = $this->db->f('idtplconf');

		}

		return array('idtpl' => $idtpl, 'idtplconf' => $idtplconf);
	}
	
	/**
     * Generates an array with all needed moddata including all containers.
     * Returns an empty array if generation of the template config fails.
     * 
     * The "containernumber" returnarray have some aliases. The following values
     * contains the same value config=0, view=1, edit=2, name=3, output=4,
     * idmod=5, verbose=6  
     * 
     * @param int $idcat
     * @param int $idside
     * @param int $lang
     * @return bool false | array [int idtpl, int idtplconf, 
     *                int containernumber [config, view, edit, name, output, idmod, verbose, 0, 1, 2, 3, 4, 5, 6] ]
     */
    function getTemplateConfig($idcat, $idside, $lang)
    {
    	$idcat = (int) $idcat;
    	$idside = (int) $idside;
    	$lang = (int) $lang;
    	
    	$container = array();

		$arr = $this->getIdtplIdtplconf($idcat, $idside, $lang);
		$idtpl = $arr['idtpl'];
		$idtplconf =$arr['idtplconf'];
		if (! $idtpl || ! $idtplconf)
		{
			return $container;
		}
			
		$sql = "SELECT
					A.config, A.view, A.edit, B.container, C.name, C.output, C.idmod, C.verbose
				FROM
					".$this->dbnames['container_conf']." A
					LEFT JOIN ".$this->dbnames['container']." B USING(idcontainer)
					LEFT JOIN ".$this->dbnames['mod']." C USING(idmod)
				WHERE
					A.idtplconf='".$idtplconf."'";

		$this->db->query($sql);

		$container['idtpl'] = $idtpl;
		$container['idtplconf'] = $idtplconf;

		while ($this->db->next_record())
		{
			$containerid = $this->db->f('container');

			$container[$containerid] = array (
										'0' => $this->db->f('config'),
										'1' => $this->db->f('view'),
										'2' => $this->db->f('edit'),
										'3' => $this->db->f('name'),
										'4' => $this->db->f('output'),
										'5' => $this->db->f('idmod'),
										'6' => $this->db->f('verbose')
									);

			//make named aliases for better reading
			$container[$containerid]['config'] = $container[$containerid]['0'];
			$container[$containerid]['view'] = $container[$containerid]['1'];
			$container[$containerid]['edit'] = $container[$containerid]['2'];
			$container[$containerid]['name'] = $container[$containerid]['3'];
			$container[$containerid]['output'] = $container[$containerid]['4'];
			$container[$containerid]['idmod'] = $container[$containerid]['5'];
			$container[$containerid]['verbose'] = $container[$containerid]['6'];
		}

	    return $container;
    }
    
    /**
     * Returns Content Raw data of one page 
     * 
     * idcontent -> Autincrement id
	 * idsidelang -> Unique page id, regard lang and the oageid idside
	 * container -> Container id of the cms:lay tag <cms:lay type="container" id="XX" />
	 * number -> container repeat id. Starts with 1,2,3...n . Every module can repeat his output in a container
	 * idtype -> Contenttype identifier (text, wysiwyg,...), see below for details
	 * typenumber -> cms:tag id
	 * 
	 * Assignment IDTYPE > TYPE
	 * 1 > text
	 * 2 > wysiwyg
	 * 3 > textarea
	 * 4 > img
	 * 5 > imgdescr
	 * 6 > link
	 * 7 > linkdescr
	 * 8 > linktarget
	 * 9 > sourcecode
	 * 10 > file
	 * 11 > filedescr
	 * 12 > filetarget 
	 * 13 > wysiwyg2
	 * 14 > select
	 * 15 > hidden
	 * 16 > checkbox
	 * 17 > radio
	 * 18 > date
	 * 19 > UNUSED
	 * 20 > checkboxsave (?)
     * 
     * @param int $idside
     * @param int $lang
     * @return array $content[container][number][idtype][typenumber]
     */
    function getContentArray($idside, $lang)
    {
    	$idside = (int) $idside;
    	$lang = (int) $lang;
    	
    	$sql = "SELECT 
					A.idcontent, container, number, idtype, typenumber, value 
				FROM 
					".$this->dbnames['content']." A 
					LEFT JOIN ".$this->dbnames['side_lang']." B USING(idsidelang) 
				WHERE 
					B.idside='$idside' 
					AND B.idlang='$lang' 
				ORDER BY 
					number";
					
		$this->db->query($sql);
		
		$contetn = array();
		
		while ($this->db->next_record()) 
		{
			$content[$this->db->f('container')][$this->db->f('number')][$this->db->f('idtype')][$this->db->f('typenumber')] 
					= array($this->db->f('idcontent'), $this->db->f('value'));
		}
    	
    	return $content;
    }

	function getIdlay($idtpl)
	{
		$idtpl = (int) $idtpl;
		$idlay = FALSE;

		$sql = "SELECT
					A.idlay
				FROM
					".$this->dbnames['tpl']." A
					LEFT JOIN ".$this->dbnames['lay']." B USING(idlay)
				WHERE
					A.idtpl='$idtpl'";

		$this->db->query($sql);

		if ( $this->db->next_record() )
		{
			$idlay = $this->db->f('idlay');
		}

		return $idlay;
	}

    
    function getLayoutArray($idtpl)
    {
    	$idtpl = (int) $idtpl;
    	
    	$out = array();
    	
    	$sql = "SELECT 
					A.idlay, B.doctype, B.doctype_autoinsert, B.code 
				FROM 
					".$this->dbnames['tpl']." A 
					LEFT JOIN ".$this->dbnames['lay']." B USING(idlay) 
				WHERE 
					A.idtpl='$idtpl'";
		
		$this->db->query($sql);
		
		if ( $this->db->next_record() )
		{
			$out['idlay'] = $this->db->f('idlay');
			$out['layout'] = $this->db->f('code');
			$out['doctype'] = $this->db->f('doctype');
			$out['doctype_autoinsert'] = $this->db->f('doctype_autoinsert');
		
			$idlay = $this->db->f('idlay');
			

		
			if ($out['doctype_autoinsert'] == 1) 
			{
				$out['layout'] = '{%sf_doctype%}' . $out['layout'];
			}
		}
			
		return $out;
    }
    
    public function mapCMSPHPCACHEToPhp($code)
    {
    	$code = str_replace(array('<CMSPHP:CACHE>', '</CMSPHP:CACHE>', '<DEDIPHP:CACHE>', '</DEDIPHP:CACHE>'), 
    						array('<?PHP ', ' ?>', '<?PHP ', ' ?>'), 
    						$code);
    	
    	return $code;
    }
    
 	public function mapCMSPHPToPhp($code)
    {
    	$code = str_replace(array('<CMSPHP>', '</CMSPHP>', '<DEDIPHP>', '</DEDIPHP>'), 
    						array('<?PHP ', ' ?>', '<?PHP ', ' ?>'), 
    						$code);
    	
    	return $code;
    }
    
    public function extractCmsTags($in, $sort='') 
    {
		//alle CMS Tags extrahieren
		//Hinterher befindet sich in
		//$matches[0][x] -> der gesamte Tag
		//$matches[3][x] -> alle Attribute
		//$matches[4][x] -> Content zwischen <cms></cms>
		//todo: 2remove
		preg_match_all ('/<(dedi|cms):(mod|lay) ([^>]*)\/?>([^<\/]*<\/$1>)?/i', $in, $matches);
		$match_count = count($matches[0]);
	
		//Attribute filtern
		if($match_count) 
		{
			for ($i=0; $i< $match_count; $i++) 
			{
				$attributes = $matches[3][$i];
				$preg = '/(([A-Za-z_:]|[^\\x00-\\x7F])([A-Za-z0-9_:.-]|[^\\x00-\\x7F])*)'."([ \\n\\t\\r]+)?(=([ \\n\\t\\r]+)?(\"[^\"]*\"|'[^']*'|[^ \\n\\t\\r]*))?/";
				if (preg_match_all($preg, $attributes, $regs)) 
				{
					$valCounter = 0;
					for ($counter=0; $counter<count($regs[1]); $counter++) 
					{
						$name = $regs[1][$counter];
						$check = $regs[0][$counter];
						$value = $regs[7][$valCounter];
						if (trim($name) == trim($check))
						{
							$arrAttr[] = strtoupper(trim($name));
						}
						else 
						{
							if (substr($value, 0, 1) == '"' || substr($value, 0, 1) == "'")
							{
								$value = substr($value, 1, -1);
							}
							
							$arrAttr[strtolower(trim($name))] = trim($value);
							$valCounter++;
						}
					}
	
					// Alle Attribute f�r returnwert aufbereiten
	//				$arrAttr['id'] = sprintf("%01d",$arrAttr['id']);
					if ($sort == 'type') 
					{
						//$out[type][id][attributekey] = attributevalue
						$out[$arrAttr['type']][$arrAttr['id']] = $arrAttr;
						$out[$arrAttr['type']][$arrAttr['id']]['in_tag'] = $matches[4][$i];
					} 
					else 
					{
						//$out[$i][attributekey] = attributevalue
						$out[$i] = $arrAttr;
						$out[$i]['full_tag'] = $matches[0][$i];
						$out[$i]['in_tag'] = $matches[4][$i];
					}
					unset($arrAttr);
				}
			}
			
			return $out;
		} 
		
		return false;
	}
	
	/**
	 * 
	 * @param int $idlay
	 * @param str $sf_slash_closing_tag
	 * @param str $lang_charset
     * @param str $doctype
	 * @param $backendcfg [bool is_backend, bool is_https, str cms_html_path, str skin, 
	 * 						str lang_gen_deletealert, obj perm, str view, int idcatside, int idcat, int lang]
	 */
	public function getContainerHead($idlay, $sf_slash_closing_tag, $lang_charset, $doctype, $backendcfg = array())
	{
		$code ='';

        if ( $doctype != 'xml')
        {
            $code .= "<!--START head//-->\n";
            $code .= "<meta name=\"generator\" content=\"Sefrengo / www.sefrengo.org\" ".$sf_slash_closing_tag.">\n";
        }

		$code .= '<CMSPHP> if ($cfg_client[\'url_rewrite\'] == \'2\'){ echo \'<base href="\'.htmlspecialchars(str_replace(\'{%http_host}\',  $_SERVER[\'HTTP_HOST\'], ';
		if (count($backendcfg) > 0 && $backendcfg['is_backend'])
		{
			$code .= '$cfg_client[\'path_http_edit\'] . $cfg_client[\'path_rel\']';
		}
		else
		{
			$code .= '$cfg_client[\'path_http\'] . $cfg_client[\'path_rel\']';
		}
		$code .= '), ENT_COMPAT, \'utf-8\').\'"'.$sf_slash_closing_tag.'>\'."\n"; }</CMSPHP>';
		$code .= '{%sf_head_title%}{%sf_meta_author%}{%sf_meta_description%}{%sf_meta_keywords%}{%sf_meta_robots%}{%sf_meta_content_type%}';
		
		//headvars only for backend
		if (count($backendcfg) > 0)
		{
			if ($backendcfg['is_backend'])
			{
				//$cfg_client['https'] == 1 && $con_side[$idcatside]['is_https'] == 1
				
				$html_path = $backendcfg['cms_html_path'];
				if ($backendcfg['is_https'])
				{
					$html_path = str_replace('http://', 'https://', $html_path); 
				}
				
				$code .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"". $html_path.'tpl/'.$backendcfg['skin'].'/css/sf_hovermenu.css' ."\" />\n";
				
				$code .= "<script src=\"". $html_path.'tpl/'.$backendcfg['skin'].'/js/init.sefrengo.js' ."\" type=\"text/javascript\"></script>\n";
				$code .= "<script src=\"". $html_path.'tpl/'.$backendcfg['skin'].'/js/lib/jquery.min.js' ."\" type=\"text/javascript\"></script>\n";
				
				$code .= "<script type=\"text/javascript\">/* <![CDATA[ */
					SF.Config.debug = false;
					SF.Config.backend_dir = '".$html_path."'; // e.g. /backend/
					SF.Config.js_dir = SF.Config.backend_dir + 'tpl/".$backendcfg['skin']."/js/';
					SF.Config.css_dir = SF.Config.backend_dir + 'tpl/".$backendcfg['skin']."/css/';
					SF.Config.img_dir = SF.Config.backend_dir + 'tpl/".$backendcfg['skin']."/img/';
				/* ]]> */</script>";
				
				$code .= "<script src=\"". $html_path.'tpl/'.$backendcfg['skin'].'/js/jquery.frontend.js' ."\" type=\"text/javascript\"></script>\n";
				
				$code .= "<script type=\"text/javascript\">
					/* <![CDATA[ */
					//Sefrengo langstrings
					var sf_lng_delete_confirm = '".$backendcfg['lang_gen_deletealert']."';
					";
				
				//edit page perm
				$copycontent_disabled = ($backendcfg['perm']->have_perm(19, 'side', $backendcfg['idcatside'], $backendcfg['idcat']) && $backendcfg['view'] !='preview') ? 'false': 'true';
				$code .= '
					// (de)activate Sefrengo langcopy
					try {	
						window.parent.con_nav.sf_setCurrentIdcatside('.$backendcfg['idcatside'].', '.$copycontent_disabled.', '.$backendcfg['lang'].')
					} catch (e) {
	
					}
					/* ]]> */
					</script>';
			}
		}

		//CSS and JS file include
		$sql = "SELECT
					C.filetype, D.dirname, B.filename
				FROM
					". $this->dbnames['lay_upl'] ." A
					LEFT JOIN ". $this->dbnames['upl'] ." B USING(idupl)
					LEFT JOIN ". $this->dbnames['filetype'] ." C USING(idfiletype)
					LEFT JOIN ". $this->dbnames['directory'] ." D on B.iddirectory=D.iddirectory
				WHERE
					A.idlay='$idlay'
				ORDER BY
					A.sortindex ASC";
		$this->db->query($sql);
		while ($this->db->next_record())
		{
			//TODO BUG: JS and CSS files have not an iddirectory
			if ($this->db->f('filetype') == 'js')
			{
				$code .= "<script src=\"". $this->cfg->client('path_js_rel').$this->db->f('dirname').$this->db->f('filename')."\" type=\"text/javascript\"></script>\n";
			}
			else if ($this->db->f('filetype') == 'css')
			{
				$code .= "<link rel=\"stylesheet\" href=\"".  $this->cfg->client('path_css_rel').$this->db->f('dirname').$this->db->f('filename')."\" type=\"text/css\" ".$sf_slash_closing_tag.">\n";
			}
		}
		
        if ( $doctype != 'xml')
        {
            $code .= "<!--END head//-->\n";
        }
		
		return $code;
		
	}
	
	public function getContainerConfig($is_backend)
	{
		if ($is_backend)
		{
			return $this->_getLayermenuConfig();
		}

		return '';
	}
	
	/**
	 * 
	 * @param arr $container
	 * @param arr $cms_mod
	 * @param arr $content
	 * @param arr $backendcfg [int idcatside, str view]
	 */
	public function getContainerContent($container, $cms_mod, $content, $backendcfg = array())
	{
		$output = '';
		
		$cms_side_edit_var = '';
		$sf_modinfostring_start = '$sf_modinfo = sf_api("LIB", "Modinfo");';
		
		if (count($backendcfg) > 0)
		{
			if ($backendcfg['is_backend'])
			{
				// darf Modul bearbeitet werden? Seitencontent darf bearbeitet werden
				if ($container[$cms_mod['container']['id']]['2'] == '0' && $backendcfg['idcatside'] > 0 && $backendcfg['view'] == 'edit')
				{
					$cms_side_edit_var = '$this->cfg->setVal(\'env\', \'current_container_editable\', TRUE); ';
					$sf_modinfostring_start .= '$sf_modinfo->setVal("is_editiable", TRUE);';
					
				}
				else 
				{
					$cms_side_edit_var = '$this->cfg->setVal(\'env\', \'current_container_editable\', FALSE); ';
					$sf_modinfostring_start .= '$sf_modinfo->setVal("is_editiable", FALSE);';
				}
			}
		}
		
		
		// darf Modul gesehen werden?
		if ($container[$cms_mod['container']['id']]['1'] == '0') 
		{
			// Container konfigurieren
			$code = $container[$cms_mod['container']['id']]['4'];
			$config = preg_split('/&/', $container[$cms_mod['container']['id']]['0']);
			foreach ($config as $key1 => $value1) 
			{
				$tmp2 = explode('=', $value1);
				if ($tmp2['1'] != '') 
				{
					// $mod_value Array schreiben
					$cms_mod['value'][$tmp2['0']] = cms_stripslashes(urldecode($tmp2['1']));
		
					// MOD_VALUE[x] ersetzen
					$code = str_replace('MOD_VALUE['.$tmp2['0'].']', str_replace("\'","'", urldecode($tmp2['1'])), $code);
				}
				unset($tmp2);
			}
		
			// nicht benutzte Variablen strippen
			$code = preg_replace('/MOD_VALUE\[\d*\]/', '', $code);
			$code = $this->mapCMSPHPCACHEToPhp($code);
		
			if (stristr ($code, '<cms:mod constant="tagmode" />')) 
			{
				$code = str_replace('<cms:mod constant="tagmode" />', '', $code);
				$code = cms_stripslashes($code);
			} 
			
		
			// Das Modul existiert noch nicht in der Datenbank
			if (!is_array($content[$cms_mod['container']['id']])) 
			{
				$content[$cms_mod['container']['id']]['1'] = 'neu';
			}
		
			// Alle MOD_TAGS[] im Container ersetzen
			$used_type = extract_cms_tags($code);
			
			// alle Module in einem Container generieren
			if(is_array($content[$cms_mod['container']['id']]))
			{
				$output = '';
				foreach ($content[$cms_mod['container']['id']] as $key3 => $value3)
				{
					$sf_modinfostring = $sf_modinfostring_start;

					// letztes Modul in diesem Container?
					$pre_container_code = '';
					if (! $content[$cms_mod['container']['id']][$key3+1]) 
					{
						$cms_mod['modul']['lastentry'] = 'true';
						$pre_container_code = '<CMSPHP> $cms_mod[\'modul\'][\'lastentry\']=\'true\'; </CMSPHP>';
						$sf_modinfostring .= '$sf_modinfo->setVal("is_last_entry", TRUE);';
					} 
					else 
					{
						unset($cms_mod['modul']['lastentry']);
						$pre_container_code = '<CMSPHP> unset($cms_mod[\'modul\'][\'lastentry\']); </CMSPHP>';
						$sf_modinfostring .= '$sf_modinfo->setVal("is_last_entry", FALSE);';
					}

					$sf_modinfostring .= '$sf_modinfo->setVal("id_container", '.$cms_mod['container']['id'].');';
					$sf_modinfostring .= '$sf_modinfo->setVal("entry_nr", '.$key3.');';
					
					// erstes Modul generieren?
					if ($key3 == '1') 
					{
						$container_code = $code;
						$sf_modinfostring .= '$sf_modinfo->setVal("is_first_entry", TRUE);';

						if (is_array($used_type)) 
						{
							// CMS-TAG in Funktionsaufruf umwandeln
							foreach ($used_type as $value4) 
							{
								// CMS-TAG Konfiguration auslesen
								$cms_type_config = '\'';
								foreach ($value4 as $key5=>$value5) 
								{
									if ($key5 != 'type' && $key5 != 'id' && $key5 != 'full_tag') 
									{
										$cms_type_config .= '\''.$key5.'\'=>\''.str_replace('\"','"', cms_addslashes($value5)).'\',';
									}
								}
		
								// letztes Komma entfernen
								$cms_type_config = substr ($cms_type_config,  1, -1);
								if (!$value4['id'])
								{
									$value4['id'] = '0';
								}
								if ($value4['addslashes'] == 'true') 
								{
									$container_code = str_replace($value4['full_tag'], 'type_output_'.strtolower($value4['type']).'('.$cms_mod['container']['id'].', '. $key3.', '.$value4['id'].', array('.$cms_type_config.'))', $container_code);
								}
								else 
								{
									$container_code = str_replace($value4['full_tag'], '<?PHP echo type_output_'.strtolower($value4['type']).'('.$cms_mod['container']['id'].', '. $key3.', '.$value4['id'].', array('.$cms_type_config.')); ?>', $container_code);
									
								}
							}
						}
		
						$cms_mod['modul']['id'] = $key3;
						$cms_mod['key'] = 'mod'.$cms_mod['container']['id'].'_'.$key3.'_';

						$sf_modinfostring .= '$sf_modinfo->setVal("mod_values", $cms_mod["value"]);';
						$sf_modinfostring .= '$sf_modinfo->setVal("container_id", $cms_mod["container"]["id"]);';
						$sf_modinfostring .= '$sf_modinfo->setVal("container_title", $cms_mod["container"]["title"]);';
						$sf_modinfostring .= '$sf_modinfo->setVal("container_tag", $cms_mod["container"]["full_tag"]);';
						$sf_modinfostring .= '$sf_modinfo->setVal("mod_key", $cms_mod["key"]);';

						$container_code_cachephp = '<?PHP '.$cms_side_edit_var.' $cms_mod = '.var_export($cms_mod, TRUE).'; '. $sf_modinfostring .' ?>'.$pre_container_code.$container_code;
						$output = '<CMSPHP> '.$cms_side_edit_var.' $cms_mod = '.var_export($cms_mod, TRUE).';  $cms_mod[\'modul\'][\'id\']=\''.$key3.'\';$cms_mod[\'key\'] = \'mod\'.$cms_mod[\'container\'][\'id\'].\'_'.$key3.'_\';  '. $sf_modinfostring .' </CMSPHP>'.$container_code_cachephp;

					} 
					// alle weiteren Module dranh�ngen
					else 
					{
						$container_code = $code;
						$sf_modinfostring .= '$sf_modinfo->setVal("is_first_entry", FALSE);';
						if (is_array($used_type)) 
						{
							// CMS-TAG in Funktionsaufruf umwandeln
							foreach ($used_type as $value4) 
							{
								/// CMS-TAG Konfiguration auslesen
								$cms_type_config = '\'';
								foreach ($value4 as $key5=>$value5) 
								{
									if ($key5 != 'type' && $key5 != 'id' && $key5 != 'full_tag') 
									{
										$cms_type_config .= '\''.$key5.'\'=>\''.str_replace('\"','"', cms_addslashes($value5)).'\',';
									}
								}
		
								// letztes Komma entfernen
								$cms_type_config = substr ($cms_type_config,  1, -1);
								if (!$value4['id'])
								{
									$value4['id'] = '0';
								}
								
								if ($value4['addslashes'] == 'true') 
								{
									$container_code = str_replace($value4['full_tag'], 'type_output_'.strtolower($value4['type']).'('.$cms_mod['container']['id'].', '. $key3.', '.$value4['id'].', array('.$cms_type_config.'))', $container_code);
								}
								else 
								{
									$container_code = str_replace($value4['full_tag'], '<?PHP echo type_output_'.strtolower($value4['type']).'('.$cms_mod['container']['id'].', '. $key3.', '.$value4['id'].', array('.$cms_type_config.')); ?>', $container_code);
								}
									
							}
						}

						$sf_modinfostring .= '$sf_modinfo->setVal("mod_values", $cms_mod["value"]);';
						$sf_modinfostring .= '$sf_modinfo->setVal("container_id", $cms_mod["container"]["id"]);';
						$sf_modinfostring .= '$sf_modinfo->setVal("container_title", $cms_mod["container"]["title"]);';
						$sf_modinfostring .= '$sf_modinfo->setVal("container_tag", $cms_mod["container"]["full_tag"]);';
						$sf_modinfostring .= '$sf_modinfo->setVal("mod_key", $cms_mod["key"]);';
						
						$container_code_cachephp = '<?PHP '.$cms_side_edit_var.' $cms_mod[\'modul\'][\'id\']=\''.$key3.'\'; '. $sf_modinfostring .'   ?>'.$pre_container_code.$container_code;
						$output .= '<CMSPHP> '.$cms_side_edit_var.' $cms_mod[\'modul\'][\'id\']=\''.$key3.'\';$cms_mod[\'key\'] = \'mod\'.$cms_mod[\'container\'][\'id\'].\'_'.$key3.'_\'; '. $sf_modinfostring .'  </CMSPHP>'.$container_code_cachephp;
					}
				}
			}
		
			$output = '<!--START '.$cms_mod['container']['id'].'//--><CMSPHP> '.$cms_side_edit_var.' $cms_mod[\'container\'][\'id\']=\''.$cms_mod['container']['id'].'\'; </CMSPHP>'.$output.'<!--END '.$cms_mod['container']['id'].'//-->';
			
	
		}
		
		return $output;
	}
	
	public function getMappedCmsFileUrlsToNamedUrls($code)
	{
		$matches = array();
		$all_searches = array();
		$all_replaces = array();
		preg_match_all("!cms://(idfile|idfilethumb)=(\d+)(\?([^'\"><\s]+))?!", $code, $matches);

		if (count($matches) > 0)
		{
			//sort uris
			$uris = $matches['0'];
			$cmp = create_function('$a,$b','if (strlen($a) == strlen($b)) { return 0; } return strlen($a) > strlen($b) ? -1 : 1;');
			uasort($uris, $cmp);
			$uris = array_unique($uris);


			foreach ($uris AS $k=>$search)
			{
				//idfile or idfilethumb
				$type = $matches['1'][$k];
				$id = $matches['2'][$k];
				$parms = $matches['4'][$k];
				$local_sep = '&amp;';
				$local_parmstr = '';
				$local_idthumb = 0;
				$replace = '';

				$parmarr = array();
				if ($parms != '')
				{
					$parms = str_replace('&amp;', '&', $parms);
					$parmpairs = explode('&', $parms);
					foreach ($parmpairs AS $pv)
					{
						$key = $val = '';
						list($key, $val) = explode('=', $pv, 2);
						$parmarr[$key] = $val;
					}

					// get idthumb
					if (array_key_exists('thumb', $parmarr))
					{
						$local_idthumb = (int) $parmarr['thumb'];
						unset($parmarr['thumb']);
					}
				}

				$file = sf_api('MODEL', 'FileSqlItem');
				$file->setIdclient($this->cfg->env('idclient'));
				$file->setIdlang($this->cfg->env('idlang'));

				if($file->loadById($id) == TRUE)
				{
					if ($type == 'idfilethumb')
					{
						$thumbdata = $file->getThumbnails();
						if (array_key_exists($local_idthumb, $thumbdata))
						{
							$replace = $thumbdata[$local_idthumb]['html_path'];
						}
					}
					else
					{
						$replace = $file->getHtmlPath();
					}

					array_push($all_searches, $search);
					array_push($all_replaces, $replace);
				}

			}

			$code = str_replace($all_searches, $all_replaces, $code);
		}
		return $code;
	}

    public function getMappedCmsLinks($code, $options = array())
    {
		if ($code == '')
		{
			return $code;
		}

		$def_options = array (
			'force_http_path' => FALSE,
			'force_entitydecode' => FALSE,
			'force_langprefix' => FALSE
		);

		$options = array_merge($def_options, $options);

		$all_searches = array();
		$all_replaces = array();
        $matches = array();
		$sess = $this->cfg->sess();
		$is_rewrite2 = $this->cfg->env('is_frontend_rewrite2');
		$use_idlang_in_rewrite2 = ($this->cfg->client('url_langid_in_defaultlang') == '1');
		
        preg_match_all("!cms://(idcat|idcatside)=(\d+)(\?([^'\"><\s]+))?!", $code, $matches);

		if (count($matches) > 0)
		{
			//init common vars
			$is_backend = $this->cfg->env('is_backend');
			$view = $is_backend ? $this->cfg->env('view') : FALSE;
			$is_rewrite1 = $this->cfg->env('is_frontend_rewrite1');
			$current_idlang = $this->cfg->env('idlang');
			$idstartlang = $this->cfg->env('idstartlang');
			$is_https_enabled = $this->cfg->client('https') == '1' ? TRUE:FALSE;
			$contentfile = $this->cfg->client('contentfile');
			$path_http = $this->cfg->env('path_frontend_http');
			$sep = '&amp;';
			$pageinfos = $catinfos = $path_https = FALSE;
			if ($is_https_enabled)
			{
				$pageinfos = sf_api('PAGE', 'Pageinfos');
				$catinfos = sf_api('PAGE', 'Catinfos');
				$path_https = str_replace('http://', 'https://',$path_http);
			}

			//sort uris
			$uris = $matches['0'];
			$cmp = create_function('$a,$b','if (strlen($a) == strlen($b)) { return 0; } return strlen($a) > strlen($b) ? -1 : 1;');
			uasort($uris, $cmp);
			$uris = array_unique($uris);

			foreach ($uris AS $k=>$search)
			{
				//idcatside or idcat
				$type = $matches['1'][$k];
				$parms = $matches['4'][$k];
				$local_sep = $sep;
				$local_parmstr = '';
				$local_lang = FALSE;
				$local_langstr = '';
				$local_langprefix = FALSE;
				$local_view = '';
				$replace = '';

				$parmarr = array();
				if ($parms != '')
				{
					$parms = str_replace('&amp;', '&', $parms);
					$parmpairs = explode('&', $parms);
					foreach ($parmpairs AS $pv)
					{
						$key = $val = '';
						list($key, $val) = explode('=', $pv, 2);
						$parmarr[$key] = $val;
					}

					// use '&' or '&amp;' as separator
					if (array_key_exists('sf_entitydecode', $parmarr) || $options['force_entitydecode'])
					{
						if ($parmarr['sf_entitydecode'] == 'true' || $options['force_entitydecode'] === TRUE)
						{
							$local_sep = '&';
							unset($parmarr['sf_entitydecode']);
						}
					}
					//use lang id
					if (array_key_exists('lang', $parmarr))
					{
						$local_lang = (int) $parmarr['lang'];
						unset($parmarr['lang']);
					}

					//handle view parm
					if (array_key_exists('view', $parmarr))
					{
						if ($is_backend)
						{
							$local_view = $parmarr['view'];
						}
						unset($parmarr['view']);
					}

					//handle mod_rewrite2 langprefix
					if (array_key_exists('sf_langprefix', $parmarr) || $options['force_langprefix'])
					{
						if ($parmarr['sf_langprefix'] == 'true')
						{
							$local_langprefix = TRUE;
						}
						unset($parmarr['sf_langprefix']);
					}

					if (count($parmarr) > 0)
					{
						$parmarr2 = array();
						foreach ($parmarr AS $pk => $pv)
						{
							array_push($parmarr2, $pk.'='.$pv);
						}
						$local_parmstr = implode($local_sep, $parmarr2);
					}
				}
				if ($is_backend)
				{
					if ($idstartlang != $current_idlang && $local_lang < 1)
					{
						$local_lang = $current_idlang;
					}

					if ($local_lang > 0)
					{
						$local_langstr = 'lang='.$local_lang.$local_sep;
					}

					if ($local_view != '')
					{
						$local_view = 'view='.$local_view;
					}
					else
					{
						$local_view = 'view='.$view;
					}

					if ($local_parmstr != '')
					{
						$local_parmstr = $local_sep.$local_parmstr;
					}

					$replace = $sess->url( $contentfile .'?'.$local_langstr.$type.'='.$matches['2'][$k].$local_sep.$local_view.$local_parmstr);
				}
				else if ($is_rewrite1)
				{
					if ($local_lang > 0)
					{
						if ($local_lang != $current_idlang)
						{
							$local_langstr = '-'.$local_lang;
						}
					}

					if ($local_parmstr != '')
					{
						$local_parmstr = '?'.$local_parmstr;
					}

					$pre = ($type == 'idcatside') ? 'page' : 'cat';
					$replace = $sess->url( $pre . $matches['2'][$k] . $local_langstr . '.html'.$local_parmstr);
				}
				else if ($is_rewrite2)
				{

					if ($local_parmstr != '')
					{
						$local_parmstr = '?'.$local_parmstr;
					}
					$local_langid = ($local_lang > 0) ? $local_lang : $current_idlang;
					
					if ($use_idlang_in_rewrite2 || $idstartlang != $local_langid)
					{
						$local_langprefix = TRUE;
					}

					if ($type == 'idcatside')
					{
						$replace = rewriteGetPageUrl($matches['2'][$k], $local_langid, $local_langprefix).$local_parmstr;
					}
					else
					{
						$replace = rewriteGetCatUrl($matches['2'][$k], $local_langid, $local_langprefix).$local_parmstr;
					}
				}
				//frontend with standard *.php URLs
				else
				{
					if ($local_lang > 0)
					{
						if ($local_lang != $current_idlang)
						{
							$local_langstr = 'lang='.$local_lang.$local_sep;
						}
					}

					if ($local_parmstr != '')
					{
						$local_parmstr = $local_sep.$local_parmstr;
					}
					
					$replace = $sess->url( $contentfile .'?'.$local_langstr.$type.'='.$matches['2'][$k].$local_parmstr);
				}

				//https mappings
				if ($is_https_enabled)
				{
					$this_page_hase_https_enabled = ($type == 'idcatside') ? ($pageinfos->getIsHttps($matches['2'][$k]) == '1') : ($catinfos->getStartpageIsHttps($matches['2'][$k]));
					if (! $this->cfg->env('is_https')  && $this_page_hase_https_enabled)
					{
						$replace = $path_https . $replace;
					}
					else if ($this->cfg->env('is_https') && ! $this_page_hase_https_enabled)
					{
						$replace = $path_http . $replace;
					}
				}
				//Force http path
		
				if ($options['force_http_path'])
				{
					if (substr($replace, 0, 4) != 'http')
					{
						$replace = $path_http . $replace;
					}
				}

				array_push($all_searches, $search);
				array_push($all_replaces, $replace);
			}

			$code = str_replace($all_searches, $all_replaces, $code);

			//if mod rewrite 2: handle local anchor links
			if ($is_rewrite2)
			{
				$code = preg_replace("!(<a[\s]+[^>]*?href[\s]?=[\s\"\']+)#(.*?)([\"\'])!i", '\\1'.str_replace('&', '&amp;', $_SERVER['REQUEST_URI']).'#\\2\\3', $code);
			}
		}

        return $code;
    }
	
	protected function _getLayermenuConfig() 
	{
			global $cms_db, $db, $sess, $cfg_cms, $cfg_client;
            global $cms_lang, $con_side, $con_tree;

			$cfg = sf_api('LIB', 'Config');
			$view = $cfg->env('view');
			$idcatside = $cfg->env('idcatside');
			$idcat = $cfg->env('idcat');
			$lang = $cfg->env('idlang');

			$perm = $cfg->perm();

            include_once($cfg_cms['path_base'].$cfg_cms['path_backend_rel'].'inc/class.popupmenubuilder_js.php');
            $p_menu = new popupmenubuilder_js();


            // Bearbeitungsrecht auf dieser Seite?
            if ($perm->have_perm(19, 'side', $idcatside, $con_side[$idcatside]['idcat']) || $perm->have_perm(2, 'cat' , $con_side[$idcatside]['idcat']))
            {
                $p_menu->set_image('cms/img/but_editside.gif', 16, 16);


                // Seite
                if (($idcatside && $perm->have_perm(18, 'side', $idcatside, $idcat)) || ($idcatside && $perm->have_perm(19, 'side', $idcatside, $idcat)) || ($perm->have_perm('10', 'cat', $idcat) || $perm->have_perm('11', 'cat', $idcat)) || ($idcatside && $perm->have_perm('12', 'cat', $idcat)))
                {
                    $p_menu->add_title($cms_lang['type_edit_side']);
                }

                // Seite konfigurieren
                if ($idcatside && $perm->have_perm(20, 'side', $idcatside, $idcat))
                {

                    $entry = $cms_lang['side_config'];
                    $link = $sess->url($cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'main.php?area=con_configside&view='.$view.'&idcatside='.$idcatside.'&idside='.$con_side[$idcatside]['idside'].'&idcat='.$con_side[$idcatside]['idcat'].'&idtplconf='.$con_side[$idcatside]['idtplconf']);
                    $target = '_self';
                    $mouseover_text = $cms_lang['side_config'];
                    $optional_js = '';
                    $p_menu->add_entry($entry, $link, $target, $mouseover_text, $optional_js);
                }

                // Seite anlegen
                if ($perm->have_perm('18', 'cat', $idcat) )
                {
                    $entry = $cms_lang['side_new'];
                    $link = $sess->url($cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'main.php?area=con_configside&view='.$view.'&idcat='.$idcat.'&idtplconf=0');
                    $target = '_self';
                    $mouseover_text = $cms_lang['side_new'];
                    $optional_js = '';
                    $p_menu->add_entry($entry, $link, $target, $mouseover_text, $optional_js);
                }

                // Seite löschen
                if ($idcatside && $perm->have_perm(21, 'side', $idcatside, $idcat))
                {
                    $entry = $cms_lang['side_delete'];
                    $link = $sess->url($cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'main.php?area=con&view='.$view.'&action=side_delete&idcat='.$idcat.'&idside='.$con_side[$idcatside]['idside']);
                    $target = '_self';
                    $mouseover_text = $cms_lang['side_delete'];
                    $optional_js = 'return delete_confirm();';
                    $p_menu->add_entry($entry, $link, $target, $mouseover_text, $optional_js);
                }

                // Ordner
                if ($perm->have_perm('2', 'cat', $idcat) || ($perm->have_perm('3', 'cat', $idcat) || $perm->have_perm('4', 'cat', $idcat)) || (!$idcatside && $perm->have_perm('5', 'cat', $idcat)))
                {
                    $p_menu->add_title($cms_lang['type_edit_folder']);
                }

                // Ordner konfigurieren
                if ($perm->have_perm('2', 'cat', $idcat))
                {
                    $entry = $cms_lang['side_config'];
                    $link = $sess->url($cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'main.php?area=con_configcat&view='.$view.'&idcat='.$idcat.'&idcatside='.$idcatside.'&idcatlang='.$con_tree[$idcat]['idcatlang'].'&idtplconf='.$con_tree[$idcat]['idtplconf']);
                    $target = '_self';
                    $mouseover_text = $cms_lang['side_config'];
                    $optional_js = '';
                    $p_menu->add_entry($entry, $link, $target, $mouseover_text, $optional_js);
                }

                // Ordner anlegen
                if ($perm->have_perm('3', 'cat', $idcat) || $perm->have_perm('4', 'cat', $idcat))
                {
                    $entry = $cms_lang['side_new'];
                    $link = $sess->url($cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'main.php?area=con_configcat&view='.$view.'&idcatside='.$idcatside.'&parent='.$idcat.'&idtplconf=0');
                    $target = '_self';
                    $mouseover_text = $cms_lang['side_new'];
                    $optional_js = '';
                    $p_menu->add_entry($entry, $link, $target, $mouseover_text, $optional_js);
                }

                // Ordner löschen
                if (!$idcatside && $perm->have_perm('5', 'cat', $idcat))
                {
                    $entry = $cms_lang['side_delete'];
                    $link = $sess->url($cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'main.php?area=con&view='.$view.'&action=cat_delete&idcat='.$idcat);
                    $target = '_self';
                    $mouseover_text = $cms_lang['side_delete'];
                    $optional_js = 'return delete_confirm();';
                    $p_menu->add_entry($entry, $link, $target, $mouseover_text, $optional_js);
                }

                // Modus
                $p_menu->add_title($cms_lang['side_mode']);


                // Seitenübersicht
                if ($perm->have_perm('1', 'area_con'))
                {
                    $entry = $cms_lang['side_overview'];
                    $link = $sess->url($cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'main.php?area=con');
                    $target = '_top';
                    $mouseover_text = $cms_lang['side_overview'];
                    $optional_js = '';
                    $p_menu->add_entry($entry, $link, $target, $mouseover_text, $optional_js);
                }

                // Editor / Vorschau
                if ($view == 'preview')
                {
                    $entry = $cms_lang['side_edit'];
                    $link = $sess->url($cfg_client['path_http_edit'] . $cfg_client['path_rel'].$cfg_client['contentfile'].'?lang='.$lang.'&idcat='.$idcat.'&idcatside='.$idcatside.'&view=edit');
                    $target = '_self';
                    $mouseover_text = $cms_lang['side_edit'];
                    $optional_js = '';
                    $p_menu->add_entry($entry, $link, $target, $mouseover_text, $optional_js);
                }
                else
                {
                    $entry = $cms_lang['side_preview'];
                    $link = $sess->url($cfg_client['path_http_edit'] . $cfg_client['path_rel'].$cfg_client['contentfile'].'?lang='.$lang.'&idcat='.$idcat.'&idcatside='.$idcatside.'&view=preview');
                    $target = '_self';
                    $mouseover_text = $cms_lang['side_preview'];
                    $optional_js = '';
                    $p_menu->add_entry($entry, $link, $target, $mouseover_text, $optional_js);
                }
            }
            return $p_menu->get_menu_and_flush();
	}
	
	public function getBackendEditForm($cms_path, $lang_charset, $cfg_cms, $idcatside, $lang, $sess, $cfg_client, $con_tree, $con_side, $cms_lang, $idside)
	{
        //echo "$cms_path, $lang_charset, $cfg_cms, $idcatside, $lang, $sess, $cfg_client, $con_tree, $con_side, $cms_lang, $idside";exit;

		$cfg = sf_api('LIB', 'Config');
		$this->cfg->setVal('env', 'current_container_editable', TRUE);

		$enable_code_editor = (bool) $this->cfg->cms('enable_code_editor');

        // Formularelemente includieren
		include_once($cms_path.'inc/fnc.type_forms.php');
		$code .= '<head>'."\n";
		$code .= '<title>Sefrengo | Edit-Mode</title>'."\n";
		$code .= '<meta http-equiv="content-type" content="text/html; charset='.$lang_charset.'" />'."\n";
		$code .= '<link rel="stylesheet" type="text/css" href="'.$cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/css/sefrengo-theme/jquery-ui.custom.css" />'."\n";
		$code .= '<link rel="stylesheet" type="text/css" href="'.$cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/css/styles.css" />'."\n";
		if ($enable_code_editor)
		{
			$code .= '<link rel="stylesheet" type="text/css" href="'.$cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/js/editor/codemirror/lib/codemirror.css" />'."\n";
			$code .= '<link rel="stylesheet" type="text/css" href="'.$cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/js/editor/codemirror/theme/default.css" />'."\n";
			$code .= '<link rel="stylesheet" type="text/css" href="'.$cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/js/editor/codemirror/sefrengo/sf_codemirror.css" />'."\n";
		}
		$code .= '<script type="text/javascript" src="'.$cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/js/init.sefrengo.js"></script>'."\n";
		$code .= '<script type="text/javascript" src="'.$cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/js/lib/jquery.min.js"></script>'."\n";
		$code .= '<script type="text/javascript" src="'.$cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/js/lib/jquery-ui.custom.min.js"></script>'."\n";
		$code .= "<script type=\"text/javascript\">/* <![CDATA[ */
					var \$jqsf = jQuery;
					//var \$jqsf = $.noConflict(true); // currently the jQuery plugins won\'t work in this mode 
					
					SF.Config.debug = false;
					SF.Config.backend_dir = '".$cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel']."'; // e.g. /backend/
					SF.Config.js_dir = SF.Config.backend_dir + 'tpl/".$cfg_cms['skin']."/js/';
					SF.Config.css_dir = SF.Config.backend_dir + 'tpl/".$cfg_cms['skin']."/css/';
					SF.Config.img_dir = SF.Config.backend_dir + 'tpl/".$cfg_cms['skin']."/img/';
				/* ]]> */</script>";
		if ($enable_code_editor)
		{
			$code .= '<script type="text/javascript" src="'.$cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/js/jquery.frontend.js"></script>'."\n";
			$code .= '<script type="text/javascript" src="'.$cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/js/editor/codemirror/lib/codemirror-compressed.js"></script>'."\n";
			$code .= '<script type="text/javascript" src="'.$cfg_cms['path_base_http'] . $cfg_cms['path_backend_rel'].'tpl/'.$cfg_cms['skin'].'/js/editor/codemirror/sefrengo/jquery.sf_codemirror.js"></script>'."\n";
		}
		//disable selector content sync
		$copycontent_disabled = true;
		$code .= '<script type="text/javascript">
			try {	
				window.parent.con_nav.sf_setCurrentIdcatside('.$idcatside.', '.$copycontent_disabled.', '.$lang.')
			} catch (e) {
	
			}
			</script>';
		$code .= '</head>'."\n";
		$code .= '<body id="con-edit2">'."\n";
		$code .= '<!-- inc.con_edit.php -->'."\n";
		$code .= '<div id="main">'."\n";
		$code .= "    <form name=\"editcontent\" method=\"post\" action=\"".$sess->url($cfg_client['contentfile'])."\">\n";
		$code .= "    <input type=\"hidden\" name=\"view\" value=\"edit\" />\n";
		$code .= "    <input type=\"hidden\" name=\"lang\" value=\"$lang\" />\n";
		$code .= "    <input type=\"hidden\" name=\"action\" value=\"save\" />\n";
		$code .= "    <input type=\"hidden\" name=\"entry\" value=\"".$_REQUEST['entry']."\" />\n";
		$code .= "    <input type=\"hidden\" name=\"idcatside\" value=\"$idcatside\" />\n";
		$code .= "    <input type=\"hidden\" name=\"content\" value=\"".$_REQUEST['content']."\" />\n";
		$code .= "    <table class=\"config\" cellspacing=\"1\">\n";
	
	    $con_type['1'] =array('type'=>'text', 'descr'=> $cms_lang['type_text'], 'input'=>'type_form_text');
	    $con_type['2'] =array('type'=>'wysiwyg', 'descr'=>$cms_lang['type_wysiwyg'], 'input'=>'type_form_wysiwyg');
	    $con_type['3'] =array('type'=>'textarea', 'descr'=>$cms_lang['type_textarea'], 'input'=>'type_form_textarea');
	    $con_type['4'] =array('type'=>'image', 'descr'=>$cms_lang['type_image'], 'input'=>'type_form_img');
	    $con_type['5'] =array('type'=>'imgdescr', 'descr'=>$cms_lang['type_image_desc'], 'input'=>'type_form_imgdescr');
	    $con_type['6'] =array('type'=>'link', 'descr'=>$cms_lang['type_link'], 'input'=>'type_form_link');
	    $con_type['7'] =array('type'=>'linkdescr', 'descr'=>$cms_lang['type_link_name'], 'input'=>'type_form_linkdescr');
	    $con_type['8'] =array('type'=>'linktarget', 'descr'=>$cms_lang['type_link_target'] , 'input'=>'type_form_linktarget');
	    $con_type['9'] =array('type'=>'sourcecode', 'descr'=>$cms_lang['type_sourcecode'], 'input'=>'type_form_sourcecode');
	    $con_type['10']=array('type'=>'file', 'descr'=>$cms_lang['type_file'], 'input'=>'type_form_file');
	    $con_type['11']=array('type'=>'filedescr', 'descr'=>$cms_lang['type_file_desc'], 'input'=>'type_form_filedescr');
	    $con_type['12']=array('type'=>'filetarget', 'descr'=>$cms_lang['type_file_target'], 'input'=>'type_form_filetarget');
	    $con_type['13']=array('type'=>'wysiwyg2', 'descr'=>$cms_lang['type_wysiwyg'], 'input'=>'type_form_wysiwyg2');
	    $con_type['14']=array('type'=>'select', 'descr'=>$cms_lang['type_select'], 'input'=>'type_form_select');
	    $con_type['15']=array('type'=>'hidden', 'descr'=>$cms_lang['type_hidden'], 'input'=>'type_form_hidden');
	    $con_type['16']=array('type'=>'checkbox', 'descr'=>$cms_lang['type_checkbox'], 'input'=>'type_form_checkbox');
	    $con_type['17']=array('type'=>'radio', 'descr'=>$cms_lang['type_radio'], 'input'=>'type_form_radio');
	    $con_type['18']=array('type'=>'date', 'descr'=>$cms_lang['type_date'], 'input'=>'type_form_date');
	    $con_type['20']=array('type'=>'checkboxsave' ,'descr'=>$cms_lang['type_checkbox'], 'input'=>'type_form_checkboxsave');
	
		// Content-Array erstellen
		$sql = "SELECT
					A.idcontent, container, number, idtype, typenumber, value
				FROM
					{$this->dbnames[content]} A
					LEFT JOIN {$this->dbnames[side_lang]} B USING(idsidelang)
				WHERE
					B.idside='$idside'
					AND B.idlang='$lang'";
		$this->db->query($sql);
		while ($this->db->next_record())
		{
		 	$content_array[$this->db->f('container')][$this->db->f('number')][$this->db->f('idtype')][$this->db->f('typenumber')] = array($this->db->f('idcontent'), htmlentities($this->db->f('value'), ENT_COMPAT, 'UTF-8'));
		}
	
		// Module finden
		$idtplconf = $con_side[$idcatside]['idtplconf'];
		if ($con_side[$idcatside]['idtplconf'] == '0')
		{
			$idtplconf = $con_tree[$con_side[$idcatside]['idcat']]['idtplconf'];
		}
	
		$modlist = browse_template_for_module('0', $idtplconf);
	
		// Containernamen suchen
		$sql = "SELECT idlay FROM {$this->dbnames[tpl_conf]} A LEFT JOIN {$this->dbnames[tpl]} B USING(idtpl) WHERE A.idtplconf='$idtplconf'";
		$this->db->query($sql);
		$this->db->next_record();
		$idlay = $this->db->f('idlay');
		$list = browse_layout_for_containers($idlay);
	
		// Bearbeitungsarray erstellen
		$content = $_REQUEST['content']; //TODO
		$con_content = explode (';', $content);
		unset($content);
	
		// Einzelne Container auflisten
		foreach ($con_content as $value)
		{
	
			// Konfiguration einlesen
			$con_config = explode ('.', $value);
			$con_container = $con_config['0'];
			$con_contnbr = explode (',', $con_config[1]);
			$con_content_type = explode (',', $con_config[2]);
	
			// Konfigurationsparameter mod_values extahieren und aufbereiten
			$sql = "SELECT
						container_conf.config
					FROM
						".$this->dbnames['container_conf']." container_conf
						LEFT JOIN ".$this->dbnames['tpl_conf']." tpl_conf USING(idtplconf)
						LEFT JOIN ".$this->dbnames['container']." container USING(idtpl)
					WHERE
						container_conf.idtplconf = $idtplconf
						AND container = $con_container
						AND container_conf.idcontainer = container.idcontainer";
			$this->db->query($sql);
			$this->db->next_record();
			$tpl_config_vars = $this->db->f('config');
	
			// mod_values aus Container ersetzen
			$container = $modlist[$con_container]['output'];
			$config = preg_split('/&/', $tpl_config_vars );
			foreach ($config as $key1 => $value1) {
				$tmp2 = explode('=', $value1);
				if ($tmp2['1'] != '') {
					// $mod_value Array schreiben
					$cms_mod['value'][$tmp2['0']] = cms_stripslashes(urldecode($tmp2['1']));
					// MOD_VALUE[x] ersetzen
					$container = str_replace('MOD_VALUE['.$tmp2['0'].']', str_replace("\'","'", urldecode($tmp2['1'])), $container);//'
				}
				unset($tmp2);
			}
	
			// nicht benutzte Variablen strippen
			$container = preg_replace('/MOD_VALUE\[\d*\]/', '', $container);
			if( stristr ($container, '<cms:mod constant="tagmode" />') ){
				$container = str_replace('<cms:mod constant="tagmode" />', '', $container);
				$container = cms_stripslashes($container);
			//todo: 2remove
			} elseif( stristr ($container, '<dedi:mod constant="tagmode" />') ){
				$container = str_replace('<dedi:mod constant="tagmode" />', '', $container);
				$container = cms_stripslashes($container);
			}
	
	
			// Moduloutput simulieren, zum generieren der CMS-Tag Informationen
			$sefrengotag_config = extract_cms_tags($container, 'type');
			
			
	
			// Rowspan für Containertabelle berechnen
			$rowspan = 1;
			foreach ($con_contnbr as $con_containernumber) {
				$rowspan++;
				foreach ($con_content_type as $value3) {
					$rowspan++;
					$rowspan++;
				}
			}
	
			$code .= "  <tr>\n";
	
			// Containername
			$code .= "    <td class=\"head\" width=\"110\" rowspan=\"$rowspan\"><p>";
			if (!empty($list[$con_container]['title'])) $code .= $list[$con_container]['title'];
			else $code .= "$con_container. ".$cms_lang['tpl_container'];
			$code .= "</p></td>\n";
			unset($rowspan);
			foreach ($con_contnbr as $con_containernumber) {
	
				// neues Modul erstellen?
				if ($con_containernumber == '-1') $print_containernumber = '';
				else $print_containernumber = $con_containernumber.'. ';
	
				// Modulname
				$modname = (($modlist[$con_container]['verbose']) != '' ? $modlist[$con_container]['verbose'] : $modlist[$con_container]['modname']) . ((empty($modlist[$con_container]['version'])) ? '' : ' (' . $modlist[$con_container]['version'] . ')');
				$code .= "    <td class=\"header\"><!-- $print_containernumber -->".$modname."</td>\n";
				$code .= "  </tr>\n";
				foreach ($con_content_type as $value3) {
					$value3 = explode ('-', $value3);
					$con_contype = $value3['0'];
					$con_typenumber = $value3['1'];
	
					// Name f�r Eingabefeld
					// Nicht anzeigen bei Dateilink, wenn hidetarget auf true gesetzt ist
					if ($GLOBALS['filetarget_is_hidden'] == 'true' && $con_contype == 12) {
						$code .= "    <td></td>\n";
						$code .= "  </tr>\n";
						$code .= "  <tr>\n";
	
	//				} elseif ($con_contype == 15) { 
	//				    $code .="";
	                } elseif ($con_contype == 20) { 
						$code .= "    <td height=\"0\">";
						$code .= "    </td>\n";
						$code .= "  </tr>\n";
						$code .= "  <tr>\n";
	                } else {
	                    if (in_array($con_contype,array(1,2,3,4,6,9,10,13,14,15,16,17,18))) {
						    $code .= "  <tr class=\"fomrstitle\">\n";
						} else {
						    $code .= "  <tr>\n";
						}
						$code .= "    <td>";
						if (!empty($sefrengotag_config[$con_type[$con_contype]['type']][$con_typenumber]['title'])) $code .= $sefrengotag_config[$con_type[$con_contype]['type']][$con_typenumber]['title'];
						else $code .= $con_type[$con_contype]['descr'];
						$code .= ":</td>\n";
						$code .= "  </tr>\n";
						$code .= "  <tr>\n";
					}
	
					// Name des Formularfeldes
					$formname = 'content_'.$con_container.'_'.$con_containernumber.'_'.$con_contype.'_'.$con_typenumber;
	
					// Variable f�r den Content
					$content = $content_array[$con_container][$con_containernumber][$con_contype][$con_typenumber]['1'];
					$type_config = $sefrengotag_config[$con_type[$con_contype]['type']][$con_typenumber];
	                if ($con_contype == '16') {
	                    $type_config['saved'] = $content_array[$con_container][$con_containernumber]['20'][$con_typenumber]['1'];
	                }
	                
	                //make element
					$code .= $con_type[$con_contype]['input']($formname, $content, $type_config);
					unset($content);
					unset($formname);
					$code .= "  </tr>\n";
	
				}
			}
		}
		$code .= "      <tr>\n";
		$code .= "        <td class='content7' colspan='2' style='text-align:right'>\n";
		$code .= "        <input type='submit' value='".$cms_lang['gen_save']."' class=\"sf_buttonAction\" />\n";
		$code .= "        <input type='submit' value='".$cms_lang['gen_apply']."' class=\"sf_buttonAction\" onclick=\"document.editcontent.action.value='saveedit'\" />\n";
		$code .= "        <input type='button' value='".$cms_lang['gen_cancel']."' class=\"sf_buttonActionCancel\" onclick=\"window.location='".$sess->url("".$cfg_client['contentfile']."?lang=$lang&action=abort&view=edit&idcatside=$idcatside")."'\" />\n";
			
		$code .= "      </tr>\n";
		$code .= "    </table>\n";
		$code .= "    </form>\n";
		$code .= '</div>'."\n";
		$code .= '</body>'."\n";
		$code .= '</html>'."\n";
		return $code;
	}

    public function getSessionIdsFromRequest($view)
    {
        $sid = FALSE;
        $sefrengo = FALSE;
        if ($view == 'edit' || $view == 'preview')
        {
            $sefrengo = $this->req->cookie('sefrengo') ? $this->req->cookie('sefrengo') : $this->req->req('sefrengo');
        }
        else
        {
           $sid = $this->req->cookie('sid') ? $this->req->cookie('sid') : $this->req->req('sid');
        }

        return array($sid, $sefrengo);
    }


    public function getViewModes($view, $sefrengo)
    {
        $is_frontend = TRUE;
		$is_backend = FALSE;
		$is_backend_preview = FALSE;
		$is_backend_edit = FALSE;

        if ($view && $sefrengo != '')
		{
			if ($view == 'preview')
			{
				$is_frontend = FALSE;
				$is_backend = TRUE;
				$is_backend_preview = TRUE;
			}
			else if ($view == 'edit')
			{
				$is_frontend = FALSE;
				$is_backend = TRUE;
				$is_backend_edit = TRUE;
			}
		}

        return array($is_frontend, $is_backend, $is_backend_preview, $is_backend_edit);
    }

    public function getPageIdsFromRequest($startlang)
    {
        $idcatside = $this->req->asInt('idcatside', 'r');
		$idcat = $this->req->asInt('idcat', 'r');
		$lang = $this->req->asInt('lang', 'r');
		if (! $lang)
		{
			$lang = $startlang;
		}

        return array($idcatside, $idcat, $lang);
    }

	/**
	 * @todo lookup for $mod_lang vars and kill them
	 */
    public function getFrontendLangFile($lang_dir, $backendpath)
    {
		$cms_lang = array();

		if (file_exists ($lang_dir.'lang_general.php'))
		{
			require_once($lang_dir.'lang_general.php');
		}
		else
		{
			require_once($backendpath.'lang/de/lang_general.php');
		}

		return $cms_lang;
	}

	public function tryToRemapIdcatsideOrShow404()
	{
		$error_url = $this->cfg->client('page_404');
		$error_url = str_replace(array('{%http_host}', '{%request_uri}' ), array($_SERVER['HTTP_HOST'], $_SERVER['QUERY_STRING']), $error_url);


		if ( (is_numeric($error_url) || is_int($error_url) ) && (int) $error_url != 0)
		{
			$this->cfg->setVal('env', 'send_header_404', TRUE);
			return $error_url;
		}

		$this->show404();
	}

	public function show404()
	{
		$is_frontend = $this->cfg->env('is_frontend');
		$is_backend = $this->cfg->env('is_backend');
		$is_rewrite2 = $this->cfg->env('is_frontend_rewrite2');
		$error_url = $this->cfg->client('page_404');
		$error_url = str_replace(array('{%http_host}', '{%request_uri}' ), array($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']), $error_url);
		$current_idcatside = $this->cfg->env('idcatside');

		//abort if current page ist errorpage and errorpage is desposited as idcatside
		if ($errorpage_url == $current_idcatside)
		{
			$this->cfg->setVal('env', 'send_header_404', TRUE);
			return;
		}
		//errorpage is desposited as idcatside
		else if ((is_numeric($errorpage_url) || is_int($errorpage_url)) && $errorpage_url != '0')
		{
			$errorpage_url = 'cms://idcatside='.$errorpage_url;
			$this->redirect($errorpage_url);
		}
		//try header redirect to external domain
		else if (strlen($errorpage_url) > 7)
		{
			$this->redirect($errorpage_url);
		}
		//give up - throw 404 and exit
		else
		{
			header("HTTP/1.1 404 Not Found");
            header('Status: 404 Not Found');
			page_close();
			exit;
		}

	}

	public function redirect($url, $shutdown = TRUE)
	{
		$pageinfos = sf_api('LIB', 'Pageinfos');


		//lookup for idcatside
		if ( (is_numeric($url) || is_int($url)) && $url != '0')
		{
			$url = $pageinfos->getLink($url);
		}

		//try to map cms links
		$url = $this->getMappedCmsFileUrlsToNamedUrls($url);
		$url = $this->getMappedCmsLinks($url, array('force_http_path' => TRUE));

		$url = str_replace('&amp;', '&', $url);

		header ('HTTP/1.1 302 Moved Temporarily');
		header ('Location:' . $url );

		if ($shutdown)
		{
			page_close();
			exit;
		}	
	}

	function getStartIdcatside()
	{
		$idcat = $this->cfg->env('idcat');
		$config_start_idcatside = (int) $this->cfg->client('page_start');
		$idclient = $this->cfg->env('idclient');
		$t_cat_side = $this->cfg->db('cat_side');
		$t_cat = $this->cfg->db('cat');
		$sql = '';
		$idcatside_return = FALSE;
		$db = sf_api('LIB', 'Ado');

		if ($idcat > 0)
		{
			$sql  = '	SELECT
							idcatside
						FROM
							' . $t_cat_side . '
						WHERE
							idcat = ' . $idcat .'
							AND is_start = 1
						LIMIT 0,1';
			$rs = $db->Execute($sql);

			if ($rs === FALSE || $rs->EOF )
			{
				return FALSE;
			}

			$idcatside_return = $rs->fields['idcatside'];
		}
		else if ($config_start_idcatside > 0)
		{
			$idcatside_return = $config_start_idcatside;
		}
		else
		{
			$sql  = '	SELECT
							idcatside
						FROM
							' . $t_cat_side . ' AS A
							LEFT JOIN '. $t_cat .' AS B USING(idcat)
						WHERE
							is_start = 1
							AND idclient = ' . $idclient . '
						ORDER BY
							parent, B.sortindex
						LIMIT 0,1';
			$rs = $db->Execute($sql);

			if ($rs === FALSE || $rs->EOF )
			{
				return FALSE;
			}

			$idcatside_return = $rs->fields['idcatside'];
		}


		return $idcatside_return;
	}


	public function isSessionDisabledByUseragent()
	{
		$useragents = $this->cfg->client('session_disabled_useragents');
		$ret = FALSE;

		if(strlen($useragents)>3)
		{
			$preg_spiders = preg_replace("#(\r\n)|(\r)#m", "\n", trim($useragents));
			$preg_spiders = preg_replace('#\n#m', '|', $preg_spiders);
			if(preg_match('#('.$preg_spiders.')#i', $_SERVER['HTTP_USER_AGENT']) )
			{
			  $ret = TRUE;
			}
		}

		return $ret;
	}

	public function isSessionDisabledByIp()
	{
		$ips = $this->cfg->client('session_disabled_ips');
		$ret = FALSE;

		if(strlen($ips)>5)
		{
                $preg_ips = preg_replace("#(\r\n)|(\r)#m", "\n", trim($ips));
                $preg_ips = preg_replace('#\n#m', '|', $preg_ips);
                if(preg_match('#('.$preg_ips.')#i', $_SERVER['REMOTE_ADDR']) )
				{
                  $ret = TRUE;
                }
        }

		return $ret;
	}

	public function startSession()
	{
		$is_backend = $this->cfg->env('is_backend');

		if ($is_backend)
		{
      page_open( array('sess' => 'cms_Backend_Session',
							'auth' => 'cms_Backend_Auth'));
			
		}
		else
		{
			page_open(array('sess' => 'cms_Frontend_Session',
                            'auth' => 'cms_Frontend_Auth'));
		}
	}
}
?>