<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_SubstrReplace extends UTF8_Function {

	const REGEX = '/./us';

	/**
	 * Replaces text within a portion of a UTF-8 string. This is a UTF8-aware
	 * version of [substr_replace](http://php.net/substr_replace).
	 *
	 *     $str = UTF8::substr_replace($str, $replacement, $offset);
	 *
	 * @param   string  $str            input string
	 * @param   string  $replacement    replacement string
	 * @param   integer $offset         offset
	 * @return  string
	 */
	public static function process($str, $replacement, $offset, $length = NULL)
	{
		if (UTF8::is_ascii($str))
		{
			return ($length === NULL) ? substr_replace($str, $replacement, $offset) : substr_replace($str, $replacement, $offset, $length);
		}

		$length = ($length === NULL) ? UTF8::strlen($str) : (int) $length;
		preg_match_all(UTF8_Function_SubstrReplace::REGEX, $str, $str_array);
		preg_match_all(UTF8_Function_SubstrReplace::REGEX, $replacement, $replacement_array);

		array_splice($str_array[0], $offset, $length, $replacement_array[0]);
		return implode('', $str_array[0]);
	}

}
