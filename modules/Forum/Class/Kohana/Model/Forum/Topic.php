<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 帖子模型
 *
 * 表结构：
 *
 * CREATE TABLE `xunsec_forum_topic` (
 *   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 *   `group_id` int(10) unsigned NOT NULL,
 *   `poster_id` int(10) unsigned NOT NULL,
 *   `poster_name` varchar(100) NOT NULL COMMENT '发帖者昵称',
 *   `title` varchar(100) NOT NULL,
 *   `content` longtext NOT NULL,
 *   `sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
 *   `visible` tinyint(1) unsigned NOT NULL DEFAULT '0',
 *   `comments` int(10) unsigned NOT NULL DEFAULT '0',
 *   `hits` int(10) unsigned NOT NULL DEFAULT '0',
 *   `ip` varchar(20) NOT NULL COMMENT '发帖者IP',
 *   `date_created` int(10) unsigned NOT NULL DEFAULT '0',
 *   `date_updated` int(10) unsigned NOT NULL DEFAULT '0',
 *   `date_touched` int(10) unsigned NOT NULL DEFAULT '0',
 *   PRIMARY KEY (`id`),
 *   KEY `title` (`title`)
 * ) ENGINE=MyISAM AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8;
 *
 * @package		Kohana/Forum
 * @category	Model
 * @author		YwiSax
 */
class Kohana_Model_Forum_Topic extends Model_Forum {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);


	// 默认每页16个回复
	const REPLY_ONE_PAGE = 16;
	// 第一页时应该少一个回复
	const REPLY_FIRST_PAGE = 15;
	
	protected $_has_many = array(
		'replies' => array(
			'model' => 'Forum_Reply',
			'foreign_key' => 'topic_id',
		),
	);
	
	// 属于
	protected $_belongs_to = array(
		'poster' => array(
			'model' => 'User',
			'foreign_key' => 'poster_id',
		),
		'group' => array(
			'model' => 'Forum_Group',
			'foreign_key' => 'group_id',
		),
	);

	/**
	 * 校验规则
	 */
	public function rules()
	{
		return array(
			'title' => array(
				array('not_empty'), // 帖子名称是必填的
			),
			'content' => array(
				array('not_empty'), // 帖子内容怎么可以为空
			),
		);
	}
	
	/**
	 * 过滤规则
	 */
	public function filters()
	{
		return array(
			'group_id' => array(
				array('intval'), // 强制转换为数值
			),
			'poster_id' => array(
				array('intval'), // 强制转换为数值
			),
			'title' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'), // 去除危险标签
			),
			'content' => array(
				array('trim'), // 去除空余空格
				//array('strip_tags'), // 帖子内容应该对html进行白名单验证才对
			),
		);
	}

	/**
	 * 获取指定的帖子回复
	 */
	public function get_replies($page = NULL)
	{
		$page = (int) $page;
	
		$limit = ($page == 1)
			? Model_Forum_Topic::REPLY_FIRST_PAGE
			: Model_Forum_Topic::REPLY_ONE_PAGE;
		$offset = ($page - 1) * $limit;
	}
	
	/**
	 * 返回当前帖子对应的URL
	 */
	public function link()
	{
		return Route::url('forum-topic', array('id' => $this->id));
	}
	
	/**
	 * 返回当前帖子对应的URL
	 */
	public function title_link()
	{
		return HTML::anchor(
			$this->link(),
			$this->title
		);
	}
	
	/**
	 * 更新点击数
	 */
	public function update_click_count($num = 1)
	{
		$this->hits = $this->hits + intval($num);
		$this->save();
	}
	
	/**
	 * 获取指定数目的最新帖子
	 */
	public function fetch_newest($num = 10)
	{
		$num = (int) $num;

		return $this
			->order_by('date_touched', 'DESC')
			->limit($num)
			->find_all();
	}

	/**
	 * 获取指定数目的随机帖子
	 */
	public function fetch_random($num = 10)
	{
		$num = (int) $num;

		return $this
			->order_by(DB::expr('RAND()'))
			->limit($num)
			->find_all();
	}
	
	/**
	 * 更新评论数目
	 */
	public function update_comment_count()
	{
		// 更新帖子的点击数什么的
		$this->comments = $this
			->replies
			->find_all()
			->count();
		$this->save();
	}
	
	public function log()
	{
		$log = Model::factory('Forum.Topic.Log');
		$log->topic_id		= $this->id;
		$log->group_id		= $this->group_id;
		$log->poster_id		= $this->poster_id;
		$log->poster_name	= $this->poster_name;
		$log->title			= $this->title;
		$log->content		= $this->content;
		$log->sticky		= $this->sticky;
		$log->visible		= $this->visible;
		$log->comments		= $this->comments;
		$log->hits			= $this->hits;
		$log->ip			= $this->ip;
		$log->date_touched	= $this->date_touched;
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
		$this->date_updated = $this->date_touched = time();
		$result = parent::create($validation);
		if ($this->loaded())
		{
			$this->log();
			// 保存到数据库
			XunSec::log(
				Forum::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Add a forum topic, ID: :id', array(
					':id' => $this->id,
				))
			);
		}
		return $result;
	}
	
	/**
	 * 修改记录的同时，把旧的数据保存到Log中去
	 */
	public function update(Validation $validation = NULL)
	{
		// 如果只更新hits的话，那就不管他
		if ( ! empty($this->_changed) AND count($this->_changed) == 1 AND isset($this->_changed['hits']))
		{
		}
		else
		{
			$this->ip = Request::$client_ip;
			$this->date_touched = time();
			$this->log();
			// 保存到数据库
			XunSec::log(
				Forum::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Update a forum topic, ID: :id', array(
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
		// 保存一份到Element.Snippet.Log中去
		if ($this->loaded())
		{
			$this->log();
			// 保存到数据库
			XunSec::log(
				Forum::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Delete a forum topic, ID: :id', array(
					':id' => $this->id,
				))
			);
			
			// 循环删除回复
			foreach ($this->replies->find_all() AS $reply)
			{
				$reply->delete();
			}
		}
		return parent::delete();
	}
}
