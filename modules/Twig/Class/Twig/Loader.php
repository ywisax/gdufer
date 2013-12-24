<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 模板加载器接口
 *
 * @package    Kohana/Twig
 * @category   Loader
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
abstract class Twig_Loader {

	/**
	 * Gets the source code of a template, given its name.
	 *
	 * @param string $name The name of the template to load
	 *
	 * @return string The template source code
	 *
	 * @throws Twig_Exception_Loader When $name is not found
	 */
	abstract public function get_source($name);

	/**
	 * Gets the cache key to use for the cache for a given template name.
	 *
	 * @param string $name The name of the template to load
	 *
	 * @return string The cache key
	 *
	 * @throws Twig_Exception_Loader When $name is not found
	 */
	abstract public function get_cache_key($name);

	/**
	 * Returns true if the template is still fresh.
	 *
	 * @param string    $name The template name
	 * @param timestamp $time The last modification time of the cached template
	 *
	 * @return Boolean true if the template is fresh, false otherwise
	 *
	 * @throws Twig_Exception_Loader When $name is not found
	 */
	abstract public function is_fresh($name, $time);

	/**
	 * Check if we have the source code of a template, given its name.
	 *
	 * @param string $name The name of the template to check if we can load
	 *
	 * @return boolean If the template source code is handled by this loader or not
	 */
	abstract public function exists($name);
}
