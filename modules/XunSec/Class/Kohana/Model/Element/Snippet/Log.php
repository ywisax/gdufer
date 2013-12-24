<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * Snippet存档记录模型
 *
 * @package		XunSec
 * @category	Model
 * @copyright	YwiSax
 */
class Kohana_Model_Element_Snippet_Log extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);

}
