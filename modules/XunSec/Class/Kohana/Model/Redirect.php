<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 跳转模型
 *
 * 表结构：
 *
 * CREATE TABLE `xunsec_redirect` (
 *   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 *   `url` varchar(255) NOT NULL,
 *   `newurl` varchar(255) NOT NULL,
 *   `type` enum('301','302') NOT NULL DEFAULT '302',
 *   `date_created` int(10) DEFAULT NULL,
 *   `date_updated` int(10) DEFAULT NULL,
 *   PRIMARY KEY (`id`)
 * ) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
 *
 * @package		XunSec
 * @category	Model
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Model_Redirect extends ORM {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

	public static $_status = array(
		301 => 'permanent',
		302 => 'temporary',
	);
	
	/**
	 * 过滤规则
	 */
	public function filters()
	{
		return array(
			'url' => array(
				array('trim'),
				array('strip_tags'),
			),
			'newurl' => array(
				array('trim'),
				array('strip_tags'),
			),
		);
	}
	
	/**
	 * 检验规则
	 */
	public function rules()
	{
		return array(
			'url' => array(
				array('not_empty'),
			),
			'newurl' => array(
				array('not_empty'),
			),
			'type' => array(
				array('not_empty'),
			),
		);
	}
	
	/**
	 * 如果查找到，那就直接跳转
	 */
	public function go()
	{
		// 要加载到记录才继续
		if ( $this->loaded())
		{
			if ($this->type == '301' || $this->type == '302')
			{
				Kohana::$log->add('INFO', __("XunSec - Redirected ':url' to ':newurl' (:type).", array(
					':url' => $this->url,
					':newurl' => $this->newurl,
					':type' => $this->type,
				))); 
				HTTP::redirect($this->newurl, $this->type);
			}
			else
			{
				Kohana::$log->add('ERROR', __("XunSec - Could not redirect ':url' to ':newurl', type: :type.", array(
					':url' => $this->url,
					':newurl' => $this->newurl,
					':type' => $this->type,
				)));
				throw new XunSec_Exception('Unknown redirect type', array(), 404);
			}
		}
	}

	/**
	 * 添加Log
	 */
	public function log()
	{
		$log = Model::factory('Redirect.Log');
		$log->redirect_id	= $this->id;
		$log->url			= $this->url;
		$log->newurl		= $this->newurl;
		$log->type			= $this->type;
		$log->poster_id		= Auth::instance()->get_user()->id;
		$log->poster_name	= Auth::instance()->get_user()->username;
		$log->save();
	}
	
	/**
	 * 创建记录的同时，插入一份到Log中去
	 */
	public function create(Validation $validation = NULL)
	{
		$result = parent::create($validation);
		if ($this->loaded())
		{
			$this->log();
			// 保存到数据库
			XunSec::log(
				XunSec::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Add a redirect, ID: :id', array(
					':id' => $this->id,
				))
			);
		}
		return $result;
	}
	
	/**
	 * 修改记录的同时，把旧的数据保存到Log中去
	 */
	public function update(Validation $validation = NULL)
	{
		if (empty($this->_changed))
		{
			// 没有东西需要更新
			return $this;
		}

		if ($this->loaded())
		{
			$this->log();

			// 保存到数据库
			XunSec::log(
				XunSec::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Update a redirect, ID: :id', array(
					':id' => $this->id,
				))
			);
		}
	
		return parent::update($validation);
	}
	
	/**
	 * 删除前保存一份到Log中去
	 */
	public function delete()
	{
		if ($this->loaded())
		{
			$this->log();
			
			// 保存到数据库
			XunSec::log(
				XunSec::LOG_TYPE,
				Auth::instance()->get_user()->id,
				Auth::instance()->get_user()->username,
				__('Delete a redirect, ID: :id', array(
					':id' => $this->id,
				))
			);
		}

		return parent::delete();
	}
}
