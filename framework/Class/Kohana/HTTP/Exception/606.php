<?php defined('SYS_PATH') OR die('No direct script access.');
/**
 * SAE中定义的606错误 invalid_host	_FetchUrl_对应的服务器不可达或者是一个私网地址
 */
class Kohana_HTTP_Exception_606 extends HTTP_Exception {

	/**
	 * @var   integer    HTTP 505 HTTP Version Not Supported
	 */
	protected $_code = 606;

}