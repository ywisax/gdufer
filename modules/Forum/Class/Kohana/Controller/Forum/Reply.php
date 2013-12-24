<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 论坛回复控制器
 *
 * @package		Kohana/Forum
 * @category	Controller
 */
class Kohana_Controller_Forum_Reply extends Controller_Forum {

	/**
	 * 回复帖子
	 */
	public function action_new()
	{
		$content = $this->request->post('content');
		// 不能为空
		if ( ! $content)
		{
			return;
		}

		// 因为图片中可能带有data-url，所以解析多一次
		$content = Attachment::convert_local($content);

		$insert_log = FALSE;
		$errors = FALSE;
		
		$user_id = $this->request->post('user_id');
		// 如果当前用户没有管理员权限，那就不能随便指定个人来发帖啊
		if ( ! Auth::instance()->get_user()->has_role('admin'))
		{
			$insert_log = TRUE;
			$user_id = Auth::instance()->get_user()->id;
		}
		
		// 查找帖子
		$topic = Model::factory('Forum.Topic')
			->where('id', '=', $this->request->post('topic_id'))
			->find();
		// 这里应该要验证下帖子是否允许发帖
		if ($topic->loaded())
		{
			try
			{
				$reply = Model::factory('Forum.Reply')
					->values(array(
						'topic_id' => $topic->id,
						'poster_id' => $user_id,
						'content' => $content,
					))
					->save();
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors();
			}

			// 跳转回帖子吧、
			HTTP::redirect( Route::url('forum-topic', array('id' => $topic->id)) );
		}
		else
		{
			HTTP::redirect( $this->request->referrer() );
		}
	}
	
	/**
	 * 删除回复
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
		
		$id = $this->request->param('id');
		$reply = Model::factory('Forum.Reply')
			->where('id', '=', $id)
			->find();
		if ($reply->loaded())
		{
			$reply->delete();
		}

		HTTP::redirect( $this->request->referrer() );
	}

}
