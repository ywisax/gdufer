<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 基础扩展类
 *
 * @package    Kohana/Twig
 * @category   Extension
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
abstract class Twig_Extension {

	/**
	 * Initializes the runtime environment.
	 *
	 * This is where you can load some file that contains filter functions for instance.
	 *
	 * @param Twig_Environment $environment The current Twig_Environment instance
	 */
	public function init_runtime(Twig_Environment $environment)
	{
	}

	/**
	 * Returns the token parser instances to add to the existing list.
	 *
	 * @return array An array of Twig_Token_Parser or Twig_Token_Parser_Broker instances
	 */
	public function get_token_parsers()
	{
		return array();
	}

	/**
	 * Returns the node visitor instances to add to the existing list.
	 *
	 * @return array An array of Twig_Node_Visitor instances
	 */
	public function get_node_visitors()
	{
		return array();
	}

	/**
	 * Returns a list of filters to add to the existing list.
	 *
	 * @return array An array of filters
	 */
	public function get_filters()
	{
		return array();
	}

	/**
	 * Returns a list of tests to add to the existing list.
	 *
	 * @return array An array of tests
	 */
	public function getTests()
	{
		return array();
	}

	/**
	 * Returns a list of functions to add to the existing list.
	 *
	 * @return array An array of functions
	 */
	public function get_functions()
	{
		return array();
	}

	/**
	 * Returns a list of operators to add to the existing list.
	 *
	 * @return array An array of operators
	 */
	public function get_operators()
	{
		return array();
	}

	/**
	 * Returns a list of global variables to add to the existing list.
	 *
	 * @return array An array of global variables
	 */
	public function getGlobals()
	{
		return array();
	}

	/**
	 * Marks a variable as being safe.
	 *
	 * @param string $string A PHP variable
	 */
	public static function raw($string)
	{
		return $string;
	}
}
