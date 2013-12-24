<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 微博帖子模型
 *
 * @package		Kohana/Weibo
 * @category	Model
 * @author		YwiSax
 */
class Kohana_Model_Weibo_Feed extends Model_Weibo {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

	// 自动序列化字段
	protected $_serialize_columns = array('geo', 'user', 'retweeted_status', 'visible', 'pic_urls', 'ad');

	protected $_has_one = array(
		// 带有一个图片
		'img' => array(
			'model'=> 'Weibo_Image',
			'foreign_key' => 'id',
		),
	);
	
	// 属于
	protected $_belongs_to = array(
		'poster' => array(
			'model' => 'Weibo_User',
			'foreign_key' => 'poster_id',
		),
	);
	
		/**
	 * 过滤规则
	 */
	public function filters()
	{
		return array(
			'screen_name' => array(
				array('trim'), // 过滤空格
				array('strip_tags'), // 去除危险字符
			),
			'text' => array(
				array('trim'), // 过滤空格
				array('strip_tags'), // 去除危险字符
			),
		);
	}
}
