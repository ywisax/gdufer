<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 保存附件下载记录
 *
 * USER_ID、UA和IP这些不要自己添加，留待模型自行完成（防止篡改）
 *
 * @package    Kohana/Attachment
 * @category   Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Model_Attachment_Log extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);

	/**
	 * 添加对应附件的下载记录
	 */
	public function down($attachment)
	{
		$this->attachment_id = $attachment->id;
		$this->file = $attachment->file;
		$this->name = $attachment->name;

		return $this;
	}

	/**
	 * 创建记录时，自动补充相关信息
	 */
	public function create(Validation $validation = NULL)
	{
		$this->user_id = Auth::instance()->logged_in()
			? Auth::instance()->get_user()->id
			: 0;
		$this->ip = Request::$client_ip;
		$this->ua = Request::$user_agent;

		$result = parent::create($validation);
		return $result;
	}
	
}
