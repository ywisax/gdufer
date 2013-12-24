<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Remove whitespaces between HTML tags.
 *
 * <pre>
 * {% spaceless %}
 *      <div>
 *          <strong>foo</strong>
 *      </div>
 * {% endspaceless %}
 * {# output will be <div><strong>foo</strong></div> #}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_Spaceless extends Twig_Token_Parser {

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

		$this->parser
			->getStream()
			->expect(Twig_Token::BLOCK_END_TYPE);
		$body = $this
			->parser
			->subparse(array($this, 'decideSpacelessEnd'), TRUE);
		$this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

		return new Twig_Node_Spaceless($body, $lineno, $this->getTag());
	}

	public function decideSpacelessEnd(Twig_Token $token)
	{
		return $token->test('endspaceless');
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'spaceless';
	}
}
