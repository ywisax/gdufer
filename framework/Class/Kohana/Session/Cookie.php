<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 基于Cookie实现的会话系统，CI默认就是这个啦
 *
 * @package    Kohana
 * @category   Session
 */
class Kohana_Session_Cookie extends Session {

	/**
	 * @param   string  $id  会话ID
	 * @return  string
	 */
	protected function _read($id = NULL)
	{
		return Helper_Cookie::get($this->_name, NULL);
	}

	/**
	 * @return  null
	 */
	protected function _regenerate()
	{
		// Cookie中没有会话ID这个东西
		return NULL;
	}

	/**
	 * @return  bool
	 */
	protected function _write()
	{
		return Helper_Cookie::set($this->_name, $this->__toString(), $this->_lifetime);
	}

	/**
	 * @return  bool
	 */
	protected function _restart()
	{
		return TRUE;
	}

	/**
	 * @return  bool
	 */
	protected function _destroy()
	{
		return Helper_Cookie::delete($this->_name);
	}

} // End Session_Cookie
