# TeleApp
[![License: GPL v3](https://img.shields.io/github/license/Adriapp/TeleApp)](https://img.shields.io/github/license/Adriapp/TeleApp)
[![GitHub issues](https://img.shields.io/github/issues/Adriapp/TeleApp)](https://img.shields.io/github/issues/Adriapp/TeleApp)
[![GitHub stars](https://img.shields.io/github/stars/Adriapp/TeleApp)](https://img.shields.io/github/stars/Adriapp/TeleApp)
[![GitHub forks](https://img.shields.io/github/forks/Adriapp/TeleApp)](https://img.shields.io/github/forks/Adriapp/TeleApp)
Ciao! Questa repository contiene i file delle funzioni che uso per creare i miei bot telegram, essendo stato un progetto "privato", non ho commentato molto dettagliatamente i pezzi del codice, era ed è fatto da me per me. 

Sentiti libero di suggerire delle modifiche :D

## Installazione

`sudo apt install git`
`git clone <https://github.com/Adriapp/TeleApp>`

## Getting Started
```php
<?php
include 'bot.php';
$bot = new Bot('TOKEN DEL BOT');
```

## Il tuo primo comando
```php
if ($bot->text == '/start'){
 $bot->sendMessage($bot->user_id,'Funziona!');
 }
```
