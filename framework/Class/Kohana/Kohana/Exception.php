<?php defined('SYS_PATH') OR die('No direct access');
/**
 * Kohana异常扩展类，会使用[I18N]来翻译异常信息
 * 最好配合[XDebug]来使用，事半功倍啊
 *
 * @package    Kohana
 * @category   Exceptions
 */
class Kohana_Kohana_Exception extends Exception {

	/**
	 * @var  array  PHP错误代号 => 错误信息
	 */
	public static $php_errors = array(
		E_ERROR              => 'Fatal Error',
		E_USER_ERROR         => 'User Error',
		E_PARSE              => 'Parse Error',
		E_WARNING            => 'Warning',
		E_USER_WARNING       => 'User Warning',
		E_STRICT             => 'Strict',
		E_NOTICE             => 'Notice',
		E_RECOVERABLE_ERROR  => 'Recoverable Error',
	);

	/**
	 * @var  string  错误渲染视图
	 */
	public static $error_view = 'Kohana.Error';

	// 错误页面输出的content-type
	const ERROR_VIEW_CONTENT_TYPE = 'text/html';

	/**
	 * 构造函数，同时对异常文本进行处理
	 *
	 *     throw new Kohana_Exception('Something went terrible wrong, :user',
	 *         array(':user' => $user));
	 *
	 * @param   string          $message    错误文本
	 * @param   array           $variables  待翻译的变量
	 * @param   integer|string  $code       异常代码
	 * @param   Exception       $previous   上一个异常
	 * @return  void
	 */
	public function __construct($message = '', array $variables = NULL, $code = 0, Exception $previous = NULL)
	{
		// 为了兼容
		if (defined('E_DEPRECATED'))
		{
			Kohana_Exception::$php_errors[E_DEPRECATED] = 'Deprecated';
		}
	
		$message = __($message, $variables);
		
		// 貌似要判断下版本
		if (version_compare(PHP_VERSION, '5.3.0', '<'))
		{
			parent::__construct($message, (int) $code);
		}
		else
		{
			parent::__construct($message, (int) $code, $previous);
		}

		// 保存未更改的值
		// @link http://bugs.php.net/39615
		$this->code = $code;
	}

	/**
	 * 魔术方法，获取异常文本信息
	 *
	 *     echo $exception;
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return Kohana_Exception::text($this);
	}

	/**
	 * 异常处理器，输出错误信息和源码回溯等
	 *
	 * @param   Exception  $e
	 * @return  boolean
	 */
	public static function handler(Exception $e)
	{
		$response = Kohana_Exception::_handler($e);
		echo $response->send_headers()->body();
		exit(1);
	}

	/**
	 * 异常处理器，记录错误和生成一个[Response]对象
	 *
	 * @param   Exception  $e
	 * @return  boolean
	 */
	public static function _handler(Exception $e)
	{
		try
		{
			// 记录异常信息
			Kohana_Exception::log($e);
			$response = Kohana_Exception::response($e);
			return $response;
		}
		catch (Exception $e)
		{
			/**
			 * Things are going *really* badly for us, We now have no choice
			 * but to bail. Hard.
			 */
			// Clean the output buffer if one exists
			ob_get_level() AND ob_clean();

			// Set the Status code to 500, and Content-Type to text/plain.
			header('Content-Type: text/plain; charset='.Kohana::$charset, TRUE, 500);

			echo Kohana_Exception::text($e);

			exit(1);
		}
	}

	/**
	 * 记录异常信息
	 *
	 * @param   Exception  $e
	 * @param   int        $level
	 * @return  void
	 */
	public static function log(Exception $e, $level = Log::EMERGENCY)
	{
		if (is_object(Kohana::$log))
		{
			// 获取异常文本
			$error = Kohana_Exception::text($e);
			// Add this exception to the log
			Kohana::$log->add($level, $error, NULL, array('exception' => $e));
			// Make sure the logs are written
			Kohana::$log->write();
		}
	}

	/**
	 * Get a single line of text representing the exception:
	 *
	 * Error [ Code ]: Message ~ File [ Line ]
	 *
	 * @param   Exception  $e
	 * @return  string
	 */
	public static function text(Exception $e)
	{
		if ( ! class_exists('Debug'))
		{
			return sprintf('%s [ %s ]: %s ~ %s [ %d ]',
				get_class($e), $e->getCode(), strip_tags($e->getMessage()), $e->getFile(), $e->getLine());
		}
		return sprintf('%s [ %s ]: %s ~ %s [ %d ]',
			get_class($e), $e->getCode(), strip_tags($e->getMessage()), Debug::path($e->getFile()), $e->getLine());
	}

	/**
	 * Get a Response object representing the exception
	 *
	 * @param   Exception  $e
	 * @return  Response
	 */
	public static function response(Exception $e)
	{
		try
		{
			// 获取异常信息
			$class   = get_class($e);
			$code    = $e->getCode();
			$message = $e->getMessage();
			$file    = $e->getFile();
			$line    = $e->getLine();
			$trace   = $e->getTrace();

			if ( ! headers_sent())
			{
				// Make sure the proper http header is sent
				$http_header_status = ($e instanceof HTTP_Exception) ? $code : 500;
			}

			/**
			 * HTTP_Exceptions are constructed in the HTTP_Exception::factory()
			 * method. We need to remove that entry from the trace and overwrite
			 * the variables from above.
			 */
			if ($e instanceof HTTP_Exception AND $trace[0]['function'] == 'factory')
			{
				extract(array_shift($trace));
			}


			if ($e instanceof ErrorException)
			{
				/**
				 * If XDebug is installed, and this is a fatal error,
				 * use XDebug to generate the stack trace
				 */
				if (function_exists('xdebug_get_function_stack') AND $code == E_ERROR)
				{
					$trace = array_slice(array_reverse(xdebug_get_function_stack()), 4);

					foreach ($trace AS & $frame)
					{
						/**
						 * XDebug pre 2.1.1 doesn't currently set the call type key
						 * http://bugs.xdebug.org/view.php?id=695
						 */
						if ( ! isset($frame['type']))
						{
							$frame['type'] = '??';
						}

						// XDebug also has a different name for the parameters array
						if (isset($frame['params']) AND ! isset($frame['args']))
						{
							$frame['args'] = $frame['params'];
						}
					}
				}
				
				if (isset(Kohana_Exception::$php_errors[$code]))
				{
					// Use the human-readable error name
					$code = Kohana_Exception::$php_errors[$code];
				}
			}

			/**
			 * The stack trace becomes unmanageable inside PHPUnit.
			 *
			 * The error view ends up several GB in size, taking
			 * serveral minutes to render.
			 */
			if (defined('PHPUnit_MAIN_METHOD'))
			{
				$trace = array_slice($trace, 0, 2);
			}

			// 实例化错误视图
			$view = View::factory(Kohana_Exception::$error_view, get_defined_vars());
			// 准备输出对象
			$response = Response::factory();
			// 设置输出状态码
			$response->status(($e instanceof HTTP_Exception) ? $e->getCode() : 500);
			// 设置输出头部信息
			$response->headers('Content-Type', Kohana_Exception::ERROR_VIEW_CONTENT_TYPE.'; charset='.Kohana::$charset);
			// 设置输出主题
			$response->body($view->render());
		}
		catch (Exception $e)
		{
			/**
			 * 怎么可能这样的，蛋疼
			 */
			$response = Response::factory();
			$response->status(500);
			$response->headers('Content-Type', 'text/plain');
			$response->body(Kohana_Exception::text($e));
		}

		return $response;
	}
	
	/**
	 * 返回调用函数的名称
	 *
	 * @return string
	 */
	public function get_calling_function_name()
	{
		$backtrace = debug_backtrace();
		return $backtrace[2]['function'];
	}

} // End Kohana_Exception
