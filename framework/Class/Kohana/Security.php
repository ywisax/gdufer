<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 安全助手类
 *
 * @package    Kohana
 * @category   Security
 */
class Kohana_Security {

	const STRIP_IMAGE_TAGS_MATCH = '#<img\s.*?(?:src\s*=\s*["\']?([^"\'<>\s]*)["\']?[^>]*)?>#is';
	const STRIP_IMAGE_TAGS_REPLACE = '$1';
	
	/**
	 * 从字符串中删除img标签
	 *
	 *     $str = Security::strip_image_tags($str);
	 *
	 * @param   string  $str	要过滤的字符串
	 * @return  string
	 */
	public static function strip_image_tags($str)
	{
		return preg_replace(Security::STRIP_IMAGE_TAGS_MATCH, Security::STRIP_IMAGE_TAGS_REPLACE, $str);
	}

	/**
	 * Encodes PHP tags in a string.
	 *
	 *     $str = Security::encode_php_tags($str);
	 *
	 * @param   string  $str    string to sanitize
	 * @return  string
	 */
	public static function encode_php_tags($str)
	{
		return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $str);
	}

	/**
	 * Returns a safe string for aliases/url-s/file names
	 *
	 * @param string $input Arbitrary string
	 * @return string Cleaned input string
	 */
	public static function safe_string($input)
	{
		return URL::title($input);
	}

} // End Security
