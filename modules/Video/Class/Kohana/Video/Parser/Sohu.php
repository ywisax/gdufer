<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 搜狐在线视频
 *
 * @package    Kohana/Video
 * @category   Parser
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Video_Parser_Sohu extends Video_Parser {

	/**
	*	sohu 的
	*
	*	1 参数 vid or url
	*
	*	返回值 false array
	**/
	public static function process($vid)
	{
		if ( ! $vid)
		{
			return FALSE;
		}
		if ( ! preg_match('/^[0-9]+$/i', $vid))
		{
			if ( ! preg_match( '/^http\:\/\/my\.tv\.sohu\.com\/us\/\d+\/(\d+)/i', $vid, $match ) && !preg_match( '/^http\:\/\/my\.tv\.sohu\.com\/u\/vw\/(\d+)/i', $vid, $match ) && !preg_match( '/^http\:\/\/share\.vrs\.sohu\.com\/my\/v\.swf.*&id=(\d+)/i', $vid, $match ) && !preg_match( '/^http\:\/\/share\.vrs\.sohu\.com\/(\d+)/i', $vid, $match ) ) {
				return FALSE;
			}
			$vid = $match[1];
		}
		if ( ! $json = Video_Parser::url('http://my.tv.sohu.com/videinfo.jhtml?m=viewnew&vid=' . $vid))
		{
			return FALSE;
		}

		if ( ! $json = @json_decode($json, TRUE))
		{
			return FALSE;
		}

		if (empty($json['url']))
		{
			return FALSE;
		}
		$r['vid'] = $vid;
		$r['url'] = $json['url'] . '?ref=gdufer.com';
		$r['swf'] = 'http://share.vrs.sohu.com/my/v.swf&ref=gdufer.com&id=' . $vid;
		$r['img']['large'] = $json['data']['coverImg'];
		$r['img']['small'] = str_replace( array( 'b.jpg', '_0.jpg' ), array( '.jpg', '_1.jpg' ), $json['data']['coverImg'] );
		$r['time'] = $json['data']['totalDuration'];
		$r['tag'] = empty( $json['data']['tag'] ) ? array() : explode( ' ', $json['data']['tag'] );
		return $r;
	}
}
