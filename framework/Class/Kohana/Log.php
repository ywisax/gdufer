<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 基于观察者模式的日志记录器
 *
 * [!!] 这个类不支持扩展，只支持附加的记录Writer
 *
 * @package    Kohana
 * @category   Logging
 */
class Kohana_Log {

	// LOG级别
	const EMERGENCY = LOG_EMERG;    // 0
	const ALERT     = LOG_ALERT;    // 1
	const CRITICAL  = LOG_CRIT;     // 2
	const ERROR     = LOG_ERR;      // 3
	const WARNING   = LOG_WARNING;  // 4
	const NOTICE    = LOG_NOTICE;   // 5
	const INFO      = LOG_INFO;     // 6
	const DEBUG     = LOG_DEBUG;    // 7

	/**
	 * @var  boolean  添加记录时，是否立即写日志
	 */
	public static $write_on_add = FALSE;

	/**
	 * @var  Log  单例容器
	 */
	protected static $_instance;

	/**
	 * 获取这个类的单例，同时注册操作。
	 *
	 *     $log = Log::instance();
	 *
	 * @return  Log
	 */
	public static function instance()
	{
		if (Log::$_instance === NULL)
		{
			Log::$_instance = new Log;
			register_shutdown_function(array(Log::$_instance, 'write'));
		}

		return Log::$_instance;
	}

	/**
	 * @var  array  已添加的信息列表
	 */
	protected $_messages = array();

	/**
	 * @var  array  LOG记录器列表
	 */
	protected $_writers = array();

	/**
	 * 附加一个LOG记录器
	 *
	 *     $log->attach($writer);
	 *
	 * @param   Log_Writer  $writer     实例
	 * @param   mixed       $levels     传入要记录的级别数组或者记录的最大级别
	 * @param   integer     $min_level  $levels不是数组时这个才有效，设置最小记录级别
	 * @return  Log
	 */
	public function attach(Log_Writer $writer, $levels = array(), $min_level = 0)
	{
		if ( ! is_array($levels))
		{
			$levels = range($min_level, $levels);
		}
		$this->_writers["{$writer}"] = array
		(
			'object' => $writer,
			'levels' => $levels
		);

		return $this;
	}

	/**
	 * 移除一个LOG记录器
	 *
	 *     $log->detach($writer);
	 *
	 * @param   Log_Writer  $writer instance
	 * @return  Log
	 */
	public function detach(Log_Writer $writer)
	{
		unset($this->_writers["{$writer}"]);
		return $this;
	}

	/**
	 * Adds a message to the log. Replacement values must be passed in to be
	 * replaced using [strtr](http://php.net/strtr).
	 *
	 *     $log->add(Log::ERROR, 'Could not locate user: :user', array(
	 *         ':user' => $username,
	 *     ));
	 *
	 * @param   string  $level       level of message
	 * @param   string  $message     message body
	 * @param   array   $values      values to replace in the message
	 * @param   array   $additional  additional custom parameters to supply to the log writer
	 * @return  Log
	 */
	public function add($level, $message, array $values = NULL, array $additional = NULL)
	{
		if ($values)
		{
			// Insert the values into the message
			$message = strtr($message, $values);
		}

		// Grab a copy of the trace
		if (isset($additional['exception']))
		{
			$trace = $additional['exception']->getTrace();
		}
		else
		{
			// 旧版本PHP没有'DEBUG_BACKTRACE_IGNORE_ARGS'
			if ( ! defined('DEBUG_BACKTRACE_IGNORE_ARGS'))
			{
				$trace = array_map(array($this, 'backtrack_array_map_callback'), array_slice(debug_backtrace(FALSE), 1));
			}
			else
			{
				$trace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 1);
			}
		}

		if ($additional == NULL)
		{
			$additional = array();
		}

		// 创建一条新的记录
		$this->_messages[] = array
		(
			'time'       => time(),
			'level'      => $level,
			'body'       => $message,
			'trace'      => $trace,
			'file'       => isset($trace[0]['file']) ? $trace[0]['file'] : NULL,
			'line'       => isset($trace[0]['line']) ? $trace[0]['line'] : NULL,
			'class'      => isset($trace[0]['class']) ? $trace[0]['class'] : NULL,
			'function'   => isset($trace[0]['function']) ? $trace[0]['function'] : NULL,
			'additional' => $additional,
		);

		if (Log::$write_on_add)
		{
			$this->write();
		}

		return $this;
	}

	/**
	 * 你懂的，callback
	 */
	protected function backtrack_array_map_callback($item)
	{
		unset($item['args']);
		return $item;
	}

	/**
	 * 写记录
	 *
	 *     $log->write();
	 *
	 * @return  void
	 */
	public function write()
	{
		if (empty($this->_messages))
		{
			return;
		}

		// 导入所有消息
		$messages = $this->_messages;
		$this->_messages = array();

		foreach ($this->_writers AS $writer)
		{
			if (empty($writer['levels']))
			{
				// 如果没有指定级别，那就全部写进去
				$writer['object']->write($messages);
			}
			else
			{
				// 已过滤的信息
				$filtered = array();

				foreach ($messages AS $message)
				{
					if (in_array($message['level'], $writer['levels']))
					{
						// 有选择性地过滤信息
						$filtered[] = $message;
					}
				}

				// 写记录
				$writer['object']->write($filtered);
			}
		}
	}

} // End Kohana_Log

