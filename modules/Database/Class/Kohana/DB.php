<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 提供一些数据库查询的快捷方式
 *
 * 快捷方式     | 返回对象
 * -------------|---------------
 * [`DB::query()`](#query)   | [Database_Query]
 * [`DB::insert()`](#insert) | [Database_Query_Builder_Insert]
 * [`DB::select()`](#select),<br />[`DB::select_array()`](#select_array) | [Database_Query_Builder_Select]
 * [`DB::update()`](#update) | [Database_Query_Builder_Update]
 * [`DB::delete()`](#delete) | [Database_Query_Builder_Delete]
 * [`DB::expr()`](#expr)     | [Database_Query_Expression]
 *
 * @package    Kohana/Database
 * @category   Base
 */
class Kohana_DB {

	/**
	 * 根据指定类型创建[Database_Query]实例.
	 *
	 *     $query = DB::query(Database::SELECT, 'SELECT * FROM users');
	 *
	 *     $query = DB::query(Database::DELETE, 'DELETE FROM users WHERE id = 5');
	 *
	 * @param   integer  $type  type: Database::SELECT, Database::UPDATE, etc
	 * @param   string   $sql   SQL statement
	 * @return  Database_Query
	 */
	public static function query($type, $sql)
	{
		return new Database_Query($type, $sql);
	}

	/**
	 * 根据指定类型创建[Database_Query_Builder_Select]实例.
	 *
	 *     // SELECT id, username
	 *     $query = DB::select('id', 'username');
	 *
	 *     // SELECT id AS user_id
	 *     $query = DB::select(array('id', 'user_id'));
	 *
	 * @param   mixed   $columns  column name or array($column, $alias) or object
	 * @return  Database_Query_Builder_Select
	 */
	public static function select($columns = NULL)
	{
		return new Database_Query_Builder_Select(func_get_args());
	}

	/**
	 * 传入一个由字段组成的数组，创建[Database_Query_Builder_Select]实例.
	 *
	 *     // SELECT id, username
	 *     $query = DB::select_array(array('id', 'username'));
	 *
	 * @param   array   $columns  字段数组
	 * @return  Database_Query_Builder_Select
	 */
	public static function select_array(array $columns = NULL)
	{
		return new Database_Query_Builder_Select($columns);
	}

	/**
	 * 根据指定类型创建[Database_Query_Builder_Insert]实例.
	 *
	 *     // INSERT INTO users (id, username)
	 *     $query = DB::insert('users', array('id', 'username'));
	 *
	 * @param   string  $table    要插入数据的表
	 * @param   array   $columns  list of column names or array($column, $alias) or object
	 * @return  Database_Query_Builder_Insert
	 */
	public static function insert($table = NULL, array $columns = NULL)
	{
		return new Database_Query_Builder_Insert($table, $columns);
	}

	/**
	 * Create a new [Database_Query_Builder_Update].
	 *
	 *     // UPDATE users
	 *     $query = DB::update('users');
	 *
	 * @param   string  $table  table to update
	 * @return  Database_Query_Builder_Update
	 */
	public static function update($table = NULL)
	{
		return new Database_Query_Builder_Update($table);
	}

	/**
	 * Create a new [Database_Query_Builder_Delete].
	 *
	 *     // DELETE FROM users
	 *     $query = DB::delete('users');
	 *
	 * @param   string  $table  table to delete from
	 * @return  Database_Query_Builder_Delete
	 */
	public static function delete($table = NULL)
	{
		return new Database_Query_Builder_Delete($table);
	}

	/**
	 * Create a new [Database_Query_Expression] which is not escaped. An expression
	 * is the only way to use SQL functions within query builders.
	 *
	 *     $expression = DB::expr('COUNT(users.id)');
	 *     $query = DB::update('users')->set(array('login_count' => DB::expr('login_count + 1')))->where('id', '=', $id);
	 *     $users = ORM::factory('user')->where(DB::expr("BINARY `hash`"), '=', $hash)->find();
	 *
	 * @param   string  $string  expression
	 * @param   array   parameters
	 * @return  Database_Query_Expression
	 */
	public static function expr($string, $parameters = array())
	{
		//echo $string;exit;
		return new Database_Query_Expression($string, $parameters);
	}

} // End DB
