<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Media基类
 *
 * @package    Kohana/Media
 * @category   Base
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/
 */
abstract class Kohana_Media {

	const MEDIA_DIR = 'Media';

	/**
	 * 根据文件路径返回指定的URL
	 */
	public static function url($filepath)
	{
		// 如果开启的静态文件对比
		if (Kohana::config('Media.use_static'))
		{
			// 查找和返回静态文件
			$static_files = Kohana::config('Media.static_file');
			if (isset($static_files[$filepath]))
			{
				return $static_files[$filepath];
			}
		}

		$url = Route::url('media', array(
			'filepath' => $filepath,
		));
		if (Kohana::$environment != Kohana::DEVELOPMENT AND Kohana::config('Media.cdn_domain'))
		{
			$url = Kohana::config('Media.cdn_domain') . $url;
		}

		// 默认根据路由来返回
		return $url;
	}

	/**
	 * 根据文件路径返回指定的URI
	 */
	public static function uri($filepath)
	{
		return Route::get('media')->uri(array(
			'filepath' => $filepath,
		));
	}

	/**
	 * 查找文件
	 */
	public static function find_file($filepath)
	{
		// 缓存键名
		$key = Kohana::config('Media.cache_prefix').$filepath;
		$lifetime = Kohana::config('Media.cache_lifetime');

		// 缓存中存在，就读取缓存
		if ($fullpath = Kohana::cache($key, NULL, $lifetime))
		{
			return $fullpath;
		}

		// 读取文件
		$fullpath = Media::find_source($filepath);
		
		// 写缓存
		Media::wirte_cache($fullpath, $key, $lifetime);
		return $fullpath;
	}

	/**
	 * 读取缓存文件路径
	 */
	public static function find_cache($filepath)
	{
	}

	/**
	 * 读取原始文件
	 */
	public static function find_source($filepath)
	{
		$fullpath = Kohana::find_file(Media::MEDIA_DIR, $filepath, FALSE);
		if ( ! $fullpath)
		{
			throw HTTP_Exception::factory(404);
		}
		
		return $fullpath;
	}

	/**
	 * 写缓存
	 */
	public static function wirte_cache($filepath, $key, $lifetime)
	{
		return Kohana::cache($key, $filepath, $lifetime);
	}
}
