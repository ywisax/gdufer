<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Returns the value or the default value when it is undefined or empty.
 *
 * <pre>
 *  {{ var.foo|default('foo item on var is not defined') }}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_Filter_Default extends Twig_Node_Expression_Filter {

	public function __construct(Twig_Node $node, Twig_Node_Expression_Constant $filterName, Twig_Node $arguments, $lineno, $tag = NULL)
	{
		$default = new Twig_Node_Expression_Filter($node, new Twig_Node_Expression_Constant('default', $node->getLine()), $arguments, $node->getLine());

		if ('default' === $filterName->get_attribute('value') && ($node instanceof Twig_Node_Expression_Name || $node instanceof Twig_Node_Expression_GetAttr))
		{
			$test = new Twig_Node_Expression_Test_Defined(clone $node, 'defined', new Twig_Node(), $node->getLine());
			$false = count($arguments) ? $arguments->getNode(0) : new Twig_Node_Expression_Constant('', $node->getLine());

			$node = new Twig_Node_Expression_Conditional($test, $default, $false, $node->getLine());
		}
		else
		{
			$node = $default;
		}

		parent::__construct($node, $filterName, $arguments, $lineno, $tag);
	}

	public function compile(Twig_Compiler $compiler)
	{
		$compiler->subcompile($this->getNode('node'));
	}
}
