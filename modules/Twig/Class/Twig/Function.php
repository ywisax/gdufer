<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a template function. Use Twig_Simple_Function instead.
 *
 * @package    Kohana/Twig
 * @category   Function
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
abstract class Twig_Function {

	protected $options;
	protected $arguments = array();

	public function __construct(array $options = array())
	{
		$this->options = array_merge(array(
			'needs_environment' => FALSE,
			'needs_context'     => FALSE,
			'callable'          => NULL,
		), $options);
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

	public function get_safe(Twig_Node $functionArgs)
	{
		if (isset($this->options['is_safe']))
		{
			return $this->options['is_safe'];
		}

		if (isset($this->options['is_safe_callback']))
		{
			return call_user_func($this->options['is_safe_callback'], $functionArgs);
		}

		return array();
	}

	public function getCallable()
	{
		return $this->options['callable'];
	}
}
