<?php

namespace SellingPartner\Core;


abstract class Curl{
	const DEFAULT_TIMEOUT = 30;
	protected static $debug = false;
	
	public static function setDebug(){
		self::$debug = true;
	}
	/**
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
			$opt[CURLOPT_SSL_VERIFYPEER] = 0;
			$opt[CURLOPT_SSL_VERIFYHOST] = 1;
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
	 * @param string $url
	 * @param mixed $data
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
	 * @param string $url
	 * @param array $curl_option
	 * @throws HttpException
	 * @return array
	 */
	public static function execute($url, $curl_option = array()){
		$opt = array(
			CURLOPT_HEADER         => true,
		);
		$curl_option = self::arrayMergeKeepKeys($opt, $curl_option);
		$curl = self::getCurlInstance($url, $curl_option);
		$content = curl_exec($curl);
		
		if(self::$debug){
			$method = $curl_option[CURLOPT_CUSTOMREQUEST]?:($curl_option[CURLOPT_POST]?'POST':'GET');
			echo $method.' '.$url,PHP_EOL;
			echo json_encode($curl_option[CURLOPT_HTTPHEADER], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT),PHP_EOL;
			echo $curl_option[CURLOPT_POSTFIELDS],PHP_EOL;
			echo PHP_EOL;
			echo $content,PHP_EOL,PHP_EOL;
		}
		
		$curl_errno = curl_errno($curl);
		if($curl_errno>0){
			throw new HttpException($curl_errno);
		}
		
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		$response_headers = substr($content, 0, $header_size);
		// Parse out the headers
		$response_headers = explode("\r\n\r\n", trim($response_headers));
		$response_headers = array_pop($response_headers);
		$response_headers = explode("\r\n", $response_headers);
		array_shift($response_headers);
		// Loop through and split up the headers.
		$header_assoc = array();
		foreach ($response_headers as $header) {
			$kv = explode(': ', $header);
			$header_assoc[strtolower($kv[0])] = isset($kv[1]) ? $kv[1] : '';
		}
		$response_body = substr($content, $header_size);
		curl_close($curl);
		return [$response_body,$response_code,$header_assoc];
	}
}