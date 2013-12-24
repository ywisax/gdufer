<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * XXX
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Visitor_SafeAnalysis extends Twig_Node_Visitor
{
	protected $data = array();
	protected $safeVars = array();

	public function setSafeVars($safeVars)
	{
		$this->safeVars = $safeVars;
	}

	public function get_safe(Twig_Node $node)
	{
		$hash = spl_object_hash($node);
		if (isset($this->data[$hash]))
		{
			foreach ($this->data[$hash] AS $bucket)
			{
				if ($bucket['key'] === $node)
				{
					return $bucket['value'];
				}
			}
		}
	}

	protected function setSafe(Twig_Node $node, array $safe)
	{
		$hash = spl_object_hash($node);
		if (isset($this->data[$hash]))
		{
			foreach ($this->data[$hash] as &$bucket)
			{
				if ($bucket['key'] === $node)
				{
					$bucket['value'] = $safe;
					return;
				}
			}
		}
		$this->data[$hash][] = array(
			'key' => $node,
			'value' => $safe,
		);
	}

	public function enterNode(Twig_Node $node, Twig_Environment $env)
	{
		return $node;
	}

	public function leave_node(Twig_Node $node, Twig_Environment $env)
	{
		if ($node instanceof Twig_Node_Expression_Constant)
		{
			// constants are marked safe for all
			$this->setSafe($node, array('all'));
		}
		elseif ($node instanceof Twig_Node_Expression_BlockReference)
		{
			// blocks are safe by definition
			$this->setSafe($node, array('all'));
		}
		elseif ($node instanceof Twig_Node_Expression_Parent)
		{
			// parent block is safe by definition
			$this->setSafe($node, array('all'));
		}
		elseif ($node instanceof Twig_Node_Expression_Conditional)
		{
			// intersect safeness of both operands
			$safe = $this->intersectSafe($this->get_safe($node->getNode('expr2')), $this->get_safe($node->getNode('expr3')));
			$this->setSafe($node, $safe);
		}
		elseif ($node instanceof Twig_Node_Expression_Filter)
		{
			// filter expression is safe when the filter is safe
			$name = $node->getNode('filter')->get_attribute('value');
			$args = $node->getNode('arguments');
			if (($filter = $env->getFilter($name)) !== FALSE)
			{
				$safe = $filter->get_safe($args);
				if ($safe === NULL)
				{
					$safe = $this->intersectSafe($this->get_safe($node->getNode('node')), $filter->getPreservesSafety());
				}
				$this->setSafe($node, $safe);
			}
			else
			{
				$this->setSafe($node, array());
			}
		}
		elseif ($node instanceof Twig_Node_Expression_Function)
		{
			// function expression is safe when the function is safe
			$name = $node->get_attribute('name');
			$args = $node->getNode('arguments');
			$function = $env->getFunction($name);
			if ($function !== FALSE)
			{
				$this->setSafe($node, $function->get_safe($args));
			}
			else
			{
				$this->setSafe($node, array());
			}
		}
		elseif ($node instanceof Twig_Node_Expression_MethodCall)
		{
			if ($node->get_attribute('safe'))
			{
				$this->setSafe($node, array('all'));
			}
			else
			{
				$this->setSafe($node, array());
			}
		}
		elseif ($node instanceof Twig_Node_Expression_MacroCall)
		{
			$this->setSafe($node, array('all'));
		}
		elseif ($node instanceof Twig_Node_Expression_GetAttr && $node->getNode('node') instanceof Twig_Node_Expression_Name)
		{
			$name = $node->getNode('node')->get_attribute('name');
			// attributes on template instances are safe
			if ('_self' == $name || in_array($name, $this->safeVars))
			{
				$this->setSafe($node, array('all'));
			}
			else
			{
				$this->setSafe($node, array());
			}
		}
		else
		{
			$this->setSafe($node, array());
		}

		return $node;
	}

	protected function intersectSafe(array $a = NULL, array $b = NULL)
	{
		if (null === $a || null === $b)
		{
			return array();
		}

		if (in_array('all', $a))
		{
			return $b;
		}

		if (in_array('all', $b))
		{
			return $a;
		}

		return array_intersect($a, $b);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPriority()
	{
		return 0;
	}
}
