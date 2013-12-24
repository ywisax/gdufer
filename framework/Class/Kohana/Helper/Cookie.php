<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Cookie助手类，功能跟[Request::cookie]有部分重叠
 *
 * @package    Kohana
 * @category   Helpers
 */
class Kohana_Helper_Cookie {

	/**
	 * @var  string  cookie加密盐
	 */
	public static $salt = NULL;

	/**
	 * @var  integer  默认cookie过期时间
	 */
	public static $expiration = 0;

	/**
	 * @var  string  cookie作用路径
	 */
	public static $path = '/';

	/**
	 * @var  string  cookie作用域
	 */
	public static $domain = NULL;

	/**
	 * @var  boolean  只通过安全连接进行传输
	 */
	public static $secure = FALSE;

	/**
	 * @var  boolean  是否只通过http传输cookie，javascript无效
	 */
	public static $httponly = FALSE;

	/**
	 * 获取指定的cookie，主意如果不是自身设置的cookie，那么不能获取
	 * 如果cookie存在，但已经过期，那么该方法会删除该cookie和返回NULL.
	 *
	 *     // 获取键名为"theme"的cookie，没找到的话就返回"blue"
	 *     $theme = Helper_Cookie::get('theme', 'blue');
	 *
	 * @param   string  $key        cookie键名
	 * @param   mixed   $default    默认值
	 * @return  string
	 */
	public static function get($key, $default = NULL)
	{
		if ( ! isset($_COOKIE[$key]))
		{
			// 不存在，返回默认值
			return $default;
		}

		// 获取存在的cookie
		$cookie = $_COOKIE[$key];
		// Find the position of the split between salt and contents
		$split = strlen(Helper_Cookie::salt($key, NULL));

		if (isset($cookie[$split]) AND $cookie[$split] === '~')
		{
			// Separate the salt and the value
			list ($hash, $value) = explode('~', $cookie, 2);

			if (Helper_Cookie::salt($key, $value) === $hash)
			{
				// Cookie有效
				return $value;
			}

			// Cookie无效，删除
			Helper_Cookie::delete($key);
		}

		return $default;
	}

	/**
	 * Sets a signed cookie. Note that all cookie values must be strings and no
	 * automatic serialization will be performed!
	 *
	 *     Helper_Cookie::set('theme', 'red');
	 *
	 * @param   string  $name       cookie名
	 * @param   string  $value      cookie值
	 * @param   integer $expiration 生命期（单位秒）
	 * @return  boolean
	 */
	public static function set($name, $value, $expiration = NULL)
	{
		if ($expiration === NULL)
		{
			// 使用默认过期时间
			$expiration = Helper_Cookie::$expiration;
		}

		if ($expiration !== 0)
		{
			$expiration += time();
		}

		// 给cookie值附加上盐值
		$value = Helper_Cookie::salt($name, $value).'~'.$value;

		return setcookie($name, $value, $expiration, Helper_Cookie::$path, Helper_Cookie::$domain, Helper_Cookie::$secure, Helper_Cookie::$httponly);
	}

	/**
	 * 通过把值设置为NULL和修改过期时间来删除cookie
	 *
	 *     Helper_Cookie::delete('theme');
	 *
	 * @param   string  $name   cookie键名
	 * @return  boolean
	 */
	public static function delete($name)
	{
		// 要先从全局变量中删除
		unset($_COOKIE[$name]);
		return setcookie($name, NULL, -86400, Helper_Cookie::$path, Helper_Cookie::$domain, Helper_Cookie::$secure, Helper_Cookie::$httponly);
	}

	/**
	 * 通过cookie键和值生成特定的不可读字符串
	 *
	 *     $salt = Helper_Cookie::salt('theme', 'red');
	 *
	 * @param   string  $name   cookie键名
	 * @param   string  $value   cookie值
	 * @return  string
	 */
	public static function salt($name, $value)
	{
		// 默认需指定一个salt，为了保证安全，不使用默认salt
		if ( ! Helper_Cookie::$salt)
		{
			throw new Kohana_Exception('A valid cookie salt is required. Please set Helper_Cookie::$salt.');
		}
		$agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';

		return sha1($agent.$name.$value.Helper_Cookie::$salt);
	}

} // Kohana_Cookie完成
