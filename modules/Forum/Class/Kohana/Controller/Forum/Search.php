<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 论坛搜索控制器
 *
 * @package		Kohana/Forum
 * @category	Controller
 */
class Kohana_Controller_Forum_Search extends Controller_Forum {

	/**
	 * 搜索主题
	 */
	public function action_index()
	{
		$keyword = $this->request->param('keyword');
		// 从路由中获取不到keyword
		if ( ! $keyword)
		{
			if ($this->request->post('keyword'))
			{
				HTTP::redirect(Route::url('forum-search', array('keyword' => $this->request->post('keyword'))));
			}
		}

		echo $keyword;
	}

}
