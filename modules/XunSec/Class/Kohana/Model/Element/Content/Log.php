<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Content存档记录模型
 *
 * @package		XunSec
 * @category	Model
 * @copyright	YwiSax
 */
class Kohana_Model_Element_Content_Log extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);

}
