<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 土豆网在线视频解析类
 *
 * @package    Kohana/Video
 * @category   Parser
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Video_Parser_Tudou extends Video_Parser {

	// 匹配URL的规则
	const URL_RULE_1 = '/^http\:\/\/www\.tudou\.com\/programs\/view\/([0-9a-z_-]+)/i';
	const URL_RULE_2 = '/^http\:\/\/www\.tudou\.com\/v\/([0-9a-z_-]+)/i';
	const URL_RULE_3 = '/^http\:\/\/www\.tudou\.com\/(?:listplay|albumplay)\/[0-9a-z_-]+\/([0-9a-z_-]+)/i';
	const URL_RULE_4 = '/^http\:\/\/www\.tudou\.com\/(?:a|l)\/[0-9a-z_-]+\/.+iid\=(\d+)/i';
	
	// 视频地址
	const VIDEO_URL = 'http://www.tudou.com/v/%s/v.swf';
	// 播放地址？
	const PROGRAM_URL = 'http://www.tudou.com/programs/view/%s/?FR=http://www.gdufer.com/';
	// SWF地址
	const SWF_URL = 'http://www.tudou.com/v/%s/gdufer.swf';

	/**
	*	土豆的
	*
	*	1 参数 vid or url
	*
	*	返回值 false array
	**/
	public static function process($vid)
	{
		if ( ! $vid)
		{
			return false;
		}
		if ( ! preg_match('/^[0-9a-z_-]+$/i', $vid))
		{
			if (
				! preg_match(Video_Parser_Tudou::URL_RULE_1, $vid, $match)
				&& ! preg_match(Video_Parser_Tudou::URL_RULE_2, $vid, $match)
				&& ! preg_match(Video_Parser_Tudou::URL_RULE_3, $vid, $match)
				&& ! preg_match(Video_Parser_Tudou::URL_RULE_4 , $vid, $match)
			)
			{
				return false;
			}
			$vid = $match[1];
		}
		
		
		$url = sprintf(Video_Parser_Tudou::VIDEO_URL, $vid);
		Video_Parser::url($url, $header);
		if( empty( $header['Location'] ) ) {
			return false;
		}
		$parse = parse_url($header['Location']);
		if (empty($parse['query']))
		{
			return FALSE;
		}
		Video_Parser::parse_str($parse['query'], $arr);
		if (empty($arr['snap_pic']))
		{
			return FALSE;
		}
		$r['vid'] = $arr['code'];
		$r['url'] = sprintf(Video_Parser_Tudou::PROGRAM_URL, $arr['code']);
		$r['swf'] = sprintf(Video_Parser_Tudou::SWF_URL, $arr['code']);
		$r['title'] = $arr['title'];
		$r['img']['large'] = $arr['snap_pic'];
		$r['img']['small'] = str_replace( array( '/w.jpg', 'ykimg.com/11' ), array( '/p.jpg', 'ykimg.com/01' ), $arr['snap_pic'] );
		$r['time'] = $arr['totalTime'] / 1000;
		$r['tag'] = empty( $arr['tag'] ) || $arr['tag'] == 'null' ? array() : Video_Parser::array_unempty( explode( ',', $arr['tag'] ) );
		return $r;
	}

}
