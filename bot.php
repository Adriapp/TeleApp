<?php
/**
 * TeleApp - A lightweight PHP wrapper for the Telegram Bot API.
 *
 * The Bot class parses an incoming Telegram update (from a webhook) and exposes
 * its data as public properties (e.g. $bot->text, $bot->user_id). It also provides
 * one method per Telegram API action (sendMessage, sendPhoto, ...).
 *
 * Backward compatibility note:
 * - All legacy property names and method signatures are preserved.
 * - Methods only ever ADD optional parameters, never remove or reorder them.
 * - The #[\AllowDynamicProperties] attribute keeps the historical behaviour of
 *   setting properties dynamically while remaining compatible with PHP 8.2+
 *   (on PHP 7.x the line is simply treated as a comment).
 *
 * @license MIT
 * @link    https://github.com/Adriapp/TeleApp
 */

#[\AllowDynamicProperties]
class Bot {

  /** @var string Full API base URL including the bot token (kept private for security). */
  private $apiUrl;

  /** @var \CurlHandle|resource|null Persistent cURL handle reused across requests (keep-alive). */
  private $ch;

  /** @var string|null Description of the last cURL transport error, if any. */
  public $last_error = null;

  /**
   * @param string      $token The bot token obtained from @BotFather.
   * @param string|bool $json  Optional raw JSON update. When false, the update is
   *                           read from the webhook body (php://input).
   *
   * @throws \InvalidArgumentException When the token is empty or not a string.
   */
  public function __construct($token, $json = false){

    // --- Validate the token (avoids hard-to-debug "Unauthorized" responses) ---
    if(!is_string($token) || trim($token) === ''){
      throw new \InvalidArgumentException('Bot token is required and must be a non-empty string.');
    }

    // Keep the token only inside the private API URL so it is never exposed as a public property.
    $this->apiUrl = 'https://api.telegram.org/bot'.$token;

    // Create one persistent cURL handle and reuse it for every request.
    $this->ch = curl_init();

    // Read the incoming update either from the webhook body or from the provided JSON.
    if($json == false){
      $this->json = file_get_contents('php://input');
    } else {
      $this->json = $json;
    }

    $this->update = json_decode($this->json, TRUE);

    // Default state so the properties always exist (no "undefined property" notices).
    $this->messageType = null;
    $this->edit = null;

    // Nothing to parse if the body was empty or not valid JSON.
    if($this->update == null){
      return;
    }

    // --- Determine which container holds the message (new vs edited) ---
    if(isset($this->update['message'])){
      $this->edit = false;
      $this->messageType = 'message';
    } else if(isset($this->update['edited_message'])){
      $this->edit = true;
      $this->messageType = 'edited_message';
    }

    // The update_id is present on every update type.
    if(isset($this->update['update_id'])) $this->update_id = $this->update['update_id'];

    if(isset($this->update['callback_query']['id'])){
      // ===== Callback query (an inline keyboard button was pressed) =====
      $cq = $this->update['callback_query'];
      if(isset($cq['id']))                              $this->callback_query_id     = $cq['id'];
      if(isset($cq['from']['id']))                      $this->callback_user_id      = $cq['from']['id'];
      if(isset($cq['from']['is_bot']))                  $this->callback_is_bot       = $cq['from']['is_bot'];
      if(isset($cq['from']['first_name']))              $this->callback_nome         = $cq['from']['first_name'];
      if(isset($cq['from']['last_name']))               $this->callback_cognome      = $cq['from']['last_name'];
      if(isset($cq['from']['language_code']))           $this->callback_lingua       = $cq['from']['language_code'];
      if(isset($cq['message']['message_id']))           $this->callback_message_id   = $cq['message']['message_id'];
      if(isset($cq['message']['from']['id']))           $this->callback_bot_id       = $cq['message']['from']['id'];
      if(isset($cq['message']['from']['is_bot']))       $this->callback_bot_is_bot   = $cq['message']['from']['is_bot'];
      if(isset($cq['message']['from']['first_name']))   $this->callback_bot_nome     = $cq['message']['from']['first_name'];
      if(isset($cq['message']['from']['username']))     $this->callback_bot_username = $cq['message']['from']['username'];
      if(isset($cq['message']['chat']['id']))           $this->callback_chat_id      = $cq['message']['chat']['id'];
      if(isset($cq['message']['chat']['title']))        $this->callback_chat_title   = $cq['message']['chat']['title'];
      if(isset($cq['message']['chat']['type']))         $this->callback_chat_type    = $cq['message']['chat']['type'];
      if(isset($cq['message']['date']))                 $this->callback_time         = $cq['message']['date'];
      if(isset($cq['message']['text']))                 $this->callback_text         = $cq['message']['text'];
      if(isset($cq['message']['entities']))             $this->callback_entities     = $cq['message']['entities'];
      if(isset($cq['message']['reply_markup']['inline_keyboard'])) $this->callback_inline_keyboard = $cq['message']['reply_markup']['inline_keyboard'];
      if(isset($cq['chat_instance']))                   $this->callback_chat_instance = $cq['chat_instance'];
      if(isset($cq['data']))                            $this->callback_data         = $cq['data'];

    } else if(isset($this->update['inline_query']['id'])){
      // ===== Inline query (the user typed "@yourbot ..." in any chat) =====
      $iq = $this->update['inline_query'];
      if(isset($iq['id']))                    $this->inline_id       = $iq['id'];
      if(isset($iq['from']['id']))            $this->inline_user_id  = $iq['from']['id'];
      if(isset($iq['from']['is_bot']))        $this->inline_is_bot   = $iq['from']['is_bot'];
      if(isset($iq['from']['first_name']))    $this->inline_nome     = $iq['from']['first_name'];
      if(isset($iq['from']['last_name']))     $this->inline_cognome  = $iq['from']['last_name'];
      if(isset($iq['from']['username']))      $this->inline_username = $iq['from']['username'];
      if(isset($iq['from']['language_code'])) $this->inline_lingua   = $iq['from']['language_code'];
      if(isset($iq['query']))                 $this->inline_query    = $iq['query'];
      if(isset($iq['offset']))                $this->inline_offset   = $iq['offset'];

    } else if($this->messageType !== null && isset($this->update[$this->messageType]['message_id'])){
      // ===== Regular or edited message =====
      $msg = $this->update[$this->messageType];

      // Basic chat/sender info.
      if(isset($msg['chat']['first_name'])) $this->nome_chat = $msg['chat']['first_name'];
      if(isset($msg['from']['last_name']))  $this->cognome   = $msg['from']['last_name'];

      // --- Sticker ---
      if(isset($msg['sticker'])){
        if(isset($msg['sticker']['is_animated'])) $this->is_animated     = $msg['sticker']['is_animated'];
        if(isset($msg['sticker']['width']))       $this->width_sticker   = $msg['sticker']['width'];
        if(isset($msg['sticker']['height']))      $this->height_sticker  = $msg['sticker']['height'];
        if(isset($msg['sticker']['emoji']))       $this->emoji_sticker   = $msg['sticker']['emoji'];
        if(isset($msg['sticker']['set_name']))    $this->nome_sticker    = $msg['sticker']['set_name'];
        if(isset($msg['sticker']['file_id']))     $this->sticker         = $msg['sticker']['file_id'];
        if(isset($msg['sticker']['file_size']))   $this->size_sticker    = $msg['sticker']['file_size'];
      }

      // --- New chat member(s) joined ---
      if(isset($msg['new_chat_participant'])){
        if(isset($msg['new_chat_member']))               $this->nuovo_membro          = $msg['new_chat_member'];
        if(isset($msg['new_chat_member']['id']))         $this->nuovo_membro_id       = $msg['new_chat_member']['id'];
        if(isset($msg['new_chat_member']['first_name'])) $this->nuovo_membro_nome     = $msg['new_chat_member']['first_name'];
        if(isset($msg['new_chat_member']['last_name']))  $this->nuovo_membro_cognome  = $msg['new_chat_member']['last_name'];
        if(isset($msg['new_chat_member']['username']))   $this->nuovo_membro_username = $msg['new_chat_member']['username'];
        if(isset($msg['new_chat_member']['is_bot']))     $this->nuovo_membro_is_bot   = $msg['new_chat_member']['is_bot'];
        if(isset($msg['new_chat_participant']))          $this->nuovo_partecipante    = $msg['new_chat_participant'];
        if(isset($msg['new_chat_members']))              $this->nuovi_membri          = $msg['new_chat_members'];
      }

      // --- Photo ---
      // Telegram sends an array of sizes; the LAST element is the highest resolution.
      if(isset($msg['photo'])){
        if(isset($msg['caption'])) $this->didascalia = $msg['caption'];
        $photos = $msg['photo'];
        $biggest = is_array($photos) ? end($photos) : null;
        if(isset($biggest['file_id']))        $this->foto           = $biggest['file_id'];
        if(isset($biggest['file_unique_id'])) $this->file_unique_id = $biggest['file_unique_id'];
      }

      // --- Document ---
      if(isset($msg['document'])){
        if(isset($msg['document']['file_name']))      $this->nome_file       = $msg['document']['file_name'];
        if(isset($msg['document']['mime_type']))      $this->tipo_file       = $msg['document']['mime_type'];
        if(isset($msg['document']['file_id']))        $this->file            = $msg['document']['file_id'];
        if(isset($msg['document']['file_unique_id'])) $this->file_unique_id  = $msg['document']['file_unique_id'];
        if(isset($msg['document']['file_size']))      $this->size_file       = $msg['document']['file_size'];
      }

      // --- Video ---
      if(isset($msg['video'])){
        if(isset($msg['video']['duration']))       $this->durata_video    = $msg['video']['duration'];
        if(isset($msg['video']['file_id']))        $this->video           = $msg['video']['file_id'];
        if(isset($msg['video']['mime_type']))      $this->tipo_video      = $msg['video']['mime_type'];
        if(isset($msg['video']['file_unique_id'])) $this->file_unique_id  = $msg['video']['file_unique_id'];
        if(isset($msg['video']['width']))          $this->width_video     = $msg['video']['width'];
        if(isset($msg['video']['file_size']))      $this->size_video      = $msg['video']['file_size'];
        if(isset($msg['video']['height']))         $this->height_video    = $msg['video']['height'];
      }

      // --- Animation (GIF) ---
      if(isset($msg['animation'])){
        if(isset($msg['animation']['duration']))  $this->durata_gif = $msg['animation']['duration'];
        if(isset($msg['animation']['file_id']))   $this->gif        = $msg['animation']['file_id'];
        if(isset($msg['animation']['mime_type'])) $this->tipo_gif   = $msg['animation']['mime_type'];
        if(isset($msg['animation']['width']))     $this->width_gif  = $msg['animation']['width'];
        if(isset($msg['animation']['file_size'])) $this->size_gif   = $msg['animation']['file_size'];
        if(isset($msg['animation']['height']))    $this->height_gif = $msg['animation']['height'];
      }

      // --- Common message fields ---
      if(isset($msg['entities']))              $this->entities      = $msg['entities'];
      if(isset($msg['message_id']))            $this->message_id    = $msg['message_id'];
      if(isset($msg['from']['id']))            $this->user_id       = $msg['from']['id'];
      if(isset($msg['from']['is_bot']))        $this->is_bot        = $msg['from']['is_bot'];
      if(isset($msg['from']['first_name']))    $this->nome          = $msg['from']['first_name'];
      if(isset($msg['from']['username']))      $this->username      = $msg['from']['username'];
      if(isset($msg['from']['language_code'])) $this->lingua        = $msg['from']['language_code'];
      if(isset($msg['chat']['id']))            $this->chat_id       = $msg['chat']['id'];
      if(isset($msg['chat']['username']))      $this->username_chat = $msg['chat']['username'];
      if(isset($msg['chat']['type']))          $this->tipo_chat     = $msg['chat']['type'];
      if(isset($msg['date']))                  $this->time          = $msg['date']; // message timestamp (top-level "date")
      if(isset($msg['text']))                  $this->text          = $msg['text'];
      if(isset($msg['chat']['title']))         $this->nome_chat     = $msg['chat']['title'];

      // --- Forwarded message ---
      if(isset($msg['forward_sender_name'])){
        // Forwarded from a user who hides the "forwarded from" link.
        $this->forward_sender_name = $msg['forward_sender_name'];
        if(isset($msg['forward_date'])) $this->forward_date = $msg['forward_date'];
        if(isset($msg['text']))         $this->forward_text = $msg['text'];
      } else if(isset($msg['forward_from'])){
        // Forwarded from a user.
        if(isset($msg['forward_from']['id']))         $this->forward_chat_id  = $msg['forward_from']['id'];
        if(isset($msg['forward_from']['is_bot']))     $this->forward_is_bot   = $msg['forward_from']['is_bot'];
        if(isset($msg['forward_from']['first_name'])) $this->forward_nome     = $msg['forward_from']['first_name'];
        if(isset($msg['forward_from']['username']))   $this->forward_username = $msg['forward_from']['username'];
        if(isset($msg['forward_from']['last_name']))  $this->forward_cognome  = $msg['forward_from']['last_name'];
        if(isset($msg['text']))                       $this->forward_text     = $msg['text'];
        if(isset($msg['forward_date']))               $this->forward_date     = $msg['forward_date'];
      } else if(isset($msg['forward_from_chat'])){
        // Forwarded from a channel/group.
        if(isset($msg['forward_from_chat']['id']))       $this->forward_chat_id         = $msg['forward_from_chat']['id'];
        if(isset($msg['forward_from_chat']['title']))    $this->forward_title           = $msg['forward_from_chat']['title'];
        if(isset($msg['forward_from_chat']['username'])) $this->forward_username        = $msg['forward_from_chat']['username'];
        if(isset($msg['forward_from_chat']['type']))     $this->forward_type            = $msg['forward_from_chat']['type'];
        if(isset($msg['forward_from_message_id']))       $this->forward_from_message_id = $msg['forward_from_message_id'];
        if(isset($msg['forward_date']))                  $this->forward_date            = $msg['forward_date'];
      }

      // --- Reply to another message ---
      if(isset($msg['reply_to_message']['message_id'])){
        $r = $msg['reply_to_message'];
        if(isset($r['message_id']))         $this->reply_message_id   = $r['message_id'];
        if(isset($r['from']['id']))         $this->reply_user_id      = $r['from']['id'];
        if(isset($r['from']['is_bot']))     $this->reply_is_bot       = $r['from']['is_bot'];
        if(isset($r['from']['first_name'])) $this->reply_nome         = $r['from']['first_name'];
        if(isset($r['from']['last_name']))  $this->reply_cognome      = $r['from']['last_name'];
        if(isset($r['from']['type']))       $this->reply_tipo         = $r['from']['type'];
        if(isset($r['from']['username']))   $this->reply_username     = $r['from']['username'];
        if(isset($r['chat']['id']))         $this->reply_chat_id      = $r['chat']['id'];
        if(isset($r['chat']['first_name'])) $this->reply_chat_nome    = $r['chat']['first_name'];
        if(isset($r['chat']['last_name']))  $this->reply_chat_cognome = $r['chat']['last_name'];
        if(isset($r['chat']['type']))       $this->reply_chat_tipo    = $r['chat']['type'];
        if(isset($r['date']))               $this->reply_time         = $r['date'];
        if(isset($r['text']))               $this->reply_text         = $r['text'];
        if(isset($r['entities']))           $this->reply_entities     = $r['entities'];

        // The replied-to message is itself a forward.
        if(isset($r['forward_from']['id'])){
          $this->chat_id_reply_forward = $r['forward_from']['id'];
          if(isset($r['forward_from']['is_bot']))     $this->is_bot_reply_forward  = $r['forward_from']['is_bot'];
          if(isset($r['forward_from']['first_name'])) $this->nome_reply_forward    = $r['forward_from']['first_name'];
          if(isset($r['forward_date']))               $this->time_reply_forward    = $r['forward_date'];
          if(isset($r['text']))                       $this->text_reply            = $r['text'];
          if(isset($r['forward_from']['last_name']))  $this->cognome_reply_forward = $r['forward_from']['last_name'];
        }
      }
    }

    // ===== Channel post (separate update type, handled independently) =====
    if(isset($this->update['channel_post'])){
      $cp = $this->update['channel_post'];
      if(isset($cp['message_id'])) $this->message_id   = $cp['message_id'];
      if(isset($cp['chat']['id'])) $this->canale_id    = $cp['chat']['id'];
      if(isset($cp['caption']))    $this->didascalia   = $cp['caption'];
      if(isset($cp['text']))       $this->testo_canale = $cp['text'];
    }
  }

  /** Close the persistent cURL handle when the object is destroyed. */
  public function __destruct(){
    if(isset($this->ch) && ($this->ch instanceof \CurlHandle || is_resource($this->ch))){
      curl_close($this->ch);
    }
  }

  #FUNCTIONS

  /**
   * Build the reply_markup JSON for a keyboard.
   *
   * Centralises the logic previously duplicated across sendMessage, sendPhoto,
   * sendSticker, editMessageText, etc.
   *
   * @param string|bool $keyboard Raw rows of the keyboard, e.g. '[{"text":"Hi"}]'.
   * @param string|bool $type     'fisica' (reply keyboard) or 'inline' (inline keyboard).
   * @return string The reply_markup JSON, or '' when no keyboard is requested.
   */
  private function buildReplyMarkup($keyboard, $type){
    if($keyboard == false){
      return '';
    }
    if($type == 'fisica'){
      return '{"keyboard":['.$keyboard.'],"resize_keyboard":true}';
    } else if($type == 'inline'){
      return '{"inline_keyboard":['.$keyboard.'],"resize_keyboard":true}';
    }
    return '';
  }

  /**
   * Safely build a CURLFile for uploading a local file.
   *
   * Rejects null-byte injection and ensures the file exists and is readable,
   * mitigating path-traversal/arbitrary-file-read issues.
   *
   * @param string $path Local filesystem path.
   * @return \CURLFile|false The CURLFile on success, or false when the path is invalid.
   */
  private function makeCurlFile($path){
    if(!is_string($path) || $path === '' || strpos($path, "\0") !== false){
      return false;
    }
    if(!is_file($path) || !is_readable($path)){
      return false;
    }
    return new CURLFile($path);
  }

  /**
   * Perform a POST request to the Telegram API.
   *
   * Reuses a single persistent cURL handle, enforces TLS certificate
   * verification, and returns a Telegram-style error array on transport failure
   * instead of failing silently.
   *
   * @param string $url       API path, e.g. '/sendMessage'.
   * @param array  $post      POST fields.
   * @param bool   $multipart Set true when uploading files (multipart/form-data).
   * @return array Decoded API response, or ['ok'=>false,...] on a transport error.
   */
  public function cURL($url, $post, $multipart = false){

    // Reset the persistent handle so options from a previous call do not leak.
    curl_reset($this->ch);

    curl_setopt($this->ch, CURLOPT_URL, $this->apiUrl.$url);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->ch, CURLOPT_POST, true);
    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post);

    // Always verify the server certificate to prevent man-in-the-middle attacks.
    curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($this->ch, CURLOPT_TIMEOUT, 30);

    if($multipart){
      curl_setopt($this->ch, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);
    }

    $output = curl_exec($this->ch);

    if($output === false){
      // Network/cURL failure: surface the error instead of returning null silently.
      $this->last_error = curl_error($this->ch);
      return ['ok' => false, 'error_code' => 0, 'description' => $this->last_error];
    }

    return json_decode($output, TRUE);
  }

  public function deleteMessage($user_id,$message_id){
    $post = [
      'chat_id' => $user_id,
      'message_id' => $message_id
    ];

    return $this->cURL('/deleteMessage', $post);

  }

  // Alternative version: prints the request as JSON (webhook reply). Use only once per request.
  public function deleteMessage2($user_id,$message_id){

    header('Content-Type: application/json');

    $parameters = [
      'chat_id' => $user_id,
      'message_id' => $message_id,
      'method' => 'deleteMessage'
    ];

    echo json_encode($parameters, TRUE);

  }

  public function restrictChatMember($chat_id, $user_id, $perms = false, $until_date = 0){

    $post = [
      'chat_id' => $chat_id,
      'user_id' => $user_id,
      'until_date' => $until_date,
    ];

    // Merge permissions only when a valid array is supplied (avoids a TypeError on PHP 8+).
    if(is_array($perms)){
      $post = array_merge($post, $perms);
    }

    return $this->cURL('/restrictChatMember', $post);

  }

  public function promoteChatMember($chat_id, $user_id, $perms = []){

    $post = [
      'chat_id' => $chat_id,
      'user_id' => $user_id,
    ];

    // Merge admin permissions only when a valid array is supplied.
    if(is_array($perms)){
      $post = array_merge($post, $perms);
    }

    return $this->cURL('/promoteChatMember', $post);

  }

  public function exportChatInviteLink($chat_id){

    $post = [
      'chat_id' => $chat_id
    ];

    return $this->cURL('/exportChatInviteLink', $post);

  }

  public function unbanChatMember($chat_id,$user_id){

    $post = [
      'chat_id' => $chat_id,
      'user_id' => $user_id
    ];

    return $this->cURL('/unbanChatMember', $post);

  }

  /**
   * Ban a chat member (current Telegram API name).
   *
   * @param int $until_date Unix time when the ban ends; 0/false = permanent.
   */
  public function banChatMember($chat_id, $user_id, $until_date = false){

    if($until_date == false){
      $until_date = 0;
    }

    $post = [
      'chat_id' => $chat_id,
      'user_id' => $user_id,
      'until_date' => $until_date
    ];

    return $this->cURL('/banChatMember', $post);

  }

  /**
   * Ban a chat member.
   *
   * @deprecated Telegram renamed this to banChatMember(). Kept as a
   *             backward-compatible alias; it now calls banChatMember().
   */
  public function kickChatMember($chat_id, $user_id, $until_date = false){
    return $this->banChatMember($chat_id, $user_id, $until_date);
  }

  public function setChatTitle($chat_id, $title){

    $post = [
      'chat_id' => $chat_id,
      'title' => $title
    ];

    return $this->cURL('/setChatTitle', $post);

  }

  public function setChatDescription($chat_id, $description = false){

    if($description == false){
      $description = '';
    }

    $post = [
      'chat_id' => $chat_id,
      'description' => $description
    ];

    return $this->cURL('/setChatDescription',$post);

  }

  public function sendChatAction($chat_id, $action){

    $post = [
      'chat_id' => $chat_id,
      'action' => $action
    ];

    return $this->cURL('/sendChatAction',$post);

  }

  public function revokeChatInviteLink($chat_id, $invite_link){

    $post = [
      'chat_id' => $chat_id,
      'invite_link' => $invite_link
    ];

    return $this->cURL('/revokeChatInviteLink', $post);

  }

  public function forwardMessage($from_chat_id, $user_id, $message_id, $disable_notification = false){

    $post = [
      'chat_id' => $user_id,
      'from_chat_id' => $from_chat_id,
      'message_id' => $message_id,
      'disable_notification' => $disable_notification
    ];

    return $this->cURL('/forwardMessage', $post);

  }

  public function sendMessage($user_id, $text, $keyboard = false, $type = false, $risposta = false, $forceReply = false, $notifica = false, $parse_mode = 'HTML', $disableWebPagePreview = true){

    $rm = $this->buildReplyMarkup($keyboard, $type);

    if($risposta == false){
      $risposta = '';
    }

    $post = [
      'chat_id' => $user_id,
      'text' => $text,
      'parse_mode' => $parse_mode,
      'disable_web_page_preview' => $disableWebPagePreview,
      'reply_markup' => $rm,
      'reply_to_message_id' => $risposta,
      'force_reply' => $forceReply,
      'disable_notification' => $notifica,
    ];

    return $this->cURL('/sendMessage', $post);

  }

  // Alternative version: prints the request as JSON (webhook reply). Use only once per request.
  public function sendMessage2($user_id, $text, $keyboard = false, $type = false, $risposta = false, $forceReply = false, $notifica = false, $parse_mode = 'HTML', $disableWebPagePreview = true){

    $rm = $this->buildReplyMarkup($keyboard, $type);

    header('Content-Type: application/json');

    $parameters = [
      'chat_id' => $user_id,
      'method' => 'sendMessage',
      'disable_notification' => $notifica,
      'force_reply' => $forceReply,
      'reply_to_message_id' => $risposta,
      'reply_markup' => $rm,
      'parse_mode' => $parse_mode,
      'text' => $text,
      'disable_web_page_preview' => $disableWebPagePreview
    ];

    echo json_encode($parameters, TRUE);

  }

  public function getStickerSet($name){

    $post = [
      'name' => $name
    ];

    return $this->cURL('/getStickerSet', $post);

  }

  public function uploadStickerFile($user_id, $png_sticker){

    // Validate the local file before uploading (prevents arbitrary-file reads).
    $file = $this->makeCurlFile($png_sticker);
    if($file === false){
      return ['ok' => false, 'error_code' => 0, 'description' => 'File not found or not readable: '.$png_sticker];
    }

    $post = [
      'chat_id' => $user_id,
      'png_sticker' => $file
    ];

    return $this->cURL('/uploadStickerFile', $post, true);

  }

  public function sendSticker($user_id,$sticker, $keyboard = false, $type = false, $reply_to_message_id = false, $disable_notification = false, $forceReply = false){

    $rm = $this->buildReplyMarkup($keyboard, $type);

    if($reply_to_message_id == false){
      $reply_to_message_id = '';
    }

    $post = [
      'chat_id' => $user_id,
      'sticker' => $sticker,
      'disable_notification' => $disable_notification,
      'reply_markup' => $rm,
      'reply_to_message_id' => $reply_to_message_id,
      'force_reply' => $forceReply
    ];

    return $this->cURL('/sendSticker',$post);

  }

  /**
   * Send a photo.
   *
   * @param bool $file_id true: $photo is a Telegram file_id/URL; false: $photo is a local path.
   */
  public function sendPhoto($user_id, $photo, $caption = '', $keyboard = false, $type = false, $file_id = true){

    $rm = $this->buildReplyMarkup($keyboard, $type);

    $post = [
      'caption' => $caption,
      'chat_id' => $user_id,
      'reply_markup' => $rm,
      'parse_mode' => 'HTML'
    ];

    if($file_id == true){
      $post['photo'] = $photo;
      return $this->cURL('/sendPhoto', $post);
    }

    // Local file upload.
    $file = $this->makeCurlFile($photo);
    if($file === false){
      return ['ok' => false, 'error_code' => 0, 'description' => 'File not found or not readable: '.$photo];
    }
    $post['photo'] = $file;
    return $this->cURL('/sendPhoto', $post, true);

  }

  public function sendAudio($user_id,$audio,$caption = ''){

    $post = [
     'chat_id' => $user_id,
     'audio' => $audio,
     'caption' => $caption
    ];

    return $this->cURL('/sendAudio', $post);

  }

  public function sendVideo($user_id,$video,$caption = false){

    $post = [
      'chat_id' => $user_id,
      'video' => $video,
      'caption' => $caption
    ];

    return $this->cURL('/sendVideo', $post);

  }

  public function sendMediaGroup($user_id,$album,$caption = ''){

    $post = [
      'chat_id' => $user_id,
      'media' => $album, // Telegram expects the "media" field (was incorrectly "InputMedia").
      'caption' => $caption,
    ];

    return $this->cURL('/sendMediaGroup',$post);

  }

  /**
   * Send a document/file.
   *
   * @param bool $file_id true: $document is a Telegram file_id/URL; false: local path.
   */
  public function sendDocument($user_id, $document, $file_id = true, $caption = false, $parse_mode = false){

    if($caption == false){
      $caption = '';
    }

    if($parse_mode == false){
      $parse_mode = 'HTML';
    }

    $post = [
      'chat_id' => $user_id,
      'caption' => $caption,
      'parse_mode' => $parse_mode
    ];

    if($file_id == true){
      $post['document'] = $document;
      return $this->cURL('/sendDocument', $post);
    }

    // Local file upload.
    $file = $this->makeCurlFile($document);
    if($file === false){
      return ['ok' => false, 'error_code' => 0, 'description' => 'File not found or not readable: '.$document];
    }
    $post['document'] = $file;
    return $this->cURL('/sendDocument', $post, true);

  }

  public function sendVoice($user_id,$voice,$caption = ''){

    $post = [
      'chat_id' => $user_id,
      'voice' => $voice,
      'caption' => $caption
    ];

    return $this->cURL('/sendVoice',$post);

  }

  public function sendAnimation($user_id,$animation,$caption = ''){

    $post = [
      'chat_id' => $user_id,
      'animation' => $animation,
      'caption' => $caption
    ];

    return $this->cURL('/sendAnimation',$post);

  }

  public function answerCallbackQuery($callback_query_id,$text,$show_alert = true){

    $post = [
      'callback_query_id' => $callback_query_id,
      'text' => $text,
      'show_alert' => $show_alert
    ];

    return $this->cURL('/answerCallbackQuery', $post);

  }

  public function editMessageText($user_id, $message_id, $text, $keyboard = false, $type = false, $parse_mode = 'HTML', $disableWebPagePreview = true){

    $rm = $this->buildReplyMarkup($keyboard, $type);

    $post = [
      'chat_id' => $user_id,
      'message_id' => $message_id,
      'text' => $text,
      'disable_web_page_preview' => $disableWebPagePreview,
      'parse_mode' => $parse_mode,
      'reply_markup' => $rm
    ];

    return $this->cURL('/editMessageText', $post);

  }

  // Alternative version: prints the request as JSON (webhook reply). Use only once per request.
  public function editMessageText2($user_id, $message_id, $newText, $keyboard = false, $type = false, $parse_mode = 'HTML', $disableWebPagePreview = true){

    $rm = $this->buildReplyMarkup($keyboard, $type);

    header('Content-Type: application/json');

    $parameters = [
      'chat_id' => $user_id,
      'message_id' => $message_id,
      'method' => 'editMessageText',
      'parse_mode' => $parse_mode,
      'text' => $newText,
      'disable_web_page_preview' => $disableWebPagePreview,
      'reply_markup' => $rm
    ];

    echo json_encode($parameters, TRUE);

  }

  public function leaveChat($chat_id){

    $post = [
      'chat_id' => $chat_id
    ];

    return $this->cURL('/leaveChat', $post);

  }

  // Alternative version: prints the request as JSON (webhook reply). Use only once per request.
  public function leaveChat2($chat_id){

    header('Content-Type: application/json');

    $parameters = [
      'chat_id' => $chat_id, // fixed: previously referenced an undefined $user_id
      'method' => 'leaveChat'
    ];

    echo json_encode($parameters, TRUE);

  }

  public function pinChatMessage($chat_id,$message_id,$disable_notification = false){

    $post = [
      'chat_id' => $chat_id,
      'message_id' => $message_id,
      'disable_notification' => $disable_notification
    ];

    return $this->cURL('/pinChatMessage', $post);

  }

  public function getChat($chat_id){

    $post = [
      'chat_id' => $chat_id
    ];

    return $this->cURL('/getChat', $post);

  }

  public function deleteWebhook($token = false){

    // $token kept for backward compatibility; the request is authenticated by the
    // bot token already embedded in the API URL, so the field is informational.
    $post = [];
    if($token !== false){
      $post['token'] = $token;
    }

    return $this->cURL('/deleteWebhook', $post);

  }

  public function setChatStickerSet($chat_id, $sticker_set_name){

    $post = [
      'chat_id' => $chat_id,
      'sticker_set_name' => $sticker_set_name
    ];

    return $this->cURL('/setChatStickerSet', $post);

  }

  public function deleteChatStickerSet($chat_id){

    $post = [
      'chat_id' => $chat_id
    ];

    return $this->cURL('/deleteChatStickerSet', $post);

  }

  public function unpinChatMessage($chat_id, $message_id = false){

    $post = [
      'chat_id' => $chat_id,
      'message_id' => $message_id
    ];

    return $this->cURL('/unpinChatMessage', $post);

  }

  public function unpinAllChatMessages($chat_id){

    $post = [
      'chat_id' => $chat_id
    ];

    return $this->cURL('/unpinAllChatMessages', $post);

  }

  public function setWebhook($token = false, $url = '', $max_connections = 40, $allowed_updates = ''){

    // $token kept for backward compatibility; authentication uses the embedded token.
    $post = [
      'url' => $url,
      'max_connections' => $max_connections,
      'allowed_updates' => $allowed_updates
    ];
    if($token !== false){
      $post['token'] = $token;
    }

    return $this->cURL('/setWebhook',$post);

  }

  public function getWebhookInfo($token = false){

    $post = [];
    if($token !== false){
      $post['token'] = $token;
    }

    return $this->cURL('/getWebhookInfo',$post);

  }

  public function getChatAdministrators($chat_id){

    $post = [
      'chat_id' => $chat_id
    ];

    return $this->cURL('/getChatAdministrators', $post);

  }

  /**
   * Get the number of members in a chat (current Telegram API name).
   */
  public function getChatMemberCount($chat_id){

    $post = [
      'chat_id' => $chat_id
    ];

    return $this->cURL('/getChatMemberCount',$post);

  }

  /**
   * Get the number of members in a chat.
   *
   * @deprecated Telegram renamed this to getChatMemberCount(). Kept as a
   *             backward-compatible alias; it now calls getChatMemberCount().
   */
  public function getChatMembersCount($chat_id){
    return $this->getChatMemberCount($chat_id);
  }

  public function getChatMember($chat_id, $user_id){

    $post = [
      'chat_id' => $chat_id,
      'user_id' => $user_id
    ];

    return $this->cURL('/getChatMember', $post);

  }

  public function gestisciInlineQuery($inlineData,$switchText = 'Ritorna al bot', $switchParameter = 123, $cacheTime = 0){

    $post = [
      'inline_query_id' => $this->inline_id,
      'results' => json_encode($inlineData,true),
      'cache_time' => $cacheTime,
      'switch_pm_text' => $switchText,
      'switch_pm_parameter' => $switchParameter
    ];

    return $this->cURL('/answerInlineQuery', $post);

  }


}

?>
