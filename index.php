<?php
/*
 Telegram.me/OneProgrammer
 Telegram.me/SpyGuard
                   ----[ Lotfan Copy Right Ro Rayat Konid <3 ]----
############################################################################################
# if you need Help for develop this source , You Can Send Message To Me With @SpyGuard_BOT #
############################################################################################
*/
define('API_KEY','YOUR_BOT_TOKEN');
include_once("basic.php");

//----######------
function makereq($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($datas));
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
//---------
$update = json_decode(file_get_contents('php://input'));
var_dump($update);
//=========
$chat_id = @$update->message->chat->id;
$message_id = @$update->message->message_id;
$from_id = @$update->message->from->id;
$name = @$update->message->from->first_name;
$username = @$update->message->from->username;
$textmessage = isset($update->message->text)?$update->message->text:'';
$reply = isset($update->message->reply_to_message->forward_from->id)?$update->message->reply_to_message->forward_from->id:'';
$forward = @$update->message->forward_from;
$photo = @$update->message->photo;
$video = @$update->message->video;
$sticker = @$update->message->sticker;
$file = @$update->message->document;
$music = @$update->message->audio;
$voice = @$update->message->voice;
$admins  = [66443035,0];
//-------
function SendMessage($ChatId, $TextMsg,$message_id = null,$parse_mode="MarkDown",$keyboard=null)
{
   makereq('sendMessage',[
  'chat_id'=>$ChatId,
  'text'=>$TextMsg,
  'parse_mode'=>$parse_mode,
  'reply_to_message_id'=>$message_id,
  'reply_markup'=>$keyboard
  ]);
}
function SendSticker($ChatId, $sticker_ID)
{
 makereq('sendSticker',[
'chat_id'=>$ChatId,
'sticker'=>$sticker_ID
]);
}
function Forward($KojaShe,$AzKoja,$KodomMSG)
{
makereq('ForwardMessage',[
'chat_id'=>$KojaShe,
'from_chat_id'=>$AzKoja,
'message_id'=>$KodomMSG
]);
}
$main_kb = [
  [
    ['text'=>"ثبت سفارش جدید"]
  ],
  [
    ['text'=>'👤مشخصات حساب'],
    ['text'=>'🚶🏻خروج از حساب']
  ]
];
//===========
$user = false;
$search_query = mysqli_query($db->con,"SELECT * FROM `users` WHERE `user_id` = '$from_id'");
if (mysqli_num_rows($search_query) == 1) {
  $user = mysqli_fetch_assoc($search_query);
  $check_account = $_4rd->request(['action'=>'account_check'],$user['token']);
  if ($check_account->result != "ok" && $user['token'] != 'null') {
    $db->update_user_field($from_id,"step",'enter_token');
    $db->update_user_field($from_id,"token","null");
    $user['step'] = 'enter_token';
    SendMessage($chat_id,"توکن حساب شما تغییر پیدا کرده !
لطفا از سایت توکن جدید را دریافت کرده و برای ربات بفرستید .");
  }
}
if ($textmessage == "ورود" && $user['step'] == "enter_token") {
  SendMessage($chat_id,"توکن خود را ارسال کنید : ");
}
elseif ($user['step'] == "enter_token") {
  SendMessage($chat_id,"در حال برسی توکن ...
لطفا کمی صبور باشید 🌹");
  $check_account = $_4rd->request(['action'=>'account_check'],$textmessage);
  if ($check_account->result == "ok") {
    SendMessage($chat_id,"✅توکن تایید شد
موجودی حساب شما : ".number_format($check_account->cash));
  $db->update_user_field($from_id,"step",'none');
  $db->update_user_field($from_id,"token",$textmessage);
  }
  else {
    SendMessage($chat_id,"توکن ارسالی اشتباه است !");
  }
}
elseif ($textmessage == "برگشت به صفحه اول") {
  $db->update_user_field($from_id,"step",'none');
  makereq('sendMessage',[
    'chat_id'=>$chat_id,
    "text"=>"🏡خانه :",
    'parse_mode'=>"MarkDown",
    'reply_markup'=>json_encode([
               'keyboard'=>$main_kb,
               'resize_keyboard'=>true
            ])
  ]);
}
elseif ($user['step'] == "submit_order") {
  if ($textmessage == "بله") {
    $fields_array = explode('(+)',$user['custom_field']);
    $service_id = $fields_array[0];
    $count = $fields_array[1];
    $link = $fields_array[2];
    $order = $_4rd->request(['action'=>'new_order','service'=>$service_id,'count'=>$count,'link'=>$link],$user['token']);
    if ($order->result == "ok") {
      $db->update_user_field($from_id,"step",'none');
      makereq('sendMessage',[
        'chat_id'=>$chat_id,
        "text"=>"✅سفارش شما با موفقیت ثبت شد",
        'parse_mode'=>"MarkDown",
        'reply_markup'=>json_encode([
                   'keyboard'=>$main_kb,
                   'resize_keyboard'=>true
                ])
      ]);
    }
    else {
      $db->update_user_field($from_id,"step",'none');
      makereq('sendMessage',[
        'chat_id'=>$chat_id,
        "text"=>$order->message,
        'parse_mode'=>"MarkDown",
        'reply_markup'=>json_encode([
                   'keyboard'=>$main_kb,
                   'resize_keyboard'=>true
                ])
      ]);
    }
  }
  elseif ($textmessage == "خیر") {
    $db->update_user_field($from_id,"step",'none');
    makereq('sendMessage',[
      'chat_id'=>$chat_id,
      "text"=>"🏡خانه :",
      'parse_mode'=>"MarkDown",
      'reply_markup'=>json_encode([
                 'keyboard'=>$main_kb,
                 'resize_keyboard'=>true
              ])
    ]);
  }
  else {
    SendMessage($chat_id,"لطفا از کیبرد استفاده کنید");
  }
}
elseif ($user['step'] == "enter_post_link") {
  $db->update_user_field($from_id,"step",'submit_order');
  
  $db->update_user_field($from_id,"custom_field",$user['custom_field']."(+)$textmessage");

  makereq('sendMessage',[
    'chat_id'=>$chat_id,
    "text"=>"آیا از سفارش خود مطمعن هستید ؟",
    'parse_mode'=>"MarkDown",
    'reply_markup'=>json_encode([
               'keyboard'=>[
                 [
                   ['text'=>'بله'],
                   ['text'=>'خیر']
                 ],
                 [
                   ['text'=>'برگشت به صفحه اول']
                 ]
               ],
               'resize_keyboard'=>true
            ])
  ]);
}
elseif ($user['step'] == "enter_order_count") {
  if (is_numeric($textmessage)) {
    if ($textmessage < 1000) {
      SendMessage($chat_id,"تعداد مورد نظر برای سفارش نباید کمتر از 1000 باشد !");
      return 0;
    }
    $db->update_user_field($from_id,"step",'enter_post_link');
    $db->update_user_field($from_id,"custom_field",$user['custom_field']."(+)$textmessage");
    makereq('sendMessage',[
      'chat_id'=>$chat_id,
      "text"=>"لطفا لینک پست خود را وارد کنید : ",
      'parse_mode'=>"MarkDown",
      'reply_markup'=>json_encode([
                 'keyboard'=>[
                   [
                     ['text'=>'برگشت به صفحه اول']
                   ]
                 ],
                 'resize_keyboard'=>true
              ])
    ]);
  }
  else {
    SendMessage($chat_id,"لطفا تعداد را به عدد و با اعداد انگلیسی وارد کنید .");
  }
}
elseif ($user['step'] == "new_order") {
  $item_array = explode('|',$textmessage);
  $item = $item_array[0];
  $item = str_replace("لایک ","1",$item);
  $item = str_replace("ویو ","2",$item);
  $db->update_user_field($from_id,"step",'enter_order_count');
  $db->update_user_field($from_id,"custom_field","$item");
  makereq('sendMessage',[
    'chat_id'=>$chat_id,
    "text"=>"تعداد مورد نظر خود برای سفارش را وارد کنید :",
    'parse_mode'=>"MarkDown",
    'reply_markup'=>json_encode([
               'keyboard'=>[
                 [
                   ['text'=>'برگشت به صفحه اول']
                 ]
               ],
               'resize_keyboard'=>true
            ])
  ]);
}
elseif($textmessage == '/start') {
  $search_query = mysqli_query($db->con,"SELECT * FROM `users` WHERE `user_id` = '$from_id'");
  if ($user != false) {

    SendMessage($chat_id,"سلام دوست عزیز به ربات خوش امدی 🌹

از دکمه های زیر برای عملیات مورد نظرت استفاده کن 👇🏽");
  }
  else {

  mysqli_query($db->con,"INSERT INTO `users` (`id`, `user_id`, `token`, `step`) VALUES (NULL, '$from_id', 'null', 'enter_token');");
  SendMessage($chat_id,"سلام دوست عزیز !
به ربات رسمی سایت 4rd خوش امدی 😊

برای شروع سفارش ویو و ویو شما باید اول وارد سایت 4rd.ir بشید و در اونجا ثبت نام کنید
سپس در داشبورد خود یک کلید API میبینید .
کلید API را کپی کنید و برای ربات بفرستید تا بتونید سفارش های خودتون از ربات به راحتی ثبت کنید 😉");
  }
}
elseif ($textmessage == "ثبت سفارش جدید") {
  $db->update_user_field($from_id,"step",'new_order');
  makereq('sendMessage',[
    'chat_id'=>$chat_id,
    "text"=>"چه محصولی را می خواهید خریداری کنید ؟",
    'parse_mode'=>"MarkDown",
    'reply_markup'=>json_encode([
               'keyboard'=>[
                 [
                   ['text'=>'لایک | 1,000 عدد ، 500 تک تومان']
                 ],
                 [
                   ['text'=>'ویو | 1,000 عدد ، 500 تک تومان']
                 ],
                 [
                   ['text'=>'برگشت به صفحه اول']
                 ]
               ],
               'resize_keyboard'=>true
            ])
  ]);
}
elseif ($textmessage == "👤مشخصات حساب") {
  $check_account = $_4rd->request(['action'=>'account_check'],$user['token']);
    SendMessage($chat_id,"👤مشخصات حساب :
نام حساب : ".$check_account->user_name."
نام کاربری : ".$check_account->number."
موجودی حساب شما : ".number_format($check_account->cash)." تومان");
}
elseif ($textmessage == "🚶🏻خروج از حساب") {
  makereq('sendMessage',[
    'chat_id'=>$chat_id,
    "text"=>"آیا مطمعن هستید که میخواهید از حساب خود خارج شوید ؟",
    'parse_mode'=>"MarkDown",
    'reply_markup'=>json_encode([
               'keyboard'=>[
                 [
                   ['text'=>'بله'],
                   ['text'=>'خیر']
                 ]
               ],
               'resize_keyboard'=>true
            ])
  ]);
}

elseif ($textmessage == "بله") {
  $db->update_user_field($from_id,"step",'enter_token');
  $db->update_user_field($from_id,"token","null");
  makereq('sendMessage',[
    'chat_id'=>$chat_id,
    "text"=>"شما با موفقیت از حساب خود خارج شدید !
  لطفا توکن جدید خود را بفرستید",
    'parse_mode'=>"MarkDown",
    'reply_markup'=>json_encode([
               'keyboard'=>[
                 [
                   ['text'=>'ورود']
                 ]
               ],
               'resize_keyboard'=>true
            ])
  ]);
}
else {
  $db->update_user_field($from_id,"step",'none');

  makereq('sendMessage',[
    'chat_id'=>$chat_id,
    "text"=>"🏡خانه :",
    'parse_mode'=>"MarkDown",
    'reply_markup'=>json_encode([
               'keyboard'=>$main_kb,
               'resize_keyboard'=>true
            ])
  ]);
}
?>
