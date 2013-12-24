<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Checks that a variable is null.
 *
 * <pre>
 *  {{ var is none }}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_Test_Null extends Twig_Node_Expression_Test
{
	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->raw('(null === ')
			->subcompile($this->getNode('node'))
			->raw(')')
		;
	}
}
