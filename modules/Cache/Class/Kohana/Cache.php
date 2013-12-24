<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Kohana Cache provides a common interface to a variety of caching engines. Tags are
 * supported where available natively to the cache system. Kohana Cache supports multiple
 * instances of cache engines through a grouped singleton pattern.
 *
 * ### 目前支持的缓存类型
 *
 * *  [APC](http://php.net/manual/en/book.apc.php)
 * *  [eAccelerator](http://eaccelerator.net/)
 * *  文件驱动
 * *  [Memcache](http://memcached.org/)
 * *  [Memcached-tags](http://code.google.com/p/memcached-tags/)
 * *  [SQLite](http://www.sqlite.org/)
 * *  [Xcache](http://xcache.lighttpd.net/)
 *
 * ### 缓存介绍
 *
 * Caching should be implemented with consideration. Generally, caching the result of resources
 * is faster than reprocessing them. Choosing what, how and when to cache is vital. PHP APC is
 * presently one of the fastest caching systems available, closely followed by Memcache. SQLite
 * and File caching are two of the slowest cache methods, however usually faster than reprocessing
 * a complex set of instructions.
 *
 * Caching engines that use memory are considerably faster than the file based alternatives. But
 * memory is limited whereas disk space is plentiful. If caching large datasets it is best to use
 * file caching.
 *
 * ### 配置设置
 *
 * Kohana Cache uses configuration groups to create cache instances. A configuration group can
 * use any supported driver, with successive groups using the same driver type if required.
 *
 * #### 配置示例
 *
 * Below is an example of a _memcache_ server configuration.
 *
 *     return array(
 *          'default'       => array(                      // Default group
 *                  'driver'         => 'memcache',        // using Memcache driver
 *                  'servers'        => array(             // Available server definitions
 *                         array(
 *                              'host'       => 'localhost',
 *                              'port'       => 11211,
 *                              'persistent' => FALSE
 *                         )
 *                  ),
 *                  'compression'    => FALSE,             // Use compression?
 *           ),
 *     )
 *
 * In cases where only one cache group is required, if the group is named `default` there is
 * no need to pass the group name when instantiating a cache instance.
 *
 * #### 通用的缓存配置组配置示例
 *
 * Below are the settings available to all types of cache driver.
 *
 * 名称           | 必需     | 描述
 * -------------- | -------- | ---------------------------------------------------------------
 * 驱动           | __YES__  | (_string_) 要使用的驱动类型
 *
 * Details of the settings specific to each driver are available within the drivers documentation.
 *
 * ### 系统需求
 *
 * *  Kohana 3.0.x
 * *  PHP 5.2.4 or greater
 *
 * @package    Kohana/Cache
 * @category   Base
 */
abstract class Kohana_Cache {

	const DEFAULT_EXPIRE = 3600;

	/**
	 * @var   string     默认使用的驱动
	 */
	public static $default = 'memcache';

	/**
	 * @var   Kohana_Cache  实例数组
	 */
	public static $instances = array();

	/**
	 * Creates a singleton of a Kohana Cache group. If no group is supplied
	 * the __default__ cache group is used.
	 *
	 *     // Create an instance of the default group
	 *     $default_group = Cache::instance();
	 *
	 *     // Create an instance of a group
	 *     $foo_group = Cache::instance('foo');
	 *
	 *     // Access an instantiated group directly
	 *     $foo_group = Cache::$instances['default'];
	 *
	 * @param   string  $group  the name of the cache group to use [Optional]
	 * @return  Cache
	 */
	public static function instance($group = NULL)
	{
		// 如果没指定组名
		if ($group === NULL)
		{
			// 使用默认设置
			$group = Cache::$default;
		}

		if (isset(Cache::$instances[$group]))
		{
			return Cache::$instances[$group];
		}

		$config = Kohana::config('Cache');
		if ( ! $config->offsetExists($group))
		{
			throw new Cache_Exception('Failed to load Kohana Cache Group: :group', array(
				':group' => $group,
			));
		}
		$config = $config->get($group);

		$cache_class = 'Cache_'.ucfirst($config['driver']);
		Cache::$instances[$group] = new $cache_class($config);

		return Cache::$instances[$group];
	}

	/**
	 * @var  Config  当前实例的配置信息
	 */
	protected $_config = array();

	/**
	 * Ensures singleton pattern is observed, loads the default expiry
	 *
	 * @param  array  $config  configuration
	 */
	protected function __construct(array $config)
	{
		$this->config($config);
	}

	/**
	 * Getter and setter for the configuration. If no argument provided, the
	 * current configuration is returned. Otherwise the configuration is set
	 * to this class.
	 *
	 *     // Overwrite all configuration
	 *     $cache->config(array('driver' => 'memcache', '...'));
	 *
	 *     // Set a new configuration setting
	 *     $cache->config('servers', array(
	 *          'foo' => 'bar',
	 *          '...'
	 *          ));
	 *
	 *     // Get a configuration setting
	 *     $servers = $cache->config('servers);
	 *
	 * @param   mixed    key to set to array, either array or config path
	 * @param   mixed    value to associate with key
	 * @return  mixed
	 */
	public function config($key = NULL, $value = NULL)
	{
		if ($key === NULL)
			return $this->_config;

		if (is_array($key))
		{
			$this->_config = $key;
		}
		else
		{
			if ($value === NULL)
				return Helper_Array::get($this->_config, $key);

			$this->_config[$key] = $value;
		}

		return $this;
	}

	/**
	 * Overload the __clone() method to prevent cloning
	 *
	 * @return  void
	 */
	final public function __clone()
	{
		throw new Cache_Exception('Cloning of Kohana_Cache objects is forbidden');
	}

	/**
	 * 获取指定的缓存信息
	 *
	 *     // Retrieve cache entry from default group
	 *     $data = Cache::instance()->get('foo');
	 *
	 *     // Retrieve cache entry from default group and return 'bar' if miss
	 *     $data = Cache::instance()->get('foo', 'bar');
	 *
	 *     // Retrieve cache entry from memcache group
	 *     $data = Cache::instance('memcache')->get('foo');
	 *
	 * @param   string  $id       id of cache to entry
	 * @param   string  $default  default value to return if cache miss
	 * @return  mixed
	 */
	abstract public function get($id, $default = NULL);

	/**
	 * 设置和保存缓存
	 *
	 *     $data = 'bar';
	 *
	 *     // Set 'bar' to 'foo' in default group, using default expiry
	 *     Cache::instance()->set('foo', $data);
	 *
	 *     // Set 'bar' to 'foo' in default group for 30 seconds
	 *     Cache::instance()->set('foo', $data, 30);
	 *
	 *     // Set 'bar' to 'foo' in memcache group for 10 minutes
	 *     if (Cache::instance('memcache')->set('foo', $data, 600))
	 *     {
	 *          // Cache was set successfully
	 *          return
	 *     }
	 *
	 * @param   string   $id        id of cache entry
	 * @param   string   $data      data to set to cache
	 * @param   integer  $lifetime  lifetime in seconds
	 * @return  boolean
	 */
	abstract public function set($id, $data, $lifetime = 3600);

	/**
	 * 删除指定的缓存
	 *
	 *     // Delete 'foo' entry from the default group
	 *     Cache::instance()->delete('foo');
	 *
	 *     // Delete 'foo' entry from the memcache group
	 *     Cache::instance('memcache')->delete('foo')
	 *
	 * @param   string  $id  id to remove from cache
	 * @return  boolean
	 */
	abstract public function delete($id);

	/**
	 * 删除所有缓存
	 *
	 * Beware of using this method when
	 * using shared memory cache systems, as it will wipe every
	 * entry within the system for all clients.
	 *
	 *     // Delete all cache entries in the default group
	 *     Cache::instance()->delete_all();
	 *
	 *     // Delete all cache entries in the memcache group
	 *     Cache::instance('memcache')->delete_all();
	 *
	 * @return  boolean
	 */
	abstract public function delete_all();

	protected $_troublesome_characters = array('/', '\\', ' ');
	protected $_troublesome_characters_replace = '_';

	/**
	 * Replaces troublesome characters with underscores.
	 *
	 *     // Sanitize a cache id
	 *     $id = $this->_sanitize_id($id);
	 *
	 * @param   string  $id  id of cache to sanitize
	 * @return  string
	 */
	protected function _sanitize_id($id)
	{
		// Change slashes and spaces to underscores
		return str_replace(
			$this->_troublesome_characters,
			$this->_troublesome_characters_replace,
			$id);
	}
}
// End Kohana_Cache
