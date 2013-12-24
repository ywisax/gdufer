<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Represents a function template helper.
 *
 * @package    Kohana/Twig
 * @category   Filter
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Twig_Filter_Helper {

	/**
	 * Returns a capitalized string.
	 *
	 * @param Twig_Environment $env    A Twig_Environment instance
	 * @param string           $string A string
	 *
	 * @return string The capitalized string
	 */
    function capitalize_string(Twig_Environment $env, $string)
	{
		if (($charset = $env->getCharset()) !== NULL)
		{
			return mb_strtoupper(mb_substr($string, 0, 1, $charset), $charset).
						 mb_strtolower(mb_substr($string, 1, mb_strlen($string, $charset), $charset), $charset);
		}

		return ucfirst(strtolower($string));
	}

	/**
	 * Returns a titlecased string.
	 *
	 * @param Twig_Environment $env    A Twig_Environment instance
	 * @param string           $string A string
	 *
	 * @return string The titlecased string
	 */
	public static function title_string(Twig_Environment $env, $string)
	{
		if (($charset = $env->getCharset()) !== NULL)
		{
			return mb_convert_case($string, MB_CASE_TITLE, $charset);
		}

		return ucwords(strtolower($string));
	}

	/**
	 * Converts a string to lowercase.
	 *
	 * @param Twig_Environment $env    A Twig_Environment instance
	 * @param string           $string A string
	 *
	 * @return string The lowercased string
	 */
	public static function lower(Twig_Environment $env, $string)
	{
		if (($charset = $env->getCharset()) !== NULL)
		{
			return mb_strtolower($string, $charset);
		}
		return strtolower($string);
	}

	/**
	 * Converts a string to uppercase.
	 *
	 * @param Twig_Environment $env    A Twig_Environment instance
	 * @param string           $string A string
	 *
	 * @return string The uppercased string
	 */
	public static function upper(Twig_Environment $env, $string)
	{
		if (($charset = $env->getCharset()) !== NULL)
		{
			return mb_strtoupper($string, $charset);
		}
		return strtoupper($string);
	}

	/**
	 * Returns the length of a variable.
	 *
	 * @param Twig_Environment $env   A Twig_Environment instance
	 * @param mixed            $thing A variable
	 *
	 * @return integer The length of the value
	 */
    public static function length(Twig_Environment $env, $thing)
	{
		return is_scalar($thing) ? mb_strlen($thing, $env->getCharset()) : count($thing);
	}

	/**
	 * Converts a date to the given format.
	 *
	 * <pre>
	 *   {{ post.published_at|date("m/d/Y") }}
	 * </pre>
	 *
	 * @param Twig_Environment             $env      A Twig_Environment instance
	 * @param DateTime|DateInterval|string $date     A date
	 * @param string                       $format   A format
	 * @param DateTimeZone|string          $timezone A timezone
	 *
	 * @return string The formatted date
	 */
	public static function date_format(Twig_Environment $env, $date, $format = NULL, $timezone = NULL)
	{
		if ($format === NULL)
		{
			$formats = $env->getExtension('core')->date_format();
			$format = $date instanceof DateInterval ? $formats[1] : $formats[0];
		}

		if ($date instanceof DateInterval)
		{
			return $date->format($format);
		}
		return twig_date_converter($env, $date, $timezone)->format($format);
	}

	/**
	 * Number format filter.
	 *
	 * All of the formatting options can be left null, in that case the defaults will
	 * be used.  Supplying any of the parameters will override the defaults set in the
	 * environment object.
	 *
	 * @param Twig_Environment    $env          A Twig_Environment instance
	 * @param mixed               $number       A float/int/string of the number to format
	 * @param integer             $decimal      The number of decimal points to display.
	 * @param string              $decimalPoint The character(s) to use for the decimal point.
	 * @param string              $thousandSep  The character(s) to use for the thousands separator.
	 *
	 * @return string The formatted number
	 */
	public static function number_format(Twig_Environment $env, $number, $decimal = NULL, $decimalPoint = NULL, $thousandSep = NULL)
	{
		$defaults = $env->getExtension('core')->getNumberFormat();
		if ($decimal === NULL)
		{
			$decimal = $defaults[0];
		}

		if ($decimalPoint === NULL)
		{
			$decimalPoint = $defaults[1];
		}

		if ($thousandSep === NULL)
		{
			$thousandSep = $defaults[2];
		}

		return number_format((float) $number, $decimal, $decimalPoint, $thousandSep);
	}

	/**
	 * Returns a new date object modified
	 *
	 * <pre>
	 *   {{ post.published_at|date_modify("-1day")|date("m/d/Y") }}
	 * </pre>
	 *
	 * @param Twig_Environment  $env      A Twig_Environment instance
	 * @param DateTime|string   $date     A date
	 * @param string            $modifier A modifier string
	 *
	 * @return DateTime A new date object
	 */
	public static function date_modify(Twig_Environment $env, $date, $modifier)
	{
		$date = twig_date_converter($env, $date, FALSE);
		$date->modify($modifier);

		return $date;
	}

	/**
	 * URL encodes a string as a path segment or an array as a query string.
	 *
	 * @param string|array $url A URL or an array of query parameters
	 * @param bool         $raw true to use rawurlencode() instead of urlencode
	 *
	 * @return string The URL encoded value
	 */
	public static function urlencode($url, $raw = FALSE)
	{
		if (is_array($url))
		{
			return http_build_query($url, '', '&');
		}

		if ($raw)
		{
			return rawurlencode($url);
		}
		return urlencode($url);
	}

	/**
	 * Reverses a variable.
	 *
	 * @param Twig_Environment         $env          A Twig_Environment instance
	 * @param array|Traversable|string $item         An array, a Traversable instance, or a string
	 * @param Boolean                  $preserveKeys Whether to preserve key or not
	 *
	 * @return mixed The reversed input
	 */
	public static function reverse(Twig_Environment $env, $item, $preserveKeys = FALSE)
	{
		if (is_object($item) && $item instanceof Traversable)
		{
			return array_reverse(iterator_to_array($item), $preserveKeys);
		}

		if (is_array($item))
		{
			return array_reverse($item, $preserveKeys);
		}

		if (null !== $charset = $env->getCharset())
		{
			$string = (string) $item;

			if ('UTF-8' != $charset)
			{
				$item = UTF8::iconv($string, 'UTF-8', $charset);
			}

			preg_match_all('/./us', $item, $matches);

			$string = implode('', array_reverse($matches[0]));

			if ('UTF-8' != $charset)
			{
				$string = UTF8::iconv($string, $charset, 'UTF-8');
			}

			return $string;
		}

		return strrev((string) $item);
	}

	/**
	 * Sorts an array.
	 *
	 * @param array $array An array
	 */
	public static function sort($array)
	{
		asort($array);
		return $array;
	}

	/* used internally */
	public static function in($value, $compare)
	{
		if (is_array($compare))
		{
			return in_array($value, $compare, is_object($value));
		}
		elseif (is_string($compare))
		{
			if ( ! strlen($value))
			{
				return empty($compare);
			}

			return FALSE !== strpos($compare, (string) $value);
		}
		elseif ($compare instanceof Traversable)
		{
			return in_array($value, iterator_to_array($compare, FALSE), is_object($value));
		}

		return FALSE;
	}

	/**
	 * Escapes a string.
	 *
	 * @param Twig_Environment $env        A Twig_Environment instance
	 * @param string           $string     The value to be escaped
	 * @param string           $strategy   The escaping strategy
	 * @param string           $charset    The charset
	 * @param Boolean          $autoescape Whether the function is called by the auto-escaping feature (true) or by the developer (false)
	 */
	public static function escape(Twig_Environment $env, $string, $strategy = 'html', $charset = NULL, $autoescape = FALSE)
	{
		if ($autoescape && $string instanceof Twig_Markup)
		{
			return $string;
		}

		if ( ! is_string($string))
		{
			if (is_object($string) && method_exists($string, '__toString'))
			{
				$string = (string) $string;
			}
			else
			{
				return $string;
			}
		}

		if ($charset === NULL)
		{
			$charset = $env->getCharset();
		}

		switch ($strategy)
		{
			case 'html':
				// see http://php.net/htmlspecialchars

				// Using a static variable to avoid initializing the array
				// each time the function is called. Moving the declaration on the
				// top of the function slow downs other escaping strategies.
				static $htmlspecialcharsCharsets = array(
					'ISO-8859-1' => TRUE, 'ISO8859-1' => TRUE,
					'ISO-8859-15' => TRUE, 'ISO8859-15' => TRUE,
					'utf-8' => TRUE, 'UTF-8' => TRUE,
					'CP866' => TRUE, 'IBM866' => TRUE, '866' => TRUE,
					'CP1251' => TRUE, 'WINDOWS-1251' => TRUE, 'WIN-1251' => TRUE,
					'1251' => TRUE,
					'CP1252' => TRUE, 'WINDOWS-1252' => TRUE, '1252' => TRUE,
					'KOI8-R' => TRUE, 'KOI8-RU' => TRUE, 'KOI8R' => TRUE,
					'BIG5' => TRUE, '950' => TRUE,
					'GB2312' => TRUE, '936' => TRUE,
					'BIG5-HKSCS' => TRUE,
					'SHIFT_JIS' => TRUE, 'SJIS' => TRUE, '932' => TRUE,
					'EUC-JP' => TRUE, 'EUCJP' => TRUE,
					'ISO8859-5' => TRUE, 'ISO-8859-5' => TRUE, 'MACROMAN' => TRUE,
				);

				if (isset($htmlspecialcharsCharsets[$charset]))
				{
					return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, $charset);
				}

				if (isset($htmlspecialcharsCharsets[strtoupper($charset)]))
				{
					// cache the lowercase variant for future iterations
					$htmlspecialcharsCharsets[$charset] = TRUE;
					return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, $charset);
				}

				$string = UTF8::iconv($string, 'UTF-8', $charset);
				$string = htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

				return UTF8::iconv($string, $charset, 'UTF-8');

			case 'js':
				// escape all non-alphanumeric characters
				// into their \xHH or \uHHHH representations
				if ('UTF-8' != $charset)
				{
					$string = UTF8::iconv($string, 'UTF-8', $charset);
				}

				if (0 == strlen($string) ? FALSE : (1 == preg_match('/^./su', $string) ? FALSE : TRUE))
				{
					throw new Twig_Exception_Runtime('The string to escape is not a valid UTF-8 string.');
				}

				$string = preg_replace_callback('#[^a-zA-Z0-9,\._]#Su', 'Twig_Extension_Helper::_twig_escape_js_callback', $string);

				if ('UTF-8' != $charset)
				{
					$string = UTF8::iconv($string, $charset, 'UTF-8');
				}

				return $string;

			case 'css':
				if ('UTF-8' != $charset)
				{
					$string = UTF8::iconv($string, 'UTF-8', $charset);
				}

				if (0 == strlen($string) ? FALSE : (1 == preg_match('/^./su', $string) ? FALSE : TRUE))
				{
					throw new Twig_Exception_Runtime('The string to escape is not a valid UTF-8 string.');
				}

				$string = preg_replace_callback('#[^a-zA-Z0-9]#Su', 'Twig_Extension_Helper::_twig_escape_css_callback', $string);

				if ('UTF-8' != $charset)
				{
					$string = UTF8::iconv($string, $charset, 'UTF-8');
				}

				return $string;

			case 'html_attr':
				if ('UTF-8' != $charset)
				{
					$string = UTF8::iconv($string, 'UTF-8', $charset);
				}

				if (0 == strlen($string) ? FALSE : (1 == preg_match('/^./su', $string) ? FALSE : TRUE))
				{
					throw new Twig_Exception_Runtime('The string to escape is not a valid UTF-8 string.');
				}

				$string = preg_replace_callback('#[^a-zA-Z0-9,\.\-_]#Su', 'Twig_Extension_Helper::_twig_escape_html_attr_callback', $string);

				if ('UTF-8' != $charset)
				{
					$string = UTF8::iconv($string, $charset, 'UTF-8');
				}

				return $string;

			case 'url':
				// hackish test to avoid version_compare that is much slower, this works unless PHP releases a 5.10.*
				// at that point however PHP 5.2.* support can be removed
				if (PHP_VERSION < '5.3.0')
				{
					return str_replace('%7E', '~', rawurlencode($string));
				}

				return rawurlencode($string);

			default:
				throw new Twig_Exception_Runtime(sprintf('Invalid escaping strategy "%s" (valid ones: html, js, url, css, and html_attr).', $strategy));
		}
	}

}
