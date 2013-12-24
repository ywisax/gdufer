<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Marks a section of a template to be escaped or not.
 *
 * <pre>
 * {% autoescape true %}
 *   Everything will be automatically escaped in this block
 * {% endautoescape %}
 *
 * {% autoescape false %}
 *   Everything will be outputed as is in this block
 * {% endautoescape %}
 *
 * {% autoescape true js %}
 *   Everything will be automatically escaped in this block
 *   using the js escaping strategy
 * {% endautoescape %}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_AutoEscape extends Twig_Token_Parser {

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
		$stream = $this->parser->getStream();

		if ($stream->test(Twig_Token::BLOCK_END_TYPE))
		{
			$value = 'html';
		}
		else
		{
			$expr = $this->parser->get_expression_parser()->parse_expression();
			if ( ! $expr instanceof Twig_Node_Expression_Constant)
			{
				throw new Twig_Exception_Syntax('An escaping strategy must be a string or a Boolean.', $stream->getCurrent()->getLine(), $stream->getFilename());
			}
			$value = $expr->get_attribute('value');

			$compat = true === $value || false === $value;

			if ($value === TRUE)
			{
				$value = 'html';
			}

			if ($compat && $stream->test(Twig_Token::NAME_TYPE))
			{
				if ($value === FALSE)
				{
					throw new Twig_Exception_Syntax('Unexpected escaping strategy as you set autoescaping to false.', $stream->getCurrent()->getLine(), $stream->getFilename());
				}

				$value = $stream->next()->getValue();
			}
		}

		$stream->expect(Twig_Token::BLOCK_END_TYPE);
		$body = $this->parser->subparse(array($this, 'decide_block_end'), TRUE);
		$stream->expect(Twig_Token::BLOCK_END_TYPE);

		return new Twig_Node_AutoEscape($value, $body, $lineno, $this->getTag());
	}

	public function decide_block_end(Twig_Token $token)
	{
		return $token->test('endautoescape');
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'autoescape';
	}
}
