<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 控制器基类。默认情况下，控制器只能在[Request]中调用。
 *
 * 控制器的方法会依照以下的顺序被调用：
 *
 *     $controller = new Controller_Foo($request);
 *     $controller->before();
 *     $controller->action_bar();
 *     $controller->after();
 *
 * 关于输出，最好是使用`$this->response->body($output)`来设置，传递一个[View]对象
 *
 * @package    Kohana
 * @category   Controller
 */
abstract class Kohana_Controller {

	const ACTION_PREFIX = 'action_';

	/**
	 * @var  Request  当前控制器创建的请求实例
	 */
	public $request;

	/**
	 * @var  Response  控制器要返回的输出内容
	 */
	public $response;

	/**
	 * 创建一个新的控制器实例
	 *
	 * @param   Request   $request  执行了当前控制器的请求实例
	 * @param   Response  $response  控制器输出实例
	 * @return  void
	 */
	public function __construct(Request $request, Response $response)
	{
		$this->request = $request;
		$this->response = $response;
	}

	/**
	 * 根据指定的动作来完成流程，在执行指定动作前会自动调用[Controller::before]和[Controller::after]。
	 * 
	 * @return  Response
	 */
	public function execute()
	{
		// 先执行"before action"方法
		$this->before();

		// 要执行的动作
		$action = Controller::ACTION_PREFIX . $this->request->action();

		// 动作不存在，404
		if ( ! method_exists($this, $action))
		{
			throw HTTP_Exception::factory(404,
				'The requested URL :uri was not found on this server.',
				array(':uri' => $this->request->uri())
			)->request($this->request);
		}

		// 执行动作
		$this->{$action}();

		// 后执行"after action"方法
		$this->after();

		// 返回输出
		return $this->response;
	}

	/**
	 * 预执行操作
	 *
	 * @return  void
	 */
	public function before()
	{
		// 默认该处为空
	}

	/**
	 * 后执行动作
	 * 
	 * @return  void
	 */
	public function after()
	{
		// 默认该处为空
	}

	/**
	 * Checks the browser cache to see the response needs to be returned,
	 * execution will halt and a 304 Not Modified will be sent if the
	 * browser cache is up to date.
	 * 
	 *     $this->check_cache(sha1($content));
	 * 
	 * @param  string  $etag  Resource Etag
	 * @return Response
	 */
	protected function check_cache($etag = NULL)
	{
		return HTTP::check_cache($this->request, $this->response, $etag);
	}

} // End Controller
