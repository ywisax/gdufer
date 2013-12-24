<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 微博上传图片
 *
 * @package		Kohana/Weibo
 * @category	Model
 * @author		YwiSax
 */
class Kohana_Model_Weibo_Image extends Model_Weibo {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	//protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

	protected $_table_name = 'weibo_image';
	
	// 允许上传的文件类型
	public static $allow_upload_img_ext = array(
		'jpg', 'gif', 'png', 'jpeg',
	);
	
	protected $_belongs_to = array(
		'feed' => array(
			'model'=> 'Weibo_Feed',
			'far_key' => 'img_id',
		),
	);
	
	/**
	 * 上传图片文件
	 */
	public function upload($file)
	{
		// 先检查文件
		if (
			! Upload::valid($file) OR
			! Upload::not_empty($file) OR
			! Upload::type($file, Model_Weibo_Image::$allow_upload_img_ext)
		)
		{
			//exit('File data error.');
			return FALSE;
		}
		
		$upload_directory = WEB_PATH . 'media' . DIRECTORY_SEPARATOR;
		$sub_directory = 'upload/'
			. date('Y'). '/'
			. date('m'). '/'
			. date('d') . '/';
		$directory = $upload_directory . $sub_directory;
		if ( ! IN_SAE)
		{
			Helper_Directory::create($directory, TRUE);
		}
		$ext = substr(strrchr($file['name'], '.'), 1);
		$filename = Helper_Text::random('alnum', 20) . '.' . $ext;
		$upload_file = Upload::save($file, $filename, $directory);
		
		if ($upload_file)
		{
			// 此时的upload_file是文件的完整路径，要处理下
			if ( ! IN_SAE)
			{
				$upload_file = str_replace(realpath($upload_directory), '', $upload_file);
				//$upload_file = str_replace(realpath(WEB_PATH), '', $upload_file);
				$upload_file = str_replace(DIRECTORY_SEPARATOR, '/', $upload_file);
				$upload_file = trim($upload_file, '/');
			}
			
			// 保存路径到数据库
			$this->poster_id = Session::instance()->get('uid');
			$this->poster_name = Session::instance()->get('screen_name');
			$this->filename = $file['name']; // 原始文件名
			$this->filepath = $upload_file;
			$this->save();
			return $this;
		}
		else
		{
			return FALSE;
		}
	}
}
