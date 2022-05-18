<?php


include '.config.php';

$cred = new \SellingPartner\Core\Credentials($options);
$access_token = $cred->getLWAToken();
$tokens = $cred->getStsTokens();

$client = new \SellingPartner\Api\Tokens();
$client->setAccessToken($access_token);
$client->setHost($options['endpoint']);
$client->setRegion($options['region']);
$client->setStsAccessKey($tokens['access_key']);
$client->setStsSecretKey($tokens['secret_key']);
$client->setStsSessionToken($tokens['session_token']);
$data = [
	[
		'method'=>'GET',
		'path'=>'/orders/v0/orders/304-0985825-4344317',
		'dataElements'=>["buyerInfo", "shippingAddress"],
	]
];

$response = $client->createRestrictedDataToken(['restrictedResources'=>$data]);
print_r($response);