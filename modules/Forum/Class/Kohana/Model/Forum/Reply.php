<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 论坛回复模型
 *
 * @package		Kohana/Forum
 * @category	Model
 */
class Kohana_Model_Forum_Reply extends Model_Forum {

	protected $_table_name = 'forum_reply';

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

	// 模型关系映射
	protected $_belongs_to = array(
		// 当前回复属于哪个帖子
		'topic' => array(
			'model' => 'Forum_Topic',
			'foreign_key' => 'topic_id',
		),
		// 当前回复属于哪个发布者
		'poster'	=> array(
			'model' 		=> 'User',
			'foreign_key'	=> 'poster_id',
		),
		// 当前回复对应的回复
		'reply'	=> array(
			'model' 		=> 'Forum_Reply',
			'foreign_key'	=> 'reply_id',
		),
	);
	
	/**
	 * 校验规则
	 */
	public function rules()
	{
		return array(
			'topic_id' => array(
				array('not_empty'), // 必须要跟一个topic对应
			),
			'poster_id' => array(
				array('not_empty'), // 必须要跟一个用户对应
			),
			'content' => array(
				array('not_empty'), // 回复内容不能为空
			),
		);
	}
	
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
			'content' => array(
				array('trim'), // 去除空余空格
				//array('strip_tags'), // 回复内容应该进行白名单验证才对
			),
		);
	}

	public function log()
	{
		$log = Model::factory('Forum.Reply.Log');
		$log->reply_id		= $this->id;
		$log->topic_id		= $this->topic_id;
		$log->poster_id		= $this->poster_id;
		$log->poster_name	= $this->poster_name;
		$log->content		= $this->content;
		$log->ip			= $this->ip;
		$log->operator_id	= Auth::instance()->get_user()->id;
		$log->operator_name	= Auth::instance()->get_user()->username;
		$log->save();
	}
	
	/**
	 * 创建记录的同时，插入一份到Log中去
	 */
	public function create(Validation $validation = NULL)
	{
		$this->ip = Request::$client_ip;
	
		$result = parent::create($validation);
		if ($this->loaded())
		{
			$this->log();
			XunSec::log(
				Forum::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Add a reply, ID: :id', array(
					':id' => $this->id,
				))
			);
		}
		
		// 更新帖子的点击数什么的
		$this->topic->update_comment_count();

		return $result;
	}

	/**
	 * 修改记录的同时，把旧的数据保存到Log中去
	 */
	public function update(Validation $validation = NULL)
	{
		$this->ip = Request::$client_ip;
	
		if ($this->loaded())
		{
			$this->log();
			XunSec::log(
				Forum::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Update a reply, ID: :id', array(
					':id' => $this->id,
				))
			);
		}
		return parent::update($validation);
	}
	
	/**
	 * 删除前保存一份到Log中去
	 */
	public function delete()
	{
		if ($this->loaded())
		{
			$this->log();
			XunSec::log(
				Forum::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Delete a reply, ID: :id', array(
					':id' => $this->id,
				))
			);
		}
		
		$this->topic->update_comment_count();
		
		return parent::delete();
	}
}
