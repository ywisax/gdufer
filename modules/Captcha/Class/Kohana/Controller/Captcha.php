<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 输出验证码图片
 * Usage: Call the Captcha controller from a view, e.g.
 *        <img src="<?php echo URL::site('captcha') ?>" />
 *
 * $Id: captcha.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package		Kohana/Captcha
 * @cateogry	Controller
 * @author		YwiSax
 */
class Kohana_Controller_Captcha extends Controller {

	/**
	 * @var boolean Auto render template
	 **/
	public $auto_render = FALSE;

	public function before()
	{
		$this->group = $this->request->param('group', 'default');
	}
	
	/**
	 * Output the captcha challenge
	 *
	 * @param string $group Config group name
	 */
	public function action_index()
	{
		// Output the Captcha challenge resource (no html)
		// Pull the config group name from the URL
		//$group = $this->request->param('group', 'default');
		Captcha::instance($this->group)->render(FALSE);
	}

	public function after()
	{
		Captcha::instance($this->group)->update_response_session();
	}

} // End Captcha_Controller
