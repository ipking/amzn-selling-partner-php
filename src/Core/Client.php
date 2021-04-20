<?php

namespace SellingPartner\Core;

abstract class Client{
	
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_DELETE = 'DELETE';
	
	public static $debug = false;
	
	protected static $callback;
	
	/**
	 * 请求方式
	 */
	protected $method = self::METHOD_GET;
	
	/**
	 * curl option
	 */
	protected $curl_option = array();
	
	/**
	 * 请求地址
	 */
	protected $url;
	/**
	 * 请求数据
	 */
	protected $data;
	
	
	protected $param;
	
	
	protected $client_response;
	
	/**
	 * 通过OAuth授权方式获得
	 */
	protected $access_token;

	
	/**
	 * @param $access_token
	 */
	public function setAccessToken($access_token){
		$this->access_token = $access_token;
	}
	
	/**
	 * @param $cb
	 */
	public static function setSendCallback($cb){
		self::$callback = $cb;
	}
	
	
	/**
	 * @return array
	 */
	public function getParam(){
		return $this->param;
	}
	
	/**
	 * @return string
	 */
	public function getMethod(){
		return $this->method;
	}
	
	/**
	 * @return string
	 */
	public function getUrl(){
		return $this->url;
	}
	
	/**
	 * @return string
	 */
	public function getData(){
		return $this->data;
	}
	
	/**
	 * @return string
	 */
	public function getResponse(){
		return $this->client_response;
	}
	
	
	/**
	 * 发送数据
	 * @param array $arr_data
	 * @return array
	 * @throws HttpException
	 */
	protected function sendData($arr_data){
		$this->data = json_encode($arr_data);
		
		
		if(self::$debug){
			echo "\n+++++++++++++++++ REQ +++++++++++++++\n";
			echo $this->url.PHP_EOL;
			echo $this->data;
			echo "\n+++++++++++++++++ REQ +++++++++++++++\n";
		}
		
		try{
			$curl_option= $this->curl_option;
			
			$timeout = Curl::DEFAULT_TIMEOUT;
			switch($this->method){
				case self::METHOD_GET:
					$this->client_response = Curl::get($this->url,$timeout,$curl_option);
					break;
				case self::METHOD_POST:
					$this->client_response = Curl::postInJson($this->url, $arr_data,$timeout,$curl_option);
					break;
				case self::METHOD_PUT:
					$this->client_response = Curl::put($this->url, $arr_data,$timeout,$curl_option);
					break;
				case self::METHOD_DELETE:
					$this->client_response = Curl::del($this->url,$timeout,$curl_option);
					break;
				default:
					throw new \Exception('method '.$this->method.' not yet supply');
					break;
			}
		}catch(\Exception $e){
			return ['error'=>1,'message'=>$e->getMessage()];
		}
		
		
		if(self::$debug){
			echo "\n=============== RSP ================\n";
			echo $this->client_response;
			echo "\n=============== RSP ================\n";
		}
		
		if(is_callable(self::$callback)){
			$callback = self::$callback;
			$callback($this);
		}
		return \json_decode($this->client_response, true);
	}
}