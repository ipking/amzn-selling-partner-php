<?php

namespace SellingPartner\Helper;

use SellingPartner\Core\Curl;

class Document
{
	/**
	 * @param $payload : Response from getReportDocument Function. e.g.: response['payload']
	 * @return string : Processing Report.
	 */
	public static function download($payload)
	{
		$feedDownloadUrl = $payload['url'];
		$request = array(
			CURLOPT_CUSTOMREQUEST => "GET",
		);
		$content = Curl::execute($feedDownloadUrl,$request);
		
		if(isset($payload['compressionAlgorithm']) && $payload['compressionAlgorithm']=='GZIP') {
			$content=gzdecode($content);
		}
		return $content;
	}
}
