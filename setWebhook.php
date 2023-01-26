<?php

//Usage: user@hostname:~$ php setWebhook.php /path/to/selfsigned-certificate.crt BOT_TOKEN_HERE IP_ADDRESS_OR_DOMAIN_HERE/path/to/bot.php

//Example: hitori@bocchi:~$ php setWebhook.php /etc/ssl/certs/selfsigned-nginx.crt 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11 12.34.56.78/telegram/bots/myawesomebot/index.php

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
exit($output);
