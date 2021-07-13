<?php


include '.config.php';

$cred = new \SellingPartner\Core\Credentials($options);

$access_token = $cred->getLWAToken();

print_r($access_token);