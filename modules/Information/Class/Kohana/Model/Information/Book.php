<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 图书模型基类
 *
 * @package    Kohana/Information
 * @category   Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Model_Information_Book extends Model_Information {

	protected $_list_view_file = 'Information.List.Book';
	
	public $quality_selector = array(
		-1 => '略有破损',
		0 => '正常痕迹',
		1 => '基本全新',
	);

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);
	
	protected $_return_types = array(
		0 => '用户说明',
		1 => '感谢',
		2 => '吃饭',
		3 => '唱歌',
	);

	// 属于
	protected $_belongs_to = array(
		'category' => array(
			'model' => 'Information_Category',
			'foreign_key' => 'category_id',
		),
		'poster' => array(
			'model' => 'User',
			'foreign_key' => 'poster_id',
		),
	);

	/**
	 * 校验规则
	 */
	public function rules()
	{
		return array(
			'category_id' => array(
				array('not_empty'), // 分类不能为空
			),
			'book_name' => array(
				array('not_empty'), // 书名不能为空
			),
			'book_author' => array(
				array('not_empty'), // 作者不能为空
			),
			'publisher' => array(
				array('not_empty'), // 出版社不能为空
			),
			'raw_price' => array(
				array('not_empty'), // 原价必填
			),
			'description' => array(
				array('not_empty'), // 描述必填
			),
			//'image' => array(
			//	array('not_empty'), // 最好是强制上传
			//),
			'return_type' => array(
				array('not_empty'), // 回报类型必选啊
			),
			//'return_text' => array(
			//	array('not_empty'),
			//),
			'realname' => array(
				array('not_empty'), // 回报类型必选啊
			),
			'telephone' => array(
				array('not_empty'), // 回报类型必选啊
			),
			//'remark' => array(
			//	array('not_empty'), // 回报类型必选啊
			//),
		);
	}
	
	/**
	 * 过滤规则
	 */
	public function filters()
	{
		return array(
			'category_id' => array(
				array('intval'), // 强制转换
			),
			'book_name' => array(
				array('strip_tags'), // 强制过滤HTML标签
				array('trim'),
			),
			'book_author' => array(
				array('strip_tags'), // 强制过滤HTML标签
				array('trim'),
			),
			'publisher' => array(
				array('strip_tags'), // 强制过滤HTML标签
				array('trim'),
			),
			'raw_price' => array(
				array('trim'),
				array('floatval'), // 强制转换为浮点数
			),
			'description' => array(
				array('strip_tags'), // 强制过滤HTML标签
				array('trim'),
			),
			'image' => array(
				array('strip_tags'), // 强制过滤HTML标签
				array('trim'),
			),
			'return_type' => array(
				array('intval'), // 回报类型
			),
			'return_text' => array(
				array('strip_tags'), // 回报文本
				array('trim'), // 回报文本
			),
			'realname' => array(
				array('strip_tags'), // 真实姓名
				array('trim'),
			),
			'telephone' => array(
				array('strip_tags'), // 手机号码
				array('trim'),
			),
			'remark' => array(
				array('strip_tags'), // 备注
				array('trim'),
			),
		);
	}
	
	/**
	 * 当前分类模型
	 */
	public function type()
	{
		return 'book';
	}
	
	/**
	 * 返回当前记录的图片地址
	 */
	public function image_url()
	{
		return $this->image ? URL::site($this->image) : Media::url('information/img/no-book.jpg');
	}
	
	/**
	 * 自定义一个标题
	 */
	public function title()
	{
		return $this->book_name . '-广金二手书-广金教材交易-广金二手市场';
	}
	
	/**
	 * 获取回报文本
	 */
	public function return_text()
	{
		// 0的话。。
		if ($this->return_type == 0)
		{
			return $this->return_text;
		}
	}
	
	/**
	 * 上传和保存文件
	 */
	public function upload_image()
	{
		if (isset($_FILES['image']))
		{
			$attachment = Model::factory('Attachment');
			if ($attachment->upload($_FILES['image'], Auth::instance()->get_user()->id))
			{
				$this->image = $attachment->url();
			}
			else
			{
				//echo Debug::vars($attachment);
				//exit;
			}
		}
	}
	
	/**
	 * 查看指定页面
	 */
	public function action_view()
	{
		if ( ! $this->loaded())
		{
			throw new Information_Exception('This book not found.');
		}
		
		return View::factory('Information.View.Book')
			->set('model', $this);
	}
	
	/**
	 * 保存记录
	 */
	public function action_save($data)
	{
		if ( ! Auth::instance()->logged_in())
		{
			return FALSE;
		}

		$data['poster_id'] = Auth::instance()->get_user()->id;
		$data['poster_name'] = Auth::instance()->get_user()->username;
		$this->values($data);
		return $this->save();
	}

	/**
	 * 返回列表
	 */
	public function action_list($page = 1)
	{
		$page = (int) $page;
		if ($page < 1)
		{
			$page = 1;
		}
		
		$pagination_config = Kohana::config('Pagination.information.book');
		
		// 每页显示条数
		$limit = $pagination_config['items_per_page'];
		// 偏移位置
		$offset = ($page - 1) * $limit;
		
		// 处理分页
		$pagination_config['total_items'] = Model::factory('Information.Book')
			->find_all()
			->count();
		$pagination_config['items_per_page'] = $limit;
		
		$records = $this
			->order_by('date_created', 'DESC')
			->limit($limit)
			->offset($offset)
			->find_all();
		
		return $content = View::factory($this->_list_view_file)
			->set('model', $this)
			->set('records', $records)
			->set('pagination', Pagination::factory($pagination_config));
	}
	
	/**
	 * 返回搜索结果
	 */
	public function action_search($keyword)
	{
		$records = $this
			->where('book_name', 'LIKE', "%{$keyword}%")
			->order_by('date_created', 'DESC')
			->limit(20)
			->find_all();
		
		return $content = View::factory('Information.Search.Result')
			->set('model', $this)
			->set('keyword', $keyword)
			->set('records', $records);
	}

	public function values(array $values, array $expected = NULL)
	{
		// 如果有上传文件，那就上传
		$this->upload_image();
		// 这里可能会有个bug，那就是用户恶意post一个image变量过来，那就蛋疼了

		return parent::values($values, $expected);
	}
}
