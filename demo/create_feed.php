<?php

include '.config.php';

$cred = new \SellingPartner\Core\Credentials($options);
$access_token = $cred->getLWAToken();
$tokens = $cred->getStsTokens();

// content type of the feed data to be uploaded.
$contentType = 'text/xml; charset=UTF-8';

// create feed document
$feedClient = new \SellingPartner\Api\Feeds();
$feedClient->setAccessToken($access_token);
$feedClient->setHost($options['endpoint']);
$feedClient->setRegion($options['region']);
$feedClient->setStsAccessKey($tokens['access_key']);
$feedClient->setStsSecretKey($tokens['secret_key']);
$feedClient->setStsSessionToken($tokens['session_token']);

$response = $feedClient->createFeedDocument(["contentType" => $contentType]);
$payload = $response['payload'];

$feedContentFilePath = './testFeedDoc.xml';

$feedContent = file_get_contents($feedContentFilePath);

$result = (new \SellingPartner\Helper\Feeder())->uploadFeedDocument($payload,$contentType,$feedContent);


$data = [
	'feedType'=>'POST_ORDER_FULFILLMENT_DATA',
	'marketplaceIds'=>['A1PA6795UKMFR9'],
	'inputFeedDocumentId'=>$payload['feedDocumentId'],
];
$response = $feedClient->createFeed($data);

print_r($response);
