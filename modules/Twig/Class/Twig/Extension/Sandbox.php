<?php defined('SYS_PATH') or die('No direct script access.');

class Twig_Extension_Sandbox extends Twig_Extension
{
	protected $sandboxedGlobally;
	protected $sandboxed;
	protected $policy;

	public function __construct(Twig_Sandbox_Policy $policy, $sandboxed = FALSE)
	{
		$this->policy            = $policy;
		$this->sandboxedGlobally = $sandboxed;
	}

	/**
	 * Returns the token parser instances to add to the existing list.
	 *
	 * @return array An array of Twig_Token_Parser or Twig_Token_Parser_Broker instances
	 */
	public function get_token_parsers()
	{
		return array(new Twig_Token_Parser_Sandbox());
	}

	/**
	 * Returns the node visitor instances to add to the existing list.
	 *
	 * @return array An array of Twig_Node_Visitor instances
	 */
	public function get_node_visitors()
	{
		return array(new Twig_Node_Visitor_Sandbox());
	}

	public function enableSandbox()
	{
		$this->sandboxed = TRUE;
	}

	public function disableSandbox()
	{
		$this->sandboxed = FALSE;
	}

	public function isSandboxed()
	{
		return $this->sandboxedGlobally || $this->sandboxed;
	}

	public function isSandboxedGlobally()
	{
		return $this->sandboxedGlobally;
	}

	public function setSecurityPolicy(Twig_Sandbox_Policy $policy)
	{
		$this->policy = $policy;
	}

	public function getSecurityPolicy()
	{
		return $this->policy;
	}

	public function checkSecurity($tags, $filters, $functions)
	{
		if ($this->isSandboxed())
		{
			$this->policy->checkSecurity($tags, $filters, $functions);
		}
	}

	public function checkMethodAllowed($obj, $method)
	{
		if ($this->isSandboxed())
		{
			$this->policy->checkMethodAllowed($obj, $method);
		}
	}

	public function checkPropertyAllowed($obj, $method)
	{
		if ($this->isSandboxed())
		{
			$this->policy->checkPropertyAllowed($obj, $method);
		}
	}

	public function ensureToStringAllowed($obj)
	{
		if (is_object($obj))
		{
			$this->policy->checkMethodAllowed($obj, '__toString');
		}

		return $obj;
	}

	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName()
	{
		return 'sandbox';
	}
}
