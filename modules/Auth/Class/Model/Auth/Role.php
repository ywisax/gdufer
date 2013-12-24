<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * Default auth role
 *
 * @package    Kohana/Auth
 */
class Model_Auth_Role extends ORM {

	// 关系
	protected $_has_many = array(
		'users' => array(
			'model' => 'User',
			'through' => 'role_user'
		),
	);

	/**
	 * 校验规则
	 */
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'),
				array('min_length', array(':value', 4)),
				array('max_length', array(':value', 32)),
			),
			'description' => array(
				array('max_length', array(':value', 255)),
			)
		);
	}

} // End Auth Role Model
