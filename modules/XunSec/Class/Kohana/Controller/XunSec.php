<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * CMS控制器基础类
 * 这个控制器主要是进行页面的查找和渲染
 * 如果要了解CMS和控制器的扩展，建议参看下Module目录下的代码
 *
 * @package		XunSec
 * @category	Controller
 * @copyright	YwiSax
 */
abstract class Kohana_Controller_XunSec extends Controller {

	/**
	 * 查看CMS指定页面
	 */ 
	public function action_view()
	{
		if (Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start('XunSec', 'XunSec Controller');
		}
		
		$url = $this->request->param('path');
		
		// 去除 Kohana::$base_url
		$url = preg_replace('#^'.Kohana::$base_url.'#', '', $url);
		
		// 去除结尾的斜杆
		$url = preg_replace('/\/$/', '', $url);
		
		// 去除开头的斜杆
		$url = preg_replace('/^\//', '', $url);
		
		// Remove anything ofter a ? or #
		$url = preg_replace('/[\?#].+/', '', $url);

		try
		{
			// 要确保URL有效，可以参考http://www.faqs.org/rfcs/rfc2396.html
			// 不过部分客户会要求在URL中带中午，所以只能去掉咯。
			//if (preg_match("/[^\/A-Za-z0-9-_\.!~\*\(\)]/", $url))
			//{
			//	Kohana::$log->add('INFO', "XunSec - Request had unknown characters. '$url'"); 
			//	throw new XunSec_Exception("Url request had unknown characters '$url'", array(), 404);
			//}

			// 下面这里可能要做个缓存，减少一次查询
			if (1)
			{
				// 检查这个URL是否有跳转
				Model::factory('Redirect')
					->where('url', '=', $url)
					->find()
					->go();
			}
			
			// 查找页面
			$page = Model::factory('Page')
				->where('url', '=', $url)
				->where('islink', '=', 0)
				->find();
			
			if ( ! $page->loaded())
			{
				// 404
				Kohana::$log->add('INFO', "XunSec - Could not find ':url', IP: :ip, BROWSER: :browser", array(
					':url' => $url,
					':ip' => Request::$client_ip,
					':browser' => strip_tags(Request::$user_agent), // 防止出现有害的PHP代码
				)); 
				throw new XunSec_Exception("Could not find '$page->url'", array(), 404);
			}
			
			// 渲染页面
			$this->response->status(200);
			$out = $page->render();
		}
		catch (XunSec_Exception $e)
		{
			$out = $this->error();
		}

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
		}
		
		// 最后输出页面啦
		$this->response->body($out);
	}
	
	/**
	 * 返回错误页面
	 */
	public function error()
	{
		$this->response->status(404);
		$error = Model::factory('Page')
			->where('url', '=', 'error')
			->find();

		// 默认的视图404页面
		if ( ! $error->loaded())
		{
			return View::factory('XunSec.404');
		}
		return $error->render();
	}
	
	/**
	 * 非CMS页面的渲染助手
	 */
	public function render($data)
	{
		XunSec::$_page = new stdClass;
	
		// 标题
		if (isset($data['title']))
		{
			XunSec::page('title', $data['title']);
		}

		// 描述
		if (isset($data['metadesc']))
		{
			XunSec::page('metadesc', $data['metadesc']);
		}

		// 关键词
		if (isset($data['metakw']))
		{
			XunSec::page('metakw', $data['metakw']);
		}

		// 渲染内容
		$this->response->body(
			XunSec::override(
				$this->layout,
				(isset($data['content']) ? $data['content'] : '')
			)
		);
	}
	
	/**
	 * 渲染出JSON字符串
	 */
	public function json_render($data, $code = 200)
	{
		$json = new XunSec_JSON;
		$json->code((int) $code);
		$json->body($data);
		
		$this->response->headers('Content-Type', 'application/json');
		$this->response->body($json->response());
	}
}

