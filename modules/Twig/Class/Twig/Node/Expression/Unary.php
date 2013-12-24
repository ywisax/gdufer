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
abstract class Twig_Node_Expression_Unary extends Twig_Node_Expression {

	public function __construct(Twig_Node $node, $lineno)
	{
		parent::__construct(array('node' => $node), array(), $lineno);
	}

	public function compile(Twig_Compiler $compiler)
	{
		$compiler->raw('(');
		$this->operator($compiler);
		$compiler
			->subcompile($this->getNode('node'))
			->raw(')')
		;
	}

	abstract public function operator(Twig_Compiler $compiler);
}
