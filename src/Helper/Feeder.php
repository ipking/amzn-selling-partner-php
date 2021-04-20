<?php


namespace SellingPartner\Helper;



use SellingPartner\Core\ASECryptoStream;
use SellingPartner\Core\Curl;

class Feeder
{
    /**
     * @param $payload : Response from createFeedDocument Function. e.g.: response['payload']
     * @param $contentType : Content type used during createFeedDocument function call.
     * @param $feedContent : Content that contain data to be uploaded.
     * @return string
     * @throws \Exception
     */
    public function uploadFeedDocument($payload, $contentType, $feedContent)
    {
        $encryptionDetails = $payload['encryptionDetails'];
        $feedUploadUrl = $payload['url'];

        $key = $encryptionDetails['key'];
        $initializationVector = $encryptionDetails['initializationVector'];

        // base64 decode before using in encryption
        $initializationVector = base64_decode($initializationVector, true);
        $key = base64_decode($key, true);

        // utf8 !
        $file = utf8_encode($feedContent);

        // encrypt string and get value as base64 encoded string
        $encryptedFile = ASECryptoStream::encrypt($file, $key, $initializationVector);
        
	    $request = array(
		    CURLOPT_HTTPHEADER    => array('Content-Type: '.$contentType),
		    CURLOPT_CUSTOMREQUEST => "PUT",
		    CURLOPT_POSTFIELDS    => $encryptedFile
	    );
	
	    return Curl::execute($feedUploadUrl,$request);
    }

    /**
     * @param $payload : Response from getFeedDocument Function. e.g.: response['payload']
     * @return array : Feed Processing Report.
     */
    public function downloadFeedProcessingReport($payload)
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
        $decryptedFile = preg_replace('/\s+/S', " ", $decryptedFile);

        $xml = simplexml_load_string($decryptedFile);
        $json = json_encode($xml);
        return json_decode($json, TRUE);
    }
}
