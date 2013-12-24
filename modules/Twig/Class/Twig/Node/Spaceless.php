<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a spaceless node. It removes spaces between HTML tags.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Spaceless extends Twig_Node {

	/**
	 * 构造函数
	 */
	public function __construct(Twig_Node $body, $lineno, $tag = 'spaceless')
	{
		parent::__construct(array('body' => $body), array(), $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->debug_info($this)
			->write("ob_start();\n")
			->subcompile($this->getNode('body'))
			->write("echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));\n");
	}
}
