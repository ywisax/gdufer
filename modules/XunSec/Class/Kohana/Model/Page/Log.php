<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Page的存档记录
 *
 * @package		XunSec
 * @category	Model
 * @copyright	YwiSax
 */
class Kohana_Model_Page_Log extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);

	// 属于
	protected $_belongs_to = array(
		'page' => array(
			'model' => 'Page',
			'foreign_key' => 'page_id',
		),
		'poster' => array(
			'model' => 'User',
			'foreign_key' => 'poster_id',
		),
	);
}
