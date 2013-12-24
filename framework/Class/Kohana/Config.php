<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 配置基类，用来存放和控制配置读写器。
 * Kohana的配置系统十分巧妙利用了级联文件系统的特性，赞一个。
 *
 * @package    Kohana
 * @category   Configuration
 */
class Kohana_Config {

	/**
	 * @var  array  配置读取器
	 */
	protected $_sources = array();

	/**
	 * @var  array  查找到的配置组信息
	 */
	protected $_groups = array();

	/**
	 * 附加一个读取器，读取器位置由第二个参数决定
	 *
	 *     $config->attach($reader);        // 放在开头
	 *     $config->attach($reader, FALSE); // 放在结尾
	 *
	 * @param   Kohana_Config_Source    $source 实例对象
	 * @param   boolean                 $first  是否放在开头
	 * @return  $this
	 */
	public function attach(Kohana_Config_Source $source, $first = TRUE)
	{
		if ($first)
		{
			// 把当前日志读取器放在第一位
			array_unshift($this->_sources, $source);
		}
		else
		{
			// 默认把当前日志读取器放在最后一位
			$this->_sources[] = $source;
		}

		// 清空已经缓存的_groups
		$this->groups(array());
		return $this;
	}

	/**
	 * Detach a configuration reader.
	 *
	 *     $config->detach($reader);
	 *
	 * @param   Kohana_Config_Source    $source instance
	 * @return  $this
	 */
	public function detach(Kohana_Config_Source $source)
	{
		if (($key = array_search($source, $this->_sources)) !== FALSE)
		{
			// Remove the writer
			unset($this->_sources[$key]);
		}

		return $this;
	}

	/**
	 * Load a configuration group. Searches all the config sources, merging all the 
	 * directives found into a single config group.  Any changes made to the config 
	 * in this group will be mirrored across all writable sources.  
	 *
	 *     $array = $config->load($name);
	 *
	 * See [Kohana_Config_Group] for more info
	 *
	 * @param   string  $group  configuration group name
	 * @return  Kohana_Config_Group
	 */
	public function load($group)
	{
		if ( ! count($this->_sources))
		{
			throw new Kohana_Exception('No configuration sources attached');
		}

		if (empty($group))
		{
			throw new Kohana_Exception("Need to specify a config group");
		}

		if ( ! is_string($group))
		{
			throw new Kohana_Exception("Config group must be a string");
		}

		if (strpos($group, '.') !== FALSE)
		{
			// Split the config group and path
			list($group, $path) = explode('.', $group, 2);
		}

		if (isset($this->_groups[$group]))
		{
			if (isset($path))
			{
				return Helper_Array::path($this->_groups[$group], $path, NULL, '.');
			}
			return $this->_groups[$group];
		}

		$config = array();

		// We search from the "lowest" source and work our way up
		$sources = array_reverse($this->_sources);

		foreach ($sources AS $source)
		{
			if ($source instanceof Kohana_Config_Reader)
			{
				if ($source_config = $source->load($group))
				{
					$config = Helper_Array::merge($config, $source_config);
				}
			}
		}

		$this->_groups[$group] = new Config_Group($this, $group, $config);

		if (isset($path))
		{
			return Helper_Array::path($config, $path, NULL, '.');
		}

		return $this->_groups[$group];
	}
	
	/**
	 * 读取或设置group信息
	 *
	 * @param  array  要设置和替换的配置信息
	 */
	public function groups($groups = NULL)
	{
		if ($groups === NULL)
		{
			return $this->_groups;
		}
		$this->_groups = $groups;
	}

	/**
	 * Copy one configuration group to all of the other writers.
	 * 
	 *     $config->copy($name);
	 *
	 * @param   string  $group  configuration group name
	 * @return  $this
	 */
	public function copy($group)
	{
		// Load the configuration group
		$config = $this->load($group);

		foreach ($config->as_array() AS $key => $value)
		{
			$this->_write_config($group, $key, $value);
		}

		return $this;
	}
	
	/**
	 * Deletes a config item
	 *
	 * @param    string       config group
	 * @param    string       config key
	 * @return boolean
	 */
	public static function delete($group, $key)
	{
		$status = TRUE;
		foreach (self::$_sources AS $source)
		{
			$status = $source->delete($group, $key);
		}
		
		return $status;
	}

	/**
	 * Callback used by the config group to store changes made to configuration
	 *
	 * @param string    $group  Group name
	 * @param string    $key    Variable name
	 * @param mixed     $value  The new value
	 * @return Kohana_Config Chainable instance
	 */
	public function _write_config($group, $key, $value)
	{
		foreach ($this->_sources AS $source)
		{
			if ( ! ($source instanceof Kohana_Config_Writer))
			{
				continue;
			}
			
			// Copy each value in the config
			$source->write($group, $key, $value);
		}

		return $this;
	}

} // End Kohana_Config
