<?php

require_once "function/handler.php";
$update = json_decode(file_get_contents("php://input"));
if (isset($update)) {
    $telegram_ip_ranges = [["lower" => "149.154.160.0", "upper" => "149.154.175.255"], ["lower" => "91.108.4.0", "upper" => "91.108.7.255"]];
    $ip_dec = (int) sprintf("%u", ip2long($_SERVER["REMOTE_ADDR"]));
    $ok = false;
    foreach ($telegram_ip_ranges as $telegram_ip_range) {
        if (!$ok) {
            $lower_dec = (int) sprintf("%u", ip2long($telegram_ip_range["lower"]));
            $upper_dec = (int) sprintf("%u", ip2long($telegram_ip_range["upper"]));
            if ($lower_dec <= $ip_dec && $ip_dec <= $upper_dec) {
                $ok = true;
            }
        }
    }
    if (!$ok) {
        exit("<h1 style='text-align: center;margin-top:30px'>برای ورود به ربات به ایدی زیر مراجعه کنید <a href='tg://resolve?domain=" . $idbot . "'>@" . $idbot . "</a></h1>");
    }
}
if (isset($update->message)) {
    $message = $update->message;
    $text = $message->text;
    $tc = $message->chat->type;
    $cid = $message->chat->id;
    $fid = $message->from->id;
    $message_id = $message->message_id;
    $first_name = @str_replace([">", "<"], ["&gt;", "&lt;"], $message->from->first_name);
    $photo = $message->photo;
    $music = $message->audio;
    $caption = $message->caption;
    $contact = $message->contact;
    $contactid = $contact->user_id;
    $contactnum = $contact->phone_number;
    $for = $message->forward_from_chat;
    $forid = $for->id;
    $forusername = $for->username;
    $forname = $for->title;
    $fortype = $for->type;
    $data = NULL;
} else {
    if (isset($update->callback_query)) {
        $callback_query = $update->callback_query;
        $data = $callback_query->data;
        $tc = $callback_query->message->chat->type;
        $cid = $callback_query->message->chat->id;
        $fid = $callback_query->from->id;
        $message_id = $callback_query->message->message_id;
        $first_name = @str_replace([">", "<"], ["&gt;", "&lt;"], $callback_query->from->first_name);
        $callbackid = $callback_query->id;
        $text = $callback_query->message->text;
    } else {
        if (isset($update->channel_post)) {
            $channel_post = $update->channel_post;
            $cid = $channel_post->chat->id;
            $text = $channel_post->text;
            $fid = NULL;
            $tc = $channel_post->chat->type;
        } else {
            exit("<h1 style='text-align: center;margin-top:30px'>برای ورود به ربات به ایدی زیر مراجعه کنید <a href='tg://resolve?domain=" . $idbot . "'>@" . $idbot . "</a></h1>");
        }
    }
}
$admin = @$db->notuser($fid, "works");
$ban = @$db->notuser($fid, "banlist");
$user = @$db->info($fid, "users");
if ($tc == "private" || $tc == "channel") {
    require_once "bot_files/pravite.php";
    if (in_array($fid, $admins) || $admin !== 0) {
        require_once "bot_files/admin.php";
    }
} else {
    $bot->bot("leaveChat", ["chat_id" => $cid]);
}

?>