<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_StrIreplace extends UTF8_Function {

	/**
	 * Returns a string or an array with all occurrences of search in subject
	 * (ignoring case) and replaced with the given replace value. This is a
	 * UTF8-aware version of [str_ireplace](http://php.net/str_ireplace).
	 *
	 * [!!] This function is very slow compared to the native version. Avoid
	 * using it when possible.
	 *
	 * @param   string|array    $search     text to replace
	 * @param   string|array    $replace    replacement text
	 * @param   string|array    $str        subject text
	 * @param   integer         $count      number of matched and replaced needles will be returned via this parameter which is passed by reference
	 * @return  string  if the input was a string
	 * @return  array   if the input was an array
	 */
	public static function process($search, $replace, $str, & $count = NULL)
	{
		if (UTF8::is_ascii($search) AND UTF8::is_ascii($replace) AND UTF8::is_ascii($str))
		{
			return str_ireplace($search, $replace, $str, $count);
		}

		if (is_array($str))
		{
			foreach ($str AS $key => $val)
			{
				$str[$key] = UTF8::str_ireplace($search, $replace, $val, $count);
			}
			return $str;
		}

		if (is_array($search))
		{
			$keys = array_keys($search);

			foreach ($keys AS $k)
			{
				if (is_array($replace))
				{
					$str = array_key_exists($k, $replace)
						? UTF8::str_ireplace($search[$k], $replace[$k], $str, $count)
						: UTF8::str_ireplace($search[$k], '', $str, $count);
				}
				else
				{
					$str = UTF8::str_ireplace($search[$k], $replace, $str, $count);
				}
			}
			return $str;
		}

		$search = UTF8::strtolower($search);
		$str_lower = UTF8::strtolower($str);

		$total_matched_strlen = 0;
		$i = 0;

		while (preg_match('/(.*?)'.preg_quote($search, '/').'/s', $str_lower, $matches))
		{
			$matched_strlen = strlen($matches[0]);
			$str_lower = substr($str_lower, $matched_strlen);

			$offset = $total_matched_strlen + strlen($matches[1]) + ($i * (strlen($replace) - 1));
			$str = substr_replace($str, $replace, $offset, strlen($search));

			$total_matched_strlen += $matched_strlen;
			$i++;
		}

		$count += $i;
		return $str;
	}

}
