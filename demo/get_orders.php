<?php



include '.config.php';

$cred = new \SellingPartner\Core\Credentials($options);
$access_token = $cred->getLWAToken();
$tokens = $cred->getStsTokens();

$order = new \SellingPartner\Api\Orders();
$order->setAccessToken($access_token);
$order->setHost($options['endpoint']);
$order->setRegion($options['region']);
$order->setStsAccessKey($tokens['access_key']);
$order->setStsSecretKey($tokens['secret_key']);
$order->setStsSessionToken($tokens['session_token']);

$data = [
	'LastUpdatedAfter'  => "2021-03-11T09:00:00",
	'LastUpdatedBefore' => "2021-03-31T09:00:00",
	'MarketplaceIds'    => 'A1PA6795UKMFR9',
	'MaxResultsPerPage' => '2',
];

$rsp = $order->getOrders($data);

print_r($rsp);