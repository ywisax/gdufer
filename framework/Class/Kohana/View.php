<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Kohana视图实现类
 *
 * @package    Kohana
 * @category   Base
 */
class Kohana_View {

	// 全局变量数组
	protected static $_global_data = array();

	/**
	 * 返回一个新的视图对象。
	 * 如果你没有指定"file"参数，那么你还需要调用[View::set_filename]。
	 *
	 *     $view = View::factory($file);
	 *
	 * @param   string  $file   视图文件
	 * @param   array   $data   传递的数据
	 * @return  View
	 */
	public static function factory($file = NULL, array $data = NULL)
	{
		return new View($file, $data);
	}

	/**
	 * 渲染和获取视图输出
	 *
	 *     $output = View::capture($file, $data);
	 *
	 * @param   string  $kohana_view_filename   文件名
	 * @param   array   $kohana_view_data       变量
	 * @return  string
	 */
	protected static function capture($kohana_view_filename, array $kohana_view_data)
	{
		// 加载变量到当前空间
		extract($kohana_view_data, EXTR_SKIP);

		if (View::$_global_data)
		{
			// 加载全局变量到当前空间，会覆盖前面的变量
			extract(View::$_global_data, EXTR_SKIP | EXTR_REFS);
		}

		// 获取视图输出
		ob_start();
		
		// 直接调用gzip压缩会导致浏览器编码错误，应该是多重压缩出问题了
		//ob_start("ob_gzhandler");

		try
		{
			// 加载视图文件
			include $kohana_view_filename;
		}
		catch (Exception $e)
		{
			// 删除缓冲区内容
			ob_end_clean();
			// 重新抛出异常
			throw $e;
		}

		// 获取输出，返回
		$response = ob_get_clean();
		return $response;
	}

	/**
	 * 设置全局变量，功能跟[View::set]类似，只是作用域变化了
	 *
	 *     View::set_global($name, $value);
	 *
	 * @param   string  $key    变量名或一个数组
	 * @param   mixed   $value  值
	 * @return  void
	 */
	public static function set_global($key, $value = NULL)
	{
		if (is_array($key))
		{
			foreach ($key AS $key2 => $value)
			{
				View::$_global_data[$key2] = $value;
			}
		}
		else
		{
			View::$_global_data[$key] = $value;
		}
	}

	/**
	 * 绑定全局变量，作用大致跟[View::bind]一样，不过他的作用域是所有视图
	 *
	 *     View::bind_global($key, $value);
	 *
	 * @param   string  $key    variable name
	 * @param   mixed   $value  referenced variable
	 * @return  void
	 */
	public static function bind_global($key, & $value)
	{
		View::$_global_data[$key] =& $value;
	}

	// 当前视图文件
	protected $_file;

	// 变量列表
	protected $_data = array();

	/**
	 * 构造方法，设置视图文件名和数据，默认只通过[View::factory]来调用这个方法
	 *
	 *     $view = new View($file);
	 *
	 * @param   string  $file   view filename
	 * @param   array   $data   array of values
	 * @return  void
	 */
	public function __construct($file = NULL, array $data = NULL)
	{
		if ($file !== NULL)
		{
			$this->set_filename($file);
		}

		if ($data !== NULL)
		{
			// Add the values to the current data
			$this->_data = $data + $this->_data;
		}
	}

	/**
	 * 魔术方法，获取视图中指定的变量。本地变量会优先于全局变量返回。
	 *
	 *     $value = $view->foo;
	 *
	 * [!!] 如果变量不存在，会抛出一个异常。
	 *
	 * @param   string  $key    variable name
	 * @return  mixed
	 */
	public function & __get($key)
	{
		if (array_key_exists($key, $this->_data))
		{
			return $this->_data[$key];
		}
		elseif (array_key_exists($key, View::$_global_data))
		{
			return View::$_global_data[$key];
		}
		else
		{
			throw new Kohana_Exception('View variable is not set: :var',
				array(':var' => $key));
		}
	}

	/**
	 * 魔术方法，相当于[View::set]。
	 *
	 *     $view->foo = 'something';
	 *
	 * @param   string  $key    变量名
	 * @param   mixed   $value  变量值
	 * @return  void
	 */
	public function __set($key, $value)
	{
		$this->set($key, $value);
	}

	/**
	 * 魔术方法，检查变量是否存在
	 *
	 *     isset($view->foo);
	 *
	 * [!!] `NULL`变量通过[isset](http://php.net/isset)是检查不到的。
	 *
	 * @param   string  $key    变量名
	 * @return  boolean
	 */
	public function __isset($key)
	{
		return (isset($this->_data[$key]) OR isset(View::$_global_data[$key]));
	}

	/**
	 * 魔术方法，注销一个给定的变量
	 *
	 *     unset($view->foo);
	 *
	 * @param   string  $key    变量名
	 * @return  void
	 */
	public function __unset($key)
	{
		unset($this->_data[$key], View::$_global_data[$key]);
	}

	/**
	 * 魔术方法，返回[View::render]的输出。
	 *
	 * @return  string
	 */
	public function __toString()
	{
		try
		{
			return $this->render();
		}
		catch (Exception $e)
		{
			/**
			 * 之所有在这里要这样获取输出的错误，是因为这里好像不能直接`__toString`输出。
			 */
			$error_response = Kohana_exception::_handler($e);
			return $error_response->body();
		}
	}

	/**
	 * 设置视图文件
	 *
	 *     $view->set_filename($file);
	 *
	 * @param   string  $file   视图文件
	 * @return  View
	 */
	public function set_filename($file)
	{
		if (($path = Kohana::find_file('View', $file)) === FALSE)
		{
			throw new View_Exception('The requested view :file could not be found', array(
				':file' => $file,
			));
		}

		// 保存文件路径
		$this->_file = $path;

		return $this;
	}

	/**
	 * 设置变量，如：
	 *
	 *     $view->set('foo', 'my value');
	 *
	 * 也可以通过一个数组来批量传递变量：
	 *
	 *     $view->set(array('food' => 'bread', 'beverage' => 'water'));
	 *
	 * @param   string  $key    变量名或变量数组
	 * @param   mixed   $value  值
	 * @return  $this
	 */
	public function set($key, $value = NULL)
	{
		if (is_array($key))
		{
			foreach ($key AS $name => $value)
			{
				$this->_data[$name] = $value;
			}
		}
		else
		{
			$this->_data[$key] = $value;
		}

		return $this;
	}

	/**
	 * Assigns a value by reference. The benefit of binding is that values can
	 * be altered without re-setting them. It is also possible to bind variables
	 * before they have values. Assigned values will be available as a
	 * variable within the view file:
	 *
	 *     // This reference can be accessed as $ref within the view
	 *     $view->bind('ref', $bar);
	 *
	 * @param   string  $key    variable name
	 * @param   mixed   $value  referenced variable
	 * @return  $this
	 */
	public function bind($key, & $value)
	{
		$this->_data[$key] =& $value;

		return $this;
	}

	/**
	 * Renders the view object to a string. Global and local data are merged
	 * and extracted to create local variables within the view file.
	 *
	 *     $output = $view->render();
	 *
	 * [!!] Global variables with the same key name as local variables will be
	 * overwritten by the local variable.
	 *
	 * @param   string  $file   view filename
	 * @return  string
	 */
	public function render($file = NULL)
	{
		if ($file !== NULL)
		{
			$this->set_filename($file);
		}

		if (empty($this->_file))
		{
			throw new View_Exception('You must set the file to use within your view before rendering');
		}

		// Combine local and global data and capture the output
		return View::capture($this->_file, $this->_data);
	}

} // Kohana_View
