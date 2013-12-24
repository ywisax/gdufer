<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 优酷在线视频解析类
 *
 * @package    Kohana/Video
 * @category   Parser
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Video_Parser_Youku extends Video_Parser {

	// 播放地址匹配规则
	const URL_RULE_1 = '/^http\:\/\/v\.youku\.com\/v_show\/id_([0-9a-z_-]+)/i';
	const URL_RULE_2 = '/^http\:\/\/player\.youku\.com\/player\.php[0-9a-z\/_-]*\/sid\/([0-9a-z_-]+)/i';
	
	// VID匹配规则
	const VID_RULE = '/^[0-9a-z_-]+$/i';
	
	// PlayList地址前缀
	const PLAY_LIST_URL_PREFIX = 'http://v.youku.com/player/getPlayList/VideoIDS/';
	
	const VIDEO_URL = 'http://v.youku.com/v_show/id_%s.html?f=http://www.gdufer.com/';
	const SWF_URL = 'http://player.youku.com/player.php/sid/%s/gdufer.swf';

	/**
	*	优酷的
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
		
		// 检查是否为VID
		if ( ! preg_match(Video_Parser_Youku::VID_RULE, $vid ) )
		{
			if ( ! preg_match( Video_Parser_Youku::URL_RULE_1, $vid, $match ) && ! preg_match( Video_Parser_Youku::URL_RULE_2, $vid, $match ) )
			{
				return FALSE;
			}
			$vid = $match[1];
		}
		
		$url = Video_Parser_Youku::PLAY_LIST_URL_PREFIX . $vid;
		if ( !$json = $this->url( $url ) )
		{
			return FALSE;
		}
		if ( ! $json = @json_decode($json, TRUE))
		{
			return FALSE;
		}
		if ( empty( $json['data'][0] ) )
		{
			return FALSE;
		}
		$json = $json['data'][0];

		$r['vid'] = $json['vidEncoded'];
		$r['url'] = sprintf(Video_Parser_Youku::VIDEO_URL, $json['vidEncoded']);
		$r['swf'] = sprintf(Video_Parser_Youku::SWF_URL, $json['vidEncoded']);
		$r['title'] = $json['title'];
		$r['img']['large'] = $json['logo'];
		// 这里是额外的替换？
		$r['img']['small'] = str_replace( '.com/11', '.com/01', $json['logo'] );
		$r['time'] = $json['seconds'];
		$r['tag'] = $json['tags'];
		return $r;
	}
}
