<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Twig_Node_Visitor_Escaper implements output escaping.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Visitor_Escaper extends Twig_Node_Visitor {

	protected $statusStack = array();
	protected $blocks = array();
	protected $safeAnalysis;
	protected $traverser;
	protected $defaultStrategy = FALSE;
	protected $safeVars = array();

	public function __construct()
	{
		$this->safeAnalysis = new Twig_Node_Visitor_SafeAnalysis();
	}

	/**
	 * Called before child nodes are visited.
	 *
	 * @param Twig_Node $node The node to visit
	 * @param Twig_Environment   $env  The Twig environment instance
	 *
	 * @return Twig_Node The modified node
	 */
	public function enterNode(Twig_Node $node, Twig_Environment $env)
	{
		if ($node instanceof Twig_Node_Module)
		{
			if ($env->hasExtension('escaper') && $defaultStrategy = $env->getExtension('escaper')->getDefaultStrategy($node->get_attribute('filename')))
			{
				$this->defaultStrategy = $defaultStrategy;
			}
			$this->safeVars = array();
		}
		elseif ($node instanceof Twig_Node_AutoEscape)
		{
			$this->statusStack[] = $node->get_attribute('value');
		}
		elseif ($node instanceof Twig_Node_Block)
		{
			$this->statusStack[] = isset($this->blocks[$node->get_attribute('name')]) ? $this->blocks[$node->get_attribute('name')] : $this->needEscaping($env);
		}
		elseif ($node instanceof Twig_Node_Import)
		{
			$this->safeVars[] = $node->getNode('var')->get_attribute('name');
		}

		return $node;
	}

	/**
	 * Called after child nodes are visited.
	 *
	 * @param Twig_Node $node The node to visit
	 * @param Twig_Environment   $env  The Twig environment instance
	 *
	 * @return Twig_Node The modified node
	 */
	public function leave_node(Twig_Node $node, Twig_Environment $env)
	{
		if ($node instanceof Twig_Node_Module)
		{
			$this->defaultStrategy = FALSE;
			$this->safeVars = array();
		}
		elseif ($node instanceof Twig_Node_Expression_Filter)
		{
			return $this->preEscapeFilterNode($node, $env);
		}
		elseif ($node instanceof Twig_Node_Print)
		{
			return $this->escapePrintNode($node, $env, $this->needEscaping($env));
		}

		if ($node instanceof Twig_Node_AutoEscape || $node instanceof Twig_Node_Block)
		{
			array_pop($this->statusStack);
		}
		elseif ($node instanceof Twig_Node_BlockReference)
		{
			$this->blocks[$node->get_attribute('name')] = $this->needEscaping($env);
		}

		return $node;
	}

	protected function escapePrintNode(Twig_Node_Print $node, Twig_Environment $env, $type)
	{
		if ($type === FALSE)
		{
			return $node;
		}

		$expression = $node->getNode('expr');

		if ($this->isSafeFor($type, $expression, $env))
		{
			return $node;
		}

		$class = get_class($node);

		return new $class(
			$this->getEscaperFilter($type, $expression),
			$node->getLine()
		);
	}

	protected function preEscapeFilterNode(Twig_Node_Expression_Filter $filter, Twig_Environment $env)
	{
		$name = $filter->getNode('filter')->get_attribute('value');

		$type = $env->getFilter($name)->getPreEscape();
		if ($type === NULL)
		{
			return $filter;
		}

		$node = $filter->getNode('node');
		if ($this->isSafeFor($type, $node, $env))
		{
			return $filter;
		}

		$filter->setNode('node', $this->getEscaperFilter($type, $node));

		return $filter;
	}

	protected function isSafeFor($type, Twig_Node $expression, $env)
	{
		$safe = $this->safeAnalysis->get_safe($expression);

		if ($safe === NULL)
		{
			if ($this->traverser === NULL)
			{
				$this->traverser = new Twig_Node_Traverser($env, array($this->safeAnalysis));
			}

			$this->safeAnalysis->setSafeVars($this->safeVars);

			$this->traverser->traverse($expression);
			$safe = $this->safeAnalysis->get_safe($expression);
		}

		return in_array($type, $safe) || in_array('all', $safe);
	}

	protected function needEscaping(Twig_Environment $env)
	{
		if (count($this->statusStack))
		{
			return $this->statusStack[count($this->statusStack) - 1];
		}

		return $this->defaultStrategy ? $this->defaultStrategy : FALSE;
	}

	protected function getEscaperFilter($type, Twig_Node $node)
	{
		$line = $node->getLine();
		$name = new Twig_Node_Expression_Constant('escape', $line);
		$args = new Twig_Node(array(
			new Twig_Node_Expression_Constant((string) $type, $line),
			new Twig_Node_Expression_Constant(NULL, $line),
			new Twig_Node_Expression_Constant(TRUE, $line),
		));

		return new Twig_Node_Expression_Filter($node, $name, $args, $line);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPriority()
	{
		return 0;
	}
}
