<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 基于文件的权限驱动
 * [!!] 文件驱动是不支持角色和自动登录检测功能的。
 *
 * @package    Kohana/Auth
 * @category   Driver
 */
class Kohana_Auth_File extends Auth {

	// 用户列表
	protected $_users;

	/**
	 * 构造器，自动加载所有用户
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		// 加载用户列表
		$this->_users = Helper_Array::get($config, 'users', array());
	}

	/**
	 * 用户登录
	 *
	 * @param   string   $username  用户名
	 * @param   string   $password  密码
	 * @param   boolean  $remember  是否自动登录（当前驱动不支持）
	 * @return  boolean
	 */
	protected function _login($username, $password, $remember)
	{
		if (is_string($password))
		{
			// 加密密码
			$password = $this->hash($password);
		}

		if (isset($this->_users[$username]) AND $this->_users[$username] === $password)
		{
			// 完成登录
			return $this->complete_login($username);
		}

		// 登录失败，返回FALSE
		return FALSE;
	}

	/**
	 * 强制登录某一用户，不检测密码.
	 *
	 * @param   mixed    $username  用户名
	 * @return  boolean
	 */
	public function force_login($username)
	{
		// Complete the login
		return $this->complete_login($username);
	}

	/**
	 * 获取指定用户名的密码
	 *
	 * @param   mixed   $username  用户名
	 * @return  string
	 */
	public function password($username)
	{
		return Helper_Array::get($this->_users, $username, FALSE);
	}

	/**
	 * 检测当前登录用户的密码是否正确。
	 *
	 * @param   string   $password  明文密码
	 * @return  boolean
	 */
	public function check_password($password)
	{
		$username = $this->get_user();

		if ($username === FALSE)
		{
			return FALSE;
		}
		return ($password === $this->password($username));
	}

} // End Auth File

