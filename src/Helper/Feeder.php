<?php

namespace SellingPartner\Helper;

use SellingPartner\Core\Curl;

class Feeder
{
    /**
     * @param $payload : Response from createFeedDocument Function. e.g.: response['payload']
     * @param $contentType : Content type used during createFeedDocument function call.
     * @param $feedContent : Content that contain data to be uploaded.
     * @return array
     * @throws \Exception
     */
    public function uploadFeedDocument($payload, $contentType, $feedContent)
    {
        $feedUploadUrl = $payload['url'];

	    $request = array(
		    CURLOPT_HTTPHEADER    => array('Content-Type: '.$contentType),
		    CURLOPT_CUSTOMREQUEST => "PUT",
		    CURLOPT_POSTFIELDS    => $feedContent
	    );
	
	    return Curl::execute($feedUploadUrl,$request);
    }

    /**
     * @param $payload : Response from getFeedDocument Function. e.g.: response['payload']
     * @return array : Feed Processing Report.
     */
    public function downloadFeedProcessingReport($payload)
    {
        $decryptedFile = Document::download($payload);
        $decryptedFile = preg_replace('/\s+/S', " ", $decryptedFile);
        $xml = simplexml_load_string($decryptedFile);
        $json = json_encode($xml);
        return json_decode($json, TRUE);
    }
}
