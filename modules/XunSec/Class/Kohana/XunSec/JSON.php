<?php defined('SYS_PATH') or die('No direct script access.');
/**
 * XunSec自用的JSON协议
 *
 * @package    Kohana/XunSec
 * @category   Base
 * @author     YwiSax
 * @copyright  (c) 2009 XunSec Team
 * @license    http://www.xunsec.com/license
 */
class Kohana_XunSec_JSON {

	public $code   = 200;
	public $msg    = '';
	public $body   = '';
	
	public static $msg_map = array(
		'200' => 'OK',
		'400' => 'Missing Parameters or Bad Request',
		'401' => 'Unauthorized',            //401 未登录或者登录失败
		'403' => 'Forbidden',               //当前用户已经登录或已提供uid，但禁止获取此内容。
		'404' => 'Not Found',               //404 请求的内容不存在
		'406' => 'Not Acceptable',          //但无法使用请求的内容特性来响应请求
		'407' => 'Second Auth Required',    //比如支付密码
		'408' => 'Timeout',                 //任务超时
		'409' => 'Conflict',                //客户端要求写入数据，但数据已存在且不能覆盖
		'412' => 'Precondition Failed',     //未满足前提条件
		'413' => 'Request Too Large',       //上传文件太大
		'414' => 'Data Too Long',           //输入的数据太长
		'415' => 'Unsupported Type',        //上传文件格式不能被接受
		'421' => 'Too many connections',    //当前IP 地址请求数量太多，怀疑受到攻击
		'423' => 'Locked',                  //当前资源被锁定。比如写入文件时文件被锁定
		'424' => 'Failed Dependency',       //由于之前的某个请求发生的错误，导致当前请求失败。
		'426' => 'Upgrade Required',        //客户端的数据需要更新，App 可能需重新安装
		'500' => 'System Busy',             //遇到未知错误，或内部错误
		'503' => 'Service Unavailable',     //服务器临时错误，比如数据库负载过重
		'505' => 'Version Not Supported'    //客户端必须更新
	);

	//设置返回码和消息
	public function code($code, $msg = null)
	{
		$this->code = $code;

		if($msg == null)
		{
			$this->msg = XunSec_JSON::$msg_map[$code];
		}
		else
		{
			$this->msg = $msg;
		}
	}

	//设置数据
	public function body($key, $data)
	{
		//$this->body = new stdClass();
		$this->body->$key = $data;
	}

	public function json_pretty_print($json)
	{
		$result      = '';
		$pos         = 0;
		$strLen      = strlen($json);
		$indentStr   = '  ';
		$newLine     = "\n";
		$prevChar    = '';
		$outOfQuotes = true;

		for ($i=0; $i<=$strLen; $i++) {

			// Grab the next character in the string.
			$char = substr($json, $i, 1);

			// Are we inside a quoted string?
			if ($char == '"' && $prevChar != '\\') {
				$outOfQuotes = !$outOfQuotes;

				// If this character is the end of an element,
				// output a new line and indent the next line.
			} else if(($char == '}' || $char == ']') && $outOfQuotes) {
				$result .= $newLine;
				$pos --;
				for ($j=0; $j<$pos; $j++) {
					$result .= $indentStr;
				}
			}

			// Add the character to the result string.
			$result .= $char;

			// If the last character was the beginning of an element,
			// output a new line and indent the next line.
			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}

				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}

			$prevChar = $char;
		}

		return $result;
	}

	/**
	 * 根据对象转化为JSON字符串，但是中文直接显示出来会比较直观
	 */
	public function json_encode_unescaped_unicode($obj)
	{
		$json_str = json_encode($obj);
		//linux:UCS-2BE   windows:UCS-2LE
		$json_str = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $json_str);
		return $json_str;
	}

	/**
	 * 返回JSON序列化后的数据
	 */
	public function response()
	{
		$this->response_json();
	}

	/**
	 * JSON序列化数据
	 */
	protected function response_json()
	{
		$str = $this->json_encode_unescaped_unicode($this);
		$str = str_replace('\/','/',$str);

		return $this->json_pretty_print($str);
	}
}
