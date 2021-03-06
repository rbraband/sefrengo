<?php
class SF_LIB_Logger extends SF_LIB_ApiObject
{	
	/**
	 * Logger configuration
	 * @var array
	 */
	protected $config = array(
		'is_backend' => FALSE,
		'idclient' => 0,
		'idlang' => 0,
		'logfile_mailaddress' => '',
		'logfile_size' => 1, // 1048576 = 1 MB
		'logfile_path' => '',
		'storage' => array( 
			'*' => array(
				'*' => array(
					'logfile' // default: save all logs to logfile, will be overwritten
				) 
			)
		),
		'storage_substract' => array(
			'*' => array(
				'debug' => array(
					'logfile' // default: do not save debug logs to logfile
				),
				'info' => array(
					'logfile' // default: do not save info logs to logfile
				) 
			)
		),
		'isset_storage' => FALSE,
	);
	
	/**
	 * Configuration object
	 * @var SF_LIB_Config
	 */
	protected $cfg;
	
	/**
	 * Priorities from SF_MODEL_LogSqlItem
	 * @var array
	 */
	protected $priorities = array();
	
	/**
	 * Collected logs
	 * @var array
	 */
	protected $logs = array();
	
	
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		// logger as singleton
		$this->_API_setObjectIsSingleton(TRUE);
	}
	
	/**
	 * Setting if the logger is used in backend (true) or in frontend (false)
	 * @param Boolean $is_backend
	 */
	public function setIsBackend($is_backend)
	{
		$this->config['is_backend'] = (bool)$is_backend;
	}
	
	/**
	 * Sets the client id
	 * @param int $idclient
	 */
	public function setIdclient($idclient)
	{
		$this->config['idclient'] = (int)$idclient;
	}
	
	/**
	 * Sets the lang id
	 * @param int $idlang
	 */
	public function setIdlang($idlang)
	{
		$this->config['idlang'] = (int)$idlang;
	}
	
	/**
	 * Set the path to the log file
	 * @param string $path
	 */
	public function setLogfilePath($path)
	{
		$this->config['logfile_path'] = (string)$path;
	}
	
	/**
	 * Set the path to the log file
	 * @param int $size
	 */
	public function setLogfileSize($size)
	{
		$this->config['logfile_size'] = (int)$size;
	}
	
	/**
	 * Set the e-mail address for logfile
	 * @param string $address
	 */
	public function setLogfileMailAddress($address)
	{
		$this->config['logfile_mailaddress'] = (string)$address;
	}
	
	/**
	 * Save the location of log storage for each type
	 * @param string $outputmedium Defines where to storage ('database', 'logfile, 'screen')
	 * @param string $priority_type Gives a list like 'php[error,warning];*[warning]' or 'sql[info,error];some-userdefined[*]' with the * as wildcard
	 */
	public function setStorage($outputmedium, $priority_type)
	{
		// delete the default storage and storage_substract array the first time user defined storage type is set
		if($this->config['isset_storage'] === FALSE)
		{
			$this->config['storage'] = array();
			$this->config['storage_substract'] = array();
			$this->config['isset_storage'] = TRUE;
		
			// init model
			$logitem = sf_api('MODEL', 'LogSqlItem');
			$this->priorities = $logitem->getPriorities();
			unset($logitem);
			
			$this->cfg = sf_api('LIB', 'Config');
		}
		
		// pattern for regular expression
		$pattern = '/(.*)\[(.*)\]/i';
		
		$array = explode(';', $priority_type);
		foreach($array as $typestring)
		{
			// get the type and prioritys by regular expression
			preg_match_all($pattern, $typestring, $regexp);	
			
			$type = trim($regexp[1][0]);
			$prios = $regexp[2][0];
			$prio_array = explode(',', $prios);
			
			// go further if type is not empty
			if(empty($type))
			{
				continue;
			}
			
			for($i=0; $i<count($prio_array); $i++)
			{
				// trim whitespace
				$prio_array[$i] = trim($prio_array[$i]);
				// go further if priority is not empty
				if(empty($prio_array[$i]))
				{
					continue;
				}
				
				// is this a substract type
				if($type[0] === '-') {
					// save the outputmedium in array under the type and the priority in an extra array
					$this->config['storage_substract'][substr($type, 1)][$prio_array[$i]][] = $outputmedium;
					
				} else {
					// save the outputmedium in array under the type and the priority
					$this->config['storage'][$type][$prio_array[$i]][] = $outputmedium;
				}
			}
		}
	}
	
	/**
	 * Stored logs for display
	 * @return array 
	 */
	public function getLogs()
	{
		return $this->logs;
	}
	
	
	/*
	 * Log functions
	 */
		
	public function fatal($type, $message, $param = array())
	{
		return $this->_handleLog($this->priorities['fatal'], $type, $message, $param);
	}
	
	public function error($type, $message, $param = array()) {
		return $this->_handleLog($this->priorities['error'], $type, $message, $param);
	}
	
	public function warning($type, $message, $param = array())
	{
		return $this->_handleLog($this->priorities['warning'], $type, $message, $param);
	}
	
	public function info($type, $message, $param = array())
	{
		return $this->_handleLog($this->priorities['info'], $type, $message, $param);
	}
	
	public function debug($type, $message, $param = array())
	{
		return $this->_handleLog($this->priorities['debug'], $type, $message, $param);
	}
	
	public function trace($type, $message, $param = array())
	{
		return $this->_handleLog($this->priorities['trace'], $type, $message, $param);
	}
	
	// general function for custom log priorities
	public function log($priority, $type, $message, $param = array())
	{
		return $this->_handleLog($priority, $type, $message, $param);
	}
	
	
	/**
	 * Creates a LogItem and decides where to save (database or file) or show directly
	 * @param int $priority
	 * @param string $type
	 * @param string $message
	 * @param array $param
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _handleLog($priority, $type, $message, $param)
	{
		// priority must be a number and type is required
		if( !is_numeric($priority) || strlen($type) < 0)
		{
			return FALSE;
		}
		
		$priokey = array_search($priority, $this->priorities);
		
		$merged = array();
		// add defined type and priority
		if(array_key_exists($type, $this->config['storage']) && is_array($this->config['storage'][$type][$priokey]))
		{
			$merged = array_merge($merged, $this->config['storage'][$type][$priokey]);
		}
		// add wildcard type for defined priority
		if(array_key_exists('*', $this->config['storage']) && is_array($this->config['storage']['*'][$priokey]))
		{
			$merged = array_merge($merged, $this->config['storage']['*'][$priokey]);
		}
		// add wildcard priority for defined type
		if(array_key_exists($type, $this->config['storage']) && is_array($this->config['storage'][$type]['*']))
		{
			$merged = array_merge($merged, $this->config['storage'][$type]['*']);
		}
		// add wildcard priority and type
		if(array_key_exists('*', $this->config['storage']) && is_array($this->config['storage']['*']['*']))
		{
			$merged = array_merge($merged, $this->config['storage']['*']['*']);
		}
		
		// remove duplicate entries
		$unique = array_unique($merged);

		// is there any output medium to log
		if(count($unique) > 0)
		{
			// create logitem
			$logitem = sf_api('MODEL', 'LogSqlItem');
			$logitem->setField('is_backend', $this->config['is_backend']);
			$logitem->setField('idclient', $this->config['idclient']);
			$logitem->setField('idlang', $this->config['idlang']);
			$logitem->setField('priority', $priority);
			$logitem->setField('priorityname', $priokey);
			$logitem->setField('type', $type);
			$logitem->setField('message', $message);
			$logitem->setField('created', time());
			$logitem->setField('created_author', $this->cfg->auth('uname'));
			if(!empty($param))
			{
				$logitem->setField('param', $param);
			}
			// print_r($logitem);

			foreach($unique as $outputmedium)
			{
				// do not add defined type and priority listed in the substract array -> continue to next item
				if(is_array($this->config['storage_substract'][$type][$priokey]) && in_array($outputmedium, $this->config['storage_substract'][$type][$priokey]))
				{
					// echo 'do not add: '.$type.' - '.$priokey.' - '.$outputmedium.'<br />\n';
					continue;
					
				}
				// do not add wildcard type for defined priority listed in the substract array -> continue to next item
				else if(is_array($this->config['storage_substract']['*'][$priokey]) && in_array($outputmedium, $this->config['storage_substract']['*'][$priokey]))
				{
					// echo 'do not add: '.$type.' - '.$priokey.' - '.$outputmedium.'<br />\n';
					continue;
				
				}
				// do not add wildcard priority for defined type listed in the substract array -> continue to next item
				else if(is_array($this->config['storage_substract'][$type]['*']) && in_array($outputmedium, $this->config['storage_substract'][$type]['*']))
				{
					// echo 'do not add: '.$type.' - '.$priokey.' - '.$outputmedium.'<br />\n';
					continue;
				
				}
				// do not add wildcard priority and type listed in the substract array -> continue to next item
				else if(is_array($this->config['storage_substract']['*']['*']) && in_array($outputmedium, $this->config['storage_substract']['*']['*']))
				{
					// echo 'do not add: '.$type.' - '.$priokey.' - '.$outputmedium.'<br />\n';
					continue;
				
				}
				// save the item
				else
				{
					// echo 'add: '.$type.' - '.$priokey.' - '.$outputmedium.'<br />\n';
					// switch where to storage the item
					switch($outputmedium)
					{
						case 'database':
							$return = $this->_saveLogItemToDB($logitem);
							break;
							
						case 'logfile':
							$return = $this->_saveLogItemToFile($logitem);
							break;
							
						case 'screen':
							$return = $this->_displayLogItem($logitem);
							break;
					}
				}
			}
		} 
		else
		{
			$return = FALSE;
		}
		
		return $return;
	}

	/**
	 * Write an LogItem to the log file.
	 * Decides wheter to save the $item.toString() to frontend or backend logfile and write the log.
	 * @return bool Returns true if item is saved successful, otherwise false
	 * @param SF_MODEL_LogSqlItem $item LogItem
	 */
	function _saveLogItemToFile($item)
	{
		// global $cms_lang;
		$fsm = sf_api('LIB', 'FilesystemManipulation');
		
		$logfile_size = $this->config['logfile_size'];
		$logfile_path = $this->config['logfile_path'];
		
		// if logfile is to big, rename it and send e-mail or stop writing to logfile
		if( file_exists($fsm->utf8_decode($logfile_path)) == TRUE &&
			filesize($fsm->utf8_decode($logfile_path)) >= $logfile_size)
		{
			if($fsm->renameFile($logfile_path, $logfile_path.'.old') === TRUE)
			{
				// want to send a notification email
				if(strlen($this->config['logfile_mailaddress']) > 0)
				{
					// TODO subject und mail body aus der sprachdatei bekommen -> funktioniert nicht, da die Sprachdatei erst sehr spät geladen wird
					//$subject = $cms_lang['logfile_mail_subject'];
					//$tpl_email = printf($cms_lang['logfile_mail_body'], $logfile_path, ($logfile_size/1048576), $logfile_path.'.old');
					
					$subject = 'Sefrengo: Maximale Dateigröße für Log-Datei überschritten';
					$tpl_email = 'Die Log-Datei '.$logfile_path.' hat die angegebene Dateigröße von '.$fsm->readablizeBytes($logfile_size).' überschritten. Die Log-Datei wurde in '.$logfile_path.'.old umbenannt, um weiterhin Logs zu speichern. Bitte löschen Sie die umbenannte Datei aus dem Verzeichnis.';
					
					// TODO Use mail class from new API
					$mail = $GLOBALS['sf_factory']->getObject('UTILS', 'Mail');
					$mail->setFrom('no-reply@'.$_SERVER['SERVER_NAME']);
					$mail->addTo($this->config['logfile_mailaddress']);
					$mail->setSubject($subject);
					$mail->setTxtBody($tpl_email);
					$result = $mail->process();
				}
			}
			// file rename failed, so stop logging in logfile
			else
			{
				return FALSE;				
			}
		}
		
		return $fsm->writeContentToFile($logfile_path, $item, 'a+');
	}

	/**
	 * Initialize the storage of the LogItem in the database
	 * @return bool Returns true if item is saved successful, otherwise false
	 * @param SF_MODEL_LogSqlItem $item LogItem
	 */
	protected function _saveLogItemToDB($item)
	{
		return $item->save();
	}

	/**
	 * Prepare LogItem for direct output
	 * @return bool Returns true if log item is added to the array
	 * @param SF_MODEL_LogSqlItem $item LogItem
	 */
	protected function _displayLogItem($item)
	{
		array_push($this->logs, $item);		
		return TRUE;
	}
	
}
?>