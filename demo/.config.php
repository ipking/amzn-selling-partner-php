<?php


include dirname(__DIR__).'/src/autoload.inc.php';

$options = [
	'refresh_token' => '',
	'client_id' => '', // App ID from Seller Central, amzn1.sellerapps.app.cfbfac4a-......
	'client_secret' => '', // The corresponding Client Secret
	'region' => 'eu-west-1', // or NORTH_AMERICA / FAR_EAST
	'access_key' => '', // Access Key of AWS IAM User, for example AKIAABCDJKEHFJDS
	'secret_key' => '', // Secret Key of AWS IAM User
	'endpoint' => 'sellingpartnerapi-eu.amazon.com', // or NORTH_AMERICA / FAR_EAST
	'role_arn' => '',
];

