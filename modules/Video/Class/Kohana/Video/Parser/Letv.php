<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 乐视网
 *
 * @package    Kohana/Video
 * @category   Parser
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Video_Parser_Letv extends Video_Parser {

	/**
	*	letv 的
	*
	*	1 参数 vid or url
	*
	*	返回值 false array
	**/
	public static function process( $vid )
	{
		if ( ! $vid)
		{
			return FALSE;
		}
		
		if ( !preg_match( '/^[0-9]+$/i', $vid ) ) {
			if ( !preg_match( '/^http\:\/\/www\.letv\.com\/ptv\/vplay\/(\d+)/i', $vid, $match ) && !preg_match( '/^http\:\/\/i\d+\.imgs\.letv\.com\/player\/swfPlayer\.swf\?[0-9a-z&=_-]*id=(\d+)/i', $vid, $match ) && !preg_match( '/^http\:\/\/www\.letv\.com\/player\/x(\d+)/i', $vid, $match ) ) {
				return FALSE;
			}
			$vid = $match[1];
		}
		if ( !$html = Video_Parser::url( 'http://www.letv.com/ptv/vplay/'. $vid .'.html' ) ) {
			return FALSE;
		}
		if ( !preg_match( '/\<script.*?__INFO__\s*\\=\{(.+?)\<\/script\>/is', $html, $match ) ) {
			return FALSE;
		}
		
		$html = $match[1];
		
		$r['vid'] = preg_replace( '/.+vid\s*\:\s*(\d+)\s*,.+/is', '$1', $html );
		$r['url'] = 'http://www.letv.com/ptv/vplay/' . $r['vid'] . '.html';
		$r['swf'] = 'http://i7.imgs.letv.com/player/swfPlayer.swf?id='. $r['vid'];
		$r['img']['large'] = '';
		$r['img']['small'] = preg_replace( '/^.+pic\s*\:\s*["\'](http.+?)["\']\s*,.+$/is', '$1', $html );
		$r['time'] = 0;
		$r['tag'] = array();
		return $r;
	}
}
