<?php

class SF_LIB_HtmlHelper extends SF_LIB_ApiObject
{
	/**
	 * Constructor converts the object to singleton
	 */
	public function __construct()
	{
		$this->_API_setObjectIsSingleton(TRUE);
	}
	
	/**
	 * Converts an array with key => value pairs into 
	 * an string that can be used in an HTML element.
	 * E.g. this array:
	 * 		array('class' => 'firstclass active', 'style' => 'color:red;');
	 * is converted into:
	 * 		class="firstclass active" style="color:red;"
	 * 
	 * @param array $attributes
	 * @return string Returns the converted string
	 */
	public function attributesArrayToHtmlString($attributes = array())
	{
		$str = '';
		if(is_array($attributes) == TRUE)
		{
			foreach($attributes AS $k=>$v)
			{
				// use single quotes for HTML5 data-* attributes with json objects
				if(strpos($k, 'data-') == 0 && is_array($v))
				{
					$str .= $k."='".json_encode($v)."' ";
				}
				else
				{
					$str .= $k.'="'.$v.'" ';
				}
			}
		}
		return $str;
	}

	/**
	 * Adds a new CSS class to an existing list
	 * of CSS classes in the class attribute.
	 * @param string $attributes String with a list of HTML attributes
	 * @param string $cssclass The CSS class that should be added 
	 * @return string Returns the modified attributes parameter
	 */
	public function addCssClassToAttributes($attributes, $cssclass)
	{
		if($attributes != '')
		{
			if(strpos($attributes, 'class=') === FALSE)
			{
				$attributes .= ' class="'.$cssclass.'"';
			}
			else
			{
				$attributes = str_replace('class="', 'class="'.$cssclass.' ', $attributes);
			}
		}
		else
		{
			$attributes = ' class="'.$cssclass.'"';
		}
		
		return $attributes;
	}
}