<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Includes a template.
 *
 * <pre>
 *   {% include 'header.html' %}
 *     Body
 *   {% include 'footer.html' %}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_Include extends Twig_Token_Parser {

	/**
	 * Parses a token and returns a node.
	 *
	 * @param Twig_Token $token A Twig_Token instance
	 *
	 * @return Twig_Node A Twig_Node instance
	 */
	public function parse(Twig_Token $token)
	{
		$expr = $this->parser->get_expression_parser()->parse_expression();

		list($variables, $only, $ignoreMissing) = $this->parseArguments();

		return new Twig_Node_Include($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
	}

	protected function parseArguments()
	{
		$stream = $this->parser->getStream();

		$ignoreMissing = FALSE;
		if ($stream->test(Twig_Token::NAME_TYPE, 'ignore'))
		{
			$stream->next();
			$stream->expect(Twig_Token::NAME_TYPE, 'missing');

			$ignoreMissing = TRUE;
		}

		$variables = NULL;
		if ($stream->test(Twig_Token::NAME_TYPE, 'with'))
		{
			$stream->next();
			$variables = $this->parser->get_expression_parser()->parse_expression();
		}

		$only = FALSE;
		if ($stream->test(Twig_Token::NAME_TYPE, 'only'))
		{
			$stream->next();
			$only = TRUE;
		}

		$stream->expect(Twig_Token::BLOCK_END_TYPE);

		return array($variables, $only, $ignoreMissing);
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'include';
	}
}
