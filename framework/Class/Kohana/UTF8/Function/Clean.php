<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_Clean extends UTF8_Function {

	/**
	 * Recursively cleans arrays, objects, and strings. Removes ASCII control
	 * codes and converts to the requested charset while silently discarding
	 * incompatible characters.
	 *
	 *     UTF8::clean($_GET); // Clean GET data
	 *
	 * [!!] This method requires [Iconv](http://php.net/iconv)
	 *
	 * @param   mixed   $var        variable to clean
	 * @param   string  $charset    character set, defaults to Kohana::$charset
	 * @return  mixed
	 */
	public static function process($var, $charset = NULL)
	{
		if ( ! $charset)
		{
			// Use the application character set
			$charset = Kohana::$charset;
		}

		if (is_array($var) OR is_object($var))
		{
			foreach ($var AS $key => $val)
			{
				// Recursion!
				$var[UTF8::clean($key)] = UTF8::clean($val);
			}
		}
		elseif (is_string($var) AND $var !== '')
		{
			// Remove control characters
			$var = UTF8::strip_ascii_ctrl($var);

			if ( ! UTF8::is_ascii($var))
			{
				// Disable notices
				$error_reporting = error_reporting(~E_NOTICE);

				// iconv is expensive, so it is only used when needed
				$var = iconv($charset, $charset.'//IGNORE', $var);

				// Turn notices back on
				error_reporting($error_reporting);
			}
		}

		return $var;
	}

}
