<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * PHP模拟[gettext](http://php.net/gettext)实现的一个国际化函数，提供了名为`__()`的快捷函数供调用
 * 总得来说，目前翻译的还不怎么好看
 *
 *     // 一般用法
 *     echo __('Hello, world');
 *
 *     // 带参数的用法
 *     echo __('Hello, :user', array(':user' => $username));
 *
 * @package    Kohana
 * @category   Base
 */
class Kohana_I18N {

	/**
	 * @var  string   目标语言: en-us, es-es, zh-cn, etc
	 */
	public static $lang = 'en-us';

	/**
	 * @var  string  源语言: en-us, es-es, zh-cn, etc
	 */
	public static $source = 'en-us';

	/**
	 * @var  array  已经加载的语言信息
	 */
	protected static $_cache = array();

	/**
	 * 获取和设置当前目标语言
	 *
	 *     // 获取
	 *     $lang = I18N::lang();
	 *
	 *     // 设置
	 *     I18N::lang('es-es');
	 *
	 * @param   string  $lang   表示语言的字符串
	 * @return  string
	 */
	public static function lang($lang = NULL)
	{
		if ($lang)
		{
			// 格式化字符串，保证不会出现乱七八糟的格式啊
			I18N::$lang = strtolower(str_replace(array(' ', '_'), '-', $lang));
		}
		return I18N::$lang;
	}

	/**
	 * 返回翻译后的字符串，附加参数不在这里处理的喔
	 *
	 *     $hello = I18N::get('Hello friends, my name is :name');
	 *
	 * @param   string  $string  文本
	 * @param   string  $lang	 目标语言
	 * @return  string
	 */
	public static function get($string, $lang = NULL)
	{
		if ( ! $lang)
		{
			$lang = I18N::$lang;
		}
		// 加载语言信息
		$table = I18N::load($lang);

		// 返回已经翻译的字符串
		return isset($table[$string]) ? $table[$string] : $string;
	}
	
	const LANGUAGE_FILE_SEPARATOR = '-';

	/**
	 * 获取指定语言的所有语言文本
	 *
	 *     // 获取所有翻译内容
	 *     $messages = I18N::load('es-es');
	 *
	 * @param   string  $lang   要加载的语言
	 * @return  array
	 */
	public static function load($lang)
	{
		if (isset(I18N::$_cache[$lang]))
		{
			return I18N::$_cache[$lang];
		}
		$table = array();

		// 分割字符串
		$parts = explode(I18N::LANGUAGE_FILE_SEPARATOR, $lang);
		do
		{
			// 一步步合并
			$path = implode(DIRECTORY_SEPARATOR, $parts);
			if ($files = Kohana::find_file('I18N', $path, NULL, TRUE))
			{
				$t = array();
				foreach ($files AS $file)
				{
					// 合并语言变量
					$t = array_merge($t, Kohana::load($file));
				}
				// 合并到最终的表中去
				$table += $t;
			}
			// 移除最后一个数组
			array_pop($parts);
		}
		while ($parts);

		// 保存到本地同时返回
		return I18N::$_cache[$lang] = $table;
	}

} // End I18N

if ( ! function_exists('__'))
{
	/**
	 * 翻译助手函数，功能强大。
	 *
	 *    __('Welcome back, :user', array(':user' => $username));
	 *
	 * [!!] 翻译目标语言在[I18N::$lang]中设置.
	 * 
	 * @param   string  $string 要翻译的文本
	 * @param   array   $values 要替换的数据
	 * @param   string  $lang   源语言
	 * @return  string
	 */
	function __($string, array $values = NULL, $lang = 'en-us')
	{
		if ($lang !== I18N::$lang)
		{
			// 不同语言才需要翻译
			$string = I18N::get($string);
		}
		return empty($values) ? $string : strtr($string, $values);
	}
}

