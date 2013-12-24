<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 配置读取器的接口类，Specifies the methods that a config writer must implement
 *
 * @package    Kohana
 * @category   Configuration
 */
abstract class Kohana_Config_Writer implements Kohana_Config_Source {

	/**
	 * Writes the passed config for $group
	 *
	 * Returns chainable instance on success or throws 
	 * Kohana_Config_Exception on failure
	 *
	 * @param string      $group  The config group
	 * @param string      $key    The config key to write to
	 * @param array       $config The configuration to write
	 * @return boolean
	 */
	public function write($group, $key, $config);
}
