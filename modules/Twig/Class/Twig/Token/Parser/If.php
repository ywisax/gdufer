<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Tests a condition.
 *
 * <pre>
 * {% if users %}
 *  <ul>
 *    {% for user in users %}
 *      <li>{{ user.username|e }}</li>
 *    {% endfor %}
 *  </ul>
 * {% endif %}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_If extends Twig_Token_Parser {

	/**
	 * Parses a token and returns a node.
	 *
	 * @param Twig_Token $token A Twig_Token instance
	 *
	 * @return Twig_Node A Twig_Node instance
	 */
	public function parse(Twig_Token $token)
	{
		$lineno = $token->getLine();
		$expr = $this->parser->get_expression_parser()->parse_expression();
		$stream = $this->parser->getStream();
		$stream->expect(Twig_Token::BLOCK_END_TYPE);
		$body = $this->parser->subparse(array($this, 'decideIfFork'));
		$tests = array($expr, $body);
		$else = NULL;

		$end = FALSE;
		while ( ! $end)
		{
			switch ($stream->next()->getValue())
			{
				case 'else':
					$stream->expect(Twig_Token::BLOCK_END_TYPE);
					$else = $this->parser->subparse(array($this, 'decideIfEnd'));
					break;

				case 'elseif':
					$expr = $this->parser->get_expression_parser()->parse_expression();
					$stream->expect(Twig_Token::BLOCK_END_TYPE);
					$body = $this->parser->subparse(array($this, 'decideIfFork'));
					$tests[] = $expr;
					$tests[] = $body;
					break;

				case 'endif':
					$end = TRUE;
					break;

				default:
					throw new Twig_Exception_Syntax(sprintf('Unexpected end of template. Twig was looking for the following tags "else", "elseif", or "endif" to close the "if" block started at line %d)', $lineno), $stream->getCurrent()->getLine(), $stream->getFilename());
			}
		}

		$stream->expect(Twig_Token::BLOCK_END_TYPE);

		return new Twig_Node_If(new Twig_Node($tests), $else, $lineno, $this->getTag());
	}

	public function decideIfFork(Twig_Token $token)
	{
		return $token->test(array('elseif', 'else', 'endif'));
	}

	public function decideIfEnd(Twig_Token $token)
	{
		return $token->test(array('endif'));
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'if';
	}
}
