<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 基于文件的配置读取器。
 *
 * @package    Kohana
 * @category   Configuration
 */
class Kohana_Config_Reader_File extends Kohana_Config_Reader {

	/**
	 * 配置文件保存的路径
	 *
	 * @var string
	 */
	protected $_directory = '';

	/**
	 * 使用给定的配置目录来创建一个新的文件配置读取器。
	 *
	 * @param string    $directory  Configuration directory to search
	 */
	public function __construct(array $config = NULL)
	{
		$directory = Helper_Array::get($config, 'directory', 'Config');
		$this->_directory = trim($directory, '/');
	}

	/**
	 * 加载和合并所有同名的配置文件
	 *
	 *     $config->load($name);
	 *
	 * @param   string  $group  配置组名
	 * @return  $this   当前对象
	 */
	public function load($group)
	{
		$config = array();

		if ($files = Kohana::find_file($this->_directory, $group, NULL, TRUE))
		{
			foreach ($files AS $file)
			{
				// 合并每个文件的配置
				$config = Helper_Array::merge($config, Kohana::load($file));
			}
		}

		return $config;
	}
	
	/**
	 * Writes the passed config for $group
	 *
	 * @param   string  $group  The config group
	 * @param   string  $key    The config key to write to
	 * @param   array   $config The configuration to write
	 * @return  boolean
	 */
	public function write($group, $key, $config)
	{
		//always return true
		return TRUE;
	}
	
	/**
	 * Delete the config item from config
	 *
	 * @param   string  $group  The config group
	 * @param   string  $key    The config key to delete
	 * @return  boolean
	 */
	public function delete($group, $key)
	{
		return TRUE;
	}

} // Kohana_Config_Reader_File 完成
