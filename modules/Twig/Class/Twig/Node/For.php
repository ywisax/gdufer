<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a for node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_For extends Twig_Node {

	protected $loop;

	public function __construct(Twig_Node_Expression_AssignName $keyTarget, Twig_Node_Expression_AssignName $valueTarget, Twig_Node_Expression $seq, Twig_Node_Expression $ifexpr = NULL, Twig_Node $body, Twig_Node $else = NULL, $lineno, $tag = NULL)
	{
		$body = new Twig_Node(array($body, $this->loop = new Twig_Node_ForLoop($lineno, $tag)));

		if ($ifexpr !== NULL)
		{
			$body = new Twig_Node_If(new Twig_Node(array($ifexpr, $body)), null, $lineno, $tag);
		}

		parent::__construct(array('key_target' => $keyTarget, 'value_target' => $valueTarget, 'seq' => $seq, 'body' => $body, 'else' => $else), array('with_loop' => TRUE, 'ifexpr' => null !== $ifexpr), $lineno, $tag);
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
			// the (array) cast bypasses a PHP 5.2.6 bug
			->write("\$context['_parent'] = (array) \$context;\n")
			->write("\$context['_seq'] = Twig_Extension_Helper::ensure_traversable(")
			->subcompile($this->getNode('seq'))
			->raw(");\n")
		;

		if (null !== $this->getNode('else'))
		{
			$compiler->write("\$context['_iterated'] = FALSE;\n");
		}

		if ($this->get_attribute('with_loop'))
		{
			$compiler
				->write("\$context['loop'] = array(\n")
				->write("  'parent' => \$context['_parent'],\n")
				->write("  'index0' => 0,\n")
				->write("  'index'  => 1,\n")
				->write("  'first'  => TRUE,\n")
				->write(");\n");

			if ( ! $this->get_attribute('ifexpr'))
			{
				$compiler
					->write("if (is_array(\$context['_seq']) || (is_object(\$context['_seq']) && \$context['_seq'] instanceof Countable)) {\n")
					->indent()
					->write("\$length = count(\$context['_seq']);\n")
					->write("\$context['loop']['revindex0'] = \$length - 1;\n")
					->write("\$context['loop']['revindex'] = \$length;\n")
					->write("\$context['loop']['length'] = \$length;\n")
					->write("\$context['loop']['last'] = 1 === \$length;\n")
					->outdent()
					->write("}\n");
			}
		}

		$this->loop->set_attribute('else', null !== $this->getNode('else'));
		$this->loop->set_attribute('with_loop', $this->get_attribute('with_loop'));
		$this->loop->set_attribute('ifexpr', $this->get_attribute('ifexpr'));

		$compiler
			->write("foreach (\$context['_seq'] as ")
			->subcompile($this->getNode('key_target'))
			->raw(" => ")
			->subcompile($this->getNode('value_target'))
			->raw(") {\n")
			->indent()
			->subcompile($this->getNode('body'))
			->outdent()
			->write("}\n")
		;

		if ($this->getNode('else') !== NULL)
		{
			$compiler
				->write("if ( ! \$context['_iterated']) {\n")
				->indent()
				->subcompile($this->getNode('else'))
				->outdent()
				->write("}\n");
		}

		$compiler->write("\$_parent = \$context['_parent'];\n");

		// remove some "private" loop variables (needed for nested loops)
		$compiler->write('unset($context[\'_seq\'], $context[\'_iterated\'], $context[\''.$this->getNode('key_target')->get_attribute('name').'\'], $context[\''.$this->getNode('value_target')->get_attribute('name').'\'], $context[\'_parent\'], $context[\'loop\']);'."\n");

		// keep the values set in the inner context for variables defined in the outer context
		$compiler->write("\$context = array_intersect_key(\$context, \$_parent) + \$_parent;\n");
	}
}
