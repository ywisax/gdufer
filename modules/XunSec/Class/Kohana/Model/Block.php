<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * XunSec Block Model
 *
 * 表结构：
 *
 * CREATE TABLE `xunsec_block` (
 *   `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'BLOCK ID',
 *   `page_id` int(11) NOT NULL COMMENT '页面ID',
 *   `area` int(11) NOT NULL,
 *   `order` int(11) NOT NULL,
 *   `elementtype` int(11) NOT NULL,
 *   `element` int(11) NOT NULL COMMENT '元素ID',
 *   `date_created` int(10) NOT NULL COMMENT '添加日期',
 *   `date_updated` int(10) NOT NULL COMMENT '更新日期',
 *   PRIMARY KEY (`id`),
 *   KEY `page_id` (`page_id`)
 * ) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COMMENT='CMS元素关系表';
 *
 * @package		XunSec
 * @category	Model
 * @author		YwiSax
 * @copyright	(c) 2009 XunSec Team
 * @license		http://www.xunsec.com/license
 */
class Kohana_Model_Block extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

	protected $_belongs_to = array(
		'type' => array(
			'model'=> 'Element_Type',
			'foreign_key' => 'elementtype',
		),
		'page' => array(
			'model' => 'Page',
			'column' => 'page_id',
		),
	);

	public function add_one($page, $area, $elementtype, $element)
	{
		if ($this->loaded())
		{
			throw XunSec_Exception('Cannot add a block that already exists');
		}

		$elementtype = Model::factory('Element.Type')
			->where('name', '=', $elementtype)
			->find();
		if ( ! $elementtype->loaded())
		{
			throw XunSec_Exception('Could not find elementtype ' . $elementtype);
		}

		// 查找级别最高的那个
		$block = Model::factory('Block')
			->where('page_id', '=', intval($page))
			->where('area', '=', intval($area))
			->order_by('order', 'DESC')
			->find();
		$order = ($block->order) + 1;

		// 新建
		$this->values(array(
			'page_id'        => $page,
			'area'        => $area,
			'order'       => $order,
			'elementtype' => $elementtype->id,
			'element'     => $element,
		))->create();
	}
}
