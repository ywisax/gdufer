<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Twig_Node_Traverser is a node traverser. It visits all nodes and their children and call the given visitor for each.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Traverser {

	protected $env;
	protected $visitors;

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $env      A Twig_Environment instance
	 * @param array            $visitors An array of Twig_Node_Visitor instances
	 */
	public function __construct(Twig_Environment $env, array $visitors = array())
	{
		$this->env = $env;
		$this->visitors = array();
		foreach ($visitors AS $visitor)
		{
			$this->addVisitor($visitor);
		}
	}

	/**
	 * Adds a visitor.
	 *
	 * @param Twig_Node_Visitor $visitor A Twig_Node_Visitor instance
	 */
	public function addVisitor(Twig_Node_Visitor $visitor)
	{
		if ( ! isset($this->visitors[$visitor->getPriority()]))
		{
			$this->visitors[$visitor->getPriority()] = array();
		}

		$this->visitors[$visitor->getPriority()][] = $visitor;
	}

	/**
	 * Traverses a node and calls the registered visitors.
	 *
	 * @param Twig_Node $node A Twig_Node instance
	 */
	public function traverse(Twig_Node $node)
	{
		ksort($this->visitors);
		foreach ($this->visitors AS $visitors)
		{
			foreach ($visitors AS $visitor)
			{
				$node = $this->traverseForVisitor($visitor, $node);
			}
		}

		return $node;
	}

	protected function traverseForVisitor(Twig_Node_Visitor $visitor, Twig_Node $node = NULL)
	{
		if ($node === NULL)
		{
			return NULL;
		}

		$node = $visitor->enterNode($node, $this->env);

		foreach ($node AS $k => $n)
		{
			if (($n = $this->traverseForVisitor($visitor, $n)) !== FALSE)
			{
				$node->setNode($k, $n);
			}
			else
			{
				$node->removeNode($k);
			}
		}

		return $visitor->leave_node($node, $this->env);
	}
}
