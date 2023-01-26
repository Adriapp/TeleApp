<?php

/*
*
* Use this script if you want to use self-signed certificates with your telegram bot.
*
* Official guide: https://core.telegram.org/bots/self-signed
*
* Usage: user@adenela:~$ php setWebhook.php /path/to/selfsigned-certificate.crt BOT_TOKEN_HERE IP_ADDRESS_OR_DOMAIN_HERE/path/to/bot.php
*
* Example: hitori@bocchi:~$ php setWebhook.php /etc/ssl/certs/selfsigned-nginx.crt 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11 12.34.56.78/telegram/bots/myawesomebot/index.php
*
*/

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);
$document = curl_file_create($argv[1]);

$args = [
   'url' => 'https://'.$argv[3],
   'certificate' => $document
];

curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.$argv[2].'/setWebhook');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $args);

$output = curl_exec($ch);
curl_close($ch);

if($output == '{"ok":false,"error_code":401,"description":"Unauthorized"}'){
   exit('Error, wrong bot token'.PHP_EOL);
} else if($output == '{"ok":true,"result":true,"description":"Webhook was set"}'){
   exit('Done, webhook was set'.PHP_EOL);
} else {
   exit($output.PHP_EOL);
}
