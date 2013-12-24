<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 广金首页的用户
 *
 * @package		Kohana/Gduf
 * @category	Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Model_Gduf_User extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);

}

