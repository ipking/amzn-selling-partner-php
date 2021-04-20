<?php


include dirname(__DIR__).'/src/autoload.inc.php';

$options = [
	'refresh_token' => 'Atzr|IwEBIGXFSwrBVNOg09bsx0hO5ZJP_xNGsud8fHJsrRpWHTOuFG5m-iA3dvMzQmddmppnl6CVLwxmnyiY5VGED3dr6tz-i8OFn909NzQQ7SkFx9xAO-eww88KhWk5LJvNyl_sUSMCctloPLhfUAb4cbuh-9yRthbhIBi7EKE3XS0bmQfEfeNR1w6P9uvyypnzPMiMt2vCymjpQbyssnn8c1cP8DNumnSrRpT9TsMcX33rQWC1QvQMgqNSt6K29apw8iNfEg5PJuB7kegcUQV2LbMB9J4cPS9rjKzoTj22nUfvZQgwOPF01b0vGx0z6hNWfCbTfq8',
	'client_id' => 'amzn1.application-oa2-client.1d53b73b857f4e1bbcec062deda8b34b', // App ID from Seller Central, amzn1.sellerapps.app.cfbfac4a-......
	'client_secret' => '04ba3983af0e064a1a66a826d1bfd7cf89570e1fe1ee91939c83b710bcbb1d56', // The corresponding Client Secret
	'region' => 'eu-west-1', // or NORTH_AMERICA / FAR_EAST
	'access_key' => 'AKIAQN3R6LVYLPGY43PT', // Access Key of AWS IAM User, for example AKIAABCDJKEHFJDS
	'secret_key' => '1EX9Hma/VCzieMVmYfW7mcu1gcl5+pg5dqN7oYm7', // Secret Key of AWS IAM User
	'endpoint' => 'sellingpartnerapi-eu.amazon.com', // or NORTH_AMERICA / FAR_EAST
	'role_arn' => 'arn:aws:iam::029766868336:role/AWSRole',
];

