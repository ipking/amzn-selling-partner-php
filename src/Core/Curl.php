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
			CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_RETURNTRANSFER => true,
		);
		
		if($ssl){
			$opt[CURLOPT_SSL_VERIFYPEER] = 0;                       //对认证证书来源的检查
			$opt[CURLOPT_SSL_VERIFYHOST] = 1;                       //从证书中检查SSL加密算法是否存在
		}
		
		//设置缺省参数
		$curl_option = self::arrayMergeKeepKeys($opt, $curl_option);
		
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		
		$a[CURLOPT_URL] = $url;
		foreach($curl_option as $k => $val){
			$a[$k] = $val;
			curl_setopt($curl, $k, $val);
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
	 * @param mixed $data POST数据
	 * @param array $curl_option
	 * @throws HttpException
	 * @return bool|mixed
	 */
	public static function post($url, $data, $curl_option=array()) {
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