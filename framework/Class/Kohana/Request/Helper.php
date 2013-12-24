<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 包含了一些Request常用到的助手方法
 *
 * @package    Kohana
 * @category   Request
 */
class Kohana_Request_Helper {

	/**
	 * Checks whether the request called by bot/crawller by useragent string
	 * Preg is faster than for loop
	 *
	 * @return boolean
	 *
	 * @todo use Request::$user_agent but it is null
	 */
	public static function is_crawler()
	{
		$crawlers = 'Bloglines subscriber|Dumbot|Sosoimagespider|QihooBot|FAST-WebCrawler'.
			'|Superdownloads Spiderman|LinkWalker|msnbot|ASPSeek|WebAlta Crawler|'.
			'Lycos|FeedFetcher-Google|Yahoo|YoudaoBot|AdsBot-Google|Googlebot|Scooter|'.
			'Gigabot|Charlotte|eStyle|AcioRobot|GeonaBot|msnbot-media|Baidu|CocoCrawler|'.
			'Google|Charlotte t|Yahoo! Slurp China|Sogou web spider|YodaoBot|MSRBOT|AbachoBOT|'.
			'Sogou head spider|AltaVista|IDBot|Sosospider|Yahoo! Slurp|'.
			'Java VM|DotBot|LiteFinder|Yeti|Rambler|Scrubby|Baiduspider|accoona';

		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			return (preg_match("/$crawlers/i", $_SERVER['HTTP_USER_AGENT']) > 0);
		}

		return FALSE;
	}

	/**
	 * 移动设备的UA关键词
	 */
	public static $mobile_agent_keyword = array(
		'android',
		'avantgo',
		'blackberry',
		'bolt',
		'boost',
		'cricket',
		'docomo',
		'fone',
		'hiptop',
		'mini',
		'mobi',
		'palm',
		'phone',
		'pie',
		'tablet',
		'webos',
		'wos',
	);

	/**
	 * 检测当前访问者是否为移动设备访问，这个方法的检测不是很严谨，不过应该够用了
	 *
	 * @return boolean
	 */
	public static function is_mobile($ua = NULL)
	{
		// 获取UA
		if ($ua === NULL)
		{
			$ua = Request::$user_agent;
		}

		foreach (Request_Helper::$mobile_agent_keyword AS $keyword)
		{
			if (stripos($ua, $keyword) !== FALSE)
			{
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Determines if a file larger than the post_max_size has been uploaded. PHP
	 * does not handle this situation gracefully on its own, so this method
	 * helps to solve that problem.
	 *
	 * @return  boolean
	 */
	public static function post_max_size_exceeded()
	{
		// Make sure the request method is POST
		if (Request::$initial->method() !== HTTP_Request::POST)
			return FALSE;

		// Get the post_max_size in bytes
		$max_bytes = Helper_Number::bytes(ini_get('post_max_size'));

		// Error occurred if method is POST, and content length is too long
		return (Helper_Array::get($_SERVER, 'CONTENT_LENGTH') > $max_bytes);
	}

	/**
	 * Parses an accept header and returns an array (type => quality) of the
	 * accepted types, ordered by quality.
	 *
	 *     $accept = Request_Helper::parse_accept($header, $defaults);
	 *
	 * @param   string   $header   Header to parse
	 * @param   array    $accepts  Default values
	 * @return  array
	 */
	protected static function parse_accept( & $header, array $accepts = NULL)
	{
		if ( ! empty($header))
		{
			// Get all of the types
			$types = explode(',', $header);

			foreach ($types AS $type)
			{
				// Split the type into parts
				$parts = explode(';', $type);

				// Make the type only the MIME
				$type = trim(array_shift($parts));

				// Default quality is 1.0
				$quality = 1.0;

				foreach ($parts AS $part)
				{
					// Prevent undefined $value notice below
					if (strpos($part, '=') === FALSE)
						continue;

					// Separate the key and value
					list ($key, $value) = explode('=', trim($part));

					if ($key === 'q')
					{
						// There is a quality for this type
						$quality = (float) trim($value);
					}
				}

				// Add the accept type and quality
				$accepts[$type] = $quality;
			}
		}

		// Make sure that accepts is an array
		$accepts = (array) $accepts;

		// Order by quality
		arsort($accepts);

		return $accepts;
	}
}
