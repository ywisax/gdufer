<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 会话系统基础类
 *
 * @package    Kohana
 * @category   Session
 */
abstract class Kohana_Session {

	/**
	 * @var  string  默认会话驱动器
	 */
	public static $default = 'native';

	/**
	 * @var  布尔值  会话出错时是直接抛出错误还是忽略
	 */
	public static $halt = FALSE;
	
	/**
	 * @var  array  会话实例
	 */
	public static $instances = array();

	/**
	 * 根据给定的类型来创建一个会话单例，暂时可选`native`和`database`
	 *
	 *     $session = Session::instance();
	 *
	 * [!!] 当请求结束时，会自动调用[Session::write]。
	 *
	 * @param   string  $type   会话类型（native、cookie或其他）
	 * @param   string  $id     会话标识符
	 * @return  Session
	 */
	public static function instance($type = NULL, $id = NULL)
	{
		if ($type === NULL)
		{
			// 使用默认驱动
			$type = Session::$default;
		}

		if ( ! isset(Session::$instances[$type]))
		{
			// 加载对应驱动的配置
			$config = Kohana::config('Session')->get($type);

			$class = 'Session_'.ucfirst($type);
			Session::$instances[$type] = $session = new $class($config, $id);

			// Write the session at shutdown
			register_shutdown_function(array($session, 'write'));
		}

		return Session::$instances[$type];
	}

	/**
	 * @var  string  cookie名
	 */
	protected $_name = 'session';

	/**
	 * @var  int  cookie生命周期
	 */
	protected $_lifetime = 0;

	/**
	 * @var  bool  encrypt session data?
	 */
	protected $_encrypted = FALSE;

	/**
	 * @var  array  会话数据
	 */
	protected $_data = array();

	/**
	 * @var  bool  会话销毁了？
	 */
	protected $_destroyed = FALSE;

	/**
	 * 构造函数，重载各种配置
	 *
	 * [!!] 会话类只能通过[Session::instance]来创建
	 *
	 * @param   array   $config 配置
	 * @param   string  $id     会话ID
	 * @return  void
	 */
	public function __construct(array $config = NULL, $id = NULL)
	{
		if (isset($config['name']))
		{
			// 用于保存session_id的cookie名
			$this->_name = (string) $config['name'];
		}

		if (isset($config['lifetime']))
		{
			// Cookie生命期
			$this->_lifetime = (int) $config['lifetime'];
		}

		if (isset($config['encrypted']))
		{
			if ($config['encrypted'] === TRUE)
			{
				// 使用默认的Encrypt实例
				$config['encrypted'] = 'default';
			}

			// 是否加密数据
			$this->_encrypted = $config['encrypted'];
		}

		// 加载会话
		$this->read($id);
	}

	/**
	 * 返回会话数据的序列化字符串
	 * 如果选择了数据加密的话，数据将加密之后才被序列化
	 *
	 *     echo $session;
	 *
	 * @return  string
	 */
	public function __toString()
	{
		// Serialize the data array
		$data = $this->_serialize($this->_data);
		$data = $this->_encrypted
			? Encrypt::instance($this->_encrypted)->encode($data) // 加密数据
			: $this->_encode($data); // 直接ENCODE数据

		return $data;
	}

	/**
	 * 返回当前的会话数据数组，注意这里返回的是引用
	 *
	 *     // 获取当前会话数据的拷贝
	 *     $data = $session->as_array();
	 *
	 *     // 获取引用，注意跟上面的区别
	 *     $data =& $session->as_array();
	 *
	 * @return  array
	 */
	public function & as_array()
	{
		return $this->_data;
	}

	/**
	 * 获取当前会话ID
	 *
	 *     $id = $session->id();
	 *
	 * [!!] 不是所有的session类型都有id的
	 *
	 * @return  string
	 */
	public function id()
	{
		return NULL;
	}

	/**
	 * 获取当前会话的cookie名
	 *
	 *     $name = $session->name();
	 *
	 * @return  string
	 */
	public function name()
	{
		return $this->_name;
	}

	/**
	 * 获取会话数据
	 *
	 *     $foo = $session->get('foo');
	 *
	 * @param	string	$key	变量名
	 * @param	mixed	$default	返回默认值
	 * @return	mixed
	 */
	public function get($key, $default = NULL)
	{
		return array_key_exists($key, $this->_data) ? $this->_data[$key] : $default;
	}

	/**
	 * 获取并删除一个会话数据，相当于flash数据
	 *
	 *     $bar = $session->get_once('bar');
	 *
	 * @param	string	$key	变量名
	 * @param	mixed	$default	返回默认值
	 * @return	mixed
	 */
	public function get_once($key, $default = NULL)
	{
		$value = $this->get($key, $default);
		unset($this->_data[$key]);
		return $value;
	}

	/**
	 * 设置会话数据
	 *
	 *     $session->set('foo', 'bar');
	 *
	 * @param   string  $key    变量名
	 * @param   mixed   $value  值
	 * @return  $this
	 */
	public function set($key, $value)
	{
		$this->_data[$key] = $value;
		return $this;
	}

	/**
	 * 绑定数据（引用）
	 *
	 *     $session->bind('foo', $foo);
	 *
	 * @param   string  $key    变量名
	 * @param   mixed   $value  引用值
	 * @return  $this
	 */
	public function bind($key, & $value)
	{
		$this->_data[$key] =& $value;
		return $this;
	}

	/**
	 * 从会话中删除指定数据
	 *
	 *     $session->delete('foo');
	 *
	 * @param   string  $key,...    variable name
	 * @return  $this
	 */
	public function delete($key)
	{
		$args = func_get_args();
		foreach ($args AS $key)
		{
			unset($this->_data[$key]);
		}

		return $this;
	}

	/**
	 * 读取当前会话数据
	 *
	 *     $session->read();
	 *
	 * @param   string  $id  会话ID
	 * @return  void
	 */
	public function read($id = NULL)
	{
		$data = NULL;

		try
		{
			if (is_string($data = $this->_read($id)))
			{
				$data = $this->_encrypted
					? Encrypt::instance($this->_encrypted)->decode($data) // 使用默认key来解密数据
					: $this->_decode($data); // DECODE数据
				// 反序列化数据啦
				$data = $this->_unserialize($data);
			}
			else
			{
					// 会话数据可能存在，但是可能被破坏或者被更改
			}
		}
		catch (Exception $e)
		{
			// 读取数据时出错啦
			if (Session::$halt)
			{
				throw new Session_Exception('Error reading session data.', NULL, Session_Exception::SESSION_CORRUPT);
			}
			else
			{
				// 注销会话
				$this->destroy();
				Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e))->write();
				return;
			}
		}

		if (is_array($data))
		{
			// 加载数据
			$this->_data = $data;
		}
	}

	/**
	 * 生成一个新的会话ID并返回
	 *
	 *     $id = $session->regenerate();
	 *
	 * @return  string
	 */
	public function regenerate()
	{
		return $this->_regenerate();
	}

	/**
	 * 保存会话数据，同时更新`last_active`时间戳
	 *
	 *     $session->write();
	 *
	 * [!!] 在写会话数据的时候如果出错，系统不会提示错误，但是依然会记录
	 * 因为那个时候，缓冲区的数据已经输出了，再输出可能会有未知的错误
	 *
	 * @return  boolean
	 */
	public function write()
	{
		// 参考上面的注释内容
		if (headers_sent() OR $this->_destroyed)
		{
			return FALSE;
		}
		$this->_data['last_active'] = time();

		try
		{
			return $this->_write();
		}
		catch (Exception $e)
		{
			// 只记录错误，不再抛出这个异常
			Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e))->write();
			return FALSE;
		}
	}

	/**
	 * 注销会话
	 *
	 *     $success = $session->destroy();
	 *
	 * @return  boolean
	 */
	public function destroy()
	{
		if ($this->_destroyed === FALSE)
		{
			if ($this->_destroyed = $this->_destroy())
			{
				// 删除会话数据，同时把对象中的已有数据也清空
				$this->_data = array();
			}
		}

		return $this->_destroyed;
	}

	/**
	 * 重启会话
	 *
	 *     $success = $session->restart();
	 *
	 * @return  boolean
	 */
	public function restart()
	{
		// 清空当前会话
		if ($this->_destroyed === FALSE)
		{
			$this->destroy();
		}
		$this->_destroyed = FALSE;

		return $this->_restart();
	}

	/**
	 * 序列化会话数据
	 *
	 * @param	array	$data	数据
	 * @return	string
	 */
	protected function _serialize($data)
	{
		return serialize($data);
	}

	/**
	 * 反序列化会话数据
	 *
	 * @param   string  $data  数据
	 * @return  array
	 */
	protected function _unserialize($data)
	{
		return unserialize($data);
	}

	/**
	 * 使用[base64_encode]来加密数据内容，有没有其他更好的方法呢？
	 *
	 * @param   string  $data  数据
	 * @return  string
	 */
	protected function _encode($data)
	{
		return base64_encode($data);
	}

	/**
	 * 使用[base64_decode]来解密数据
	 *
	 * @param   string  $data  data
	 * @return  string
	 */
	protected function _decode($data)
	{
		return base64_decode($data);
	}

	/**
	 * 加载最原始的会话数据字符串
	 *
	 * @param   string  $id  会话ID
	 * @return  string
	 */
	abstract protected function _read($id = NULL);

	/**
	 * 生成一个新的会话ID
	 *
	 * @return  string
	 */
	abstract protected function _regenerate();

	/**
	 * 保存会话（写文件）
	 *
	 * @return  boolean
	 */
	abstract protected function _write();

	/**
	 * 注销当前会话
	 *
	 * @return  boolean
	 */
	abstract protected function _destroy();

	/**
	 * 重新开始一个新会话
	 *
	 * @return  boolean
	 */
	abstract protected function _restart();

} // End Session

