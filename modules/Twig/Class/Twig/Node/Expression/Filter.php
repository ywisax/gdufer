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
class Twig_Node_Expression_Filter extends Twig_Node_Expression_Call {

	public function __construct(Twig_Node $node, Twig_Node_Expression_Constant $filterName, Twig_Node $arguments, $lineno, $tag = NULL)
	{
		parent::__construct(array('node' => $node, 'filter' => $filterName, 'arguments' => $arguments), array(), $lineno, $tag);
	}

	public function compile(Twig_Compiler $compiler)
	{
		$name = $this->getNode('filter')->get_attribute('value');
		$filter = $compiler->get_environment()->getFilter($name);

		$this->set_attribute('name', $name);
		$this->set_attribute('type', 'filter');
		$this->set_attribute('thing', $filter);
		$this->set_attribute('needs_environment', $filter->needsEnvironment());
		$this->set_attribute('needs_context', $filter->needsContext());
		$this->set_attribute('arguments', $filter->getArguments());
		if ($filter instanceof Twig_Filter || $filter instanceof Twig_Simple_Filter)
		{
			$this->set_attribute('callable', $filter->getCallable());
		}

		$this->compileCallable($compiler);
	}
}
