<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a macro call node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_MacroCall extends Twig_Node_Expression {

	public function __construct(Twig_Node_Expression $template, $name, Twig_Node_Expression_Array $arguments, $lineno)
	{
		parent::__construct(array('template' => $template, 'arguments' => $arguments), array('name' => $name), $lineno);
	}

	public function compile(Twig_Compiler $compiler)
	{
		$namedNames = array();
		$namedCount = 0;
		$positionalCount = 0;
		foreach ($this->getNode('arguments')->getKeyValuePairs() AS $pair)
		{
			$name = $pair['key']->get_attribute('value');
			if ( ! is_int($name))
			{
				$namedCount++;
				$namedNames[$name] = 1;
			}
			elseif ($namedCount > 0)
			{
				throw new Twig_Exception_Syntax(sprintf('Positional arguments cannot be used after named arguments for macro "%s".', $this->get_attribute('name')), $this->lineno);
			}
			else
			{
				$positionalCount++;
			}
		}

		$compiler
			->raw('$this->call_macro(')
			->subcompile($this->getNode('template'))
			->raw(', ')->repr($this->get_attribute('name'))
			->raw(', ')->subcompile($this->getNode('arguments'));

		if ($namedCount > 0)
		{
			$compiler
				->raw(', ')->repr($namedNames)
				->raw(', ')->repr($namedCount)
				->raw(', ')->repr($positionalCount);
		}

		$compiler->raw(')');
	}
}
