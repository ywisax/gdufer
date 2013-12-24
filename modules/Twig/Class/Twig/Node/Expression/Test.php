<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * XXXXX
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_Test extends Twig_Node_Expression_Call {

	public function __construct(Twig_Node $node, $name, Twig_Node $arguments = NULL, $lineno)
	{
		parent::__construct(array('node' => $node, 'arguments' => $arguments), array('name' => $name), $lineno);
	}

	public function compile(Twig_Compiler $compiler)
	{
		$name = $this->get_attribute('name');
		$test = $compiler->get_environment()->getTest($name);

		$this->set_attribute('name', $name);
		$this->set_attribute('type', 'test');
		$this->set_attribute('thing', $test);
		if ($test instanceof Twig_Test || $test instanceof Twig_Simple_Test)
		{
			$this->set_attribute('callable', $test->getCallable());
		}

		$this->compileCallable($compiler);
	}
}
