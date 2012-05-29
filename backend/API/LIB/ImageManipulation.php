<?php
class SF_LIB_ImageManipulation extends SF_LIB_ApiObject 
{
	/**
	 * The phpThumb object that is used to do the heavy stuff... :)
	 * @var phpThumb object
	 */
	private $phpthumb;
	
	/**
	 * Constructor
	 * @return void
	 */
	public function __construct()
	{
		$this->_API_setObjectIsSingleton(TRUE);
		
		$path = str_replace ('\\', '/', dirname(__FILE__) . '/');
		$path .= '../../external/phpthumb/phpthumb.class.php';
		include_once($path);
		
		$this->phpthumb = new phpthumb();
		
		$cfg = sf_api('LIB', 'Config');
		$this->phpthumb->setParameter('config_temp_directory', $cfg->env('path_backend').'upload/out');
	}
	
	
	/**
	 * Get the width of given source image
	 * @param string $src
	 * @return integer Returns the width.
	 */
	public function getWidth($src)
	{
		$size = getimagesize($src);
		return $size[0];
	}
	
	/**
	 * Get the height of given source image
	 * @param string $src
	 * @return integer Returns the height.
	 */
	public function getHeight($src)
	{
		$size = getimagesize($src);
		return $size[1];
	}
	
	/**
	 * Get the mimetype of given source image
	 * @param string $src
	 * @return string Returns the mimetype.
	 */
	public function getMimetype($src)
	{
		$size = getimagesize($src);
		return $size['mime'];
	}
	
	/**
	 * Resizes the image in the X and/or Y direction(s)
	 *
	 * If either is 0 it will keep the original size for that dimension
	 * 
	 * @param string $src
	 * @param integer $new_width
	 * @param integer $new_height
	 * @param string $dest
	 * @param array $options
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function resize($src, $new_width = 0, $new_height = 0, $dest = '', $options = array())
	{
		// 0 means keep original size
		$new_width = (0 == $new_width)
				 ? $this->getWidth($src)
				 : $this->_parseSize($new_width, $this->getWidth($src));
		$new_height = (0 == $new_height)
				 ? $this->getHeight($src)
				 : $this->_parseSize($new_height, $this->getHeight($src));
		
		return $this->_resize($src, $new_width, $new_height, $dest, $options);
	}
	
	/**
	 * Scales the image to the specified width
	 *
	 * This method preserves the aspect ratio
	 *
	 * @param string $src
	 * @param integer $new_width
	 * @param string $dest
	 * @param array $options
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function scaleByWidth($src, $new_width, $dest = '', $options = array())
	{
		$new_height = round(($new_width / $this->getWidth($src)) * $this->getHeight($src), 0);
		return $this->_resize($src, $new_width, $new_height, $dest, $options);
	}
	
	/**
	 * Scales the image to the specified height.
	 *
	 * This method preserves the aspect ratio
	 *
	 * @param string $src
	 * @param integer $new_height
	 * @param string $dest
	 * @param array $options
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function scaleByHeight($src, $new_height, $dest = '', $options = array())
	{
		$new_width = round(($new_height / $this->getHeight($src)) * $this->getWidth($src), 0);
		return $this->_resize($src, $new_width, $new_height, $dest, $options);
	}
	
	/**
	 * Scales an image so that the longest side has the specified dimension.
	 *
	 * This method preserves the aspect ratio
	 *
	 * @param string $src
	 * @param integer $new_length
	 * @param string $dest
	 * @param array $options
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function scaleByLength($src, $new_length, $dest = '', $options = array())
	{
		if ($this->getWidth($src) >= $this->getHeight($src))
		{
			$new_width = $new_length;
			$new_height = round(($new_width / $this->getWidth($src)) * $this->getHeight($src), 0);
		}
		else
		{
			$new_height = $new_length;
			$new_width = round(($new_height / $this->getHeight($src)) * $this->getWidth($src), 0);
		}
		return $this->_resize($src, $new_width, $new_height, $dest, $options);
	}
	
	/**
	 * Zoom/Shrinks and crops the given source image by length to a
	 * quadratic thumbnail. If destination image is not given,
	 * the source image is overwritten.
	 * 
	 * @param string $src
	 * @param integer $new_length
	 * @param string $dest
	 * @param array $options
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	public function zoomCropSquare($src, $new_length, $dest = '', $options = array())
	{
		$this->phpthumb->resetObject();
		
		$this->phpthumb->setSourceFilename($src);
		$this->phpthumb->setParameter('w', $new_length);
		$this->phpthumb->setParameter('h', $new_length);
		
		// Ignore aspect ratio of phpThumb to set own width and height 
		$this->phpthumb->setParameter('zc', 1);
		
		if(array_key_exists('imagemagick_path', $options))
		{
			$this->phpthumb->setParameter('config_imagemagick_path', $options['imagemagick_path']);
		}
		
		if(array_key_exists('output_format', $options))
		{
			$this->phpthumb->setParameter('config_output_format', $options['output_format']);
		}
		
		// take src if dest not given
		$dest = (empty($dest)) ? $src : $dest;
		
		// this line is VERY important, do not remove it!
		if ($this->phpthumb->GenerateThumbnail())
		{
			return $this->phpthumb->RenderToFile($dest);
		}
		
		return FALSE;
	}
	
	/**
	 * Resizes the given source image by width and height.
	 * If destination image is not given, the source image
	 * is overwritten.
	 *   
	 * @param string $src
	 * @param integer $new_width
	 * @param integer $new_height
	 * @param string $dest
	 * @param array $options
	 * @return boolean Returns TRUE on success or FALSE on failure.
	 */
	protected function _resize($src, $new_width, $new_height, $dest = '', $options = array())
	{
		$this->phpthumb->resetObject();
		
		$this->phpthumb->setSourceFilename($src);
		$this->phpthumb->setParameter('w', $new_width);
		$this->phpthumb->setParameter('h', $new_height);
		
		// Ignore aspect ratio of phpThumb to set own width and height 
		$this->phpthumb->setParameter('iar', 1);
		
		if(array_key_exists('imagemagick_path', $options))
		{
			$this->phpthumb->setParameter('config_imagemagick_path', $options['imagemagick_path']);
		}
		
		if(array_key_exists('output_format', $options))
		{
			$this->phpthumb->setParameter('config_output_format', $options['output_format']);
		}
		
		// take src if dest not given
		$dest = (empty($dest)) ? $src : $dest;
		
		// this line is VERY important, do not remove it!
		if ($this->phpthumb->GenerateThumbnail())
		{
			return $this->phpthumb->RenderToFile($dest);
		}
		
		return FALSE;
	}
	
	
	/**
	 * Parses input for number format and convert
	 *
	 * If either parameter is 0 it will be scaled proportionally
	 *
	 * @param mixed $new_size (0, number, percentage 10% or 0.1)
	 * @param integer $old_size
	 * @return integer
	 */
	protected function _parseSize($new_size, $old_size)
	{
		if (substr($new_size, -1) == '%')
		{
			$new_size = substr($new_size, 0, -1);
			$new_size = $new_size / 100;
		}
		if ($new_size > 1)
		{
			return (int) $new_size;
		}
		elseif ($new_size == 0)
		{
			return (int) $old_size;
		}
		else
		{
			return (int) round($new_size * $old_size, 0);
		}
	}

	/**
	 * Returns an angle between 0 and 360 from any angle value
	 *
	 * @param  float $angle The angle to normalize
	 * @return float the angle
	 */
	protected function _rotationAngle($angle)
	{
		$angle %= 360;
		return ($angle < 0) ? $angle + 360 : $angle;
	}	
}
?>