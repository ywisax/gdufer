<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Marks a section of a template as being reusable.
 *
 * <pre>
 *  {% block head %}
 *    <link rel="stylesheet" href="style.css" />
 *    <title>{% block title %}{% endblock %} - My Webpage</title>
 *  {% endblock %}
 * </pre>
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_Block extends Twig_Token_Parser {

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
		$name = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
		if ($this->parser->has_block($name))
		{
			throw new Twig_Exception_Syntax(sprintf("The block '$name' has already been defined line %d", $this->parser->getBlock($name)->getLine()), $stream->getCurrent()->getLine(), $stream->getFilename());
		}
		$this->parser->setBlock($name, $block = new Twig_Node_Block($name, new Twig_Node(array()), $lineno));
		$this->parser->pushLocalScope();
		$this->parser->push_block_stack($name);

		if ($stream->test(Twig_Token::BLOCK_END_TYPE))
		{
			$stream->next();

			$body = $this->parser->subparse(array($this, 'decide_block_end'), TRUE);
			if ($stream->test(Twig_Token::NAME_TYPE))
			{
				$value = $stream->next()->getValue();

				if ($value != $name)
				{
					throw new Twig_Exception_Syntax(sprintf("Expected endblock for block '$name' (but %s given)", $value), $stream->getCurrent()->getLine(), $stream->getFilename());
				}
			}
		}
		else
		{
			$body = new Twig_Node(array(
				new Twig_Node_Print($this->parser->get_expression_parser()->parse_expression(), $lineno),
			));
		}
		$stream->expect(Twig_Token::BLOCK_END_TYPE);

		$block->setNode('body', $body);
		$this->parser->popBlockStack();
		$this->parser->popLocalScope();

		return new Twig_Node_BlockReference($name, $lineno, $this->getTag());
	}

	public function decide_block_end(Twig_Token $token)
	{
		return $token->test('endblock');
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'block';
	}
}
