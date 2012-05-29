<?php
class SF_LIB_FrontendPageContentManipulation extends SF_LIB_ApiObject
{	
	
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
	 * Local config array
	 * @var cfg_lib
	 */
	protected $cfg_lib = array(
							'idsidelang' => 0,
							'mapping_formtypenr' => array(
										'text' => 1,
										'wysiwyg' => 2,
										'textarea' => 3,
										'img' => 4,
										'imgdescr' => 5,
										'link' => 6,
										'linkdescr' => 7,
										'linktarget' => 8,
										'sourcecode' => 9,
										'file' => 10,
										'filedescr' => 11,
										'filetarget' => 12,
										'wysiwyg2' => 13,
										'select' => 14,
										'hidden' => 15,
										'checkbox' => 16,
										'radio' => 17,
										'date' => 18,
										'checkboxsave' => 20
									),
							);
	
	public function __construct() 
	{
		global $db, $cms_db;
		
		//set singelton
		$this->_API_setObjectIsSingleton(TRUE);
        
		$this->cfg = sf_api('LIB', 'Config');
		$this->lng = sf_api('LIB', 'Lang');
		$this->adb = sf_api('LIB', 'Ado');
        
        //db object
        $this->db = $db;
        
        //db names
        $this->dbnames = $cms_db;
	}
	
	public function setIdsidelang($id)
	{
		$this->cfg_lib['idsidelang'] = (int) $id;
	}
	
	// public function save($contentstr)
	public function save($idcontainer, $idmodtag, $formtypenumber, $content, $idrepeat = 1, $idsidelang = 0)
	{
		//try to map formtype if a name is given
		if (array_key_exists($formtypenumber, $this->cfg_lib['mapping_formtypenr']))
		{
			$formtypenumber =  $this->cfg_lib['mapping_formtypenr'][$formtypenumber];
		}
		
		$idcontainer = (int) $idcontainer;
		$idmodtag = (int) $idmodtag;
		$formtypenumber = (int) $formtypenumber;
		$idrepeat = (int) $idrepeat;
		$idsidelang = ((int) $idsidelang != 0) ? (int) $idsidelang: $this->cfg_lib['idsidelang'];
		
		if ($idcontainer < 1 || $idmodtag < 1 || $formtypenumber < 1 || $idrepeat < 1 || $idsidelang < 1)
		{
			return FALSE;
		}
		
		$content = $this->_prepareContentToSave($content, $formtypenumber);
       
		$this->_saveContent($idsidelang, $idcontainer, $idrepeat, $formtypenumber, $idmodtag, $content);
		$this->_deleteFrontendCache();
		
		return TRUE;
	}
	
	public function delete($idcontainer, $idrepeat = 1, $idsidelang = 0)
	{
		$idcontainer = (int) $idcontainer;
		$idrepeat = (int) $idrepeat;
		$idsidelang = ((int) $idsidelang != 0) ? (int) $idsidelang: $this->cfg_lib['idsidelang'];
		
		if ($idcontainer < 1 || $idrepeat < 1 || $idsidelang < 1)
		{
			return FALSE;
		}
		
		// delete entrys
		$sql = "DELETE FROM 
					".$this->dbnames['content']." 
				WHERE 
					idsidelang='$idsidelang' 
					AND container='$idcontainer' 
					AND number='$idrepeat'";
		$this->db->query($sql);

		// update repeatid
		$sql = "UPDATE 
					".$this->dbnames['content']." 
				SET 
					number=number-1 
				WHERE 
					idsidelang='$idsidelang' 
					AND container='$idcontainer' 
					AND number>'$idrepeat'";
		$this->db->query($sql);

		$this->_updateSideLangAndCodestatus($idsidelang);
		$this->_deleteFrontendCache();
		
		return TRUE;
	}
	
	public function moveUp($idcontainer, $idrepeat = 1, $idsidelang = 0)
	{
		$idcontainer = (int) $idcontainer;
		$idrepeat = (int) $idrepeat;
		$idsidelang = ((int) $idsidelang != 0) ? (int) $idsidelang: $this->cfg_lib['idsidelang'];
		
		if ($idcontainer < 1 || $idrepeat < 1 || $idsidelang < 1)
		{
			return FALSE;
		}
		
		$sql = "UPDATE 
					".$this->dbnames['content']." 
				SET 
					number='-1' 
				WHERE 
					idsidelang='$idsidelang' 
					AND container='$idcontainer' 
					AND number='$idrepeat'";
		$this->db->query($sql);
		
		$sql = "UPDATE 
					".$this->dbnames['content']." 
				SET 
					number=number+1 
				WHERE 
					idsidelang='$idsidelang' 
					AND container='$idcontainer' 
					AND number='".($idrepeat-1)."'";
		$this->db->query($sql);
		
		$sql = "UPDATE 
					".$this->dbnames['content']." 
				SET 
					number='".($idrepeat-1)."' 
				WHERE 
					idsidelang='$idsidelang' 
					AND container='$idcontainer' 
					AND number='-1'";
		$this->db->query($sql);
		
		$this->_updateSideLangAndCodestatus($idsidelang);
		$this->_deleteFrontendCache();
		
		return TRUE;
	}
	
	public function moveDown($idcontainer, $idrepeat = 1, $idsidelang = 0)
	{
		$idcontainer = (int) $idcontainer;
		$idrepeat = (int) $idrepeat;
		$idsidelang = ((int) $idsidelang != 0) ? (int) $idsidelang: $this->cfg_lib['idsidelang'];
		
		if ($idcontainer < 1 || $idrepeat < 1 || $idsidelang < 1)
		{
			return FALSE;
		}
		
		$sql = "UPDATE 
					".$this->dbnames['content']." 
				SET 
					number='-1' 
				WHERE 
					idsidelang='$idsidelang' 
					AND container='$idcontainer' 
					AND number='$idrepeat'";
		$this->db->query($sql);
		
		$sql = "UPDATE 
					".$this->dbnames['content']." 
				SET 
					number=number-1 
				WHERE 
					idsidelang='$idsidelang' 
					AND container='$idcontainer' 
					AND number='".($idrepeat+1)."'";
		$this->db->query($sql);
		
		$sql = "UPDATE 
					".$this->dbnames['content']." 
				SET 
					number='".($idrepeat+1)."' 
				WHERE 
					idsidelang='$idsidelang' 
					AND container='$idcontainer' 
					AND number='-1'";
		$this->db->query($sql);
		
		$this->_updateSideLangAndCodestatus($idsidelang);
		$this->_deleteFrontendCache();
		
		return TRUE;
	}

	public function addNewRepeatContainer($idcontainer, $idrepeat, $idsidelang = 0)
	{
		$idcontainer = (int) $idcontainer;
		$idrepeat = (int) $idrepeat;
		$idsidelang = ((int) $idsidelang != 0) ? (int) $idsidelang: $this->cfg_lib['idsidelang'];
		
		if ($idcontainer < 1 || $idrepeat < 1 || $idsidelang < 1)
		{
			return FALSE;
		}
		
		// Schauen, ob idrepeat schon exisiert
		$sql = "SELECT 
					* 
				FROM 
					".$this->dbnames['content']."
				WHERE 
					idsidelang='".$idsidelang."' 
					AND container='$idcontainer' 
					AND number='$idrepeat'";
		$this->db->query($sql);
	
		// Idrepeat um eins erh�hen f�r existierenden Content
		if ($this->db->affected_rows()) 
		{
			$sql = "UPDATE 
						".$this->dbnames['content']."
					SET 
						number=number+1 
					WHERE idsidelang='".$idsidelang."' 
					AND container='$idcontainer' 
					AND number>'$idrepeat'";
			$this->db->query($sql);
		}
		
		return TRUE;
		
	}
	
	public function checkAndOptimizeRepeatContainer($idcontainer, $idrepeat, $idsidelang = 0)
	{
		$idcontainer = (int) $idcontainer;
		$idrepeat = (int) $idrepeat;
		$idsidelang = ((int) $idsidelang != 0) ? (int) $idsidelang: $this->cfg_lib['idsidelang'];
		
		if ($idcontainer < 1 || $idrepeat < 1 || $idsidelang < 1)
		{
			return FALSE;
		}
		
		//remove empty containers
		$sql = "SELECT 
					* 
				FROM 
					".$this->dbnames['content']."
				WHERE 
					idsidelang='".$idsidelang."' 
					AND container='$idcontainer' 
					AND number='$idrepeat'";
		$this->db->query($sql);
		
		if (! $this->db->affected_rows()) 
		{
			$sql = "UPDATE 
						".$this->dbnames['content']."
					SET 
						number=number-1 
					WHERE 
						idsidelang='".$idsidelang."' 
						AND container='$idcontainer' 
						AND number>'$idrepeat'";
			$this->db->query($sql);
		}
		
		return TRUE;
	}
	
	
	
	protected function _prepareContentToSave($content, $type)
	{
		if (is_array($content)) 
		{
			// content type select
			if ($type == '14') 
			{
				$content = implode("\n",$content);
			} 
			// content type datetime
			else if ($type == '18') 
			{
				if (!array_key_exists(0,$content)) 
				{
					// Hour
					if (array_key_exists('h',$content)) 
					{
						$content['H'] = $content['h'];
   						if (array_key_exists('a',$content)) 
						{
 							if ($content['H'] == "12")
							{
								$content['H'] = "0"; 
							}
							if ($content['a']=="pm") 
							{
								$content['H'] = $content['H'] + 12;
							}
						} 
						else 
						{
							if (array_key_exists('A',$content)) 
							{
								if ($content['h'] == "12") 
								{
									$content['H'] = "0";
								} 
								if ($content['A']=="PM") 
								{
									$content['H'] = $content['H'] + 12;
								}
							} 
						}
					}
					
					if (!array_key_exists('H',$content)) 
					{
						$content['H'] = 12;
					}
					// Day
					if (!array_key_exists('d',$content)) 
					{
						$content['d'] = 1;
					}
					// Months
					if (!array_key_exists('m',$content)) 
					{
						$content['m'] = 1;
					}
					if (array_key_exists('M',$content)) 
					{
						$content['m'] = $content['M'];
					}
					if (array_key_exists('F',$content)) 
					{
						$content['m'] = $content['F'];
					}
					// Year
					if (!array_key_exists('y',$content)) 
					{
						$content['y'] = 2000;
					}
					if (array_key_exists('Y',$content)) 
					{
						$content['y'] = $content['Y'];
					}

					$content = mktime($content['H'],$content['i'],0,$content['m'], $content['d'], $content['y']);
				} 
				else 
				{
					$content = strtotime($content[1].' '.$content[3]);
				}
			}
		 }
		       
		return $content;
	}
	
	
	protected function _saveContent($idsidelang, $idcontainer, $idrepeat, $formtypenumber, $idmodtag, $content) 
	{
		$author = $this->cfg->auth('uid');
		$change = FALSE;
        $cms_db = $GLOBALS['cms_db'];

		
		//strip trailingslashes if they occur in internal links  
		$content = preg_replace('#cms://(idcatside|idcat)=(\d+)/#U', 'cms://\1=\2', $content);
		//make internal image pathes relative
		$in = array("!href=(\\\\)?[\"\']".$this->cfg->client('path_http').$this->cfg->client('path_rel')."([^\"\'\\\\]*)(\\\\)?[\"\']!i",
			    "!src=(\\\\)?[\"\']".$this->cfg->client('path_http').$this->cfg->client('path_rel')."([^\"\'\\\\]*)(\\\\)?[\"\']!i",
		"!href=(\\\\)?[\"\']".$this->cfg->client('path_http_edit').$this->cfg->client('path_rel')."([^\"\'\\\\]*)(\\\\)?[\"\']!i",
			    "!src=(\\\\)?[\"\']".$this->cfg->client('path_http_edit').$this->cfg->client('path_rel')."([^\"\'\\\\]*)(\\\\)?[\"\']!i",
		);
		$out = array("href=\\1\"\\2\\3\"",
		             "src=\\1\"\\2\\3\"",
		"href=\\1\"\\2\\3\"",
		             "src=\\1\"\\2\\3\"",
		);
		$content = preg_replace($in, $out, $content);
		
		set_magic_quotes_gpc($content);
	
		$sql = "SELECT 
					value 
				FROM 
					".$this->dbnames['content']." 
				WHERE 
					idsidelang='$idsidelang' 
					AND container='$idcontainer' 
					AND number='$idrepeat' 
					AND idtype='$formtypenumber' 
					AND typenumber='$idmodtag'";
		$this->db->query($sql);
	
		//Update
		if ($this->db->next_record()) 
		{
			// hat sich was ge�ndert?
			if (addslashes($this->db->f('value')) != $content) 
			{
				// wurde �berhaupt was eingegeben?
				if ($content != '') 
				{
					$sql = "UPDATE
								".$this->dbnames['content']." 
							SET 
								value='$content', 
								author='$author', 
								lastmodified='".time()."' 
							WHERE 
								idsidelang='$idsidelang' 
								AND container='$idcontainer' 
								AND number='$idrepeat' 
								AND idtype='$formtypenumber' 
								AND typenumber='$idmodtag'";
					$this->db->query($sql);
					$change = TRUE;
				}
				else 
				{
					$sql = "DELETE FROM 
								".$cms_db['content']." 
							WHERE 
								idsidelang='$idsidelang' 
								AND container='$idcontainer' 
								AND number='$idrepeat' 
								AND idtype='$formtypenumber' 
								AND typenumber='$idmodtag'";
					$this->db->query($sql);
					$change = TRUE;
				}
			}
		}
		//Insert
		else 
		{
			if ($content != '') 
			{
				$sql = "INSERT INTO
							$cms_db[content] 
							(idsidelang, container, number, idtype, typenumber, value, author, created, lastmodified) 
						VALUES
							('$idsidelang', '$idcontainer', '$idrepeat', '$formtypenumber', '$idmodtag', '$content', '$author', '".time()."', '".time()."')";
				$this->db->query($sql);
				$change = TRUE;
			}
		}
	
		if ($change) 
		{
			$this->_updateSideLangAndCodestatus($idsidelang);
		}
	}

	protected function _updateSideLangAndCodestatus($idsidelang)
	{
		// update lastmodified
		$sql = "UPDATE 
				".$this->dbnames['side_lang']." 
			SET 
				lastmodified='".time()."', 
				author='".$this->cfg->auth('uid')."' 
			WHERE 
				idsidelang='$idsidelang'";
		$this->db->query($sql);

		// look up for clonepages
		$sql = "SELECT 
					idcatside 
				FROM 
					".$this->dbnames['side_lang']." A 
					LEFT JOIN ".$this->dbnames['cat_side']." B USING(idside) 
				WHERE 
					A.idsidelang='$idsidelang'";
		$this->db->query($sql);
		while ($this->db->next_record()) 
		{
			$list[] = $this->db->f('idcatside');
		}

		// change code status
		change_code_status($list, '1', 'idcatside');
	}
	
	protected function _deleteFrontendCache()
	{
		static $run_once = FALSE;
		
		if (! $run_once)
		{
			// Delete Content Cache
			sf_factoryCallMethod('UTILS', 'DbCache', null, null, 'flushByGroup', array('frontend'));
			$run_once = TRUE;
		}
	}
	
}

?>