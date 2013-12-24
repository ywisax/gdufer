<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Inflector helper class. Inflection is changing the form of a word based on
 * the context it is used in. For example, changing a word into a plural form.
 *
 * [!!] Inflection is only tested with English, and is will not work with other languages.
 *
 * @package    Kohana
 * @category   Helpers
 */
class Kohana_Helper_Inflector {

	/**
	 * @var  array  cached inflections
	 */
	protected static $cache = array();

	/**
	 * @var  array  uncountable words
	 */
	protected static $uncountable;

	/**
	 * @var  array  irregular words
	 */
	protected static $irregular;

	/**
	 * Checks if a word is defined as uncountable. An uncountable word has a
	 * single form. For instance, one "fish" and many "fish", not "fishes".
	 *
	 *     Helper_Inflector::uncountable('fish'); // TRUE
	 *     Helper_Inflector::uncountable('cat');  // FALSE
	 *
	 * If you find a word is being pluralized improperly, it has probably not
	 * been defined as uncountable in `Config/Inflector.php`. If this is the
	 * case, please report [an issue](http://dev.kohanaphp.com/projects/kohana3/issues).
	 *
	 * @param   string  $str    word to check
	 * @return  boolean
	 */
	public static function uncountable($str)
	{
		if (Helper_Inflector::$uncountable === NULL)
		{
			// Cache uncountables
			Helper_Inflector::$uncountable = Kohana::config('Inflector')->uncountable;

			// Make uncountables mirrored
			Helper_Inflector::$uncountable = array_combine(Helper_Inflector::$uncountable, Helper_Inflector::$uncountable);
		}

		return isset(Helper_Inflector::$uncountable[strtolower($str)]);
	}

	/**
	 * Makes a plural word singular.
	 *
	 *     echo Helper_Inflector::singular('cats'); // "cat"
	 *     echo Helper_Inflector::singular('fish'); // "fish", uncountable
	 *
	 * You can also provide the count to make inflection more intelligent.
	 * In this case, it will only return the singular value if the count is
	 * greater than one and not zero.
	 *
	 *     echo Helper_Inflector::singular('cats', 2); // "cats"
	 *
	 * [!!] Special inflections are defined in `Config/Inflector.php`.
	 *
	 * @param   string  $str    word to singularize
	 * @param   integer $count  count of thing
	 * @return  string
	 */
	public static function singular($str, $count = NULL)
	{
		// $count should always be a float
		$count = ($count === NULL) ? 1.0 : (float) $count;

		// Do nothing when $count is not 1
		if ($count != 1)
			return $str;

		// Remove garbage
		$str = strtolower(trim($str));

		// Cache key name
		$key = 'singular_'.$str.$count;

		if (isset(Helper_Inflector::$cache[$key]))
			return Helper_Inflector::$cache[$key];

		if (Helper_Inflector::uncountable($str))
			return Helper_Inflector::$cache[$key] = $str;

		if (empty(Helper_Inflector::$irregular))
		{
			// Cache irregular words
			Helper_Inflector::$irregular = Kohana::config('Inflector')->irregular;
		}

		if ($irregular = array_search($str, Helper_Inflector::$irregular))
		{
			$str = $irregular;
		}
		elseif (preg_match('/us$/', $str))
		{
			// http://en.wikipedia.org/wiki/Plural_form_of_words_ending_in_-us
			// Already singular, do nothing
		}
		elseif (preg_match('/[sxz]es$/', $str) OR preg_match('/[^aeioudgkprt]hes$/', $str))
		{
			// Remove "es"
			$str = substr($str, 0, -2);
		}
		elseif (preg_match('/[^aeiou]ies$/', $str))
		{
			// Replace "ies" with "y"
			$str = substr($str, 0, -3).'y';
		}
		elseif (substr($str, -1) === 's' AND substr($str, -2) !== 'ss')
		{
			// Remove singular "s"
			$str = substr($str, 0, -1);
		}

		return Helper_Inflector::$cache[$key] = $str;
	}
	
	const PLURAL_WORD_REGEX_1 = '/[sxz]$/';
	const PLURAL_WORD_REGEX_2 = '/[^aeioudgkprt]h$/';
	const PLURAL_WORD_REGEX_3 = '/[^aeiou]y$/';

	/**
	 * Makes a singular word plural.
	 *
	 *     echo Helper_Inflector::plural('fish'); // "fish", uncountable
	 *     echo Helper_Inflector::plural('cat');  // "cats"
	 *
	 * You can also provide the count to make inflection more intelligent.
	 * In this case, it will only return the plural value if the count is
	 * not one.
	 *
	 *     echo Helper_Inflector::singular('cats', 3); // "cats"
	 *
	 * [!!] Special inflections are defined in `Config/Inflector.php`.
	 *
	 * @param   string  $str    word to pluralize
	 * @param   integer $count  count of thing
	 * @return  string
	 */
	public static function plural($str, $count = NULL)
	{
		// $count should always be a float
		$count = ($count === NULL) ? 0.0 : (float) $count;

		// Do nothing with singular
		if ($count == 1)
			return $str;

		// Remove garbage
		$str = trim($str);

		// Cache key name
		$key = 'plural_'.$str.$count;

		// Check uppercase
		$is_uppercase = ctype_upper($str);

		if (isset(Helper_Inflector::$cache[$key]))
		{
			return Helper_Inflector::$cache[$key];
		}

		if (Helper_Inflector::uncountable($str))
		{
			return Helper_Inflector::$cache[$key] = $str;
		}

		if (empty(Helper_Inflector::$irregular))
		{
			Helper_Inflector::$irregular = Kohana::config('Inflector.irregular');
		}

		// 处理不规则的转换
		if (isset(Helper_Inflector::$irregular[$str]))
		{
			$str = Helper_Inflector::$irregular[$str];
		}
		elseif (preg_match(Helper_Inflector::PLURAL_WORD_REGEX_1, $str) OR preg_match(Helper_Inflector::PLURAL_WORD_REGEX_2, $str))
		{
			$str .= 'es';
		}
		elseif (preg_match(Helper_Inflector::PLURAL_WORD_REGEX_3, $str))
		{
			// Change "y" to "ies"
			$str = substr_replace($str, 'ies', -1);
		}
		else
		{
			$str .= 's';
		}

		// Convert to uppsecase if nessasary
		if ($is_uppercase)
		{
			$str = strtoupper($str);
		}
		// Set the cache and return
		return Helper_Inflector::$cache[$key] = $str;
	}
	
	const CAMELIZE_UCWORDS_MATCH = '/[\s_]+/';
	const CAMELIZE_UCWORDS_REPLACE = ' ';
	const CAMELIZE_SUBSTR_MATCH = ' ';
	const CAMELIZE_SUBSTR_REPLACE = '';

	/**
	 * Makes a phrase camel case. Spaces and underscores will be removed.
	 *
	 *     $str = Helper_Inflector::camelize('mother cat');     // "motherCat"
	 *     $str = Helper_Inflector::camelize('kittens in bed'); // "kittensInBed"
	 *
	 * @param   string  $str    phrase to camelize
	 * @return  string
	 */
	public static function camelize($str)
	{
		$str = 'x'.strtolower(trim($str));
		$str = ucwords(preg_replace(Helper_Inflector::CAMELIZE_UCWORDS_MATCH, Helper_Inflector::CAMELIZE_UCWORDS_REPLACE, $str));

		return substr(str_replace(Helper_Inflector::CAMELIZE_SUBSTR_MATCH, Helper_Inflector::CAMELIZE_SUBSTR_REPLACE, $str), 1);
	}

	const DECAMELIZE_REGEX_MATCH = '/([a-z])([A-Z])/';
	
	/**
	 * Converts a camel case phrase into a spaced phrase.
	 *
	 *     $str = Helper_Inflector::decamelize('houseCat');    // "house cat"
	 *     $str = Helper_Inflector::decamelize('kingAllyCat'); // "king ally cat"
	 *
	 * @param   string  $str    phrase to camelize
	 * @param   string  $sep    word separator
	 * @return  string
	 */
	public static function decamelize($str, $sep = ' ')
	{
		return strtolower(preg_replace(Helper_Inflector::DECAMELIZE_REGEX_MATCH, '$1'.$sep.'$2', trim($str)));
	}
	
	const UNDERSCORE_REGEX_MATCH = '/\s+/';
	const UNDERSCORE_REGEX_REPLACE = '_';

	/**
	 * Makes a phrase underscored instead of spaced.
	 *
	 *     $str = Helper_Inflector::underscore('five cats'); // "five_cats";
	 *
	 * @param   string  $str    phrase to underscore
	 * @return  string
	 */
	public static function underscore($str)
	{
		return preg_replace(Helper_Inflector::UNDERSCORE_REGEX_MATCH, Helper_Inflector::UNDERSCORE_REGEX_REPLACE, trim($str));
	}

	const HUMANIZE_REGEX_MATCH = '/[_-]+/';
	const HUMANIZE_REGEX_REPLACE = ' ';
	
	/**
	 * Makes an underscored or dashed phrase human-readable.
	 *
	 *     $str = Helper_Inflector::humanize('kittens-are-cats'); // "kittens are cats"
	 *     $str = Helper_Inflector::humanize('dogs_as_well');     // "dogs as well"
	 *
	 * @param   string  $str    phrase to make human-readable
	 * @return  string
	 */
	public static function humanize($str)
	{
		return preg_replace(Helper_Inflector::HUMANIZE_REGEX_MATCH, Helper_Inflector::HUMANIZE_REGEX_REPLACE, trim($str));
	}

} // End Inflector
