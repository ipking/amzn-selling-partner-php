<?php

use SellingPartner\Core\Credentials;

include '.config.php';

$cred = new Credentials($options);

$access_token = $cred->getLWAToken();

print_r($access_token);