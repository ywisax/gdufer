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
class Twig_Node_Expression_Binary_BitwiseXor extends Twig_Node_Expression_Binary {

	public function operator(Twig_Compiler $compiler)
	{
		return $compiler->raw('^');
	}
}
