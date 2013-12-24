<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 数据库驱动基础类
 *
 * @package    Kohana/Database
 * @category   Driver
 */
abstract class Kohana_Database_Driver extends Database {

	/**
	 * @var  string  上次执行的SQL
	 */
	protected $_last_query = '';

	/**
	 * 工厂方法
	 */
	public static function factory($name, $config = NULL)
	{
		// Set the driver class name
		$driver = 'Database_Driver_'.ucfirst($config['type']);

		// Create the database connection instance
		return new $driver($name, $config);
	}
	
	/**
	 * 上次执行的方法
	 */
	public function last_query($sql = NULL)
	{
		if ($sql === NULL)
		{
			return $this->_last_query;
		}
		$this->_last_query = (string) $sql;
	}
	
	/**
	 * 设置数据库连接字符集，这个方法应该在[Database::connect]中自动调用
	 *
	 *     $db->set_charset('utf8');
	 *
	 * @param   string   $charset  字符集
	 * @return  void
	 */
	abstract public function set_charset($charset);
	
	/**
	 * Perform an SQL query of the given type.
	 *
	 *     // Make a SELECT query and use objects for results
	 *     $db->query(Database::SELECT, 'SELECT * FROM groups', TRUE);
	 *
	 *     // Make a SELECT query and use "Model_User" for the results
	 *     $db->query(Database::SELECT, 'SELECT * FROM users LIMIT 1', 'Model_User');
	 *
	 * @param   integer  $type       Database::SELECT, Database::INSERT, etc
	 * @param   string   $sql        SQL query
	 * @param   mixed    $as_object  result object class string, TRUE for stdClass, FALSE for assoc array
	 * @param   array    $params     object construct parameters for result class
	 * @return  object   Database_Result for SELECT queries
	 * @return  array    list (insert id, row count) for INSERT queries
	 * @return  integer  number of affected rows for all other queries
	 */
	abstract public function query($type, $sql, $as_object = FALSE, array $params = NULL);
	
	/**
	 * Start a SQL transaction
	 *
	 *     // Start the transactions
	 *     $db->begin();
	 *
	 *     try {
	 *          DB::insert('users')->values($user1)...
	 *          DB::insert('users')->values($user2)...
	 *          // Insert successful commit the changes
	 *          $db->commit();
	 *     }
	 *     catch (Database_Exception $e)
	 *     {
	 *          // Insert failed. Rolling back changes...
	 *          $db->rollback();
	 *      }
	 *
	 * @param string $mode  transaction mode
	 * @return  boolean
	 */
	abstract public function begin($mode = NULL);

	/**
	 * Commit the current transaction
	 *
	 *     // Commit the database changes
	 *     $db->commit();
	 *
	 * @return  boolean
	 */
	abstract public function commit();

	/**
	 * Abort the current transaction
	 *
	 *     // Undo the changes
	 *     $db->rollback();
	 *
	 * @return  boolean
	 */
	abstract public function rollback();

	/**
	 * Count the number of records in a table.
	 *
	 *     // Get the total number of records in the "users" table
	 *     $count = $db->count_records('users');
	 *
	 * @param   mixed    $table  table name string or array(query, alias)
	 * @return  integer
	 */
	abstract public function count_records($table);
	
	/**
	 * 返回当前数据库的所有表明，支持LIKE查询
	 *
	 *     // 获取当前数据库的所有表
	 *     $tables = $db->list_tables();
	 *
	 *     // 传入查询条件的查询
	 *     $tables = $db->list_tables('user%');
	 *
	 * @param   string   $like  table to search for
	 * @return  array
	 */
	abstract public function list_tables($like = NULL);

	/**
	 * Lists all of the columns in a table. Optionally, a LIKE string can be
	 * used to search for specific fields.
	 *
	 *     // Get all columns from the "users" table
	 *     $columns = $db->list_columns('users');
	 *
	 *     // Get all name-related columns
	 *     $columns = $db->list_columns('users', '%name%');
	 *
	 *     // Get the columns from a table that doesn't use the table prefix
	 *     $columns = $db->list_columns('users', NULL, FALSE);
	 *
	 * @param   string  $table       table to get columns from
	 * @param   string  $like        column to search for
	 * @param   boolean $add_prefix  whether to add the table prefix automatically or not
	 * @return  array
	 */
	abstract public function list_columns($table, $like = NULL, $add_prefix = TRUE);
	
	/**
	 * 过滤字符串，防止SQL注射
	 *
	 *     $value = $db->escape('any string');
	 *
	 * @param   string   $value  value to quote
	 * @return  string
	 */
	abstract public function escape($value);
}
