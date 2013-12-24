<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Parses expressions. This parser implements a "Precedence climbing" algorithm.
 *
 * @package    Kohana/Twig
 * @category   Base
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Expression {

	const OPERATOR_LEFT = 1;
	const OPERATOR_RIGHT = 2;

	protected $parser;
	protected $unaryOperators;
	protected $binary_operators;

	public function __construct(Twig_Parser $parser, array $unaryOperators, array $binary_operators)
	{
		$this->parser = $parser;
		$this->unaryOperators = $unaryOperators;
		$this->binary_operators = $binary_operators;
	}

	public function parse_expression($precedence = 0)
	{
		$expr = $this->getPrimary();
		$token = $this->parser->getCurrentToken();
		while ($this->isBinary($token) && $this->binary_operators[$token->getValue()]['precedence'] >= $precedence)
		{
			$op = $this->binary_operators[$token->getValue()];
			$this->parser->getStream()->next();

			if (isset($op['callable']))
			{
				$expr = call_user_func($op['callable'], $this->parser, $expr);
			}
			else
			{
				$expr1 = $this->parse_expression(self::OPERATOR_LEFT === $op['associativity'] ? $op['precedence'] + 1 : $op['precedence']);
				$class = $op['class'];
				$expr = new $class($expr, $expr1, $token->getLine());
			}

			$token = $this->parser->getCurrentToken();
		}

		if ($precedence === 0)
		{
			return $this->parse_conditional_expression($expr);
		}

		return $expr;
	}

	protected function getPrimary()
	{
		$token = $this->parser->getCurrentToken();

		if ($this->isUnary($token))
		{
			$operator = $this->unaryOperators[$token->getValue()];
			$this->parser->getStream()->next();
			$expr = $this->parse_expression($operator['precedence']);
			$class = $operator['class'];

			return $this->parse_postfix_expression(new $class($expr, $token->getLine()));
		}
		elseif ($token->test(Twig_Token::PUNCTUATION_TYPE, '('))
		{
			$this->parser->getStream()->next();
			$expr = $this->parse_expression();
			$this->parser->getStream()->expect(Twig_Token::PUNCTUATION_TYPE, ')', 'An opened parenthesis is not properly closed');

			return $this->parse_postfix_expression($expr);
		}

		return $this->parse_primary_expression();
	}

	protected function parse_conditional_expression($expr)
	{
		while ($this->parser->getStream()->test(Twig_Token::PUNCTUATION_TYPE, '?'))
		{
			$this->parser->getStream()->next();
			if ( ! $this->parser->getStream()->test(Twig_Token::PUNCTUATION_TYPE, ':'))
			{
				$expr2 = $this->parse_expression();
				if ($this->parser->getStream()->test(Twig_Token::PUNCTUATION_TYPE, ':'))
				{
					$this->parser->getStream()->next();
					$expr3 = $this->parse_expression();
				}
				else
				{
					$expr3 = new Twig_Node_Expression_Constant('', $this->parser->getCurrentToken()->getLine());
				}
			}
			else
			{
				$this->parser->getStream()->next();
				$expr2 = $expr;
				$expr3 = $this->parse_expression();
			}

			$expr = new Twig_Node_Expression_Conditional($expr, $expr2, $expr3, $this->parser->getCurrentToken()->getLine());
		}

		return $expr;
	}

	protected function isUnary(Twig_Token $token)
	{
		return $token->test(Twig_Token::OPERATOR_TYPE) && isset($this->unaryOperators[$token->getValue()]);
	}

	protected function isBinary(Twig_Token $token)
	{
		return $token->test(Twig_Token::OPERATOR_TYPE) && isset($this->binary_operators[$token->getValue()]);
	}

	public function parse_primary_expression()
	{
		$token = $this->parser->getCurrentToken();
		switch ($token->getType())
		{
			case Twig_Token::NAME_TYPE:
				$this->parser->getStream()->next();
				switch ($token->getValue())
				{
					case 'true':
					case 'TRUE':
						$node = new Twig_Node_Expression_Constant(TRUE, $token->getLine());
						break;

					case 'false':
					case 'FALSE':
						$node = new Twig_Node_Expression_Constant(FALSE, $token->getLine());
						break;

					case 'none':
					case 'NONE':
					case 'null':
					case 'NULL':
						$node = new Twig_Node_Expression_Constant(null, $token->getLine());
						break;

					default:
						if ('(' === $this->parser->getCurrentToken()->getValue())
						{
						    $node = $this->get_function_node($token->getValue(), $token->getLine());
						}
						else
						{
						    $node = new Twig_Node_Expression_Name($token->getValue(), $token->getLine());
						}
				}
				break;

			case Twig_Token::NUMBER_TYPE:
				$this->parser->getStream()->next();
				$node = new Twig_Node_Expression_Constant($token->getValue(), $token->getLine());
				break;

			case Twig_Token::STRING_TYPE:
			case Twig_Token::INTERPOLATION_START_TYPE:
				$node = $this->parse_string_expression();
				break;

			default:
				if ($token->test(Twig_Token::PUNCTUATION_TYPE, '['))
				{
					$node = $this->parse_array_expression();
				}
				elseif ($token->test(Twig_Token::PUNCTUATION_TYPE, '{'))
				{
					$node = $this->parse_hash_expression();
				}
				else
				{
					throw new Twig_Exception_Syntax(sprintf('Unexpected token "%s" of value "%s"', Twig_Token::typeToEnglish($token->getType(), $token->getLine()), $token->getValue()), $token->getLine(), $this->parser->getFilename());
				}
		}

		return $this->parse_postfix_expression($node);
	}

	public function parse_string_expression()
	{
		$stream = $this->parser->getStream();

		$nodes = array();
		// a string cannot be followed by another string in a single expression
		$nextCanBeString = TRUE;
		while (TRUE)
		{
			if ($stream->test(Twig_Token::STRING_TYPE) && $nextCanBeString)
			{
				$token = $stream->next();
				$nodes[] = new Twig_Node_Expression_Constant($token->getValue(), $token->getLine());
				$nextCanBeString = FALSE;
			}
			elseif ($stream->test(Twig_Token::INTERPOLATION_START_TYPE))
			{
				$stream->next();
				$nodes[] = $this->parse_expression();
				$stream->expect(Twig_Token::INTERPOLATION_END_TYPE);
				$nextCanBeString = TRUE;
			}
			else
			{
				break;
			}
		}

		$expr = array_shift($nodes);
		foreach ($nodes AS $node)
		{
			$expr = new Twig_Node_Expression_Binary_Concat($expr, $node, $node->getLine());
		}

		return $expr;
	}

	public function parse_array_expression()
	{
		$stream = $this->parser->getStream();
		$stream->expect(Twig_Token::PUNCTUATION_TYPE, '[', 'An array element was expected');

		$node = new Twig_Node_Expression_Array(array(), $stream->getCurrent()->getLine());
		$first = TRUE;
		while ( ! $stream->test(Twig_Token::PUNCTUATION_TYPE, ']'))
		{
			if ( ! $first)
			{
				$stream->expect(Twig_Token::PUNCTUATION_TYPE, ',', 'An array element must be followed by a comma');

				// trailing ,?
				if ($stream->test(Twig_Token::PUNCTUATION_TYPE, ']'))
				{
					break;
				}
			}
			$first = FALSE;

			$node->addElement($this->parse_expression());
		}
		$stream->expect(Twig_Token::PUNCTUATION_TYPE, ']', 'An opened array is not properly closed');

		return $node;
	}

	public function parse_hash_expression()
	{
		$stream = $this->parser->getStream();
		$stream->expect(Twig_Token::PUNCTUATION_TYPE, '{', 'A hash element was expected');

		$node = new Twig_Node_Expression_Array(array(), $stream->getCurrent()->getLine());
		$first = TRUE;
		while ( ! $stream->test(Twig_Token::PUNCTUATION_TYPE, '}'))
		{
			if ( ! $first)
			{
				$stream->expect(Twig_Token::PUNCTUATION_TYPE, ',', 'A hash value must be followed by a comma');

				// trailing ,?
				if ($stream->test(Twig_Token::PUNCTUATION_TYPE, '}'))
				{
					break;
				}
			}
			$first = FALSE;

			// a hash key can be:
			//
			//  * a number -- 12
			//  * a string -- 'a'
			//  * a name, which is equivalent to a string -- a
			//  * an expression, which must be enclosed in parentheses -- (1 + 2)
			if ($stream->test(Twig_Token::STRING_TYPE) || $stream->test(Twig_Token::NAME_TYPE) || $stream->test(Twig_Token::NUMBER_TYPE))
			{
				$token = $stream->next();
				$key = new Twig_Node_Expression_Constant($token->getValue(), $token->getLine());
			}
			elseif ($stream->test(Twig_Token::PUNCTUATION_TYPE, '('))
			{
				$key = $this->parse_expression();
			}
			else
			{
				$current = $stream->getCurrent();
				throw new Twig_Exception_Syntax(sprintf('A hash key must be a quoted string, a number, a name, or an expression enclosed in parentheses (unexpected token "%s" of value "%s"', Twig_Token::typeToEnglish($current->getType(), $current->getLine()), $current->getValue()), $current->getLine(), $this->parser->getFilename());
			}

			$stream->expect(Twig_Token::PUNCTUATION_TYPE, ':', 'A hash key must be followed by a colon (:)');
			$value = $this->parse_expression();

			$node->addElement($value, $key);
		}
		$stream->expect(Twig_Token::PUNCTUATION_TYPE, '}', 'An opened hash is not properly closed');

		return $node;
	}

	public function parse_postfix_expression($node)
	{
		while (TRUE)
		{
			$token = $this->parser->getCurrentToken();
			if ($token->getType() == Twig_Token::PUNCTUATION_TYPE)
			{
				if ('.' == $token->getValue() || '[' == $token->getValue())
				{
					$node = $this->parseSubscriptExpression($node);
				}
				elseif ('|' == $token->getValue())
				{
					$node = $this->parse_filter_expression($node);
				}
				else
				{
					break;
				}
			}
			else
			{
				break;
			}
		}

		return $node;
	}

	public function get_function_node($name, $line)
	{
		switch ($name)
		{
			case 'parent':
				$args = $this->parseArguments();
				if ( ! count($this->parser->getBlockStack()))
				{
					throw new Twig_Exception_Syntax('Calling "parent" outside a block is forbidden', $line, $this->parser->getFilename());
				}

				if ( ! $this->parser->get_parent() && ! $this->parser->hasTraits())
				{
					throw new Twig_Exception_Syntax('Calling "parent" on a template that does not extend nor "use" another template is forbidden', $line, $this->parser->getFilename());
				}

				return new Twig_Node_Expression_Parent($this->parser->peek_block_stack(), $line);
			case 'block':
				return new Twig_Node_Expression_BlockReference($this->parseArguments()->getNode(0), FALSE, $line);
			case 'attribute':
				$args = $this->parseArguments();
				if (count($args) < 2)
				{
					throw new Twig_Exception_Syntax('The "attribute" function takes at least two arguments (the variable and the attributes)', $line, $this->parser->getFilename());
				}

				return new Twig_Node_Expression_GetAttr($args->getNode(0), $args->getNode(1), count($args) > 2 ? $args->getNode(2) : new Twig_Node_Expression_Array(array(), $line), Twig_Template::ANY_CALL, $line);
			default:
				$args = $this->parseArguments(TRUE);
				if (null !== $alias = $this->parser->getImportedSymbol('macro', $name))
				{
					return new Twig_Node_Expression_MacroCall($alias['node'], $alias['name'], $this->create_array_from_arguments($args), $line);
				}

				try
				{
					$class = $this->get_function_node_class($name, $line);
				}
				catch (Twig_Exception_Syntax $e)
				{
					if ( ! $this->parser->hasMacro($name))
					{
						throw $e;
					}

					return new Twig_Node_Expression_MacroCall(new Twig_Node_Expression_Name('_self', $line), $name, $this->create_array_from_arguments($args), $line);
				}

				return new $class($name, $args, $line);
		}
	}

	public function parseSubscriptExpression($node)
	{
		$stream = $this->parser->getStream();
		$token = $stream->next();
		$lineno = $token->getLine();
		$arguments = new Twig_Node_Expression_Array(array(), $lineno);
		$type = Twig_Template::ANY_CALL;
		if ($token->getValue() == '.')
		{
			$token = $stream->next();
			if (
				$token->getType() == Twig_Token::NAME_TYPE
				||
				$token->getType() == Twig_Token::NUMBER_TYPE
				||
				($token->getType() == Twig_Token::OPERATOR_TYPE && preg_match(Twig_Lexer::REGEX_NAME, $token->getValue()))
			)
			{
				$arg = new Twig_Node_Expression_Constant($token->getValue(), $lineno);
			}
			else
			{
				throw new Twig_Exception_Syntax('Expected name or number', $lineno, $this->parser->getFilename());
			}

			if ($node instanceof Twig_Node_Expression_Name && null !== $this->parser->getImportedSymbol('template', $node->get_attribute('name')))
			{
				if ( ! $arg instanceof Twig_Node_Expression_Constant)
				{
					throw new Twig_Exception_Syntax(sprintf('Dynamic macro names are not supported (called on "%s")', $node->get_attribute('name')), $token->getLine(), $this->parser->getFilename());
				}
				$arguments = $this->create_array_from_arguments($this->parseArguments(TRUE));

				return new Twig_Node_Expression_MacroCall($node, $arg->get_attribute('value'), $arguments, $lineno);
			}

			if ($stream->test(Twig_Token::PUNCTUATION_TYPE, '('))
			{
				$type = Twig_Template::METHOD_CALL;
				$arguments = $this->create_array_from_arguments($this->parseArguments());
			}
		}
		else
		{
			$type = Twig_Template::ARRAY_CALL;

			// slice?
			$slice = FALSE;
			if ($stream->test(Twig_Token::PUNCTUATION_TYPE, ':'))
			{
				$slice = TRUE;
				$arg = new Twig_Node_Expression_Constant(0, $token->getLine());
			}
			else
			{
				$arg = $this->parse_expression();
			}

			if ($stream->test(Twig_Token::PUNCTUATION_TYPE, ':'))
			{
				$slice = TRUE;
				$stream->next();
			}

			if ($slice)
			{
				if ($stream->test(Twig_Token::PUNCTUATION_TYPE, ']'))
				{
					$length = new Twig_Node_Expression_Constant(null, $token->getLine());
				}
				else
				{
					$length = $this->parse_expression();
				}

				$class = $this->get_filter_node_class('slice', $token->getLine());
				$arguments = new Twig_Node(array($arg, $length));
				$filter = new $class($node, new Twig_Node_Expression_Constant('slice', $token->getLine()), $arguments, $token->getLine());

				$stream->expect(Twig_Token::PUNCTUATION_TYPE, ']');

				return $filter;
			}

			$stream->expect(Twig_Token::PUNCTUATION_TYPE, ']');
		}

		return new Twig_Node_Expression_GetAttr($node, $arg, $arguments, $type, $lineno);
	}

	public function parse_filter_expression($node)
	{
		$this->parser->getStream()->next();

		return $this->parse_filter_expression_raw($node);
	}

	public function parse_filter_expression_raw($node, $tag = NULL)
	{
		while (TRUE)
		{
			$token = $this->parser->getStream()->expect(Twig_Token::NAME_TYPE);

			$name = new Twig_Node_Expression_Constant($token->getValue(), $token->getLine());
			if ( ! $this->parser->getStream()->test(Twig_Token::PUNCTUATION_TYPE, '('))
			{
				$arguments = new Twig_Node();
			}
			else
			{
				$arguments = $this->parseArguments(TRUE);
			}

			$class = $this->get_filter_node_class($name->get_attribute('value'), $token->getLine());

			$node = new $class($node, $name, $arguments, $token->getLine(), $tag);

			if ( ! $this->parser->getStream()->test(Twig_Token::PUNCTUATION_TYPE, '|'))
			{
				break;
			}
			$this->parser->getStream()->next();
		}

		return $node;
	}

	/**
	 * Parses arguments.
	 *
	 * @param Boolean $namedArguments Whether to allow named arguments or not
	 * @param Boolean $definition     Whether we are parsing arguments for a function definition
	 *
	 * @return Twig_Node
	 */
	public function parseArguments($namedArguments = FALSE, $definition = FALSE)
	{
		$args = array();
		$stream = $this->parser->getStream();

		$stream->expect(Twig_Token::PUNCTUATION_TYPE, '(', 'A list of arguments must begin with an opening parenthesis');
		while ( ! $stream->test(Twig_Token::PUNCTUATION_TYPE, ')'))
		{
			if ( ! empty($args))
			{
				$stream->expect(Twig_Token::PUNCTUATION_TYPE, ',', 'Arguments must be separated by a comma');
			}

			if ($definition)
			{
				$token = $stream->expect(Twig_Token::NAME_TYPE, null, 'An argument must be a name');
				$value = new Twig_Node_Expression_Name($token->getValue(), $this->parser->getCurrentToken()->getLine());
			}
			else
			{
				$value = $this->parse_expression();
			}

			$name = NULL;
			if ($namedArguments && $stream->test(Twig_Token::OPERATOR_TYPE, '='))
			{
				$token = $stream->next();
				if ( ! $value instanceof Twig_Node_Expression_Name)
				{
					throw new Twig_Exception_Syntax(sprintf('A parameter name must be a string, "%s" given', get_class($value)), $token->getLine(), $this->parser->getFilename());
				}
				$name = $value->get_attribute('name');

				if ($definition)
				{
					$value = $this->parse_primary_expression();

					if ( ! $this->check_constant_expression($value))
					{
						throw new Twig_Exception_Syntax('A default value for an argument must be a constant (a boolean, a string, a number, or an array).', $token->getLine(), $this->parser->getFilename());
					}
				}
				else
				{
					$value = $this->parse_expression();
				}
			}

			if ($definition && $name === NULL)
			{
				$name = $value->get_attribute('name');
				$value = new Twig_Node_Expression_Constant(null, $this->parser->getCurrentToken()->getLine());
			}

			if ($name === NULL)
			{
				$args[] = $value;
			}
			else
			{
				if ($definition && isset($args[$name]))
				{
					throw new Twig_Exception_Syntax(sprintf('Arguments cannot contain the same argument name more than once ("%s" is defined twice).', $name), $token->getLine(), $this->parser->getFilename());
				}
				$args[$name] = $value;
			}
		}
		$stream->expect(Twig_Token::PUNCTUATION_TYPE, ')', 'A list of arguments must be closed by a parenthesis');

		return new Twig_Node($args);
	}

	public function parse_assignment_expression()
	{
		$targets = array();
		while (TRUE)
		{
			$token = $this->parser->getStream()->expect(Twig_Token::NAME_TYPE, null, 'Only variables can be assigned to');
			if (in_array($token->getValue(), array('true', 'false', 'none')))
			{
				throw new Twig_Exception_Syntax(sprintf('You cannot assign a value to "%s"', $token->getValue()), $token->getLine(), $this->parser->getFilename());
			}
			$targets[] = new Twig_Node_Expression_AssignName($token->getValue(), $token->getLine());

			if ( ! $this->parser->getStream()->test(Twig_Token::PUNCTUATION_TYPE, ','))
			{
				break;
			}
			$this->parser->getStream()->next();
		}

		return new Twig_Node($targets);
	}

	public function parse_multitarget_expression()
	{
		$targets = array();
		while (TRUE)
		{
			$targets[] = $this->parse_expression();
			if ( ! $this->parser->getStream()->test(Twig_Token::PUNCTUATION_TYPE, ','))
			{
				break;
			}
			$this->parser->getStream()->next();
		}

		return new Twig_Node($targets);
	}

	protected function get_function_node_class($name, $line)
	{
		$env = $this->parser->get_environment();

		if (FALSE === $function = $env->getFunction($name))
		{
			$message = sprintf('The function "%s" does not exist', $name);
			if ($alternatives = $env->computeAlternatives($name, array_keys($env->get_functions())))
			{
				$message = sprintf('%s. Did you mean "%s"', $message, implode('", "', $alternatives));
			}

			throw new Twig_Exception_Syntax($message, $line, $this->parser->getFilename());
		}

		if ($function instanceof Twig_Simple_Function)
		{
			return $function->get_node_class();
		}
		return $function instanceof Twig_Function_Node ? $function->getClass() : 'Twig_Node_Expression_Function';
	}

	protected function get_filter_node_class($name, $line)
	{
		$env = $this->parser->get_environment();

		if (FALSE === $filter = $env->getFilter($name))
		{
			$message = sprintf('The filter "%s" does not exist', $name);
			if ($alternatives = $env->computeAlternatives($name, array_keys($env->get_filters())))
			{
				$message = sprintf('%s. Did you mean "%s"', $message, implode('", "', $alternatives));
			}

			throw new Twig_Exception_Syntax($message, $line, $this->parser->getFilename());
		}

		if ($filter instanceof Twig_Simple_Filter)
		{
			return $filter->get_node_class();
		}
		return $filter instanceof Twig_Filter_Node ? $filter->getClass() : 'Twig_Node_Expression_Filter';
	}

    // checks that the node only contains "constant" elements
	protected function check_constant_expression(Twig_Node $node)
	{
		if ( ! ($node instanceof Twig_Node_Expression_Constant || $node instanceof Twig_Node_Expression_Array))
		{
			return FALSE;
		}

		foreach ($node AS $n)
		{
			if ( ! $this->check_constant_expression($n))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

    private function create_array_from_arguments(Twig_Node $arguments, $line = NULL)
	{
		$line = ($line === NULL) ? $arguments->getLine() : $line;
		$array = new Twig_Node_Expression_Array(array(), $line);
		foreach ($arguments AS $key => $value)
		{
			$array->addElement($value, new Twig_Node_Expression_Constant($key, $value->getLine()));
		}

		return $array;
	}
}
