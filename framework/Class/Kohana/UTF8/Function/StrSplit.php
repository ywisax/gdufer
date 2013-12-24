<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_StrSplit extends UTF8_Function {

	/**
	 * Converts a UTF-8 string to an array. This is a UTF8-aware version of
	 * [str_split](http://php.net/str_split).
	 *
	 *     $array = UTF8::str_split($str);
	 *
	 * @param   string  $str            input string
	 * @param   integer $split_length   maximum length of each chunk
	 * @return  array
	 */
	public static function process($str, $split_length = 1)
	{
		$split_length = (int) $split_length;

		if (UTF8::is_ascii($str))
		{
			return str_split($str, $split_length);
		}

		if ($split_length < 1)
		{
			return FALSE;
		}

		if (UTF8::strlen($str) <= $split_length)
		{
			return array($str);
		}

		preg_match_all('/.{'.$split_length.'}|[^\x00]{1,'.$split_length.'}$/us', $str, $matches);

		return $matches[0];
	}

}
