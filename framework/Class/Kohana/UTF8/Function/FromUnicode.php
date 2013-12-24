<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   UTF8
 */
class Kohana_UTF8_Function_FromUnicode extends UTF8_Function {

	/**
	 * Takes an array of ints representing the Unicode characters and returns a UTF-8 string.
	 * Astral planes are supported i.e. the ints in the input can be > 0xFFFF.
	 * Occurrances of the BOM are ignored. Surrogates are not allowed.
	 *
	 *     $str = UTF8::to_unicode($array);
	 *
	 * The Original Code is Mozilla Communicator client code.
	 * The Initial Developer of the Original Code is Netscape Communications Corporation.
	 * Portions created by the Initial Developer are Copyright (C) 1998 the Initial Developer.
	 * Ported to PHP by Henri Sivonen <hsivonen@iki.fi>, see http://hsivonen.iki.fi/php-utf8/
	 * Slight modifications to fit with phputf8 library by Harry Fuecks <hfuecks@gmail.com>.
	 *
	 * @param   array   $str    unicode code points representing a string
	 * @return  string  utf8 string of characters
	 * @return  boolean FALSE if a code point cannot be found
	 */
	public static function process($arr)
	{
		ob_start();

		$keys = array_keys($arr);

		foreach ($keys AS $k)
		{
			// ASCII范围（包括控制字符）
			if (($arr[$k] >= 0) AND ($arr[$k] <= 0x007f))
			{
				echo chr($arr[$k]);
			}
			// 2字节序列
			elseif ($arr[$k] <= 0x07ff)
			{
				echo chr(0xc0 | ($arr[$k] >> 6));
				echo chr(0x80 | ($arr[$k] & 0x003f));
			}
			// 字节顺序标记（跳过）
			elseif ($arr[$k] == 0xFEFF)
			{
				// nop -- 忽视BOM
			}
			// Test for illegal surrogates
			elseif ($arr[$k] >= 0xD800 AND $arr[$k] <= 0xDFFF)
			{
				// Found a surrogate
				throw new UTF8_Exception("UTF8::from_unicode: Illegal surrogate at index: ':index', value: ':value'", array(
					':index' => $k,
					':value' => $arr[$k],
				));
			}
			// 3字节序列
			elseif ($arr[$k] <= 0xffff)
			{
				echo chr(0xe0 | ($arr[$k] >> 12));
				echo chr(0x80 | (($arr[$k] >> 6) & 0x003f));
				echo chr(0x80 | ($arr[$k] & 0x003f));
			}
			// 4字节序列
			elseif ($arr[$k] <= 0x10ffff)
			{
				echo chr(0xf0 | ($arr[$k] >> 18));
				echo chr(0x80 | (($arr[$k] >> 12) & 0x3f));
				echo chr(0x80 | (($arr[$k] >> 6) & 0x3f));
				echo chr(0x80 | ($arr[$k] & 0x3f));
			}
			// 超出范围
			else
			{
				throw new UTF8_Exception("UTF8::from_unicode: Codepoint out of Unicode range at index: ':index', value: ':value'", array(
					':index' => $k,
					':value' => $arr[$k],
				));
			}
		}

		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

}
