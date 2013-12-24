<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * UTF8助手类，提供多个方法来处理UTF8编码相关的应用
 *
 * @package    Kohana
 * @category   Base
 */
class Kohana_UTF8 {

	/**
	 * @var  boolean  服务器是否原生支持UTF8
	 */
	public static $server_utf8 = NULL;
	
	/**
	 * 参考[UTF8_Function_Iconv::process]
	 */
	public static function iconv($string, $to = NULL, $from = NULL)
	{
		return UTF8_Function_Iconv::process($string, $to, $from);
	}

	/**
	 * 参考[UTF8_Function_Clean::process]
	 */
	public static function clean($var, $charset = NULL)
	{
		return UTF8_Function_Clean::process($var, $charset);
	}

	/**
	 * 参考[UTF8_Function_IsAscii::process]
	 */
	public static function is_ascii($str)
	{
		return UTF8_Function_IsAscii::process($str);
	}

	/**
	 * 参考[UTF8_Function_StripAsciiCtrl::process]
	 */
	public static function strip_ascii_ctrl($str)
	{
		return UTF8_Function_StripAsciiCtrl::process($str);
	}

	/**
	 * 参考[UTF8_Function_StripNonAscii::process]
	 */
	public static function strip_non_ascii($str)
	{
		return UTF8_Function_StripNonAscii::process($str);
	}

	/**
	 * 参考[UTF8_Function_TransliterateToASCII::process]
	 */
	public static function transliterate_to_ascii($str, $case = 0)
	{
		return UTF8_Function_TransliterateToASCII::process($str, $case);
	}

	/**
	 * [UTF8_Function_Strlen::process]的助手方法
	 */
	public static function strlen($str)
	{
		return UTF8_Function_StrLen::process($str);
	}

	/**
	 * 参考[UTF8_Function_StrPos::process]
	 */
	public static function strpos($str, $search, $offset = 0)
	{
		return UTF8_Function_StrPos::process($str, $search, $offset);
	}

	/**
	 * 参考[UTF8_Function_Strrpos::process]
	 */
	public static function strrpos($str, $search, $offset = 0)
	{
		return UTF8_Function_Strrpos::process($str, $search, $offset);
	}

	/**
	 * 参考[UTF8_Function_SubStr::process]
	 */
	public static function substr($str, $offset, $length = NULL)
	{
		return UTF8_Function_SubStr::process($str, $offset, $length);
	}

	/**
	 * 参考[UTF8_Function_SubstrReplace::process]
	 */
	public static function substr_replace($str, $replacement, $offset, $length = NULL)
	{
		return UTF8_Function_SubstrReplace::process($str, $replacement, $offset, $length);
	}

	/**
	 * 参考[UTF8_Function_StrToLower::process]
	 */
	public static function strtolower($str)
	{
		return UTF8_Function_StrToLower::process($str);
	}

	/**
	 * 参考[UTF8_Function_StrToUpper::process]
	 */
	public static function strtoupper($str)
	{
		return UTF8_Function_StrToUpper::process($str);
	}

	/**
	 * 参考[UTF8_Function_UcFirst::process]
	 */
	public static function ucfirst($str)
	{
		return UTF8_Function_UcFirst::process($str);
	}

	/**
	 * 参考[UTF8_Function_UcWords::process]
	 */
	public static function ucwords($str)
	{
		return UTF8_Function_UcWords::process($str);
	}

	/**
	 * 参考[UTF8_Function_StrCaseCmp::process]
	 */
	public static function strcasecmp($str1, $str2)
	{
		return UTF8_Function_StrCaseCmp::process($str1, $str2);
	}

	/**
	 * 参考[UTF8_Function_StrIreplace::process]
	 */
	public static function str_ireplace($search, $replace, $str, & $count = NULL)
	{
		return UTF8_Function_StrIreplace::process($search, $replace, $str, $count);
	}

	/**
	 * 参考[UTF8_Function_StrIStr::process]
	 */
	public static function stristr($str, $search)
	{
		return UTF8_Function_StrIStr::process($str, $search);
	}

	/**
	 * 参考[UTF8_Function_StrSpn::process]
	 */
	public static function strspn($str, $mask, $offset = NULL, $length = NULL)
	{
		return UTF8_Function_StrSpn::process($str, $mask, $offset, $length);
	}

	/**
	 * 参考[UTF8_Function_StrCspn::process]
	 */
	public static function strcspn($str, $mask, $offset = NULL, $length = NULL)
	{
		return UTF8_Function_StrCspn::process($str, $mask, $offset, $length);
	}

	/**
	 * 参考[UTF8_Function_StrPad::process]
	 */
	public static function str_pad($str, $final_str_length, $pad_str = ' ', $pad_type = STR_PAD_RIGHT)
	{
		return UTF8_Function_StrPad::process($str, $final_str_length, $pad_str, $pad_type);
	}

	/**
	 * 参考[UTF8_Function_StrSplit::process]
	 */
	public static function str_split($str, $split_length = 1)
	{
		return UTF8_Function_StrSplit::process($str, $split_length);
	}

	/**
	 * 参考[UTF8_Function_StrRev::process]
	 */
	public static function strrev($str)
	{
		return UTF8_Function_StrRev::process($str);
	}

	/**
	 * 参考[UTF8_Function_Trim::process]
	 */
	public static function trim($str, $charlist = NULL)
	{
		return UTF8_Function_Trim::process($str, $charlist);
	}

	/**
	 * 参考[UTF8_Function_LTrim::process]
	 */
	public static function ltrim($str, $charlist = NULL)
	{
		return UTF8_Function_LTrim::process($str, $charlist);
	}

	/**
	 * 参考[UTF8_Function_RTrim::process]
	 */
	public static function rtrim($str, $charlist = NULL)
	{
		return UTF8_Function_RTrim::process($str, $charlist);
	}

	/**
	 * 参考[UTF8_Function_Ord::process]
	 */
	public static function ord($chr)
	{
		return UTF8_Function_Ord::process($str);
	}

	/**
	 * 参考[UTF8_Function_ToUnicode::process]
	 */
	public static function to_unicode($str)
	{
		return UTF8_Function_ToUnicode::process($str);
	}

	/**
	 * 参考[UTF8_Function_FromUnicode::process]
	 */
	public static function from_unicode($arr)
	{
		return UTF8_Function_FromUnicode::process($arr);
	}

} // End UTF8

if (Kohana_UTF8::$server_utf8 === NULL)
{
	// 当前环境是否原生支持UTF8
	Kohana_UTF8::$server_utf8 = (bool) extension_loaded('mbstring');
}
