<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * ku6.com在线视频解析类
 *
 * @package    Kohana/Video
 * @category   Parser
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Video_Parser_Ku6 extends Video_Parser {

	const VID_RULE = '/^[0-9a-z_-]+\.{0,2}$/i';
	const VID_REGEX_CALLBACK = '/^([0-9a-z_-]+)\.*$/i';
	const VID_REGEX_REPLACE = '$1..';

	const URL_RULE_1 = '/^http\:\/\/v\.ku6\.com\/show\/([0-9a-z_-]+)/i';
	const URL_RULE_2 = '/^http\:\/\/player\.ku6\.com\/refer\/([0-9a-z_-]+)/i';
	const URL_RULE_3 = '/^http\:\/\/v\.ku6\.com\/special\/show_\d+\/([0-9a-z_-]+)/i';

	const JSON_CALLBACK = 'http://v.ku6.com/fetchVideo4Player/%s.html';

	const VIDEO_URL = 'http://v.ku6.com/show/%s.html?ref=http://www.gdufer.com/';
	const SWF_URL = 'http://player.ku6.com/refer/%s/gdufer.swf';

	/**
	*	酷 6 的
	*
	*	1 参数 vid or url
	*
	*	很老的视频某些 没大图
	*	返回值 false array
	**/
	public static function process($vid)
	{
		if ( ! $vid)
		{
			return FALSE;
		}
		if ( ! preg_match(Video_Parser_Ku6::VID_RULE, $vid))
		{
			if (
				! preg_match(Video_Parser_Ku6::URL_RULE_1, $vid, $match)
				AND ! preg_match(Video_Parser_Ku6::URL_RULE_2, $vid, $match)
				AND ! preg_match(Video_Parser_Ku6::URL_RULE_3, $vid, $match ) 
			)
			{
				return FALSE;
			}
			$vid = $match[1];
		}
		$vid = preg_replace(Video_Parser_Ku6::VID_REGEX_CALLBACK, Video_Parser_Ku6::VID_REGEX_REPLACE, $vid );
		if ( ! $json = Video_Parser::url(sprintf(Video_Parser_Ku6::JSON_CALLBACK, $vid)))
		{
			return FALSE;
		}
		if ( ! $json = @json_decode($json, TRUE))
		{
			return FALSE;
		}
		if (empty( $json['data']['picpath']))
		{
			return FALSE;
		}
		
		$json = $json['data'];
		$json['vtime'] = explode(',', $json['vtime'] );
		$r['vid'] = $vid;
		$r['url'] = sprintf(Video_Parser_Ku6::VIDEO_URL, $vid);
		$r['swf'] = sprintf(Video_Parser_Ku6::SWF_URL, $vid);
		$r['title'] = $json['t'];
		$r['img']['large'] = $json['bigpicpath'];
		$r['img']['small'] = $json['picpath'];
		$r['time'] = reset($json['vtime']);
		$r['tag'] = empty($json['tag']) ? array() : Video_Parser::array_unempty(explode( ' ', $json['tag']));
		return $r;
	}

}
