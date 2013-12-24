<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 论坛群组模型
 *
 * @package		Kohana/Forum
 * @category	Model
 */
class Kohana_Model_Forum_Group extends Model_Forum {

	protected $_table_name = 'forum_group';

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

	protected $_belongs_to = array(
		// 每个论坛都可能有他的上级
		'parent'	=> array(
			'model' 		=> 'Forum_Group',
			'foreign_key'	=> 'parent_id',
		),
	);

	protected $_has_many = array(
		// 论坛有上下级分的
		'children'	=> array(
			'model' 		=> 'Forum_Group',
			'foreign_key'	=> 'parent_id',
		),
		// 每个论坛都有多个帖子
		'topics' 	=>	array(
			'model' 		=> 'Forum_Topic'
		),
	);
	
	/**
	 * 校验规则
	 */
	public function rules()
	{
		return array(
			'name' => array(
				array('not_empty'), // 论坛群组名称会必填的
			),
			'description' => array(
				array('not_empty'), // 论坛描述不能为空
			),
		);
	}
	
	/**
	 * 过滤规则
	 */
	public function filters()
	{
		return array(
			'parent_id' => array(
				array('intval'), // 强制转换为数值
			),
			'name' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'), // 去除危险标签
			),
			'uri' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'), // 去除危险标签
			),
			'description' => array(
				array('trim'), // 去除空余空格
				//array('strip_tags'), // 为了功能展示更好，这里是允许使用HTML的
			),
		);
	}
	
	/**
	 * 返回当前群组对应的URL
	 */
	public function link()
	{
		return Route::url('forum-group', array('group' => $this->id));
	}
	
	/**
	 * 返回当前群组对应的URL
	 */
	public function title_link()
	{
		return HTML::anchor(
			$this->link(),
			$this->name
		);
	}
	
	/**
	 * 发表新帖子的URL
	 */
	public function new_post_link()
	{
		return Route::url('forum-topic-action', array('action' => 'new', 'id' => $this->id));
	}
}
