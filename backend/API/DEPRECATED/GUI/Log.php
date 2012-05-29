<?php
class SF_GUI_Log extends SF_LIB_ApiObject {
	
	/**
	 * Stack with items
	 * @var array
	 */
	var $items = array();
	
	/**
	 * Language variables for the logkeys
	 * @var array
	 */
	var $cms_lang = array();
	
	/**
	 * Stores the configuration
	 * @var array 
	 */
	var $config = array(
		'colorize' => true,
		'style_table' => true,
		'hide_column' => array('client', 'author')
	);
	
	/**
	 * Constructor
	 */
	function SF_GUI_Log() {
		//get language variables
		$this->getLang();
	}
	
	/**
	 * Load the language variables for the log keys
	 * @global lang_dir
	 * @global cms_path
	 */
	function getLang() {
		global $lang_dir, $cms_path, $lang_defdir, $cms_lang;
		
		if (file_exists ($lang_dir.'lang_logs.php')) {
			require_once($lang_dir.'lang_logs.php');
			
		} else if (file_exists ($lang_defdir.'lang_logs.php')) {
			require_once($lang_defdir.'lang_logs.php');
			
		} else {
			require_once($cms_path.'lang/de/lang_logs.php');
		}
		
		$this->cms_lang = $cms_lang;
	}
	
	/**
	 * Adds an LogItem to the items stack
	 * @param SF_ADMINISTRATION_LogItem $item
	 */
	function addItem( $item ) {
		array_push($this->items, $item);
	}

	/**
	 * Adds an array with LogItem to the items stack
	 * @param Array $items
	 */
	function addItemsArray( $items ) {
		$this->items = array_merge($this->items, $items);
	}
	
	/**
	 * Add a column that is not displayed
	 * @param String $column Name of the column
	 */
	function hideColumn($column) {
		array_push($this->config['hide_column'], $column);
	}
	
	/**
	 * Style the table output
	 * @param Boolean $bool True, if the table is styled, otherwise false
	 */
	function styleTable($bool) {
		$this->config['style_table'] = $bool;
	}
	
	/**
	 * Colorize the table output each priority with an own color
	 * @param Boolean $bool True, if the table is colorized, otherwise false
	 */
	function colorizeTable($bool) {
		$this->config['colorize'] = $bool;
	}
	

	/**
	 * Generates the output of the items in the items stack
	 * @global cfg_cms
	 * @return String output
	 */
	function show() {
		global $cfg_cms;
		
		$output = '';
		
		// want to style table?
		if($this->config['style_table'] === true) {
			$output .= '<style type="text/css">
				table#sf_debug {
					color: #111;
					text-align: center;
					font-family: verdana, arial, sans-serif;
					font-size: 11px;
					width: 98%;
					margin: 10px;
					border-collapse: collapse;
				}
				table#sf_debug caption {
					text-align:left;
					font-size: 14px;
					font-weight: bold;
				}
				table#sf_debug tr {
					vertical-align: top;
					background-color: #fff;
				}
				table#sf_debug td,
				table#sf_debug th {
					padding: 3px;
					border: 1px solid #111;
				}
				table#sf_debug td.left {
					text-align: left;
				}
				table#sf_debug th {
					background-color: #D6D6D6;
				}
				</style>';
		}
		
		$output .= '<table id="sf_debug">'."\n";
		$output .= '<caption>'.$this->cms_lang['logs_screen'].'</caption>'."\n";
		$output .= '<thead>'."\n";
		$output .= '<tr>'."\n";
		if(in_array('counter', $this->config['hide_column']) === false) {
			$output .= '<th>#</th>'."\n";
		}
		if(in_array('created', $this->config['hide_column']) === false) {
			$output .= '<th>'.$this->cms_lang['logs_created'].'</th>'."\n";
		}
		if(in_array('priority', $this->config['hide_column']) === false) {
			$output .= '<th>'.$this->cms_lang['logs_priority'].'</th>'."\n";
		}
		if(in_array('type', $this->config['hide_column']) === false) {
			$output .= '<th>'.$this->cms_lang['logs_type'].'</th>'."\n";
		}
		if(in_array('message', $this->config['hide_column']) === false) {
			$output .= '<th>'.$this->cms_lang['logs_message'].'</th>'."\n";
		}
		if(in_array('author', $this->config['hide_column']) === false) {
			$output .= '<th>'.$this->cms_lang['logs_author'].'</th>'."\n";
		}
		if(in_array('client', $this->config['hide_column']) === false) {
			$output .= '<th>'.$this->cms_lang['logs_client'].'</th>'."\n";
		}
		$output .= '</tr>'."\n";
		$output .= '</thead>'."\n";
		
		$output .= '<tbody>'."\n";
		
		// loop through stack and output each item
		$counter = 0;
		foreach($this->items as $item) {
			// increase counter
			$counter++;
			
			// want to colorize table?
			if($this->config['colorize'] == true) {
				switch($item->getPriorityName()) {
					case "fatal": $color = "#FFB3B3"; break;
					case "error": $color = "#FFCCCC"; break;
					case "warning": $color = "#FFE6CC"; break;
					case "notice": $color = "#FFF7CE"; break;
					case "info": $color = "#F0F8FF"; break;
					case "debug": $color = "#F8F8F8"; break;
					default: $color = "#FFFFFF"; break;
				}
				$output .= '<tr style="background-color:'.$color.';">'."\n";
			} else {
				//$color = "#FFFFFF";
				$output .= '<tr>'."\n";
			}
			
			if(in_array('counter', $this->config['hide_column']) === false) {
				$output .= '<td>'.$counter.'</td>'."\n";
			}
		
			if(in_array('created', $this->config['hide_column']) === false) {
				$date = $cfg_cms['format_date'];
				$time = (strpos($cfg_cms['format_time'], "s") === false) ? $cfg_cms['FormatTime'].':s' : $cfg_cms['FormatTime'];
				$output .= '<td>'.date($date." - ".$time, htmlentities($item->getCreated(), ENT_COMPAT, 'UTF-8')).'</td>'."\n";
			}
			
			if(in_array('priority', $this->config['hide_column']) === false) {
				$output .= '<td>'.htmlentities($item->getPriorityName(), ENT_COMPAT, 'UTF-8').'</td>'."\n";
			}
			
			if(in_array('type', $this->config['hide_column']) === false) {
				$output .= '<td>'.htmlentities($item->getType(), ENT_COMPAT, 'UTF-8').'</td>'."\n";
			}
			
			if(in_array('message', $this->config['hide_column']) === false) {
				$param = $item->getParam();
				$message_raw = $item->getMessage();
				$message = htmlentities($item->getMessage(), ENT_COMPAT, 'UTF-8');
				// message as langkey exists
				if(isset($this->cms_lang['logs_messages'][$message_raw])) {
					// has parameters?
					if(empty($param) === false) {
						$param2 = array();
						foreach($param as $key => $val) {
							$param2["{".$key."}"] = $val;
						}
						$msg = str_replace(array_keys($param2), array_values($param2), $this->cms_lang['logs_messages'][$message_raw]);
						
					} else {
						$msg = $this->cms_lang['logs_messages'][$message_raw];
					}
					
				} else {
					//wrap long messages
					$msg = wordwrap($message, 50, "<br />\n", true);
					
					// has parameters?
					if(empty($param) === false) {
						$msg .= '<br />'.htmlentities(print_r($param, true), ENT_COMPAT, 'UTF-8');
					}
				}
				$output .= '<td class="left">'.$msg.'</td>'."\n";
			}
			
			if(in_array('author', $this->config['hide_column']) === false) {
				$author = htmlentities($item->getAuthor(), ENT_COMPAT, 'UTF-8');
				if(empty($author) === true) {
					$author = $this->cms_lang['logs_noauthor'];
				}
				$output .= '<td>'.$author.'</td>'."\n";
			}
			
			if(in_array('client', $this->config['hide_column']) === false) {
				if($item->getIsBackend() === true) {
					$output .= '<td>'.$this->cms_lang['logs_backend'].'</td>'."\n";
				} else {
					$output .= '<td>'.htmlentities($item->getClient(), ENT_COMPAT, 'UTF-8').'</td>'."\n";
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