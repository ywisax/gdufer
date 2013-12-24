<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * XXXX
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_Binary_In extends Twig_Node_Expression_Binary {

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->raw('Twig_Filter_Helper::in(')
			->subcompile($this->getNode('left'))
			->raw(', ')
			->subcompile($this->getNode('right'))
			->raw(')');
	}

	public function operator(Twig_Compiler $compiler)
	{
		return $compiler->raw('in');
	}
}
