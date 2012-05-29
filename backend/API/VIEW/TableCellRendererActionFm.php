<?php

$this->includeClass('VIEW', 'TableCellRendererAction');

class SF_VIEW_TableCellRendererActionFm extends SF_VIEW_TableCellRendererAction
{
	/**
	 * If the idfiletype is set to $action get the matching action icon  
	 * @see API/VIEW/SF_VIEW_SF_VIEW_TableCellRendererAction#_modifyAction($action)
	 */
	protected function _modifyAction($action)
	{
		if( array_key_exists('switch_url_by_filetype', $action) &&
			$this->item->getField('idfiletype') > 0)
		{
			$filetype = sf_api('MODEL', 'FiletypeSqlItem');
			$filetype->loadById( $this->item->getField('idfiletype') );
			
			$html = sf_api('LIB', 'HtmlHelper');
			
			if(strpos($filetype->getField('mimetype'), 'text') !== FALSE || strpos($filetype->getField('mimetype'), 'image') !== FALSE)
			{
				$action['url'] = $action['url_preview'];
				$action['attributes'] = $action['attributes_preview'];
				
				if(strpos($filetype->getField('mimetype'), 'text') !== FALSE )
				{
					$action['attributes'] = $html->addCssClassToAttributes($action['attributes'], 'overlay_text');
				}
				else
				{
					$action['attributes'] = $html->addCssClassToAttributes($action['attributes'], 'overlay_image');
				}
			}
			else
			{
				$action['url'] = $action['url_download'];
				$action['attributes'] = $action['attributes_download'];
			}
			
			$action['attributes'] = $html->addCssClassToAttributes($action['attributes'], 'action');
		}
		
		if( array_key_exists('get_filetype_icon', $action)  &&
			$this->item->getField('idfiletype') > 0)
		{
			$filetype = sf_api('MODEL', 'FiletypeSqlItem');
			$filetype->loadById( $this->item->getField('idfiletype') );
			
			$action['icon'] = 'ressource_browser/icons/rb_typ_'.$filetype->getField('filetype').'.gif';
			unset($filetype);
			
			if(!file_exists($this->getSkinImgPath().$action['icon']))
			{
				$action['icon'] = 'ressource_browser/icons/rb_typ_generic.gif';
			}
		}
		
		return parent::_modifyAction($action);
	}
	
	/**
	 * Generates one row with name, value and attributes for the toolinfo
	 * @see API/VIEWS/SF_VIEW_TableCellRendererActionicon#_buildToolinfoTableRow($item)
	 * @param array $item Part of the $action
	 * @return array Returns an array with array('name' => '', 'value' => '', 'attributes' => '')
	 */
	protected function _buildToolinfoTableRow($item)
	{
		$row = parent::_buildToolinfoTableRow($item);

		if($item['fieldname'] == 'thumbnail')
		{
			$item['thumb_url'] = str_replace('{id}', $this->item->getId(), $item['thumb_url']);
			$item['thumb_url'] = str_replace('{thumb_index}', $item['thumb_index'], $item['thumb_url']);
			
			$row['value'] = $this->_getThumbnailImageTag($item['thumb_index'], $item['thumb_url'], FALSE);
			
			if($row['value'] == '')
			{
				$row = array();
			}
		}
		else if((is_array($item['fieldname']) == TRUE && (in_array('pictwidth', $item['fieldname']) || in_array('pictwidth', $item['fieldname']))) || 
				(is_string($item['fieldname']) == TRUE && ($item['fieldname'] == 'pictwidth' || $item['fieldname'] == 'pictheight')))
		{
			// Note: The formatting of the output is done by the default TableCellFormatter
			//       by replacing the format template variables in the pattern.
			
			// delete row if no image and/or has no dimensions
			if($this->item->getField('pictwidth') == 0 || $this->item->getField('pictheight') == 0)
			{
				$row = array();
			}
		}
		
		return $row;
	}
	
	/**
	 * Generates an IMG tag for the thumbnail of the current item.
	 * @param integer $thumb_index Index number of the thumbnail
	 * @param String $thumb_url Custom thumbnail url (e.g. preview url)
	 * @param boolean $use_fallback_icons Use icons, if no thumbnails available
	 * @return String Returns the IMG tag for the thumbnail
	 */
	protected function _getThumbnailImageTag($thumb_index = 0, $thumb_url = '', $use_fallback_icons = TRUE)
	{
		$imgtag = '';
		
		if($this->item instanceof SF_MODEL_FileSqlItem)
		{
			// Get thumbnail or large filetype icon
			if($this->item->isThumbnailGenerationPossible() == TRUE)
			{
				$thumbdata = $this->item->getThumbnails($thumb_index);
				
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
				$filetype->loadById($this->item->getField('idfiletype'));
		
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