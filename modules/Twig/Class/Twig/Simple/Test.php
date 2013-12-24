<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a template test.
 *
 * @package    Kohana/Twig
 * @category   Simple
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Simple_Test
{
	protected $name;
	protected $callable;
	protected $options;

	public function __construct($name, $callable, array $options = array())
	{
		$this->name = $name;
		$this->callable = $callable;
		$this->options = array_merge(array(
			'node_class' => 'Twig_Node_Expression_Test',
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
}
