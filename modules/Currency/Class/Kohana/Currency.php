<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 汇率转换类，在开发商城系统的时候十分好用。
 */
abstract class Kohana_Currency {

	// Currency instances
	protected static $_instance = array();

	protected $_config = array();
  
	/**
	 * 单例模式，传递要使用的驱动即可
	 *
	 * @return Currency
	 */
	public static function instance($type = NULL)
	{
		if ($type === NULL)
		{
			// 使用默认驱动
			$type = Kohana::config('Currency.driver');
		}
		if ( ! isset(Currency::$_instance[$type]))
		{
			$class = 'Currency_'.ucfirst($type);
			Currency::$_instance[$type] = new $class(Kohana::config('Currency'));
		}

		return Currency::$_instance[$type];
	}

	public function __construct($config)
	{
		$this->_config = $config;
	}

	abstract public function convert($amount, $from, $to);
}
