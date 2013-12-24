<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 微博控制器
 *
 * @package		Kohana/Weibo
 * @category	Controller
 * @author		YwiSax
 */
class Kohana_Controller_Weibo extends Controller_XunSec {

	public $layout = 'blank';

	/**
	 * 回调地址
	 */
	public function action_callback()
	{
		return Weibo::callback();
	}
	
	/**
	 * 微博发布页面
	 */
	public function action_post()
	{
		Weibo::authentication();
	
		$this->render(array(
			'title' => __('Post Weibo'),
			'metadesc' => Kohana::config('Weibo.metadesc'),
			'metakw' => Kohana::config('Weibo.metakw'),
			'content' => View::factory('Weibo.Post'),
		));
	}
	
	/**
	 * 获取表情列表
	 */
	public function action_emotions()
	{
		// 依然是要验证一次
		Weibo::authentication();
		
		$emotions = Weibo::get_emotions();
		//var_export($emotions);
		
		// 为了方便，直接读数组返回数据好了，一般来说，表情更新不会太频繁吧？
		// 这里返回的数组跟上面返回的其实是一样的
		//$emotions = Kohana::config('WeiboEmotions');
		$emotion_groups = array();

		// 先抽取常用的表情出来
		/* foreach ($emotions AS $key => $emotion)
		{
			if ( ! isset($emotion_groups[$emotion['category']]))
			{
				$emotion_groups[$emotion['category']] = array();
			}
			$emotion_groups[$emotion['category']][$key] = $emotion;
		} */
		foreach ($emotions AS $key => $emotion)
		{
			// 只要常用的和热门的
			if ($emotion['common'] OR $emotion['hot'])
			{
				$emotion_groups[] = $emotion;
			}
		}
		
		$this->response->body(View::factory('Weibo.Emotions', array(
			'emotions' => $emotion_groups,
		)));
		//echo Debug::vars($emotion_groups);
	}
	
	/**
	 * 发送微博啊，正常发布时返回为空
	 */
	public function action_send()
	{
		// 是否关闭了发布功能
		if ( ! Weibo::setting('client_status'))
		{
			echo __('The weibo client has been closed');
			return;
		}
	
		// 依然是要验证一次
		Weibo::authentication();

		/**
		 * 检查当前用户是否有权限发
		 */
		// 先看是否已经被设置了禁用时间
		if (Weibo::$user->ban_expired)
		{
			// 如果到期时间已经过了
			if (Weibo::$user->ban_expired < time())
			{
				// 那就重置下过期时间吧
				Weibo::$user->ban_expired = 0;
				Weibo::$user->save();
			}
			// 还在禁止时间内，别想发了大爷
			else
			{
				echo __('You are banned now until :time.', array(
					':time' => date('Y-m-d H:i:s', Weibo::$user->ban_expired),
				));
				return;
			}
		}

		// 要检验下是否在可发布时间段内
		$time_range = Weibo::setting('allow_time_range');
		$time_range = explode(',', $time_range);
		$current_date_int = (int) date('Hi');
		$in_time_range = FALSE;
		//echo $current_date_int;exit;
		foreach ($time_range AS $range)
		{
			$range = explode('-', trim($range));
			if ( ! $range)
			{
				continue;
			}
			
			// 规范格式
			if (isset($range[0]) AND isset($range[1]))
			{
				$range[0] = str_replace(':', '', $range[0]);
				$range[0] = intval($range[0]);
				$range[1] = str_replace(':', '', $range[1]);
				$range[1] = intval($range[1]);
			}
			else
			{
				continue;
			}
			// 检查是否在发布时间段内
			if ((intval($range[0]) < $current_date_int) AND ($current_date_int < intval($range[1])))
			{
				$in_time_range = TRUE;
			}
		}
		if ( ! $in_time_range)
		{
			echo __('Now is not the time range allowed to send.');
			return;
		}
		
		// 要限制发送时限的啊
		$latest_weibo =  Model::factory('Weibo.Feed')
			->order_by('id', 'DESC')
			->limit(1)
			->find();
		$latest_time = $latest_weibo->loaded() ? $latest_weibo->date_created : 0;
		$post_interval = Weibo::setting('post_interval');
		// 不在时限内
		if ((time() - $latest_time) < $post_interval)
		{
			echo __('Administrator has set up the time limit to post weibo, left :second s', array(
				':second' => $post_interval - (time() - $latest_time)
			));
			exit;
		}

		// 下面要注意，带图和不带图使用的是不同的接口
		
		$img_id = $this->request->post('img_id');
		$feed = $this->request->post('feed');

		// 注意后面的token，如果是要发布到树洞的话，就要修改成树洞的token
		echo Weibo::send($feed, $img_id, Weibo::client('token')->val);

		// 测试环境中，暂时这样吧
		//echo Weibo::send($feed, $img_id);
	}
	
	/**
	 * 上传图片啦
	 */
	public function action_upload()
	{
		// 依然是要验证一次
		Weibo::authentication();

		$image = Model::factory('Weibo.Image')
			->upload($_FILES['Filedata']);

		// 返回一段json、
		$this->response->headers('Content-type', 'text/json');
		echo json_encode($image ? array(
			'id' => $image->id,
			'image' => Media::url($image->filepath, 'http'),
		) : array());
	}
}
