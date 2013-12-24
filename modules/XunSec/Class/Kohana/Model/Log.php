<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 系统记录模型
 *
 * @package		XunSec
 * @category	Model
 * @author		YwiSax
 * @copyright	(c) 2009 XunSec Team
 * @license		http://www.xunsec.com/license
 */
class Kohana_Model_Log extends ORM {

	// 日志生成日期
	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);

	protected $_belongs_to = array(
		'operator' => array(
			'model'			=> 'User',
			'foreign_key'	=> 'operator_id',
		),
	);
	
	/**
	 * 校验规则
	 */
	public function rules()
	{
		return array(
			'type' => array(
				array('not_empty'), // 日志类型是必选的（字符串）
			),
			'content' => array(
				array('not_empty'), // 日志内容不能为空
			),
		);
	}

	/**
	 * 过滤规则
	 */
	public function filters()
	{
		return array(
			'content' => array(
				array('trim'), // 过滤空格
				array('strip_tags'), // 去除危险字符
			),
		);
	}
	
	/**
	 * 为了保证多方备份，在新增记录到Log的时候，最好还同步一份Log到其他地方。
	 */
	public function create(Validation $validation = NULL)
	{
		// 保存IP
		$this->ip = Request::$client_ip;

		// 同步log到其他地方？push？
		return parent::create($validation);
	}
}
