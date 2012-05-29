<?php

$this->includeClass('INTERFACE', 'TableCellFormatter');
$this->includeClass('VIEW', 'AbstractView');
$this->includeClass('VIEW', 'Table');

/**
 * TODO This class uses some copied functions of other classes,
 * so the task is to create a TableCellRenderer for the detailed row.
 */
class SF_VIEW_TableFm extends SF_VIEW_Table
{
	/**
	 * Item instance
	 * @var SF_MODEL_AbstractSqlItem
	 */
	protected $item = null;
	
	/**
	 * Build one table row with detailed data from $itemobject.
	 * @see API/VIEWS/SF_VIEW_Table#buildTableRowDetail($itemobject, $field, $options = array())
	 * @param SF_INTERFACE_SqlItem $itemobject Get the table data
	 * @param array $field Fiels as array. Every field represents a table cell
	 * @param array $options
	 * @return boolean Returns TRUE if row is added to table. Otherwise returns FALSE.
	 */
	public function buildTableRowDetail($itemobject, $field, $options = array())
	{
		$this->item = $itemobject; // for _formatValues()
		
		$this->tpl->setCurrentBlock('ENTRY_DETAIL');
		
		foreach ($options['detail']['fields'] AS $key => $val)
		{
			$tplvals['DETAIL_LANG_'.strtoupper($key)] = $val['text'];
			
			if($key == 'thumbnail')
			{
				$val['thumb_url'] = str_replace('{id}', $itemobject->getId(), $val['thumb_url']);
				$val['thumb_url'] = str_replace('{thumb_index}', $val['thumb_index'], $val['thumb_url']);
					
				$tplvals['DETAIL_THUMBNAIL'] = $this->_getThumbnailImageTag($itemobject, $val['thumb_index'], $val['thumb_url'], TRUE);
				
				continue;
			}
			
			$values = array();
			$fieldname = $val['fieldname'];
			if(is_array($fieldname) == TRUE)
			{
				foreach ($fieldname as $field)
				{
					$values[$field] = $itemobject->getField($field);
				}
			}
			else
			{
				$values = array($fieldname => $itemobject->getField($fieldname));
			}
			$value = $this->_formatValues($val, $values);
			
			$tplvals['DETAIL_'.strtoupper($key)] = ($value == "") ? " - " : $value;
		}
		
		$tplvals['DETAIL_COLSPAN'] = $options['detail']['colspan'];
		$tplvals['DETAIL_TR_ATTRIBUTES'] = $options['detail']['tr_attributes'];
		
		$this->tpl->setVariable($tplvals);
		$this->tpl->parseCurrentBlock();
	}

	/**
	 * If pattern exists in $config_fields, a TableCellFormater is created
	 * and the value is formated. The array $values is used as base for formatting.
	 * @param array $config_fields Extract of the $config_fields for one field
	 * @param array $values An associative array as array('fieldname' => 'value', ...)
	 * @return string Returns the ready formated values as string
	 */
	protected function _formatValues($config_fields, $values)
	{
		if( array_key_exists('format', $config_fields) == TRUE &&
			array_key_exists('pattern', $config_fields['format']) == TRUE)
		{
			$formatter = null;
			
			if(sf_api_exists('VIEW', $config_fields['format']['classname']) == TRUE)
			{
				$formatter = sf_api('VIEW', $config_fields['format']['classname']);
			}
			else if(sf_api_exists('VIEW', 'TableCellFormatter') == TRUE)
			{
				$formatter = sf_api('VIEW', 'TableCellFormatter');
			}

			if($formatter instanceof SF_INTERFACE_TableCellFormatter)
			{
				$formatter->setItem($this->item);
				$value = $formatter->format($config_fields['format']['pattern'], $values);
			}
		}
		else
		{
			$value = implode(' ', $values);
			$value = htmlentities($value, ENT_COMPAT, 'UTF-8');
		}
		
		return $value;
	}
	
	/**
	 * Generates an IMG tag for the thumbnail of the $itemobject.
	 * @param SF_INTERFACE_SqlItem $itemobject
	 * @param integer $thumb_index Index number of the thumbnail
	 * @param String $thumb_url Custom thumbnail url (e.g. preview url)
	 * @param boolean $use_fallback_icons Use icons, if no thumbnails available
	 * @return String Returns the IMG tag for the thumbnail
	 */
	protected function _getThumbnailImageTag($itemobject, $thumb_index = 0, $thumb_url = '', $use_fallback_icons = TRUE)
	{
		$imgtag = '';
		
		if($itemobject instanceof SF_MODEL_FileSqlItem)
		{
			// Get thumbnail or large filetype icon
			if($itemobject->isThumbnailGenerationPossible() == TRUE)
			{
				$thumbdata = $itemobject->getThumbnails($thumb_index);
				
				// use html path -> image may blocked by htaccess
				if($thumb_url == '')
				{
					$imgtag = '<img '.$thumbdata['img_attr'].' />';
				}
				// use custom url (e.g. preview url)
				else
				{
					$imgtag = '<img src="'.$thumb_url.'" width="'.$thumbdata['width'].'" height="'.$thumbdata['height'].'" />';
				}
			}
			else if($use_fallback_icons == TRUE)
			{
				$filetype = sf_api('MODEL', 'FiletypeSqlItem');
				$filetype->loadById($itemobject->getField('idfiletype'));
		
				$icon_url = 'file_icons/large/'.$filetype->getField('filetype').'.gif';
				unset($filetype);
				if(file_exists($this->getSkinImgPath().$icon_url))
				{
					$imgtag = '<img src="'.$this->getSkinImgPath().$icon_url.'" alt="" class="icon" />';
				}
				else
				{
					$imgtag = '<img src="'.$this->getSkinImgPath().'file_icons/large/unkown.gif" alt="" class="icon" />';
				}
			}
		}
		
		return $imgtag;
	}
	
}
?>