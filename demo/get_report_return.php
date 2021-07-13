<?php

include '.config.php';

$cred = new \SellingPartner\Core\Credentials($options);
$access_token = $cred->getLWAToken();
$tokens = $cred->getStsTokens();

// content type of the feed data to be uploaded.
$contentType = 'text/xml; charset=UTF-8';

// create feed document
$feedClient = new \SellingPartner\Api\Reports();
$feedClient->setAccessToken($access_token);
$feedClient->setHost($options['endpoint']);
$feedClient->setRegion($options['region']);
$feedClient->setStsAccessKey($tokens['access_key']);
$feedClient->setStsSecretKey($tokens['secret_key']);
$feedClient->setStsSessionToken($tokens['session_token']);

$response = $feedClient->createReport([
	'reportType'=>'GET_FBA_FULFILLMENT_CUSTOMER_RETURNS_DATA',
	'dataStartTime'=>date("Y-m-d\\TH:i:s\\Z", strtotime('2021-04-01'."-8 hours")),
	'dataEndTime'=>date("Y-m-d\\TH:i:s\\Z", strtotime('2021-04-14'."-8 hours")),
	'marketplaceIds'=>['A1PA6795UKMFR9'],
]);
while(1){
	sleep(1);
	$response = $feedClient->getReport($response['payload']['reportId']);
	
	if(strtoupper($response['processingStatus']) == 'DONE'){
		break;
	}
}


print_r($response);

$response = $feedClient->getReportDocument($response['reportDocumentId']);
print_r($response);


$result = (new \SellingPartner\Helper\Report())->downloadProcessingReport($response);
print_r($result);


$fileArr = array_filter(explode("\r\n",$result));
$keys    = array_filter(explode("\t", array_shift($fileArr)));
$reportData = [];

$total = count($keys);
foreach($fileArr as $k => $item){
	if ($item && FALSE !== strpos($item, "\t")) {
		$items = array_filter(explode("\t", $item));
		if (count($items) < $keys){
			$items = array_pad($items, $total, '');
		}
		$reportData[] = array_combine($keys, $items);
	}
}
print_r($reportData);