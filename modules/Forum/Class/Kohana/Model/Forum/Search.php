<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * 论坛搜索模型
 *
 * @package		Kohana/Forum
 * @category	Model
 */
class Kohana_Model_Forum_Search extends Model_Forum {

	protected $_created_column = array('column' => 'date_created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'date_updated', 'format' => TRUE);

}
