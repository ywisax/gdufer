<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Embeds a template.
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_Embed extends Twig_Token_Parser_Include {

	/**
	 * Parses a token and returns a node.
	 *
	 * @param Twig_Token $token A Twig_Token instance
	 *
	 * @return Twig_Node A Twig_Node instance
	 */
	public function parse(Twig_Token $token)
	{
		$stream = $this->parser->getStream();

		$parent = $this->parser->get_expression_parser()->parse_expression();

		list($variables, $only, $ignoreMissing) = $this->parseArguments();

		// inject a fake parent to make the parent() function work
		$stream->inject_tokens(array(
			new Twig_Token(Twig_Token::BLOCK_START_TYPE, '', $token->getLine()),
			new Twig_Token(Twig_Token::NAME_TYPE, 'extends', $token->getLine()),
			new Twig_Token(Twig_Token::STRING_TYPE, '__parent__', $token->getLine()),
			new Twig_Token(Twig_Token::BLOCK_END_TYPE, '', $token->getLine()),
		));

		$module = $this->parser->parse($stream, array($this, 'decide_block_end'), TRUE);

		// override the parent with the correct one
		$module->setNode('parent', $parent);

		$this->parser->embedTemplate($module);

		$stream->expect(Twig_Token::BLOCK_END_TYPE);

		return new Twig_Node_Embed($module->get_attribute('filename'), $module->get_attribute('index'), $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
	}

	public function decide_block_end(Twig_Token $token)
	{
		return $token->test('endembed');
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'embed';
	}
}
