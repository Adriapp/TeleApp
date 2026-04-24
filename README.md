# TeleApp

[![License: MIT](https://img.shields.io/github/license/Adriapp/TeleApp)](https://github.com/Adriapp/TeleApp/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/Adriapp/TeleApp)](https://github.com/Adriapp/TeleApp/issues)
[![GitHub stars](https://img.shields.io/github/stars/Adriapp/TeleApp)](https://github.com/Adriapp/TeleApp/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/Adriapp/TeleApp)](https://github.com/Adriapp/TeleApp/network)

> **[Leggi la documentazione in Italiano](README_IT.md)**

TeleApp is a lightweight PHP framework for building Telegram bots. It provides an object-oriented wrapper around the [Telegram Bot API](https://core.telegram.org/bots/api), allowing you to easily access message data through object properties and call methods to interact with the API.

---

## Table of Contents

- [Repository Structure](#repository-structure)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
  - [Webhook Setup via PHP](#webhook-setup-via-php)
  - [Webhook Setup with Self-Signed Certificate](#webhook-setup-with-self-signed-certificate-setwebhookphp)
- [Quick Start](#quick-start)
- [Bot Properties](#bot-properties)
  - [User Properties](#user-properties)
  - [Chat Properties](#chat-properties)
  - [Message Properties](#message-properties)
  - [Media Properties](#media-properties)
  - [Forwarded Message Properties](#forwarded-message-properties)
  - [Reply Properties](#reply-properties)
  - [Callback Query Properties](#callback-query-properties)
  - [Inline Query Properties](#inline-query-properties)
  - [Channel Properties](#channel-properties)
  - [New Member Properties](#new-member-properties)
- [Methods](#methods)
  - [Sending Messages](#sending-messages)
  - [Sending Media](#sending-media)
  - [Editing and Deleting Messages](#editing-and-deleting-messages)
  - [Forwarding Messages](#forwarding-messages)
  - [Chat Actions](#chat-actions)
  - [Chat Management](#chat-management)
  - [Member Management](#member-management)
  - [Pin Management](#pin-management)
  - [Sticker Management](#sticker-management)
  - [Invite Links](#invite-links)
  - [Callback Queries](#callback-queries)
  - [Inline Queries](#inline-queries)
  - [Webhook Management](#webhook-management)
- [Practical Examples](#practical-examples)
  - [Echo Bot](#echo-bot)
  - [Handling Multiple Commands](#handling-multiple-commands)
  - [Inline Keyboard](#inline-keyboard)
  - [Physical Keyboard (Reply Keyboard)](#physical-keyboard-reply-keyboard)
  - [Handling Callback Queries](#handling-callback-queries)
  - [Sending Photos](#sending-photos)
  - [Welcoming New Members](#welcoming-new-members)
  - [Group Moderation](#group-moderation)
  - [Detecting Forwarded Messages](#detecting-forwarded-messages)
  - [Replying to Messages](#replying-to-messages)
  - [Showing Typing Indicator](#showing-typing-indicator)
- [Version 1 vs Version 2 Methods](#version-1-vs-version-2-methods)
- [License](#license)

---

## Repository Structure

| File | Description |
|---|---|
| `bot.php` | Main `Bot` class - OOP wrapper for the Telegram Bot API. Parses incoming updates and provides methods for all API interactions. |
| `setWebhook.php` | CLI utility for configuring the webhook when using self-signed SSL certificates. |
| `LICENSE` | MIT License. |
| `README.md` | This documentation (English). |
| `README_IT.md` | Documentation in Italian. |

---

## Requirements

- **PHP** with **cURL** extension enabled
- A web server with **HTTPS** (required by Telegram)
- A Telegram Bot **API token** (obtained from [@BotFather](https://t.me/BotFather))

---

## Installation

```bash
sudo apt install git
git clone https://github.com/Adriapp/TeleApp
```

---

## Configuration

### 1. Create a bot on Telegram

1. Open Telegram and search for [@BotFather](https://t.me/BotFather)
2. Send the command `/newbot`
3. Follow the instructions: choose a name and a username for your bot
4. BotFather will provide an **API token** (e.g. `123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11`)

### 2. Create your bot file

Create a PHP file (e.g. `index.php`) on your HTTPS-enabled server:

```php
<?php
include 'bot.php';
$bot = new Bot('YOUR_BOT_TOKEN');

// Your bot logic here
```

### Webhook Setup via PHP

Once your file is accessible via HTTPS, set the webhook with:

```php
<?php
include 'bot.php';
$bot = new Bot('YOUR_BOT_TOKEN');
$bot->setWebhook(false, 'https://yourdomain.com/path/to/index.php');
```

Run this file once in your browser, then remove or comment out the `setWebhook` call.

You can verify the webhook status with:

```php
$info = $bot->getWebhookInfo();
print_r($info);
```

And delete the webhook with:

```php
$bot->deleteWebhook();
```

### Webhook Setup with Self-Signed Certificate (`setWebhook.php`)

If your server uses a **self-signed SSL certificate**, use the `setWebhook.php` utility from the command line:

```bash
php setWebhook.php /path/to/certificate.crt BOT_TOKEN DOMAIN/path/to/bot.php
```

**Example:**

```bash
php setWebhook.php /etc/ssl/certs/selfsigned-nginx.crt 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11 12.34.56.78/telegram/bots/mybot/index.php
```

**Arguments:**

| # | Argument | Description |
|---|---|---|
| 1 | Certificate path | Absolute path to the `.crt` file |
| 2 | Bot token | Token received from BotFather |
| 3 | Bot URL | Domain or IP + path to your bot file (without `https://`, it is added automatically) |

**Possible outputs:**
- `Done, webhook was set` - Success
- `Error, wrong bot token` - Invalid token
- JSON response - Other API errors

---

## Quick Start

```php
<?php
include 'bot.php';
$bot = new Bot('YOUR_BOT_TOKEN');

if ($bot->text == '/start') {
    $bot->sendMessage($bot->user_id, 'Hello! The bot is working!');
}
```

---

## Bot Properties

When a Telegram update is received, the `Bot` constructor automatically parses the JSON and populates the following properties.

### User Properties

| Property | Type | Description |
|---|---|---|
| `$bot->user_id` | int | Sender's Telegram user ID |
| `$bot->nome` | string | User's first name |
| `$bot->cognome` | string | User's last name |
| `$bot->username` | string | User's Telegram username |
| `$bot->lingua` | string | User's language code (e.g. `it`, `en`) |
| `$bot->is_bot` | bool | Whether the sender is a bot |

### Chat Properties

| Property | Type | Description |
|---|---|---|
| `$bot->chat_id` | int | Chat ID (same as `user_id` in private chats) |
| `$bot->username_chat` | string | Chat username |
| `$bot->tipo_chat` | string | Chat type: `private`, `group`, `supergroup`, or `channel` |
| `$bot->nome_chat` | string | Chat name (first name in private, title in groups) |

### Message Properties

| Property | Type | Description |
|---|---|---|
| `$bot->text` | string | Message text content |
| `$bot->message_id` | int | Unique message ID |
| `$bot->update_id` | int | Update ID |
| `$bot->time` | int | Message timestamp (Unix) |
| `$bot->edit` | bool | `true` if the message was edited |
| `$bot->messageType` | string | `'message'` or `'edited_message'` |
| `$bot->entities` | array | Message entities (mentions, hashtags, URLs, etc.) |

### Media Properties

#### Photos

| Property | Type | Description |
|---|---|---|
| `$bot->foto` | string | Photo `file_id` |
| `$bot->didascalia` | string | Photo caption |
| `$bot->file_unique_id` | string | Unique file identifier |

#### Documents

| Property | Type | Description |
|---|---|---|
| `$bot->file` | string | Document `file_id` |
| `$bot->nome_file` | string | File name |
| `$bot->tipo_file` | string | MIME type (e.g. `application/pdf`) |
| `$bot->size_file` | int | File size in bytes |

#### Videos

| Property | Type | Description |
|---|---|---|
| `$bot->video` | string | Video `file_id` |
| `$bot->tipo_video` | string | MIME type |
| `$bot->durata_video` | int | Duration in seconds |
| `$bot->width_video` | int | Width in pixels |
| `$bot->height_video` | int | Height in pixels |
| `$bot->size_video` | int | File size in bytes |

#### Animations (GIF)

| Property | Type | Description |
|---|---|---|
| `$bot->gif` | string | Animation `file_id` |
| `$bot->tipo_gif` | string | MIME type |
| `$bot->durata_gif` | int | Duration in seconds |
| `$bot->width_gif` | int | Width in pixels |
| `$bot->height_gif` | int | Height in pixels |
| `$bot->size_gif` | int | File size in bytes |

#### Stickers

| Property | Type | Description |
|---|---|---|
| `$bot->sticker` | string | Sticker `file_id` |
| `$bot->nome_sticker` | string | Sticker set name |
| `$bot->emoji_sticker` | string | Associated emoji |
| `$bot->is_animated` | bool | Whether the sticker is animated |
| `$bot->width_sticker` | int | Width in pixels |
| `$bot->height_sticker` | int | Height in pixels |
| `$bot->size_sticker` | int | File size in bytes |

### Forwarded Message Properties

| Property | Type | Description |
|---|---|---|
| `$bot->forward_sender_name` | string | Original sender's name (if forwarded anonymously) |
| `$bot->forward_date` | int | Original message timestamp |
| `$bot->forward_text` | string | Forwarded message text |
| `$bot->forward_chat_id` | int | Original sender/chat ID |
| `$bot->forward_is_bot` | bool | Whether the original sender is a bot |
| `$bot->forward_nome` | string | Original sender's first name |
| `$bot->forward_cognome` | string | Original sender's last name |
| `$bot->forward_username` | string | Original sender's username |
| `$bot->forward_title` | string | Original chat/channel title (if forwarded from a channel) |
| `$bot->forward_type` | string | Original chat type (if forwarded from a channel) |
| `$bot->forward_from_message_id` | int | Original message ID in the source chat |

### Reply Properties

| Property | Type | Description |
|---|---|---|
| `$bot->reply_message_id` | int | ID of the replied-to message |
| `$bot->reply_user_id` | int | User ID of the original message sender |
| `$bot->reply_nome` | string | First name of the original sender |
| `$bot->reply_cognome` | string | Last name of the original sender |
| `$bot->reply_username` | string | Username of the original sender |
| `$bot->reply_text` | string | Text of the replied-to message |
| `$bot->reply_is_bot` | bool | Whether the original sender is a bot |
| `$bot->reply_tipo` | string | Type |
| `$bot->reply_time` | int | Timestamp of the original message |
| `$bot->reply_chat_id` | int | Chat ID of the reply |
| `$bot->reply_chat_nome` | string | Chat first name |
| `$bot->reply_chat_cognome` | string | Chat last name |
| `$bot->reply_chat_tipo` | string | Chat type |
| `$bot->reply_entities` | array | Entities in the replied-to message |

**Reply to a forwarded message:**

| Property | Type | Description |
|---|---|---|
| `$bot->chat_id_reply_forward` | int | Original sender's ID |
| `$bot->is_bot_reply_forward` | bool | Whether original sender is a bot |
| `$bot->nome_reply_forward` | string | Original sender's first name |
| `$bot->cognome_reply_forward` | string | Original sender's last name |
| `$bot->time_reply_forward` | int | Forward timestamp |
| `$bot->text_reply` | string | Text of the replied-to forwarded message |

### Callback Query Properties

| Property | Type | Description |
|---|---|---|
| `$bot->callback_query_id` | string | Callback query ID |
| `$bot->callback_user_id` | int | User who clicked the button |
| `$bot->callback_data` | string | Data associated with the button |
| `$bot->callback_message_id` | int | ID of the message containing the button |
| `$bot->callback_text` | string | Text of the message containing the button |
| `$bot->callback_chat_id` | int | Chat ID |
| `$bot->callback_nome` | string | User's first name |
| `$bot->callback_cognome` | string | User's last name |
| `$bot->callback_lingua` | string | User's language code |
| `$bot->callback_is_bot` | bool | Whether the user is a bot |
| `$bot->callback_chat_title` | string | Chat title |
| `$bot->callback_chat_type` | string | Chat type |
| `$bot->callback_time` | int | Message timestamp |
| `$bot->callback_entities` | array | Message entities |
| `$bot->callback_inline_keyboard` | array | Inline keyboard data |
| `$bot->callback_chat_instance` | string | Unique chat instance identifier |
| `$bot->callback_bot_id` | int | Bot's user ID |
| `$bot->callback_bot_is_bot` | bool | Always `true` |
| `$bot->callback_bot_nome` | string | Bot's name |
| `$bot->callback_bot_username` | string | Bot's username |

### Inline Query Properties

| Property | Type | Description |
|---|---|---|
| `$bot->inline_id` | string | Inline query ID |
| `$bot->inline_user_id` | int | User who initiated the inline query |
| `$bot->inline_nome` | string | User's first name |
| `$bot->inline_cognome` | string | User's last name |
| `$bot->inline_username` | string | User's username |
| `$bot->inline_lingua` | string | User's language code |
| `$bot->inline_query` | string | Query text |
| `$bot->inline_offset` | string | Pagination offset |
| `$bot->inline_is_bot` | bool | Whether the user is a bot |

### Channel Properties

| Property | Type | Description |
|---|---|---|
| `$bot->canale_id` | int | Channel ID |
| `$bot->testo_canale` | string | Channel post text |
| `$bot->didascalia` | string | Channel post caption |

### New Member Properties

| Property | Type | Description |
|---|---|---|
| `$bot->nuovo_membro` | array | Full new member object |
| `$bot->nuovo_membro_id` | int | New member's user ID |
| `$bot->nuovo_membro_nome` | string | New member's first name |
| `$bot->nuovo_membro_cognome` | string | New member's last name |
| `$bot->nuovo_membro_username` | string | New member's username |
| `$bot->nuovo_membro_is_bot` | bool | Whether the new member is a bot |
| `$bot->nuovi_membri` | array | Array of new members (when multiple join at once) |
| `$bot->nuovo_partecipante` | array | New participant object |

---

## Methods

### Sending Messages

#### `sendMessage()`

Sends a text message.

```php
sendMessage($user_id, $text, $keyboard = false, $type = false, $risposta = false, $forceReply = false, $notifica = false, $parse_mode = 'HTML', $disableWebPagePreview = true)
```

| Parameter | Type | Default | Description |
|---|---|---|---|
| `$user_id` | int | *required* | Target chat ID |
| `$text` | string | *required* | Message text (supports HTML/Markdown) |
| `$keyboard` | string | `false` | Keyboard JSON data |
| `$type` | string | `false` | `'fisica'` for reply keyboard, `'inline'` for inline keyboard |
| `$risposta` | int | `false` | Message ID to reply to |
| `$forceReply` | bool | `false` | Force user to reply |
| `$notifica` | bool | `false` | `true` to send silently |
| `$parse_mode` | string | `'HTML'` | `'HTML'` or `'Markdown'` |
| `$disableWebPagePreview` | bool | `true` | Disable link previews |

**Example:**

```php
$bot->sendMessage($bot->user_id, 'Hello <b>World</b>!');
```

#### `sendMessage2()`

Same as `sendMessage()`, but outputs the JSON response directly via `echo`. **Can only be used once per request.** Useful for responding directly to Telegram's webhook without making an additional HTTP request.

---

### Sending Media

#### `sendPhoto()`

```php
sendPhoto($user_id, $photo, $caption = '', $keyboard = false, $type = false, $file_id = true)
```

| Parameter | Type | Default | Description |
|---|---|---|---|
| `$user_id` | int | *required* | Target chat ID |
| `$photo` | string | *required* | Photo `file_id` or file path |
| `$caption` | string | `''` | Photo caption (HTML supported) |
| `$keyboard` | string | `false` | Keyboard JSON data |
| `$type` | string | `false` | `'fisica'` or `'inline'` |
| `$file_id` | bool | `true` | `true` if `$photo` is a file_id, `false` if it's a local file path |

#### `sendDocument()`

```php
sendDocument($user_id, $document, $file_id = true, $caption = false, $parse_mode = false)
```

| Parameter | Type | Default | Description |
|---|---|---|---|
| `$user_id` | int | *required* | Target chat ID |
| `$document` | string | *required* | Document `file_id` or file path |
| `$file_id` | bool | `true` | `true` if `$document` is a file_id, `false` for local path |
| `$caption` | string | `false` | Caption |
| `$parse_mode` | string | `false` | Parse mode (defaults to `'HTML'`) |

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

Sends a group of photos/videos as an album.

```php
sendMediaGroup($user_id, $album, $caption = '')
```

---

### Editing and Deleting Messages

#### `editMessageText()`

```php
editMessageText($user_id, $message_id, $text, $keyboard = false, $type = false, $parse_mode = 'HTML', $disableWebPagePreview = true)
```

| Parameter | Type | Default | Description |
|---|---|---|---|
| `$user_id` | int | *required* | Chat ID |
| `$message_id` | int | *required* | ID of the message to edit |
| `$text` | string | *required* | New text |
| `$keyboard` | string | `false` | Updated keyboard |
| `$type` | string | `false` | `'fisica'` or `'inline'` |
| `$parse_mode` | string | `'HTML'` | Parse mode |
| `$disableWebPagePreview` | bool | `true` | Disable link previews |

#### `editMessageText2()`

Same as `editMessageText()` but outputs JSON directly. **Can only be used once per request.**

#### `deleteMessage()`

```php
deleteMessage($user_id, $message_id)
```

#### `deleteMessage2()`

Same as `deleteMessage()` but outputs JSON directly. **Can only be used once per request.**

---

### Forwarding Messages

#### `forwardMessage()`

```php
forwardMessage($from_chat_id, $user_id, $message_id, $disable_notification = false)
```

| Parameter | Type | Default | Description |
|---|---|---|---|
| `$from_chat_id` | int | *required* | Source chat ID |
| `$user_id` | int | *required* | Destination chat ID |
| `$message_id` | int | *required* | Message ID to forward |
| `$disable_notification` | bool | `false` | Send silently |

---

### Chat Actions

#### `sendChatAction()`

Shows a typing indicator or similar action.

```php
sendChatAction($chat_id, $action)
```

**Available actions:** `typing`, `upload_photo`, `record_video`, `upload_video`, `record_voice`, `upload_voice`, `upload_document`, `find_location`

---

### Chat Management

#### `setChatTitle()`

```php
setChatTitle($chat_id, $title)
```

#### `setChatDescription()`

```php
setChatDescription($chat_id, $description = '')
```

#### `getChat()`

Returns chat information.

```php
getChat($chat_id)
```

#### `getChatAdministrators()`

Returns a list of chat administrators.

```php
getChatAdministrators($chat_id)
```

#### `getChatMembersCount()`

Returns the number of members in a chat.

```php
getChatMembersCount($chat_id)
```

#### `getChatMember()`

Returns information about a specific chat member.

```php
getChatMember($chat_id, $user_id)
```

#### `leaveChat()`

Makes the bot leave a group.

```php
leaveChat($chat_id)
```

#### `leaveChat2()`

Same as `leaveChat()` but outputs JSON directly. **Can only be used once per request.**

---

### Member Management

#### `kickChatMember()`

Bans a user from a group.

```php
kickChatMember($chat_id, $user_id, $until_date = false)
```

| Parameter | Type | Default | Description |
|---|---|---|---|
| `$chat_id` | int | *required* | Chat ID |
| `$user_id` | int | *required* | User to ban |
| `$until_date` | int | `false` | Unix timestamp until which the ban lasts. `0` or `false` = permanent |

#### `unbanChatMember()`

```php
unbanChatMember($chat_id, $user_id)
```

#### `restrictChatMember()`

Restricts a user's permissions.

```php
restrictChatMember($chat_id, $user_id, $perms = false, $until_date = 0)
```

| Parameter | Type | Default | Description |
|---|---|---|---|
| `$chat_id` | int | *required* | Chat ID |
| `$user_id` | int | *required* | User to restrict |
| `$perms` | array | `false` | Associative array of permissions (e.g. `['can_send_messages' => false]`) |
| `$until_date` | int | `0` | Unix timestamp. `0` = permanent |

#### `promoteChatMember()`

Promotes a user to administrator.

```php
promoteChatMember($chat_id, $user_id, $perms = [])
```

| Parameter | Type | Default | Description |
|---|---|---|---|
| `$chat_id` | int | *required* | Chat ID |
| `$user_id` | int | *required* | User to promote |
| `$perms` | array | `[]` | Admin permissions (e.g. `['can_delete_messages' => true, 'can_restrict_members' => true]`) |

---

### Pin Management

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

### Sticker Management

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

### Invite Links

#### `exportChatInviteLink()`

```php
exportChatInviteLink($chat_id)
```

#### `revokeChatInviteLink()`

```php
revokeChatInviteLink($chat_id, $invite_link)
```

---

### Callback Queries

#### `answerCallbackQuery()`

Responds to a button press (inline keyboard).

```php
answerCallbackQuery($callback_query_id, $text, $show_alert = true)
```

| Parameter | Type | Default | Description |
|---|---|---|---|
| `$callback_query_id` | string | *required* | Callback query ID |
| `$text` | string | *required* | Notification text |
| `$show_alert` | bool | `true` | `true` for alert popup, `false` for top notification |

---

### Inline Queries

#### `gestisciInlineQuery()`

Responds to an inline query.

```php
gestisciInlineQuery($inlineData, $switchText = 'Ritorna al bot', $switchParameter = 123, $cacheTime = 0)
```

| Parameter | Type | Default | Description |
|---|---|---|---|
| `$inlineData` | array | *required* | Array of [InlineQueryResult](https://core.telegram.org/bots/api#inlinequeryresult) objects |
| `$switchText` | string | `'Ritorna al bot'` | Button text to switch to private chat |
| `$switchParameter` | mixed | `123` | Deep-linking parameter |
| `$cacheTime` | int | `0` | Cache time in seconds |

---

### Webhook Management

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

## Practical Examples

### Echo Bot

A bot that repeats every message it receives:

```php
<?php
include 'bot.php';
$bot = new Bot('YOUR_BOT_TOKEN');

if (isset($bot->text)) {
    $bot->sendMessage($bot->user_id, $bot->text);
}
```

### Handling Multiple Commands

```php
<?php
include 'bot.php';
$bot = new Bot('YOUR_BOT_TOKEN');

if ($bot->text == '/start') {
    $bot->sendMessage($bot->user_id, 'Welcome! Use /help to see available commands.');
}

if ($bot->text == '/help') {
    $bot->sendMessage($bot->user_id,
        '<b>Available commands:</b>' . PHP_EOL .
        '/start - Start the bot' . PHP_EOL .
        '/help - Show this help message' . PHP_EOL .
        '/info - Bot information'
    );
}

if ($bot->text == '/info') {
    $bot->sendMessage($bot->user_id,
        'Your user ID: <code>' . $bot->user_id . '</code>' . PHP_EOL .
        'Your name: ' . $bot->nome . PHP_EOL .
        'Chat type: ' . $bot->tipo_chat
    );
}
```

### Inline Keyboard

```php
<?php
include 'bot.php';
$bot = new Bot('YOUR_BOT_TOKEN');

if ($bot->text == '/menu') {
    $keyboard = '[{"text":"Option 1","callback_data":"option1"},{"text":"Option 2","callback_data":"option2"}],[{"text":"Option 3","callback_data":"option3"}]';
    $bot->sendMessage($bot->user_id, 'Choose an option:', $keyboard, 'inline');
}
```

### Physical Keyboard (Reply Keyboard)

```php
<?php
include 'bot.php';
$bot = new Bot('YOUR_BOT_TOKEN');

if ($bot->text == '/keyboard') {
    $keyboard = '[{"text":"Button 1"},{"text":"Button 2"}],[{"text":"Button 3"}]';
    $bot->sendMessage($bot->user_id, 'Here is a keyboard:', $keyboard, 'fisica');
}
```

### Handling Callback Queries

```php
<?php
include 'bot.php';
$bot = new Bot('YOUR_BOT_TOKEN');

if (isset($bot->callback_data)) {
    if ($bot->callback_data == 'option1') {
        $bot->answerCallbackQuery($bot->callback_query_id, 'You chose Option 1!');
        $bot->editMessageText($bot->callback_chat_id, $bot->callback_message_id, 'You selected: <b>Option 1</b>');
    }

    if ($bot->callback_data == 'option2') {
        $bot->answerCallbackQuery($bot->callback_query_id, 'You chose Option 2!', false);
        $bot->editMessageText($bot->callback_chat_id, $bot->callback_message_id, 'You selected: <b>Option 2</b>');
    }
}
```

### Sending Photos

```php
// Send a photo by file_id (e.g. received from another message)
$bot->sendPhoto($bot->user_id, $bot->foto, 'Nice photo!');

// Send a local file
$bot->sendPhoto($bot->user_id, '/path/to/image.jpg', 'A local photo', false, false, false);
```

### Welcoming New Members

```php
<?php
include 'bot.php';
$bot = new Bot('YOUR_BOT_TOKEN');

if (isset($bot->nuovo_membro_id)) {
    $welcome = 'Welcome, <b>' . $bot->nuovo_membro_nome . '</b>! Please read the group rules.';
    $bot->sendMessage($bot->chat_id, $welcome);
}
```

### Group Moderation

```php
// Ban a user permanently
$bot->kickChatMember($bot->chat_id, $user_id_to_ban);

// Ban a user for 24 hours
$bot->kickChatMember($bot->chat_id, $user_id_to_ban, time() + 86400);

// Unban a user
$bot->unbanChatMember($bot->chat_id, $user_id_to_unban);

// Mute a user (cannot send messages)
$bot->restrictChatMember($bot->chat_id, $user_id, ['can_send_messages' => false]);

// Promote a user to admin
$bot->promoteChatMember($bot->chat_id, $user_id, [
    'can_delete_messages' => true,
    'can_restrict_members' => true,
    'can_pin_messages' => true
]);
```

### Detecting Forwarded Messages

```php
<?php
include 'bot.php';
$bot = new Bot('YOUR_BOT_TOKEN');

if (isset($bot->forward_date)) {
    if (isset($bot->forward_sender_name)) {
        // Anonymous forward
        $bot->sendMessage($bot->user_id, 'Message forwarded from: ' . $bot->forward_sender_name);
    } else if (isset($bot->forward_nome)) {
        // Forward from a user
        $bot->sendMessage($bot->user_id, 'Message forwarded from: ' . $bot->forward_nome);
    } else if (isset($bot->forward_title)) {
        // Forward from a channel
        $bot->sendMessage($bot->user_id, 'Message forwarded from channel: ' . $bot->forward_title);
    }
}
```

### Replying to Messages

```php
// Reply to the current message
$bot->sendMessage($bot->user_id, 'This is a reply!', false, false, $bot->message_id);

// Check if the message is a reply to another message
if (isset($bot->reply_message_id)) {
    $bot->sendMessage($bot->user_id,
        'You replied to a message from: ' . $bot->reply_nome . PHP_EOL .
        'Original text: ' . $bot->reply_text
    );
}
```

### Showing Typing Indicator

```php
// Show "typing..." indicator
$bot->sendChatAction($bot->user_id, 'typing');

// Show "sending photo..." indicator
$bot->sendChatAction($bot->user_id, 'upload_photo');
```

---

## Version 1 vs Version 2 Methods

Some methods have two versions (e.g. `sendMessage` / `sendMessage2`, `editMessageText` / `editMessageText2`, `deleteMessage` / `deleteMessage2`, `leaveChat` / `leaveChat2`):

| | Version 1 (e.g. `sendMessage`) | Version 2 (e.g. `sendMessage2`) |
|---|---|---|
| **Mechanism** | Makes an HTTP request to the Telegram API via cURL and **returns** the response | Outputs the JSON directly via `echo` (webhook response method) |
| **Return value** | Returns the API response as a PHP array | No return value (outputs JSON) |
| **Usage limit** | Can be called multiple times per request | **Can only be used once per request** |
| **Use case** | General purpose, most common | Faster: avoids an extra HTTP call by responding directly to the webhook request |

**When to use Version 2:** If your bot only needs to perform a single action per update (e.g. send one message), Version 2 is slightly faster because it piggybacks on the webhook response instead of making a separate HTTP call to Telegram.

---

## License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

You are free to use, modify, distribute, and sell this software, provided you **credit the original repository** ([Adriapp/TeleApp](https://github.com/Adriapp/TeleApp)) and include the license text in the credits.
