<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_UcWords extends UTF8_Function {

	/**
	 * Makes the first character of every word in a UTF-8 string uppercase.
	 * This is a UTF8-aware version of [ucwords](http://php.net/ucwords).
	 *
	 *     $str = UTF8::ucwords($str);
	 *
	 * @param   string  $str    mixed case string
	 * @return  string
	 */
	public static function process($str)
	{
		if (UTF8::is_ascii($str))
		{
			return ucwords($str);
		}

		// [\x0c\x09\x0b\x0a\x0d\x20] matches form feeds, horizontal tabs, vertical tabs, linefeeds and carriage returns.
		// This corresponds to the definition of a 'word' defined at http://php.net/ucwords
		return preg_replace(
			'/(?<=^|[\x0c\x09\x0b\x0a\x0d\x20])[^\x0c\x09\x0b\x0a\x0d\x20]/ue',
			'UTF8::strtoupper(\'$0\')',
			$str
		);
	}

}
