<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 附件控制器，提供一些简单的上传和下载功能
 *
 * @package    Kohana/Attachment
 * @category   Controller
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Controller_Attachment extends Controller_XunSec {

	/**
	 * 上传文件，返回上传后的数据，一般为JSON格式
	 */
	public function action_upload()
	{
		// 默认返回的信息
		$result = array(
			'error' => 1,
			'message' => __('Unknown error.'),
		);

		if (Auth::instance()->logged_in())
		{
			if ($this->request->is_post() AND isset($_FILES['imgFile']))
			{
				// 新建一个对象
				$attachment = Model::factory('Attachment');
				// 如果上传成功
				if ($attachment->upload($_FILES['imgFile'], Auth::instance()->get_user()->id))
				{
					$result = array(
						'error' => 0,
						'url' => $attachment->url(),
					);
				}
				else
				{
					$result['message'] = __('File upload error.');
				}
			}
		}
		else
		{
			$result = array(
				'error' => 1,
				'message' => __('Auth failed.'),
			);
		}

		// 输出json
		$this->response
			->headers('Content-Type', 'application/json')
			->body(json_encode($result));
	}
	
	/**
	 * 下载指定ID的文件
	 */
	public function action_down()
	{
		$id = $this->request->param('id');
		$attachment = Model::factory('Attachment')
			->where('id', '=', $id)
			->find();
		if ( ! $attachment->loaded())
		{
			HTTP::redirect( URL::base() );
		}
		
		$filename = WEB_PATH . 'media/'. $attachment->file;

		// 文件不存在
		if ( ! file_exists($filename))
		{
			exit('File not found.');
		}

		// 要添加下载记录
		$log = Model::factory('Attachment.Log')
			->down($attachment)
			->save();

		if ($attachment->name)
		{
			$this->response->send_file($filename, $attachment->name);
		}
		else
		{
			$this->response->send_file($filename);
		}
	}
}
