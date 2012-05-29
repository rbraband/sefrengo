<?php
class SF_LIB_SefrengoException extends SF_LIB_ApiObject
{
	/**
	 * PHP Exception
	 * @var Exception
	 */
	protected $exception;
	
	/**
	 * Logger
	 * @var SF_ADMINISTRATION_Logger
	 */
	protected $logger;
	
	/**
	 * Constructor creates a new Exception for the given object bridge.
	 * @param string|integer $priority as name (e.g. 'error', 'warning',...) or as integer.
	 * @param string $message
	 * @param array $param parameter for the logger
	 * @return void
	 */
	public function __construct ($priority = '', $message = '', $param = array())
	{
		$this->_API_setObjectBridge(TRUE);
		
		// get logger (is a singleton)
		$this->_setupLogger();
		
		switch($priority)
		{
			case 'fatal':
			case 0:
				$code = 0;
				$this->logger->fatal('exception', $message, $param);
				break;
			case 'error':
			case 1:
				$code = 1;
				$this->logger->error('exception', $message, $param);
				break;
			case 'warning':
			case 2:
				$code = 2;
				$this->logger->warning('exception', $message, $param);
				break;
			case 'info':
			case 3:
				$code = 3;
				$this->logger->info('exception', $message, $param);
				break;
			case 'debug':
			case 4:
				$code = 4;
				$this->logger->debug('exception', $message, $param);
				break;
			case 'trace':
			case 5:
				$code = 5;
				$this->logger->trace('exception', $message, $param);
				break;
			default:
				$code = 0;
				break;
		}
		
		$this->exception = new Exception($message, $code);
	}
	
	/**
	 * Returns the exception object
	 * @see API/LIBS/SF_LIB_ApiObject#_API_getBridgeObject()
	 * @return Exception
	 */
	public function &_API_getBridgeObject()
	{
		return $this->exception;	
	}
	
	/**
	 * Sets up the {@link $logger}
	 * @return void
	 */
	protected function _setupLogger()
	{
		//$cfg = sf_api('LIB', 'Config');
		$this->logger = sf_api('LIB', 'Logger');
		/*$this->logger->setIsBackend(TRUE);
		$this->logger->setLogfilePath($cfg->cms('log_path'));
		$this->logger->setLogfileSize($cfg->cms('log_size'));
		$this->logger->setLogfileMailAddress($cfg->cms('logfile_mailaddress'));
		$this->logger->setIdclient($cfg->env('idclient'));
		$this->logger->setIdlang($cfg->env('idlang'));
		$this->logger->setStorage('screen', $cfg->cms('logs_storage_screen'));
		$this->logger->setStorage('logfile', $cfg->cms('logs_storage_logfile'));
		$this->logger->setStorage('database', $cfg->cms('logs_storage_database'));*/
	}
}