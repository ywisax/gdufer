<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Internal class. This class is used by Twig_Environment as a staging area and must not be used directly.
 *
 * @package    Kohana/Twig
 * @category   Extension
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Extension_Staging extends Twig_Extension
{
	protected $functions = array();
	protected $filters = array();
	protected $visitors = array();
	protected $tokenParsers = array();
	protected $globals = array();
	protected $tests = array();

	public function addFunction($name, $function)
	{
		$this->functions[$name] = $function;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_functions()
	{
		return $this->functions;
	}

	public function addFilter($name, $filter)
	{
		$this->filters[$name] = $filter;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_filters()
	{
		return $this->filters;
	}

	public function add_node_visitor(Twig_Node_Visitor $visitor)
	{
		$this->visitors[] = $visitor;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_node_visitors()
	{
		return $this->visitors;
	}

	public function addTokenParser(Twig_Token_Parser $parser)
	{
		$this->tokenParsers[] = $parser;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_token_parsers()
	{
		return $this->tokenParsers;
	}

	public function addGlobal($name, $value)
	{
		$this->globals[$name] = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getGlobals()
	{
		return $this->globals;
	}

	public function addTest($name, $test)
	{
		$this->tests[$name] = $test;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTests()
	{
		return $this->tests;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'staging';
	}
}
