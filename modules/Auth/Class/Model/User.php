<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * XunSec User Model
 *
 * @package		XunSec
 * @category	Model
 * @copyright	YwiSax
 */
class Model_User extends Model_Auth_User {

	// Relationships
	protected $_has_many = array(
		// forum
		'topics' 	=>	array(
			'model' 	=> 'Forum_Topic'
		),
		'replies'		=>	array(
			'model' 	=> 'Forum_Reply'
		),
		'friends' 	=>	array(
			'model'		=> 'User'
		),
		// auth
		'user_tokens' => array(
			'model' 	=> 'User_Token'
		),
		'roles' 	=> array(
			'model' 	=> 'Role', 
			'through' 	=> 'role_user'
		),
	);

	// Auto-update columns for creation and updates
	protected $_created_column = array(
		'column' => 'created',
		'format' => TRUE,
	);
	
	// 唯一字段
	protected $_uneditable_columns = array(
		'username', // 用户名不可编辑
		'email', // email不可编辑
	);
	
	/**
	 * 自定义的过滤规则
	 */
	public function filters()
	{
		return array(
			'username' => array(
				array('trim'),
				array('strip_tags'),
			),
			'password' => array(
				array(array(Auth::instance(), 'hash'))
			),
			'location' => array(
				array('trim'),
				array('strip_tags'),
			),
			'realname' => array(
				array('trim'),
				array('strip_tags'),
			),
			'telephone' => array(
				array('trim'),
				array('strip_tags'),
			),
			'stuno' => array(
				array('trim'),
				array('strip_tags'),
			),
			'address' => array(
				array('trim'),
				array('strip_tags'),
			),
		);
	}
	
	/**
	 * 扩充的校验规则
	 */
	public function rules()
	{
		$custom_rules = $parent_rules = parent::rules();
		
		$custom_rules['username'] = array(
			array('not_empty'),
			array('min_length', array(':value', 2)),
			array('max_length', array(':value', 16)),
			array(array($this, 'unique'), array('username', ':value')),
		);
		$custom_rules['qq'] = array(
			array('numeric'),
			array('min_length', array(':value', 5)),
			array('max_length', array(':value', 15)),
		);
		$custom_rules['stuno'] = array(
			array('alpha_numeric'),
			array('min_length', array(':value', 5)),
			array('max_length', array(':value', 12)),
		);
		
		return $custom_rules;
	}

	/**
	 * Check user role by role name
	 *
	 * @param  $role_name
	 * @return bool
	 */
	public function has_role($role_name)
	{
		return $this->has('roles', ORM::factory('Role', array('name' => $role_name)));
	}

	/**
	 * 初始化一个用户，通常用于一个用户在注册后，给他分配权限和头像等
	 */
	public function init_user()
	{
		if ( ! $this->loaded())
		{
			throw new Kohana_Exception('This method should be called after load the record.');
		}
		// 添加登陆角色权限
		$this->add('roles', ORM::factory('Role', array('name' => 'login')));
		// 生成默认头像
		Model_User::generate_default_avatar($this->id);
	}

	/**
	 * 返回头像html，带连接的
	 */
	public function avatar_link($user_id = NULL)
	{
		// 用户
		$user_id = ($user_id === NULL)
			? $this->id
			: (int) $user_id;
		return HTML::anchor(
			Route::url('auth-action', array('action' => 'view', 'id' => $user_id)),
			HTML::image( Media::url( Model_User::avatar_path($user_id) ) ),
			array(
				'class' => 'avatar-block popuser',
				'data-uid' => $user_id,
			)
		);
	}
	
	/**
	 * 返回用户的头像图片
	 */
	public function avatar_img()
	{
		return Media::url( Model_User::avatar_path($this->id) );
	}
	
	/**
	 * 保存新头像
	 */
	public function new_avatar($file, $name = 'new_avatar', $resize = FALSE)
	{
		$validation = Validation::factory($file)
			->rule($name, array('Upload', 'valid'));
		// 检测不通过
		if ( ! $validation->check())
		{
			return FALSE;
		}
		
		$avatar_path = Model_User::avatar_path($this->id);
		if (IN_SAE)
		{
			$avatar_path = Kohana::config('Gdufer.sae_media_prefix').$avatar_path;
		}
		else
		{
			//$avatar_path = Media::path($avatar_path);
			$avatar_path = WEB_PATH . 'media/' . $avatar_path;
		}

		if (IN_SAE)
		{
			copy($file[$name]['tmp_name'], $avatar_path);
		}
		else
		{
			$avatar_dir = dirname($avatar_path);
			$avatar_name = basename($avatar_path);
			// 保存上传文件
			if ( ! Upload::save($file[$name], $avatar_name, $avatar_dir))
			{
				//echo "Upload error: $avatar_path";
				// 可能没更新文件
				return FALSE;
			}
		}
		
		// 调整尺寸
		if ($resize !== FALSE)
		{
			$resize = (int) $resize;
			
			if (IN_SAE)
			{
				$img = new SaeImage();
				$img->setData( file_get_contents($avatar_path) );
				$img->resize($resize, $resize);
				$new_data = $img->exec();
				file_put_contents($avatar_path, $new_data);
			}
			else
			{
				Image::factory($avatar_path)
					->resize($resize, $resize)
					->save();
			}
		}

		// 获取完整的URL
		$avatar_url = sprintf('http://gdufcdn.sinaapp.com/index.php?force=true&q=media/%s', Model_User::avatar_path($this->id));
		// 读取一次就刷新啦
		file_get_contents($avatar_url);
		return Model_User::avatar_path($this->id);
	}
	
	/**
	 * 获取指定用户的头像路径
	 */
	public static function avatar_path($uid)
	{
		$uid = $suid = abs(intval($uid));
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		return "avatar/{$dir1}/{$dir2}/{$dir3}/{$suid}.gif";
	}
	
	/**
	 * 生成默认的头像
	 */
	public static function generate_default_avatar($uid)
	{
		$default_avatar = NULL;
		
		// 循环直到查找到一个有效的头像
		while ( ! $default_avatar)
		{
			$default_avatar_number = rand(1, 271);
			if (is_file(WEB_PATH . 'media/avatar/default/' . $default_avatar_number . '.gif'))
			{
				$default_avatar = WEB_PATH . 'media/avatar/default/' . $default_avatar_number . '.gif';
			}
		}

		$new_avatar = WEB_PATH . 'media/' . Model_User::avatar_path($uid);

		//echo "new_avatar: $new_avatar<br />";
		Helper_File::copy($default_avatar, $new_avatar);
	}
	
	/**
	 * 获取指定数目的最新用户
	 */
	public static function newest_user($limit = 1)
	{
		$limit = (int) $limit;
		return Model::factory('User')
			->order_by('id', 'DESC')
			->limit($limit)
			->find_all();
	}
	
	/**
	 *
	 */
	public function link($action = 'view')
	{
		return Route::url('auth-action', array(
			'action' => $action,
			'id' => $this->id,
		));
	}
} // End User
