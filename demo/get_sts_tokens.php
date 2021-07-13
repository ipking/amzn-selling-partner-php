<?php

include '.config.php';

$cred = new \SellingPartner\Core\Credentials($options);

$tokens = $cred->getStsTokens();

print_r($tokens);