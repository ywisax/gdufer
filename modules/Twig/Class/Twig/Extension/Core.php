<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 扩展类核心库
 *
 * @package    Kohana/Twig
 * @category   Extension
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Extension_Core extends Twig_Extension {

	protected $date_formats = array('F j, Y H:i', '%d days');
	protected $number_formats = array(0, '.', ',');
	protected $timezone = NULL;
	
	/**
	 * 设置或者获取时间格式
	 */
	public function date_format($format = NULL, $dateIntervalFormat = NULL)
	{
		if ($format !== NULL)
		{
			$this->date_formats[0] = $format;
		}
		if ($dateIntervalFormat !== NULL)
		{
			$this->date_formats[1] = $dateIntervalFormat;
		}

		return $this->date_formats;
	}

	/**
	 * Sets the default timezone to be used by the date filter.
	 *
	 * @param DateTimeZone|string $timezone The default timezone string or a DateTimeZone object
	 */
	public function setTimezone($timezone)
	{
		$this->timezone = $timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone);
	}

	/**
	 * Gets the default timezone to be used by the date filter.
	 *
	 * @return DateTimeZone The default timezone currently in use
	 */
	public function getTimezone()
	{
		if ($this->timezone === NULL)
		{
			$this->timezone = new DateTimeZone(date_default_timezone_get());
		}

		return $this->timezone;
	}

	/**
	 * Sets the default format to be used by the number_format filter.
	 *
	 * @param integer $decimal      The number of decimal places to use.
	 * @param string  $decimalPoint The character(s) to use for the decimal point.
	 * @param string  $thousandSep  The character(s) to use for the thousands separator.
	 */
	public function setNumberFormat($decimal, $decimalPoint, $thousandSep)
	{
		$this->number_formats = array($decimal, $decimalPoint, $thousandSep);
	}

	/**
	 * Get the default format used by the number_format filter.
	 *
	 * @return array The arguments for number_format()
	 */
	public function getNumberFormat()
	{
		return $this->number_formats;
	}

	/**
	 * Returns the token parser instance to add to the existing list.
	 *
	 * @return array An array of Twig_Token_Parser instances
	 */
	public function get_token_parsers()
	{
		return array(
			new Twig_Token_Parser_For(),
			new Twig_Token_Parser_If(),
			new Twig_Token_Parser_Extends(),
			new Twig_Token_Parser_Include(),
			new Twig_Token_Parser_Block(),
			new Twig_Token_Parser_Use(),
			new Twig_Token_Parser_Filter(),
			new Twig_Token_Parser_Macro(),
			new Twig_Token_Parser_Import(),
			new Twig_Token_Parser_From(),
			new Twig_Token_Parser_Set(),
			new Twig_Token_Parser_Spaceless(),
			new Twig_Token_Parser_Flush(),
			new Twig_Token_Parser_Do(),
			new Twig_Token_Parser_Embed(),
		);
	}

	/**
	 * Returns a list of filters to add to the existing list.
	 *
	 * @return array An array of filters
	 */
	public function get_filters()
	{
		$filters = array(
			// formatting filters
			new Twig_Simple_Filter('date', 'Twig_Filter_Helper::date_format', array('needs_environment' => TRUE)),
			new Twig_Simple_Filter('date_modify', 'Twig_Filter_Helper::date_modify', array('needs_environment' => TRUE)),
			new Twig_Simple_Filter('format', 'sprintf'),
			new Twig_Simple_Filter('replace', 'strtr'),
			new Twig_Simple_Filter('number_format', 'Twig_Filter_Helper::number_format', array('needs_environment' => TRUE)),
			new Twig_Simple_Filter('abs', 'abs'),

			// encoding
			new Twig_Simple_Filter('url_encode', 'Twig_Filter_Helper::urlencode'),
			new Twig_Simple_Filter('json_encode', 'twig_jsonencode_filter'),
			new Twig_Simple_Filter('convert_encoding', 'UTF8::iconv'),

			// string filters
			new Twig_Simple_Filter('title', 'Twig_Filter_Helper::title_string', array('needs_environment' => TRUE)),
			new Twig_Simple_Filter('capitalize', 'Twig_Filter_Helper::capitalize_string', array('needs_environment' => TRUE)),
			new Twig_Simple_Filter('upper', 'strtoupper'),
			new Twig_Simple_Filter('lower', 'strtolower'),
			new Twig_Simple_Filter('striptags', 'strip_tags'),
			new Twig_Simple_Filter('trim', 'trim'),
			new Twig_Simple_Filter('nl2br', 'nl2br', array('pre_escape' => 'html', 'is_safe' => array('html'))),

			/**
			 * 参考下面的语法：
			 *
			 * <pre>
			 *  {{ [1, 2, 3]|join('|') }}
			 *  {# returns 1|2|3 #}
			 *
			 *  {{ [1, 2, 3]|join }}
			 *  {# returns 123 #}
			 * </pre>
			 */
			new Twig_Simple_Filter('join', 'Helper_Array::join'),

			/**
			 * 分割数组
			 *
			 * <pre>
			 *  {{ "one,two,three"|split(',') }}
			 *  {# returns [one, two, three] #}
			 *
			 *  {{ "one,two,three,four,five"|split(',', 3) }}
			 *  {# returns [one, two, "three,four,five"] #}
			 *
			 *  {{ "123"|split('') }}
			 *  {# returns [1, 2, 3] #}
			 *
			 *  {{ "aabbcc"|split('', 2) }}
			 *  {# returns [aa, bb, cc] #}
			 * </pre>
			 */
			new Twig_Simple_Filter('split', 'Helper_Array::split'),
			new Twig_Simple_Filter('sort', 'Twig_Filter_Helper::sort'),

			/**
			 * 合并数组，参考下面的语法：
			 *
			 * <pre>
			 *  {% set items = { 'apple': 'fruit', 'orange': 'fruit' } %}
			 *
			 *  {% set items = items|merge({ 'peugeot': 'car' }) %}
			 *
			 *  {# items now contains { 'apple': 'fruit', 'orange': 'fruit', 'peugeot': 'car' } #}
			 * </pre>
			 */
			new Twig_Simple_Filter('merge', 'Helper_Arrray::merge'),
			// Batches item.
			new Twig_Simple_Filter('batch', 'Helper_Array::batch'),

			// string/array filters
			new Twig_Simple_Filter('reverse', 'Twig_Filter_Helper::reverse', array('needs_environment' => TRUE)),
			new Twig_Simple_Filter('length', 'Twig_Filter_Helper::length', array('needs_environment' => TRUE)),
			new Twig_Simple_Filter('slice', 'twig_slice', array('needs_environment' => TRUE)),
			new Twig_Simple_Filter('first', 'twig_first', array('needs_environment' => TRUE)),
			new Twig_Simple_Filter('last', 'twig_last', array('needs_environment' => TRUE)),

			// iteration and runtime
			new Twig_Simple_Filter('default', '_twig_default_filter', array('node_class' => 'Twig_Node_Expression_Filter_Default')),
			
			/**
			 * Returns the keys for the given array.
			 *
			 * It is useful when you want to iterate over the keys of an array:
			 *
			 * <pre>
			 *  {% for key in array|keys %}
			 *      {# ... #}
			 *  {% endfor %}
			 * </pre>
			 */
			new Twig_Simple_Filter('keys', 'Helper_Array::keys'),

			// escaping
			new Twig_Simple_Filter('escape', 'Twig_Filter_Helper::escape', array(
				'needs_environment' => TRUE,
				'is_safe_callback' => 'Twig_Extension_Helper::escape_filter_is_safe'
			)),
			new Twig_Simple_Filter('e', 'Twig_Filter_Helper::escape', array(
				'needs_environment' => TRUE,
				'is_safe_callback' => 'Twig_Extension_Helper::escape_filter_is_safe'
			)),
		);

		if (function_exists('mb_get_info'))
		{
			$filters[] = new Twig_Simple_Filter('upper', 'Twig_Filter_Helper::upper', array('needs_environment' => TRUE));
			$filters[] = new Twig_Simple_Filter('lower', 'Twig_Filter_Helper::lower', array('needs_environment' => TRUE));
		}
		return $filters;
	}

	/**
	 * Returns a list of global functions to add to the existing list.
	 *
	 * @return array An array of global functions
	 */
	public function get_functions()
	{
		return array(
			new Twig_Simple_Function('range', 'range'),
			new Twig_Simple_Function('constant', 'Twig_Extension_Helper::constant'),
			new Twig_Simple_Function('cycle', 'twig_cycle'),
			new Twig_Simple_Function('random', 'twig_random', array('needs_environment' => TRUE)),
			new Twig_Simple_Function('date', 'twig_date_converter', array('needs_environment' => TRUE)),
			new Twig_Simple_Function('include', 'Twig_Extension_Helper::twig_include', array('needs_environment' => TRUE, 'needs_context' => TRUE, 'is_safe' => array('all'))),
		);
	}

	/**
	 * Returns a list of tests to add to the existing list.
	 *
	 * @return array An array of tests
	 */
	public function getTests()
	{
		return array(
			new Twig_Simple_Test('even', null, array('node_class' => 'Twig_Node_Expression_Test_Even')),
			new Twig_Simple_Test('odd', null, array('node_class' => 'Twig_Node_Expression_Test_Odd')),
			new Twig_Simple_Test('defined', null, array('node_class' => 'Twig_Node_Expression_Test_Defined')),
			new Twig_Simple_Test('sameas', null, array('node_class' => 'Twig_Node_Expression_Test_Sameas')),
			new Twig_Simple_Test('none', null, array('node_class' => 'Twig_Node_Expression_Test_Null')),
			new Twig_Simple_Test('null', null, array('node_class' => 'Twig_Node_Expression_Test_Null')),
			new Twig_Simple_Test('divisibleby', null, array('node_class' => 'Twig_Node_Expression_Test_Divisibleby')),
			new Twig_Simple_Test('constant', null, array('node_class' => 'Twig_Node_Expression_Test_Constant')),

			/**
			 * 检查一个值是否为空
			 *
			 * <pre>
			 * {# evaluates to true if the foo variable is null, false, or the empty string #}
			 * {% if foo is empty %}
			 *     {# ... #}
			 * {% endif %}
			 * </pre>
			 */
			new Twig_Simple_Test('empty', 'Valid::is_empty'),

			/**
			 * Checks if a variable is traversable.
			 *
			 * <pre>
			 * {# evaluates to true if the foo variable is an array or a traversable object #}
			 * {% if foo is traversable %}
			 *     {# ... #}
			 * {% endif %}
			 * </pre>
			 */
			new Twig_Simple_Test('iterable', 'Helper_Array::is_iterable'),
		);
	}

	/**
	 * Returns a list of operators to add to the existing list.
	 *
	 * @return array An array of operators
	 */
	public function get_operators()
	{
		return array(
			array(
				'not' => array('precedence' => 50, 'class' => 'Twig_Node_Expression_Unary_Not'),
				'-'   => array('precedence' => 500, 'class' => 'Twig_Node_Expression_Unary_Neg'),
				'+'   => array('precedence' => 500, 'class' => 'Twig_Node_Expression_Unary_Pos'),
			),
			array(
				'or'     => array('precedence' => 10, 'class' => 'Twig_Node_Expression_Binary_Or', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'and'    => array('precedence' => 15, 'class' => 'Twig_Node_Expression_Binary_And', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'b-or'   => array('precedence' => 16, 'class' => 'Twig_Node_Expression_Binary_BitwiseOr', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'b-xor'  => array('precedence' => 17, 'class' => 'Twig_Node_Expression_Binary_BitwiseXor', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'b-and'  => array('precedence' => 18, 'class' => 'Twig_Node_Expression_Binary_BitwiseAnd', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'=='     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_Equal', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'!='     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_NotEqual', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'<'      => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_Less', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'>'      => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_Greater', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'>='     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_GreaterEqual', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'<='     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_LessEqual', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'not in' => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_NotIn', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'in'     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_In', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'..'     => array('precedence' => 25, 'class' => 'Twig_Node_Expression_Binary_Range', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'+'      => array('precedence' => 30, 'class' => 'Twig_Node_Expression_Binary_Add', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'-'      => array('precedence' => 30, 'class' => 'Twig_Node_Expression_Binary_Sub', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'~'      => array('precedence' => 40, 'class' => 'Twig_Node_Expression_Binary_Concat', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'*'      => array('precedence' => 60, 'class' => 'Twig_Node_Expression_Binary_Mul', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'/'      => array('precedence' => 60, 'class' => 'Twig_Node_Expression_Binary_Div', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'//'     => array('precedence' => 60, 'class' => 'Twig_Node_Expression_Binary_FloorDiv', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'%'      => array('precedence' => 60, 'class' => 'Twig_Node_Expression_Binary_Mod', 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'is'     => array('precedence' => 100, 'callable' => array($this, 'parseTestExpression'), 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'is not' => array('precedence' => 100, 'callable' => array($this, 'parseNotTestExpression'), 'associativity' => Twig_Expression::OPERATOR_LEFT),
				'**'     => array('precedence' => 200, 'class' => 'Twig_Node_Expression_Binary_Power', 'associativity' => Twig_Expression::OPERATOR_RIGHT),
			),
		);
	}

	public function parseNotTestExpression(Twig_Parser $parser, $node)
	{
		return new Twig_Node_Expression_Unary_Not($this->parseTestExpression($parser, $node), $parser->getCurrentToken()->getLine());
	}

	public function parseTestExpression(Twig_Parser $parser, $node)
	{
		$stream = $parser->getStream();
		$name = $stream
			->expect(Twig_Token::NAME_TYPE)
			->getValue();
		$arguments = NULL;
		if ($stream->test(Twig_Token::PUNCTUATION_TYPE, '('))
		{
			$arguments = $parser
				->get_expression_parser()
				->parseArguments(TRUE);
		}

		$class = $this->get_test_node_class($parser, $name, $node->getLine());

		return new $class($node, $name, $arguments, $parser->getCurrentToken()->getLine());
	}

	protected function get_test_node_class(Twig_Parser $parser, $name, $line)
	{
		$env = $parser->get_environment();
		$testMap = $env->getTests();
		if ( ! isset($testMap[$name]))
		{
			$message = sprintf('The test "%s" does not exist', $name);
			if ($alternatives = $env->computeAlternatives($name, array_keys($env->getTests())))
			{
				$message = sprintf('%s. Did you mean "%s"', $message, implode('", "', $alternatives));
			}

			throw new Twig_Exception_Syntax($message, $line, $parser->getFilename());
		}

		if ($testMap[$name] instanceof Twig_Simple_Test)
		{
			return $testMap[$name]->get_node_class();
		}
		return $testMap[$name] instanceof Twig_Test_Node ? $testMap[$name]->getClass() : 'Twig_Node_Expression_Test';
	}

	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName()
	{
		return 'core';
	}
}

