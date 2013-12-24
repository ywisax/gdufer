<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Twig_Node_Visitor_Optimizer tries to optimizes the AST.
 *
 * This visitor is always the last registered one.
 *
 * You can configure which optimizations you want to activate via the
 * optimizer mode.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Node_Visitor_Optimizer extends Twig_Node_Visitor {

	const OPTIMIZE_ALL         = -1;
	const OPTIMIZE_NONE        = 0;
	const OPTIMIZE_FOR         = 2;
	const OPTIMIZE_RAW_FILTER  = 4;
	const OPTIMIZE_VAR_ACCESS  = 8;

	protected $loops = array();
	protected $optimizers;
	protected $prependedNodes = array();
	protected $inABody = FALSE;

	/**
	 * Constructor.
	 *
	 * @param integer $optimizers The optimizer mode
	 */
	public function __construct($optimizers = -1)
	{
		if ( ! is_int($optimizers) || $optimizers > 2)
		{
			throw new InvalidArgumentException(sprintf('Optimizer mode "%s" is not valid.', $optimizers));
		}

		$this->optimizers = $optimizers;
	}

	/**
	 * {@inheritdoc}
	 */
	public function enterNode(Twig_Node $node, Twig_Environment $env)
	{
		if (self::OPTIMIZE_FOR === (self::OPTIMIZE_FOR & $this->optimizers))
		{
			$this->enter_optimizeFor($node, $env);
		}

		if ( ! version_compare(phpversion(), '5.4.0RC1', '>=') && self::OPTIMIZE_VAR_ACCESS === (self::OPTIMIZE_VAR_ACCESS & $this->optimizers) && !$env->isStrictVariables() && !$env->hasExtension('sandbox'))
		{
			if ($this->inABody)
			{
				if ( ! $node instanceof Twig_Node_Expression)
				{
					if (get_class($node) !== 'Twig_Node')
					{
						array_unshift($this->prependedNodes, array());
					}
				}
				else
				{
					$node = $this->optimize_variables($node, $env);
				}
			}
			elseif ($node instanceof Twig_Node_Body)
			{
				$this->inABody = TRUE;
			}
		}

		return $node;
	}

	/**
	 * {@inheritdoc}
	 */
	public function leave_node(Twig_Node $node, Twig_Environment $env)
	{
		$expression = $node instanceof Twig_Node_Expression;

		if (self::OPTIMIZE_FOR === (self::OPTIMIZE_FOR & $this->optimizers))
		{
			$this->leaveOptimizeFor($node, $env);
		}

		if (self::OPTIMIZE_RAW_FILTER === (self::OPTIMIZE_RAW_FILTER & $this->optimizers))
		{
			$node = $this->optimize_raw_filter($node, $env);
		}

		$node = $this->optimize_print_node($node, $env);

		if (self::OPTIMIZE_VAR_ACCESS === (self::OPTIMIZE_VAR_ACCESS & $this->optimizers) && !$env->isStrictVariables() && !$env->hasExtension('sandbox'))
		{
			if ($node instanceof Twig_Node_Body)
			{
				$this->inABody = FALSE;
			}
			elseif ($this->inABody)
			{
				if ( ! $expression && get_class($node) !== 'Twig_Node' && $prependedNodes = array_shift($this->prependedNodes))
				{
					$nodes = array();
					foreach (array_unique($prependedNodes) AS $name)
					{
						$nodes[] = new Twig_Node_SetTemp($name, $node->getLine());
					}

					$nodes[] = $node;
					$node = new Twig_Node($nodes);
				}
			}
		}

		return $node;
	}

	protected function optimize_variables($node, $env)
	{
		if ('Twig_Node_Expression_Name' === get_class($node) && $node->isSimple())
		{
			$this->prependedNodes[0][] = $node->get_attribute('name');
			return new Twig_Node_Expression_TempName($node->get_attribute('name'), $node->getLine());
		}

		return $node;
	}

	/**
	 * Optimizes print nodes.
	 *
	 * It replaces:
	 *
	 *   * "echo $this->render(Parent)Block()" with "$this->display(Parent)Block()"
	 *
	 * @param Twig_Node $node A Node
	 * @param Twig_Environment   $env  The current Twig environment
	 */
	protected function optimize_print_node($node, $env)
	{
		if ( ! $node instanceof Twig_Node_Print)
		{
			return $node;
		}

		if (
			$node->getNode('expr') instanceof Twig_Node_Expression_BlockReference ||
			$node->getNode('expr') instanceof Twig_Node_Expression_Parent
		)
		{
			$node->getNode('expr')->set_attribute('output', TRUE);

			return $node->getNode('expr');
		}

		return $node;
	}

	/**
	 * Removes "raw" filters.
	 *
	 * @param Twig_Node $node A Node
	 * @param Twig_Environment   $env  The current Twig environment
	 */
	protected function optimize_raw_filter($node, $env)
	{
		if ($node instanceof Twig_Node_Expression_Filter && 'raw' == $node->getNode('filter')->get_attribute('value'))
		{
			return $node->getNode('node');
		}

		return $node;
	}

	/**
	 * Optimizes "for" tag by removing the "loop" variable creation whenever possible.
	 *
	 * @param Twig_Node $node A Node
	 * @param Twig_Environment   $env  The current Twig environment
	 */
	protected function enter_optimizeFor($node, $env)
	{
		if ($node instanceof Twig_Node_For)
		{
			// disable the loop variable by default
			$node->set_attribute('with_loop', FALSE);
			array_unshift($this->loops, $node);
		}
		elseif ( ! $this->loops)
		{
			// we are outside a loop
			return;
		}

		// when do we need to add the loop variable back?

		// the loop variable is referenced for the current loop
		elseif ($node instanceof Twig_Node_Expression_Name && 'loop' === $node->get_attribute('name'))
		{
			$this->add_loop_to_current();
		}

		// block reference
		elseif ($node instanceof Twig_Node_BlockReference || $node instanceof Twig_Node_Expression_BlockReference)
		{
			$this->add_loop_to_current();
		}

		// include without the only attribute
		elseif ($node instanceof Twig_Node_Include && !$node->get_attribute('only'))
		{
			$this->add_loop_to_all();
		}

		// the loop variable is referenced via an attribute
		elseif ($node instanceof Twig_Node_Expression_GetAttr
			&& ( ! $node->getNode('attribute') instanceof Twig_Node_Expression_Constant
				|| 'parent' === $node->getNode('attribute')->get_attribute('value')
			   )
			&& (TRUE === $this->loops[0]->get_attribute('with_loop')
				|| ($node->getNode('node') instanceof Twig_Node_Expression_Name
					&& 'loop' === $node->getNode('node')->get_attribute('name')
				   )
			   )
		)
		{
			$this->add_loop_to_all();
		}
	}

	/**
	 * Optimizes "for" tag by removing the "loop" variable creation whenever possible.
	 *
	 * @param Twig_Node $node A Node
	 * @param Twig_Environment   $env  The current Twig environment
	 */
	protected function leaveOptimizeFor($node, $env)
	{
		if ($node instanceof Twig_Node_For)
		{
			array_shift($this->loops);
		}
	}

	protected function add_loop_to_current()
	{
		$this->loops[0]->set_attribute('with_loop', TRUE);
	}

	protected function add_loop_to_all()
	{
		foreach ($this->loops AS $loop)
		{
			$loop->set_attribute('with_loop', TRUE);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPriority()
	{
		return 255;
	}
}
