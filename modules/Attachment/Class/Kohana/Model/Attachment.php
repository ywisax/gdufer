<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 附件模型。
 * 最早我以为不用考虑这个了。后来才发现自己sb了。。。
 *
 * 表结构：
 *
 * CREATE TABLE IF NOT EXISTS `xunsec_attachment` (
 *   `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '附件ID',
 *   `uid` int(10) NOT NULL COMMENT '提交者ID',
 *   `type` varchar(30) DEFAULT NULL COMMENT '附件类型',
 *   `file` varchar(200) NOT NULL COMMENT '文件路径，相对比本地的路径',
 *   `name` varchar(200) NOT NULL COMMENT '上传时使用的文件名',
 *   `ip` varchar(30) DEFAULT NULL COMMENT '提交者IP',
 *   `ua` varchar(100) DEFAULT NULL COMMENT '提交者浏览器标志',
 *   `date_created` int(10) NOT NULL COMMENT '创建日期',
 *   PRIMARY KEY (`id`)
 * ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='保存附件的表' AUTO_INCREMENT=1 ;
 *
 * 使用前记得在media目录中新建一个upload文件夹
 *
 * @package    Kohana/Attachment
 * @category   Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Model_Attachment extends ORM {

	// 默认允许上传的后缀列表
	public $allow_ext = array(
		'image' => array('jpg', 'png', 'gif'), // 图片
		'file' => array('zip', 'rar', '7z', 'gz'), // 压缩文件
		'document' => array('doc', 'docx', 'xls', 'xlsx', 'txt'), // 文档
	);

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);

	protected $_belongs_to = array(
		'poster' => array(
			'model' => 'User',
			'foreign_key' => 'uid',
		),
	);
	
	/**
	 * 校验规则
	 */
	public function rules()
	{
		return array(
			'uid' => array(
				array('not_empty'), // 发表者ID不能为空，当然可以使用0代替
			),
			'ip' => array(
				array('not_empty'), // 一定要保存上传者IP，这个比上面的UID还要重要
			),
			'ua' => array(
				array('not_empty'), // 上传浏览器UA信息，这个可以用于分析浏览器占用率等
			),
			'file' => array(
				array('not_empty'), // 文件路径这个怎么可以为空
			),
		);
	}
	
	/**
	 * 过滤规则
	 */
	public function filters()
	{
		return array(
			'uid' => array(
				array('intval'), // 强制转换为数值
			),
			'type' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'), // 去除危险标签
			),
			'ip' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'), // 去除危险标签
			),
			'ua' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'), // 去除危险标签
			),
			'name' => array(
				array('trim'), // 去除空余空格
				array('strip_tags'), // 去除危险标签
			),
		);
	}
	
	/**
	 * 生成一个随机文件名
	 */
	public static function random_filename($ext)
	{
		return Helper_Text::random('alnum', 20) . '.' . $ext;
	}
	
	/**
	 * 获取上传目录（相对MEDIA目录）
	 */
	public static function sub_directory()
	{
		$upload_directory = WEB_PATH . 'media' . DIRECTORY_SEPARATOR;
		$sub_directory = 'upload/'
			. date('Y'). '/'
			. date('m'). '/'
			. date('d') . '/';
		if ( ! IN_SAE)
		{
			$directory = $upload_directory . $sub_directory;
			Helper_Directory::create($directory, TRUE);
		}
		return $sub_directory;
	}
	
	/**
	 * 上传新文件
	 */
	public function upload($file, $uid = NULL)
	{
		$allow_ext = array();
		foreach ($this->allow_ext AS $arr)
		{
			$allow_ext = Helper_Array::merge($allow_ext, $arr);
		}

		// 检验文件是否合格
		if (
			! Upload::valid($file) OR
			! Upload::not_empty($file) OR
			! Upload::type($file, $allow_ext)
		)
		{
			//exit('File data error.');
			return FALSE;
		}
		// APP_PATH/media/upload/201301/
		$upload_directory = WEB_PATH . 'media' . DIRECTORY_SEPARATOR;
		$sub_directory = Model_Attachment::sub_directory();
		$directory = $upload_directory . $sub_directory;
		
		// 获取扩展名
		$ext = substr(strrchr($file['name'], '.'), 1);

		// 根据扩展名来判断文件类型啊
		$type = 'unknown';
		foreach ($this->allow_ext AS $_type => $_exts)
		{
			// 如果在这个分组中，那就使用这个分组的名来作为分类
			if (in_array($ext, $_exts))
			{
				$type = $_type;
			}
		}
		
		// 随机文件名
		$filename = Model_Attachment::random_filename($ext);

		// 调用Upload类来保存文件
		$upload_file = Upload::save($file, $filename, $directory);
		
		if ($upload_file)
		{
			//exit($upload_file);
			// 此时的upload_file是文件的完整路径，要处理下
			if ( ! IN_SAE)
			{
				$upload_file = str_replace(realpath($upload_directory), '', $upload_file);
				$upload_file = str_replace(DIRECTORY_SEPARATOR, '/', $upload_file);
				$upload_file = trim($upload_file, '/');
			}
			
			// 这里还要记录下文件是谁上传的
			// $attachment = Model::factory('Attachment');
			// $attachment->uid = $uid;
			// $attachment->type = $type;
			// $attachment->file = $upload_file;
			// $attachment->name = $file['name'];
			// $attachment->ip = Request::$client_ip;
			// $attachment->ua = Request::$user_agent;
			// $attachment->save();
			
			$this->uid = $uid;
			$this->type = $type;
			$this->file = $upload_file;
			$this->name = $file['name'];
			$this->ip = Request::$client_ip;
			$this->ua = Request::$user_agent;
			$this->save();
			
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * 返回附件的URL
	 */
	public function url()
	{
		if ( ! $this->loaded())
		{
			throw new Attachment_Exception('Attachment model need to be loaded at first.');
		}

		$url = '';
		// 根据不同类型文件，返回不同的URL
		switch ($this->type)
		{
			case 'image':
				// 如果是图片，那么就直接返回图片地址算了
				$url = Media::url($this->file);
				break;
			case 'file':
				$url = Route::url('attachment-down', array(
					'id' => $this->id,
				));
			case 'doc':
				$url = Route::url('attachment-down', array(
					'id' => $this->id,
				));
			default:
				$url = Route::url('attachment-down', array(
					'id' => $this->id,
				));
				break;
		}

		return $url;
	}
}
