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
class Twig_Node_Expression_Function extends Twig_Node_Expression_Call {

	public function __construct($name, Twig_Node $arguments, $lineno)
	{
		parent::__construct(array('arguments' => $arguments), array('name' => $name), $lineno);
	}

	public function compile(Twig_Compiler $compiler)
	{
		$name = $this->get_attribute('name');
		$function = $compiler->get_environment()->getFunction($name);

		$this->set_attribute('name', $name);
		$this->set_attribute('type', 'function');
		$this->set_attribute('thing', $function);
		$this->set_attribute('needs_environment', $function->needsEnvironment());
		$this->set_attribute('needs_context', $function->needsContext());
		$this->set_attribute('arguments', $function->getArguments());
		if ($function instanceof Twig_Function || $function instanceof Twig_Simple_Function)
		{
			$this->set_attribute('callable', $function->getCallable());
		}

		$this->compileCallable($compiler);
	}
}
