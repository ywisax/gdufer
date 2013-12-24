<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 论坛群组控制器
 *
 * @package		Kohana/Forum
 * @category	Controller
 */
class Kohana_Controller_Forum_Group extends Controller_Forum {

	/**
	 * 默认是列出所有群组啦
	 */
	public function action_index()
	{
		// 处理分页相关
		$page = $this->request->param('page');
		if ($page < 1)
		{
			$page = 1;
		}
		$limit = 14;
		$offset = ($page - 1) * $limit;
		
		// 处理群组相关
		$group_id = $this->request->param('group');
		$group = Model::factory('Forum.Group');
		if ($group_id)
		{
			$group
				->where('id', '=', $group_id)
				->find();
		}
		
		// 查找帖子
		$topics_total_count = Model::factory('Forum.Topic');
		$topics = Model::factory('Forum.Topic')
			->where('visible', '=', 1)
			->order_by('sticky', 'DESC')
			->order_by('date_touched', 'DESC')
			->limit($limit)
			->offset($offset);
		
		// 指定群组
		if ($group->loaded())
		{
			$topics->where('group_id', '=', $group->id);
			$topics_total_count->where('group_id', '=', $group->id);
		}
		$topics = $topics->find_all();
		$topics_total_count = $topics_total_count->find_all()->count();

		// 处理分页
		$pagination_config = Kohana::config('Pagination.forum');
		$pagination_config['total_items'] = $topics_total_count;
		$pagination_config['items_per_page'] = $limit;
		
		// 查找所有群组
		$groups = Model::factory('Forum.Group')
			->order_by('count', 'DESC')
			->find_all();

		$content = View::factory('Forum.Group.Main', array(
			'topics' => $topics,
			'groups' => $groups,
			'group' => $group,
			'page' => $page,
			'pagination' => Pagination::factory($pagination_config),
		));

		$title = $group->loaded() ? $group->name : __('Forum Index');
		$title .= ' '.__('Page :page', array(':page' => $page));
		$this->render(array(
			'title' => $title,
			'metadesc' => Kohana::config('Forum.metadesc'),
			'metakw' => Kohana::config('Forum.metakw'),
			'content' => $content,
		));
	}
}
