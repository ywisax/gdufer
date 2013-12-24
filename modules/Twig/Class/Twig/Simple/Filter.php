<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a template filter.
 *
 * @package    Kohana/Twig
 * @category   Simple
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Simple_Filter {

	protected $name;
	protected $callable;
	protected $options;
	protected $arguments = array();

	public function __construct($name, $callable, array $options = array())
	{
		$this->name = $name;
		$this->callable = $callable;
		$this->options = array_merge(array(
			'needs_environment' => FALSE,
			'needs_context'     => FALSE,
			'is_safe'           => NULL,
			'is_safe_callback'  => NULL,
			'pre_escape'        => NULL,
			'preserves_safety'  => NULL,
			'node_class'        => 'Twig_Node_Expression_Filter',
		), $options);
	}

	public function getName()
	{
		return $this->name;
	}

	public function getCallable()
	{
		return $this->callable;
	}

	public function get_node_class()
	{
		return $this->options['node_class'];
	}

	public function setArguments($arguments)
	{
		$this->arguments = $arguments;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	public function needsEnvironment()
	{
		return $this->options['needs_environment'];
	}

	public function needsContext()
	{
		return $this->options['needs_context'];
	}

	public function get_safe(Twig_Node $filterArgs)
	{
		if (null !== $this->options['is_safe'])
		{
			return $this->options['is_safe'];
		}

		if (null !== $this->options['is_safe_callback'])
		{
			return call_user_func($this->options['is_safe_callback'], $filterArgs);
		}
	}

	public function getPreservesSafety()
	{
		return $this->options['preserves_safety'];
	}

	public function getPreEscape()
	{
		return $this->options['pre_escape'];
	}
}
