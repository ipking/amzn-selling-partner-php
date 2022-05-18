<?php

namespace SellingPartner\Helper;

use SellingPartner\Core\Curl;

class Document
{
	/**
	 * @param $payload : Response from getReportDocument Function. e.g.: response['payload']
	 * @param $to_encoding
	 * @return string : Processing Report.
	 */
	public static function download($payload,$to_encoding='utf-8')
	{
		$feedDownloadUrl = $payload['url'];
		$request = array(
			CURLOPT_CUSTOMREQUEST => "GET",
		);
		$response_array = Curl::execute($feedDownloadUrl,$request);
		list($response_body,$response_code,$response_headers) = $response_array;
		if(isset($payload['compressionAlgorithm']) && $payload['compressionAlgorithm']=='GZIP') {
			$response_body=gzdecode($response_body);
		}
		list($pre,$charset) = explode('charset=',$response_headers['content-type']);
		if($charset and $to_encoding and strtolower($charset) != strtolower($to_encoding)){
			$response_body = mb_convert_encoding($response_body, $to_encoding, $charset);
		}
		
		return $response_body;
	}
}
