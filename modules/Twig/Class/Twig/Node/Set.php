<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a set node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Set extends Twig_Node {

	public function __construct($capture, Twig_Node $names, Twig_Node $values, $lineno, $tag = NULL)
	{
		parent::__construct(array('names' => $names, 'values' => $values), array('capture' => $capture, 'safe' => FALSE), $lineno, $tag);

		/*
    	 * Optimizes the node when capture is used for a large block of text.
    	 *
    	 * {% set foo %}foo{% endset %} is compiled to $context['foo'] = new Twig_Markup("foo");
    	 */
		if ($this->get_attribute('capture'))
		{
			$this->set_attribute('safe', TRUE);

			$values = $this->getNode('values');
			if ($values instanceof Twig_Node_Text)
			{
				$this->setNode('values', new Twig_Node_Expression_Constant($values->get_attribute('data'), $values->getLine()));
				$this->set_attribute('capture', FALSE);
			}
		}
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler->debug_info($this);

		if (count($this->getNode('names')) > 1)
		{
			$compiler->write('list(');
			foreach ($this->getNode('names') AS $idx => $node)
			{
				if ($idx)
				{
					$compiler->raw(', ');
				}

				$compiler->subcompile($node);
			}
			$compiler->raw(')');
		}
		else
		{
			if ($this->get_attribute('capture'))
			{
				$compiler
					->write("ob_start();\n")
					->subcompile($this->getNode('values'));
			}

			$compiler->subcompile($this->getNode('names'), FALSE);

			if ($this->get_attribute('capture'))
			{
				$compiler->raw(" = ('' === \$tmp = ob_get_clean()) ? '' : new Twig_Markup(\$tmp, \$this->env->getCharset())");
			}
		}

		if ( ! $this->get_attribute('capture'))
		{
			$compiler->raw(' = ');

			if (count($this->getNode('names')) > 1)
			{
				$compiler->write('array(');
				foreach ($this->getNode('values') AS $idx => $value)
				{
					if ($idx)
					{
						$compiler->raw(', ');
					}

					$compiler->subcompile($value);
				}
				$compiler->raw(')');
			}
			else
			{
				if ($this->get_attribute('safe'))
				{
					$compiler
						->raw("('' === \$tmp = ")
						->subcompile($this->getNode('values'))
						->raw(") ? '' : new Twig_Markup(\$tmp, \$this->env->getCharset())");
				}
				else
				{
					$compiler->subcompile($this->getNode('values'));
				}
			}
		}

		$compiler->raw(";\n");
	}
}
