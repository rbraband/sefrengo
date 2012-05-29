<?php
class SF_VIEW_LogOutput extends SF_LIB_ApiObject
{	
	/**
	 * Stores the configuration
	 * @var array 
	 */
	protected $config = array(
		'area_name' => 'logs',
		'colorize' => TRUE,
		'hide_column' => array('idclient', 'author')
	);
	
	/**
	 * Stack with items
	 * @var array
	 */
	protected $items = array();
	
	/**
	 * Configuration object
	 * @var SF_LIB_Config
	 */
	protected $cfg;
	
	/**
	 * Language variables for the logkeys
	 * @var array
	 */
	protected $lng = array();
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->cfg = sf_api('LIB', 'Config');
		
		$this->lng = sf_api('LIB', 'Lang');
		$this->lng->loadByFile('lang_logs.php', 'logs', 'lang_dir');
	}
	
	/**
	 * Adds an LogItem to the items stack
	 * @param SF_MODEL_LogSqlItem $item
	 */
	public function addItem($item)
	{
		array_push($this->items, $item);
	}

	/**
	 * Adds an array with LogItem to the items stack
	 * @param Array $items
	 */
	public function addItemsArray($items)
	{
		$this->items = array_merge($this->items, $items);
	}
	
	/**
	 * Add a column that is not displayed
	 * @param String $column Name of the column
	 */
	public function hideColumn($column)
	{
		array_push($this->config['hide_column'], $column);
	}
	
	/**
	 * Colorize the table output each priority with an own color
	 * @param Boolean $bool TRUE, if the table is colorized, otherwise FALSE
	 */
	public function colorizeTable($bool)
	{
		$this->config['colorize'] = $bool;
	}
	

	/**
	 * Generates the output of the items in the items stack
	 * @global cfg_cms
	 * @return String output
	 */
	public function show()
	{	
		$output = '';
		$area_name = $this->config['area_name'];
		$lng_messages = $this->lng->get($area_name.'_messages', $area_name);
		
		$output .= '<table id="sf_debug">'."\n";
		$output .= '<caption>'.$this->lng->get($area_name.'_screen', $area_name).'</caption>'."\n";
		$output .= '<thead>'."\n";
		$output .= '<tr>'."\n";
		if(in_array('counter', $this->config['hide_column']) === FALSE)
		{
			$output .= '<th style="width:25px">#</th>'."\n";
		}
		if(in_array('created', $this->config['hide_column']) === FALSE)
		{
			$output .= '<th style="width:160px">'.$this->lng->get($area_name.'_created', $area_name).'</th>'."\n";
		}
		if(in_array('priority', $this->config['hide_column']) === FALSE)
		{
			$output .= '<th style="width:80px">'.$this->lng->get($area_name.'_priority', $area_name).'</th>'."\n";
		}
		if(in_array('type', $this->config['hide_column']) === FALSE)
		{
			$output .= '<th style="width:120px">'.$this->lng->get($area_name.'_type', $area_name).'</th>'."\n";
		}
		if(in_array('message', $this->config['hide_column']) === FALSE)
		{
			$output .= '<th>'.$this->lng->get($area_name.'_message', $area_name).'</th>'."\n";
		}
		if(in_array('author', $this->config['hide_column']) === FALSE)
		{
			$output .= '<th>'.$this->lng->get($area_name.'_author', $area_name).'</th>'."\n";
		}
		if(in_array('idclient', $this->config['hide_column']) === FALSE)
		{
			$output .= '<th>'.$this->lng->get($area_name.'_client', $area_name).'</th>'."\n";
		}
		$output .= '</tr>'."\n";
		$output .= '</thead>'."\n";
		
		$output .= '<tbody>'."\n";
		
		// loop through stack and output each item
		$counter = 0;
		foreach($this->items as $item)
		{
			// increase counter
			$counter++;
			
			// want to colorize table?
			if($this->config['colorize'] == TRUE)
			{
				switch($item->getField('priorityname')) {
					case "fatal": $color = "#FFB3B3"; break;
					case "error": $color = "#FFCCCC"; break;
					case "warning": $color = "#FFE6CC"; break;
					case "notice": $color = "#FFF7CE"; break;
					case "info": $color = "#F0F8FF"; break;
					case "debug": $color = "#F8F8F8"; break;
					default: $color = "#FFFFFF"; break;
				}
				$output .= '<tr style="background-color:'.$color.';">'."\n";
			}
			else
			{
				//$color = "#FFFFFF";
				$output .= '<tr>'."\n";
			}
			
			if(in_array('counter', $this->config['hide_column']) === FALSE)
			{
				$output .= '<td>'.$counter.'</td>'."\n";
			}
		
			if(in_array('created', $this->config['hide_column']) === FALSE)
			{
				$date = $this->cfg->cms('format_date');
				$time = (strpos($this->cfg->cms('format_time'), "s") === FALSE) ? $this->cfg->cms('FormatTime').':s' : $this->cfg->cms('FormatTime');
				$output .= '<td>'.date($date." - ".$time, htmlentities($item->getField('created'), ENT_COMPAT, 'UTF-8')).'</td>'."\n";
			}
			
			if(in_array('priority', $this->config['hide_column']) === FALSE)
			{
				$output .= '<td>'.htmlentities($item->getField('priorityname'), ENT_COMPAT, 'UTF-8').'</td>'."\n";
			}
			
			if(in_array('type', $this->config['hide_column']) === FALSE)
			{
				$output .= '<td>'.htmlentities($item->getField('type'), ENT_COMPAT, 'UTF-8').'</td>'."\n";
			}
			
			if(in_array('message', $this->config['hide_column']) === FALSE)
			{
				$param = $item->getField('param');
				$message_raw = $item->getField('message');
				$message = htmlentities($item->getField('message'), ENT_COMPAT, 'UTF-8');
				
				// message as langkey exists
				if(isset($lng_messages[$message_raw]))
				{
					// has parameters?
					if(empty($param) === FALSE)
					{
						$param2 = array();
						foreach($param as $key => $val)
						{
							$param2["{".$key."}"] = $val;
						}
						$msg = str_replace(array_keys($param2), array_values($param2), $lng_messages[$message_raw]);
					}
					else
					{
						$msg = $lng_messages[$message_raw];
					}
				}
				else
				{
					//wrap long messages
					$msg = wordwrap($message, 50, "<br />\n", TRUE);
					
					// has parameters?
					if(empty($param) === FALSE)
					{
						$msg .= '<br /><pre class="params"><code>'.htmlentities(print_r($param, TRUE), ENT_COMPAT, 'UTF-8').'</code></pre>';
					}
				}
				
				$output .= '<td class="left">'.$msg.'</td>'."\n";
			}
			
			if(in_array('author', $this->config['hide_column']) === FALSE)
			{
				$author = htmlentities($item->getField('created_author'), ENT_COMPAT, 'UTF-8');
				if(empty($author) === TRUE)
				{
					$author = $this->lng->get($area_name.'_noauthor', $area_name);
				}
				$output .= '<td>'.$author.'</td>'."\n";
			}
			
			if(in_array('idclient', $this->config['hide_column']) === FALSE)
			{
				if($item->getField('is_backend') === TRUE)
				{
					$output .= '<td>'.$this->lng->get($area_name.'_backend', $area_name).'</td>'."\n";
				}
				else
				{
					$output .= '<td>'.htmlentities($item->getField('idclient'), ENT_COMPAT, 'UTF-8').'</td>'."\n";
				}
			}
			
			$output .= '</tr>'."\n";
		}
		$output .= '</tbody>'."\n";
		$output .= '</table>'."\n";
		
		return $output;
	}
}

?>