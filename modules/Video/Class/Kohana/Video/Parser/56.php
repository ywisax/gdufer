<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 56.com在线视频解析类
 *
 * @package    Kohana/Video
 * @category   Parser
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Video_Parser_56 extends Video_Parser {

	const VID_RULE = '/^[0-9a-z_-]+$/i';
	
	const URL_RULE_1 = '/^http\:\/\/www\.56\.com\/[0-9a-z]+\/v_([0-9a-z_-]+)/i';
	const URL_RULE_2 = '/^http\:\/\/player\.56\.com\/v_([0-9a-z_-]+)/i';
	const URL_RULE_3 = '/^http\:\/\/www\.56\.com\/[0-9a-z]+\/play_album-aid-\d+_vid-([0-9a-z_-]+)/i';
	
	const JSON_CALLBACK = 'http://vxml.56.com/json/%s/?src=out';
	
	const VIDEO_URL = 'http://www.56.com/u/v_%s.html?ref=gdufer.com';
	const SWF_URL = 'http://player.56.com/v_%s.swf?ref=gdufer';

	/** 
	* 56视频解析类
	*
	* [!!]一些很老的视频，某些没大图
	*
	* @param  string  视频ID或地址
	* @return  bool   解析失败时
	* @return  array  返回解析后的数据
	**/
	public static function process($vid)
	{
		if ( ! $vid)
		{
			return FALSE;
		}
		if ( ! preg_match(Video_Parser_56::VID_RULE, $vid))
		{
			if (
				! preg_match(Video_Parser_56::URL_RULE_1, $vid, $match)
				AND ! preg_match(Video_Parser_56::URL_RULE_2, $vid, $match )
				AND ! preg_match(Video_Parser_56::URL_RULE_3, $vid, $match )
			)
			{
				return FALSE;
			}
			$vid = $match[1];
		}
		if ( ! $json = Video_Parser::url(sprintf(Video_Parser_56::JSON_CALLBACK, $vid)))
		{
			return FALSE;
		}
		if ( ! $json = @json_decode($json, TRUE))
		{
			return FALSE;
		}
		if ( empty($json['info']['img']))
		{
			return FALSE;
		}
		$json = $json['info'];
		$r['vid'] = $json['textid'];
		$r['url'] = sprintf(Video_Parser_56::VIDEO_URL, $json['textid']);
		$r['swf'] = sprintf(Video_Parser_56::SWF_URL, $json['textid']);
		$r['title'] = $json['Subject'];
		$r['img']['large'] = $json['bimg'];
		$r['img']['small'] = $json['img'];
		$r['time'] = $json['duration']/1000;
		$r['tag'] = empty($json['tag']) ? array() : Video_Parser::array_unempty(explode(',', $json['tag'])); 
		return $r;
	}
}

