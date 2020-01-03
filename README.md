# TeleApp

Ciao! Questa repository contiene i file delle funzioni che uso per creare i miei bot telegram, essendo stato un progetto "privato", non ho commentato molto dettagliatamente i pezzi del codice, era ed è fatto da me per me. 


Sentiti libero di suggerire delle modifiche :D

# Getting Started

<code>include 'bot.php'; </code> <br>
<code>$bot = new Bot('TOKEN DEL BOT'); </code>


# Il tuo primo comando

<code>if($bot->text == '/start'){</code><br>
  <code>$bot->sendMessage($bot->user_id,'Funziona!'); </code> <br>
<code>}</code>
