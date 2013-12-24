<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * Guide模块基础类
 *
 * @package    Kohana/Attachment
 * @category   Base
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Attachment {

	const LOG_TYPE = 'attachment';

	/**
	 * 返回上传回调的地址
	 */
	public static function callback_url()
	{
		return Route::url('attachment-action', array('action' => 'callback'));
	}
	
	const IMG_SRC_REGEX = "/<[img|IMG].*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/";
	const IMG_DATA_URI_REGEX = '/image\/(png|gif|jpeg|bmp);(.*?),(.*)/';
	
	/**
	 * 解析那些本地啊，dataurl之类的东西
	 */
	public static function convert_local($html)
	{
		//echo strlen($html);exit;
		//echo $html;exit;
		if (preg_match_all(Attachment::IMG_SRC_REGEX, $html, $matches))
		{
			$images = array();
			//print_r($matches);
			$matches = $matches[1];
			foreach ($matches AS $src)
			{
				$images[] = Attachment::_convert_local($src);
			}

			// 替换
			if ( ! empty($images))
			{
				$html = str_replace($matches, $images, $html);
			}
		}
		else
		{
			//echo $html;
			echo 'no img ?';
		}
		//echo $html;
		//exit;
		// 直接返回html算了
		return $html;
	}

	/**
	 * 替换图片等等
	 */
	protected static function _convert_local($src)
	{
		// 如果是data-uri
		if (preg_match(Attachment::IMG_DATA_URI_REGEX, $src, $attributes))
		{
			//echo Debug::vars($attributes);
			$ext = $attributes[1];
			if ($ext == 'jpeg')
			{
				$ext = 'jpg';
			}
			
			$type = $attributes[2];
			$data = $attributes[3];
					
			$upload_directory = WEB_PATH . 'media' . DIRECTORY_SEPARATOR;
			$filename = Model_Attachment::sub_directory() . Model_Attachment::random_filename($ext);
			$full_filename = $upload_directory . $filename;
					
			// 保存base编码的
			if ($type == 'base64')
			{
				$data = base64_decode($data);
				file_put_contents($full_filename, $data);

				$attachment = Model::factory('Attachment');
				$attachment->uid = Auth::instance()->logged_in() ? Auth::instance()->get_user()->id : 0;
				$attachment->type = 'image';
				$attachment->file = $filename;
				$attachment->name = '';
				$attachment->ip = Request::$client_ip;
				$attachment->ua = Request::$user_agent;
				$attachment->save();
						
				return Media::url($filename);
			}
			// 未知编码类型？
			else
			{
				// 未知的，那就不让显示，日
				return '';
			}
		}
		else
		{
			return $src;
		}
	}
}
