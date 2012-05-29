<?php
/**
 * Handles the events in the system. On initialization the class loads
 * all available events from the database and caches them. 
 * Events can be run in the normal PHP code by calling the function fire().
 */
class SF_LIB_Event extends SF_LIB_ApiObject
{
	/**
	 * Configuration object
	 * @var SF_LIB_Config
	 */
	protected $cfg;
	
	/**
	 * Cache the events from DB
	 * @var array
	 */
	protected $events = array();
	
	/**
	 * Are the events already loaded
	 * @var boolean
	 */
	protected $is_loaded = FALSE;
	
	/**
	 * Debug events
	 */
	protected $is_debug = FALSE;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_API_setObjectIsSingleton(TRUE);

		$this->cfg = sf_api('LIB', 'Config');
		
		if($this->is_loaded == FALSE)
		{
			$this->_loadEvents();
			$this->is_loaded = TRUE;
		}
	}
	
	/**
	 * Runs the event by the given event name. If multiple actions for one event are defined, the will be executed in order of sortindex. 
	 * Arguments will cascade from one action to another and can be used in the event by the variable $args.
	 * The $returns array define which $args will be changed on multiple event actions and returned after the last action is executed.
	 * @param string $eventname Name of the event (field 'key1' in values table)
	 * @param array $args Optional. An associative array with key and values, that can be used in event code. 
	 * @param array $returns Optional. Simple array where the values according to $args keys.
	 * @return array Returns an array with the keys given in $returns and values from the last return action.
	 */
	public function fire($eventname, $args = array(), $returns = array())
	{
		//echo $eventname.'( '.print_r($args, true).");<br />\n";
		$args_cached = $args;
		
		foreach($this->events as $event)
		{
			// skip if not the correct event
			if($event->getField('name') != $eventname) { continue; }

			if($this->is_debug == TRUE)
			{
				echo 'idevent('.$event->getId().")<br />\n";
				echo '$args: '.print_r($args, true)."<br />\n";
			}
			
			// eval the event code in a separate function
			$eval_return = $this->_evalCode($event->getField('code'), $args);
			
			
			if($this->is_debug == TRUE)
			{
				echo '$eval_return: '.print_r($eval_return, true)."<br />\n";
			}

			// rebuild the $args array and use only $returns from the eval return values
			$args = array();
			foreach($args_cached as $key => $value)
			{
				$args[$key] = (in_array($key, $returns) == TRUE) ? $eval_return[$key] : $value;
			}
		}

		$returnval = array();
		foreach($returns as $value)
		{
			$returnval[$value] = $args[$value];
		}

		
		if($this->is_debug == TRUE)
		{
			echo 'return value: '.print_r($returnval, true)."<br />\n";
		}
		
		return $returnval;
	}
	
	/**
	 * Retrieves all events from the database and stores
	 * them to {$events} array.
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _loadEvents()
	{
		$evtcol = sf_api('MODEL', 'EventSqlCollection');
		$evtcol->setIdclient( $this->cfg->env('idclient') );
		$evtcol->setIdlang( $this->cfg->env('idlang') );
		$evtcol->setOrder('name');
		$evtcol->setOrder('sortindex');
		$success = $evtcol->generate();
		$this->events = $evtcol->getItemsAsArray();
		return $success;
	}
	
	/**
	 * Evaluate the event given event code and pass the $args to the code
	 * @param string $code The event code.
	 * @param array $args The arguments that can be used inside the eval code.
	 * @return eval() returns NULL unless return is called in the evaluated code, in which case the value passed to return is returned. If there is a parse error in the evaluated code, eval() returns FALSE and execution of the following code continues normally. It is not possible to catch a parse error in eval() using set_error_handler(). 
	 */
	protected function _evalCode($code, $args)
	{
		eval($code);
		return $args;
	}
}
?>
