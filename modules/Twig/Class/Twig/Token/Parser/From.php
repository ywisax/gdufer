<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Imports macros.
 *
 * <pre>
 *   {% from 'forms.html' import forms %}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_From extends Twig_Token_Parser {

	/**
	 * Parses a token and returns a node.
	 *
	 * @param Twig_Token $token A Twig_Token instance
	 *
	 * @return Twig_Node A Twig_Node instance
	 */
	public function parse(Twig_Token $token)
	{
		$macro = $this->parser->get_expression_parser()->parse_expression();
		$stream = $this->parser->getStream();
		$stream->expect('import');

		$targets = array();
		do
		{
			$name = $stream->expect(Twig_Token::NAME_TYPE)->getValue();

			$alias = $name;
			if ($stream->test('as'))
			{
				$stream->next();

				$alias = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
			}

			$targets[$name] = $alias;

			if ( ! $stream->test(Twig_Token::PUNCTUATION_TYPE, ','))
			{
				break;
			}

			$stream->next();
		}
		while (TRUE);

		$stream->expect(Twig_Token::BLOCK_END_TYPE);

		$node = new Twig_Node_Import($macro, new Twig_Node_Expression_AssignName($this->parser->getVarName(), $token->getLine()), $token->getLine(), $this->getTag());

		foreach ($targets AS $name => $alias)
		{
			$this->parser->addImportedSymbol('macro', $alias, $name, $node->getNode('var'));
		}

		return $node;
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'from';
	}
}
