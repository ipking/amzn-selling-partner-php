<?php

namespace SellingPartner\Core;


/**
 * HTTP请求基类
 */
abstract class Curl{
	const DEFAULT_TIMEOUT = 30;
	
	/**
	 * 合并保留关键字
	 * @return mixed
	 */
	private static function arrayMergeKeepKeys(){
		$arg_list = func_get_args();
		$Zoo = null;
		foreach((array)$arg_list as $arg){
			foreach((array)$arg as $K => $V){
				$Zoo[$K] = $V;
			}
		}
		return $Zoo;
	}
	
	/**
	 * 获取CURL实例
	 * @param $url
	 * @param array $curl_option
	 * @throws HttpException
	 * @return resource
	 */
	private static function getCurlInstance($url, $curl_option = array()){
		if(!$url){
			throw new HttpException('CURL URL NEEDED');
		}
		
		//use ssl
		$ssl = substr($url, 0, 8) == 'https://' ? true : false;
		
		$opt = array(
//			CURLOPT_USERAGENT      => $_SERVER['HTTP_USER_AGENT'],
			//在HTTP请求中包含一个"User-Agent: "头的字符串。
			CURLOPT_FOLLOWLOCATION => 1,
			//启用时会将服务器服务器返回的"Location: "放在header中递归的返回给服务器，使用CURLOPT_MAXREDIRS可以限定递归返回的数量。
			CURLOPT_RETURNTRANSFER => true,
			//文件流形式
		);
		
		if($ssl){
			$opt[CURLOPT_SSL_VERIFYPEER] = 0;                       //对认证证书来源的检查
			$opt[CURLOPT_SSL_VERIFYHOST] = 1;                       //从证书中检查SSL加密算法是否存在
		}
		
		//设置缺省参数
		$curl_option = self::arrayMergeKeepKeys($opt, $curl_option);
		
		$max_execution_time = ini_get('max_execution_time');
		if($max_execution_time && $curl_option[CURLOPT_TIMEOUT] && $curl_option[CURLOPT_TIMEOUT]>$max_execution_time){
			throw new HttpException('curl timeout setting larger than php.ini max_execution_time setting');
		}
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		
		$a[CURLOPT_URL] = $url;
		foreach($curl_option as $k => $val){
			if($k == 'USE_COOKIE'){
				curl_setopt($curl, CURLOPT_COOKIEJAR, $val);    //连接结束后保存cookie信息的文件。
				curl_setopt($curl, CURLOPT_COOKIEFILE, $val);   //包含cookie数据的文件名，cookie文件的格式可以是Netscape格式，或者只是纯HTTP头部信息存入文件。
			} else{
				$a[$k] = $val;
				curl_setopt($curl, $k, $val);
			}
		}
		return $curl;
	}
	
	/**
	 * CURL-get方式获取数据
	 * @param string $url URL
	 * @param array $curl_option
	 * @throws HttpException
	 * @return bool|mixed
	 */
	public static function get($url, $curl_option = array()){
		$opt = array(
			CURLOPT_TIMEOUT => self::DEFAULT_TIMEOUT,
		);
		
		$curl_option = self::arrayMergeKeepKeys($opt, $curl_option);
		$curl = self::getCurlInstance($url, $curl_option);
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		if($curl_errno>0){
			throw new HttpException(curl_error($curl));
		}
		curl_close($curl);
		return $content;
	}
	
	/**
	 * CURL-post方式获取数据
	 * @param string $url URL
	 * @param array $data POST数据
	 * @param array $curl_option
	 * @throws HttpException
	 * @return bool|mixed
	 */
	public static function postInJson($url, $data, $curl_option = array()){
		$opt = array(
			CURLOPT_HTTPHEADER     => array('Content-Type: application/json'),
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => json_encode($data),
			CURLOPT_TIMEOUT        => self::DEFAULT_TIMEOUT,
			CURLOPT_RETURNTRANSFER => 1
		);
		
		$curl_option = self::arrayMergeKeepKeys($opt, $curl_option);
		$curl = self::getCurlInstance($url, $curl_option);
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		$curl_msg = curl_error($curl);
		if($curl_errno>0){
			throw new HttpException($curl_msg);
		}
		curl_close($curl);
		return $content;
	}
	
	/**
	 * CURL-post方式获取数据
	 * @param string $url URL
	 * @param mixed $data POST数据
	 * @param array $curl_option
	 * @throws HttpException
	 * @return bool|mixed
	 */
	public static function postInField($url, $data,  $curl_option=array()) {
		if($data && !is_string($data)){
			$data = http_build_query($data);
		}
		$opt = array(
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $data,
			CURLOPT_TIMEOUT        => self::DEFAULT_TIMEOUT,
			CURLOPT_RETURNTRANSFER => 1,
		);
		$curl_option = self::arrayMergeKeepKeys($opt, $curl_option);
		$curl = self::getCurlInstance($url, $curl_option);
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		$curl_msg = curl_error($curl);
		if($curl_errno>0){
			throw new HttpException($curl_msg);
		}
		
		curl_close($curl);
		return $content;
	}
	
	/**
	 * CURL-post方式上传文件
	 * @param string $url URL
	 * @param mixed $data POST数据
	 * @param array $curl_option
	 * @throws HttpException
	 * @return bool|mixed
	 */
	public static function postFile($url, $data, $curl_option=array()) {
		$opt = array(
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $data,
			CURLOPT_TIMEOUT        => self::DEFAULT_TIMEOUT,
			CURLOPT_RETURNTRANSFER => 1,
		);
		$curl_option = self::arrayMergeKeepKeys($opt, $curl_option);
		$curl = self::getCurlInstance($url, $curl_option);
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		$curl_msg = curl_error($curl);
		if($curl_errno>0){
			throw new HttpException($curl_msg);
		}
		
		curl_close($curl);
		return $content;
	}
	
	/**
	 * CURL-put方式获取数据
	 * @param string $url URL
	 * @param array $data POST数据
	 * @param array $curl_option
	 * @throws HttpException
	 * @return bool|mixed
	 */
	public static function put($url, $data, $curl_option = array()){
		$body = json_encode($data);
		$opts = array(
			CURLOPT_HTTPHEADER    => array('Content-Type: application/json'),
			CURLOPT_CUSTOMREQUEST => "PUT",
			CURLOPT_TIMEOUT       => self::DEFAULT_TIMEOUT,
			CURLOPT_POSTFIELDS    => $body
		);
		$curl_option = self::arrayMergeKeepKeys($opts, $curl_option);
		$curl = self::getCurlInstance($url, $curl_option);
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		if($curl_errno>0){
			throw new HttpException($curl_errno);
		}
		curl_close($curl);
		return $content;
	}
	
	/**
	 * CURL-DEL方式获取数据
	 * @param string $url URL
	 * @param array $curl_option
	 * @throws HttpException
	 * @return bool|mixed
	 */
	public static function del($url, $curl_option = array()){
		$opt = array(
			CURLOPT_CUSTOMREQUEST => "DELETE",
			CURLOPT_TIMEOUT        => self::DEFAULT_TIMEOUT,
		);
		$curl_option = self::arrayMergeKeepKeys($opt, $curl_option);
		$curl = self::getCurlInstance($url, $curl_option);
		$content = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		if($curl_errno>0){
			throw new HttpException($curl_errno);
		}
		curl_close($curl);
		return $content;
	}
}