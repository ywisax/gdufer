<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * PHP的原生会话类.
 *
 * @package    Kohana
 * @category   Session
 */
class Kohana_Session_Native extends Session {

	/**
	 * @return  string
	 */
	public function id()
	{
		return session_id();
	}

	/**
	 * @param   string  $id  会话ID
	 * @return  null
	 */
	protected function _read($id = NULL)
	{
		// 保证Cookie和Session的参数一致
		session_set_cookie_params(
			$this->_lifetime,
			Helper_Cookie::$path,
			Helper_Cookie::$domain,
			Helper_Cookie::$secure,
			Helper_Cookie::$httponly
		);

		// Do not allow PHP to send Cache-Control headers
		session_cache_limiter(FALSE);

		// 设置会话Cookie名称
		session_name($this->_name);

		if ($id)
		{
			session_id($id);
		}

		// 修复出错？
		if ( ! IN_SAE)
		{
			//session_save_path(Kohana::$cache_dir);
		}
		// 开始会话记录
		session_start();

		// Use the $_SESSION global for storing data
		$this->_data =& $_SESSION;

		return NULL;
	}

	/**
	 * @return  string
	 */
	protected function _regenerate()
	{
		// 重新生成一个会话ID
		session_regenerate_id();
		return session_id();
	}

	/**
	 * @return  bool
	 */
	protected function _write()
	{
		session_write_close();
		return TRUE;
	}

	/**
	 * @return  bool
	 */
	protected function _restart()
	{
		// 开始一个新会话
		$status = session_start();

		// 引用$_SESSION这个变量
		$this->_data =& $_SESSION;

		return $status;
	}

	/**
	 * @return  bool
	 */
	protected function _destroy()
	{
		// Destroy the current session
		session_destroy();

		// Did destruction work?
		$status = ! session_id();

		if ($status)
		{
			// Make sure the session cannot be restarted
			Helper_Cookie::delete($this->_name);
		}

		return $status;
	}

} // End Session_Native
