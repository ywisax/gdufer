<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 帖子记录模型
 *
 * 考虑到举证和数据恢复等需求，添加一个模型来保存历史帖子内容是很有必要的了。
 * 用户提交帖子时，新增一个记录日志
 * 用户编辑并提交帖子时，再插入一个记录日志
 *
 * 考虑到以后功能的扩展、应付某方面的审查和可能会出现的纠纷，这个新功能是十分有必要的。
 *
 * @package		Kohana/Forum
 * @category	Model
 */
class Kohana_Model_Forum_Topic_Log extends Model_Forum {

	protected $_table_name = 'forum_topic_log';

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);

	// 属于
	protected $_belongs_to = array(
		'topic' => array(
			'model' => 'Forum_Topic',
			'foreign_key' => 'topic_id',
		),
		'poster' => array(
			'model' => 'User',
			'foreign_key' => 'poster_id',
		),
		'operator' => array(
			'model' => 'User',
			'foreign_key' => 'operator_id',
		),
	);
	
	// 因为这个记录不能给人认为操作，所以，rules就暂时不写了

	/**
	 * 过滤规则
	 */
	public function filters()
	{
		return array(
			'topic_id' => array(
				array('intval'), // 强制转换为数值
			),
			'poster_id' => array(
				array('intval'), // 强制转换为数值
			),
			'poster_name' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'), // 去除危险标签
			),
			'title' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'), // 去除危险标签
			),
			'content' => array(
				array('trim'), // 去除空余空格
				//array('strip_tags'), // 帖子内容应该对html进行白名单验证才对
			),
			'ip' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'), // IP也可能成为攻击源
			),
		);
	}
}
