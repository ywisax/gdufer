<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Checks if a variable is defined in the current context.
 *
 * <pre>
 * {# defined works with variable names and variable attributes #}
 * {% if foo is defined %}
 *     {# ... #}
 * {% endif %}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_Test_Defined extends Twig_Node_Expression_Test
{
	public function __construct(Twig_Node $node, $name, Twig_Node $arguments = NULL, $lineno)
	{
		parent::__construct($node, $name, $arguments, $lineno);

		if ($node instanceof Twig_Node_Expression_Name)
		{
			$node->set_attribute('is_defined_test', TRUE);
		}
		elseif ($node instanceof Twig_Node_Expression_GetAttr)
		{
			$node->set_attribute('is_defined_test', TRUE);

			$this->changeIgnoreStrictCheck($node);
		}
		else
		{
			throw new Twig_Exception_Syntax('The "defined" test only works with simple variables', $this->getLine());
		}
	}

	protected function changeIgnoreStrictCheck(Twig_Node_Expression_GetAttr $node)
	{
		$node->set_attribute('ignore_strict_check', TRUE);

		if ($node->getNode('node') instanceof Twig_Node_Expression_GetAttr)
		{
			$this->changeIgnoreStrictCheck($node->getNode('node'));
		}
	}

	public function compile(Twig_Compiler $compiler)
	{
		$compiler->subcompile($this->getNode('node'));
	}
}
