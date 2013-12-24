<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 扩展类调试相关
 *
 * @package    Kohana/Twig
 * @category   Extension
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Extension_Debug extends Twig_Extension {

	/**
	 * Returns a list of global functions to add to the existing list.
	 *
	 * @return array An array of global functions
	 */
	public function get_functions()
	{
		// dump is safe if var_dump is overridden by xdebug
		$isDumpOutputHtmlSafe = extension_loaded('xdebug')
			// false means that it was not set (and the default is on) or it explicitly enabled
			&& (FALSE === ini_get('xdebug.overload_var_dump') || ini_get('xdebug.overload_var_dump'))
			// false means that it was not set (and the default is on) or it explicitly enabled
			// xdebug.overload_var_dump produces HTML only when html_errors is also enabled
			&& (FALSE === ini_get('html_errors') || ini_get('html_errors'))
			|| 'cli' === php_sapi_name();

		return array(
			new Twig_Simple_Function('dump', 'Twig_Extension_Debug::dump', array(
				'is_safe' => $isDumpOutputHtmlSafe ? array('html') : array(),
				'needs_context' => TRUE,
				'needs_environment' => TRUE
				)),
		);
	}

	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName()
	{
		return 'debug';
	}

	/**
	 * 调试，输出变量，原来叫twig_var_dump
	 */
	public static function dump(Twig_Environment $env, $context)
	{
		if ( ! $env->isDebug())
		{
			return;
		}
		// 使用Kohana自带的调试工具
		return Debug::vars($context);
	}
}
