<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents an import node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Import extends Twig_Node {

	public function __construct(Twig_Node_Expression $expr, Twig_Node_Expression $var, $lineno, $tag = NULL)
	{
		parent::__construct(array('expr' => $expr, 'var' => $var), array(), $lineno, $tag);
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
			->write('')
			->subcompile($this->getNode('var'))
			->raw(' = ')
		;

		if ($this->getNode('expr') instanceof Twig_Node_Expression_Name && '_self' === $this->getNode('expr')->get_attribute('name'))
		{
			$compiler->raw("\$this");
		}
		else
		{
			$compiler
				->raw('$this->env->load_template(')
				->subcompile($this->getNode('expr'))
				->raw(")");
		}

		$compiler->raw(";\n");
	}
}
