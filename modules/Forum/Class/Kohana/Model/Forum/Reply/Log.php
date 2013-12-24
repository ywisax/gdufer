<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 回复记录模型
 *
 * 回复是可以编辑的，保存历史回复，一是可以保证数据源多处备份，同时可以用于处理各种纠纷。
 *
 * @package		Kohana/Forum
 * @category	Model
 */
class Kohana_Model_Forum_Reply_Log extends Model_Forum {

	protected $_table_name = 'forum_reply_log';

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	// 因为只记录操作步骤，所以应该不用记录更新时间（事实上也没有更新时间）
	//protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);
	
	// 属于
	protected $_belongs_to = array(
		'reply' => array(
			'model' => 'Forum_Reply',
			'foreign_key' => 'reply_id',
		),
		'poster' => array(
			'model' => 'User',
			'foreign_key' => 'poster_id',
		),
	);
	
	// 因为这个记录不能给人认为操作，所以，rules就暂时不写了

	/**
	 * 过滤规则
	 */
	public function filters()
	{
		return array(
			'reply_id' => array(
				array('intval'), // 强制转换为数值
			),
			'poster_id' => array(
				array('intval'), // 强制转换为数值
			),
			'poster_name' => array(
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
