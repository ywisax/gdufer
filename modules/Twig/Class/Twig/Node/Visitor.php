<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Twig_Node_Visitor is the interface the all node visitor classes must implement.
 *
 * @package    Kohana/Twig
 * @category   Node
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
abstract class Twig_Node_Visitor {

	/**
	 * Called before child nodes are visited.
	 *
	 * @param Twig_Node $node The node to visit
	 * @param Twig_Environment   $env  The Twig environment instance
	 *
	 * @return Twig_Node The modified node
	 */
	abstract public function enterNode(Twig_Node $node, Twig_Environment $env);

	/**
	 * Called after child nodes are visited.
	 *
	 * @param Twig_Node $node The node to visit
	 * @param Twig_Environment   $env  The Twig environment instance
	 *
	 * @return Twig_Node|false The modified node or false if the node must be removed
	 */
	abstract public function leave_node(Twig_Node $node, Twig_Environment $env);

	/**
	 * Returns the priority for this visitor.
	 *
	 * Priority should be between -10 and 10 (0 is the default).
	 *
	 * @return integer The priority level
	 */
	abstract public function getPriority();
}
