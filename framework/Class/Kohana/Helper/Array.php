<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 数组助手类.
 *
 * @package    Kohana
 * @category   Helpers
 */
class Kohana_Helper_Array {

	/**
	 * @var  string  用于`path()`方法的默认分隔符
	 */
	public static $delimiter = '.';

	/**
	 * 删除所有带空值的数组项，暂时不支持多维数组的处理
	 *
	 * @param array $input
	 * @return array
	 */
	public static function unset_empty(array $input, $call = FALSE)
	{
		if ( ! is_array($input) OR empty($input))
		{
			return array();
		}

		foreach ($input AS $key => $value)
		{
			if (is_string($value) && $value == '')
			{
				unset($input[$key]);
			}
		}
		return $input;
	}
	
	/**
	 * 检查一个数字是否为关联数组
	 *
	 *     // 返回TRUE
	 *     Helper_Array::is_assoc(array('username' => 'john.doe'));
	 *
	 *     // 返回FALSE
	 *     Helper_Array::is_assoc('foo', 'bar');
	 *
	 * @param   array   $array  要检查的数组
	 * @return  boolean
	 */
	public static function is_assoc(array $array)
	{
		// 数组键名
		$keys = array_keys($array);

		// If the array keys of the keys match the keys, then the array must
		// not be associative (e.g. the keys array looked like {0:0, 1:1...}).
		return array_keys($keys) !== $keys;
	}

	/**
	 * 检查是否为数组或者像数组对象
	 *
	 *     // 返回TRUE
	 *     Helper_Array::is_array(array());
	 *     Helper_Array::is_array(new ArrayObject);
	 *
	 *     // 返回FALSE
	 *     Helper_Array::is_array(FALSE);
	 *     Helper_Array::is_array('not an array!');
	 *     Helper_Array::is_array(Database::instance());
	 *
	 * @param   mixed   $value  要检查的数据
	 * @return  boolean
	 */
	public static function is_array($value)
	{
		return is_array($value)
			? TRUE // 类型为数组
			: ((is_object($value) AND $value instanceof Traversable)); // 可能是一个Traversable对象，功能上跟数组一样
	}

	/**
	 * Checks if a variable is traversable.
	 *
	 * @param mixed $value A variable
	 *
	 * @return Boolean true if the value is traversable
	 */
	function is_iterable($value)
	{
		return $value instanceof Traversable || is_array($value);
	}

	/**
	 * Returns the keys for the given array.
	 *
	 * @param array $array An array
	 *
	 * @return array The keys
	 */
	public static function keys($array)
	{
		if (is_object($array) && $array instanceof Traversable)
		{
			return array_keys(iterator_to_array($array));
		}
		if ( ! is_array($array))
		{
			return array();
		}
		return array_keys($array);
	}

	/**
	 * 使用路径的方法来获取变量值
	 *
	 *     // 获取$array['foo']['bar']
	 *     $value = Helper_Array::path($array, 'foo.bar');
	 *
	 * Using a wildcard "*" will search intermediate arrays and return an array.
	 *
	 *     // Get the values of "color" in theme
	 *     $colors = Helper_Array::path($array, 'theme.*.color');
	 *
	 *     // Using an array of keys
	 *     $colors = Helper_Array::path($array, array('theme', '*', 'color'));
	 *
	 * @param   array   $array      array to search
	 * @param   mixed   $path       key path string (delimiter separated) or array of keys
	 * @param   mixed   $default    default value if the path is not set
	 * @param   string  $delimiter  key path delimiter
	 * @return  mixed
	 */
	public static function path($array, $path, $default = NULL, $delimiter = NULL)
	{
		if ( ! Helper_Array::is_array($array))
		{
			// This is not an array!
			return $default;
		}

		if (is_array($path))
		{
			// The path has already been separated into keys
			$keys = $path;
		}
		else
		{
			if (array_key_exists($path, $array))
			{
				// No need to do extra processing
				return $array[$path];
			}

			if ($delimiter === NULL)
			{
				// Use the default delimiter
				$delimiter = Helper_Array::$delimiter;
			}

			// Remove starting delimiters and spaces
			$path = ltrim($path, "{$delimiter} ");

			// Remove ending delimiters, spaces, and wildcards
			$path = rtrim($path, "{$delimiter} *");

			// Split the keys by delimiter
			$keys = explode($delimiter, $path);
		}

		do
		{
			$key = array_shift($keys);

			if (ctype_digit($key))
			{
				// Make the key an integer
				$key = (int) $key;
			}

			if (isset($array[$key]))
			{
				if ($keys)
				{
					if (Helper_Array::is_array($array[$key]))
					{
						// Dig down into the next part of the path
						$array = $array[$key];
					}
					else
					{
						// Unable to dig deeper
						break;
					}
				}
				else
				{
					// Found the path requested
					return $array[$key];
				}
			}
			elseif ($key === '*')
			{
				// Handle wildcards

				$values = array();
				foreach ($array AS $arr)
				{
					if ($value = Helper_Array::path($arr, implode('.', $keys)))
					{
						$values[] = $value;
					}
				}

				if ($values)
				{
					// Found the values requested
					return $values;
				}
				else
				{
					// Unable to dig deeper
					break;
				}
			}
			else
			{
				// Unable to dig deeper
				break;
			}
		}
		while ($keys);

		// Unable to find the value requested
		return $default;
	}

	/**
	* Set a value on an array by path.
	*
	* @see Helper_Array::path()
	* @param array   $array     Array to update
	* @param string  $path      Path
	* @param mixed   $value     Value to set
	* @param string  $delimiter Path delimiter
	*/
	public static function set_path( & $array, $path, $value, $delimiter = NULL)
	{
		if ( ! $delimiter)
		{
			// Use the default delimiter
			$delimiter = Helper_Array::$delimiter;
		}

		// Split the keys by delimiter
		$keys = explode($delimiter, $path);

		// Set current $array to inner-most array path
		while (count($keys) > 1)
		{
			$key = array_shift($keys);

			if (ctype_digit($key))
			{
				// Make the key an integer
				$key = (int) $key;
			}

			if ( ! isset($array[$key]))
			{
				$array[$key] = array();
			}

			$array = & $array[$key];
		}

		// Set key on inner-most array
		$array[array_shift($keys)] = $value;
	}

	/**
	 * Fill an array with a range of numbers.
	 *
	 *     // Fill an array with values 5, 10, 15, 20
	 *     $values = Helper_Array::range(5, 20);
	 *
	 * @param   integer $step   stepping
	 * @param   integer $max    ending number
	 * @return  array
	 */
	public static function range($step = 10, $max = 100)
	{
		if ($step < 1)
			return array();

		$array = array();
		for ($i = $step; $i <= $max; $i += $step)
		{
			$array[$i] = $i;
		}

		return $array;
	}

	/**
	 * Retrieve a single key from an array. If the key does not exist in the
	 * array, the default value will be returned instead.
	 *
	 *     // Get the value "username" from $_POST, if it exists
	 *     $username = Helper_Array::get($_POST, 'username');
	 *
	 *     // Get the value "sorting" from $_GET, if it exists
	 *     $sorting = Helper_Array::get($_GET, 'sorting');
	 *
	 * @param   array   $array      array to extract from
	 * @param   string  $key        key name
	 * @param   mixed   $default    default value
	 * @return  mixed
	 */
	public static function get($array, $key, $default = NULL)
	{
		return isset($array[$key]) ? $array[$key] : $default;
	}

	/**
	 * Retrieves multiple paths from an array. If the path does not exist in the
	 * array, the default value will be added instead.
	 *
	 *     // Get the values "username", "password" from $_POST
	 *     $auth = Helper_Array::extract($_POST, array('username', 'password'));
	 *     
	 *     // Get the value "level1.level2a" from $data
	 *     $data = array('level1' => array('level2a' => 'value 1', 'level2b' => 'value 2'));
	 *     Helper_Array::extract($data, array('level1.level2a', 'password'));
	 *
	 * @param   array  $array    array to extract paths from
	 * @param   array  $paths    list of path
	 * @param   mixed  $default  default value
	 * @return  array
	 */
	public static function extract($array, array $paths, $default = NULL)
	{
		$found = array();
		foreach ($paths AS $path)
		{
			Helper_Array::set_path($found, $path, Helper_Array::path($array, $path, $default));
		}

		return $found;
	}

	/**
	 * Retrieves muliple single-key values from a list of arrays.
	 *
	 *     // Get all of the "id" values from a result
	 *     $ids = Helper_Array::pluck($result, 'id');
	 *
	 * [!!] A list of arrays is an array that contains arrays, eg: array(array $a, array $b, array $c, ...)
	 *
	 * @param   array   $array  list of arrays to check
	 * @param   string  $key    key to pluck
	 * @return  array
	 */
	public static function pluck($array, $key)
	{
		$values = array();

		foreach ($array AS $row)
		{
			if (isset($row[$key]))
			{
				// Found a value in this row
				$values[] = $row[$key];
			}
		}

		return $values;
	}

	/**
	 * Adds a value to the beginning of an associative array.
	 *
	 *     // Add an empty value to the start of a select list
	 *     Helper_Array::unshift($array, 'none', 'Select a value');
	 *
	 * @param   array   $array  array to modify
	 * @param   string  $key    array key name
	 * @param   mixed   $val    array value
	 * @return  array
	 */
	public static function unshift( array & $array, $key, $val)
	{
		$array = array_reverse($array, TRUE);
		$array[$key] = $val;
		$array = array_reverse($array, TRUE);

		return $array;
	}

	/**
	 * Recursive version of [array_map](http://php.net/array_map), applies one or more
	 * callbacks to all elements in an array, including sub-arrays.
	 *
	 *     // Apply "strip_tags" to every element in the array
	 *     $array = Helper_Array::map('strip_tags', $array);
	 *
	 *     // Apply $this->filter to every element in the array
	 *     $array = Helper_Array::map(array(array($this,'filter')), $array);
	 *
	 *     // Apply strip_tags and $this->filter to every element
	 *     $array = Helper_Array::map(array('strip_tags', array($this, 'filter')), $array);
	 *
	 * [!!] Because you can pass an array of callbacks, if you wish to use an array-form callback
	 * you must nest it in an additional array as above. Calling Helper_Array::map(array($this, 'filter'), $array)
	 * will cause an error.
	 * [!!] Unlike `array_map`, this method requires a callback and will only map
	 * a single array.
	 *
	 * @param   mixed   $callbacks  array of callbacks to apply to every element in the array
	 * @param   array   $array      array to map
	 * @param   array   $keys       array of keys to apply to
	 * @return  array
	 */
	public static function map($callbacks, $array, $keys = NULL)
	{
		foreach ($array AS $key => $val)
		{
			if (is_array($val))
			{
				$array[$key] = Helper_Array::map($callbacks, $array[$key]);
			}
			elseif ( ! is_array($keys) OR in_array($key, $keys))
			{
				if (is_array($callbacks))
				{
					foreach ($callbacks AS $cb)
					{
						$array[$key] = call_user_func($cb, $array[$key]);
					}
				}
				else
				{
					$array[$key] = call_user_func($callbacks, $array[$key]);
				}
			}
		}

		return $array;
	}

	/**
	 * Recursively merge two or more arrays. Values in an associative array
	 * overwrite previous values with the same key. Values in an indexed array
	 * are appended, but only when they do not already exist in the result.
	 *
	 * Note that this does not work the same as [array_merge_recursive](http://php.net/array_merge_recursive)!
	 *
	 *     $john = array('name' => 'john', 'children' => array('fred', 'paul', 'sally', 'jane'));
	 *     $mary = array('name' => 'mary', 'children' => array('jane'));
	 *
	 *     // John and Mary are married, merge them together
	 *     $john = Helper_Array::merge($john, $mary);
	 *
	 *     // The output of $john will now be:
	 *     array('name' => 'mary', 'children' => array('fred', 'paul', 'sally', 'jane'))
	 *
	 * @param   array  $array1      initial array
	 * @param   array  $array2,...  array to merge
	 * @return  array
	 */
	public static function merge($array1, $array2)
	{
		if (Helper_Array::is_assoc($array2))
		{
			foreach ($array2 AS $key => $value)
			{
				if (is_array($value)
					AND isset($array1[$key])
					AND is_array($array1[$key])
				)
				{
					$array1[$key] = Helper_Array::merge($array1[$key], $value);
				}
				else
				{
					$array1[$key] = $value;
				}
			}
		}
		else
		{
			foreach ($array2 AS $value)
			{
				if ( ! in_array($value, $array1, TRUE))
				{
					$array1[] = $value;
				}
			}
		}

		if (func_num_args() > 2)
		{
			foreach (array_slice(func_get_args(), 2) AS $array2)
			{
				if (Helper_Array::is_assoc($array2))
				{
					foreach ($array2 AS $key => $value)
					{
						if (is_array($value)
							AND isset($array1[$key])
							AND is_array($array1[$key])
						)
						{
							$array1[$key] = Helper_Array::merge($array1[$key], $value);
						}
						else
						{
							$array1[$key] = $value;
						}
					}
				}
				else
				{
					foreach ($array2 AS $value)
					{
						if ( ! in_array($value, $array1, TRUE))
						{
							$array1[] = $value;
						}
					}
				}
			}
		}

		return $array1;
	}

	/**
	 * Overwrites an array with values from input arrays.
	 * Keys that do not exist in the first array will not be added!
	 *
	 *     $a1 = array('name' => 'john', 'mood' => 'happy', 'food' => 'bacon');
	 *     $a2 = array('name' => 'jack', 'food' => 'tacos', 'drink' => 'beer');
	 *
	 *     // Overwrite the values of $a1 with $a2
	 *     $array = Helper_Array::overwrite($a1, $a2);
	 *
	 *     // The output of $array will now be:
	 *     array('name' => 'jack', 'mood' => 'happy', 'food' => 'tacos')
	 *
	 * @param   array   $array1 master array
	 * @param   array   $array2 input arrays that will overwrite existing values
	 * @return  array
	 */
	public static function overwrite($array1, $array2)
	{
		foreach (array_intersect_key($array2, $array1) AS $key => $value)
		{
			$array1[$key] = $value;
		}

		if (func_num_args() > 2)
		{
			foreach (array_slice(func_get_args(), 2) AS $array2)
			{
				foreach (array_intersect_key($array2, $array1) AS $key => $value)
				{
					$array1[$key] = $value;
				}
			}
		}

		return $array1;
	}
	
	const CALLBACK_COMMAND_REGEX = '/^([^\(]*+)\((.*)\)$/';
	const CALLBACK_COMMAND_SPLIT_REGEX = '/(?<!\\\\),/';
	const CALLBACK_COMMAND_REPLACE_REGEX = '\,';
	const CALLBACK_COMMAND_REPLACE_REPLACE = ',';

	/**
	 * Creates a callable function and parameter list from a string representation.
	 * Note that this function does not validate the callback string.
	 *
	 *     // Get the callback function and parameters
	 *     list($func, $params) = Helper_Array::callback('Foo::bar(apple,orange)');
	 *
	 *     // Get the result of the callback
	 *     $result = call_user_func_array($func, $params);
	 *
	 * @param   string  $str    callback string
	 * @return  array   function, params
	 */
	public static function callback($str)
	{
		// Overloaded as parts are found
		$command = $params = NULL;

		// command[param,param]
		if (preg_match(Helper_Array::CALLBACK_COMMAND_REGEX, $str, $match))
		{
			// command
			$command = $match[1];

			if ($match[2] !== '')
			{
				// param,param
				$params = preg_split(Helper_Array::CALLBACK_COMMAND_SPLIT_REGEX, $match[2]);
				$params = str_replace(
					Helper_Array::CALLBACK_COMMAND_REPLACE_REGEX,
					Helper_Array::CALLBACK_COMMAND_REPLACE_REPLACE,
					$params
				);
			}
		}
		else
		{
			// command
			$command = $str;
		}

		if (strpos($command, '::') !== FALSE)
		{
			// Create a static method callable command
			$command = explode('::', $command, 2);
		}

		return array($command, $params);
	}

	/**
	 * Convert a multi-dimensional array into a single-dimensional array.
	 *
	 *     $array = array('set' => array('one' => 'something'), 'two' => 'other');
	 *
	 *     // Flatten the array
	 *     $array = Helper_Array::flatten($array);
	 *
	 *     // The array will now be
	 *     array('one' => 'something', 'two' => 'other');
	 *
	 * [!!] The keys of array values will be discarded.
	 *
	 * @param   array   $array  array to flatten
	 * @return  array
	 */
	public static function flatten($array)
	{
		$is_assoc = Helper_Array::is_assoc($array);

		$flat = array();
		foreach ($array AS $key => $value)
		{
			if (is_array($value))
			{
				$flat = array_merge($flat, Helper_Array::flatten($value));
			}
			else
			{
				if ($is_assoc)
				{
					$flat[$key] = $value;
				}
				else
				{
					$flat[] = $value;
				}
			}
		}
		return $flat;
	}
	
	/**
	 * 将关联数组的属性-值copy到对象上去
	 *
	 *     Arr::mix($myobj, array('foo' => 'bar'), TRUE);
	 *
	 * @param mixed   被copy到的对象
	 * @param array   关联数组
	 * @param boolean 是否覆盖对象上已有属性
	 * @return mixed  被copy到的对象
	 */
	public static function mix($obj, $hash, $override = FALSE)
	{
		foreach ($hash AS $key => $value)
		{
			if ($override || !isset($obj->{$key}))
			{
				$obj->{$key} = $value;
			}
		}
		return $obj;
	}
	
	/**
	*	stripslashes 取消转义 数组
	*
	*	1 参数 输入数组
	*
	*	返回值 处理后的数组
	**/
	public static function stripslashes($value)
	{
		if (is_array($value))
		{
			$value = array_map('Helper_Array::stripslashes', $value);
		}
		elseif (is_object($value))
		{
			$vars = get_object_vars($value);
			foreach ($vars AS $key => $data)
			{
				$value->{$key} = Helper_Array::stripslashes($data);
			}
		}
		else
		{
			$value = stripslashes($value);
		}
		return $value;
	}
	
	/**
	*	转换成 数组
	*
	*	1 参数 需要进行处理的 类 或者 数组 支持多维数组
	*
	*	返回值 处理后的数组
	**/
	public static function to_array($arr = array())
	{
		$arr = (array) $arr;
		$r = array();
		foreach ($arr AS $k => $v)
		{
			$r[$k] = (is_object($v) OR is_array($v))
				? Helper_Array::to_array($v)
				: $v;
		}
		return $r;
	}
	
	/**
	 * Batches item.
	 *
	 * @param array   $items An array of items
	 * @param integer $size  The size of the batch
	 * @param mixed   $fill  A value used to fill missing items
	 *
	 * @return array
	 */
	public static function batch($items, $size, $fill = NULL)
	{
		if ($items instanceof Traversable)
		{
			$items = iterator_to_array($items, FALSE);
		}

		$size = ceil($size);

		$result = array_chunk($items, $size, TRUE);

		if ($fill !== NULL)
		{
			$last = count($result) - 1;
			if ($fillCount = $size - count($result[$last]))
			{
				$result[$last] = array_merge(
					$result[$last],
					array_fill(0, $fillCount, $fill)
				);
			}
		}

		return $result;
	}
	
	/**
	 * Joins the values to a string.
	 *
	 * @param array  $value An array
	 * @param string $glue  The separator
	 *
	 * @return string The concatenated string
	 */
	public static function join($value, $glue = '')
	{
		if ($value instanceof Traversable)
		{
			$value = iterator_to_array($value, FALSE);
		}
		return implode($glue, (array) $value);
	}

	/**
	 * Splits the string into an array.
	 *
	 * @param string  $value     A string
	 * @param string  $delimiter The delimiter
	 * @param integer $limit     The limit
	 *
	 * @return array The split string as an array
	 */
	public static function split($value, $delimiter, $limit = NULL)
	{
		if (empty($delimiter))
		{
			return str_split($value, null === $limit ? 1 : $limit);
		}
		return ($limit === NULL) ? explode($delimiter, $value) : explode($delimiter, $value, $limit);
	}
} // End Helper_Array
