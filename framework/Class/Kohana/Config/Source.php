<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Base Config source Interface. Used to identify either config readers or writers when calling [Kohana_Config::attach()]
 *
 * @package    Kohana
 * @category   Configuration
 */
interface Kohana_Config_Source {
	//	public function load($group);
	//	public function write($group, $key, $value);
	//	public function delete($group, $key);
}
