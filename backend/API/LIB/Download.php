<?php
$this->includeClass('LIB', 'FilesystemManipulation');

class SF_LIB_Download extends SF_LIB_ApiObject 
{
	/**
	 * Forces the download for the given $content.
	 * If no $options set, they are replaced by default options.
	 * After sending the header and content the PHP is exiting.
	 * @param string $content
	 * @param array $options Array contains filename, content-type, charset or content-disposition.
	 * @return void
	 */
	public function force($content, $options = array())
	{
		//terminate outputbuffering if active
		$level = ob_get_level();
		for($level = ob_get_level(); $level>0;--$level)
		{
			ob_end_clean();
		}
		
		$options_def = array(
							'filename' => 'download_'.date('Y-m-d_H_i').'.txt',
							'content-type' => 'text',
							'charset' => 'utf-8',
							'content-disposition' => 'inline'
							);
		
		$options = array_merge($options_def, $options);
		
		header("Expires: Mon, 01 Jan 2000 01:00:00 GMT");
		header("Last-Modified: " . gmdate ("D, d M Y H:i:s", time() ) . " GMT");
		header("Pragma: no-cache");
		header('Content-type: '.$options['content-type'].'; charset='.$options['charset']);
		// as attachment for download
		if($options['content-disposition'] == 'attachment')
		{
			header('Content-disposition: attachment; filename='.$options['filename']);
		}
		else
		{
			header('Content-disposition: inline; filename='.$options['filename']);
		}
		
		// write file
		echo $content;
		
		exit;
	}
	
	/**
	 * Reads the file content and forces the download
	 * @param string $path
	 * @param array $options See function force()
	 * @return void
	 */
	public function forceByPath($path, $options = array())
	{
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		$path_info = $fsm->getPathinfo($path);
		$content = $fsm->readContentFromFile($path, -1);
		$content = $this->_checkUTF8($content);
		
		if(! array_key_exists('filename', $options))
		{
			$options['filename'] = $path_info['filename'];
		}
		
		if(! array_key_exists('content-type', $options))
		{
			$options['content-type'] = $this->_getContentTypeByExtension($path_info['extension']);
			
			// add <pre> for all inline text files, except HTML
			if( $options['content-disposition'] == 'inline' &&
				$options['content-type'] != 'text/html' &&
				stripos($options['content-type'], 'text/') === 0)
			{
				$content = '<pre>'.$content.'</pre>';
			}
		}
		
		$this->force($content, $options);
	}
	
	/**
	 * Get the mime-type from the filetype table.
	 * @param string $filename
	 * @return Returns the mimetype for the file. If not found returns an empty string.
	 */
	private function _getContentTypeByExtension($extension)
	{
		if($extension != '')
		{
			$filetype = sf_api('MODEL', 'FiletypeSqlItem');
			if($filetype->loadByFiletype(array('filetype' => $extension)) != FALSE)
			{
				return $filetype->getField('mimetype');
			}
		}
		
		return '';
	}
	
	/**
	 * Checks if string or array is encoded in UTF-8.
	 * If not, try to encode them.
	 * @param array|string $value
	 * @return array|string Returns the converted incoming value.
	 */
	private function _checkUTF8($value)
	{
		if (is_array($value))
		{
			foreach ($value as $k => $v)
			{
				$value[$k] = $this->_checkUTF8($value[$k]);
			}
		}
		else
		{
			// only asccii 0-127 are in use
			if (! preg_match('/[\x80-\xff]/', $value))
			{
				return $value;
			} 

			$is_utf8 = preg_match('%([\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})%xs', $value) ? true : false; 
			if (! $is_utf8)
			{
				$value = utf8_encode($value);
			} 
		} 
		return $value;
	}
	
}
?>
