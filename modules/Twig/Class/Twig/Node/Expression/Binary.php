<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a block call node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
abstract class Twig_Node_Expression_Binary extends Twig_Node_Expression {

	public function __construct(Twig_Node $left, Twig_Node $right, $lineno)
	{
		parent::__construct(array('left' => $left, 'right' => $right), array(), $lineno);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->raw('(')
			->subcompile($this->getNode('left'))
			->raw(' ');
		$this->operator($compiler);
		$compiler
			->raw(' ')
			->subcompile($this->getNode('right'))
			->raw(')');
	}

	abstract public function operator(Twig_Compiler $compiler);
}
