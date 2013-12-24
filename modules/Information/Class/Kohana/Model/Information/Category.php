<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 分类信息模型分类
 *
 * @package    Kohana/Information
 * @category   Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Model_Information_Category extends Model_Information {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

}
