<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * The group wrapper acts as an interface to all the config directives
 * gathered from across the system.
 *
 * This is the object returned from Kohana_Config::load 
 *
 * Any modifications to configuration items should be done through an instance of this object
 *
 * @package    Kohana
 * @category   Configuration
 */
class Kohana_Config_Group extends ArrayObject {

	/**
	 * Reference the config object that created this group
	 * Used when updating config
	 * @var Kohana_Config
	 */
	protected $_parent_instance = NULL;

	/**
	 * The group this config is for
	 * Used when updating config items
	 * @var string
	 */
	protected $_group_name = '';

	/**
	 * Constructs the group object.  Kohana_Config passes the config group 
	 * and its config items to the object here.
	 *
	 * @param Kohana_Config  $instance "Owning" instance of Kohana_Config
	 * @param string         $group    The group name
	 * @param array          $config   Group's config
	 */
	public function __construct(Kohana_Config $instance, $group, array $config = array())
	{
		$this->_parent_instance = $instance;
		$this->_group_name      = $group;

		parent::__construct($config, ArrayObject::ARRAY_AS_PROPS);
	}

	/**
	 * Return the current group in serialized form.
	 *
	 *     echo $config;
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return serialize($this->getArrayCopy());
	}

	/**
	 * Alias for getArrayCopy()
	 *
	 * @return array Array copy of the group's config
	 */
	public function as_array()
	{
		return $this->getArrayCopy();
	}

	/**
	 * 返回当前配置组名
	 *
	 * @return string  组名
	 */
	public function group_name()
	{
		return $this->_group_name;
	}
	
	/**
	 * Get a variable from the configuration or return the default value.
	 *
	 *     $value = $config->get($key);
	 *
	 * @param   string  $key        数组键名
	 * @param   mixed   $default    默认值
	 * @return  mixed
	 */
	public function get($key, $default = NULL)
	{
		return $this->offsetExists($key) ? $this->offsetGet($key) : $default;
	}

	/**
	 * Sets a value in the configuration array.
	 *
	 *     $config->set($key, $new_value);
	 *
	 * @param   string  $key    array key
	 * @param   mixed   $value  array value
	 * @return  $this
	 */
	public function set($key, $value)
	{
		$this->offsetSet($key, $value);

		return $this;
	}

	/**
	 * 重载ArrayObject::offsetSet()方法。
	 * 当配置被更改时，会自动调用该方法：
	 *
	 *     $config->var = 'asd';
	 *
	 *     // 或者
	 *
	 *     $config['var'] = 'asd';
	 *
	 * @param string $key   The key of the config item we're changing
	 * @param mixed  $value The new array value
	 */
	public function offsetSet($key, $value)
	{
		$this->_parent_instance->_write_config($this->_group_name, $key, $value);

		return parent::offsetSet($key, $value);
	}
}
