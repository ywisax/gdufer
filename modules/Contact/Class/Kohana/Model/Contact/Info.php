<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Contact信息模型
 *
 * @package    Kohana/Contact
 * @category   Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/
 */
class Kohana_Model_Contact_Info extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);
	
	/**
	 * 校验规则
	 */
	public function rules()
	{
		return array(
			'realname' => array(
				array('not_empty'),
			),
			'mobile' => array(
				array('not_empty'),
			),
			'content' => array(
				array('not_empty'),
			),
		);
	}
	
	/**
	 * 过滤规则
	 */
	public function filters()
	{
		return array(
			'realname' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'),
			),
			'mobile' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'),
			),
			'content' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'),
			),
		);
	}
}
