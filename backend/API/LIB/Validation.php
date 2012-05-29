<?php
class SF_LIB_Validation extends SF_LIB_ApiObject 
{
	public function __construct() 
	{
		$this->_API_setObjectIsSingleton(TRUE);
	}
	
	
	/**
	 * Checks if string is empty.
	 * returns false on empty string, '0' or false
	 *
	 * @param string $str string to check if empty
	 * @return bool returns true if string is not empty
	 */
	public function notEmpty($str)
	{
		if( empty($str) ) 
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Is not empty
	 */
	public function required($str)
	{
		return (strlen(trim($str)) > 0);
	}
	
	/**
	 * str1 equals str2
	 */
	public function match($str1, $str2)
	{
		return ($str1 == $str2);
	}

	/**
	 * str1 equals not str2
	 */
	public function noMatch($str1, $str2)
	{
		return ($str1 != $str2);
	}
	
	/**
	 * valid emil syntax
	 */
	public function email($str)
	{
		return (bool) preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str);
	}
	
	/**
	 * no @ Symbol
	 */
	public function noAt($str)
	{
		return (strpos($str, '@') === FALSE);
	}
	
	/**
	 * The minimum length of a string
	 */
	public function minLength($str, $length)
	{
		if (preg_match("/[^0-9]/", $length))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) >= $length);		
		}

		return (strlen($str) >= $length);
	}
	
	/**
	 * The maximal length of a string
	 */
	public function maxLength($str, $length)
	{
		if (preg_match("/[^0-9]/", $length))
		{
			return FALSE;
		}
		
		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) <= $length);		
		}

		return (strlen($str) <= $length);
	}
	
	/**
	 * The exact length of a string
	 */
	public function exactLength($str, $length)
	{
		if (preg_match("/[^0-9]/", $length))
		{
			return FALSE;
		}
	
		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) == $length);		
		}

		return (strlen($str) == $length);
	}
	
	/**
	 * Only a-z
	 */
	public function alpha($str)
	{
		return (bool) preg_match("/^([a-z])+$/i", $str);
	}
	
	/**
	 * Alphanumeric strings
	 */
	public function alphaNumeric($str)
	{
		return (bool) preg_match("/^([a-z0-9])+$/i", $str);
	}
	
	/**
	 * Found [a-z0-9_-]
	 */
	public function alphaNumericDash($str)
	{
		return (bool) preg_match("/^([-a-z0-9_-])+$/i", $str);
	}
	
	public function numeric($str)
	{
		return (is_numeric($str));
	}
	
	/**
	 * Found +123, -123, 123, +123.4, .4, 123.4
	 */
	public function numericMath($str)
	{
		return (bool) preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
	}
	
	/**
	 * 0,1,2,3,4,...
	 */
	public function natural($str)
	{
		return (bool) preg_match( '/^[0-9]+$/', $str);
	}
	
	/**
	 * 1,2,3,4,5...
	 */
	public function naturalNoZero($str)
	{
		if (! preg_match( '/^[0-9]+$/', $str))
		{
			return FALSE;
		}
	
		if ($str == 0)
		{
			return FALSE;
		}

		return TRUE;
	}
	
	/**
	 * Valid base64 string
	 */
	public function base64($str)
	{
		return (bool) (! preg_match('/[^a-zA-Z0-9\/\+=]/', $str));
	}
	
	/**
	 * Valid filename
	 */
	public function filename($str)
	{
		return (bool) (! preg_match('/[^a-zA-Z0-9\.\_\-]/', $str));
	}
	
	/**
	 * Valid directoryname
	 */
	public function directoryname($str)
	{
		return (bool) (! preg_match('/([^a-zA-Z0-9\.\_\-])/', $str));
	}

	/**
	 * Regex Validation
	 */
	public function regex($str, $regex)
	{
		return (bool) (! preg_match('/([^0-9a-z\.\_\-])/', $str));
	}
	
	/**
	 * Can call all validation methods of this class. Returns also true if the given 
	 * string is empty. 
	 */
	public function orEmpty($method, $str, $opt = NULL)
	{
		if ($str == '')
		{
			return TRUE;
		}
		
		if (method_exists($this, $method))
		{
			if ($opt != NULL)
			{
				return call_user_method ($method, $this, $str, $opt);
			}
			
			return call_user_method ($method, $this, $str);
		}
		
		return FALSE;
	}
}
?>
