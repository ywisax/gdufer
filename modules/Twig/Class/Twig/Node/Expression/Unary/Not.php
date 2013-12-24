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
class Twig_Node_Expression_Unary_Not extends Twig_Node_Expression_Unary {

	public function operator(Twig_Compiler $compiler)
	{
		$compiler->raw('!');
	}
}
