<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Checks if a number is even.
 *
 * <pre>
 *  {{ var is even }}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_Test_Even extends Twig_Node_Expression_Test {

	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->raw('(')
			->subcompile($this->getNode('node'))
			->raw(' % 2 == 0')
			->raw(')');
	}
}
