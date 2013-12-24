<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 提供一些简单但高效的调试工具。
 *
 * @package    Kohana
 * @category   Base
 */
class Kohana_Debug {

	/**
	 * 以HTML格式返回任意数量的变量调试信息
	 * 每个变量都会使用 "pre" 标签来包含它们：
	 *
	 *     // 输出变量的类型和值
	 *     echo Debug::vars($foo, $bar, $baz);
	 *
	 * @param   mixed   $var,...    要调试的变量
	 * @return  string
	 */
	public static function vars()
	{
		if (func_num_args() === 0)
		{
			return;
		}

		// 获取所有传递过来的路径
		$variables = func_get_args();

		$output = array();
		foreach ($variables AS $var)
		{
			$output[] = Debug::_dump($var, 1024);
		}

		return '<pre class="debug">'.implode("\n", $output).'</pre>';
	}

	/**
	 * 返回一个单独变量的调试信息
	 *
	 * 这个方法借鉴了[Nette](http://nettephp.com/)的思路。
	 *
	 * @param   mixed   $value              要调试的变量
	 * @param   integer $length             返回字符串最大长度
	 * @param   integer $level_recursion    递归限制
	 * @return  string
	 */
	public static function dump($value, $length = 128, $level_recursion = 10)
	{
		return Debug::_dump($value, $length, $level_recursion);
	}

	/**
	 * Debug::dump()的实际执行方法
	 *
	 * @param   mixed   $var    要调试的变量
	 * @param   integer $length 返回的最大字符串长度
	 * @param   integer $limit  递归限制
	 * @param   integer $level  当前递归级别
	 * @return  string
	 */
	protected static function _dump( & $var, $length = 128, $limit = 10, $level = 0)
	{
		if ($var === NULL)
		{
			return '<small>NULL</small>';
		}
		elseif (is_bool($var))
		{
			return '<small>bool</small> '.($var ? 'TRUE' : 'FALSE');
		}
		elseif (is_float($var))
		{
			return '<small>float</small> '.$var;
		}
		elseif (is_resource($var))
		{
			if (($type = get_resource_type($var)) === 'stream' AND $meta = stream_get_meta_data($var))
			{
				$meta = stream_get_meta_data($var);

				if (isset($meta['uri']))
				{
					$file = $meta['uri'];

					if (function_exists('stream_is_local'))
					{
						// Only exists on PHP >= 5.2.4
						if (stream_is_local($file))
						{
							$file = Debug::path($file);
						}
					}

					return '<small>resource</small><span>('.$type.')</span> '.htmlspecialchars($file, ENT_NOQUOTES, Kohana::$charset);
				}
			}
			else
			{
				return '<small>resource</small><span>('.$type.')</span>';
			}
		}
		elseif (is_string($var))
		{
			$var = UTF8::clean($var, Kohana::$charset);
			if (UTF8::strlen($var) > $length)
			{
				// Encode the truncated string
				$str = htmlspecialchars(UTF8::substr($var, 0, $length), ENT_NOQUOTES, Kohana::$charset).'&nbsp;&hellip;';
			}
			else
			{
				// Encode the string
				$str = htmlspecialchars($var, ENT_NOQUOTES, Kohana::$charset);
			}

			return '<small>string</small><span>('.strlen($var).')</span> "'.$str.'"';
		}
		elseif (is_array($var))
		{
			$output = array();

			// Indentation for this variable
			$space = str_repeat($s = '    ', $level);

			static $marker;

			if ($marker === NULL)
			{
				// Make a unique marker
				$marker = uniqid("\x00");
			}

			if (empty($var))
			{
			}
			elseif (isset($var[$marker]))
			{
				$output[] = "(\n$space$s*RECURSION*\n$space)";
			}
			elseif ($level < $limit)
			{
				$output[] = "<span>(";

				$var[$marker] = TRUE;
				foreach ($var AS $key => & $val)
				{
					if ($key === $marker) continue;
					if ( ! is_int($key))
					{
						$key = '"'.htmlspecialchars($key, ENT_NOQUOTES, Kohana::$charset).'"';
					}

					$output[] = "$space$s$key => ".Debug::_dump($val, $length, $limit, $level + 1);
				}
				unset($var[$marker]);

				$output[] = "$space)</span>";
			}
			else
			{
				// Depth too great
				$output[] = "(\n$space$s...\n$space)";
			}

			return '<small>array</small><span>('.count($var).')</span> '.implode("\n", $output);
		}
		elseif (is_object($var))
		{
			$array = (array) $var;
			$output = array();
			// 生成缩进
			$space = str_repeat($s = '    ', $level);
			$hash = spl_object_hash($var);
			// 要打印的对象
			static $objects = array();

			if (empty($var))
			{
			}
			elseif (isset($objects[$hash]))
			{
				$output[] = "{\n$space$s*RECURSION*\n$space}";
			}
			elseif ($level < $limit)
			{
				$output[] = "<code>{";

				$objects[$hash] = TRUE;
				foreach ($array AS $key => & $val)
				{
					if ($key[0] === "\x00")
					{
						// Determine if the access is protected or protected
						$access = '<small>'.(($key[1] === '*') ? 'protected' : 'private').'</small>';
						// Remove the access level from the variable name
						$key = substr($key, strrpos($key, "\x00") + 1);
					}
					else
					{
						$access = '<small>public</small>';
					}

					$output[] = "$space$s$access $key => ".Debug::_dump($val, $length, $limit, $level + 1);
				}
				unset($objects[$hash]);

				$output[] = "$space}</code>";
			}
			else
			{
				// Depth too great
				$output[] = "{\n$space$s...\n$space}";
			}

			return '<small>object</small> <span>'.get_class($var).'('.count($array).')</span> '.implode("\n", $output);
		}
		else
		{
			return '<small>'.gettype($var).'</small> '.htmlspecialchars(print_r($var, TRUE), ENT_NOQUOTES, Kohana::$charset);
		}
	}

	/**
	 * 删除文件路径字符串的系统自定义目录
	 * 这个是为了保护系统目录不被泄露
	 *
	 *     // 输出SYS_PATH/Class/Kohana.php
	 *     echo Debug::path(Kohana::find_file('Class', 'Kohana'));
	 *
	 * @param   string  $file  要处理的路径
	 * @return  string
	 */
	public static function path($file)
	{
		if (strpos($file, APP_PATH) === 0)
		{
			$file = 'APP_PATH'.DIRECTORY_SEPARATOR.substr($file, strlen(APP_PATH));
		}
		elseif (strpos($file, SYS_PATH) === 0)
		{
			$file = 'SYS_PATH'.DIRECTORY_SEPARATOR.substr($file, strlen(SYS_PATH));
		}
		elseif (strpos($file, MOD_PATH) === 0)
		{
			$file = 'MOD_PATH'.DIRECTORY_SEPARATOR.substr($file, strlen(MOD_PATH));
		}
		elseif (strpos($file, WEB_PATH) === 0)
		{
			$file = 'WEB_PATH'.DIRECTORY_SEPARATOR.substr($file, strlen(WEB_PATH));
		}

		return $file;
	}

	/**
	 * Returns an HTML string, highlighting a specific line of a file, with some
	 * number of lines padded above and below.
	 *
	 *     // Highlights the current line of the current file
	 *     echo Debug::source(__FILE__, __LINE__);
	 *
	 * @param   string  $file           要打开的文件
	 * @param   integer $line_number    要高亮的行数
	 * @param   integer $padding        要显示的代码行数
	 * @return  string   文件源码
	 * @return  FALSE    如果文件不可读
	 */
	public static function source($file, $line_number, $padding = 5)
	{
		if ( ! $file OR ! is_readable($file))
		{
			// 这个情况下继续执行会产生错误
			return FALSE;
		}

		// 打开文件和设置行数
		$file = fopen($file, 'r');
		$line = 0;

		// 设置读取范围
		$range = array('start' => $line_number - $padding, 'end' => $line_number + $padding);

		// Set the zero-padding amount for line numbers
		$format = '% '.strlen($range['end']).'d';

		$source = '';
		while (($row = fgets($file)) !== FALSE)
		{
			// 递增行数
			if (++$line > $range['end'])
			{
				break;
			}

			if ($line >= $range['start'])
			{
				// 过滤HTML什么的
				$row = htmlspecialchars($row, ENT_NOQUOTES, Kohana::$charset);
				// 过滤左右的空格等
				$row = '<span class="number">'.sprintf($format, $line).'</span> '.$row;
				$row = ($line === $line_number)
					? '<span class="line highlight">'.$row.'</span>' // 对这一行进行高亮处理
					: '<span class="line">'.$row.'</span>';

				// 附加上获取到的源码
				$source .= $row;
			}
		}
		fclose($file);
		return '<pre class="source"><code>'.$source.'</code></pre>';
	}
	
	// 非标准函数调用
	public static $trace_statements = array('include', 'include_once', 'require', 'require_once');

	/**
	 * 返回当前请求回溯的HTML
	 *
	 *     // Displays the entire current backtrace
	 *     echo implode('<br/>', Debug::trace());
	 *
	 * @param   array   $trace
	 * @return  string
	 */
	public static function trace(array $trace = NULL)
	{
		if ($trace === NULL)
		{
			// 开始回溯
			$trace = debug_backtrace();
		}

		$output = array();
		foreach ($trace AS $step)
		{
			if ( ! isset($step['function']))
			{
				continue;
			}

			if (isset($step['file']) AND isset($step['line']))
			{
				// 读取这一步的源码
				$source = Debug::source($step['file'], $step['line']);
			}
			if (isset($step['file']))
			{
				$file = $step['file'];
				if (isset($step['line']))
				{
					$line = $step['line'];
				}
			}

			// function()
			$function = $step['function'];
			if (in_array($step['function'], Debug::$trace_statements))
			{
				$args = empty($step['args'])
					? array() // 没参数
					: array($step['args'][0]); // 过滤文件路径
			}
			elseif (isset($step['args']))
			{
				if ( ! function_exists($step['function']) OR strpos($step['function'], '{closure}') !== FALSE)
				{
					// Introspection on closures or language constructs in a stack trace is impossible
					$params = NULL;
				}
				else
				{
					if (isset($step['class']))
					{
						if (method_exists($step['class'], $step['function']))
						{
							$reflection = new ReflectionMethod($step['class'], $step['function']);
						}
						else
						{
							$reflection = new ReflectionMethod($step['class'], '__call');
						}
					}
					else
					{
						$reflection = new ReflectionFunction($step['function']);
					}

					// 获取方法参数
					$params = $reflection->getParameters();
				}

				$args = array();

				foreach ($step['args'] AS $i => $arg)
				{
					if (isset($params[$i]))
					{
						// Assign the argument by the parameter name
						$args[$params[$i]->name] = $arg;
					}
					else
					{
						// Assign the argument by number
						$args[$i] = $arg;
					}
				}
			}

			if (isset($step['class']))
			{
				// Class->method() 或 Class::method()
				$function = $step['class'].$step['type'].$step['function'];
			}

			$output[] = array(
				'function' => $function,
				'args'     => isset($args)   ? $args : NULL,
				'file'     => isset($file)   ? $file : NULL,
				'line'     => isset($line)   ? $line : NULL,
				'source'   => isset($source) ? $source : NULL,
			);

			unset($function, $args, $file, $line, $source);
		}

		return $output;
	}
}
