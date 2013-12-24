<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Internal node used by the for node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_ForLoop extends Twig_Node
{
	public function __construct($lineno, $tag = NULL)
	{
		parent::__construct(array(), array('with_loop' => FALSE, 'ifexpr' => FALSE, 'else' => FALSE), $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		if ($this->get_attribute('else'))
		{
			$compiler->write("\$context['_iterated'] = TRUE;\n");
		}

		if ($this->get_attribute('with_loop'))
		{
			$compiler
				->write("++\$context['loop']['index0'];\n")
				->write("++\$context['loop']['index'];\n")
				->write("\$context['loop']['first'] = FALSE;\n");

			if ( ! $this->get_attribute('ifexpr'))
			{
				$compiler
					->write("if (isset(\$context['loop']['length'])) {\n")
					->indent()
					->write("--\$context['loop']['revindex0'];\n")
					->write("--\$context['loop']['revindex'];\n")
					->write("\$context['loop']['last'] = 0 === \$context['loop']['revindex0'];\n")
					->outdent()
					->write("}\n");
			}
		}
	}
}
