<?php
class SF_LIB_Array2Ini extends SF_LIB_ApiObject
{	
	public function __construct() 
	{
		
	}
	
	public function get($arr, $options = array())
	{
		$content = '';
		
		$options_def = array(
								'with_sections' => true,
							);
		$options = array_merge($options_def, $options);
		
		if ($options['with_sections']) 
		{
			foreach ($arr as $key=>$elem) 
			{
				$content .= "[".$key."]\n";
				foreach ($elem as $key2=>$elem2)
				{
					if(is_array($elem2))
					{
						for($i=0;$i<count($elem2);$i++)
						{
							$content .= $key2."[] = \"".$elem2[$i]."\"\n";
						}
					}
					else if($elem2=="")
					{
						$content .= $key2." = \n";
					}
					else
					{
					$content .= $key2." = \"".$elem2."\"\n";
					}
				}
			}
		}
		else 
		{
			foreach ($arr as $key=>$elem) 
			{
				if(is_array($elem))
				{
					for($i=0;$i<count($elem);$i++)
					{
						$content .= $key2."[] = \"".$elem[$i]."\"\n";
					}
				}
				else if($elem=="")
				{
					$content .= $key2." = \n";
				}
				else
				{
					$content .= $key2." = \"".$elem."\"\n";
				}
			}
		}

	   return $content;
	}
	
	public function getArray($ini, $options = array())
	{
		
		$options_def = array(
								'with_sections' => true,
							);
		$options = array_merge($options_def, $options);
		
		
		$process_sections = $options['with_sections'];
	
		$ini = explode("\n", $ini);
		if (count($ini) == 0) {return array();}

		$sections = array();
		$values = array();
		$result = array();
		$globals = array();
		$i = 0;
		foreach ($ini as $line) 
		{
			$line = trim($line);
			$line = str_replace("\t", " ", $line);
	
			// Comments
			if (!preg_match('/^[a-zA-Z0-9[]/', $line)) 
			{
				continue;
			}
	
			// Sections
			if ($line{0} == '[') 
			{
				$tmp = explode(']', $line);
				$sections[] = trim(substr($tmp[0], 1));
				$i++;
				continue;
			}
		
			// Key-value pair
			list($key, $value) = explode('=', $line, 2);
			$key = trim($key);
			$value = trim($value);
	
			if ($i == 0) 
			{
				if (substr($line, -1, 2) == '[]') 
				{
					$globals[$key][] = $value;
				}
				else 
				{
					$globals[$key] = $value;
				}
			} 
			else 
			{
				if (substr($line, -1, 2) == '[]') 
				{
					$values[$i-1][$key][] = $value;
				} 
				else 
				{
					$values[$i-1][$key] = $value;
				}
			}
		}
	
		for($j = 0; $j < $i; $j++) 
		{
			if ($process_sections === true) 
			{
				$result[$sections[$j]] = $values[$j];
			} 
			else 
			{
				$result[] = $values[$j];
			}
		}
	
		return $result + $globals;
	}
	
	public function download($array, $filename, $options=array())
	{
		$content = $this->getIni($array, $options);
		
		$dl = sf_factoryGetObject('LIB', 'Download');
		
		$dl->force($content, array('filename'=>$filename, 'content-type'=>'text/text'));
		
	}
}
?>
