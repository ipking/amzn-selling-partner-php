<?php

namespace SellingPartner\Core;

abstract class Client{
	
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_DELETE = 'DELETE';
	
	protected static $debug = false;
	
	protected static $callback;
	
	/**
	 * 请求方式
	 */
	protected $method;
	
	/**
	 * 请求地址
	 */
	protected $url;
	/**
	 * 请求数据
	 */
	protected $data;
	
	
	
	protected $client_response;
	
	protected $access_token;
	protected $sts_access_key;
	protected $sts_secret_key;
	protected $sts_session_token;
	protected $region;
	protected $host;
	
	
	public static function setDebug(){
		self::$debug = true;
	}
	
	/**
	 * @param $cb
	 */
	public static function setSendCallback($cb){
		self::$callback = $cb;
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
	 * @param $access_token
	 */
	public function setAccessToken($access_token){
		$this->access_token = $access_token;
	}
	
	/**
	 * @param $sts_access_key
	 */
	public function setStsAccessKey($sts_access_key){
		$this->sts_access_key = $sts_access_key;
	}
	
	/**
	 * @param $sts_secret_key
	 */
	public function setStsSecretKey($sts_secret_key){
		$this->sts_secret_key = $sts_secret_key;
	}
	
	/**
	 * @param $sts_session_token
	 */
	public function setStsSessionToken($sts_session_token){
		$this->sts_session_token = $sts_session_token;
	}
	
	/**
	 * @param $region
	 */
	public function setRegion($region){
		$this->region = $region;
	}
	
	/**
	 * @param $host
	 */
	public function setHost($host){
		$this->host = $host;
	}
	
	private function normalizeHeaders($headers)
	{
		return array_combine(
			array_map(function($header) { return strtolower($header); }, array_keys($headers)),
			$headers
		);
		
	}
	
	/**
	 * 发送数据
	 * @param string $uri
	 * @param array $requestOptions
	 * @return array
	 * @throws HttpException|SignerException
	 */
	protected function send($uri, $requestOptions = []){
		
		$this->method = $requestOptions['method'];
		$this->url = 'https://' . $this->host.$uri;
		
		$requestOptions['headers'] = $requestOptions['headers'] ?: [];
		$requestOptions['headers'] = $this->normalizeHeaders($requestOptions['headers']);
		
		
		$signOptions = [
			'service'        => 'execute-api',
			'access_token'   => $this->access_token,
			'access_key'     => $this->sts_access_key,
			'secret_key'     => $this->sts_secret_key,
			'security_token' => $this->sts_session_token,
			'region'         => $this->region,
			'host'           => $this->host,
			'uri'            => $uri,
			'method'         => $this->method
		];
		
		if (isset($requestOptions['query'])) {
			$query = $requestOptions['query'];
			ksort($query);
			$signOptions['query_string'] =  http_build_query($query);
			$this->url .= '?'.$signOptions['query_string'];
		}
	
		if (isset($requestOptions['json'])) {
			ksort($requestOptions['json']);
			$signOptions['payload'] = json_encode($requestOptions['json']);
		}
		
		
		$headers = Signer::sign($signOptions);
		$headers = array_merge([
			'accept' => 'application/json',
		], $headers);
		
		$request = [];
		
		foreach($headers as $key => $item){
			$request[CURLOPT_HTTPHEADER][] = $key.': '.$item;
		}
		
		$this->data = json_encode($signOptions['payload']);
		
		
		if(self::$debug){
			echo "\n+++++++++++++++++ REQ +++++++++++++++\n";
			echo $this->url.PHP_EOL;
			echo $this->data;
			echo "\n+++++++++++++++++ REQ +++++++++++++++\n";
		}
		
		switch($this->method){
			case self::METHOD_GET:
				$this->client_response = Curl::get($this->url,$request);
				break;
			case self::METHOD_POST:
				$this->client_response = Curl::post($this->url, $signOptions['payload'],$request);
				break;
			case self::METHOD_PUT:
				$this->client_response = Curl::put($this->url, $signOptions['payload'],$request);
				break;
			case self::METHOD_DELETE:
				$this->client_response = Curl::del($this->url,$request);
				break;
			default:
				throw new \Exception('method '.$this->method.' not yet supply');
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