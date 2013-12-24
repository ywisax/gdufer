<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 元素类型模型
 *
 * 表结构：
 *
 * CREATE TABLE `xunsec_element_type` (
 *   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 *   `name` varchar(127) NOT NULL,
 *   PRIMARY KEY (`id`)
 * ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
 *
 * @package		XunSec
 * @category	Model
 * @copyright	YwiSax
 */
class Kohana_Model_Element_Type extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

}
