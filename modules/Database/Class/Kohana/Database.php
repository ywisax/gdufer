<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 数据库连接类
 *
 * You may get a database instance using `Database::instance('name')` where
 * name is the [config](database/config) group.
 *
 * This class provides connection instance management via Database Drivers, as
 * well as quoting, escaping and other related functions. Querys are done using
 * [Database_Query] and [Database_Query_Builder] objects, which can be easily
 * created using the [DB] helper class.
 *
 * @package    Kohana/Database
 * @category   Base
 */
abstract class Kohana_Database {

	// 请求类型
	const SELECT =  1;
	const INSERT =  2;
	const UPDATE =  3;
	const DELETE =  4;

	/**
	 * @var  string  默认实例名
	 */
	public static $default = 'default';

	/**
	 * @var  array  数据库实例
	 */
	public static $instances = array();

	/**
	 * Get a singleton Database instance. If configuration is not specified,
	 * it will be loaded from the database configuration file using the same
	 * group as the name.
	 *
	 *     // Load the default database
	 *     $db = Database::instance();
	 *
	 *     // Create a custom configured instance
	 *     $db = Database::instance('custom', $config);
	 *
	 * @param   string   $name    instance name
	 * @param   array    $config  configuration parameters
	 * @return  Database
	 */
	public static function instance($name = NULL, array $config = NULL)
	{
		if ($name === NULL)
		{
			// 使用默认实例名
			$name = Database::$default;
		}

		if ( ! isset(Database::$instances[$name]))
		{
			if ($config === NULL)
			{
				// Load the configuration for this database
				$config = Kohana::config('Database')->$name;
			}

			if ( ! isset($config['type']))
			{
				throw new Kohana_Exception('Database type not defined in :name configuration',
					array(':name' => $name));
			}

			$driver = Database_Driver::factory($name, $config);

			// Store the database instance
			Database::$instances[$name] = $driver;
		}

		return Database::$instances[$name];
	}

	// Character that is used to quote identifiers
	protected $_identifier = '"';

	// Instance name
	protected $_instance;

	// Raw server connection
	protected $_connection;

	// Configuration array
	protected $_config;

	/**
	 * Stores the database configuration locally and name the instance.
	 *
	 * [!!] This method cannot be accessed directly, you must use [Database::instance].
	 *
	 * @return  void
	 */
	public function __construct($name, array $config)
	{
		// Set the instance name
		$this->_instance = $name;

		// Store the config locally
		$this->_config = $config;

		if (empty($this->_config['table_prefix']))
		{
			$this->_config['table_prefix'] = '';
		}
	}

	/**
	 * Disconnect from the database when the object is destroyed.
	 *
	 *     // Destroy the database instance
	 *     unset(Database::instances[(string) $db], $db);
	 *
	 * [!!] Calling `unset($db)` is not enough to destroy the database, as it
	 * will still be stored in `Database::$instances`.
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		$this->disconnect();
	}

	/**
	 * Returns the database instance name.
	 *
	 *     echo (string) $db;
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->_instance;
	}

	/**
	 * Connect to the database. This is called automatically when the first
	 * query is executed.
	 *
	 *     $db->connect();
	 *
	 * @return  void
	 */
	abstract public function connect();

	/**
	 * 断开数据库连接。一般这个方法会在[Database::__destruct]中自动调用
	 *
	 *     $db->disconnect();
	 *
	 * @return  boolean
	 */
	public function disconnect()
	{
		unset(Database::$instances[$this->_instance]);

		return TRUE;
	}

	/**
	 * Returns a normalized array describing the SQL data type
	 *
	 *     $db->datatype('char');
	 *
	 * @param   string  $type  SQL data type
	 * @return  array
	 */
	public function datatype($type)
	{
		static $types = array
		(
			// SQL-92
			'bit'                           => array('type' => 'string', 'exact' => TRUE),
			'bit varying'                   => array('type' => 'string'),
			'char'                          => array('type' => 'string', 'exact' => TRUE),
			'char varying'                  => array('type' => 'string'),
			'character'                     => array('type' => 'string', 'exact' => TRUE),
			'character varying'             => array('type' => 'string'),
			'date'                          => array('type' => 'string'),
			'dec'                           => array('type' => 'float', 'exact' => TRUE),
			'decimal'                       => array('type' => 'float', 'exact' => TRUE),
			'double precision'              => array('type' => 'float'),
			'float'                         => array('type' => 'float'),
			'int'                           => array('type' => 'int', 'min' => '-2147483648', 'max' => '2147483647'),
			'integer'                       => array('type' => 'int', 'min' => '-2147483648', 'max' => '2147483647'),
			'interval'                      => array('type' => 'string'),
			'national char'                 => array('type' => 'string', 'exact' => TRUE),
			'national char varying'         => array('type' => 'string'),
			'national character'            => array('type' => 'string', 'exact' => TRUE),
			'national character varying'    => array('type' => 'string'),
			'nchar'                         => array('type' => 'string', 'exact' => TRUE),
			'nchar varying'                 => array('type' => 'string'),
			'numeric'                       => array('type' => 'float', 'exact' => TRUE),
			'real'                          => array('type' => 'float'),
			'smallint'                      => array('type' => 'int', 'min' => '-32768', 'max' => '32767'),
			'time'                          => array('type' => 'string'),
			'time with time zone'           => array('type' => 'string'),
			'timestamp'                     => array('type' => 'string'),
			'timestamp with time zone'      => array('type' => 'string'),
			'varchar'                       => array('type' => 'string'),

			// SQL:1999
			'binary large object'               => array('type' => 'string', 'binary' => TRUE),
			'blob'                              => array('type' => 'string', 'binary' => TRUE),
			'boolean'                           => array('type' => 'bool'),
			'char large object'                 => array('type' => 'string'),
			'character large object'            => array('type' => 'string'),
			'clob'                              => array('type' => 'string'),
			'national character large object'   => array('type' => 'string'),
			'nchar large object'                => array('type' => 'string'),
			'nclob'                             => array('type' => 'string'),
			'time without time zone'            => array('type' => 'string'),
			'timestamp without time zone'       => array('type' => 'string'),

			// SQL:2003
			'bigint'    => array('type' => 'int', 'min' => '-9223372036854775808', 'max' => '9223372036854775807'),

			// SQL:2008
			'binary'            => array('type' => 'string', 'binary' => TRUE, 'exact' => TRUE),
			'binary varying'    => array('type' => 'string', 'binary' => TRUE),
			'varbinary'         => array('type' => 'string', 'binary' => TRUE),
		);

		if (isset($types[$type]))
			return $types[$type];

		return array();
	}

	/**
	 * Extracts the text between parentheses, if any.
	 *
	 *     // Returns: array('CHAR', '6')
	 *     list($type, $length) = $db->_parse_type('CHAR(6)');
	 *
	 * @param   string  $type
	 * @return  array   list containing the type and length, if any
	 */
	protected function _parse_type($type)
	{
		if (($open = strpos($type, '(')) === FALSE)
		{
			// No length specified
			return array($type, NULL);
		}

		// Closing parenthesis
		$close = strrpos($type, ')', $open);

		// Length without parentheses
		$length = substr($type, $open + 1, $close - 1 - $open);

		// Type without the length
		$type = substr($type, 0, $open).substr($type, $close + 1);

		return array($type, $length);
	}

	/**
	 * 返回当前设置的表名前缀
	 *
	 *     $prefix = $db->table_prefix();
	 *
	 * @return  string
	 */
	public function table_prefix()
	{
		return $this->_config['table_prefix'];
	}

	/**
	 * Quote a value for an SQL query.
	 *
	 *     $db->quote(NULL);   // 'NULL'
	 *     $db->quote(10);     // 10
	 *     $db->quote('fred'); // 'fred'
	 *
	 * Objects passed to this function will be converted to strings.
	 * [Database_Query_Expression] objects will be compiled.
	 * [Database_Query] objects will be compiled and converted to a sub-query.
	 * All other objects will be converted using the `__toString` method.
	 *
	 * @param   mixed   $value  any value to quote
	 * @return  string
	 */
	public function quote($value)
	{
		if ($value === NULL)
		{
			return 'NULL';
		}
		elseif ($value === TRUE)
		{
			return "'1'";
		}
		elseif ($value === FALSE)
		{
			return "'0'";
		}
		elseif (is_object($value))
		{
			if ($value instanceof Database_Query)
			{
				// Create a sub-query
				return '('.$value->compile($this).')';
			}
			elseif ($value instanceof Database_Query_Expression)
			{
				// Compile the expression
				return $value->compile($this);
			}
			else
			{
				// 转换对象为字符串
				return $this->quote( (string) $value);
			}
		}
		elseif (is_array($value))
		{
			return '('.implode(', ', array_map(array($this, __FUNCTION__), $value)).')';
		}
		elseif (is_int($value))
		{
			return (int) $value;
		}
		elseif (is_float($value))
		{
			// Convert to non-locale aware float to prevent possible commas
			return sprintf('%F', $value);
		}

		return $this->escape($value);
	}

	/**
	 * Quote a database column name and add the table prefix if needed.
	 *
	 *     $column = $db->quote_column($column);
	 *
	 * You can also use SQL methods within identifiers.
	 *
	 *     $column = $db->quote_column(DB::expr('COUNT(`column`)'));
	 *
	 * Objects passed to this function will be converted to strings.
	 * [Database_Query_Expression] objects will be compiled.
	 * [Database_Query] objects will be compiled and converted to a sub-query.
	 * All other objects will be converted using the `__toString` method.
	 *
	 * @param   mixed   $column  column name or array(column, alias)
	 * @return  string
	 */
	public function quote_column($column)
	{
		// Identifiers are escaped by repeating them
		$escaped_identifier = $this->_identifier.$this->_identifier;

		if (is_array($column))
		{
			list($column, $alias) = $column;
			$alias = str_replace($this->_identifier, $escaped_identifier, $alias);
		}

		if ($column instanceof Database_Query)
		{
			// 子查询
			$column = '('.$column->compile($this).')';
		}
		elseif ($column instanceof Database_Query_Expression)
		{
			// Compile the expression
			$column = $column->compile($this);
		}
		else
		{
			// Convert to a string
			$column = (string) $column;

			$column = str_replace($this->_identifier, $escaped_identifier, $column);

			if ($column === '*')
			{
				return $column;
			}
			elseif (strpos($column, '.') !== FALSE)
			{
				$parts = explode('.', $column);

				if ($prefix = $this->table_prefix())
				{
					// Get the offset of the table name, 2nd-to-last part
					$offset = count($parts) - 2;

					// Add the table prefix to the table name
					$parts[$offset] = $prefix.$parts[$offset];
				}

				foreach ($parts AS & $part)
				{
					if ($part !== '*')
					{
						// Quote each of the parts
						$part = $this->_identifier.$part.$this->_identifier;
					}
				}

				$column = implode('.', $parts);
			}
			else
			{
				$column = $this->_identifier.$column.$this->_identifier;
			}
		}

		if (isset($alias))
		{
			$column .= ' AS '.$this->_identifier.$alias.$this->_identifier;
		}

		return $column;
	}

	/**
	 * Quote a database table name and adds the table prefix if needed.
	 *
	 *     $table = $db->quote_table($table);
	 *
	 * Objects passed to this function will be converted to strings.
	 * [Database_Query_Expression] objects will be compiled.
	 * [Database_Query] objects will be compiled and converted to a sub-query.
	 * All other objects will be converted using the `__toString` method.
	 *
	 * @param   mixed   $table  table name or array(table, alias)
	 * @return  string
	 */
	public function quote_table($table)
	{
		// Identifiers are escaped by repeating them
		$escaped_identifier = $this->_identifier.$this->_identifier;

		if (is_array($table))
		{
			list($table, $alias) = $table;
			$alias = str_replace($this->_identifier, $escaped_identifier, $alias);
		}

		if ($table instanceof Database_Query)
		{
			// Create a sub-query
			$table = '('.$table->compile($this).')';
		}
		elseif ($table instanceof Database_Query_Expression)
		{
			// Compile the expression
			$table = $table->compile($this);
		}
		else
		{
			// Convert to a string
			$table = (string) $table;

			$table = str_replace($this->_identifier, $escaped_identifier, $table);

			if (strpos($table, '.') !== FALSE)
			{
				$parts = explode('.', $table);

				if ($prefix = $this->table_prefix())
				{
					// Get the offset of the table name, last part
					$offset = count($parts) - 1;

					// Add the table prefix to the table name
					$parts[$offset] = $prefix.$parts[$offset];
				}

				foreach ($parts AS & $part)
				{
					// Quote each of the parts
					$part = $this->_identifier.$part.$this->_identifier;
				}

				$table = implode('.', $parts);
			}
			else
			{
				// Add the table prefix
				$table = $this->_identifier.$this->table_prefix().$table.$this->_identifier;
			}
		}

		if (isset($alias))
		{
			// Attach table prefix to alias
			$table .= ' AS '.$this->_identifier.$this->table_prefix().$alias.$this->_identifier;
		}

		return $table;
	}

	/**
	 * Quote a database identifier
	 *
	 * Objects passed to this function will be converted to strings.
	 * [Database_Query_Expression] objects will be compiled.
	 * [Database_Query] objects will be compiled and converted to a sub-query.
	 * All other objects will be converted using the `__toString` method.
	 *
	 * @param   mixed   $value  any identifier
	 * @return  string
	 */
	public function quote_identifier($value)
	{
		// Identifiers are escaped by repeating them
		$escaped_identifier = $this->_identifier.$this->_identifier;

		if (is_array($value))
		{
			list($value, $alias) = $value;
			$alias = str_replace($this->_identifier, $escaped_identifier, $alias);
		}

		if ($value instanceof Database_Query)
		{
			// Create a sub-query
			$value = '('.$value->compile($this).')';
		}
		elseif ($value instanceof Database_Query_Expression)
		{
			// Compile the expression
			$value = $value->compile($this);
		}
		else
		{
			// Convert to a string
			$value = (string) $value;

			$value = str_replace($this->_identifier, $escaped_identifier, $value);

			if (strpos($value, '.') !== FALSE)
			{
				$parts = explode('.', $value);

				foreach ($parts AS & $part)
				{
					// Quote each of the parts
					$part = $this->_identifier.$part.$this->_identifier;
				}

				$value = implode('.', $parts);
			}
			else
			{
				$value = $this->_identifier.$value.$this->_identifier;
			}
		}

		if (isset($alias))
		{
			$value .= ' AS '.$this->_identifier.$alias.$this->_identifier;
		}

		return $value;
	}
} // End Database_Connection
