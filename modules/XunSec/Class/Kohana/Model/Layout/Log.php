<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Layout的存档，随时恢复到原来的备份，多爽啊
 *
 * @package		XunSec
 * @category	Model
 * @copyright	YwiSax
 */
class Kohana_Model_Layout_Log extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);

	// 属于
	protected $_belongs_to = array(
		'layout' => array(
			'model' => 'Layout',
			'foreign_key' => 'layout_id',
		),
		'poster' => array(
			'model' => 'User',
			'foreign_key' => 'poster_id',
		),
	);

}
