<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 配置读取器基础入口
 *
 * @package    Kohana
 * @category   Configuration
 */
abstract class Kohana_Config_Reader implements Kohana_Config_Source {

	const CONFIG_READER_CLASS_PREFIX = 'Config_Reader_';

	/**
	 * 加载指定的Config_Reader
	 */
	public static function factory($type, array $config = NULL)
	{
		$class = Config_Reader::CONFIG_READER_CLASS_PREFIX . $type;
		if (is_array($config))
		{
			return new $class($config);
		}
		return new $class;
	}

	abstract public function load($group);
}
