<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * LOG（写）记录器基础类，用来实现一些通用的方法
 *
 * @package    Kohana
 * @category   Logging
 */
abstract class Kohana_Log_Writer {

	/**
	 * @var  string  时间戳格式
	 * 
	 * Defaults to Helper_Date::$timestamp_format
	 */
	public static $timestamp;

	/**
	 * @var  string  时间戳格式
	 * 
	 * Defaults to Helper_Date::$timezone, which defaults to date_default_timezone_get()
	 */
	public static $timezone;

	/**
	 * @var  array  级别列表

	 */
	protected $_log_levels = array(
		LOG_EMERG   => 'EMERGENCY',
		LOG_ALERT   => 'ALERT',
		LOG_CRIT    => 'CRITICAL',
		LOG_ERR     => 'ERROR',
		LOG_WARNING => 'WARNING',
		LOG_NOTICE  => 'NOTICE',
		LOG_INFO    => 'INFO',
		LOG_DEBUG   => 'DEBUG',
	);

	/**
	 * @var  int  用于堆栈跟踪的级别
	 */
	public static $strace_level = LOG_DEBUG;
	
	const WRITER_CLASS_PREFIX = 'Log_Writer_';
	
	/**
	 * 返回指定类型的LOG记录器
	 *
	 * @param   string  记录器类型
	 * @param   array   要传递的配置信息
	 * @param   object  指定的对象
	 */
	public static function factory($type, array $config = NULL)
	{
		$class = Log_Writer::WRITER_CLASS_PREFIX . ucfirst($type);
		if ($config === NULL)
		{
			return new $class;
		}
		else
		{
			return new $class($config);
		}
	}

	/**
	 * 写记录
	 *
	 *     $writer->write($messages);
	 *
	 * @param   array   $messages
	 * @return  void
	 */
	abstract public function write(array $messages);

	/**
	 * 返回一个唯一的hash值
	 *
	 *     echo $writer;
	 *
	 * @return  string
	 */
	final public function __toString()
	{
		return spl_object_hash($this);
	}

	/**
	 * 转换为标准的LOG信息
	 * 
	 * @param   array   $message
	 * @param   string  $format
	 * @return  string
	 */
	public function format_message(array $message, $format = "time --- level: body in file:line")
	{
		$message['time'] = Helper_Date::formatted_time('@'.$message['time'], Log_Writer::$timestamp, Log_Writer::$timezone, TRUE);
		$message['level'] = isset($this->_log_levels[$message['level']]) ? $this->_log_levels[$message['level']] : $message['level'];

		$string = strtr($format, $message);

		if (isset($message['additional']['exception']))
		{
			// 重复使用
			$message['body'] = $message['additional']['exception']->getTraceAsString();
			$message['level'] = isset($this->_log_levels[Log_Writer::$strace_level]) ? $this->_log_levels[Log_Writer::$strace_level] : $message['level'];

			// 前后加个换行好看点。。。
			$string .= PHP_EOL . strtr($format, $message) . PHP_EOL;
		}

		return $string;
	}
} // End Kohana_Log_Writer
