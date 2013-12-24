<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Contact基础控制器
 *
 * @package    Kohana/Contact
 * @category   Base
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/
 */
class Kohana_Controller_Contact extends Controller_XunSec {

	public $layout = 'blank';
	
	/**
	 * 联系我们-表单
	 */
	public function action_form()
	{
		$errors = array();
		// 只能post提交
		if ($this->request->is_post())
		{
			$captcha_code = $this->request->post('captcha');
			if (Captcha::valid($captcha_code))
			{
				try
				{
					$this->request->post('status', 1);
					$this->request->post('ip', Request::$client_ip);
					$contact_info = Model::factory('Contact.Info')
						->values($this->request->post())
						->save();
					$this->render(array(
						'title' => __('Submit Successful'),
						'content' => View::factory('Contact.Form.Success'),
					));
					return;
				}
				catch (ORM_Validation_Exception $e)
				{
					$errors = $e->errors();
				}
			}
			else
			{
				$errors['captcha'] = __('Captcha code must be correct');
			}
		}
	
		$this->render(array(
			'title' => __('Contact Form'),
			'metadesc' => Kohana::config('Contact.metadesc'),
			'metakw' => Kohana::config('Contact.metakw'),
			'content' => View::factory('Contact.Form', array(
				'errors' => $errors,
			)),
		));
	}
}
