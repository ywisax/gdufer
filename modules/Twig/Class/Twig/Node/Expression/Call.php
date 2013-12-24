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
abstract class Twig_Node_Expression_Call extends Twig_Node_Expression {

	protected function compileCallable(Twig_Compiler $compiler)
	{
		$callable = $this->get_attribute('callable');

		$closingParenthesis = FALSE;
		if ($callable)
		{
			if (is_string($callable))
			{
				$compiler->raw($callable);
			}
			elseif (is_array($callable) && $callable[0] instanceof Twig_Extension)
			{
				$compiler->raw(sprintf('$this->env->getExtension(\'%s\')->%s', $callable[0]->getName(), $callable[1]));
			}
			else
			{
				$type = ucfirst($this->get_attribute('type'));
				$compiler->raw(sprintf('call_user_func_array($this->env->get%s(\'%s\')->getCallable(), array', $type, $this->get_attribute('name')));
				$closingParenthesis = TRUE;
			}
		}
		else
		{
			$compiler->raw($this->get_attribute('thing')->compile());
		}

		$this->compile_arguments($compiler);

		if ($closingParenthesis)
		{
			$compiler->raw(')');
		}
	}

	protected function compile_arguments(Twig_Compiler $compiler)
	{
		$compiler->raw('(');

		$first = TRUE;

		if ($this->hasAttribute('needs_environment') && $this->get_attribute('needs_environment'))
		{
			$compiler->raw('$this->env');
			$first = FALSE;
		}

		if ($this->hasAttribute('needs_context') && $this->get_attribute('needs_context'))
		{
			if ( ! $first)
			{
				$compiler->raw(', ');
			}
			$compiler->raw('$context');
			$first = FALSE;
		}

		if ($this->hasAttribute('arguments'))
		{
			foreach ($this->get_attribute('arguments') AS $argument)
			{
				if ( ! $first)
				{
					$compiler->raw(', ');
				}
				$compiler->string($argument);
				$first = FALSE;
			}
		}

		if ($this->hasNode('node'))
		{
			if ( ! $first)
			{
				$compiler->raw(', ');
			}
			$compiler->subcompile($this->getNode('node'));
			$first = FALSE;
		}

		if ($this->hasNode('arguments') && null !== $this->getNode('arguments'))
		{
			$callable = $this->hasAttribute('callable') ? $this->get_attribute('callable') : null;

			$arguments = $this->getArguments($callable, $this->getNode('arguments'));

			foreach ($arguments AS $node)
			{
				if ( ! $first)
				{
					$compiler->raw(', ');
				}
				$compiler->subcompile($node);
				$first = FALSE;
			}
		}

		$compiler->raw(')');
	}

	protected function getArguments($callable, $arguments)
	{
		$parameters = array();
		$named = FALSE;
		foreach ($arguments AS $name => $node)
		{
			if ( ! is_int($name))
			{
				$named = TRUE;
				$name = $this->normalizeName($name);
			}
			elseif ($named)
			{
				throw new Twig_Exception_Syntax(sprintf('Positional arguments cannot be used after named arguments for %s "%s".', $this->get_attribute('type'), $this->get_attribute('name')));
			}

			$parameters[$name] = $node;
		}

		if ( ! $named)
		{
			return $parameters;
		}

		if ( ! $callable)
		{
			throw new LogicException(sprintf('Named arguments are not supported for %s "%s".', $this->get_attribute('type'), $this->get_attribute('name')));
		}

		// manage named arguments
		if (is_array($callable))
		{
			$r = new ReflectionMethod($callable[0], $callable[1]);
		}
		elseif (is_object($callable) && !$callable instanceof Closure)
		{
			$r = new ReflectionObject($callable);
			$r = $r->getMethod('__invoke');
		}
		else
		{
			$r = new ReflectionFunction($callable);
		}

		$definition = $r->getParameters();
		if ($this->hasNode('node'))
		{
			array_shift($definition);
		}
		if ($this->hasAttribute('needs_environment') && $this->get_attribute('needs_environment'))
		{
			array_shift($definition);
		}
		if ($this->hasAttribute('needs_context') && $this->get_attribute('needs_context'))
		{
			array_shift($definition);
		}
		if ($this->hasAttribute('arguments') && null !== $this->get_attribute('arguments'))
		{
			foreach ($this->get_attribute('arguments') AS $argument)
			{
				array_shift($definition);
			}
		}

		$arguments = array();
		$pos = 0;
		foreach ($definition AS $param)
		{
			$name = $this->normalizeName($param->name);

			if (array_key_exists($name, $parameters))
			{
				if (array_key_exists($pos, $parameters))
				{
					throw new Twig_Exception_Syntax(sprintf('Argument "%s" is defined twice for %s "%s".', $name, $this->get_attribute('type'), $this->get_attribute('name')));
				}

				$arguments[] = $parameters[$name];
				unset($parameters[$name]);
			}
			elseif (array_key_exists($pos, $parameters))
			{
				$arguments[] = $parameters[$pos];
				unset($parameters[$pos]);
				++$pos;
			}
			elseif ($param->isDefaultValueAvailable())
			{
				$arguments[] = new Twig_Node_Expression_Constant($param->getDefaultValue(), -1);
			}
			elseif ($param->isOptional())
			{
				break;
			}
			else
			{
				throw new Twig_Exception_Syntax(sprintf('Value for argument "%s" is required for %s "%s".', $name, $this->get_attribute('type'), $this->get_attribute('name')));
			}
		}

		if ( ! empty($parameters))
		{
			throw new Twig_Exception_Syntax(sprintf('Unknown argument%s "%s" for %s "%s".', count($parameters) > 1 ? 's' : '' , implode('", "', array_keys($parameters)), $this->get_attribute('type'), $this->get_attribute('name')));
		}

		return $arguments;
	}

	const NORMALIZE_NAME_1_REGEX = '/([A-Z]+)([A-Z][a-z])/';
	const NORMALIZE_NAME_2_REGEX = '/([a-z\d])([A-Z])/';
	const NORMALIZE_NAME_1_REPLACE = '\\1_\\2';
	const NORMALIZE_NAME_2_REPLACE = '\\1_\\2';
	
	protected function normalizeName($name)
	{
		return strtolower(preg_replace(array(
				Twig_Node_Expression_Call::NORMALIZE_NAME_1_REGEX,
				Twig_Node_Expression_Call::NORMALIZE_NAME_2_REGEX,
			), array(
				Twig_Node_Expression_Call::NORMALIZE_NAME_1_REPLACE,
				Twig_Node_Expression_Call::NORMALIZE_NAME_2_REPLACE,
			),
		$name));
	}
}
