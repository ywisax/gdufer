<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * HTTP助手类
 *
 * @package    Kohana
 * @category   HTTP
 */
abstract class Kohana_HTTP {

	/**
	 * @var  默认使用协议名称
	 */
	public static $protocol = 'HTTP/1.1';

	/**
	 * 跳转到指定的URI
	 *
	 * @param  string    $uri       指定的URI
	 * @param  int       $code      指定状态码
	 */
	public static function redirect($uri = '', $code = 302)
	{
		$e = HTTP_Exception::factory($code);
		if ( ! $e instanceof HTTP_Exception_Redirect)
		{
			throw new Kohana_Exception('Invalid redirect code \':code\'', array(
				':code' => $code
			));
		}

		throw $e->location($uri);
	}

	/**
	 * Checks the browser cache to see the response needs to be returned,
	 * execution will halt and a 304 Not Modified will be sent if the
	 * browser cache is up to date.
	 * 
	 * @param  Request   $request   Request
	 * @param  Response  $response  Response
	 * @param  string    $etag      Resource ETag
	 * @return Response
	 */
	public static function check_cache(Request $request, Response $response, $etag = NULL)
	{
		// 如果为空那就创建一个etag
		if ($etag == NULL)
		{
			$etag = $response->generate_etag();
		}

		// Set the ETag header
		$response->headers('etag', $etag);

		// Add the Cache-Control header if it is not already set
		// This allows etags to be used with max-age, etc
		$response->headers('cache-control')
			? $response->headers('cache-control', $response->headers('cache-control').', must-revalidate')
			: $response->headers('cache-control', 'must-revalidate');

		// Check if we have a matching etag
		if ($request->headers('if-none-match') AND (string) $request->headers('if-none-match') === $etag)
		{
			// No need to send data again
			throw HTTP_Exception::factory(304)->headers('etag', $etag);
		}

		return $response;
	}
	
	const PARSE_HEADER_STRING_REGEX = '/(\w[^\s:]*):[ ]*([^\r\n]*(?:\r\n[ \t][^\r\n]*)*)/';

	/**
	 * 解析HTTP头部字符串
	 *
	 * @param   string   $header_string  Header string to parse
	 * @return  HTTP_Header
	 */
	public static function parse_header_string($header_string)
	{
		// PECL HTTP 扩展有更方便的处理
		if (extension_loaded('http'))
		{
			return new HTTP_Header(http_parse_headers($header_string));
		}

		// 用PHP处理吧
		$headers = array();
		if (preg_match_all(HTTP::PARSE_HEADER_STRING_REGEX, $header_string, $matches))
		{
			foreach ($matches[0] AS $key => $value)
			{
				if ( ! isset($headers[$matches[1][$key]]))
				{
					$headers[$matches[1][$key]] = $matches[2][$key];
				}
				else
				{
					if (is_array($headers[$matches[1][$key]]))
					{
						$headers[$matches[1][$key]][] = $matches[2][$key];
					}
					else
					{
						$headers[$matches[1][$key]] = array(
							$headers[$matches[1][$key]],
							$matches[2][$key],
						);
					}
				}
			}
		}

		// 返回HTTP_Header对象
		return new HTTP_Header($headers);
	}

	/**
	 * Parses the the HTTP request headers and returns an array containing
	 * key value pairs. This method is slow, but provides an accurate
	 * representation of the HTTP request.
	 *
	 *      // Get http headers into the request
	 *      $request->headers = HTTP::request_headers();
	 *
	 * @return  HTTP_Header
	 */
	public static function request_headers()
	{
		// If running on apache server
		if (function_exists('apache_request_headers'))
		{
			// Return the much faster method
			return new HTTP_Header(apache_request_headers());
		}
		// If the PECL HTTP tools are installed
		elseif (extension_loaded('http'))
		{
			// Return the much faster method
			return new HTTP_Header(http_get_request_headers());
		}

		$headers = array();

		// 解析内容类型
		if ( ! empty($_SERVER['CONTENT_TYPE']))
		{
			$headers['content-type'] = $_SERVER['CONTENT_TYPE'];
		}

		// 解析内容长度
		if ( ! empty($_SERVER['CONTENT_LENGTH']))
		{
			$headers['content-length'] = $_SERVER['CONTENT_LENGTH'];
		}

		foreach ($_SERVER AS $key => $value)
		{
			// If there is no HTTP header here, skip
			if (strpos($key, 'HTTP_') !== 0)
			{
				continue;
			}

			// This is a dirty hack to ensure HTTP_X_FOO_BAR becomes x-foo-bar
			$headers[str_replace(array('HTTP_', '_'), array('', '-'), $key)] = $value;
		}

		return new HTTP_Header($headers);
	}

	/**
	 * Processes an array of key value pairs and encodes
	 * the values to meet RFC 3986
	 *
	 * @param   array   $params  Params
	 * @return  string
	 */
	public static function www_form_urlencode(array $params = array())
	{
		if ( ! $params)
		{
			return;
		}

		$encoded = array();

		foreach ($params AS $key => $value)
		{
			$encoded[] = $key.'='.rawurlencode($value);
		}

		return implode('&', $encoded);
	}
} // End Kohana_HTTP
