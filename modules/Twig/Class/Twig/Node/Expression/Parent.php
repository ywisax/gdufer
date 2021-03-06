<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a parent node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_Parent extends Twig_Node_Expression {

	public function __construct($name, $lineno, $tag = NULL)
	{
		parent::__construct(array(), array('output' => FALSE, 'name' => $name), $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		if ($this->get_attribute('output'))
		{
			$compiler
				->debug_info($this)
				->write("\$this->display_parent_block(")
				->string($this->get_attribute('name'))
				->raw(", \$context, \$blocks);\n");
		}
		else
		{
			$compiler
				->raw("\$this->render_parent_block(")
				->string($this->get_attribute('name'))
				->raw(", \$context, \$blocks)");
		}
	}
}
