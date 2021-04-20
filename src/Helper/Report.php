<?php

namespace SellingPartner\Helper;

use SellingPartner\Core\ASECryptoStream;

class Report
{
 
	/**
	 * @param $payload : Response from getReportDocument Function. e.g.: response['payload']
	 * @return string : Processing Report.
	 */
	public function downloadProcessingReport($payload)
	{
		$encryptionDetails = $payload['encryptionDetails'];
		$feedDownloadUrl = $payload['url'];
		
		$key = $encryptionDetails['key'];
		$initializationVector = $encryptionDetails['initializationVector'];
		
		// base64 decode before using in encryption
		$initializationVector = base64_decode($initializationVector, true);
		$key = base64_decode($key, true);
		
		$decryptedFile = ASECryptoStream::decrypt(file_get_contents($feedDownloadUrl), $key, $initializationVector);
		if(isset($payload['compressionAlgorithm']) && $payload['compressionAlgorithm']=='GZIP') {
			$decryptedFile=gzdecode($decryptedFile);
		}
		return $decryptedFile;
	}
}
