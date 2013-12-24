<?php defined('SYS_PATH') or die('No direct script access.');

defined('ENT_SUBSTITUTE') OR define('ENT_SUBSTITUTE', 8);

/**
 * Cycles over a value.
 *
 * @param ArrayAccess|array $values   An array or an ArrayAccess instance
 * @param integer           $position The cycle position
 *
 * @return string The next value in the cycle
 */
function twig_cycle($values, $position)
{
    if ( ! is_array($values) && !$values instanceof ArrayAccess)
	{
		return $values;
	}
    return $values[$position % count($values)];
}

/**
 * Returns a random value depending on the supplied parameter type:
 * - a random item from a Traversable or array
 * - a random character from a string
 * - a random integer between 0 and the integer parameter
 *
 * @param Twig_Environment                 $env    A Twig_Environment instance
 * @param Traversable|array|integer|string $values The values to pick a random item from
 *
 * @throws Twig_Exception_Runtime When $values is an empty array (does not apply to an empty string which is returned as is).
 *
 * @return mixed A random value from the given sequence
 */
function twig_random(Twig_Environment $env, $values = NULL)
{
    if (null === $values)
	{
		return mt_rand();
	}

    if (is_int($values) || is_float($values))
	{
		return $values < 0 ? mt_rand($values, 0) : mt_rand(0, $values);
	}

    if ($values instanceof Traversable)
	{
		$values = iterator_to_array($values);
	}
	elseif (is_string($values))
	{
		if ('' === $values)
		{
			return '';
		}
		if (null !== $charset = $env->getCharset())
		{
			if ('UTF-8' != $charset)
			{
				$values = UTF8::iconv($values, 'UTF-8', $charset);
			}

			// unicode version of str_split()
			// split at all positions, but not after the start and not before the end
			$values = preg_split('/(?<!^)(?!$)/u', $values);

			if ('UTF-8' != $charset)
			{
				foreach ($values AS $i => $value)
				{
					$values[$i] = UTF8::iconv($value, $charset, 'UTF-8');
				}
			}
		}
		else
		{
			return $values[mt_rand(0, strlen($values) - 1)];
		}
	}

    if ( ! is_array($values))
	{
		return $values;
	}

    if (count($values) === 0)
	{
		throw new Twig_Exception_Runtime('The random function cannot pick from an empty array.');
	}
    return $values[array_rand($values, 1)];
}

/**
 * Converts an input to a DateTime instance.
 *
 * <pre>
 *    {% if date(user.created_at) < date('+2days') %}
 *      {# do something #}
 *    {% endif %}
 * </pre>
 *
 * @param Twig_Environment    $env      A Twig_Environment instance
 * @param DateTime|string     $date     A date
 * @param DateTimeZone|string $timezone A timezone
 *
 * @return DateTime A DateTime instance
 */
function twig_date_converter(Twig_Environment $env, $date = NULL, $timezone = NULL)
{
    // determine the timezone
    if ( ! $timezone)
	{
		$defaultTimezone = $env->getExtension('core')->getTimezone();
	}
	elseif ( ! $timezone instanceof DateTimeZone)
	{
		$defaultTimezone = new DateTimeZone($timezone);
	}
	else
	{
		$defaultTimezone = $timezone;
	}

    if ($date instanceof DateTime)
	{
		$date = clone $date;
		if ($timezone !== FALSE)
		{
			$date->setTimezone($defaultTimezone);
		}

		return $date;
	}

    $asString = (string) $date;
    if (ctype_digit($asString) || ( ! empty($asString) && '-' === $asString[0] && ctype_digit(substr($asString, 1))))
	{
		$date = '@'.$date;
	}

    $date = new DateTime($date, $defaultTimezone);
    if ($timezone !== FALSE)
	{
		$date->setTimezone($defaultTimezone);
	}
    return $date;
}

if (version_compare(PHP_VERSION, '5.3.0', '<'))
{
	/**
	 * JSON encodes a variable.
	 *
	 * @param mixed   $value   The value to encode.
	 * @param integer $options Not used on PHP 5.2.x
	 *
	 * @return mixed The JSON encoded value
	 */
    function twig_jsonencode_filter($value, $options = 0)
	{
		if ($value instanceof Twig_Markup)
		{
			$value = (string) $value;
		}
		elseif (is_array($value))
		{
			array_walk_recursive($value, '_twig_markup2string');
		}

		return json_encode($value);
	}
}
else
{
	/**
	 * JSON encodes a variable.
	 *
	 * @param mixed   $value   The value to encode.
	 * @param integer $options Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT
	 *
	 * @return mixed The JSON encoded value
	 */
    function twig_jsonencode_filter($value, $options = 0)
	{
		if ($value instanceof Twig_Markup)
		{
			$value = (string) $value;
		}
		elseif (is_array($value))
		{
			array_walk_recursive($value, '_twig_markup2string');
		}

		return json_encode($value, $options);
	}
}

function _twig_markup2string(&$value)
{
    if ($value instanceof Twig_Markup)
	{
		$value = (string) $value;
	}
}

/**
 * Slices a variable.
 *
 * @param Twig_Environment $env          A Twig_Environment instance
 * @param mixed            $item         A variable
 * @param integer          $start        Start of the slice
 * @param integer          $length       Size of the slice
 * @param Boolean          $preserveKeys Whether to preserve key or not (when the input is an array)
 *
 * @return mixed The sliced variable
 */
function twig_slice(Twig_Environment $env, $item, $start, $length = NULL, $preserveKeys = FALSE)
{
    if ($item instanceof Traversable)
	{
		$item = iterator_to_array($item, FALSE);
	}

    if (is_array($item))
	{
		return array_slice($item, $start, $length, $preserveKeys);
	}

    $item = (string) $item;

    if (function_exists('mb_get_info') && null !== $charset = $env->getCharset())
	{
		return mb_substr($item, $start, null === $length ? mb_strlen($item, $charset) - $start : $length, $charset);
	}

    return null === $length ? substr($item, $start) : substr($item, $start, $length);
}

/**
 * Returns the first element of the item.
 *
 * @param Twig_Environment $env  A Twig_Environment instance
 * @param mixed            $item A variable
 *
 * @return mixed The first element of the item
 */
function twig_first(Twig_Environment $env, $item)
{
    $elements = twig_slice($env, $item, 0, 1, FALSE);

    return is_string($elements) ? $elements[0] : current($elements);
}

/**
 * Returns the last element of the item.
 *
 * @param Twig_Environment $env  A Twig_Environment instance
 * @param mixed            $item A variable
 *
 * @return mixed The last element of the item
 */
function twig_last(Twig_Environment $env, $item)
{
    $elements = twig_slice($env, $item, -1, 1, FALSE);
    return is_string($elements) ? $elements[0] : current($elements);
}

// The '_default' filter is used internally to avoid using the ternary operator
// which costs a lot for big contexts (before PHP 5.4). So, on average,
// a function call is cheaper.
function _twig_default_filter($value, $default = '')
{
    if (Valid::is_empty($value))
	{
		return $default;
	}
    return $value;
}


