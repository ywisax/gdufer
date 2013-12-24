<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a template function as a node. Use Twig_Simple_Function instead.
 *
 * @package    Kohana/Twig
 * @category   Function
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Function_Node extends Twig_Function {

	protected $class;

	public function __construct($class, array $options = array())
	{
		parent::__construct($options);

		$this->class = $class;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function compile()
	{
	}
}
