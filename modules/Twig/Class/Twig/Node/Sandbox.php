<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a sandbox node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Sandbox extends Twig_Node {

	public function __construct(Twig_Node $body, $lineno, $tag = NULL)
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
			->write("\$sandbox = \$this->env->getExtension('sandbox');\n")
			->write("if ( ! \$alreadySandboxed = \$sandbox->isSandboxed()) {\n")
			->indent()
			->write("\$sandbox->enableSandbox();\n")
			->outdent()
			->write("}\n")
			->subcompile($this->getNode('body'))
			->write("if ( ! \$alreadySandboxed) {\n")
			->indent()
			->write("\$sandbox->disableSandbox();\n")
			->outdent()
			->write("}\n");
	}
}
