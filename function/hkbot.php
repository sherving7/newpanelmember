<?php
class hkbot
{
    public $token = NULL;
    public $url = NULL;
    public function __construct($token)
    {
        $this->token = $token;
        $this->url = "https://api.telegram.org";
    }
    public function bot($method, $data = NULL)
    {
        $url = $this->url . "/bot" . $this->token . "/" . $method;
        $c = curl_init($url);
        curl_setopt_array($c, [CURLOPT_POST => 1, CURLOPT_POSTFIELDS => $data, CURLOPT_RETURNTRANSFER => 1]);
        $r = curl_exec($c);
        curl_close($c);
        return json_decode($r);
    }
    public function sendMessage($fid, $text, $key = NULL)
    {
        return $this->bot("sendMessage", ["chat_id" => $fid, "text" => $text, "parse_mode" => "Html", "disable_web_page_preview" => true, "reply_markup" => $key]);
    }
    public function editmessagereplymarkup($fid, $messageid, $key = NULL)
    {
        return $this->bot("editmessagereplymarkup", ["chat_id" => $fid, "message_id" => $messageid, "reply_markup" => $key]);
    }
    public function editmessagetext($fid, $messageid, $text, $key = NULL)
    {
        return $this->bot("editmessagetext", ["chat_id" => $fid, "message_id" => $messageid, "text" => $text, "parse_mode" => "Html", "disable_web_page_preview" => true, "reply_markup" => $key]);
    }
    public function answerCallbackQuery($callbackid, $text = NULL, $true = false)
    {
        $this->bot("answerCallbackQuery", ["callback_query_id" => $callbackid, "text" => $text, "show_alert" => $true, "cache_time" => 5]);
    }
    public function deletemessage($fid, $messageid)
    {
        $this->bot("deletemessage", ["chat_id" => $fid, "message_id" => $messageid]);
    }
    public function checkjoin($chatid, $channel)
    {
        if (strpos($channel, "-") !== false) {
            $res = $this->bot("getChatMember", ["chat_id" => (int) $channel, "user_id" => (int) $chatid]);
        } else {
            $res = $this->bot("getChatMember", ["chat_id" => "@" . $channel, "user_id" => (int) $chatid]);
        }
        $tch = $res->result->status;
        return $tch;
    }
    public function getChatMember($user)
    {
        $name = $this->bot("getChatMember", ["chat_id" => (int) $user, "user_id" => (int) $user])->result->user->first_name;
        $name = str_replace([">", "/", "<"], ["&gt;", NULL, "&lt;"], $name);
        return $name;
    }
}

?>