<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Default parser implementation.
 *
 * @package    Kohana/Twig
 * @category   Parser
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Parser {

	protected $stack = array();
	protected $stream;
	protected $parent;
	protected $handlers;
	protected $visitors;
	protected $expression_parser;
	protected $blocks;
	protected $blockStack;
	protected $macros;
	protected $env;
	protected $reserved_macro_names;
	protected $imported_symbols;
	protected $traits;
	protected $embedded_templates = array();

	/**
	 * Constructor.
	 *
	 * @param Twig_Environment $env A Twig_Environment instance
	 */
	public function __construct(Twig_Environment $env)
	{
		$this->env = $env;
	}

	public function get_environment()
	{
		return $this->env;
	}

	public function getVarName()
	{
		return sprintf('__internal_%s', hash('sha256', uniqid(mt_rand(), TRUE), FALSE));
	}

	public function getFilename()
	{
		return $this->stream->getFilename();
	}

	/**
	 * Converts a token stream to a node tree.
	 *
	 * @param Twig_Token_Stream $stream A token stream instance
	 *
	 * @return Twig_Node_Module A node tree
	 */
	public function parse(Twig_Token_Stream $stream, $test = NULL, $dropNeedle = FALSE)
	{
		// push all variables into the stack to keep the current state of the parser
		$vars = get_object_vars($this);
		unset($vars['stack'], $vars['env'], $vars['handlers'], $vars['visitors'], $vars['expression_parser']);
		$this->stack[] = $vars;

		// tag handlers
		if ($this->handlers === NULL)
		{
			$this->handlers = $this->env->get_token_parsers();
			$this->handlers->setParser($this);
		}

		// node visitors
		if ($this->visitors === NULL)
		{
			$this->visitors = $this->env->get_node_visitors();
		}

		if ($this->expression_parser === NULL)
		{
			$this->expression_parser = new Twig_Expression($this, $this->env->get_unary_operators(), $this->env->getBinaryOperators());
		}

		$this->stream = $stream;
		$this->parent = NULL;
		$this->blocks = array();
		$this->macros = array();
		$this->traits = array();
		$this->blockStack = array();
		$this->imported_symbols = array(array());
		$this->embedded_templates = array();

		try
		{
			$body = $this->subparse($test, $dropNeedle);

			if ($this->parent !== NULL)
			{
				if (null === $body = $this->filterBodyNodes($body))
				{
					$body = new Twig_Node();
				}
			}
		}
		catch (Twig_Exception_Syntax $e)
		{
			if ( ! $e->get_template_file())
			{
				$e->setTemplateFile($this->getFilename());
			}

			if ( ! $e->getTemplateLine())
			{
				$e->setTemplateLine($this->stream->getCurrent()->getLine());
			}

			throw $e;
		}

		$node = new Twig_Node_Module(new Twig_Node_Body(array($body)), $this->parent, new Twig_Node($this->blocks), new Twig_Node($this->macros), new Twig_Node($this->traits), $this->embedded_templates, $this->getFilename());

		$traverser = new Twig_Node_Traverser($this->env, $this->visitors);

		$node = $traverser->traverse($node);

		// restore previous stack so previous parse() call can resume working
		foreach (array_pop($this->stack) AS $key => $val)
		{
			$this->$key = $val;
		}

		return $node;
	}

	public function subparse($test, $dropNeedle = FALSE)
	{
		$lineno = $this->getCurrentToken()->getLine();
		$rv = array();
		while ( ! $this->stream->is_eof())
		{
			switch ($this->getCurrentToken()->getType())
			{
				case Twig_Token::TEXT_TYPE:
					$token = $this->stream->next();
					$rv[] = new Twig_Node_Text($token->getValue(), $token->getLine());
					break;

				case Twig_Token::VAR_START_TYPE:
					$token = $this->stream->next();
					$expr = $this->expression_parser->parse_expression();
					$this->stream->expect(Twig_Token::VAR_END_TYPE);
					$rv[] = new Twig_Node_Print($expr, $token->getLine());
					break;

				case Twig_Token::BLOCK_START_TYPE:
					$this->stream->next();
					$token = $this->getCurrentToken();

					if ($token->getType() !== Twig_Token::NAME_TYPE)
					{
						throw new Twig_Exception_Syntax('A block must start with a tag name', $token->getLine(), $this->getFilename());
					}

					if (null !== $test && call_user_func($test, $token))
					{
						if ($dropNeedle)
						{
						    $this->stream->next();
						}

						if (count($rv) === 1)
						{
						    return $rv[0];
						}

						return new Twig_Node($rv, array(), $lineno);
					}

					$subparser = $this->handlers->getTokenParser($token->getValue());
					if ($subparser === NULL)
					{
						if (null !== $test)
						{
						    $error = sprintf('Unexpected tag name "%s"', $token->getValue());
							if (is_array($test) && isset($test[0]) && $test[0] instanceof Twig_Token_Parser)
							{
						        $error .= sprintf(' (expecting closing tag for the "%s" tag defined near line %s)', $test[0]->getTag(), $lineno);
							}

						    throw new Twig_Exception_Syntax($error, $token->getLine(), $this->getFilename());
						}

						$message = sprintf('Unknown tag name "%s"', $token->getValue());
						if ($alternatives = $this->env->computeAlternatives($token->getValue(), array_keys($this->env->getTags())))
						{
						    $message = sprintf('%s. Did you mean "%s"', $message, implode('", "', $alternatives));
						}

						throw new Twig_Exception_Syntax($message, $token->getLine(), $this->getFilename());
					}

					$this->stream->next();

					$node = $subparser->parse($token);
					if (null !== $node)
					{
						$rv[] = $node;
					}
					break;

				default:
					throw new Twig_Exception_Syntax('Lexer or parser ended up in unsupported state.', 0, $this->getFilename());
			}
		}

		if (count($rv) === 1)
		{
			return $rv[0];
		}

		return new Twig_Node($rv, array(), $lineno);
	}

	public function addHandler($name, $class)
	{
		$this->handlers[$name] = $class;
	}

	public function add_node_visitor(Twig_Node_Visitor $visitor)
	{
		$this->visitors[] = $visitor;
	}

	public function getBlockStack()
	{
		return $this->blockStack;
	}

	public function peek_block_stack()
	{
		return $this->blockStack[count($this->blockStack) - 1];
	}

	public function popBlockStack()
	{
		array_pop($this->blockStack);
	}

	public function push_block_stack($name)
	{
		$this->blockStack[] = $name;
	}

	public function has_block($name)
	{
		return isset($this->blocks[$name]);
	}

	public function getBlock($name)
	{
		return $this->blocks[$name];
	}

	public function setBlock($name, $value)
	{
		$this->blocks[$name] = new Twig_Node_Body(array($value), array(), $value->getLine());
	}

	public function hasMacro($name)
	{
		return isset($this->macros[$name]);
	}

	public function setMacro($name, Twig_Node_Macro $node)
	{
		if ($this->reserved_macro_names === NULL)
		{
			$this->reserved_macro_names = array();
			$r = new ReflectionClass($this->env->base_template_class());
			foreach ($r->getMethods() AS $method)
			{
				$this->reserved_macro_names[] = $method->getName();
			}
		}

		if (in_array($name, $this->reserved_macro_names))
		{
			throw new Twig_Exception_Syntax(sprintf('"%s" cannot be used as a macro name as it is a reserved keyword', $name), $node->getLine(), $this->getFilename());
		}

		$this->macros[$name] = $node;
	}

	public function addTrait($trait)
	{
		$this->traits[] = $trait;
	}

	public function hasTraits()
	{
		return count($this->traits) > 0;
	}

	public function embedTemplate(Twig_Node_Module $template)
	{
		$template->set_index(mt_rand());

		$this->embedded_templates[] = $template;
	}

	public function addImportedSymbol($type, $alias, $name = NULL, Twig_Node_Expression $node = NULL)
	{
		$this->imported_symbols[0][$type][$alias] = array('name' => $name, 'node' => $node);
	}

	public function getImportedSymbol($type, $alias)
	{
		foreach ($this->imported_symbols AS $functions)
		{
			if (isset($functions[$type][$alias]))
			{
				return $functions[$type][$alias];
			}
		}
	}

	public function is_main_scope()
	{
		return 1 === count($this->imported_symbols);
	}

	public function pushLocalScope()
	{
		array_unshift($this->imported_symbols, array());
	}

	public function popLocalScope()
	{
		array_shift($this->imported_symbols);
	}

	/**
	 * Gets the expression parser.
	 *
	 * @return Twig_Expression The expression parser
	 */
	public function get_expression_parser()
	{
		return $this->expression_parser;
	}

	public function get_parent()
	{
		return $this->parent;
	}

	public function setParent($parent)
	{
		$this->parent = $parent;
	}

	/**
	 * Gets the token stream.
	 *
	 * @return Twig_Token_Stream The token stream
	 */
	public function getStream()
	{
		return $this->stream;
	}

	/**
	 * Gets the current token.
	 *
	 * @return Twig_Token The current token
	 */
	public function getCurrentToken()
	{
		return $this->stream->getCurrent();
	}

	protected function filterBodyNodes(Twig_Node $node)
	{
		// check that the body does not contain non-empty output nodes
		if (
			($node instanceof Twig_Node_Text && !ctype_space($node->get_attribute('data')))
			||
			( ! $node instanceof Twig_Node_Text && !$node instanceof Twig_Node_BlockReference && $node instanceof Twig_Node)
		)
		{
			if (strpos((string) $node, chr(0xEF).chr(0xBB).chr(0xBF)) !== FALSE)
			{
				throw new Twig_Exception_Syntax('A template that extends another one cannot have a body but a byte order mark (BOM) has been detected; it must be removed.', $node->getLine(), $this->getFilename());
			}
			throw new Twig_Exception_Syntax('A template that extends another one cannot have a body.', $node->getLine(), $this->getFilename());
		}

		// bypass "set" nodes as they "capture" the output
		if ($node instanceof Twig_Node_Set)
		{
			return $node;
		}

		if ($node instanceof Twig_Node)
		{
			return;
		}

		foreach ($node AS $k => $n)
		{
			if (null !== $n && null === $n = $this->filterBodyNodes($n))
			{
				$node->removeNode($k);
			}
		}

		return $node;
	}
}
