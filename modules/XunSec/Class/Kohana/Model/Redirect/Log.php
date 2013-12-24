<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 跳转模型的Log备份表
 *
 * 表结构：
 *
 * CREATE TABLE `xunsec_redirect_log` (
 *   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 *   `redirect_id` int(10) NOT NULL,
 *   `url` varchar(255) NOT NULL,
 *   `newurl` varchar(255) NOT NULL,
 *   `type` enum('301','302') NOT NULL DEFAULT '302',
 *   `poster_id` int(10) NOT NULL,
 *   `poster_name` varchar(100) NOT NULL,
 *   `date_created` int(10) DEFAULT NULL,
 *   PRIMARY KEY (`id`),
 *   KEY `poster_id` (`poster_id`),
 *   KEY `redirect_id` (`redirect_id`),
 *   KEY `url` (`url`)
 * ) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
 *
 *
 * @package		XunSec
 * @category	Model
 * @copyright	YwiSax
 */
class Kohana_Model_Redirect_Log extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);

	protected $_belongs_to = array(
		'redirect' => array(
			'model' => 'Redirect',
			'foreign_key' => 'redirect_id',
		),
		'poster' => array(
			'model' => 'User',
			'foreign_key' => 'poster_id',
		),
	);
}
