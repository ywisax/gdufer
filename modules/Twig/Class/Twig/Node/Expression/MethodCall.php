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
class Twig_Node_Expression_MethodCall extends Twig_Node_Expression {

	public function __construct(Twig_Node_Expression $node, $method, Twig_Node_Expression_Array $arguments, $lineno)
	{
		parent::__construct(array('node' => $node, 'arguments' => $arguments), array('method' => $method, 'safe' => FALSE), $lineno);

		if ($node instanceof Twig_Node_Expression_Name)
		{
			$node->set_attribute('always_defined', TRUE);
		}
	}

	public function compile(Twig_Compiler $compiler)
	{
		$compiler
			->subcompile($this->getNode('node'))
			->raw('->')
			->raw($this->get_attribute('method'))
			->raw('(')
		;
		$first = TRUE;
		foreach ($this->getNode('arguments')->getKeyValuePairs() AS $pair)
		{
			if ( ! $first)
			{
				$compiler->raw(', ');
			}
			$first = FALSE;

			$compiler->subcompile($pair['value']);
		}
		$compiler->raw(')');
	}
}
