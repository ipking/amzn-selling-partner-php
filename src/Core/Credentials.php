<?php

namespace SellingPartner\Core;


class Credentials
{
	const URI_API = 'https://api.amazon.com/auth/o2/token';
	
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }
	
	/**
	 * @return string
	 * @throws HttpException
	 */
	public function getLWAToken()
    {
	    $arr_data = [
		    'grant_type'    => 'refresh_token',
		    'refresh_token' => $this->config['refresh_token'],
		    'client_id'     => $this->config['client_id'],
		    'client_secret' => $this->config['client_secret']
	    ];
	    $response = Curl::postInField(self::URI_API, $arr_data);
        $json = json_decode($response, true);
        return $json['access_token'];
    }
	
	
	/**
	 * @return array
	 * @throws \SellingPartner\Core\HttpException
	 * @throws \SellingPartner\Core\SignerException
	 */
	public function getStsTokens()
    {
	    $arr_data = [
		    'Action'          => 'AssumeRole',
		    'DurationSeconds' => $this->config['duration_seconds'] ?: 3600,
		    'RoleArn'         => $this->config['role_arn'],
		    'RoleSessionName' => 'role_session',
		    'Version'         => '2011-06-15',
	    ];
		
        $host = 'sts.amazonaws.com';
        $uri = '/';

        $headers = Signer::sign([
	        'service'    => 'sts',
	        'access_key' => $this->config['access_key'],
	        'secret_key' => $this->config['secret_key'],
	        'region'     => 'us-east-1',
	        'host'       => $host,
	        'uri'        => $uri,
	        'method'     => Client::METHOD_POST,
	        'payload'    => http_build_query($arr_data),
        ]);
	    $request[CURLOPT_HTTPHEADER] = array_merge([
		    'accept' => 'application/json'
	    ], $headers);
	
	    $response = Curl::postInField('https://'.$host.$uri, $arr_data,$request);
	
	    $json = json_decode($response, true);
	    $credentials = $json['AssumeRoleResponse']['AssumeRoleResult']['Credentials'] ?: null;
	    $tokens = [
		    'access_key'    => $credentials['AccessKeyId'],
		    'secret_key'    => $credentials['SecretAccessKey'],
		    'session_token' => $credentials['SessionToken']
	    ];
	
	    return $tokens;

    }
	
	/**
	 * @param $authorizationCode
	 * @return string
	 * @throws \SellingPartner\Core\HttpException
	 */
    public function getRefreshTokenByAuthorizationCode($authorizationCode)
    {
	    $arr_data = [
		    'grant_type'    => 'authorization_code',
		    'code'          => $authorizationCode,
		    'client_id'     => $this->config['client_id'],
		    'client_secret' => $this->config['client_secret']
	    ];
	    $response = Curl::postInField(self::URI_API, $arr_data);
	    $json = json_decode($response, true);
	    return $json['refresh_token'];
    }
    
}
