<?php
class SF_LIB_FilesystemManipulation extends SF_LIB_ApiObject 
{
	/**
	 * Unix chmod for directories
	 * @var integer
	 */
	private $chmod_directory = 0755;
	
	/**
	 * Unix chmod for files
	 * @var integer
	 */
	private $chmod_file = 0755;
	
	/**
	 * Constructor converts the object to singleton
	 */
	public function __construct()
	{
		$this->_API_setObjectIsSingleton(TRUE);
	}
	
	
	/* ******************
	 *  DIRECTORY
	 * *****************/
	
	/**
	 * Creates a new directory. If more directories are new,
	 * they will be created.
	 * @param string $pathname Path to the new direcotry
	 * @param integer $mode Special chmod for this directory
	 * @param boolean $recursive Create directories recursive
	 * @return boolean Returns TRUE on success or FALSE on failure. 
	 */
	public function createDirectory($pathname, $mode = FALSE, $recursive = FALSE)
	{
		if($pathname == '')
		{
			return FALSE;
		}
		
		if(is_bool($mode) == TRUE || $mode == null || $mode == '')
		{
			$mode = $this->chmod_directory;
		}
		
		// convert to ISO-8859-1 before used in filesystem function
		$pathname = $this->utf8_decode($pathname);
		
		return mkdir($pathname, $mode, $recursive);
	}
	
	/**
	 * Attempts to rename oldname to newname.
	 * @param string $oldname 
	 * @param string $newname 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	public function renameDirectory($oldname, $newname)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$oldname = $this->utf8_decode($oldname);
		$newname = $this->utf8_decode($newname);
		
		if( $oldname == '' || $newname == '' || 
			!is_dir($oldname) || is_dir($newname))
		{
			return FALSE;
		}
		
		return rename($oldname, $newname);
	}
	
	/**
	 * Makes a copy of the directory source to dest . 
	 * @param string $source 
	 * @param string $dest 
	 * @return Returns TRUE on success or FALSE on failure. 
	 */
	public function copyDirectory($source, $dest)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$source = $this->utf8_decode($source);
		$dest = $this->utf8_decode($dest);
		
		if( $source == '' || $dest == '' || 
			!is_dir($source) || is_dir($dest))
		{
			return FALSE;
		}
		
		if(substr($source, -1) == '/')
		{
			$source = substr($source, 0, -1);
		}
		
		if (!is_dir($dest))
		{
			$this->createDirectory($dest, FALSE, TRUE);
		}
		
		$dir = opendir($source);
		while(false !== ( $file = readdir($dir)) )
		{
			if (( $file != '.' ) && ( $file != '..' ))
			{
				if ( is_dir($source.'/'.$file) )
				{
					copy($source.'/'.$file , $dest.'/'.$file);
					touch($dest, filemtime($source));
				}
			}
		}
		closedir($dir);
		
		return TRUE;
	}
	
	/**
	 * Makes a recursive copy of the directory source to dest with all files.
	 * @param string $source 
	 * @param string $dest 
	 * @return Returns TRUE on success or FALSE on failure. 
	 */
	public function copyDirectoryWithFiles($source, $dest)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$source = $this->utf8_decode($source);
		$dest = $this->utf8_decode($dest);
		
		if( $source == '' || $dest == '' || 
			!is_dir($source) || is_dir($dest))
		{
			return FALSE;
		}
		
		if(substr($source, -1) == '/')
		{
			$source = substr($source, 0, -1);
		}
		
		if (!is_dir($dest))
		{
			$this->createDirectory($dest, FALSE, TRUE);
		}
		
		$dir = opendir($source);
		while(false !== ( $file = readdir($dir)) )
		{
			if (( $file != '.' ) && ( $file != '..' ))
			{
				if ( is_dir($source.'/'.$file) )
				{
					$this->copyDirectoryWithFiles($source.'/'.$file, $dest.'/'.$file);
				}
				else
				{
					copy($source.'/'.$file, $dest.'/'.$file);
					touch($dest, filemtime($source));
				} 
			}
		}
		closedir($dir);
		
		return TRUE;
	}
	
	/**
	 * Moving directory by using function renameDirectory() with PHP rename.
	 * @param string $source 
	 * @param string $dest 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	public function moveDirectory($source, $dest)
	{
		return $this->renameDirectory($source, $dest);
	}
	
	/**
	 * Delete the directory
	 * @param string $dir 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	public function deleteDirectory($dir)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$dir = $this->utf8_decode($dir);
		
		if(!is_dir($dir))
		{
			return FALSE;
		}
		return rmdir($dir);
	}
	
	/**
	 * Deletes the directory recursively with all files
	 * @param string $dir 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	public function deleteDirectoryRecursive($dir)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$dir = $this->utf8_decode($dir);
		
		if (!file_exists($dir))
		{
			return TRUE;
		}
		if (!is_dir($dir))
		{
			return unlink($dir);
		}
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..')
			{
				continue;
			}
			if (!$this->deleteDirectoryRecursive($dir.DIRECTORY_SEPARATOR.$item))
			{
				return FALSE;
			}
		}
		return rmdir($dir);
	}
	
	/**
	 * Checks if a given directory has any subdirectory
	 * @param string $dir
	 * @return Returns TRUE if any subdirectory exists or FALSE if don't.
	 */
	public function hasSubdirectories($dir)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$dir = $this->utf8_decode($dir);
		
		if (!is_dir($dir))
		{
			return FALSE;
		}
		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..')
			{
				continue;
			}
			if (is_dir($dir) == TRUE)
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	
	/**
	 * Tells whether the filename is a directory
	 * @param string $dir
	 * @return boolean Returns TRUE if the filename exists and is a directory, FALSE otherwise. 
	 */
	public function isDir($dir)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$dir = $this->utf8_decode($dir);
		
		return is_dir($dir);
	}
	
	
	/* ******************
	 *  FILE
	 * *****************/
	
	/**
	 * Reads the content of a $file until reached the defined $length.
	 * If $length is zero the filesize if $file is taken as $length.
	 * @param string $file 
	 * @param integer $length 
	 * @return string Returns the readed content.
	 */
	public function readContentFromFile($file, $length = 0)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$file = $this->utf8_decode($file);
		
		if($file == '' || !file_exists($file))
		{
			return '';
		}
		
		if($length <= 0)
		{
			$length = filesize($file);
		}
		
		$handle = fopen($file, "r");
		$contents = fread($handle, $length);
		fclose($handle);
		return $contents;
	}
	
	/**
	 * Creates an empty file if not exists.
	 * @param string $file 
	 * @return Returns TRUE on success or FALSE on failure. 
	 */
	public function createEmptyFile($file)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$file = $this->utf8_decode($file);
		
		if($file == '' && file_exists($file) == TRUE)
		{
			return FALSE;
		}
		
		return touch($file);
	}
	
	/**
	 * Writes a content to the specified file.
	 * @param string $file 
	 * @param string $content 
	 * @param string $mode possible are the PHP options for fopen
	 * @return Returns TRUE on success or FALSE on failure. 
	 */
	public function writeContentToFile($file, $content, $mode='w+')
	{
		// convert to ISO-8859-1 before used in filesystem function
		$file = $this->utf8_decode($file);
		
		if($file == '')
		{
			return '';
		}
		$handle = fopen($file, $mode);
		$bool = fwrite($handle, $content);
		fclose($handle);
		return $bool;
	}
	
	/**
	 * Attempts to rename oldname  to newname.
	 * @param string $oldname
	 * @param string $newname
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	public function renameFile($oldname, $newname)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$oldname = $this->utf8_decode($oldname);
		$newname = $this->utf8_decode($newname);
		
		if( $oldname == '' || $newname == '' || 
			$this->fileExistsCase($oldname) == 0 ||
			$this->fileExistsCase($newname) == 1)
		{
			return FALSE;
		}
		
		return rename($oldname, $newname);
	}
	
	/**
	 * Deletes filename. Similar to the Unix C unlink() function.
	 * @param string $file 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	public function deleteFile($file)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$file = $this->utf8_decode($file);
		
		if(!file_exists($file))
		{
			return FALSE;
		}
		
		return unlink($file);
	}
	
	/**
	 * Makes a copy of the file source  to dest . 
	 * @param string $source 
	 * @param string $dest 
	 * @return Returns TRUE on success or FALSE on failure. 
	 */
	public function copyFile($source, $dest)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$source = $this->utf8_decode($source);
		$dest = $this->utf8_decode($dest);
		
		if( $source == '' || $dest == '' || 
			!file_exists($source) || file_exists($dest))
		{
			return FALSE;
		}
		
		return copy($source, $dest);
	}
	
	/**
	 * Moving file by using function renameFile() with PHP rename.
	 * @param string $source 
	 * @param string $dest 
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	public function moveFile($source, $dest)
	{
		return $this->renameFile($source, $dest);
	}
	
	/**
	 * Moves an uploaded file to a new location
	 * @param string $tmp_name The filename of the uploaded file. 
	 * @param string $destination The destination of the moved file. 
	 * @return  If filename  is not a valid upload file, then no action will occur,
	 * and move_uploaded_file() will return FALSE.
	 * If filename is a valid upload file, but cannot be moved for some reason,
	 * no action will occur, and move_uploaded_file() will return FALSE.
	 * Additionally, a warning will be issued. 
	 */
	public function moveUploadedFile($tmp_name, $destination)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$tmp_name = $this->utf8_decode($tmp_name);
		$destination = $this->utf8_decode($destination);
		
		return move_uploaded_file($tmp_name,$destination);
	}
	
	/**
	 * Returns TRUE if the file named by filename was uploaded via HTTP POST.
	 * This is useful to help ensure that a malicious user hasn't tried
	 * to trick the script into working on files upon which it should not
	 * be working--for instance, /etc/passwd.
	 * This sort of check is especially important if there is any chance
	 * that anything done with uploaded files could reveal their contents
	 * to the user, or even to other users on the same system.
	 * For proper working, the function is_uploaded_file() needs an argument
	 * like $_FILES['userfile']['tmp_name'], - the name of the uploaded file
	 * on the client's machine $_FILES['userfile']['name'] does not work. 
	 * @param string $file
	 * @return boolean Returns TRUE on success or FALSE on failure. 
	 */
	public function isUploadedFile($file)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$file = $this->utf8_decode($file);
		
		return is_uploaded_file($file);
	}
	
	/**
	 * Checks whether a file or directory exists
	 * @param string $file
	 * @return boolean Returns TRUE if the file or directory specified by filename exists; FALSE otherwise. 
	 */
	public function fileExists($file)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$file = $this->utf8_decode($file);
		
		return file_exists($file);
	}
	
	/**
	 * Tells whether the filename is a regular file
	 * @param string $file
	 * @return boolean Returns TRUE if the filename exists and is a regular file, FALSE otherwise. 
	 */
	public function isFile($file)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$file = $this->utf8_decode($file);
		
		return is_file($file);
	}
	
	/**
	 * Check case sensitive if file exists.
	 * @param string $url
	 * @return integer Returns 0 if file does not exist.
	 * Returns 1 if file exists, with correct case.
	 * Returns 2 if file exists, but wrong case.
	 */
	public function fileExistsCase($url)
	{
		// convert to ISO-8859-1 before used in filesystem function
		$url = $this->utf8_decode($url);
		
		$real_path = str_replace('\\','/',realpath($url));
		   
		if(file_exists($url) && $real_path == $url)
		{
			return 1;    //File exists, with correct case
		}
		elseif(file_exists($real_path))
		{
			return 2;    //File exists, but wrong case
		}
		else
		{
			return 0;    //File does not exist
		}
	}
	
	
	/* ******************
	 *  OTHER
	 * *****************/
	
	/**
	 * Returns an associative array containing information about path.
	 * Or when $option is set returns a string.  
	 * @param string $path 
	 * @param string $option 
	 * @return The following associative array elements are returned: dirname, basename, extension (if any), and filename.
	 * If options is used, this function will return a string if not all elements are requested. 
	 */
	public function getPathinfo($path, $option = '')
	{
		// convert to ISO-8859-1 before used in filesystem function
		//$path = $this->utf8_decode($path);
		
		$info = pathinfo($path);
		if($option != '' && array_key_exists($option, $info))
		{
			return $info[$option];
		}
		
		return $info;
	}
	
	/**
	 * Clean the filename from special chars. 
	 * @param string $filename
	 * @return string Returns the sanitized name. On failure returns FALSE.
	 */
	public function cleanFilename($filename)
	{
		if($filename == '')
		{
			return FALSE;
		}
		
		// Changed to strripos to avoid issues with "."
		$ext_point = strripos($filename, "."); 
		if($ext_point===FALSE)
		{
			return FALSE;
		}
		$ext = substr($filename, $ext_point, strlen($filename));
		$filename = substr($filename, 0, $ext_point);
				
		return $this->_sanitizeName($filename).strtolower($ext);
	}
	
	/**
	 * Clean the directory name from special chars. 
	 * @param string $directoryname
	 * @return string Returns the sanitized name. On failure returns FALSE.
	 */
	public function cleanDirectoryname($directoryname)
	{
		if($directoryname == '')
		{
			return FALSE;
		}
				
		return $this->_sanitizeName($directoryname);
	}
	
	/**
	 * Checks if the given string is UTF-8 encoded and if not,
	 * the string will be converted. Otherwise do nothing and
	 * return the given string.
	 * @param string $string
	 * @return string Returns the given string. Wether UTF-8 encoded or not.
	 */
	public function utf8_encode($string)
	{
		// only asccii 0-127 are in use
		if($string == '' || preg_match('/[\x80-\xff]/', $string) == FALSE)
		{
			return $string;
		} 

		$is_utf8 = preg_match('%([\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})%xs', $string) ? true : false; 
		if($is_utf8 == FALSE)
		{
			$string = utf8_encode($string);
		}
		
		return $string;
	}
	
	/**
	 * Checks if the given string is UTF-8 decoded and if not,
	 * the string will be converted. Otherwise do nothing and
	 * return the given string.
	 * @param string $string
	 * @return string Returns the given string. Wether UTF-8 decoded or not.
	 */
	public function utf8_decode($string)
	{
		// only asccii 0-127 are in use
		if($string == '' || preg_match('/[\x80-\xff]/', $string) == FALSE)
		{
			return $string;
		} 

		$is_utf8 = preg_match('%([\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})%xs', $string) ? true : false; 
		if ($is_utf8 == TRUE)
		{
			$string = utf8_decode($string);
		}
		
		return $string;
	}
	
	/**
	 * Convertes the given Size into a readable format
	 * and appends the size unit. 
	 * @param integer $size
	 * @return string Return the converted size with unit.
	 */
	public function readablizeBytes($size)
	{
		$si = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$remainder = $i = 0;
		while ($size >= 1024 && $i < 8) {
			$remainder = (($size & 0x3ff) + $remainder) / 1024;
			$size = $size >> 10;
			$i++;
		}
		return round($size + $remainder, 2) . ' ' . $si[$i];
	}
	
	/**
	 * Removes or convertes all special characters
	 * @param string $string
	 * @return string Returns the clean string
	 */
	protected function _sanitizeName($string)
	{
		$string = trim($string);

		if ( ctype_digit($string) )
		{
			return $string;
		}
		else
		{     
			// replace accented chars
			$accents = '/&([A-Za-z]{1,2})(grave|acute|circ|cedil|uml|lig);/';
			$string_encoded = htmlentities($string, ENT_NOQUOTES, 'UTF-8');
			
			$string = preg_replace($accents, '$1', $string_encoded);
			 
			// clean out the rest
			$replace = array('([\40])', '([^a-zA-Z0-9-_])', '(-{2,})');
			$with = array('-', '', '-');
			$string = preg_replace($replace, $with, $string);
		}
	
		return strtolower($string);
	}
}
?>