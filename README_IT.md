# TeleApp

[![License: MIT](https://img.shields.io/github/license/Adriapp/TeleApp)](https://github.com/Adriapp/TeleApp/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/Adriapp/TeleApp)](https://github.com/Adriapp/TeleApp/issues)
[![GitHub stars](https://img.shields.io/github/stars/Adriapp/TeleApp)](https://github.com/Adriapp/TeleApp/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/Adriapp/TeleApp)](https://github.com/Adriapp/TeleApp/network)

> **[Read the documentation in English](README.md)**

TeleApp e' un framework PHP leggero per creare bot Telegram. Fornisce un wrapper ad oggetti attorno alla [Telegram Bot API](https://core.telegram.org/bots/api), permettendoti di accedere facilmente ai dati dei messaggi tramite le proprieta' dell'oggetto e chiamare metodi per interagire con l'API.

---

## Indice

- [Struttura del Repository](#struttura-del-repository)
- [Requisiti](#requisiti)
- [Installazione](#installazione)
- [Configurazione](#configurazione)
  - [Setup Webhook via PHP](#setup-webhook-via-php)
  - [Setup Webhook con Certificato Self-Signed](#setup-webhook-con-certificato-self-signed-setwebhookphp)
- [Guida Rapida](#guida-rapida)
- [Proprieta' del Bot](#proprieta-del-bot)
- [Metodi](#metodi)
- [Esempi Pratici](#esempi-pratici)
- [Metodi v1 vs v2](#metodi-v1-vs-v2)
- [Licenza](#licenza)

---

## Struttura del Repository

| File | Descrizione |
|---|---|
| `bot.php` | Classe principale `Bot` - wrapper OOP per la Telegram Bot API. Analizza gli update in entrata e fornisce metodi per tutte le interazioni API. |
| `setWebhook.php` | Utility da riga di comando per configurare il webhook quando si usano certificati SSL self-signed. |
| `LICENSE` | Licenza MIT. |
| `README.md` | Documentazione in inglese. |
| `README_IT.md` | Questa documentazione (italiano). |

---

## Requisiti

- **PHP** con estensione **cURL** abilitata
- Un web server con **HTTPS** (richiesto da Telegram)
- Un **token API** Telegram Bot (ottenuto da [@BotFather](https://t.me/BotFather))

---

## Installazione

```bash
sudo apt install git
git clone https://github.com/Adriapp/TeleApp
```

---

## Configurazione

### 1. Crea un bot su Telegram

1. Apri Telegram e cerca [@BotFather](https://t.me/BotFather)
2. Invia il comando `/newbot`
3. Segui le istruzioni: scegli un nome e uno username per il tuo bot
4. BotFather ti fornira' un **token API** (es. `123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11`)

### 2. Crea il file del bot

Crea un file PHP (es. `index.php`) sul tuo server con HTTPS abilitato:

```php
<?php
include 'bot.php';
$bot = new Bot('TOKEN_DEL_TUO_BOT');

// La logica del tuo bot qui
```

### Setup Webhook via PHP

Una volta che il tuo file e' accessibile via HTTPS, imposta il webhook con:

```php
<?php
include 'bot.php';
$bot = new Bot('TOKEN_DEL_TUO_BOT');
$bot->setWebhook(false, 'https://tuodominio.com/percorso/index.php');
```

Esegui questo file una volta nel browser, poi rimuovi o commenta la chiamata `setWebhook`.

Puoi verificare lo stato del webhook con:

```php
$info = $bot->getWebhookInfo();
print_r($info);
```

E cancellarlo con:

```php
$bot->deleteWebhook();
```

### Setup Webhook con Certificato Self-Signed (`setWebhook.php`)

Se il tuo server usa un **certificato SSL self-signed**, usa l'utility `setWebhook.php` da riga di comando:

```bash
php setWebhook.php /percorso/del/certificato.crt TOKEN_BOT DOMINIO/percorso/del/bot.php
```

**Esempio:**

```bash
php setWebhook.php /etc/ssl/certs/selfsigned-nginx.crt 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11 12.34.56.78/telegram/bots/miobot/index.php
```

**Argomenti:**

| # | Argomento | Descrizione |
|---|---|---|
| 1 | Percorso certificato | Percorso assoluto al file `.crt` |
| 2 | Token bot | Token ricevuto da BotFather |
| 3 | URL bot | Dominio o IP + percorso al file del tuo bot (senza `https://`, viene aggiunto automaticamente) |

**Output possibili:**
- `Done, webhook was set` - Successo
- `Error, wrong bot token` - Token non valido
- Risposta JSON - Altri errori API

---

## Guida Rapida

```php
<?php
include 'bot.php';
$bot = new Bot('TOKEN_DEL_TUO_BOT');

if ($bot->text == '/start') {
    $bot->sendMessage($bot->user_id, 'Ciao! Il bot funziona!');
}
```

---

## Proprieta' del Bot

Quando viene ricevuto un update da Telegram, il costruttore `Bot` analizza automaticamente il JSON e popola le seguenti proprieta'.

### Proprieta' Utente

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->user_id` | int | ID utente Telegram del mittente |
| `$bot->nome` | string | Nome dell'utente |
| `$bot->cognome` | string | Cognome dell'utente |
| `$bot->username` | string | Username Telegram dell'utente |
| `$bot->lingua` | string | Codice lingua dell'utente (es. `it`, `en`) |
| `$bot->is_bot` | bool | Indica se il mittente e' un bot |

### Proprieta' Chat

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->chat_id` | int | ID della chat (uguale a `user_id` nelle chat private) |
| `$bot->username_chat` | string | Username della chat |
| `$bot->tipo_chat` | string | Tipo chat: `private`, `group`, `supergroup` o `channel` |
| `$bot->nome_chat` | string | Nome chat (nome nelle private, titolo nei gruppi) |

### Proprieta' Messaggio

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->text` | string | Contenuto testuale del messaggio |
| `$bot->message_id` | int | ID univoco del messaggio |
| `$bot->update_id` | int | ID dell'update |
| `$bot->time` | int | Timestamp del messaggio (Unix) |
| `$bot->edit` | bool | `true` se il messaggio e' stato modificato |
| `$bot->messageType` | string | `'message'` o `'edited_message'` |
| `$bot->entities` | array | Entita' del messaggio (menzioni, hashtag, URL, ecc.) |

### Proprieta' Media

#### Foto

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->foto` | string | `file_id` della foto |
| `$bot->didascalia` | string | Didascalia della foto |
| `$bot->file_unique_id` | string | Identificatore univoco del file |

#### Documenti

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->file` | string | `file_id` del documento |
| `$bot->nome_file` | string | Nome del file |
| `$bot->tipo_file` | string | Tipo MIME (es. `application/pdf`) |
| `$bot->size_file` | int | Dimensione file in byte |

#### Video

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->video` | string | `file_id` del video |
| `$bot->tipo_video` | string | Tipo MIME |
| `$bot->durata_video` | int | Durata in secondi |
| `$bot->width_video` | int | Larghezza in pixel |
| `$bot->height_video` | int | Altezza in pixel |
| `$bot->size_video` | int | Dimensione file in byte |

#### Animazioni (GIF)

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->gif` | string | `file_id` dell'animazione |
| `$bot->tipo_gif` | string | Tipo MIME |
| `$bot->durata_gif` | int | Durata in secondi |
| `$bot->width_gif` | int | Larghezza in pixel |
| `$bot->height_gif` | int | Altezza in pixel |
| `$bot->size_gif` | int | Dimensione file in byte |

#### Sticker

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->sticker` | string | `file_id` dello sticker |
| `$bot->nome_sticker` | string | Nome del set di sticker |
| `$bot->emoji_sticker` | string | Emoji associata |
| `$bot->is_animated` | bool | Indica se lo sticker e' animato |
| `$bot->width_sticker` | int | Larghezza in pixel |
| `$bot->height_sticker` | int | Altezza in pixel |
| `$bot->size_sticker` | int | Dimensione file in byte |

### Proprieta' Messaggi Inoltrati

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->forward_sender_name` | string | Nome del mittente originale (se inoltrato anonimamente) |
| `$bot->forward_date` | int | Timestamp del messaggio originale |
| `$bot->forward_text` | string | Testo del messaggio inoltrato |
| `$bot->forward_chat_id` | int | ID del mittente/chat originale |
| `$bot->forward_is_bot` | bool | Indica se il mittente originale e' un bot |
| `$bot->forward_nome` | string | Nome del mittente originale |
| `$bot->forward_cognome` | string | Cognome del mittente originale |
| `$bot->forward_username` | string | Username del mittente originale |
| `$bot->forward_title` | string | Titolo del canale/chat originale (se inoltrato da un canale) |
| `$bot->forward_type` | string | Tipo di chat originale (se inoltrato da un canale) |
| `$bot->forward_from_message_id` | int | ID del messaggio originale nella chat sorgente |

### Proprieta' Risposte

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->reply_message_id` | int | ID del messaggio a cui si sta rispondendo |
| `$bot->reply_user_id` | int | ID dell'utente del messaggio originale |
| `$bot->reply_nome` | string | Nome del mittente originale |
| `$bot->reply_cognome` | string | Cognome del mittente originale |
| `$bot->reply_username` | string | Username del mittente originale |
| `$bot->reply_text` | string | Testo del messaggio a cui si risponde |
| `$bot->reply_is_bot` | bool | Indica se il mittente originale e' un bot |
| `$bot->reply_tipo` | string | Tipo |
| `$bot->reply_time` | int | Timestamp del messaggio originale |
| `$bot->reply_chat_id` | int | ID chat della risposta |
| `$bot->reply_chat_nome` | string | Nome della chat |
| `$bot->reply_chat_cognome` | string | Cognome della chat |
| `$bot->reply_chat_tipo` | string | Tipo della chat |
| `$bot->reply_entities` | array | Entita' nel messaggio a cui si risponde |

**Risposta a un messaggio inoltrato:**

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->chat_id_reply_forward` | int | ID del mittente originale |
| `$bot->is_bot_reply_forward` | bool | Se il mittente originale e' un bot |
| `$bot->nome_reply_forward` | string | Nome del mittente originale |
| `$bot->cognome_reply_forward` | string | Cognome del mittente originale |
| `$bot->time_reply_forward` | int | Timestamp di inoltro |
| `$bot->text_reply` | string | Testo del messaggio inoltrato a cui si risponde |

### Proprieta' Callback Query

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->callback_query_id` | string | ID della callback query |
| `$bot->callback_user_id` | int | Utente che ha cliccato il bottone |
| `$bot->callback_data` | string | Dato associato al bottone |
| `$bot->callback_message_id` | int | ID del messaggio contenente il bottone |
| `$bot->callback_text` | string | Testo del messaggio contenente il bottone |
| `$bot->callback_chat_id` | int | ID della chat |
| `$bot->callback_nome` | string | Nome dell'utente |
| `$bot->callback_cognome` | string | Cognome dell'utente |
| `$bot->callback_lingua` | string | Codice lingua dell'utente |
| `$bot->callback_is_bot` | bool | Se l'utente e' un bot |
| `$bot->callback_chat_title` | string | Titolo della chat |
| `$bot->callback_chat_type` | string | Tipo di chat |
| `$bot->callback_time` | int | Timestamp del messaggio |
| `$bot->callback_entities` | array | Entita' del messaggio |
| `$bot->callback_inline_keyboard` | array | Dati della tastiera inline |
| `$bot->callback_chat_instance` | string | Identificatore univoco dell'istanza chat |
| `$bot->callback_bot_id` | int | ID utente del bot |
| `$bot->callback_bot_is_bot` | bool | Sempre `true` |
| `$bot->callback_bot_nome` | string | Nome del bot |
| `$bot->callback_bot_username` | string | Username del bot |

### Proprieta' Inline Query

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->inline_id` | string | ID della inline query |
| `$bot->inline_user_id` | int | Utente che ha iniziato la inline query |
| `$bot->inline_nome` | string | Nome dell'utente |
| `$bot->inline_cognome` | string | Cognome dell'utente |
| `$bot->inline_username` | string | Username dell'utente |
| `$bot->inline_lingua` | string | Codice lingua dell'utente |
| `$bot->inline_query` | string | Testo della query |
| `$bot->inline_offset` | string | Offset di paginazione |
| `$bot->inline_is_bot` | bool | Se l'utente e' un bot |

### Proprieta' Canale

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->canale_id` | int | ID del canale |
| `$bot->testo_canale` | string | Testo del post del canale |
| `$bot->didascalia` | string | Didascalia del post del canale |

### Proprieta' Nuovi Membri

| Proprieta' | Tipo | Descrizione |
|---|---|---|
| `$bot->nuovo_membro` | array | Oggetto completo del nuovo membro |
| `$bot->nuovo_membro_id` | int | ID utente del nuovo membro |
| `$bot->nuovo_membro_nome` | string | Nome del nuovo membro |
| `$bot->nuovo_membro_cognome` | string | Cognome del nuovo membro |
| `$bot->nuovo_membro_username` | string | Username del nuovo membro |
| `$bot->nuovo_membro_is_bot` | bool | Se il nuovo membro e' un bot |
| `$bot->nuovi_membri` | array | Array di nuovi membri (quando entrano in piu' contemporaneamente) |
| `$bot->nuovo_partecipante` | array | Oggetto nuovo partecipante |

---

## Metodi

### Invio Messaggi

#### `sendMessage()`

Invia un messaggio di testo.

```php
sendMessage($user_id, $text, $keyboard = false, $type = false, $risposta = false, $forceReply = false, $notifica = false, $parse_mode = 'HTML', $disableWebPagePreview = true)
```

| Parametro | Tipo | Default | Descrizione |
|---|---|---|---|
| `$user_id` | int | *obbligatorio* | ID chat destinataria |
| `$text` | string | *obbligatorio* | Testo del messaggio (supporta HTML/Markdown) |
| `$keyboard` | string | `false` | Dati JSON della tastiera |
| `$type` | string | `false` | `'fisica'` per reply keyboard, `'inline'` per inline keyboard |
| `$risposta` | int | `false` | ID messaggio a cui rispondere |
| `$forceReply` | bool | `false` | Forza l'utente a rispondere |
| `$notifica` | bool | `false` | `true` per inviare in modalita' silenziosa |
| `$parse_mode` | string | `'HTML'` | `'HTML'` o `'Markdown'` |
| `$disableWebPagePreview` | bool | `true` | Disabilita l'anteprima dei link |

**Esempio:**

```php
$bot->sendMessage($bot->user_id, 'Ciao <b>Mondo</b>!');
```

#### `sendMessage2()`

Uguale a `sendMessage()`, ma stampa la risposta JSON direttamente via `echo`. **Puo' essere usato solo una volta per richiesta.** Utile per rispondere direttamente al webhook di Telegram senza fare una richiesta HTTP aggiuntiva.

---

### Invio Media

#### `sendPhoto()`

```php
sendPhoto($user_id, $photo, $caption = '', $keyboard = false, $type = false, $file_id = true)
```

| Parametro | Tipo | Default | Descrizione |
|---|---|---|---|
| `$user_id` | int | *obbligatorio* | ID chat destinataria |
| `$photo` | string | *obbligatorio* | `file_id` della foto o percorso del file |
| `$caption` | string | `''` | Didascalia della foto (HTML supportato) |
| `$keyboard` | string | `false` | Dati JSON della tastiera |
| `$type` | string | `false` | `'fisica'` o `'inline'` |
| `$file_id` | bool | `true` | `true` se `$photo` e' un file_id, `false` se e' un percorso locale |

#### `sendDocument()`

```php
sendDocument($user_id, $document, $file_id = true, $caption = false, $parse_mode = false)
```

| Parametro | Tipo | Default | Descrizione |
|---|---|---|---|
| `$user_id` | int | *obbligatorio* | ID chat destinataria |
| `$document` | string | *obbligatorio* | `file_id` del documento o percorso del file |
| `$file_id` | bool | `true` | `true` se `$document` e' un file_id, `false` per percorso locale |
| `$caption` | string | `false` | Didascalia |
| `$parse_mode` | string | `false` | Parse mode (default `'HTML'`) |

#### `sendVideo()`

```php
sendVideo($user_id, $video, $caption = false)
```

#### `sendAudio()`

```php
sendAudio($user_id, $audio, $caption = '')
```

#### `sendVoice()`

```php
sendVoice($user_id, $voice, $caption = '')
```

#### `sendAnimation()`

```php
sendAnimation($user_id, $animation, $caption = '')
```

#### `sendSticker()`

```php
sendSticker($user_id, $sticker, $keyboard = false, $type = false, $reply_to_message_id = false, $disable_notification = false, $forceReply = false)
```

#### `sendMediaGroup()`

Invia un gruppo di foto/video come album.

```php
sendMediaGroup($user_id, $album, $caption = '')
```

---

### Modifica ed Eliminazione Messaggi

#### `editMessageText()`

```php
editMessageText($user_id, $message_id, $text, $keyboard = false, $type = false, $parse_mode = 'HTML', $disableWebPagePreview = true)
```

| Parametro | Tipo | Default | Descrizione |
|---|---|---|---|
| `$user_id` | int | *obbligatorio* | ID della chat |
| `$message_id` | int | *obbligatorio* | ID del messaggio da modificare |
| `$text` | string | *obbligatorio* | Nuovo testo |
| `$keyboard` | string | `false` | Tastiera aggiornata |
| `$type` | string | `false` | `'fisica'` o `'inline'` |
| `$parse_mode` | string | `'HTML'` | Parse mode |
| `$disableWebPagePreview` | bool | `true` | Disabilita anteprima link |

#### `editMessageText2()`

Uguale a `editMessageText()` ma stampa JSON direttamente. **Usabile solo una volta per richiesta.**

#### `deleteMessage()`

```php
deleteMessage($user_id, $message_id)
```

#### `deleteMessage2()`

Uguale a `deleteMessage()` ma stampa JSON direttamente. **Usabile solo una volta per richiesta.**

---

### Inoltro Messaggi

#### `forwardMessage()`

```php
forwardMessage($from_chat_id, $user_id, $message_id, $disable_notification = false)
```

| Parametro | Tipo | Default | Descrizione |
|---|---|---|---|
| `$from_chat_id` | int | *obbligatorio* | ID della chat sorgente |
| `$user_id` | int | *obbligatorio* | ID della chat destinazione |
| `$message_id` | int | *obbligatorio* | ID del messaggio da inoltrare |
| `$disable_notification` | bool | `false` | Invia silenziosamente |

---

### Azioni Chat

#### `sendChatAction()`

Mostra l'indicatore "sta scrivendo..." o simili.

```php
sendChatAction($chat_id, $action)
```

**Azioni disponibili:** `typing`, `upload_photo`, `record_video`, `upload_video`, `record_voice`, `upload_voice`, `upload_document`, `find_location`

---

### Gestione Chat

#### `setChatTitle()`

```php
setChatTitle($chat_id, $title)
```

#### `setChatDescription()`

```php
setChatDescription($chat_id, $description = '')
```

#### `getChat()`

Restituisce informazioni sulla chat.

```php
getChat($chat_id)
```

#### `getChatAdministrators()`

Restituisce la lista degli amministratori della chat.

```php
getChatAdministrators($chat_id)
```

#### `getChatMembersCount()`

Restituisce il numero di membri in una chat.

```php
getChatMembersCount($chat_id)
```

#### `getChatMember()`

Restituisce informazioni su un membro specifico della chat.

```php
getChatMember($chat_id, $user_id)
```

#### `leaveChat()`

Fa uscire il bot da un gruppo.

```php
leaveChat($chat_id)
```

#### `leaveChat2()`

Uguale a `leaveChat()` ma stampa JSON direttamente. **Usabile solo una volta per richiesta.**

---

### Gestione Membri

#### `kickChatMember()`

Banna un utente da un gruppo.

```php
kickChatMember($chat_id, $user_id, $until_date = false)
```

| Parametro | Tipo | Default | Descrizione |
|---|---|---|---|
| `$chat_id` | int | *obbligatorio* | ID della chat |
| `$user_id` | int | *obbligatorio* | Utente da bannare |
| `$until_date` | int | `false` | Timestamp Unix fino a cui dura il ban. `0` o `false` = permanente |

#### `unbanChatMember()`

```php
unbanChatMember($chat_id, $user_id)
```

#### `restrictChatMember()`

Limita i permessi di un utente.

```php
restrictChatMember($chat_id, $user_id, $perms = false, $until_date = 0)
```

| Parametro | Tipo | Default | Descrizione |
|---|---|---|---|
| `$chat_id` | int | *obbligatorio* | ID della chat |
| `$user_id` | int | *obbligatorio* | Utente da limitare |
| `$perms` | array | `false` | Array associativo dei permessi (es. `['can_send_messages' => false]`) |
| `$until_date` | int | `0` | Timestamp Unix. `0` = permanente |

#### `promoteChatMember()`

Promuove un utente ad amministratore.

```php
promoteChatMember($chat_id, $user_id, $perms = [])
```

| Parametro | Tipo | Default | Descrizione |
|---|---|---|---|
| `$chat_id` | int | *obbligatorio* | ID della chat |
| `$user_id` | int | *obbligatorio* | Utente da promuovere |
| `$perms` | array | `[]` | Permessi admin (es. `['can_delete_messages' => true, 'can_restrict_members' => true]`) |

---

### Gestione Pin

#### `pinChatMessage()`

```php
pinChatMessage($chat_id, $message_id, $disable_notification = false)
```

#### `unpinChatMessage()`

```php
unpinChatMessage($chat_id, $message_id = false)
```

#### `unpinAllChatMessages()`

```php
unpinAllChatMessages($chat_id)
```

---

### Gestione Sticker

#### `getStickerSet()`

```php
getStickerSet($name)
```

#### `uploadStickerFile()`

```php
uploadStickerFile($user_id, $png_sticker)
```

#### `setChatStickerSet()`

```php
setChatStickerSet($chat_id, $sticker_set_name)
```

#### `deleteChatStickerSet()`

```php
deleteChatStickerSet($chat_id)
```

---

### Link Invito

#### `exportChatInviteLink()`

```php
exportChatInviteLink($chat_id)
```

#### `revokeChatInviteLink()`

```php
revokeChatInviteLink($chat_id, $invite_link)
```

---

### Callback Query

#### `answerCallbackQuery()`

Risponde alla pressione di un bottone (inline keyboard).

```php
answerCallbackQuery($callback_query_id, $text, $show_alert = true)
```

| Parametro | Tipo | Default | Descrizione |
|---|---|---|---|
| `$callback_query_id` | string | *obbligatorio* | ID della callback query |
| `$text` | string | *obbligatorio* | Testo della notifica |
| `$show_alert` | bool | `true` | `true` per popup alert, `false` per notifica in alto |

---

### Inline Query

#### `gestisciInlineQuery()`

Risponde a una inline query.

```php
gestisciInlineQuery($inlineData, $switchText = 'Ritorna al bot', $switchParameter = 123, $cacheTime = 0)
```

| Parametro | Tipo | Default | Descrizione |
|---|---|---|---|
| `$inlineData` | array | *obbligatorio* | Array di oggetti [InlineQueryResult](https://core.telegram.org/bots/api#inlinequeryresult) |
| `$switchText` | string | `'Ritorna al bot'` | Testo del bottone per tornare alla chat privata |
| `$switchParameter` | mixed | `123` | Parametro di deep-linking |
| `$cacheTime` | int | `0` | Tempo di cache in secondi |

---

### Gestione Webhook

#### `setWebhook()`

```php
setWebhook($token = false, $url = '', $max_connections = 40, $allowed_updates = '')
```

#### `getWebhookInfo()`

```php
getWebhookInfo($token = false)
```

#### `deleteWebhook()`

```php
deleteWebhook($token = false)
```

---

## Esempi Pratici

### Bot Echo

Un bot che ripete ogni messaggio ricevuto:

```php
<?php
include 'bot.php';
$bot = new Bot('TOKEN_DEL_TUO_BOT');

if (isset($bot->text)) {
    $bot->sendMessage($bot->user_id, $bot->text);
}
```

### Gestione di Comandi Multipli

```php
<?php
include 'bot.php';
$bot = new Bot('TOKEN_DEL_TUO_BOT');

if ($bot->text == '/start') {
    $bot->sendMessage($bot->user_id, 'Benvenuto! Usa /help per vedere i comandi disponibili.');
}

if ($bot->text == '/help') {
    $bot->sendMessage($bot->user_id,
        '<b>Comandi disponibili:</b>' . PHP_EOL .
        '/start - Avvia il bot' . PHP_EOL .
        '/help - Mostra questo messaggio' . PHP_EOL .
        '/info - Informazioni sul bot'
    );
}

if ($bot->text == '/info') {
    $bot->sendMessage($bot->user_id,
        'Il tuo ID utente: <code>' . $bot->user_id . '</code>' . PHP_EOL .
        'Il tuo nome: ' . $bot->nome . PHP_EOL .
        'Tipo di chat: ' . $bot->tipo_chat
    );
}
```

### Tastiera Inline

```php
<?php
include 'bot.php';
$bot = new Bot('TOKEN_DEL_TUO_BOT');

if ($bot->text == '/menu') {
    $keyboard = '[{"text":"Opzione 1","callback_data":"opzione1"},{"text":"Opzione 2","callback_data":"opzione2"}],[{"text":"Opzione 3","callback_data":"opzione3"}]';
    $bot->sendMessage($bot->user_id, 'Scegli un\'opzione:', $keyboard, 'inline');
}
```

### Tastiera Fisica (Reply Keyboard)

```php
<?php
include 'bot.php';
$bot = new Bot('TOKEN_DEL_TUO_BOT');

if ($bot->text == '/tastiera') {
    $keyboard = '[{"text":"Bottone 1"},{"text":"Bottone 2"}],[{"text":"Bottone 3"}]';
    $bot->sendMessage($bot->user_id, 'Ecco una tastiera:', $keyboard, 'fisica');
}
```

### Gestione delle Callback Query

```php
<?php
include 'bot.php';
$bot = new Bot('TOKEN_DEL_TUO_BOT');

if (isset($bot->callback_data)) {
    if ($bot->callback_data == 'opzione1') {
        $bot->answerCallbackQuery($bot->callback_query_id, 'Hai scelto Opzione 1!');
        $bot->editMessageText($bot->callback_chat_id, $bot->callback_message_id, 'Hai selezionato: <b>Opzione 1</b>');
    }

    if ($bot->callback_data == 'opzione2') {
        $bot->answerCallbackQuery($bot->callback_query_id, 'Hai scelto Opzione 2!', false);
        $bot->editMessageText($bot->callback_chat_id, $bot->callback_message_id, 'Hai selezionato: <b>Opzione 2</b>');
    }
}
```

### Invio di Foto

```php
// Invia una foto tramite file_id (es. ricevuto da un altro messaggio)
$bot->sendPhoto($bot->user_id, $bot->foto, 'Bella foto!');

// Invia un file locale
$bot->sendPhoto($bot->user_id, '/percorso/immagine.jpg', 'Una foto locale', false, false, false);
```

### Benvenuto Nuovi Membri

```php
<?php
include 'bot.php';
$bot = new Bot('TOKEN_DEL_TUO_BOT');

if (isset($bot->nuovo_membro_id)) {
    $benvenuto = 'Benvenuto, <b>' . $bot->nuovo_membro_nome . '</b>! Leggi le regole del gruppo.';
    $bot->sendMessage($bot->chat_id, $benvenuto);
}
```

### Moderazione di Gruppo

```php
// Banna un utente in modo permanente
$bot->kickChatMember($bot->chat_id, $user_id_da_bannare);

// Banna un utente per 24 ore
$bot->kickChatMember($bot->chat_id, $user_id_da_bannare, time() + 86400);

// Rimuovi il ban
$bot->unbanChatMember($bot->chat_id, $user_id_da_sbannare);

// Silenzia un utente (non puo' inviare messaggi)
$bot->restrictChatMember($bot->chat_id, $user_id, ['can_send_messages' => false]);

// Promuovi un utente ad admin
$bot->promoteChatMember($bot->chat_id, $user_id, [
    'can_delete_messages' => true,
    'can_restrict_members' => true,
    'can_pin_messages' => true
]);
```

### Rilevamento Messaggi Inoltrati

```php
<?php
include 'bot.php';
$bot = new Bot('TOKEN_DEL_TUO_BOT');

if (isset($bot->forward_date)) {
    if (isset($bot->forward_sender_name)) {
        // Inoltro anonimo
        $bot->sendMessage($bot->user_id, 'Messaggio inoltrato da: ' . $bot->forward_sender_name);
    } else if (isset($bot->forward_nome)) {
        // Inoltro da un utente
        $bot->sendMessage($bot->user_id, 'Messaggio inoltrato da: ' . $bot->forward_nome);
    } else if (isset($bot->forward_title)) {
        // Inoltro da un canale
        $bot->sendMessage($bot->user_id, 'Messaggio inoltrato dal canale: ' . $bot->forward_title);
    }
}
```

### Risposta ai Messaggi

```php
// Rispondi al messaggio corrente
$bot->sendMessage($bot->user_id, 'Questa e' una risposta!', false, false, $bot->message_id);

// Controlla se il messaggio e' una risposta a un altro messaggio
if (isset($bot->reply_message_id)) {
    $bot->sendMessage($bot->user_id,
        'Hai risposto a un messaggio di: ' . $bot->reply_nome . PHP_EOL .
        'Testo originale: ' . $bot->reply_text
    );
}
```

### Mostrare l'Indicatore di Scrittura

```php
// Mostra l'indicatore "sta scrivendo..."
$bot->sendChatAction($bot->user_id, 'typing');

// Mostra l'indicatore "sta inviando una foto..."
$bot->sendChatAction($bot->user_id, 'upload_photo');
```

---

## Metodi v1 vs v2

Alcuni metodi hanno due versioni (es. `sendMessage` / `sendMessage2`, `editMessageText` / `editMessageText2`, `deleteMessage` / `deleteMessage2`, `leaveChat` / `leaveChat2`):

| | Versione 1 (es. `sendMessage`) | Versione 2 (es. `sendMessage2`) |
|---|---|---|
| **Meccanismo** | Fa una richiesta HTTP alla Telegram API via cURL e **restituisce** la risposta | Stampa il JSON direttamente via `echo` (risposta al webhook) |
| **Valore di ritorno** | Restituisce la risposta API come array PHP | Nessun valore di ritorno (stampa JSON) |
| **Limite di utilizzo** | Puo' essere chiamato piu' volte per richiesta | **Usabile solo una volta per richiesta** |
| **Quando usarlo** | Uso generale, piu' comune | Piu' veloce: evita una chiamata HTTP extra rispondendo direttamente al webhook |

**Quando usare la Versione 2:** Se il tuo bot deve eseguire solo un'azione per update (es. inviare un solo messaggio), la Versione 2 e' leggermente piu' veloce perche' usa la risposta al webhook invece di fare una chiamata HTTP separata a Telegram.

---

## Licenza

Questo progetto e' distribuito sotto la **Licenza MIT** - vedi il file [LICENSE](LICENSE) per i dettagli.

Puoi liberamente usare, modificare, distribuire e vendere questo software, a patto di **mantenere l'attribuzione** alla repository originale ([Adriapp/TeleApp](https://github.com/Adriapp/TeleApp)) e il testo della licenza nei crediti.
