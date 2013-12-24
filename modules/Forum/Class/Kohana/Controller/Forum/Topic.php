<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 论坛帖子控制器
 *
 * @package		Kohana/Forum
 * @category	Controller
 */
class Kohana_Controller_Forum_Topic extends Controller_Forum {

	/**
	 * 查看帖子
	 */
	public function action_view()
	{
		// 任何人都可以阅读帖子
		$topic_id = $this->request->param('id');
		
		// 先查找帖子
		$topic = Model::factory('Forum.Topic')
			->where('id', '=', $topic_id)
			->find();
		if ( ! $topic->loaded())
		{
			return $this->response->body( $this->error() );
		}
		
		$page = $this->request->param('page');
		if ($page < 1)
		{
			$page = 1;
		}
		$limit = 12;
		$offset = ($page - 1) * $limit;
		
		$pagination_config = Kohana::config('Pagination.forum');
		$pagination_config['total_items'] = $topic->replies
			->find_all()
			->count();
		$pagination_config['items_per_page'] = $limit;

		// 再查找帖子的回复
		$replies = $topic->replies
			->order_by('id', 'ASC')
			->limit($limit)
			->offset($offset)
			->find_all();
		
		$content = View::factory('Forum.Topic.View', array(
			'topic' => $topic,
			'replies' => $replies,
			'pagination' => Pagination::factory($pagination_config),
		));
		
		$topic->update_click_count();
		
		$this->render(array(
			'title' => __(':page in :topic', array(':page' => $page, ':topic' => $topic->title)),
			'metadesc' => Kohana::config('Forum.metadesc'),
			'metakw' => Kohana::config('Forum.metakw'),
			'content' => $content,
		));
	}

	/**
	 * 发表新帖子
	 */
	public function action_new()
	{
		// 登陆才能发表啊
		if ( ! Auth::instance()->logged_in())
		{
			HTTP::redirect(Route::url('forum-list'));
		}
	
		$group_id = $this->request->param('id');
		$group = Model::factory('Forum.Group')
			->where('id', '=', $group_id)
			->find();
		if ( ! $group->loaded())
		{
			HTTP::redirect(Route::url('forum-list'));
		}

		$errors = array();
		$topic = Model::factory('Forum.Topic');
		if ($this->request->is_post())
		{
			try
			{
				$this->request->post('poster_id', Auth::instance()->get_user()->id);
				$this->request->post('poster_name', Auth::instance()->get_user()->username);
				$this->request->post('group_id', $group->id);
				$this->request->post('stick', 0);
				$this->request->post('visible', 1);
				// 保存帖子
				$topic
					->values($this->request->post())
					->save();
				
				HTTP::redirect( Route::url('forum-topic', array('id' => $topic->id)) );
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors();
			}
		}

		$this->render(array(
			'title' => __('Post new topic in :group', array(
				':group' => $group->name,
			)),
			'metadesc' => Kohana::config('Forum.metadesc'),
			'metakw' => Kohana::config('Forum.metakw'),
			'content' => View::factory('Forum.Topic.Edit', array(
				'title' => __('Post new topic in :group', array(
					':group' => $group->title_link(),
				)),
				'group' => $group,
				'topic' => $topic,
				'errors' => $errors,
			)),
		));
	}
	
	/**
	 * 编辑帖子
	 */
	public function action_edit()
	{
		$topic_id = $this->request->param('id');
		$topic = Model::factory('Forum.Topic')
			->where('id', '=', $topic_id)
			->find();
		if ( ! $topic->loaded())
		{
			HTTP::redirect(Route::url('forum-list'));
		}

		// 登陆才能发表啊
		if ( ! Auth::instance()->get_user()->has_role('admin') OR (Auth::instance()->get_user()->id != $topic->poster->id))
		{
			HTTP::redirect(Route::url('forum-list'));
		}
		
		$title = __('Editing :topic', array(
			':topic' => $topic->title_link(),
		));
		$errors = array();
		if ($this->request->is_post())
		{
			try
			{
				// 这里还要对字段进行一些限制
				// 保存帖子
				$topic
					->values($this->request->post())
					->save();
				
				HTTP::redirect( Route::url('forum-topic', array('id' => $topic->id)) );
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors();
			}
		}

		$this->render(array(
			'title' => __('Editing :topic', array(
				':topic' => $topic->title,
			)),
			'metadesc' => Kohana::config('Forum.metadesc'),
			'metakw' => Kohana::config('Forum.metakw'),
			'content' => View::factory('Forum.Topic.Edit', array(
				'title' => __('Editing :topic', array(
					':topic' => $topic->title_link(),
				)),
				'topic' => $topic,
				'errors' => $errors,
			)),
		));
	}
	
	/**
	 * 删除帖子
	 */
	public function action_delete()
	{
		// 判断权限
		if ( ! Auth::instance()->logged_in())
		{
			exit('Access denied.');
		}
		if ( ! Auth::instance()->get_user()->has_role('admin'))
		{
			exit('Permission denied.');
		}
	
		$id = (int) $this->request->param('id');
		$topic = Model::factory('Forum.Topic')
			->where('id', '=', $id)
			->find();
		if ($topic->loaded())
		{
			$topic->delete();
		}
		HTTP::redirect( $this->request->referrer() );
	}
	
	/**
	 * 置顶帖子
	 */
	public function action_sticky()
	{
		// 判断权限
		if ( ! Auth::instance()->logged_in())
		{
			exit('Access denied.');
		}
		if ( ! Auth::instance()->get_user()->has_role('admin'))
		{
			exit('Permission denied.');
		}
	
		$id = (int) $this->request->param('id');
		$topic = Model::factory('Forum.Topic')
			->where('id', '=', $id)
			->find();
		if ($topic->loaded())
		{
			$topic->sticky = intval( ! $topic->sticky);
			$topic->save();
		}

		HTTP::redirect( $this->request->referrer() );
	}
}
