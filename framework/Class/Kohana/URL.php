<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * URL助手类
 *
 * @package    Kohana
 * @category   Helpers
 */
class Kohana_URL {

	/**
	 * 获取当前应用的基址
	 *
	 *     // 不带协议和主机名的绝对路径
	 *     echo URL::base();
	 *
	 *     // 带协议和主机名的完整路径
	 *     echo URL::base('https', TRUE);
	 *
	 *     // 指定请求实例的地址
	 *     echo URL::base($request);
	 *
	 * @param	mixed	$protocol	协议、[Request]对象或者布尔值
	 * @param	boolean	$index	Add index file to URL?
	 * @return	string
	 */
	public static function base($protocol = NULL, $index = FALSE)
	{
		// 先获取设置的基址
		$base_url = Kohana::$base_url;

		if ($protocol === TRUE)
		{
			$protocol = Request::current();
		}

		if ($protocol instanceof Request)
		{
			if ( ! $protocol->secure())
			{
				// 当前协议
				list($protocol) = explode('/', strtolower($protocol->protocol()));
			}
			else
			{
				$protocol = 'https';
			}
		}

		if ( ! $protocol)
		{
			// 默认协议
			$protocol = parse_url($base_url, PHP_URL_SCHEME);
		}

		if ($index === TRUE AND Kohana::$index_file)
		{
			$base_url .= Kohana::$index_file.'/';
		}

		if (is_string($protocol))
		{
			// 有端口号那就附加上端口号
			if ($port = parse_url($base_url, PHP_URL_PORT))
			{
				$port = ':'.$port;
			}

			if ($domain = parse_url($base_url, PHP_URL_HOST))
			{
				// 取出URL外的其他字符
				$base_url = parse_url($base_url, PHP_URL_PATH);
			}
			else
			{
				// Attempt to use HTTP_HOST and fallback to SERVER_NAME
				$domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
			}

			// Add the protocol and domain to the base URL
			$base_url = $protocol.'://'.$domain.$port.$base_url;
		}

		return $base_url;
	}

	const SITE_PATH_REGEX = '~^[-a-z0-9+.]++://[^/]++/?~';
	const SITE_PATH_REPLACE = '';
	const SITE_ENCODE_REGEX = '~([^/]+)~';
	const SITE_ENCODE_CALLBACK = 'URL::_rawurlencode_callback';

	/**
	 * Fetches an absolute site URL based on a URI segment.
	 *
	 *     echo URL::site('foo/bar');
	 *
	 * @param   string  $uri        Site URI to convert
	 * @param   mixed   $protocol   Protocol string or [Request] class to use protocol from
	 * @param   boolean $index      Include the index_page in the URL
	 * @return  string
	 */
	public static function site($uri = '', $protocol = NULL, $index = TRUE)
	{
		$path = preg_replace(URL::SITE_PATH_REGEX, URL::SITE_PATH_REPLACE, trim($uri, '/'));
		if ( ! UTF8::is_ascii($path))
		{
			// Encode all non-ASCII characters, as per RFC 1738
			$path = preg_replace_callback(URL::SITE_ENCODE_REGEX, URL::SITE_ENCODE_CALLBACK, $path);
		}

		// 合并地址
		return URL::base($protocol, $index).$path;
	}

	/**
	 * Callback used for encoding all non-ASCII characters, as per RFC 1738
	 * Used by URL::site()
	 * 
	 * @param  array $matches  Array of matches from preg_replace_callback()
	 * @return string          Encoded string
	 */
	protected static function _rawurlencode_callback($matches)
	{
		return rawurlencode($matches[0]);
	}

	/**
	 * Merges the current GET parameters with an array of new or overloaded
	 * parameters and returns the resulting query string.
	 *
	 *     // Returns "?sort=title&limit=10" combined with any existing GET values
	 *     $query = URL::query(array('sort' => 'title', 'limit' => 10));
	 *
	 * Typically you would use this when you are sorting query results,
	 * or something similar.
	 *
	 * [!!] Parameters with a NULL value are left out.
	 *
	 * @param   array    $params   Array of GET parameters
	 * @param   boolean  $use_get  Include current request GET parameters
	 * @return  string
	 */
	public static function query(array $params = NULL, $use_get = TRUE)
	{
		if ($use_get)
		{
			$params = ($params === NULL)
				? $_GET // Use only the current parameters
				: Helper_Array::merge($_GET, $params); // Merge the current and new parameters
		}
		if (empty($params))
		{
			return '';
		}

		// Note: http_build_query returns an empty string for a params array with only NULL values
		$query = http_build_query($params, '', '&');
		// Don't prepend '?' to an empty string
		return ($query === '') ? '' : ('?'.$query);
	}

	/**
	 * 转换为一个安全的标题文字，主要在英文标题中游泳。
	 *
	 *     echo URL::title('My Blog Post'); // "my-blog-post"
	 *
	 * @param   string   $title       转换字符串
	 * @param   string   $separator   字符分隔符（一般为单个字符）
	 * @param   boolean  $ascii_only  Transliterate to ASCII?
	 * @return  string
	 */
	public static function title($title, $separator = '-', $ascii_only = FALSE)
	{
		if ($ascii_only === TRUE)
		{
			// Transliterate non-ASCII characters
			$title = UTF8::transliterate_to_ascii($title);
			// Remove all characters that are not the separator, a-z, 0-9, or whitespace
			$title = preg_replace('![^'.preg_quote($separator).'a-z0-9\s]+!', '', strtolower($title));
		}
		else
		{
			// 删除所以不是分隔符、字母、数字和空格的字符
			$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', UTF8::strtolower($title));
		}

		// 处理分隔符
		$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);
		return trim($title, $separator);
	}

} // End url
