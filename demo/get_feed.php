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


// get feed document
$response = $feedClient->getFeed('91287018737');

print_r($response);
$response = $feedClient->getFeedDocument($response['resultFeedDocumentId']);

print_r($response);

$result = (new \SellingPartner\Helper\Feeder())->downloadFeedProcessingReport($response);
print_r($result);