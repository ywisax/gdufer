<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Imports blocks defined in another template into the current template.
 *
 * <pre>
 * {% extends "base.html" %}
 *
 * {% use "blocks.html" %}
 *
 * {% block title %}{% endblock %}
 * {% block content %}{% endblock %}
 * </pre>
 *
 * @see http://www.twig-project.org/doc/templates.html#horizontal-reuse for details.
 *
 * @package    Kohana/Twig
 * @category   Token
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Token_Parser_Use extends Twig_Token_Parser {

	/**
	 * Parses a token and returns a node.
	 *
	 * @param Twig_Token $token A Twig_Token instance
	 *
	 * @return Twig_Node A Twig_Node instance
	 */
	public function parse(Twig_Token $token)
	{
		$template = $this->parser->get_expression_parser()->parse_expression();
		$stream = $this->parser->getStream();

		if ( ! $template instanceof Twig_Node_Expression_Constant)
		{
			throw new Twig_Exception_Syntax('The template references in a "use" statement must be a string.', $stream->getCurrent()->getLine(), $stream->getFilename());
		}

		$targets = array();
		if ($stream->test('with'))
		{
			$stream->next();

			do
			{
				$name = $stream->expect(Twig_Token::NAME_TYPE)->getValue();

				$alias = $name;
				if ($stream->test('as'))
				{
					$stream->next();

					$alias = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
				}

				$targets[$name] = new Twig_Node_Expression_Constant($alias, -1);

				if ( ! $stream->test(Twig_Token::PUNCTUATION_TYPE, ','))
				{
					break;
				}

				$stream->next();
			}
			while (TRUE);
		}

		$stream->expect(Twig_Token::BLOCK_END_TYPE);

		$this->parser->addTrait(new Twig_Node(array('template' => $template, 'targets' => new Twig_Node($targets))));
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'use';
	}
}
