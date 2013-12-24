<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 基于数据库实现的会话系统
 *
 * 数据库表结构:
 *
 *     CREATE TABLE  `sessions` (
 *         `session_id` VARCHAR( 24 ) NOT NULL,
 *         `last_active` INT UNSIGNED NOT NULL,
 *         `contents` TEXT NOT NULL,
 *         PRIMARY KEY ( `session_id` ),
 *         INDEX ( `last_active` )
 *     ) ENGINE = MYISAM ;
 *
 * @package    Kohana/Database
 * @category   Session
 */
class Kohana_Session_Database extends Session {

	// 数据库实例
	protected $_db;

	// 表名
	protected $_table = 'sessions';

	// 数据库列名
	protected $_columns = array(
		'session_id'  => 'session_id',
		'last_active' => 'last_active',
		'contents'    => 'contents'
	);

	// Garbage collection requests
	protected $_gc = 500;

	// 当前会话ID
	protected $_session_id;

	// 旧会话ID
	protected $_update_id;

	public function __construct(array $config = NULL, $id = NULL)
	{
		if ( ! isset($config['group']))
		{
			$config['group'] = Database::$default;
		}

		// 加载数据库
		$this->_db = Database::instance($config['group']);

		if (isset($config['table']))
		{
			$this->_table = (string) $config['table'];
		}

		if (isset($config['gc']))
		{
			// Set the gc chance
			$this->_gc = (int) $config['gc'];
		}

		if (isset($config['columns']))
		{
			$this->_columns = $config['columns'];
		}

		parent::__construct($config, $id);

		if (mt_rand(0, $this->_gc) === $this->_gc)
		{
			// Run garbage collection
			// This will average out to run once every X requests
			$this->_gc();
		}
	}

	public function id()
	{
		return $this->_session_id;
	}

	protected function _read($id = NULL)
	{
		if ($id OR $id = Helper_Cookie::get($this->_name))
		{
			$result = DB::select(array($this->_columns['contents'], 'contents'))
				->from($this->_table)
				->where($this->_columns['session_id'], '=', ':id')
				->limit(1)
				->param(':id', $id)
				->execute($this->_db);

			if ($result->count())
			{
				// 当前会话ID
				$this->_session_id = $this->_update_id = $id;
				// 返回内容
				return $result->get('contents');
			}
		}

		// 生成一个新的会话ID
		$this->_regenerate();

		return NULL;
	}

	protected function _regenerate()
	{
		$query = DB::select($this->_columns['session_id'])
			->from($this->_table)
			->where($this->_columns['session_id'], '=', ':id')
			->limit(1)
			->bind(':id', $id);

		do
		{
			// 创建一个新ID
			$id = str_replace('.', '-', uniqid(NULL, TRUE));
			$result = $query->execute($this->_db);
		}
		while ($result->count());

		return $this->_session_id = $id;
	}

	protected function _write()
	{
		if ($this->_update_id === NULL)
		{
			// 插入新纪录
			$query = DB::insert($this->_table, $this->_columns)
				->values(array(':new_id', ':active', ':contents'));
		}
		else
		{
			// 更新记录
			$query = DB::update($this->_table)
				->value($this->_columns['last_active'], ':active')
				->value($this->_columns['contents'], ':contents')
				->where($this->_columns['session_id'], '=', ':old_id');

			if ($this->_update_id !== $this->_session_id)
			{
				// 同时更新会话ID
				$query->value($this->_columns['session_id'], ':new_id');
			}
		}

		$query
			->param(':new_id',   $this->_session_id)
			->param(':old_id',   $this->_update_id)
			->param(':active',   $this->_data['last_active'])
			->param(':contents', $this->__toString());

		$query->execute($this->_db);
		$this->_update_id = $this->_session_id;
		// 更新Cookie
		Helper_Cookie::set($this->_name, $this->_session_id, $this->_lifetime);

		return TRUE;
	}

	/**
	 * @return  bool
	 */
	protected function _restart()
	{
		$this->_regenerate();
		return TRUE;
	}

	protected function _destroy()
	{
		if ($this->_update_id === NULL)
		{
			return TRUE;
		}

		// 删除当前会话
		$query = DB::delete($this->_table)
			->where($this->_columns['session_id'], '=', ':id')
			->param(':id', $this->_update_id);

		try
		{
			$query->execute($this->_db);
			// 删除Cookie
			Helper_Cookie::delete($this->_name);
		}
		catch (Exception $e)
		{
			// An error occurred, the session has not been deleted
			return FALSE;
		}

		return TRUE;
	}

	protected function _gc()
	{
		if ($this->_lifetime)
		{
			// Expire sessions when their lifetime is up
			$expires = $this->_lifetime;
		}
		else
		{
			// Expire sessions after one month
			$expires = Helper_Date::MONTH;
		}

		// Delete all sessions that have expired
		DB::delete($this->_table)
			->where($this->_columns['last_active'], '<', ':time')
			->param(':time', time() - $expires)
			->execute($this->_db);
	}

} // End Session_Database
