<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a block call node.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Expression_Array extends Twig_Node_Expression {

	protected $index;

	public function __construct(array $elements, $lineno)
	{
		parent::__construct($elements, array(), $lineno);

		$this->index = -1;
		foreach ($this->getKeyValuePairs() AS $pair)
		{
			if ($pair['key'] instanceof Twig_Node_Expression_Constant && ctype_digit((string) $pair['key']->get_attribute('value')) && $pair['key']->get_attribute('value') > $this->index)
			{
				$this->index = $pair['key']->get_attribute('value');
			}
		}
	}

	public function getKeyValuePairs()
	{
		$pairs = array();

		foreach (array_chunk($this->nodes, 2) AS $pair)
		{
			$pairs[] = array(
				'key' => $pair[0],
				'value' => $pair[1],
			);
		}

		return $pairs;
	}

	public function hasElement(Twig_Node_Expression $key)
	{
		foreach ($this->getKeyValuePairs() AS $pair)
		{
			// we compare the string representation of the keys
			// to avoid comparing the line numbers which are not relevant here.
			if ((string) $key == (string) $pair['key'])
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	public function addElement(Twig_Node_Expression $value, Twig_Node_Expression $key = NULL)
	{
		if ($key === NULL)
		{
			$key = new Twig_Node_Expression_Constant(++$this->index, $value->getLine());
		}

		array_push($this->nodes, $key, $value);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
		$compiler->raw('array(');
		$first = TRUE;
		foreach ($this->getKeyValuePairs() AS $pair)
		{
			if ( ! $first)
			{
				$compiler->raw(', ');
			}
			$first = FALSE;

			$compiler
				->subcompile($pair['key'])
				->raw(' => ')
				->subcompile($pair['value']);
		}
		$compiler->raw(')');
	}
}
