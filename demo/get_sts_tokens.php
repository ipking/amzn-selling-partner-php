<?php

use SellingPartner\Core\Credentials;

include '.config.php';

$cred = new Credentials($options);

$tokens = $cred->getStsTokens();

print_r($tokens);