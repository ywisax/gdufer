<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * 暂时只实现简单的返回功能，以后可以实现CDN推送什么的吧
 *
 * @package		Kohana/Media
 * @category	Base
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
abstract class Kohana_Controller_Media extends Controller {

	/**
	 * 渲染和输出资源文件
	 */
	public function action_render()
	{
		$filepath = $this->request->param('filepath');
		// 查找文件路径
		$cfs_file = Media::find_file($filepath);

		// 发送文件内容
		$this->response->body(file_get_contents($cfs_file));
		$this->response->headers('Media-Generator', 'Kohana v3.3');
		$this->response->headers('Content-Type', (string) Helper_File::mime_by_ext(pathinfo($cfs_file, PATHINFO_EXTENSION)));
		$this->response->headers('Content-Length', (string) filesize($cfs_file));
		$this->response->headers('Last-Modified', (string) date('r', filemtime($cfs_file)));
	}
}
