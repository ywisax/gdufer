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
class Twig_Node_Expression_Constant extends Twig_Node_Expression {

	public function __construct($value, $lineno)
	{
		parent::__construct(array(), array('value' => $value), $lineno);
	}

	public function compile(Twig_Compiler $compiler)
	{
		$compiler->repr($this->get_attribute('value'));
	}
}
