<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 从字符串加载模板
 *
 * 一般来说，这个加载器只在单元测试或其他测试时用到，因为他有比较明显的缺陷，例如不支持模板中的文件包含。
 *
 * When using this loader with a cache mechanism, you should know that a new cache
 * key is generated each time a template content "changes" (the cache key being the
 * source code of the template). If you don't want to see your cache grows out of
 * control, you need to take care of clearing the old cache file by yourself.
 *
 * @package    Kohana/Twig
 * @category   Loader
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Loader_String extends Twig_Loader {

	/**
	 * {@inheritdoc}
	 */
	public function get_source($name)
	{
		return $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function exists($name)
	{
		return TRUE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_cache_key($name)
	{
		return $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_fresh($name, $time)
	{
		return TRUE;
	}
}
