<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Lexes a template string.
 *
 * @package    Kohana/Twig
 * @category   Lexer
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Lexer {

	protected $tokens;
	protected $code;
	protected $cursor;
	protected $lineno;
	protected $end;
	protected $state;
	protected $states;
	protected $brackets;
	protected $env;
	protected $filename;
	protected $options;
	protected $regexes;
	protected $position;
	protected $positions;
	protected $current_var_block_line;

	const STATE_DATA            = 0;
	const STATE_BLOCK           = 1;
	const STATE_VAR             = 2;
	const STATE_STRING          = 3;
	const STATE_INTERPOLATION   = 4;

	const REGEX_NAME            = '/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/A';
	const REGEX_NUMBER          = '/[0-9]+(?:\.[0-9]+)?/A';
	const REGEX_STRING          = '/"([^#"\\\\]*(?:\\\\.[^#"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'/As';
	const REGEX_DQ_STRING_DELIM = '/"/A';
	const REGEX_DQ_STRING_PART  = '/[^#"\\\\]*(?:(?:\\\\.|#(?!\{))[^#"\\\\]*)*/As';
	const PUNCTUATION           = '()[]{}?:.,|';

	public function __construct(Twig_Environment $env, array $options = array())
	{
		$this->env = $env;

		$this->options = array_merge(array(
			'tag_comment'     => array('{#', '#}'),
			'tag_block'       => array('{%', '%}'),
			'tag_variable'    => array('{{', '}}'),
			'whitespace_trim' => '-',
			'interpolation'   => array('#{', '}'),
		), $options);

		$this->regexes = array(
			'lex_var'             => '/\s*'.preg_quote($this->options['whitespace_trim'].$this->options['tag_variable'][1], '/').'\s*|\s*'.preg_quote($this->options['tag_variable'][1], '/').'/A',
			'lex_block'           => '/\s*(?:'.preg_quote($this->options['whitespace_trim'].$this->options['tag_block'][1], '/').'\s*|\s*'.preg_quote($this->options['tag_block'][1], '/').')\n?/A',
			'lex_raw_data'        => '/('.preg_quote($this->options['tag_block'][0].$this->options['whitespace_trim'], '/').'|'.preg_quote($this->options['tag_block'][0], '/').')\s*(?:end%s)\s*(?:'.preg_quote($this->options['whitespace_trim'].$this->options['tag_block'][1], '/').'\s*|\s*'.preg_quote($this->options['tag_block'][1], '/').')/s',
			'operator'            => $this->getOperatorRegex(),
			'lex_comment'         => '/(?:'.preg_quote($this->options['whitespace_trim'], '/').preg_quote($this->options['tag_comment'][1], '/').'\s*|'.preg_quote($this->options['tag_comment'][1], '/').')\n?/s',
			'lex_block_raw'       => '/\s*(raw|verbatim)\s*(?:'.preg_quote($this->options['whitespace_trim'].$this->options['tag_block'][1], '/').'\s*|\s*'.preg_quote($this->options['tag_block'][1], '/').')/As',
			'lex_block_line'      => '/\s*line\s+(\d+)\s*'.preg_quote($this->options['tag_block'][1], '/').'/As',
			'lex_tokens_start'    => '/('.preg_quote($this->options['tag_variable'][0], '/').'|'.preg_quote($this->options['tag_block'][0], '/').'|'.preg_quote($this->options['tag_comment'][0], '/').')('.preg_quote($this->options['whitespace_trim'], '/').')?/s',
			'interpolation_start' => '/'.preg_quote($this->options['interpolation'][0], '/').'\s*/A',
			'interpolation_end'   => '/\s*'.preg_quote($this->options['interpolation'][1], '/').'/A',
		);
	}

	/**
	 * Tokenizes a source code.
	 *
	 * @param string $code     The source code
	 * @param string $filename A unique identifier for the source code
	 *
	 * @return Twig_Token_Stream A token stream instance
	 */
	public function tokenize($code, $filename = NULL)
	{
		if (function_exists('mb_internal_encoding') && ((int) ini_get('mbstring.func_overload')) & 2)
		{
			$mbEncoding = mb_internal_encoding();
			mb_internal_encoding('ASCII');
		}

		$this->code = str_replace(array("\r\n", "\r"), "\n", $code);
		$this->filename = $filename;
		$this->cursor = 0;
		$this->lineno = 1;
		$this->end = strlen($this->code);
		$this->tokens = array();
		$this->state = self::STATE_DATA;
		$this->states = array();
		$this->brackets = array();
		$this->position = -1;

		// find all token starts in one go
		preg_match_all($this->regexes['lex_tokens_start'], $this->code, $matches, PREG_OFFSET_CAPTURE);
		$this->positions = $matches;

		while ($this->cursor < $this->end)
		{
			// dispatch to the lexing functions depending
			// on the current state
			switch ($this->state)
			{
				case self::STATE_DATA:
					$this->lex_data();
					break;

				case self::STATE_BLOCK:
					$this->lex_block();
					break;

				case self::STATE_VAR:
					$this->lex_var();
					break;

				case self::STATE_STRING:
					$this->lex_string();
					break;

				case self::STATE_INTERPOLATION:
					$this->lex_interpolation();
					break;
			}
		}

		$this->pushToken(Twig_Token::EOF_TYPE);

		if ( ! empty($this->brackets))
		{
			list($expect, $lineno) = array_pop($this->brackets);
			throw new Twig_Exception_Syntax(sprintf('Unclosed "%s"', $expect), $lineno, $this->filename);
		}

		if (isset($mbEncoding))
		{
			mb_internal_encoding($mbEncoding);
		}

		return new Twig_Token_Stream($this->tokens, $this->filename);
	}

	protected function lex_data()
	{
		// if no matches are left we return the rest of the template as simple text token
		if ($this->position == count($this->positions[0]) - 1)
		{
			$this->pushToken(Twig_Token::TEXT_TYPE, substr($this->code, $this->cursor));
			$this->cursor = $this->end;

			return;
		}

		// Find the first token after the current cursor
		$position = $this->positions[0][++$this->position];
		while ($position[1] < $this->cursor)
		{
			if ($this->position == count($this->positions[0]) - 1)
			{
				return;
			}
			$position = $this->positions[0][++$this->position];
		}

		// push the template text first
		$text = $textContent = substr($this->code, $this->cursor, $position[1] - $this->cursor);
		if (isset($this->positions[2][$this->position][0]))
		{
			$text = rtrim($text);
		}
		$this->pushToken(Twig_Token::TEXT_TYPE, $text);
		$this->move_cursor($textContent.$position[0]);

		switch ($this->positions[1][$this->position][0])
		{
			case $this->options['tag_comment'][0]:
				$this->lex_comment();
				break;

			case $this->options['tag_block'][0]:
				// raw data?
				if (preg_match($this->regexes['lex_block_raw'], $this->code, $match, null, $this->cursor))
				{
					$this->move_cursor($match[0]);
					$this->lex_raw_data($match[1]);
				}
				// {% line \d+ %}
				elseif (preg_match($this->regexes['lex_block_line'], $this->code, $match, null, $this->cursor))
				{
					$this->move_cursor($match[0]);
					$this->lineno = (int) $match[1];
				}
				else
				{
					$this->pushToken(Twig_Token::BLOCK_START_TYPE);
					$this->pushState(self::STATE_BLOCK);
					$this->current_var_block_line = $this->lineno;
				}
				break;

			case $this->options['tag_variable'][0]:
				$this->pushToken(Twig_Token::VAR_START_TYPE);
				$this->pushState(self::STATE_VAR);
				$this->current_var_block_line = $this->lineno;
				break;
		}
	}

	protected function lex_block()
	{
		if (empty($this->brackets) && preg_match($this->regexes['lex_block'], $this->code, $match, null, $this->cursor))
		{
			$this->pushToken(Twig_Token::BLOCK_END_TYPE);
			$this->move_cursor($match[0]);
			$this->pop_state();
		}
		else
		{
			$this->lex_expression();
		}
	}

	protected function lex_var()
	{
		if (empty($this->brackets) && preg_match($this->regexes['lex_var'], $this->code, $match, null, $this->cursor))
		{
			$this->pushToken(Twig_Token::VAR_END_TYPE);
			$this->move_cursor($match[0]);
			$this->pop_state();
		}
		else
		{
			$this->lex_expression();
		}
	}

	const LEX_EXPRESSION_WHITESPACE_REGEX = '/\s+/A';
	protected function lex_expression()
	{
		// whitespace
		if (preg_match(Twig_Lexer::LEX_EXPRESSION_WHITESPACE_REGEX, $this->code, $match, null, $this->cursor))
		{
			$this->move_cursor($match[0]);

			if ($this->cursor >= $this->end)
			{
				throw new Twig_Exception_Syntax(sprintf('Unclosed "%s"', $this->state === self::STATE_BLOCK ? 'block' : 'variable'), $this->current_var_block_line, $this->filename);
			}
		}

		// operators
		if (preg_match($this->regexes['operator'], $this->code, $match, null, $this->cursor))
		{
			$this->pushToken(Twig_Token::OPERATOR_TYPE, $match[0]);
			$this->move_cursor($match[0]);
		}
		// names
		elseif (preg_match(self::REGEX_NAME, $this->code, $match, null, $this->cursor))
		{
			$this->pushToken(Twig_Token::NAME_TYPE, $match[0]);
			$this->move_cursor($match[0]);
		}
		// numbers
		elseif (preg_match(self::REGEX_NUMBER, $this->code, $match, null, $this->cursor))
		{
			$number = (float) $match[0];  // floats
			if (ctype_digit($match[0]) && $number <= PHP_INT_MAX)
			{
				$number = (int) $match[0]; // integers lower than the maximum
			}
			$this->pushToken(Twig_Token::NUMBER_TYPE, $number);
			$this->move_cursor($match[0]);
		}
		// punctuation
		elseif (false !== strpos(self::PUNCTUATION, $this->code[$this->cursor]))
		{
			// opening bracket
			if (false !== strpos('([{', $this->code[$this->cursor]))
			{
				$this->brackets[] = array($this->code[$this->cursor], $this->lineno);
			}
			// closing bracket
			elseif (strpos(')]}', $this->code[$this->cursor]) !== FALSE)
			{
				if (empty($this->brackets))
				{
					throw new Twig_Exception_Syntax(sprintf('Unexpected "%s"', $this->code[$this->cursor]), $this->lineno, $this->filename);
				}

				list($expect, $lineno) = array_pop($this->brackets);
				if ($this->code[$this->cursor] != strtr($expect, '([{', ')]}'))
				{
					throw new Twig_Exception_Syntax(sprintf('Unclosed "%s"', $expect), $lineno, $this->filename);
				}
			}

			$this->pushToken(Twig_Token::PUNCTUATION_TYPE, $this->code[$this->cursor]);
			++$this->cursor;
		}
		// strings
		elseif (preg_match(self::REGEX_STRING, $this->code, $match, null, $this->cursor))
		{
			$this->pushToken(Twig_Token::STRING_TYPE, stripcslashes(substr($match[0], 1, -1)));
			$this->move_cursor($match[0]);
		}
		// opening double quoted string
		elseif (preg_match(self::REGEX_DQ_STRING_DELIM, $this->code, $match, null, $this->cursor))
		{
			$this->brackets[] = array('"', $this->lineno);
			$this->pushState(self::STATE_STRING);
			$this->move_cursor($match[0]);
		}
		// unlexable
		else
		{
			throw new Twig_Exception_Syntax(sprintf('Unexpected character "%s"', $this->code[$this->cursor]), $this->lineno, $this->filename);
		}
	}

	protected function lex_raw_data($tag)
	{
		if ( ! preg_match(str_replace('%s', $tag, $this->regexes['lex_raw_data']), $this->code, $match, PREG_OFFSET_CAPTURE, $this->cursor))
		{
			throw new Twig_Exception_Syntax(sprintf('Unexpected end of file: Unclosed "%s" block', $tag), $this->lineno, $this->filename);
		}

		$text = substr($this->code, $this->cursor, $match[0][1] - $this->cursor);
		$this->move_cursor($text.$match[0][0]);

		if (strpos($match[1][0], $this->options['whitespace_trim']) !== FALSE)
		{
			$text = rtrim($text);
		}
		$this->pushToken(Twig_Token::TEXT_TYPE, $text);
	}

	protected function lex_comment()
	{
		if ( ! preg_match($this->regexes['lex_comment'], $this->code, $match, PREG_OFFSET_CAPTURE, $this->cursor))
		{
			throw new Twig_Exception_Syntax('Unclosed comment', $this->lineno, $this->filename);
		}

		$this->move_cursor(substr($this->code, $this->cursor, $match[0][1] - $this->cursor).$match[0][0]);
	}

	protected function lex_string()
	{
		if (preg_match($this->regexes['interpolation_start'], $this->code, $match, null, $this->cursor))
		{
			$this->brackets[] = array($this->options['interpolation'][0], $this->lineno);
			$this->pushToken(Twig_Token::INTERPOLATION_START_TYPE);
			$this->move_cursor($match[0]);
			$this->pushState(self::STATE_INTERPOLATION);
		}
		elseif (preg_match(self::REGEX_DQ_STRING_PART, $this->code, $match, null, $this->cursor) && strlen($match[0]) > 0)
		{
			$this->pushToken(Twig_Token::STRING_TYPE, stripcslashes($match[0]));
			$this->move_cursor($match[0]);

		}
		elseif (preg_match(self::REGEX_DQ_STRING_DELIM, $this->code, $match, null, $this->cursor))
		{

			list($expect, $lineno) = array_pop($this->brackets);
			if ($this->code[$this->cursor] != '"')
			{
				throw new Twig_Exception_Syntax(sprintf('Unclosed "%s"', $expect), $lineno, $this->filename);
			}

			$this->pop_state();
			++$this->cursor;
		}
	}

	protected function lex_interpolation()
	{
		$bracket = end($this->brackets);
		if ($this->options['interpolation'][0] === $bracket[0] && preg_match($this->regexes['interpolation_end'], $this->code, $match, null, $this->cursor))
		{
			array_pop($this->brackets);
			$this->pushToken(Twig_Token::INTERPOLATION_END_TYPE);
			$this->move_cursor($match[0]);
			$this->pop_state();
		}
		else
		{
			$this->lex_expression();
		}
	}

	protected function pushToken($type, $value = '')
	{
		// do not push empty text tokens
		if (Twig_Token::TEXT_TYPE === $type && '' === $value)
		{
			return;
		}
		$this->tokens[] = new Twig_Token($type, $value, $this->lineno);
	}

	protected function move_cursor($text)
	{
		$this->cursor += strlen($text);
		$this->lineno += substr_count($text, "\n");
	}

	protected function getOperatorRegex()
	{
		$operators = array_merge(
			array('='),
			array_keys($this->env->get_unary_operators()),
			array_keys($this->env->getBinaryOperators())
		);

		$operators = array_combine($operators, array_map('strlen', $operators));
		arsort($operators);

		$regex = array();
		foreach ($operators AS $operator => $length)
		{
			// an operator that ends with a character must be followed by
			// a whitespace or a parenthesis
			$regex[] = ctype_alpha($operator[$length - 1])
				? preg_quote($operator, '/').'(?=[\s()])'
				: preg_quote($operator, '/');
		}

		return '/'.implode('|', $regex).'/A';
	}

	protected function pushState($state)
	{
		$this->states[] = $this->state;
		$this->state = $state;
	}

	protected function pop_state()
	{
		if (count($this->states) === 0)
		{
			throw new Exception('Cannot pop state without a previous state');
		}

		$this->state = array_pop($this->states);
	}
}
