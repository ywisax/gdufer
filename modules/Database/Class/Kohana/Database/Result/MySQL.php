<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * MySQL查询结果处理
 *
 * @package    Kohana/Database
 * @category   Query/Result
 */
class Kohana_Database_Result_MySQL extends Database_Result {

	protected $_internal_row = 0;

	public function __construct($result, $sql, $as_object = FALSE, array $params = NULL)
	{
		parent::__construct($result, $sql, $as_object, $params);

		// 查找结果的列数
		$this->_total_rows = mysql_num_rows($result);
	}

	public function __destruct()
	{
		if (is_resource($this->_result))
		{
			mysql_free_result($this->_result);
		}
	}

	public function seek($offset)
	{
		if ($this->offsetExists($offset) AND mysql_data_seek($this->_result, $offset))
		{
			// 设置当前列的偏移位置
			$this->_current_row = $this->_internal_row = $offset;
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function current()
	{
		if ($this->_current_row !== $this->_internal_row AND ! $this->seek($this->_current_row))
		{
			return NULL;
		}

		// Increment internal row for optimization assuming rows are fetched in order
		$this->_internal_row++;

		if ($this->_as_object === TRUE)
		{
			// 返回一个stdClass
			return mysql_fetch_object($this->_result);
		}
		elseif (is_string($this->_as_object))
		{
			// 返回指定类的对象
			return mysql_fetch_object($this->_result, $this->_as_object, $this->_object_params);
		}
		else
		{
			// 返回数组
			return mysql_fetch_assoc($this->_result);
		}
	}

} // End Database_Result_MySQL
