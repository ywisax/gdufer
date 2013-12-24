<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 新浪在线视频解析类
 *
 * @package    Kohana/Video
 * @category   Parser
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Video_Parser_Sina extends Video_Parser {

	/**
	*	sina 的
	*
	*	1 参数 vid or url
	*
	*	如果直接使用vid 获取不到 url 地址
	*	
	*	返回值 false array
	**/
	public static function process($vid)
	{
		if ( !$vid )
		{
			return FALSE;
		}
		$uid = 0;
		$url = '';
		$token = '';
		if ( ! preg_match( '/^[0-9]+$/i', $vid ) ) {
			if ( preg_match( '/^http\:\/\/video\.sina\.com\.cn\/p\/news\/s\/v\/\d{4}-\d{2}-\d{2}\/\d+\.html/i', $vid, $match ) ) {
				if ( !( $html = Video_Parser::url( $vid ) ) || !preg_match( '/swfOutsideUrl\s*:\s*\'(.+?)\'\s*,/i', $html, $match ) ) {
					return FALSE;
				}
				$url = $vid;
				$vid = $match[1];
			}
			if ( preg_match( '/^http\:\/\/video\.sina\.com\.cn\/v\/b\/(\d+)-(\d+)/i', $vid, $match ) || preg_match( '/^http\:\/\/you\.video\.sina\.com\.cn\/api\/sinawebApi\/outplayrefer\.php\/vid=(\d+)_(\d+)_([0-9a-zA-Z+%]+)/i', $vid, $match ) ) {
				$vid = $match[1];
				$uid = $match[2];
				$token = empty( $match[3] ) ? '' : $match[3];
				if ( $uid != 1 ) {
					$url = 'http://video.sina.com.cn/v/b/'. $vid .'-'. $uid .'.html?ref=gdufer.com';
				}
			} else {
				return FALSE;
			}
		}
		if ( !$url && $token ) {
			$token = str_replace( '+', '%2B', $token );
			if ( $xml = Video_Parser::url( 'http://video.sina.com.cn/api/sinaVideoInfo.php?pid=1012&token=' . $token ) ) {
				$xml = Video_Parser::parse_xml( $xml );
				if ( !empty( $xml['url'] ) ) {
					$url = $xml['url'];
				}
			}
		}
		
		if ( ! $xml = Video_Parser::url('http://v.iask.com/v_play.php?vid=' . $vid))
		{
			return FALSE;
		}
		
		if ( ! $xml = Video_Parser::parse_xml($xml))
		{
			return FALSE;
		}
		
		if ( ! $img = Video_Parser::url('http://interface.video.sina.com.cn/interface/common/getVideoImage.php?vid=' . $vid))
		{
			return FALSE;
		}
		
		Video_Parser::parse_str($img, $img);
		if (empty($img['imgurl']))
		{
			return FALSE;
		}
		$r['vid'] = $xml['ext'];

		$r['url'] = $url;
		$r['swf'] = 'http://you.video.sina.com.cn/api/sinawebApi/outplayrefer.php/vid=' . $xml['ext'] . '_' . $uid . '_' . $token;
		$r['title'] = $xml['vname'];
		$r['img']['large'] = $img['imgurl'];
		$r['img']['small'] = str_replace( '2.jpg', '1.jpg', $img['imgurl'] );
		$r['time'] = $xml['timelength'] / 1000;
		$r['tag'] = empty( $xml['vtags'] ) ? array() : Video_Parser::array_unempty( explode( ' ', $xml['vtags'] ) );
		return $r;
	}
}
