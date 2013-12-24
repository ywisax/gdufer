<?php defined('SYS_PATH') or die('No direct script access.');

class Twig_Extension_Optimizer extends Twig_Extension
{
	protected $optimizers;

	public function __construct($optimizers = -1)
	{
		$this->optimizers = $optimizers;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_node_visitors()
	{
		return array(new Twig_Node_Visitor_Optimizer($this->optimizers));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'optimizer';
	}
}
