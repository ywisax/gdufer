<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 数据库模型
 *
 * @package    Kohana/Database
 * @category   Models
 */
abstract class Kohana_Model_Database extends Model {

	const MODEL_PREFIX = 'Model_';

	/**
	 * Create a new model instance. A [Database] instance or configuration
	 * group name can be passed to the model. If no database is defined, the
	 * "default" database group will be used.
	 *
	 *     $model = Model::factory($name);
	 *
	 * @param   string   $name  model name
	 * @param   mixed    $db    Database instance object or string
	 * @return  Model
	 */
	public static function factory($name, $db = NULL)
	{
		$class = Model_Database::MODEL_PREFIX . $name;
		return new $class($db);
	}

	// 数据库实例
	protected $_db;

	/**
	 * 加载数据库
	 *
	 *     $model = new Foo_Model($db);
	 *
	 * @param   mixed  $db  Database instance object or string
	 * @return  void
	 */
	public function __construct($db = NULL)
	{
		if ($db === NULL)
		{
			$this->_db = Database::$default;
		}
		elseif ($this->_db)
		{
			$this->_db = $db;
		}

		if (is_string($this->_db))
		{
			// Load the database
			$this->_db = Database::instance($this->_db);
		}
	}

} // End Model
