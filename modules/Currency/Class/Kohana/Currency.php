<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * ����ת���࣬�ڿ����̳�ϵͳ��ʱ��ʮ�ֺ��á�
 */
abstract class Kohana_Currency {

	// Currency instances
	protected static $_instance = array();

	protected $_config = array();
  
	/**
	 * ����ģʽ������Ҫʹ�õ���������
	 *
	 * @return Currency
	 */
	public static function instance($type = NULL)
	{
		if ($type === NULL)
		{
			// ʹ��Ĭ������
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
