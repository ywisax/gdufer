<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Checks if a variable is divisible by a number.
 *
 * <pre>
 *  {% if loop.index is divisibleby(3) %}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_Test_Divisibleby extends Twig_Node_Expression_Test
{
	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->raw('(0 == ')
			->subcompile($this->getNode('node'))
			->raw(' % ')
			->subcompile($this->getNode('arguments')->getNode(0))
			->raw(')')
		;
	}
}
