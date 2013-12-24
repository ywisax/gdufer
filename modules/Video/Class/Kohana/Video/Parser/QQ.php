<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * QQ在线视频解析类
 *
 * @package    Kohana/Video
 * @category   Parser
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Video_Parser_QQ extends Video_Parser {

	/**
	*	QQ 的
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
		
		if ( ! preg_match( '/^[0-9a-z_-]+$/i', $vid ))
		{
			if ( ! preg_match( '/^http\:\/\/v\.qq\.com\/cover\/[0-9a-z_-]{1}\/[0-9a-z_-]+\.html\?[0-9a-z&=_-]*vid=([0-9a-z_-]+)/i', $vid, $match ) && !preg_match( '/^http\:\/\/v\.qq\.com\/cover\/[0-9a-z_-]{1}\/[0-9a-z_-]+\/([0-9a-z_-]+)\.html/i', $vid, $match ) && !preg_match( '/^http\:\/\/static\.video\.qq\.com\/TPout\.swf\?[0-9a-z&=_-]*vid=(\w+)/i', $vid, $match ) )
			{
				return FALSE;
			}
			$vid = $match[1];
		}
		
		if( ! $xml = Video_Parser::url( 'http://vv.video.qq.com/getinfo?otype=xml&vids=' . $vid))
		{
			return FALSE;
		}
		if( ! $xml = Video_Parser::parse_xml($xml))
		{
			return FALSE;
		}
		if (empty($xml['vl']['vi']))
		{
			return FALSE;
		}
		$xml = $xml['vl']['vi'];
		
		
		$num = 0xFFFFFFFF + 1;
		$m = 10000 * 10000;
		$res = 0;
		
		$i = 0;
		while ( $i < strlen ( $vid ) )
		{
			$temp = ord(substr($vid, $i, 1));
			$res = $res * 32 + $res + $temp;
			while ($res >= $num)
			{
				$res -= $num;
			}
			$i++;
		}
		while ($res >= $m)
		{
			$res -= $m;
		}
		$r['vid'] = $xml['vid'];
		$r['url'] = 'http://v.qq.com/page/t/u/h/'. $xml['vid'] .'.html?ref=gdufer.com';
		$r['swf'] = 'http://static.video.qq.com/TPout.swf?vid='. $xml['vid'] .'&ref=gdufer.com';
		$r['title'] = $xml['ti'];
		$r['img']['large'] = 'http://vpic.video.qq.com/'. $res .'/'. $xml['vid'] .'.png';
		$r['img']['small'] = 'http://vpic.video.qq.com/'. $res .'/'. $xml['vid'] .'_160_90_2.jpg';
		$r['time'] = $xml['td'];
		$r['tag'] = array();
		return $r ;
	}
}
