<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Redirect HTTP exception class. Used for all [HTTP_Exception]'s where the status
 * code indicates a redirect.
 * 
 * Eg [HTTP_Exception_301], [HTTP_Exception_302] and most of the other 30x's
 *
 * @package    Kohana
 * @category   Exceptions
 */
abstract class Kohana_HTTP_Exception_Redirect extends HTTP_Exception_Expected {

	/**
	 * 指定要跳转的URI
	 * 
	 * @param  string  $location  URI
	 */
	public function location($uri = NULL)
	{
		if ($uri === NULL)
		{
			return $this->headers('Location');
		}

		if (strpos($uri, '://') === FALSE)
		{
			// Make the URI into a URL
			$uri = URL::site($uri, TRUE, ! empty(Kohana::$index_file));
		}

		$this->headers('Location', $uri);

		return $this;
	}

	/**
	 * Validate this exception contains everything needed to continue.
	 * 
	 * @return bool
	 */
	public function check()
	{
		if ($this->headers('location') === NULL)
		{
			throw new Kohana_Exception("A 'location' must be specified for a redirect");
		}

		return TRUE;
	}

} // End Kohana_HTTP_Exception_Redirect
