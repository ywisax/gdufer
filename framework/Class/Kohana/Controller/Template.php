<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Abstract controller class for automatic templating.
 *
 * @package    Kohana
 * @category   Controller
 */
abstract class Kohana_Controller_Template extends Controller {

	/**
	 * @var  View  page template
	 */
	public $template = 'Template';

	/**
	 * @var  boolean  auto render template
	 **/
	public $auto_render = TRUE;

	/**
	 * Loads the template [View] object.
	 */
	public function before()
	{
		parent::before();

		if ($this->auto_render === TRUE)
		{
			// 加载模板视图
			$this->template = View::factory($this->template);
		}
	}

	/**
	 * Assigns the template [View] as the request response.
	 */
	public function after()
	{
		if ($this->auto_render)
		{
			$this->response->body($this->template->render());
		}

		parent::after();
	}

} // End Controller_Template
