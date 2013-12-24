<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 应用授权用户信息
 *
 * @package		Kohana/Weibo
 * @category	Model
 * @author		YwiSax
 */
class Kohana_Model_Weibo_User extends Model_Weibo {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

	protected $_table_name = 'weibo_user';
	
	// 自动序列化status这个字段
	protected $_serialize_columns = array('status');
	
	protected $_primary_key = 'uid';

	/**
	 * 检查用户是否禁用什么的
	 */
	public function check_ban_expired()
	{
		// 先看是否已经被设置了禁用时间
		if ($this->ban_expired)
		{
			// 如果到期时间已经过了
			if ($this->ban_expired < time())
			{
				// 自动解禁
				// 那就重置下过期时间吧
				$this->ban_expired = 0;
				$this->save();
			}
		}
	}
}
