<?php


class Bot {

  
  public function __construct($token,$json = false){

    if($json == false){
      $this->json = file_get_contents('php://input');
    } else {
      $this->json = $json;
    }

    $this->bot = $token;

    $this->update = json_decode($this->json, TRUE);

    if($this->update != null){

    if(isset($this->update['message'])){
      $this->edit = false;
      $this->messageType = 'message';
    } else if(isset($this->update['edited_message'])){
      $this->edit = true;
      $this->messageType = 'edited_message';
    }

    if(isset($this->update['callback_query']['id'])){
      if(isset($this->update['callback_query']['from']['last_name'])){
        if(isset($this->update['callback_query']['from']['last_name'])) $this->callback_cognome = $this->update['callback_query']['from']['last_name'];
      }
      if(isset($this->update['update_id'])) $this->update_id = $this->update['update_id'];
      if(isset($this->update['callback_query']['id'])) $this->callback_query_id = $this->update['callback_query']['id'];
      if(isset($this->update['callback_query']['from']['id'])) $this->callback_user_id = $this->update['callback_query']['from']['id']; 
      if(isset($this->update['callback_query']['from']['is_bot'])) $this->callback_is_bot = $this->update['callback_query']['from']['is_bot']; 
      if(isset($this->update['callback_query']['from']['first_name'])) $this->callback_nome = $this->update['callback_query']['from']['first_name']; 
      if(isset($this->update['callback_query']['from']['language_code'])) $this->callback_lingua = $this->update['callback_query']['from']['language_code']; 
      if(isset($this->update['callback_query']['message']['message_id'])) $this->callback_message_id = $this->update['callback_query']['message']['message_id']; 
      if(isset($this->update['callback_query']['message']['from']['id'])) $this->callback_bot_id = $this->update['callback_query']['message']['from']['id']; 
      if(isset($this->update['callback_query']['message']['from']['is_bot'])) $this->callback_bot_is_bot = $this->update['callback_query']['message']['from']['is_bot'];
      if(isset($this->update['callback_query']['message']['from']['first_name'])) $this->callback_bot_nome = $this->update['callback_query']['message']['from']['first_name'];
      if(isset($this->update['callback_query']['message']['from']['username'])) $this->callback_bot_username = $this->update['callback_query']['message']['from']['username'];
      if(isset($this->update['callback_query']['message']['chat']['id'])) $this->callback_chat_id = $this->update['callback_query']['message']['chat']['id'];
      if(isset($this->update['callback_query']['message']['chat']['title'])) $this->callback_chat_title = $this->update['callback_query']['message']['chat']['title'];
      if(isset($this->update['callback_query']['message']['chat']['type'])) $this->callback_chat_type = $this->update['callback_query']['message']['chat']['type']; 
      if(isset($this->update['callback_query']['message']['date'])) $this->callback_time = $this->update['callback_query']['message']['date']; 
      if(isset($this->update['callback_query']['message']['text'])) $this->callback_text = $this->update['callback_query']['message']['text']; 
      if(isset($this->update['callback_query']['message']['entities'])) $this->callback_entities = $this->update['callback_query']['message']['entities'];
      if(isset($this->update['callback_query']['message']['reply_markup']['inline_keyboard'])) $this->callback_inline_keyboard = $this->update['callback_query']['message']['reply_markup']['inline_keyboard'];
      if(isset($this->update['callback_query']['chat_instance'])) $this->callback_chat_instance = $this->update['callback_query']['chat_instance']; //istanza chat
      if(isset($this->update['callback_query']['data'])) $this->callback_data = $this->update['callback_query']['data'];
    } else if(isset($this->update[$this->messageType]['message_id'])){ 
          if(isset($this->update[$this->messageType]['chat']['first_name'])) $this->nome_chat = $this->update[$this->messageType]['chat']['first_name'];
          if(isset($this->update[$this->messageType]['from']['last_name'])) $this->cognome = $this->update[$this->messageType]['from']['last_name'];
      if(isset($this->update[$this->messageType]['sticker'])){ 
        if(isset($this->update[$this->messageType]['sticker']['is_animated'])) $this->is_animated = $this->update[$this->messageType]['sticker']['is_animated']; 
        if(isset($this->update[$this->messageType]['sticker']['width'])) $this->width_sticker = $this->update[$this->messageType]['sticker']['width'];
        if(isset($this->update[$this->messageType]['sticker']['height'])) $this->height_sticker = $this->update[$this->messageType]['sticker']['height'];
        if(isset($this->update[$this->messageType]['sticker']['emoji'])) $this->emoji_sticker = $this->update[$this->messageType]['sticker']['emoji'];
        if(isset($this->update[$this->messageType]['sticker']['set_name'])) $this->nome_sticker = $this->update[$this->messageType]['sticker']['set_name'];
        if(isset($this->update[$this->messageType]['sticker']['file_id'])) $this->sticker = $this->update[$this->messageType]['sticker']['file_id'];
        if(isset($this->update[$this->messageType]['sticker']['file_size'])) $this->size_sticker = $this->update[$this->messageType]['sticker']['file_size'];
      }
      if(isset($this->update[$this->messageType]['new_chat_participant'])){ //Se c'è un nuovo partecipante alla chat
        if(isset($this->update[$this->messageType]['new_chat_member'])) $this->nuovo_membro = $this->update[$this->messageType]['new_chat_member'];
        if(isset($this->update[$this->messageType]['new_chat_member']['id'])) $this->nuovo_membro_id = $this->update[$this->messageType]['new_chat_member']['id']; 
        if(isset($this->update['message']['new_chat_member']['first_name'])) $this->nuovo_membro_nome = $this->update['message']['new_chat_member']['first_name']; 
        if(isset($this->update[$this->messageType]['new_chat_member']['last_name'])){
          if(isset($this->update[$this->messageType]['new_chat_member']['last_name'])) $this->nuovo_membro_cognome = $this->update[$this->messageType]['new_chat_member']['last_name']; 
        }
        if(isset($this->update[$this->messageType]['new_chat_member']['username'])){
          if(isset($this->update[$this->messageType]['new_chat_member']['username'])) $this->nuovo_membro_username = $this->update[$this->messageType]['new_chat_member']['username'];
        }
        if(isset($this->update[$this->messageType]['new_chat_member']['is_bot'])) $this->nuovo_membro_is_bot = $this->update[$this->messageType]['new_chat_member']['is_bot'];
        if(isset($this->update[$this->messageType]['new_chat_participant'])) $this->nuovo_partecipante = $this->update[$this->messageType]['new_chat_participant']; 
        if(isset($this->update[$this->messageType]['new_chat_members'])) $this->nuovi_membri = $this->update[$this->messageType]['new_chat_members'];
      }
      if(isset($this->update[$this->messageType]['photo'])){ 
        if(isset($this->update[$this->messageType]['caption'])) $this->didascalia = $this->update[$this->messageType]['caption'];
        if(isset($this->update[$this->messageType]['photo']['file_unique_id'])) $this->file_unique_id = $this->update[$this->messageType]['photo']['file_unique_id'];
        if(isset($this->update[$this->messageType]['photo']['2']['file_id'])) $this->foto = $this->update[$this->messageType]['photo']['2']['file_id'];
      }
      if(isset($this->update[$this->messageType]['document'])){ 
        if(isset($this->update[$this->messageType]['document']['file_name'])) $this->nome_file = $this->update[$this->messageType]['document']['file_name']; 
        if(isset($this->update[$this->messageType]['document']['mime_type'])) $this->tipo_file = $this->update[$this->messageType]['document']['mime_type'];
        if(isset($this->update[$this->messageType]['document']['file_id'])) $this->file = $this->update[$this->messageType]['document']['file_id'];
        if(isset($this->update[$this->messageType]['document']['file_unique_id'])) $this->file_unique_id = $this->update[$this->messageType]['document']['file_unique_id'];
        if(isset($this->update[$this->messageType]['document']['mime_type'])) $this->tipo_file = $this->update[$this->messageType]['document']['mime_type'];
        if(isset($this->update[$this->messageType]['document']['file_size'])) $this->size_file = $this->update[$this->messageType]['document']['file_size'];
      }
      if(isset($this->update[$this->messageType]['video'])){
        if(isset($this->update[$this->messageType]['video']['duration'])) $this->durata_video = $this->update[$this->messageType]['video']['duration'];
        if(isset($this->update[$this->messageType]['video']['file_id'])) $this->video = $this->update[$this->messageType]['video']['file_id'];
        if(isset($this->update[$this->messageType]['video']['mime_type'])) $this->tipo_video = $this->update[$this->messageType]['video']['mime_type']; 
        if(isset($this->update[$this->messageType]['video']['file_unique_id'])) $this->file_unique_id = $this->update[$this->messageType]['video']['file_unique_id'];
        if(isset($this->update[$this->messageType]['video']['width'])) $this->width_video = $this->update[$this->messageType]['video']['width'];
        if(isset($this->update[$this->messageType]['video']['file_size'])) $this->size_video = $this->update[$this->messageType]['video']['file_size'];
        if(isset($this->update[$this->messageType]['video']['height'])) $this->height_video = $this->update[$this->messageType]['video']['height'];
      }
      if(isset($this->update[$this->messageType]['animation'])){
        if(isset($this->update[$this->messageType]['animation']['duration'])) $this->durata_gif = $this->update[$this->messageType]['animation']['duration'];
        if(isset($this->update[$this->messageType]['animation']['file_id'])) $this->gif = $this->update[$this->messageType]['animation']['file_id'];
        if(isset($this->update[$this->messageType]['animation']['mime_type'])) $this->tipo_gif = $this->update[$this->messageType]['animation']['mime_type']; 
        if(isset($this->update[$this->messageType]['animation']['width'])) $this->width_gif = $this->update[$this->messageType]['animation']['width'];
        if(isset($this->update[$this->messageType]['animation']['file_size'])) $this->size_gif = $this->update[$this->messageType]['animation']['file_size'];
        if(isset($this->update[$this->messageType]['animation']['height'])) $this->height_gif = $this->update[$this->messageType]['animation']['height'];
      }
      if(isset($this->update['channel_post'])){
        if(isset($this->update['channel_post']['message_id'])) $this->message_id = $this->update['channel_post']['message_id'];
        if(isset($this->update['channel_post']['chat']['id'])) $this->canale_id = $this->update['channel_post']['chat']['id'];
        if(isset($this->update['channel_post']['caption'])) $this->didascalia = $this->update['channel_post']['caption'];
        if(isset($this->update['channel_post']['text'])) $this->testo_canale = $this->update['channel_post']['text'];
      }
      if(isset($this->update[$this->messageType]['entities'])) $this->entities = $this->update[$this->messageType]['entities'];
      if(isset($this->update['update_id'])) $this->update_id = $this->update['update_id']; //ID dell'update
      if(isset($this->update[$this->messageType]['message_id'])) $this->message_id = $this->update[$this->messageType]['message_id']; 
      if(isset($this->update[$this->messageType]['from']['id'])) $this->user_id = $this->update[$this->messageType]['from']['id']; 
      if(isset($this->update[$this->messageType]['from']['is_bot'])) $this->is_bot = $this->update[$this->messageType]['from']['is_bot'];
      if(isset($this->update[$this->messageType]['from']['first_name'])) $this->nome = $this->update[$this->messageType]['from']['first_name']; 
      if(isset($this->update[$this->messageType]['from']['username'])) $this->username = $this->update[$this->messageType]['from']['username']; 
      if(isset($this->update[$this->messageType]['from']['language_code'])) $this->lingua = $this->update[$this->messageType]['from']['language_code']; 
      if(isset($this->update[$this->messageType]['chat']['id'])) $this->chat_id = $this->update[$this->messageType]['chat']['id'];
      if(isset($this->update[$this->messageType]['chat']['username'])) $this->username_chat = $this->update[$this->messageType]['chat']['username'];
      if(isset($this->update[$this->messageType]['chat']['type'])) $this->tipo_chat = $this->update[$this->messageType]['chat']['type'];
      if(isset($this->update[$this->messageType]['chat']['date'])) $this->time = $this->update[$this->messageType]['chat']['date'];
      if(isset($this->update[$this->messageType]['text'])) $this->text = $this->update[$this->messageType]['text'];
      if(isset($this->update[$this->messageType]['chat']['title'])){
        if(isset($this->update[$this->messageType]['chat']['title'])) $this->nome_chat = $this->update[$this->messageType]['chat']['title'];
      }
      if(isset($this->update[$this->messageType]['forward_sender_name'])){
        if(isset($this->update[$this->messageType]['forward_sender_name'])) $this->forward_sender_name = $this->update[$this->messageType]['forward_sender_name']; 
        if(isset($this->update[$this->messageType]['forward_date'])) $this->forward_date = $this->update[$this->messageType]['forward_date'];
        if(isset($this->update[$this->messageType]['text'])) $this->forward_text = $this->update[$this->messageType]['text'];
      } else if(isset($this->update[$this->messageType]['forward_from'])){ //Se il messaggio è INOLTRATO
        if(isset($this->update[$this->messageType]['forward_from']['id'])) $this->forward_chat_id = $this->update[$this->messageType]['forward_from']['id']; 
        if(isset($this->update[$this->messageType]['forward_from']['is_bot'])) $this->forward_is_bot = $this->update[$this->messageType]['forward_from']['is_bot'];
        if(isset($this->update[$this->messageType]['forward_from']['first_name'])) $this->forward_nome = $this->update[$this->messageType]['forward_from']['first_name'];
        if(isset($this->update[$this->messageType]['forward_from']['username'])) $this->forward_username = $this->update[$this->messageType]['forward_from']['username']; 
        if(isset($this->update[$this->messageType]['text'])) $this->forward_text = $this->update[$this->messageType]['text']; 
        if(isset($this->update[$this->messageType]['forward_date'])) $this->forward_date = $this->update[$this->messageType]['forward_date'];
        if(isset($this->update[$this->messageType]['forward_from']['last_name'])) $this->forward_cognome = $this->update[$this->messageType]['forward_from']['last_name'];
      } else if(isset($this->update[$this->messageType]['forward_from_chat'])){
        if(isset($this->update[$this->messageType]['forward_from_chat']['id'])) $this->forward_chat_id = $this->update[$this->messageType]['forward_from_chat']['id'];
        if(isset($this->update[$this->messageType]['forward_from_chat']['title'])) $this->forward_title = $this->update[$this->messageType]['forward_from_chat']['title'];
        if(isset($this->update[$this->messageType]['forward_from_chat']['username'])) $this->forward_username = $this->update[$this->messageType]['forward_from_chat']['username'];
        if(isset($this->update[$this->messageType]['forward_from_chat']['type'])) $this->forward_type = $this->update[$this->messageType]['forward_from_chat']['type'];
        if(isset($this->update[$this->messageType]['forward_from_message_id'])) $this->forward_from_message_id = $this->update[$this->messageType]['forward_from_message_id'];
        if(isset($this->update[$this->messageType]['forward_date'])) $this->forward_date = $this->update[$this->messageType]['forward_date'];
      }
      if(isset($this->update[$this->messageType]['reply_to_message']['message_id'])){
        if(isset($this->update[$this->messageType]['reply_to_message']['message_id'])) $this->reply_message_id = $this->update[$this->messageType]['reply_to_message']['message_id']; 
        if(isset($this->update[$this->messageType]['reply_to_message']['from']['id'])) $this->reply_user_id = $this->update[$this->messageType]['reply_to_message']['from']['id'];
        if(isset($this->update[$this->messageType]['reply_to_message']['from']['is_bot'])) $this->reply_is_bot = $this->update[$this->messageType]['reply_to_message']['from']['is_bot'];
        if(isset($this->update[$this->messageType]['reply_to_message']['from']['first_name'])) $this->reply_nome = $this->update[$this->messageType]['reply_to_message']['from']['first_name']; 
        if(isset($this->update[$this->messageType]['reply_to_message']['from']['last_name'])) $this->reply_cognome = $this->update[$this->messageType]['reply_to_message']['from']['last_name'];
        if(isset($this->update[$this->messageType]['reply_to_message']['from']['type'])) $this->reply_tipo = $this->update[$this->messageType]['reply_to_message']['from']['type']; 
        if(isset($this->update[$this->messageType]['reply_to_message']['from']['date'])) $this->reply_time = $this->update[$this->messageType]['reply_to_message']['from']['date'];
        if(isset($this->update[$this->messageType]['reply_to_message']['from']['username'])) $this->reply_username = $this->update[$this->messageType]['reply_to_message']['from']['username'];
        if(isset($this->update[$this->messageType]['reply_to_message']['chat']['id'])) $this->reply_chat_id = $this->update[$this->messageType]['reply_to_message']['chat']['id'];
        if(isset($this->update[$this->messageType]['reply_to_message']['chat']['first_name'])) $this->reply_chat_nome = $this->update[$this->messageType]['reply_to_message']['chat']['first_name'];
        if(isset($this->update[$this->messageType]['reply_to_message']['chat']['last_name'])) $this->reply_chat_cognome = $this->update[$this->messageType]['reply_to_message']['chat']['last_name'];
        if(isset($this->update[$this->messageType]['reply_to_message']['chat']['type'])) $this->reply_chat_tipo = $this->update[$this->messageType]['reply_to_message']['chat']['type']; 
        if(isset($this->update[$this->messageType]['reply_to_message']['date'])) $this->reply_time = $this->update[$this->messageType]['reply_to_message']['date']; 
        if(isset($this->update[$this->messageType]['reply_to_message']['text'])) $this->reply_text = $this->update[$this->messageType]['reply_to_message']['text'];
        if(isset($this->update[$this->messageType]['reply_to_message']['entities'])) $this->reply_entities = $this->update[$this->messageType]['reply_to_message']['entities'];
        if(isset($this->update[$this->messageType]['reply_to_message']['forward_from']['id'])){
          if(isset($this->update[$this->messageType]['reply_to_message']['forward_from']['id'])) $this->chat_id_reply_forward = $this->update[$this->messageType]['reply_to_message']['forward_from']['id'];
          if(isset($this->update[$this->messageType]['reply_to_message']['forward_from']['is_bot'])) $this->is_bot_reply_forward = $this->update[$this->messageType]['reply_to_message']['forward_from']['is_bot'];
          if(isset($this->update[$this->messageType]['reply_to_message']['forward_from']['first_name'])) $this->nome_reply_forward = $this->update[$this->messageType]['reply_to_message']['forward_from']['first_name'];
          if(isset($this->update[$this->messageType]['reply_to_message']['forward_date'])) $this->time_reply_forward = $this->update[$this->messageType]['reply_to_message']['forward_date'];
          if(isset($this->update[$this->messageType]['reply_to_message']['text'])) $this->text_reply = $this->update[$this->messageType]['reply_to_message']['text'];
          if(isset($this->update[$this->messageType]['reply_to_message']['forward_from']['last_name'])){
            if(isset($this->update[$this->messageType]['reply_to_message']['forward_from']['last_name'])) $this->cognome_reply_forward = $this->update[$this->messageType]['reply_to_message']['forward_from']['last_name'];
          }
      }
      } else if(isset($this->update['inline_query']['id'])){
      if(isset($this->update['update_id'])) $this->update_id = $this->update['update_id']; 
      if(isset($this->update['inline_query']['id'])) $this->inline_id = $this->update['inline_query']['id']; 
      if(isset($this->update['inline_query']['from']['id'])) $this->inline_user_id = $this->update['inline_query']['from']['id']; 
      if(isset($this->update['inline_query']['from']['is_bot'])) $this->inline_is_bot = $this->update['inline_query']['from']['is_bot']; 
      if(isset($this->update['inline_query']['from']['first_name'])) $this->inline_nome = $this->update['inline_query']['from']['first_name']; 
      if(isset($this->update['inline_query']['from']['last_name'])) $this->inline_cognome = $this->update['inline_query']['from']['last_name']; 
      if(isset($this->update['inline_query']['from']['username'])) $this->inline_username = $this->update['inline_query']['from']['username']; 
      if(isset($this->update['inline_query']['from']['language_code'])) $this->inline_lingua = $this->update['inline_query']['from']['language_code'];
      if(isset($this->update['inline_query']['query'])) $this->inline_query = $this->update['inline_query']['query']; 
      if(isset($this->update['inline_query']['offset'])) $this->inline_offset = $this->update['inline_query']['offset'];
    } 
    }
    } 
    } 



  #FUNCTIONS

  public function cURL($url, $post){

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.$this->bot.$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, TRUE);

  }

  public function deleteMessage($user_id,$message_id){
    $post = [
      'chat_id' => $user_id,
      'message_id' => $message_id
    ];

    return $this->cURL('/deleteMessage', $post);

  }

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

    $post = array_merge($post, $perms);

    return $this->cURL('/restrictChatMember',$post);

  }

  public function promoteChatMember($chat_id,$user_id,$perms = []){

    if($until_date == false){
      $until_date = 0;
    }
   $post = [
  'chat_id' => $chat_id,
  'user_id' => $user_id,
  ];
   $post = array_merge($post, $perms);
    return $this->cURL('/promoteChatMember',$post);
  }

  public function exportChatInviteLink($chat_id){

    $post = [
      'chat_id' => $chat_id
    ];

    return $this->cURL('/exportChatInviteLink',$post);

  }

  public function unbanChatMember($chat_id,$user_id){
    $post = [
      'chat_id' => $chat_id,
      'user_id' => $user_id
    ];
    return $this->cURL('/unbanChatMember',$post);
  }

  public function kickChatMember($chat_id,$user_id,$until_date = false){
    if($until_date == false){
      $until_date = 0;
    }
    $post = [
      'chat_id' => $chat_id,
      'user_id' => $user_id,
      'until_date' => $until_date
    ];
    return $this->cURL('/kickChatMember',$post);
  }

  public function setChatTitle($chat_id,$title){
    $post = [
      'chat_id' => $chat_id,
      'title' => $title
    ];
    return $this->cURL('/setChatTitle',$post);
  }

  public function setChatDescription($chat_id,$description = false){

    if($description == false){
      $description = '';
    } else {
      $description = $description;
    }
  $post = [
  'chat_id' => $chat_id,
  'description' => $description
  ];
    return $this->cURL('/setChatDescription',$post);
  }

  public function sendChatAction($user_id,$action){

    $post = [
      'chat_id' => $user_id,
      'action' => $action
    ];

    return $this->cURL('/sendChatAction',$post);

  }

  public function forwardMessage($from_chat_id,$user_id,$message_id,$disable_notification = false){

    $post = [
      'chat_id' => $user_id,
      'from_chat_id' => $from_chat_id,
      'message_id' => $message_id,
      'disable_notification' => $disable_notification
    ];

    return $this->cURL('/forwardMessage',$post);

  }

  public function sendMessage($user_id, $text, $keyboard = false, $type = false, $risposta = false, $forceReply = false, $notifica = false, $parse_mode = 'HTML', $disableWebPagePreview = true){

    if ($keyboard != false) {
        if ($type == 'fisica') {
            $rm = '{"keyboard":['.$keyboard.'],"resize_keyboard":true}';
        } else if($type == 'inline'){
            $rm = '{"inline_keyboard":['.$keyboard.'],"resize_keyboard":true}';
        }
    } else {
      $rm = '';
    }

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

    return $this->cURL('/sendMessage',$post);

  }

  public function sendMessage2($user_id, $text, $keyboard = false, $type = false, $risposta = false, $forceReply = false, $notifica = false, $parse_mode = 'HTML', $disableWebPagePreview = true){ #ATTENZIONE, può essere usato solo una volta nel file, e non restituisce alcun output

    if ($keyboard != false) {
        if ($type == 'fisica') {
            $rm = '{"keyboard":['.$keyboard.'],"resize_keyboard":true}';
        } else if($type == 'inline'){
            $rm = '{"inline_keyboard":['.$keyboard.'],"resize_keyboard":true}';
        }
    } else {
      $rm = '';
    }

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

    return $this->cURL('/getStickerSet',$post);

  }

  public function uploadStickerFile($user_id, $png_sticker){

    $ch = curl_init();

    $png_sticker = new CURLFile($png_sticker);

    $post = [
      'chat_id' => $user_id,
      'png_sticker' => $png_sticker
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);
    curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.$this->bot.'/uploadStickerFile');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $output = curl_exec($ch);
    curl_close($ch);

    return json_decode($output, TRUE);

  }

  public function sendSticker($user_id,$sticker, $keyboard = false, $type = false, $reply_to_message_id = false, $disable_notification = false, $forceReply = false){

    if ($keyboard != false) {
        if ($type == 'fisica') {
            $rm = '{"keyboard":['.$keyboard.'],"resize_keyboard":true}';
        } else if($type == 'inline'){
            $rm = '{"inline_keyboard":['.$keyboard.'],"resize_keyboard":true}';
        }
    } else {
      $rm = '';
    }

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

  public function sendPhoto($user_id, $photo, $caption = '', $keyboard = false, $type = false, $file_id = true){

    if ($keyboard != false) {
        if ($type == 'fisica') {
            $rm = '{"keyboard":['.$keyboard.'],"resize_keyboard":true}';
        } else if($type == 'inline'){
            $rm = '{"inline_keyboard":['.$keyboard.'],"resize_keyboard":true}';
        }
    } else {
      $rm = '';
    }

    $ch = curl_init();

    if($file_id == true){
      $args = [
        'caption' => $caption,
        'chat_id' => $user_id,
        'photo' => $photo,
        'reply_markup' => $rm,
        'parse_mode' => 'HTML'
      ];
    } else {
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);
      $photoFile = new CURLFile($photo);
      $args = [
        'caption' => $caption,
        'chat_id' => $user_id,
        'photo' => $photoFile,
        'reply_markup' => $rm,
        'parse_mode' => 'HTML'
      ];
    }
    curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.$this->bot.'/sendPhoto');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
    $output = curl_exec($ch);
    curl_close($ch);

    return json_decode($output, TRUE);

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
      'InputMedia' => $album,
      'caption' => $caption,
    ];

    return $this->cURL('/sendMediaGroup',$post);

  }

  public function sendDocument($user_id, $document, $file_id = true, $caption = false, $parse_mode = false){

    if($caption == false){
      $caption = '';
    }

    if($parse_mode == false){
      $parse_mode = 'HTML';
    }

    $ch = curl_init();

    if($file_id == true){
      $args = [
        'chat_id' => $user_id,
        'document' => $document,
        'caption' => $caption,
        'parse_mode' => $parse_mode
      ];
    } else {
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);
      $document = new CURLFile($document);
      $args = [
        'chat_id' => $user_id,
        'document' => $document,
        'caption' => $caption,
        'parse_mode' => $parse_mode
      ];
    }

    curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.$this->bot.'/sendDocument');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, TRUE);
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

    if ($keyboard != false) {
        if ($type == 'fisica') {
            $rm = '{"keyboard":['.$keyboard.'],"resize_keyboard":true}';
        } else if($type == 'inline'){
            $rm = '{"inline_keyboard":['.$keyboard.'],"resize_keyboard":true}';
        }
    } else {
      $rm = '';
    }

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

  public function editMessageText2($user_id, $message_id, $newText, $keyboard = false, $type = false, $parse_mode = 'HTML', $disableWebPagePreview = true){

    if ($keyboard != false) {
        if ($type == 'fisica') {
            $rm = '{"keyboard":['.$keyboard.'],"resize_keyboard":true}';
        } else if($type == 'inline'){
            $rm = '{"inline_keyboard":['.$keyboard.'],"resize_keyboard":true}';
        }
    } else {
      $rm = '';
    }

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

  public function leaveChat2($chat_id){

    header('Content-Type: application/json');

    $parameters = [
      'chat_id' => $user_id,
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

    if(!$token) $token = $this->bot;

    $post = [
      'token' => $token
    ];

    return $this->cURL('/deleteWebhook', $post);
  }

  public function setWebhook($token = false, $url = '', $max_connections = 40, $allowed_updates = ''){

    if(!$token) $token = $this->bot;

    $post = [
      'token' => $token,
      'url' => $url,
      'max_connections' => $max_connections,
      'allowed_updates' => $allowed_updates
    ];

    return $this->cURL('/setWebhook',$post);

  }

  public function getWebhookInfo($token = false){

    if(!$token) $token = $this->bot;

    $post = [
      'token' => $token
    ];

    return $this->cURL('/getWebhookInfo',$post);

  }

  public function getChatAdministrators($chat_id){

    $post = [
      'chat_id' => $chat_id
    ];

    return $this->cURL('/getChatAdministrators', $post);

  }

  public function getChatMembersCount($chat_id){

    $post = [
      'chat_id' => $chat_id
    ];

    return $this->cURL('/getChatMembersCount',$post);

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
