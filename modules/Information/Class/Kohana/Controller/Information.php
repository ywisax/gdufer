<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 分类信息控制器
 *
 * @package    Kohana/Information
 * @category   Controller
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Controller_Information extends Controller_XunSec {

	public $layout = 'blank';

	public function action_index()
	{
		$this->render(array(
			'title' => '广金书库',
			'metadesc' => Kohana::config('Information.metadesc'),
			'metakw' => Kohana::config('Information.metakw'),
			'content' => View::factory('Information.Index'),
		));
	}

	/**
	 * 分类信息列表
	 */
	public function action_list()
	{
		$type = $this->request->param('type');
		$page = (int) $this->request->param('id');

		$model = Model_Information::factory($type);
		$list = $model->action_list($page);
		
		$this->render(array(
			'title' => '第'.$this->request->param('id').'页',
			'metadesc' => Kohana::config('Information.metadesc'),
			'metakw' => Kohana::config('Information.metakw'),
			'content' => $list,
		));
	}

	/**
	 * 查看分类信息
	 */
	public function action_view()
	{
		$type = $this->request->param('type');
		$id = $this->request->param('id');
		$model = Model_Information::factory($type)
			->where('id', '=', $id)
			->find();
		if ( ! $model->loaded())
		{
			HTTP::redirect('/');
		}

		$content = $model->action_view();

		$this->render(array(
			'title' => $model->title(),
			'metadesc' => Kohana::config('Information.metadesc'),
			'metakw' => Kohana::config('Information.metakw'),
			'content' => $content,
		));
	}

	/**
	 * 发布信息
	 */
	public function action_submit()
	{
		// 要先登录
		if ( ! Auth::instance()->logged_in())
		{
			HTTP::redirect( Route::url('auth-action', array('action' => 'login')) );
		}

		$errors = array();
		if ($this->request->is_post())
		{
			try
			{
				$model = Model_Information::factory($this->request->post('model_type'));
				$model->action_save($this->request->post()); 

				// 提交成功页面
				$this->render(array(
					'title' => __('发布成功'),
					'metadesc' => Kohana::config('Information.metadesc'),
					'metakw' => Kohana::config('Information.metakw'),
					'content' => View::factory('Information.Submit.Book.Success', array(
						'model' => $model,
					)),
				));
				return;
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors = $e->errors();
			}
		}

		$this->render(array(
			'title' => __('发布信息'),
			'metadesc' => Kohana::config('Information.metadesc'),
			'metakw' => Kohana::config('Information.metakw'),
			'content' => View::factory('Information.Submit.Book', array(
				'post' => $this->request->post(),
				'errors' => $errors,
			)),
		));
	}

	/**
	 * 评论
	 */
	public function action_comment()
	{
		// 要先登录
		if ( ! Auth::instance()->logged_in())
		{
			HTTP::redirect( Route::url('auth-action', array('action' => 'login')) );
		}

		// 首先要确保这个记录存在
		$model = Model_Information::factory($this->request->param('type'))
			->where('id', '=', $this->request->param('id'))
			->find();
		if ( ! $model->loaded())
		{
			HTTP::redirect('/');
		}
		
		if ($this->request->is_post() AND $this->request->post('content'))
		{
			$model->save_comment($this->request->post('content'));
		}
		HTTP::redirect(Route::url('information-action', array(
			'type' => $this->request->param('type'),
			'action' => 'view',
			'id' => $model->id,
		)));
	}

	/**
	 * 我发布的信息，列表
	 */
	public function action_mine()
	{
		// 当然要先登录啊
		if ( ! Auth::instance()->logged_in())
		{
			HTTP::redirect(Route::url('information-index'));
		}

		$type = $this->request->param('type');
		$page = (int) $this->request->param('id');
		if ($page < 1)
		{
			$page = 1;
		}

		$limit = 10;
		$offset = ($page - 1) * $limit;

		// 暂时无视这个
		$records = Model::factory('Information.Book')
			->where('poster_id', '=', Auth::instance()->get_user()->id)
			->order_by('id', 'DESC')
			->limit($limit)
			->offset($offset)
			->find_all();

		$this->render(array(
			'title' => '我发布的信息',
			'content' => View::factory('Information.Mine', array(
				'records' => $records,
			)),
		));
	}
	
	/**
	 * 搜索功能
	 */
	public function action_search()
	{
		$keyword = $this->request->post('keyword');

		$model = Model_Information::factory('book');
		$list = $model->action_search($keyword);
		
		$this->render(array(
			'title' => __(':keyword\'s search result', array(
				':keyword' => $keyword,
			)),
			'metadesc' => Kohana::config('Information.metadesc'),
			'metakw' => Kohana::config('Information.metakw'),
			'content' => $list,
		));
	}
}
