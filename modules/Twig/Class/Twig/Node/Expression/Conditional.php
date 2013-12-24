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
class Twig_Node_Expression_Conditional extends Twig_Node_Expression {

	public function __construct(Twig_Node_Expression $expr1, Twig_Node_Expression $expr2, Twig_Node_Expression $expr3, $lineno)
	{
		parent::__construct(array('expr1' => $expr1, 'expr2' => $expr2, 'expr3' => $expr3), array(), $lineno);
	}

	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->raw('((')
			->subcompile($this->getNode('expr1'))
			->raw(') ? (')
			->subcompile($this->getNode('expr2'))
			->raw(') : (')
			->subcompile($this->getNode('expr3'))
			->raw('))');
	}
}
