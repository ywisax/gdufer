<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 教务处用户
 *
 * @package		Kohana/Gduf
 * @category	Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Model_Gduf_JWC_User extends Model_Gduf_JWC {

	public $second_limit = 600; // 10分钟一次还算正常。

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);
	
	/**
	 * 校验规则
	 */
	public function rules()
	{
		return array(
			'owner_id' => array(
				array('not_empty'),
			),
			'username' => array(
				array('not_empty'),
			),
			'password' => array(
				array('not_empty'),
			),
		);
	}
	
	/**
	 * 过滤
	 */
	public function filters()
	{
		return array(
			'owner_id' => array(
				array('trim'),
				array('intval'),
			),
			'username' => array(
				array('trim'),
				array('strip_tags'),
			),
			'password' => array(
				array('trim'),
				array('strip_tags'),
			),
		);
	}
	
	/**
	 * 更新不能太勤快滴，限制描述
	 */
	public function second_limit()
	{
		// 获取最新的那个时间
		$time = $this->date_created;
		if ($time < $this->date_updated)
		{
			$time = $this->date_updated;
		}
		if ($time < $this->date_touched)
		{
			$time = $this->date_touched;
		}
		//echo date('Y-m-d H:i:s', $time) . '<br />';
		//echo date('Y-m-d H:i:s', time()) . '<br />';
		//echo '相差'.((time() - $time)) . '<br />';
		
		// 当前时间-更新时间 > 限制秒数
		if ((time() - $time) > $this->second_limit)
		{
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * 登陆教务系统
	 */
	public function login($username = NULL, $password = NULL)
	{
		// 判断要登陆的用户名和密码
		if ($username === NULL)
		{
			$username = $this->username;
		}
		if ($password === NULL)
		{
			$password = $this->password;
		}
		
		$url = Model_Gduf_JWC::url();
		//echo $url . '<br />';

		// 获取登录页面
		//echo $url;
		$request = Request::factory($url)->execute();
		preg_match("/<input(.*)value=\"(.*)\"\s\/>/i", $request, $view_state);
		//echo $request;
		$view_state = isset($view_state[2]) ? $view_state[2] : ''; // 获取ViewState
		if ( ! $view_state)
		{
			throw new Gduf_Exception('Failed to fetch the login view state code');
		}
		
		//echo 'The view state is: ' . $view_state . '<br />';
		
		$request = Request::factory($url)
			->method('POST')
			->headers('Referer', $url) // 设置来路信息
			->post('__VIEWSTATE', $view_state)
			->post('TextBox1', $this->username)
			->post('TextBox2', $this->password)
			->post('RadioButtonList1', '学生')
			->post('Button1', '')
			->post('lbLanguage', '')
			->execute();
		if ($request->status() == 200) // 登录失败
		{
			// 登陆失败
			//exit('Login FAILED!!! Check your internet access or your password.');
			return FALSE;
		}
		else
		{
			Model_Gduf_JWC::$xs_main_url = $request->headers('Location'); // 保存xs_main.aspx的地址
			//echo Debug::vars($request);
			// 登陆成功
			return TRUE;
		}
	}
	
	/**
	 * 获取课表
	 */
	public function fetch_schedule()
	{
		$xs_main_url = Model_Gduf_JWC::JWC_URL . Model_Gduf_JWC::$xs_main_url;
		$request = Request::factory($xs_main_url)
			->headers('Referer', Model_Gduf_JWC::url())
			->execute();
		//echo Debug::vars($request);

		// 获取登录后的页面, 主要是要从中获取得到课表信息
		$personl_page = mb_convert_encoding((string) $request, 'UTF-8', 'GBK');
		//echo $personl_page;
		// <a href="xskbcx.aspx?xh=111586437&xm=姓名&gnmkdm=N121603"
		// 主要是获取后面的参数
		$final_url = '';
		preg_match('/<a href="xskbcx.aspx(.*?)" /i', $personl_page, $final_url);
		$final_param = $final_url[1];

		$url = Model_Gduf_JWC::url('xskbcx.aspx'.$final_param);
		//echo $url.'<br/>';
		$final_req = Request::factory($url)
			->headers('Referer', $xs_main_url) // 设置来路
			->execute();
		// 要说明以下，默认显示的最新的课表
		//echo "Final request:<br/>";
		//echo Debug::vars($final_req);
		//echo '<br />';
		$final_html = mb_convert_encoding((string) $final_req, 'UTF-8', 'GBK');
		//echo $final_html;
		//file_put_contents('a.html', $final_html);
		// OK啦。获取到所有课表啦拉拉啦拉拉拉拉案例拉拉
		
		// 学号
		preg_match('/<span id="Label5">学号：(.*)<\/span>/', $final_html, $student_no);
		if (isset($student_no[1]))
		{
			$this->student_no = $student_no[1];
		}
		
		// 姓名
		preg_match('/<span id="Label6">姓名：(.*)<\/span>/', $final_html, $realname);
		if (isset($realname[1]))
		{
			$this->realname = $realname[1];
		}

		// 系
		preg_match('/<span id="Label7">学院：(.*)<\/span>/', $final_html, $department);
		if (isset($department[1]))
		{
			$this->department = $department[1];
		}

		// 专业
		preg_match('/<span id="Label8">专业：(.*)<\/span>/', $final_html, $major);
		if (isset($major[1]))
		{
			$this->major = $major[1];
		}

		// 班别
		preg_match('/<span id="Label9">行政班：(.*)<\/span>/', $final_html, $class);
		if (isset($class[1]))
		{
			$this->class = $class[1];
		}

		// 匹配课程表格
		preg_match('/<table id="Table1" class="blacktab" bordercolor="Black" border="0" width="100%">([\s\S]*)<\/table>\r\n\t\t\t\t\t\t<br>/', $final_html, $schedule_table);
		//echo $schedule_table[0];
		if (isset($schedule_table[0]))
		{
			$schedule_table = $schedule_table[0];
			$schedule_table = str_replace('<table id="Table1" class="blacktab"', '<table id="Table1" class="table table-bordered"', $schedule_table);
			$this->schedule = $schedule_table;
		}
		
		$this->date_touched = time();
		$this->save();
		
		return $this;
	}

}

