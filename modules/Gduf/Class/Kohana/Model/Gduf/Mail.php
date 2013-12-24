<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 广金邮箱
 *
 * @package		Kohana/Gduf
 * @category	Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Model_Gduf_Mail extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);
	
	/**
	 * GET请求获取数据
	 */
	public static function query_data($uri, $data = array())
	{
		if ( ! Model_Gduf::check_login())
		{
			return FALSE;
		}

		$request = Request::factory(GDUF_DOMAIN . $uri);
		$request->method('GET');
		$request->query($data);
		$request->cookie(array(
			'JSESSIONID' => Model_Gduf::session_id(),
			'Path' => '/',
		));
		$request->referrer(GDUF_DOMAIN);
		$response = $request->execute();

		return array(
			'status' => $response->status(),
			'body' => trim(Model_Gduf::convert($response->body())),
		);
	}
	
	/**
	 * POST请求获取数据
	 */
	public static function post_data($uri, $data, $check_login = TRUE)
	{
		if ($check_login AND ! Model_Gduf::check_login())
		{
			return FALSE;
		}

		$request = Request::factory(GDUF_DOMAIN . $uri);
		$request->method('POST');
		$request->post($data);
		$request->cookie(array(
			'JSESSIONID' => Model_Gduf::session_id(),
			'Path' => '/',
		));
		$request->referrer(GDUF_DOMAIN . $uri);
		$response = $request->execute();
		
		echo Debug::vars($request);

		return array(
			'status' => $response->status(),
			'cookie' => $response->headers('set-cookie'),
			'body' => trim(Model_Gduf::convert($response->body())),
		);
	}
	
	/**
	 * 发送邮件
	 */
	public static function send($post = NULL)
	{
		if ($post === NULL)
		{
			$post = Request::current()->post();
		}
		
		// 提交啦~~~
		//$response = Model_Gduf_Mail::post_data('mail/mail_addok.jsp', $post);
		$response = Model_Gduf_Mail::post_data('mail/mail_addok.jsp', array(
			'annex' => '',
			'content' => '<font color="red">哈哈哈哈哈哈哈放大发大水范德萨范德萨</font>',
			'addr' => '吴立莹,吴立英',
			'addr_id' => '48777,41614',
			'copy_id' => '',
			'secret_id' => '',
			'secret' => '',
			'addr_depart' => '0',
			'addr_class' => '0',
			'copy_depart' => '',
			'secret_depart' => '',
			'addr_group' => '',
			'specialCondition' => '',
			'addr2' => '',
			'title' => 'Send From Gdufer',
			'count' => 0,
			'importance' => '',
			'reply' => '1',
			'replyto' => '48781',
			'replyName' => 'FFFFFFFFFFFFFFFFFF',
			'replyto2' => 'WWWWWWWWWWWW',
			'folder' => '1',
			'saveto' => '2',
			'before' => '',
			'last' => '',
		));
		return $response;
	}
	
	/**
	 * 获取指定的邮件
	 */
	public static function fetch($query = NULL)
	{
		if ($query === NULL)
		{
			$query = Request::current()->query();
		}
		
		// 先从数据库中检查，如果数据库中已经有记录，那就从数据库中返回
		if (isset($query['id']))
		{
			$record = Model::factory('Gduf.Mail')
				->where('mail_id', '=', $query['id'])
				->find();
			// 如果数据库已经有啦，那就直接返回好了
			if ($record->loaded())
			{
				// 查看数加一
				$record->count ++;
				$record->save();
				return $record->content;
			}
		}

		// 获取数据
		$response = Model_Gduf_Mail::query_data('mail/mail_read.jsp', $query);

		// 获取返回的状态码，如果不是200那就是错误了
		if ($response['status'] != 200)
		{
			Helper_Cookie::delete('JSESSIONID');
			return '未登陆，或登陆超时，请刷新本页！';
		}
		
		$response_body = $response['body'];
		// 终于找到个可以匹配的了。。。。
		preg_match('/<table width="100%" border="0" cellpadding="0" cellspacing="0">([\s\S]*)<\/table>\r\n\s\r\n<br>\r\n<br>\r\n<br>/', $response_body, $response_data);
		
		// 到这里就获取到正文的大部分内容啦
		$response_data = $response_data[0];
		
		// 下面还要处理下里面的iframe，哎哎，还要读多一次，感觉这里会效率低
		preg_match('/<iframe width=600 height=400 frameborder=0 scrolling=auto src="mail_read_html.jsp\?id=([0-9]+)" name="main" scrolling="yes" noresize>/', $response_data, $match_iframe);
		$match_iframe_id = (int) $match_iframe[1];
		// 获取iframe中的邮件正文
		$iframe_response = Model_Gduf_Mail::query_data('mail/mail_read_html.jsp', array('id' => $match_iframe_id));
		$iframe_response = trim($iframe_response['body']);
		
		// 正则替换。。。最讨厌这些了
		//preg_replace('/(<iframe([^>]+)>)([\s\S]*)(<\/iframe>)/', '', $response_data);
		$response_data = str_replace('</iframe>', '', $response_data);
		$response_data = str_replace('<iframe width=600 height=400 frameborder=0 scrolling=auto src="mail_read_html.jsp?id='.$match_iframe_id.'" name="main" scrolling="yes" noresize>', "<div class=\"iframe-content\">$iframe_response</div>", $response_data);
		
		$response_data = Model_Gduf_Mail::clean_mail($response_data);
		
		// 还要对其中的table什么的进行处理一下才行啊
		// <table width="95%" border="0" align="center" cellpadding="3" cellspacing="0">
		$response_data = str_replace('<table width="95%" border="0" align="center" cellpadding="3" cellspacing="0">', '<table class="table mail-table">', $response_data);
		// 替换多余的表格
		//$response_data = preg_replace('/<table width="610" border="0" cellpadding="3" cellspacing="1" bgcolor="#000000">([\s\S]*)<\/table>/i', '$1', $response_data);
		$response_data = str_replace('<table width="610" border="0" cellpadding="3" cellspacing="1" bgcolor="#000000">', '<table class="table mail-meta">', $response_data);
		$response_data = str_replace('<table  border="0" cellpadding="3" cellspacing="1" bgcolor="#000099">', '<table class="table mail-content">', $response_data);
		// 顶部发件人信息
		$response_data = str_replace('<table width="100%" border="0" cellspacing="0" cellpadding="3">', '<table class="table subject">', $response_data);
		$response_data = str_replace('<table border="0" align="left"  cellspacing="0">', '<table class="table data">', $response_data);
		$response_data = str_replace('<td bgcolor="#FFFFFF">', '<td>', $response_data);
		$response_data = str_replace('<td height="400" valign="top" align="top">', '<td>', $response_data);
		
		// 最后过滤下空格啊之类的东西
		$response_data = preg_replace('/[\n\r\t]/', ' ', $response_data);
		$response_data = preg_replace('/\s(?=\s)/', '', $response_data);
		
		// 补充过滤，过滤正文中的标题，在modal头部已经有标题啦啦啦
		//$response_data = preg_replace('/<tr> <td width="60">主题<\/td> <td align="left" colspan=3>:([\s\S]*)<\/td> <\/tr>/', '', $response_data);
		$response_data = str_replace('<td width="60"', '<td width="80"', $response_data);
		// 修正广金eweb上传的文件bug
		$response_data = str_replace('src="/eWebEditor/', 'src="http://www.gduf.edu.cn/eWebEditor/', $response_data);
		$response_data = str_replace('href="/eWebEditor/', 'href="http://www.gduf.edu.cn/eWebEditor/', $response_data);
		
		// 到这里就基本获取得到我们最需要的邮件内容
		
		// 执行到这里，一般情况下都是还没有保存到数据库的，要保存啊
		$mail = Model::factory('Gduf.Mail');
		$mail->mail_id = $query['id'];
		$mail->content = $response_data;
		$mail->save();
		
		return $response_data;
	}
	
	/**
	 * 过滤获取到的邮件
	 */
	public static function clean_mail($response_data)
	{
		// 去除头部的几个按钮表格
		$response_data = preg_replace('/<table width="100%" border="0" cellpadding="0" cellspacing="0">([\s\S]*)<\/table>\r\n<br>/', '', $response_data);
		
		// 处理下下载链接的问题
		$response_data = str_replace('"../downloadFile.jsp?link=', '"http://www.gduf.edu.cn/downloadFile.jsp?link=', $response_data);

		// 去掉 “阅读html格式” 正则表达式不好玩啊。。。
		$response_data = preg_replace('/<tr>\r\n([\s]+)<td\salign="center"><a\shref="mail_read_html.jsp\?id=(\d+)">阅读html格式<\/a>\r\n([\s]+)<\/td>\r\n([\s]+)<\/tr>/', '', $response_data);
		
		// 去除一个多余的form
		$response_data = str_replace('<form name="formSave" method="post" action="mail_addok.jsp">', '', $response_data);
		$response_data = str_replace("</form>\r\n</table>\r\n \r\n<br>\r\n<br>\r\n<br>", '</table>', $response_data);
		
		// 上面处理过后，剩下的基本就是我们要的内容啦
	
		return $response_data;
	}
	
	/**
	 * 解析和获取mail_list
	 */
	public static function parse_mail_list($response_body)
	{
		$response_data = array();
		$response_data['current_page'] = 0;
		$response_data['total_page'] = 0;
		$response_data['list'] = array();
		
		// 获取当前页和总页数
		if (preg_match_all('/<td align="right" nowrap> 第([0-9]+)页\/共([0-9]+)页&nbsp;/', $response_body, $matches))
		{
			$response_data['current_page'] = (int) $matches[1][0];
			$response_data['total_page'] = (int) $matches[2][0];
		}
		
		// 获取邮件信息，貌似有点问题，部分邮件会丢失
		if (preg_match_all("/readMail\('([\d]+)','([\d]+)','([\d]+)','([\d]+)','([\d]+)','([\d]+)','([\d]+)','([\d]+)'\)\">\s\s<font\scolor='(.*)' >(.*)<\/font>&nbsp;<\/a>/", $response_body, $matches))
		{
			// foldertype
			foreach ($matches[1] AS $key => $value)
			{
				$response_data['list'][$key]['foldertype'] = $value;
			}
			// mail id
			foreach ($matches[2] AS $key => $value)
			{
				$response_data['list'][$key]['id'] = $value;
			}
			// page，这个可能是多余的
			foreach ($matches[3] AS $key => $value)
			{
				$response_data['list'][$key]['page'] = $value;
			}
			// personId
			foreach ($matches[4] AS $key => $value)
			{
				$response_data['list'][$key]['personId'] = $value;
			}
			// reply
			foreach ($matches[5] AS $key => $value)
			{
				$response_data['list'][$key]['reply'] = $value;
			}
			// transmit
			foreach ($matches[6] AS $key => $value)
			{
				$response_data['list'][$key]['transmit'] = $value;
			}
			// transmit，日。。。居然重复了一个。。。
			foreach ($matches[7] AS $key => $value)
			{
				$response_data['list'][$key]['transmit'] = $value;
			}
			// readFlag
			foreach ($matches[8] AS $key => $value)
			{
				$response_data['list'][$key]['read'] = $value;
			}
			// color
			foreach ($matches[9] AS $key => $value)
			{
				$response_data['list'][$key]['color'] = $value;
			}
			// subject
			foreach ($matches[10] AS $key => $value)
			{
				$response_data['list'][$key]['subject'] = $value;
			}
		}
		
		// 获取邮件大小
		if (preg_match_all("/<td width=\"79\"  align=\"left\" valign=\"top\" >(.*)&nbsp;<\/td>/", $response_body, $matches))
		{
			// 邮件大小
			// 分钟
			foreach ($matches[1] AS $key => $value)
			{
				$response_data['list'][$key]['size'] = $value;
			}
		}
		
 		// 获取下载链接
		if (preg_match_all("/<td width=\"60\"  align=\"left\" valign=\"top\" >(.*)<\/td>/", $response_body, $matches))
		{
			// 针对发信箱，要做额外的处理
			if (Model_Gduf::$request->query('foldertype') == 2)
			{
				$downlinks = array();
				$matches[1] = array_chunk($matches[1], 2);
				foreach ($matches[1] AS $match)
				{
					$downlinks[] = $match[0];
				}
			}
			else
			{
				$downlinks = $matches[1];
			}
			foreach ($downlinks AS $key => $value)
			{
				$value = str_replace('../downloadFile.jsp?link=', 'http://www.gduf.edu.cn/downloadFile.jsp?link=', $value); // 替换成完整路径，要不太麻烦
				$value = str_replace('<a ', '<a target="_blank" ', $value); // 新窗口打开
				$response_data['list'][$key]['downlink'] = $value;
			}
		}
		
		//echo $response_body;
		
		// 内部发件人 外部发件人 时间
		if (preg_match_all("/<td align=\"left\" valign=\"top\" >(.*?)<\/td>/is", $response_body, $matches))
		{
			//echo Debug::vars($matches);
			$matches = array_chunk($matches[1], 4);
			foreach ($matches AS $key => $value)
			{
				$response_data['list'][$key]['YJYG'] = $value[0];
				$response_data['list'][$key]['YJFJDZ'] = isset($value[1]) ? $value[1] : '';
				$response_data['list'][$key]['YJFSSJ'] = isset($value[3]) ? trim(str_replace('&nbsp;', '', $value[3])) : '';
			}
		}
		
		return $response_data;
	}
	
}

