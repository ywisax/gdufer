<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Twig_Node_Visitor_Sandbox implements sandboxing.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Visitor_Sandbox extends Twig_Node_Visitor
{
	protected $inAModule = FALSE;
	protected $tags;
	protected $filters;
	protected $functions;

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
			$this->inAModule = TRUE;
			$this->tags = array();
			$this->filters = array();
			$this->functions = array();

			return $node;
		}
		elseif ($this->inAModule)
		{
			// look for tags
			if ($node->getNodeTag())
			{
				$this->tags[] = $node->getNodeTag();
			}

			// look for filters
			if ($node instanceof Twig_Node_Expression_Filter)
			{
				$this->filters[] = $node->getNode('filter')->get_attribute('value');
			}

			// look for functions
			if ($node instanceof Twig_Node_Expression_Function)
			{
				$this->functions[] = $node->get_attribute('name');
			}

			// wrap print to check __toString() calls
			if ($node instanceof Twig_Node_Print)
			{
				return new Twig_Node_SandboxedPrint($node->getNode('expr'), $node->getLine(), $node->getNodeTag());
			}
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
			$this->inAModule = FALSE;
			return new Twig_Node_SandboxedModule($node, array_unique($this->filters), array_unique($this->tags), array_unique($this->functions));
		}

		return $node;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPriority()
	{
		return 0;
	}
}
