<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 在线视频解析类
 *
 * @package    Kohana/Video
 * @category   Parser
 * @author     XunSec
 * @copyright  (c) 2008-2012 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_Video_Parser {

	public static $_instance = NULL;
	
	// 超时时间
	const TIMEOUT = 5;
	
	const URL_PARSERR_PATTERN = '/.*(?:$|\.)(\w+(?:\.(?:com|net|org|co|info)){0,1}\.[a-z]+)$/iU';
	const URL_PARSERR_REPLACE = '$1';
	
	/**
	 *	解析视频
	 *
	 *	1 参数 url 地址
	 *
	 *	返回值 数组 or false
	 */
	public static function parse( $url )
	{
		if ( ! Video_Parser::$_instance)
		{
			Video_Parser::$_instance = new Video_Parser;
		}

		$arr = parse_url( $url );
		if ( empty( $arr['host'] ) )
		{
			return FALSE;
		}

		$host = strtolower(preg_replace(Video_Parser::URL_PARSERR_PATTERN, Video_Parser::URL_PARSERR_REPLACE, $arr['host']));
		if ( $host == 'youku.com' )
		{
			return Video_Parser_Youku::process($url);
		}
		
		if ( $host == 'tudou.com' )
		{
			return Video_Parser_Tudou::process( $url );
		}
		
		if ( $host == 'ku6.com' )
		{
			return Video_Parser_Ku6::process($url);
		}
		
		if ( $host == '56.com' )
		{
			return Video_Parser_56::process($url);
		}
		
		if ( $host == 'sina.com.cn' )
		{
			return Video_Parser_Sina::process($url);
		}
		
		if ( $host == 'qq.com' )
		{
			return Video_Parser_QQ::process($url);
		}
		
		if ( $host == 'letv.com' )
		{
			return Video_Parser_Letv::process( $url );
		}
		
		if ( $host == 'sohu.com' )
		{
			return Video_Parser_Sohu::process( $url );
		}

		// 不要返回FALSE，因为可能还有其他用，直接返回一个数组包含原地址即可
		//return FALSE;
		return array(
			'vid' => '',
			'url' => $url,
			'swf' => $url,
			'title' => '',
			'img' => array(
				'small' => '',
				'large' => '',
			),
			'time' => '',
			'tag' => '',
		);
	}
	
	/**
	 * 处理解析URL或者VID
	 */
	public static function process($vid)
	{
	}

	/**
	*	打开 url
	*
	*	1 参数 url 地址
	*	2 参数 header 引用
	*
	*	返回值 字符串
	**/
	function url( $url = '',  &$header = array() ) {
		$timeout = Video_Parser::TIMEOUT;
		$accept = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1478.0 Safari/537.36';
		$content = '';

		if (function_exists('curl_init'))
		{
			// curl 的
			$curl = curl_init( $url );
			curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 86400) ;	
			curl_setopt($curl, CURLOPT_DNS_USE_GLOBAL_CACHE, TRUE) ;	
			curl_setopt($curl, CURLOPT_BINARYTRANSFER, TRUE);		
			curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
			curl_setopt($curl, CURLOPT_HEADER, TRUE);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_USERAGENT, $accept );
			curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
			$content = curl_exec ( $curl );
			curl_close( $curl );
		
		}
		elseif (function_exists('file_get_contents'))
		{
			
			// file_get_contents
			$head[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
			$head[] = "User-Agent: $accept";
			$head[] = "Accept-Language: zh-CN,zh;q=0.5";
			$head = implode( "\r\n", $head ). "\r\n\r\n";
			
			$context['http'] = array ( 
				'method' => "GET" ,  
				'header' => $head,
				'timeout' => $timeout,
			);   
			
			$content = @file_get_contents($url, FALSE , stream_context_create($context));
			if ($gzip = Video_Parser::gzip($content))
			{
				$content = $gzip;
			}
			$content = implode( "\r\n", $http_response_header ). "\r\n\r\n" . $content;
			
		}
		elseif (function_exists('fsockopen') || function_exists('pfsockopen'))
		{
			// fsockopen or pfsockopen
			$url = parse_url( $url );
			if ( empty( $url['host'] ) ) {
				return FALSE;
			}
			$url['port'] = empty( $url['port'] ) ? 80 : $url['port'];
			
			$host = $url['host'];
			$host .= $url['port'] == 80 ? '' : ':'. $port;
			
			$get = '';
			$get .= empty( $url['path'] ) ? '/' : $url['path'];
			$get .= empty( $url['query'] ) ? '' : '?'. $url['query'];
			
			
			$head[] = "GET $get HTTP/1.1";
			$head[] = "Host: $host";
			$head[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
			$head[] = "User-Agent: $accept";
			$head[] = "Accept-Language: zh-CN,zh;q=0.5";
			$head[] = "Connection: Close";
			$head = implode( "\r\n", $head ). "\r\n\r\n";
 			
			$function = function_exists('fsockopen') ? 'fsockopen' : 'pfsockopen';
			if ( ! $fp = @$function( $url['host'], $url['port'], $errno, $errstr, $timeout ) )
			{
				return FALSE;
			}
			
			if( !fputs( $fp, $head ) ) {
				return FALSE;
			}
			
			while ( !feof( $fp ) ) {
				$content .= fgets( $fp, 1024 );
			}
			fclose( $fp );
			
			if ( $gzip = Video_Parser::gzip( $content ) ) {
				$content = $gzip;
			}
			
			$content = str_replace( "\r\n", "\n", $content );
			$content = explode( "\n\n", $content, 2 );
			
			if ( !empty( $content[1] ) && !strpos( $content[0], "\nContent-Length:" ) ) {
				$content[1] = preg_replace( '/^[0-9a-z\r\n]*(.+?)[0-9\r\n]*$/i', '$1', $content[1] );
			}
			$content = implode( "\n\n", $content );
		}
		
		// 分割 header  body
		$content = str_replace( "\r\n", "\n", $content );
		$content = explode( "\n\n", $content, 2 );	
		
		// 解析 header
		$header = array();
		foreach ( explode( "\n", $content[0] ) AS $k => $v ) {
			if ($v)
			{
				$v = explode(':', $v, 2);
				if (isset($v[1]))
				{					
					if (substr($v[1], 0, 1) == ' ')
					{
						$v[1] = substr( $v[1], 1 );
					}
					$header[trim($v[0])] = $v[1];
				}
				elseif ( empty( $r['status'] ) && preg_match( '/^(HTTP|GET|POST)/', $v[0] ) )
				{
					$header['status'] = $v[0];
				}
				else
				{
					$header[] = $v[0];
				}
			}
		}
		
		
		$body = empty( $content[1] ) ? '' : $content[1];
		return $body;
	}


	/**
	*	gzip 解压缩
	*
	*	1 参数 data
	*
	*	返回值 false or string
	**/
	public static function gzip($data)
	{
        $len = strlen ( $data );
        if ($len < 18 || strcmp ( substr ( $data, 0, 2 ), "\x1f\x8b" )) {
            return null; // Not GZIP format (See RFC 1952) 
        }
        $method = ord ( substr ( $data, 2, 1 ) ); // Compression method 
        $flags = ord ( substr ( $data, 3, 1 ) ); // Flags 
        if ($flags & 31 != $flags) {
            // Reserved bits are set -- NOT ALLOWED by RFC 1952 
            return null;
        }
        // NOTE: $mtime may be negative (PHP integer limitations) 
        $mtime = unpack ( "V", substr ( $data, 4, 4 ) );
        $mtime = $mtime [1];
        $xfl = substr ( $data, 8, 1 );
        $os = substr ( $data, 8, 1 );
        $headerlen = 10;
        $extralen = 0;
        $extra = '';
        if ($flags & 4) {
            // 2-byte length prefixed EXTRA data in header 
            if ($len - $headerlen - 2 < 8) {
                return FALSE; // Invalid format 
            }
            $extralen = unpack ( "v", substr ( $data, 8, 2 ) );
            $extralen = $extralen [1];
            if ($len - $headerlen - 2 - $extralen < 8) {
                return FALSE; // Invalid format 
            }
            $extra = substr ( $data, 10, $extralen );
            $headerlen += 2 + $extralen;
        }
     
        $filenamelen = 0;
        $filename = '';
        if ($flags & 8) {
            // C-style string file NAME data in header 
            if ($len - $headerlen - 1 < 8) {
                return FALSE; // Invalid format 
            }
            $filenamelen = strpos ( substr ( $data, 8 + $extralen ), chr ( 0 ) );
            if ($filenamelen === FALSE || $len - $headerlen - $filenamelen - 1 < 8) {
                return FALSE; // Invalid format 
            }
            $filename = substr ( $data, $headerlen, $filenamelen );
            $headerlen += $filenamelen + 1;
        }
     
        $commentlen = 0;
        $comment = '';
        if ($flags & 16) {
            // C-style string COMMENT data in header 
            if ($len - $headerlen - 1 < 8) {
                return FALSE; // Invalid format 
            }
            $commentlen = strpos ( substr ( $data, 8 + $extralen + $filenamelen ), chr ( 0 ) );
            if ($commentlen === FALSE || $len - $headerlen - $commentlen - 1 < 8) {
                return FALSE; // Invalid header format 
            }
            $comment = substr ( $data, $headerlen, $commentlen );
            $headerlen += $commentlen + 1;
        }
     
        $headercrc = '';
        if ($flags & 1)
		{
            // 2-bytes (lowest order) of CRC32 on header present 
            if ($len - $headerlen - 2 < 8) {
                return FALSE; // Invalid format 
            }
            $calccrc = crc32 ( substr ( $data, 0, $headerlen ) ) & 0xffff;
            $headercrc = unpack ( "v", substr ( $data, $headerlen, 2 ) );
            $headercrc = $headercrc [1];
            if ($headercrc != $calccrc)
			{
                return FALSE; // Bad header CRC 
            }
            $headerlen += 2;
        }
     
        // GZIP FOOTER - These be negative due to PHP's limitations 
        $datacrc = unpack ( "V", substr ( $data, - 8, 4 ) );
        $datacrc = $datacrc [1];
        $isize = unpack ( "V", substr ( $data, - 4 ) );
        $isize = $isize [1];
     
        // Perform the decompression: 
        $bodylen = $len - $headerlen - 8;
        if ($bodylen < 1) {
            // This should never happen - IMPLEMENTATION BUG! 
            return null;
        }
        $body = substr ( $data, $headerlen, $bodylen );
        $data = '';
        if ($bodylen > 0) {
            switch ($method) {
                case 8 :
                    // Currently the only supported compression method: 
                    $data = gzinflate ( $body );
                    break;
                default :
                    // Unknown compression method 
                    return FALSE;
            }
        }
		else
		{
            //...
        }
     
        if ($isize != strlen ( $data ) || crc32 ( $data ) != $datacrc) {
            // Bad format!  Length or CRC doesn't match! 
            return FALSE;
        }
        return $data;
    }
	
	const XML_CDATA_REGEX = "/\<(?<tag>[a-z]+)\>\s*\<\!\[CDATA\s*\[(.*)\]\]\>\s*\<\/\k<tag>\>/iU";
	
	/**
	 * 解析数组
	 *
	 * @param  string  要解析成数组的XML
	 * @return array
	 **/
	public static function parse_xml( $xml )
	{
		if (preg_match_all(Video_Parser::XML_CDATA_REGEX, $xml, $matches))
		{
			$find = $replace = array();
			foreach ( $matches[0] AS $k => $v )
			{
				$find[] = $v;
				$replace[] = '<'. $matches['tag'][$k]  .'>' .htmlspecialchars($matches[2][$k] , ENT_QUOTES). '</' . $matches['tag'][$k].'>';
			}
			 
			$xml = str_replace($find, $replace, $xml);
		}
		if( ! $xml = @simplexml_load_string($xml))
		{
			return FALSE;
		}
		return Helper_Array::to_array($xml);
	}
	
	/**
	*	解析数组
	*
	*	1 参数 str
	*	2 参数 arr 引用
	*
	*	返回值 无
	**/
	public static function parse_str($str, &$arr)
	{
		parse_str($str, $arr);
		if (get_magic_quotes_gpc())
		{
			$arr = Helper_Array::stripslashes($arr);
		}
	}
	
		
	/**
	*	删除 数组中 的空值
	*
	*	1 参数 数组
	*	2 参数 是否回调删除多维数组
	*
	*	返回值 数组
	**/
	public static function array_unempty($a = array(), $call = FALSE)
	{
		foreach ($a AS $k => $v)
		{
			if ($call && is_array($a) && $a)
			{
				 $a[$k] = Video_Parser::array_unempty($a, $call);
			}
			if (empty($v))
			{
				unset($a[$k]);
			}
		}
		return $a;
	}
}
