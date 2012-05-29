<?php
class SF_LIB_Array2Csv extends SF_LIB_ApiObject 
{	
	public function __construct() 
	{
		
	}
	
	public function getCsv($array, $options = array())
	{
		
		$options_def = array(
								'delimiter' => ";",
								'newline'=> "\r\n",
							);
		$options = array_merge($options_def, $options);
		
		
		//make csv data
		foreach ($array as $line)
		{
			foreach ($line as $cell)
			{
				$cell = preg_replace("#(\r\n)|(\r)#m", "\n", $cell);
				$out .= '"'.str_replace('"', '""', utf8_decode($cell)).'"'.$options['delimiter'];
			}
			
			$out = rtrim($out);
			$out .= $options['newline'];
		}
		
		return $out;
	
	}
	
	public function getArray()
	{
		//TODO
	}
	
	public function download($array, $filename, $options=array())
	{
		$content = $this->getCsv($array, $options);
		
		$dl = sf_factoryGetObject('LIB', 'Download');
		
		$dl->force($content, array('filename'=>$filename, 'content-type'=>'text/x-comma-separated-values'));
		
	}
}
?>
