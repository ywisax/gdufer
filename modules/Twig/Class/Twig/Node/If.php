<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents an if node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_If extends Twig_Node {

	public function __construct(Twig_Node $tests, Twig_Node $else = NULL, $lineno, $tag = NULL)
	{
		parent::__construct(array('tests' => $tests, 'else' => $else), array(), $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler->debug_info($this);
		for ($i = 0; $i < count($this->getNode('tests')); $i += 2)
		{
			if ($i > 0)
			{
				$compiler
					->outdent()
					->write("} elseif (");
			}
			else
			{
				$compiler
					->write('if (');
			}

			$compiler
				->subcompile($this->getNode('tests')->getNode($i))
				->raw(") {\n")
				->indent()
				->subcompile($this->getNode('tests')->getNode($i + 1));
		}

		if ($this->hasNode('else') && null !== $this->getNode('else'))
		{
			$compiler
				->outdent()
				->write("} else {\n")
				->indent()
				->subcompile($this->getNode('else'));
		}

		$compiler
			->outdent()
			->write("}\n");
	}
}
