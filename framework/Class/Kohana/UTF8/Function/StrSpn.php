<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_StrSpn extends UTF8_Function {

	const PREVENT_MATCH = '#[-[\].:\\\\^/]#';
	const PREVENT_REPLACE = '\\\\$0';
	const EMPRT_STRING = '';

	/**
	 * Finds the length of the initial segment matching mask. This is a
	 * UTF8-aware version of [strspn](http://php.net/strspn).
	 *
	 *     $found = UTF8::strspn($str, $mask);
	 *
	 * @param   string  $str    input string
	 * @param   string  $mask   mask for search
	 * @param   integer $offset start position of the string to examine
	 * @param   integer $length length of the string to examine
	 * @return  integer length of the initial segment that contains characters in the mask
	 */
	public static function process($str, $mask, $offset = NULL, $length = NULL)
	{
		if ($str == UTF8_Function_StrSpn::EMPRT_STRING OR $mask == UTF8_Function_StrSpn::EMPRT_STRING)
		{
			return 0;
		}

		if (UTF8::is_ascii($str) AND UTF8::is_ascii($mask))
		{
			return ($offset === NULL) ? strspn($str, $mask) : (($length === NULL) ? strspn($str, $mask, $offset) : strspn($str, $mask, $offset, $length));
		}

		if ($offset !== NULL OR $length !== NULL)
		{
			$str = UTF8::substr($str, $offset, $length);
		}

		// Escape these characters:  - [ ] . : \ ^ /
		// The . and : are escaped to prevent possible warnings about POSIX regex elements
		$mask = preg_replace(UTF8_Function_StrSpn::PREVENT_MATCH, UTF8_Function_StrSpn::PREVENT_REPLACE, $mask);
		preg_match('/^[^'.$mask.']+/u', $str, $matches);

		return isset($matches[0]) ? UTF8::strlen($matches[0]) : 0;
	}
}
