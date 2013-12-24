<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Twig_Node_SandboxedPrint adds a check for the __toString() method
 * when the variable is an object and the sandbox is activated.
 *
 * When there is a simple Print statement, like {{ article }},
 * and if the sandbox is enabled, we need to check that the __toString()
 * method is allowed if 'article' is an object.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_SandboxedPrint extends Twig_Node_Print
{
	public function __construct(Twig_Node_Expression $expr, $lineno, $tag = NULL)
	{
		parent::__construct($expr, $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->debug_info($this)
			->write('echo $this->env->getExtension(\'sandbox\')->ensureToStringAllowed(')
			->subcompile($this->getNode('expr'))
			->raw(");\n");
	}

	/**
	 * Removes node filters.
	 *
	 * This is mostly needed when another visitor adds filters (like the escaper one).
	 *
	 * @param Twig_Node $node A Node
	 */
	protected function removeNodeFilter($node)
	{
		if ($node instanceof Twig_Node_Expression_Filter)
		{
			return $this->removeNodeFilter($node->getNode('node'));
		}

		return $node;
	}
}
