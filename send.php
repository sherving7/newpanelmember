<?php

require_once "./function/handler.php";
$sendtoall = $db->info(1, "sendall");
if ($sendtoall["step"] == "sendall") {
    $alluser = $mysqli->query("SELECT `id` FROM `users`")->num_rows;
    $stmt = $mysqli->prepare("SELECT `id` FROM `users` LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $countsendmin, $sendtoall["user"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $res = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    if ($sendtoall["msgid"] == 0) {
        foreach ($res as $row) {
            $bot->sendMessage($row["id"], $sendtoall["text"]);
        }
    } else {
        foreach ($res as $row) {
            $bot->bot("sendphoto", ["chat_id" => $row["id"], "photo" => $sendtoall["msgid"], "caption" => $sendtoall["text"]]);
        }
    }
    $plus = $sendtoall["user"] + $countsendmin;
    $db->update("sendall", ["user" => $plus], 1, ["i"]);
    if ($alluser <= $plus) {
        $db->update("sendall", ["step" => "none", "msgid" => 0], 1, ["s", "s"]);
        $bot->sendMessage($sendtoall["admin"], "پیام برای همه کابران ارسال شد");
    }
}
if ($sendtoall["step"] == "forall") {
    $alluser = $mysqli->query("SELECT `id` FROM `users`")->num_rows;
    $stmt = $mysqli->prepare("SELECT `id` FROM `users` LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $countformin, $sendtoall["user"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $res = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    foreach ($res as $row) {
        $bot->bot("ForwardMessage", ["chat_id" => $row["id"], "from_chat_id" => $sendtoall["chat"], "message_id" => $sendtoall["msgid"]]);
    }
    $plus = $sendtoall["user"] + $countformin;
    $db->update("sendall", ["user" => $plus], 1, ["i"]);
    if ($alluser <= $plus) {
        $db->update("sendall", ["step" => "none", "msgid" => 0], 1, ["s", "s"]);
        $bot->sendMessage($sendtoall["admin"], "پیام برای همه کابران ارسال شد");
    }
}
$stmt = $mysqli->prepare("SELECT * FROM `list` WHERE `api`!='noapi' and `status`='Pending' ORDER BY `code` ASC LIMIT ?");
$stmt->bind_param("i", $numordercheck);
$stmt->execute();
$result = $stmt->get_result();
$list = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
foreach ($list as $row) {
    if ($db->notuser($row["api"], "api", "name", "s") == 1) {
        $ap = $db->info($row["api"], "api", "name", "s");
        $res = $api->status($ap["smartpanel"], $row["api"], $ap["api_url"], $ap["api_key"], $row["codeapi"]);
        if ($res !== false) {
            if ($res["status"] == "Completed") {
                $db->update("list", ["status" => $res["status"]], $row["code"], ["s"], "code");
                $bot->sendMessage($row["chatid"], $media->text(["order_confirmation2", $row["code"], $row["codeapi"], $channels["channel"], $entime, $fadate]));
            } else {
                if ($res["status"] == "Canceled" || $res["status"] == "Refunded") {
                    $db->update("list", ["status" => $res["status"]], $row["code"], ["s"], "code");
                    $res2 = $db->info($row["chatid"], "users");
                    $moj = $res2["balance"] + $row["factor"];
                    $orde = $res2["all_orders"] - 1;
                    $ordep = $res2["all_pay"] - $row["factor"];
                    $db->update("users", ["balance" => $moj, "all_orders" => $orde, "all_pay" => $ordep], $row["chatid"], ["s", "i", "i"]);
                    $bot->sendMessage($row["chatid"], $media->text(["order_cancel2", $row["code"], $row["codeapi"], $row["factor"], $channels["channel"], $entime, $fadate]));
                } else {
                    if ($res["status"] == "Partial") {
                        $a1 = $res["remains"] * $row["factor"] / $row["count"];
                        $back = number_format($a1, 0, "", "");
                        $db->update("list", ["status" => $res["status"]], $row["code"], ["s"], "code");
                        $res2 = $db->info($row["chatid"], "users");
                        $moj = $res2["balance"] + $back;
                        $ordep = $res2["all_pay"] - $back;
                        $db->update("users", ["balance" => $moj, "all_pay" => $ordep], $row["chatid"], ["s", "i"]);
                        $bot->sendMessage($row["chatid"], $media->text(["order_partial", $row["code"], $row["codeapi"], $back, $row["factor"], $channels["channel"], $entime, $fadate]));
                    }
                }
            }
        }
    }
}
$db->update("setting", ["lastcron" => time()], 1, ["s"]);
echo "ok";

?>