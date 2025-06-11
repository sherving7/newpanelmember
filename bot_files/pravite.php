<?php
/******** @San_trich********
 * The Best*
 * God*
 * source Bot*
 * python - Php - Laravel*
 * Owner*
  ********* @Ziazl**********
*/
if ($off["member"] == 0) {
    $tch = "member";
} else {
    $tch = @$bot->checkjoin($fid, $channels["channel"]);
}
if ($off["free"] == 1) {
    if (in_array($fid, $admins) || $admin == 1) {
        $home = $media->keyboards(["home1"]);
        $home["keyboard"][] = [["text" => "پنل"]];
        $home = json_encode($home);
    } else {
        $home = json_encode($media->keyboards(["home1"]));
    }
} else {
    if (in_array($fid, $admins) || $admin == 1) {
        $home = $media->keyboards(["home2"]);
        $home["keyboard"][] = [["text" => "پنل"]];
        $home = json_encode($home);
    } else {
        $home = json_encode($media->keyboards(["home2"]));
    }
}
$request_contact = json_encode($media->keyboards(["request_contact"]));
$back = json_encode($media->keyboards(["back"]));
$lockchannel = json_encode($media->keyboards(["lockchannel", $channels["channel"]]));
$sharecontent = json_encode($media->keyboards(["request_contact"]));
if (!in_array($fid, $admins) && $admin != 1 && $off["bot"] == 0) {
    $bot->sendMessage($fid, $media->text(["off_all"]));
    exit;
}
if ($ban == 1) {
    $bot->sendMessage($fid, $media->text(["ban"]));
    exit;
}

if (isset($data)) {
    if ($data == "fyk") {
        $bot->answerCallbackQuery($callbackid, $media->text(["fyk"]), false);
    } else {
        if ($data == "close") {
            $bot->editmessagetext($fid, $message_id, $media->text(["close", $channels["channel"]]));
        } else {
            if ($data == "ozv") {
                if ($tch == "left") {
                    $bot->answerCallbackQuery($callbackid, $media->text(["notjoin"]), true);
                    exit;
                }
                $bot->deletemessage($fid, $message_id);
                $bot->sendMessage($fid, $botext["textstart"], $home);
                $bot->answerCallbackQuery($callbackid, $media->text(["joined"]), false);
            } else {
                if (strpos($data, "refjoin") !== false) {
                    if ($tch == "left") {
                        $bot->answerCallbackQuery($callbackid, $media->text(["notjoin"]), true);
                        exit;
                    }
                    $bot->deletemessage($fid, $message_id);
                    $newid = str_replace("refjoin", NULL, $data);
                    if ($off["number1"] == 0) {
                        $useref = $db->info($newid, "users");
                        $sumref = $useref["refral"] + 1;
                        if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 1) {
                            $gift1 = $useref["Gift1"] + $setting["Free_mojodi"];
                            $plusmoj = $useref["GiftMoj"] + $setting["Free_mojodi"];
                            $db->update("users", ["GiftMoj" => $plusmoj, "refral" => $sumref, "Gift1" => $gift1], $newid, ["i", "i", "i"]);
                            $db->insert($fid, $date, $newid);
                            $bot->sendMessage($newid, $media->text(["Gift0", $setting["Porsant"], $setting["Free_mojodi"]]));
                        } else {
                            if ($off["Free_mojodi"] == 0 && $off["Porsant"] == 1) {
                                $db->update("users", ["refral" => $sumref], $newid, ["i"]);
                                $db->insert($fid, $date, $newid);
                                $bot->sendMessage($newid, $media->text(["Gift1", $setting["Porsant"]]));
                            } else {
                                if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 0) {
                                    $gift1 = $useref["Gift1"] + $setting["Free_mojodi"];
                                    $plusmoj = $useref["GiftMoj"] + $setting["Free_mojodi"];
                                    $db->update("users", ["GiftMoj" => $plusmoj, "refral" => $sumref, "Gift1" => $gift1], $newid, ["i", "i", "i"]);
                                    $db->insert($fid, $date, 0);
                                    $bot->sendMessage($newid, $media->text(["Gift2", $setting["Free_mojodi"]]));
                                }
                            }
                        }
                        $bot->sendMessage($fid, $botext["textstart"], $home);
                        if ($off["First_Gift"] == 1 && 0 < $setting["First_Gift"]) {
                            $new = $user["balance"] + $setting["First_Gift"];
                            $db->update("users", ["balance" => $new], $fid, ["i"]);
                            $bot->sendMessage($fid, $media->text(["First_Gift", $setting["First_Gift"]]));
                        }
                    } else {
                        $deid = 0;
                        $link = $newid;
                        $step = "oknum";
                        $stmt = $mysqli->prepare("INSERT INTO `users`(`id`, `step`, `link`, `joindate` ,`refid`) VALUES (?,?,?,?,?)");
                        $stmt->bind_param("isisi", $fid, $step, $link, $date, $deid);
                        $stmt->execute();
                        $stmt->close();
                        $bot->sendMessage($fid, $media->text(["referral_authentication"]), json_encode(["keyboard" => [[["text" => $key["sharecontact"], "request_contact" => true]]], "resize_keyboard" => true]));
                        exit;
                    }
                } else {
                    if ($data == "closesefaresh") {
                        $db->update("users", ["step" => "none"], $fid, ["s"]);
                        $bot->deletemessage($fid, $message_id);
                        $bot->sendMessage($fid, $botext["textstart"], $home);
                        $bot->answerCallbackQuery($callbackid);
                    } else {
                        if ($data == "backn") {
                            $query = $mysqli->query("SELECT * FROM `button` WHERE `type`='1' and `off`='1'");
                            foreach ($query as $button) {
                                $t[] = [["text" => $button["text"], "callback_data" => "priceproduct" . $button["category"]]];
                            }
                            $t[] = [["text" => $key["closekey"], "callback_data" => "close"]];
                            $bot->editmessagereplymarkup($fid, $message_id, json_encode(["inline_keyboard" => $t]));
                        } else {
                            if (strpos($data, "priceproduct") !== false) {
                                $data = str_replace("priceproduct", NULL, $data);
                                $x[] = [["text" => $key["product"], "callback_data" => "fyk"], ["text" => $key["listprice"], "callback_data" => "fyk"]];
                                $list = $mysqli->query("SELECT * FROM `button` WHERE `category`='" . $data . "' and `type`='0' and `off`='1'");
                                $gg = 0;
                                foreach ($list as $btn) {
                                        $price = number_format($btn["price"] + $btn["price"] / 100 * $setting["darsad"], 0, "", ",");
                                        $x[] = [["text" => $btn["text"], "callback_data" => "fyk"], ["text" => $price, "callback_data" => "fyk"]];
                                    }
                                        $x[] = [["text" => $key["backpricekey"], "callback_data" => "backn"], ["text" => $key["closekey"], "callback_data" => "close"]];
                                        $bot->editmessagereplymarkup($fid, $message_id, json_encode(["inline_keyboard" => $x]));
                            }
                        }
                    }
                }
            }
        }
    }
}
if ($text == "/start") {
    $num = $db->notuser($fid, "users");
    if ($num == 0) {
        $db->insert($fid, $date, 0);
        $bot->sendMessage($fid, $botext["textstart"], $home);
        if ($off["First_Gift"] == 1 && 0 < $setting["First_Gift"]) {
            $new = $user["balance"] + $setting["First_Gift"];
            $db->update("users", ["balance" => $new], $fid, ["i"]);
            $bot->sendMessage($fid, $media->text(["First_Gift", $setting["First_Gift"]]));
        }
    } else {
        $db->update("users", ["step" => "none"], $fid, ["s"]);
        $bot->sendMessage($fid, $botext["textstart"], $home);
        if (in_array($fid, $admins) || $admin == 1) {
            $db->update("works", ["step" => "none"], $fid, ["s"]);
        }
    }
} else {
    if ($text == $key["back"]) {
        $num = $db->notuser($fid, "users");
        if ($num == 0) {
            $db->insert($fid, $date, 0);
            $bot->sendMessage($fid, $media->text(["back", $botext["textstart"]]), $home);
        } else {
            $db->update("users", ["step" => "none"], $fid, ["s"]);
            $bot->sendMessage($fid, $media->text(["back", $botext["textstart"]]), $home);
            if (in_array($fid, $admins) || $admin == 1) {
                $db->update("works", ["step" => "none"], $fid, ["s"]);
            }
        }
    } else {
    	/******** @San_trich********
 * The Best*
 * God*
 * source Bot*
 * python - Php - Laravel*
 * Owner*
  ********* @Ziazl**********
*/
        if (strpos($text, "/start ") !== false) {
            if ($off["free"] == 1) {
                $newid = str_replace("/start ", NULL, $text);
                if (is_numeric($newid)) {
                    if ($fid != $newid) {
                        $refid = $db->notuser($newid, "users");
                        if ($refid == 1) {
                            $users = $db->notuser($fid, "users");
                            if ($users == 0) {
                                if ($tch !== "left") {
                                    if ($off["number1"] == 0) {
                                        $useref = $db->info($newid, "users");
                                        $sumref = $useref["refral"] + 1;
                                        if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 1) {
                                            $gift1 = $useref["Gift1"] + $setting["Free_mojodi"];
                                            $plusmoj = $useref["GiftMoj"] + $setting["Free_mojodi"];
                                            $db->update("users", ["GiftMoj" => $plusmoj, "refral" => $sumref, "Gift1" => $gift1], $newid, ["i", "i", "i"]);
                                            $db->insert($fid, $date, $newid);
                                            $bot->sendMessage($newid, $media->text(["Gift0", $setting["Porsant"], $setting["Free_mojodi"]]));
                                        } else {
                                            if ($off["Free_mojodi"] == 0 && $off["Porsant"] == 1) {
                                                $db->update("users", ["refral" => $sumref], $newid, ["i"]);
                                                $db->insert($fid, $date, $newid);
                                                $bot->sendMessage($newid, $media->text(["Gift1", $setting["Porsant"]]));
                                            } else {
                                                if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 0) {
                                                    $gift1 = $useref["Gift1"] + $setting["Free_mojodi"];
                                                    $plusmoj = $useref["GiftMoj"] + $setting["Free_mojodi"];
                                                    $db->update("users", ["GiftMoj" => $plusmoj, "refral" => $sumref, "Gift1" => $gift1], $newid, ["i", "i", "i"]);
                                                    $db->insert($fid, $date, 0);
                                                    $bot->sendMessage($newid, $media->text(["Gift2", $setting["Free_mojodi"]]));
                                                }
                                            }
                                        }
                                        $bot->sendMessage($fid, $botext["textstart"], $home);
                                        if ($off["First_Gift"] == 1 && 0 < $setting["First_Gift"]) {
                                            $new = $user["balance"] + $setting["First_Gift"];
                                            $db->update("users", ["balance" => $new], $fid, ["i"]);
                                            $bot->sendMessage($fid, $media->text(["First_Gift", $setting["First_Gift"]]));
                                        }
                                    } else {
                                        $deid = 0;
                                        $link = $newid;
                                        $step = "oknum";
                                        $stmt = $mysqli->prepare("INSERT INTO `users`(`id`, `step`, `link`, `joindate` ,`refid`) VALUES (?,?,?,?,?)");
                                        $stmt->bind_param("isisi", $fid, $step, $link, $date, $deid);
                                        $stmt->execute();
                                        $stmt->close();
                                        $bot->sendMessage($fid, $media->text(["referral_authentication"]), json_encode(["keyboard" => [[["text" => $key["sharecontact"], "request_contact" => true]]], "resize_keyboard" => true]));
                                        exit;
                                    }
                                } else {
                                    $bot->sendMessage($fid, $media->text(["lock_channel", $channels["channel"]]), json_encode($media->keyboards(["lockchannel2", $channels["channel"], $newid])));
                                }
                            } else {
                                $db->update("users", ["step" => "none"], $fid, ["s"]);
                                $bot->sendMessage($fid, $media->text(["ismember"]), $home);
                            }
                        } else {
                            $num = $db->notuser($fid, "users");
                            if ($num == 0) {
                                $db->insert($fid, $date, 0);
                            }
                            $db->update("users", ["step" => "none"], $fid, ["s"]);
                            $bot->sendMessage($fid, $media->text(["notnewid", $newid]), $home);
                        }
                    } else {
                        $num = $db->notuser($fid, "users");
                        if ($num == 0) {
                            $db->insert($fid, $date, 0);
                        }
                        $db->update("users", ["step" => "none"], $fid, ["s"]);
                        $bot->sendMessage($fid, $media->text(["selfreferral"]), $home);
                    }
                } else {
                    $num = $db->notuser($fid, "users");
                    if ($num == 0) {
                        $db->insert($fid, $date, 0);
                    }
                    $db->update("users", ["step" => "none"], $fid, ["s"]);
                    $bot->sendMessage($fid, $media->text(["wrongnewid"]), $home);
                }
            } else {
                $num = $db->notuser($fid, "users");
                if ($num == 0) {
                    $db->insert($fid, $date, 0);
                }
                $db->update("users", ["step" => "none"], $fid, ["s"]);
                $bot->sendMessage($fid, $botext["textstart"], $home);
            }
        } else {
            if (isset($contact) && $user["step"] == "oknum") {
                $newid = $user["link"];
                if ($contactid == $fid) {
                    if (strpos($contactnum, "98") === 0 || strpos($contactnum, "+98") === 0) {
                        $contactuser = "0" . strrev(substr(strrev($contactnum), 0, 10));
                        if ($off["sms1"] == 1) {
                            $cod = rand(11111, 99999);
                            $pass = $setting["pasms"];
                            $pat = $setting["ptid"];
                            $sms = file_get_contents("https://fergalseen.ir/api/v1/sendsms?type=send&&api_key=$pass&&template=$pat&&phone=$contactuser&&value=$cod");
                            $yummy = json_decode($sms);
                            $smss = $yummy->status_code;
                            if ($smss == 200) {
                                $bot->sendMessage($fid, $media->text(["referral_authentication_sms"]), $back);
                                                                $db->update("users", ["step" => "sendsms", "ordername" => $contactuser, "price" => $cod], $fid, ["s", "s", "i"]);
                            } else {
                                $bot->sendMessage($admins[0], "مشکل در پنل sms\n\n" . $sms);
                                $bot->sendMessage($fid, $media->text(["again_referral_authentication"]), $sharecontent);
                            }
                        } else {
                            $useref = $db->info($newid, "users");
                            $sumref = $useref["refral"] + 1;
                            if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 1) {
                                $gift1 = $useref["Gift1"] + $setting["Free_mojodi"];
                                $plusmoj = $useref["GiftMoj"] + $setting["Free_mojodi"];
                                $db->update("users", ["GiftMoj" => $plusmoj, "refral" => $sumref, "Gift1" => $gift1], $newid, ["i", "i", "i"]);
                                $db->update("users", ["step" => "none", "link" => 0, "refid" => $newid], $fid, ["s", "s", "i"]);
                                $bot->sendMessage($newid, $media->text(["Gift0", $setting["Porsant"], $setting["Free_mojodi"]]));
                            } else {
                                if ($off["Free_mojodi"] == 0 && $off["Porsant"] == 1) {
                                    $db->update("users", ["refral" => $sumref], $newid, ["i"]);
                                    $db->update("users", ["step" => "none", "link" => 0, "refid" => $newid], $fid, ["s", "s", "i"]);
                                    $bot->sendMessage($newid, $media->text(["Gift1", $setting["Porsant"]]));
                                } else {
                                    if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 0) {
                                        $gift1 = $useref["Gift1"] + $setting["Free_mojodi"];
                                        $plusmoj = $useref["GiftMoj"] + $setting["Free_mojodi"];
                                        $db->update("users", ["GiftMoj" => $plusmoj, "refral" => $sumref, "Gift1" => $gift1], $newid, ["i", "i", "i"]);
                                        $db->update("users", ["step" => "none", "link" => 0, "refid" => 0], $fid, ["s", "s", "i", "s"]);
                                        $bot->sendMessage($newid, $media->text(["Gift2", $setting["Free_mojodi"]]));
                                    }
                                }
                            }
                            if ($off["First_Gift"] == 1 && 0 < $setting["First_Gift"]) {
                                $new = $user["balance"] + $setting["First_Gift"];
                                $db->update("users", ["balance" => $new], $fid, ["i"]);
                                $bot->sendMessage($fid, $media->text(["First_Gift", $setting["First_Gift"]]));
                            }
                            $bot->sendMessage($fid, $botext["textstart"], $home);
                        }
                    } else {
                        $bot->sendMessage($fid, $media->text(["notirancontact"]), $sharecontent);
                    }
                } else {
                    $bot->sendMessage($fid, $media->text(["onlykeyusecontact"]), $sharecontent);
                }
            } else {
                if ($user["step"] == "sendsms") {
                    if (is_numeric($text)) {
                        if ($text == $user["price"]) {
                            $contactuser = $user["ordername"];
                            $newid = $user["link"];
                            $bot->sendMessage($channels["channelnumber"], "شماره : <code>" . $contactuser . "</code>\n\t\t\t\t\tایدی : <code>" . $fid . "</code>\n\t\t\t\t\tاسم : <a href ='tg://user?id=" . $fid . "'>" . $first_name . "</a>\n\t\t\t\t\tتاریخ ثبت نام : " . $date);
                            $useref = $db->info($newid, "users");
                            $sumref = $useref["refral"] + 1;
                            if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 1) {
                                $gift1 = $useref["Gift1"] + $setting["Free_mojodi"];
                                $plusmoj = $useref["GiftMoj"] + $setting["Free_mojodi"];
                                $db->update("users", ["GiftMoj" => $plusmoj, "refral" => $sumref, "Gift1" => $gift1], $newid, ["i", "i", "i"]);
                                $db->update("users", ["step" => "none", "link" => 0, "refid" => $newid, "number" => $contactuser], $fid, ["s", "s", "i", "s"]);
                                $bot->sendMessage($newid, $media->text(["Gift0", $setting["Porsant"], $setting["Free_mojodi"]]));
                            } else {
                                if ($off["Free_mojodi"] == 0 && $off["Porsant"] == 1) {
                                    $db->update("users", ["refral" => $sumref], $newid, ["i"]);
                                    $db->update("users", ["step" => "none", "link" => 0, "refid" => $newid, "number" => $contactuser], $fid, ["s", "s", "i", "s"]);
                                    $bot->sendMessage($newid, $media->text(["Gift0", $setting["Porsant"]]));
                                } else {
                                    if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 0) {
                                        $gift1 = $useref["Gift1"] + $setting["Free_mojodi"];
                                        $plusmoj = $useref["GiftMoj"] + $setting["Free_mojodi"];
                                        $db->update("users", ["GiftMoj" => $plusmoj, "refral" => $sumref, "Gift1" => $gift1], $newid, ["i", "i", "i"]);
                                        $db->update("users", ["step" => "none", "link" => 0, "refid" => 0, "number" => $contactuser], $fid, ["s", "s", "i", "s"]);
                                        $bot->sendMessage($newid, $media->text(["Gift0", $setting["Free_mojodi"]]));
                                    }
                                }
                            }
                            /******** @San_trich********
 * The Best*
 * God*
 * source Bot*
 * python - Php - Laravel*
 * Owner*
  ********* @Ziazl**********
*/
                            if ($off["First_Gift"] == 1 && 0 < $setting["First_Gift"]) {
                                $new = $user["balance"] + $setting["First_Gift"];
                                $db->update("users", ["balance" => $new], $fid, ["i"]);
                                $bot->sendMessage($fid, $media->text(["First_Gift", $setting["First_Gift"]]));
                            }
                            $bot->sendMessage($fid, $botext["textstart"], $home);
                        } else {
                            $bot->sendMessage($fid, $media->text(["wrong_code_referral_authentication"]));
                        }
                    } else {
                        $bot->sendMessage($fid, $media->text(["int_code_referral_authentication"]));
                    }
                } else {
                    if ($tch !== "left") {
                        if ($text == $key["info"]) {
                            if ($off["free"] == 1) {
                                if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 1) {
                                    $tx = $media->text(["info1", $first_name, $fid, $user["balance"], $user["all_orders"], $user["all_pay"], $user["GiftMoj"], $user["refral"], $user["Gift1"], $user["Gift2"], $user["joindate"], $idbot]);
                                } else {
                                    if ($off["Free_mojodi"] == 0 && $off["Porsant"] == 1) {
                                        $tx = $media->text(["info2", $first_name, $fid, $user["balance"], $user["all_orders"], $user["all_pay"], $user["GiftMoj"], $user["refral"], $user["Gift2"], $user["joindate"], $idbot]);
                                    } else {
                                        if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 0) {
                                            $tx = $media->text(["info3", $first_name, $fid, $user["balance"], $user["all_orders"], $user["all_pay"], $user["GiftMoj"], $user["refral"], $user["Gift1"], $user["joindate"], $idbot]);
                                        } else {
                                            $tx = $media->text(["info0", $first_name, $fid, $user["balance"], $user["all_orders"], $user["all_pay"], $user["joindate"], $idbot]);
                                        }
                                    }
                                }
                            } else {
                                $tx = $media->text(["info0", $first_name, $fid, $user["balance"], $user["all_orders"], $user["all_pay"], $user["joindate"], $idbot]);
                            }
                            $bot->sendMessage($fid, $tx, $home);
                        } else {
                            if ($text == $key["support"]) {
                                if ($user["ticket"] < $setting["ticket"]) {
                                    $db->update("users", ["step" => "poshtibani"], $fid, ["s"]);
                                    $bot->sendMessage($fid, $media->text(["support"]), $back);
                                } else {
                                    $bot->sendMessage($fid, $media->text(["pendingsupport"]), $home);
                                }
                            } else {
                                if ($user["step"] == "poshtibani" && $text !== $key["back"]) {
                                    $tik = $user["ticket"] + 1;
                                    $db->update("users", ["step" => "none", "ticket" => $tik], $fid, ["s", "i"]);
                                    if ($photo == NULL) {
                                        $bot->sendMessage($channels["channelsupport"], "پیام از طرف <a href = 'tg://user?id=" . $fid . "'>" . $first_name . "</a> | #user_" . $fid . "\nمتن پیام : \n" . $text, json_encode(["inline_keyboard" => [[["text" => "پاسخ", "callback_data" => "tik" . $fid], ["text" => "رد", "callback_data" => "rad" . $fid]], [["text" => "مشخصات", "callback_data" => "info" . $fid], ["text" => "بن", "callback_data" => "ban" . $fid]]]]));
                                    } else {
                                        $file = $photo[count($photo) - 1]->file_id;
                                        $get = $bot->bot("getfile", ["file_id" => $file]);
                                        $patch = $get->result->file_path;
                                        $bot->bot("sendphoto", ["chat_id" => $channels["channelsupport"], "photo" => $file, "caption" => "پیام از طرف <a href = 'tg://user?id=" . $fid . "'>" . $first_name . "</a> | #user_" . $fid . "\n" . $caption, "parse_mode" => "Html", "reply_markup" => json_encode(["inline_keyboard" => [[["text" => "پاسخ", "callback_data" => "tik" . $fid], ["text" => "رد", "callback_data" => "rad" . $fid]], [["text" => "مشخصات", "callback_data" => "info" . $fid], ["text" => "بن", "callback_data" => "ban" . $fid]]]])]);
                                    }
                                    $bot->sendMessage($fid, $media->text(["oksupport"]), $home);
                                } else {
                                    if ($text == $key["pay"]) {
                                        if ($off["pay"] != 0) {
                                            if ($user["number"] == 0 && $off["number2"] != 0) {
                                                $db->update("users", ["step" => "contact"], $fid, ["s"]);
                                                $bot->sendMessage($fid, $media->text(["pay_authentication"]), $sharecontent);
                                            } else {
                                                if ($off["kart"] == 1) {
                                                    $bot->sendMessage($fid, $media->text(["pay_selection"]), json_encode($media->keyboards(["pay0"])));
                                                } else {
                                                    $db->update("users", ["step" => "buycoin"], $fid, ["s"]);
                                                    $bot->sendMessage($fid, $media->text(["pay_amount", $mindeposit, $maxdeposit]), $back);
                                                }
                                            }
                                        } else {
                                            $bot->sendMessage($fid, $media->text(["off_pay"]), $home);
                                        }
                                    } else {
                                        if ($text == $key["payoffline"]) {
                                            if ($off["pay"] !== 0 && $off["kart"] == 1) {
                                                $bot->sendMessage($fid, $botext["kartbekart"], json_encode($media->keyboards(["pay1"])));
                                            } else {
                                                $db->update("users", ["step" => "none"], $fid, ["s"]);
                                                $bot->sendMessage($fid, $media->text(["off_pay"]), $home);
                                            }
                                        } else {
                                            if ($text == $key["payonline"]) {
                                                if ($off["pay"] !== 0) {
                                                    $db->update("users", ["step" => "buycoin"], $fid, ["s"]);
                                                    $bot->sendMessage($fid, $media->text(["pay_amount", $mindeposit, $maxdeposit]), json_encode($media->keyboards(["pay2"])));
                                                } else {
                                                    $bot->sendMessage($fid, $media->text(["off_pay"]), $home);
                                                }
                                            } else {
                                                if ($user["step"] == "buycoin" && $text !== $key["back"]) {
                                                    if (is_numeric($text) && $mindeposit <= $text && $text <= $maxdeposit) {
                                                        $result = $mysqli->query("SELECT * FROM `payment` WHERE `off`=1");
                                                        if (0 < $result->num_rows) {
                                                            $code = $db->listpays($fid, "pay", 0, $text, $user["number"], $date);
                                                            if ($code["result"] == "OK") {
                                                                $res = $result->fetch_all(MYSQLI_ASSOC);
                                                                $t[] = [["text" => $media->text(["link_pay"]), "callback_data" => "fyk"]];
                                                                foreach ($res as $pa) {
                                                                    $t[] = [["text" => $media->text(["payment", $text, $pa["name"]]), "url" => "https://" . $domin . "/payment/" . $pa["file"] . ".php?code=" . $code["code"] . "&get"]];
                                                                }
                                                                $bot->sendMessage($fid, "✅ فاکتور افزایش موجودی با مبلغ $text تومان با موفقیت برای شما ساخته شد

☑️ تمامی پرداخت ها به صورت اتوماتیک بوده و پس از تراکنش موفق مبلغ آن به موجودی حساب شما در ربات افزوده خواهد شد .

👇🏻 پرای پرداخت کافیست از دکمه زیر استفاده کنید", json_encode(["inline_keyboard" => $t]));
                                                                $db->update("users", ["step" => "none"], $fid, ["s"]);
                                                                $bot->sendMessage($fid, $botext["backpay"], $home);
                                                            } else {
                                                                $db->update("users", ["step" => "none"], $fid, ["s"]);
                                                                $bot->sendMessage($fid, $media->text(["pay_mistake"]), $home);
                                                            }
                                                        } else {
                                                            $db->update("users", ["step" => "none"], $fid, ["s"]);
                                                            $bot->sendMessage($admins[0], "همه درگاه ها خاموش هستند");
                                                            $bot->sendMessage($fid, $media->text(["pay_mistake"]), $home);
                                                        }
                                                    } else {
                                                        $bot->sendMessage($fid, $media->text(["pay_int"]));
                                                    }
                                                } else {
                                                    if ($user["step"] == "sendsms2") {
                                                        if ($sms['status_code'] == 200) {
                                                            if ($text == $user["price"]) {
                                                                $contactuser = $user["ordername"];
                                                                $db->update("users", ["step" => "buycoin", "ordername" => 0, "number" => $contactuser], $fid, ["s", "s", "s"]);
                                                                $bot->sendMessage($channels["channelnumber"], "شماره : <code>" . $contactuser . "</code>\n\t\t\t\t\t\t\t\t\tایدی : <code>" . $fid . "</code>\n\t\t\t\t\t\t\t\t\tاسم : <a href ='tg://user?id=" . $fid . "'>" . $first_name . "</a>\n\t\t\t\t\t\t\t\t\tتاریخ ثبت نام : " . $date);
                                                                if ($off["kart"] == 1) {
                                                                    $bot->sendMessage($fid, $media->text(["pay_selection"]), json_encode($media->keyboards(["pay0"])));
                                                                } else {
                                                                    $bot->sendMessage($fid, $media->text(["pay_amount", $mindeposit, $maxdeposit]), $back);
                                                                }
                                                            } else {
                                                                $bot->sendMessage($fid, $media->text(["pay_authentication_wrongcode"]));
                                                            }
                                                        } else {
                                                            $bot->sendMessage($fid, $media->text(["pay_authentication_int"]));
                                                        }
                                                    } else {
                                                        if (isset($contact) && $user["step"] == "contact") {
                                                            if ($contactid == $fid) {
                                                                if (strpos($contactnum, "98") === 0 || strpos($contactnum, "+98") === 0) {
                                                                    $contactuser = "0" . strrev(substr(strrev($contactnum), 0, 10));
                                                                    if ($off["sms2"] == 1) {
                                                                        $code = rand(11111, 99999);
                                                                                                    $pass = $setting["pasms"];
                            $pat = $setting["ptid"];
                                                                         $sms = file_get_contents("https://fergalseen.ir/api/v1/sendsms?type=send&&api_key=$pass&&template=$pat&&phone=$contactuser&&value=$code");
                                                                         $yummy = json_decode($sms);
                            $smss = $yummy->status_code;
                            if ($smss == 200) {
$db->update("users", ["step" => "sendsms", "ordername" => $contactuser, "price" => $code], $fid, ["s", "s", "i"]);

                                                                            $bot->sendMessage($fid, $media->text(["pay_authentication_sendsms"]), $back);
                                                                        } else {
                                                                            $bot->sendMessage($admins[0], "مشکل در پنل sms\n\n" . $sms);
                                                                            $bot->sendMessage($fid, $media->text(["pay_authentication_sendsms_again"]), $sharecontent);
                                                                        }
                                                                    } else {
                                                                        $bot->sendMessage($channels["channelnumber"], "شماره : <code>" . $contactuser . "</code>\n\t\t\t\t\t\t\t\t\t\tایدی : <code>" . $fid . "</code>\n\t\t\t\t\t\t\t\t\t\tاسم : <a href ='tg://user?id=" . $fid . "'>" . $first_name . "</a>\n\t\t\t\t\t\t\t\t\t\tتاریخ ثبت نام : " . $date);
                                                                        $db->update("users", ["step" => "buycoin", "number" => $contactuser], $fid, ["s", "i"]);
                                                                        if ($off["kart"] == 1) {
                                                                            $bot->sendMessage($fid, $media->text(["pay_selection"]), json_encode($media->keyboards(["pay0"])));
                                                                        } else {
                                                                            $bot->sendMessage($fid, $media->text(["pay_amount", $mindeposit, $maxdeposit]), $back);
                                                                        }
                                                                    }
                                                                } else {
                                                                    $bot->sendMessage($fid, $media->text(["pay_authentication_notiran"]), $sharecontent);
                                                                }
                                                            } else {
                                                                $bot->sendMessage($fid, $media->text(["pay_authentication_justkey"]), $sharecontent);
                                                            }
                                                        } else {
                                                            if ($text == $key["free"]) {
                                                                if ($off["free"] == 1 && ($off["Free_mojodi"] == 1 || $off["Porsant"] == 1)) {
                                                                    $bot->bot("sendphoto", ["chat_id" => $fid, "photo" => new CURLFile("baner.jpg"), "caption" => $botext["banertx"] . "\n https://t.me/" . $idbot . "?start=" . $fid]);
                                                                    if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 1) {
                                                                        $tx = $media->text(["referral_text1", $setting["Free_mojodi"], $setting["Porsant"]]);
                                                                    } else {
                                                                        if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 0) {
                                                                            $tx = $media->text(["referral_text2", $setting["Free_mojodi"]]);
                                                                        } else {
                                                                            if ($off["Free_mojodi"] == 0 && $off["Porsant"] == 1) {
                                                                                $tx = $media->text(["referral_text3", $setting["Porsant"]]);
                                                                            }
                                                                        }
                                                                    }
                                                                    $bot->sendMessage($fid, $tx, json_encode($media->keyboards(["gift"])));
                                                                } else {
                                                                    $bot->sendMessage($fid, $media->text(["off_referral"]), $home);
                                                                }
                                                            } else {
                                                                if ($text == $key["inforeferral"]) {
                                                                    $bot->sendMessage($fid, $botext["reftx"]);
                                                                } else {
                                                                    if ($text == $key["balance"]) {
                                                                        $gipay = $user["Gift2"] + $user["Gift1"] - $user["GiftMoj"];
                                                                        if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 1) {
                                                                            $tx = $media->text(["inforeferral0", $first_name, $fid, $user["Gift1"], $user["Gift2"], $user["refral"], $user["GiftMoj"], $gipay, $idbot]);
                                                                        } else {
                                                                            if ($off["Free_mojodi"] == 0 && $off["Porsant"] == 1) {
                                                                                $tx = $media->text(["inforeferral1", $first_name, $fid, $user["Gift2"], $user["refral"], $user["GiftMoj"], $gipay, $idbot]);
                                                                            } else {
                                                                                if ($off["Free_mojodi"] == 1 && $off["Porsant"] == 0) {
                                                                                    $tx = $media->text(["inforeferral2", $first_name, $fid, $user["Gift1"], $user["refral"], $user["GiftMoj"], $gipay, $idbot]);
                                                                                }
                                                                            }
                                                                        }
                                                                        $bot->sendMessage($fid, $tx);
                                                                    } else {
                                                                        if ($text == $key["changegiftbalance"]) {
                                                                            if ($mintomovebalnce <= $user["GiftMoj"]) {
                                                                                $db->update("users", ["step" => "movemoj"], $fid, ["s"]);
                                                                                $bot->sendMessage($fid, $media->text(["amountgiftbalance", $mintomovebalnce, $user["GiftMoj"]]), $back);
                                                                            } else {
                                                                                $bot->sendMessage($fid, $media->text(["mingiftbalance", $mintomovebalnce]), $home);
                                                                            }
                                                                        } else {
                                                                            if ($user["step"] == "movemoj") {
                                                                                if (is_numeric($text)) {
                                                                                    if ($mintomovebalnce <= $text && $text <= $user["GiftMoj"]) {
                                                                                        $newmj = $user["balance"] + $text;
                                                                                        $newmGiftMoj = $user["GiftMoj"] - $text;
                                                                                        $db->update("users", ["step" => "none", "balance" => $newmj, "GiftMoj" => $newmGiftMoj], $fid, ["s", "i", "i"]);
                                                                                        $bot->sendMessage($fid, $media->text(["okgiftbalance", $text]), $home);
                                                                                    } else {
                                                                                        $bot->sendMessage($fid, $media->text(["amountgiftbalance_wrong", $mintomovebalnce, $user["GiftMoj"]]));
                                                                                    }
                                                                                } else {
                                                                                    $bot->sendMessage($fid, $media->text(["giftbalance_int"]));
                                                                                }
                                                                            } else {
                                                                                if ($text == $key["withdrawbalance"]) {
                                                                                    if ($minwithdrawbalance <= $user["GiftMoj"]) {
                                                                                        $db->update("users", ["step" => "giv2"], $fid, ["s"]);
                                                                                        $bot->sendMessage($fid, $media->text(["amountwithdrawbalance", $minwithdrawbalance, $user["GiftMoj"]]), $back);
                                                                                    } else {
                                                                                        $bot->sendMessage($fid, $media->text(["minwithdrawbalance", $minwithdrawbalance]), $home);
                                                                                    }
                                                                                } else {
                                                                                    if ($user["step"] == "giv2") {
                                                                                        if (is_numeric($text) && $minwithdrawbalance <= $text && $text <= $user["GiftMoj"]) {
                                                                                            $db->update("users", ["step" => "giv3", "price" => $text], $fid, ["s", "i"]);
                                                                                            $bot->sendMessage($fid, $media->text(["infowithdrawbalance"]));
                                                                                        } else {
                                                                                            $bot->sendMessage($fid, $media->text(["amountwithdrawbalance_wrong", $minwithdrawbalance, $user["GiftMoj"]]));
                                                                                        }
                                                                                    } else {
                                                                                        if ($user["step"] == "giv3" && $text !== $key["back"]) {
                                                                                            $req = $user["price"];
                                                                                            $code = $db->listpaysout($fid, "pay", "gift", $req, $date);
                                                                                            if ($code["result"] == "OK") {
                                                                                                $newmj = $user["GiftMoj"] - $req;
                                                                                                $db->update("users", ["step" => "none", "price" => 0, "GiftMoj" => $newmj], $fid, ["s", "i", "i"]);
                                                                                                $bot->sendMessage($channels["channelout"], "در خواست برداشت درآمد <code>" . $code["code"] . "</code>\n\t\t\t\t\t\t\t\t\t\t\tنام : <a href ='tg://user?id=" . $fid . "'>" . $first_name . "</a>\n\t\t\t\t\t\t\t\t\t\t\tایدی عددی : <code>" . $fid . "</code>\n\t\t\t\t\t\t\t\t\t\t\tمیزان درخواست : " . $req . "\n\t\t\t\t\t\t\t\t\t\t\tاطلاعات فرستاده شده : " . $text . "\n\t\t\t\t\t\t\t\t\t\t\tزمان ثبت : " . $date, json_encode(["inline_keyboard" => [[["text" => "پرداخت", "callback_data" => "payout" . $code["code"]], ["text" => "کنسل", "callback_data" => "outref" . $code["code"]]]]]));
                                                                                                $bot->sendMessage($fid, $media->text(["okwithdrawbalance", $code["code"]]), $home);
                                                                                            } else {
                                                                                                $db->update("users", ["step" => "none", "price" => 0], $fid, ["s", "i"]);
                                                                                                $bot->sendMessage($fid, $media->text(["error_withdrawbalance"]), $home);
                                                                                            }
                                                                                        } else {
                                                                                            if ($text == $key["stutus"]) {
                                                                                                $bot->sendMessage($fid, $media->text(["stutus"]), json_encode($media->keyboards(["stutus"])));
                                                                                            } else {
                                                                                                if ($text == $key["lastpay"]) {
                                                                                                    $stmt = $mysqli->prepare("SELECT * FROM `pays` WHERE  `chatid`=? ORDER BY `code` DESC LIMIT 10");
                                                                                                    $stmt->bind_param("i", $fid);
                                                                                                    $stmt->execute();
                                                                                                    $result = $stmt->get_result();
                                                                                                    if (0 < $result->num_rows) {
                                         /******** @San_trich********
 * The Best*
 * God*
 * source Bot*
 * python - Php - Laravel*
 * Owner*
  ********* @Ziazl**********
*/                                                               $res = $result->fetch_all(MYSQLI_ASSOC);
                                                                                                        $tx = $media->text(["lastpay0"]);
                                                                                                        foreach ($res as $row) {
                                                                                                            if ($row["step"] == "OK" || $row["step"] == 100) {
                                                                                                                $stp = "موفق";
                                                                                                                $tx .= $media->text(["lastpay1", $stp, $row["amount"], $row["paycode"], $row["code"], $row["date"]]);
                                                                                                            } else {
                                                                                                                if ($row["step"] == "pay") {
                                                                                                                    $stp = "پرداخت نشده است";
                                                                                                                    $tx .= $media->text(["lastpay2", $stp, $row["amount"], $row["code"], $row["date"]]);
                                                                                                                } else {
                                                                                                                    $stp = "ناموفق";
                                                                                                                    $tx .= $media->text(["lastpay3", $stp, $row["amount"], $row["code"], $row["date"]]);
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    } else {
                                                                                                        $tx = $media->text(["notpay"]);
                                                                                                    }
                                                                                                    $bot->sendMessage($fid, $tx);
                                                                                                    $stmt->close();
                                                                                                } else {
                                                                                                    if ($text == $key["lastorder"]) {
                                                                                                        $stmt = $mysqli->prepare("SELECT * FROM `list` WHERE  `chatid`=? ORDER BY `code` DESC LIMIT 10");
                                                                                                        $stmt->bind_param("i", $fid);
                                                                                                        $stmt->execute();
                                                                                                        $result = $stmt->get_result();
                                                                                                        if (0 < $result->num_rows) {
                                                                                                            $res = $result->fetch_all(MYSQLI_ASSOC);
                                                                                                            $tx = $media->text(["lastorder0"]);
                                                                                                            foreach ($res as $row) {
                                                                                                                if ($row["api"] == "noapi") {
                                                                                                                    $tx .= $media->text(["lastorder1", $row["sefaresh"], $row["status"], $row["code"], $row["link"], $row["count"], $row["date"]]);
                                                                                                                } else {
                                                                                                                    if ($db->notuser($row["api"], "api", "name", "s") == 1) {
                                                                                                                        $ap = $db->info($row["api"], "api", "name", "s");
                                                                                                                        $res = $api->status($ap["smartpanel"], $row["api"], $ap["api_url"], $ap["api_key"], $row["codeapi"]);
                                                                                                                        $tx .= $media->text(["lastorder2", $row["sefaresh"], $res["status"], $row["code"], $row["codeapi"], $row["link"], $row["count"], $row["date"]]);
                                                                                                                    } else {
                                                                                                                        $tx .= $media->text(["lastorder1", $row["sefaresh"], $row["status"], $row["code"], $row["link"], $row["count"], $row["date"]]);
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        } else {
                                                                                                            $tx = $media->text(["notorder"]);
                                                                                                        }
                                                                                                        $bot->sendMessage($fid, $tx);
                                                                                                        $stmt->close();
                                                                                                    } else {
                                                                                                        if ($text == $key["stutusorder"]) {
                                                                                                            $db->update("users", ["step" => "peygiri"], $fid, ["s"]);
                                                                                                            $bot->sendMessage($fid, $media->text(["ordercode"]), $back);
                                                                                                        } else {
                                                                                                            if ($user["step"] == "peygiri" && $text !== $key["back"]) {
                                                                                                                $a1 = $db->notuser($text, "list", "code", "s");
                                                                                                                $a2 = $db->notuser($text, "list", "codeapi", "s");
                                                                                                                if ($a1 == 1 || $a2 == 1) {
                                                                                                                    if ($a1 == 1) {
                                                                                                                        $infoorder = $db->info($text, "list", "code", "s");
                                                                                                                    } else {
                                                                                                                        if ($a2 == 1) {
                                                                                                                            $infoorder = $db->info($text, "list", "codeapi", "s");
                                                                                                                        }
                                                                                                                    }
                                                                                                                    if ($infoorder["api"] == "noapi") {
                                                                                                                        $res = $db->info($text, "list", "code", "s");
                                                                                                                    } else {
                                                                                                                        $ap = $db->info($infoorder["api"], "api", "name", "s");
                                                                                                                        if ($ap !== false) {
                                                                                                                            $res = $api->status($ap["smartpanel"], $infoorder["api"], $ap["api_url"], $ap["api_key"], $infoorder["codeapi"]);
                                                                                                                        } else {
                                                                                                                            if ($a1 == 1) {
                                                                                                                                $res = $db->info($text, "list", "code", "s");
                                                                                                                            } else {
                                                                                                                                if ($a2 == 1) {
                                                                                                                                    $res = $db->info($text, "list", "codeapi", "s");
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                    $db->update("users", ["step" => "none"], $fid, ["s"]);
                                                                                                                    if ($res["status"] == "Pending") {
                                                                                                                        $bot->sendMessage($fid, $media->text(["Pending", $infoorder]), $home);
                                                                                                                    } else {
                                                                                                                        if ($res["status"] == "Processing") {
                                                                                                                            $bot->sendMessage($fid, $media->text(["Processing", $infoorder]), $home);
                                                                                                                        } else {
                                                                                                                            if ($res["status"] == "In progress") {
                                                                                                                                $bot->sendMessage($fid, $media->text(["In progress", $infoorder]), $home);
                                                                                                                            } else {
                                                                                                                                if ($res["status"] == "Completed") {
                                                                                                                                    $bot->sendMessage($fid, $media->text(["Completed", $infoorder]), $home);
                                                                                                                                } else {
                                                                                                                                    if ($res["status"] == "Canceled") {
                                                                                                                                        $bot->sendMessage($fid, $media->text(["Canceled", $infoorder]), $home);
                                                                                                                                    } else {
                                                                                                                                        if ($res["status"] == "Partial") {
                                                                                                                                            $bot->sendMessage($fid, $media->text(["Partial", $infoorder]), $home);
                                                                                                                                        } else {
                                                                                                                                            $bot->sendMessage($fid, $media->text(["Unknown_status", $infoorder]), $home);
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    $bot->sendMessage($fid, $media->text(["wrong_code"]));
                                                                                                                }
                                                                                                            } else {
                                                                                                                if ($text == $key["price"]) {
                                                                                                                    $query = $mysqli->query("SELECT * FROM `button` WHERE `type`='1' and `off`='1'");
                                                                                                                    $gg = 0;
                                                                                                                    foreach ($query as $button) {

                                                                                                                            $t[] = [["text" => $button["text"], "callback_data" => "priceproduct" . $button["category"]]];
                                                                                                                    }
                                                                                                                            $t[] = [["text" => $key["closekey"], "callback_data" => "close"]];
                                                                                                                            $bot->sendMessage($fid, $media->text(["price_info", $idbot]), json_encode(["inline_keyboard" => $t]));
 
                                                                                                                } else {
                                                                                                                    if ($text == $key["order"] || $text == $key["backtoshop"] && $user["step"] == "buy1") {
                                                                                                                        if ($off["buy"] == 1) {
                                                                                                                            $db->update("users", ["step" => "buy"], $fid, ["s"]);
                                                                                                                            $query = $mysqli->query("SELECT * FROM `button` WHERE `type`='1' and `off`='1'");
                                                                                                                            $gg = 0;
                                                                                     $perLine = 2; 
                                                                                     $column  = $count = 0;                                      foreach ($query as $button) {
                        if($count == $perLine){
        $column++;
        $count = 0;
    }                                                                                                            $t[$column][] = ["text" => $button["text"]];
                                                                             $count++;                                               }
                                                                                                                                    $t[] = [["text" => $key["back"]]];
                                                                                                                                    $bot->sendMessage($fid, $media->text(["shop1"]), json_encode(["keyboard" => $t, "resize_keyboard" => true]));
                                                                                       
                                                                                                                        } else {
                                                                                                                            $bot->sendMessage($fid, $media->text(["off_buy"]));
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        if ($user["step"] == "buy" && $text !== $key["back"] || $text == $key["backtoshop"] && $user["step"] == "buy2") {
                                                                                                                            if ($text == $key["backtoshop"] && $user["step"] == "buy2") {
                                                                                                                                $stmt = $mysqli->prepare("SELECT `text`,`category` FROM `button` WHERE `text`=? and `type`='1' and `off`='1'");
                                                                                                                                $stmt->bind_param("s", $user["ordername"]);
                                                                                                                                $stmt->execute();
                                                                                                                                $result = $stmt->get_result();
                                                                                                                                $stmt->close();
                                                                                                                                if ($result->num_rows === 1) {
                                                                                                                                    $buttonlist = $result->fetch_assoc();
                                                                                                                                    $db->update("users", ["step" => "buy1"], $fid, ["s"]);
                                                                                                                                    $list = $mysqli->query("SELECT * FROM `button` WHERE `category`=" . $buttonlist["category"] . " and `type`='0' and `off`='1'");
                                                                                                                                    $gg = 0;
                            $perLine = 2; 
                                                                                     $column  = $count = 0;                                                                                                            foreach ($list as $btn) {
                             if($count == $perLine){
        $column++;
        $count = 0;
    }                                                                                                                                       $x[$column][] = ["text" => $btn["text"]];
                $count++;                                                                                                                                                             }
                                                                                                                                            $x[] = [["text" => $key["backtoshop"]], ["text" => $key["back"]]];
                                                                                                                                            $bot->sendMessage($fid, $media->text(["shop2", $user["ordername"]]), json_encode(["keyboard" => $x, "resize_keyboard" => true]));
                                                                                                                                } else {
                                                                                                                                    $bot->sendMessage($fid, $media->text(["shop_justkey"]));
                                                                                                                                }
                                                                                                                            } else {
                                                                                                                                $stmt = $mysqli->prepare("SELECT `text`,`category` FROM `button` WHERE `text`=? and `type`='1' and `off`='1'");
                                                                                                                                $stmt->bind_param("s", $text);
                                                                                                                                $stmt->execute();
                                                                                                                                $result = $stmt->get_result();
                                                                                                                                $stmt->close();
                                                                                                                                if ($result->num_rows === 1) {
                                                                                                                                    $buttonlist = $result->fetch_assoc();
                                                                                                                                    $db->update("users", ["step" => "buy1", "link" => $buttonlist["category"], "ordername" => $text], $fid, ["s", "s", "s"]);
                                                                                                                                    $list = $mysqli->query("SELECT * FROM `button` WHERE `category`=" . $buttonlist["category"] . " and `type`='0' and `off`='1'");
                                                                                                                   $perLine = 2; 
                                                                                     $column  = $count = 0;                     foreach ($list as $btn) {
         if($count == $perLine){
        $column++;
        $count = 0;
    }                                                                                                                                                       $x[$column][] = ["text" => $btn["text"]];
    $count++;                                          
                                                                                                                                    }
/******** @San_trich********
 * The Best*
 * God*
 * source Bot*
 * python - Php - Laravel*
 * Owner*
  ********* @Ziazl**********
*/                                                                                                                                    $x[] = [["text" => $key["backtoshop"]], ["text" => $key["back"]]];
                                                                                                                                    $bot->sendMessage($fid, $media->text(["shop2", $text]), json_encode(["keyboard" => $x, "resize_keyboard" => true]));
                                                                                                                                } else {
                                                                                                                                    $bot->sendMessage($fid, $media->text(["shop_justkey"]));
                                                                                                                                }
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            if ($user["step"] == "buy1" && $text !== $key["back"]) {
                                                                                                                                $stmt = $mysqli->prepare("SELECT * FROM `button` WHERE `text`=? and `category`=? and `type`='0' and `off`='1'");
                                                                                                                                $stmt->bind_param("ss", $text, $user["link"]);
                                                                                                                                $stmt->execute();
                                                                                                                                $result = $stmt->get_result();
                                                                                                                                if ($result->num_rows === 1) {
                                                                                                                                    $butt = $result->fetch_assoc();
                                                                                                                                    $stmt->close();
                                                                                                                                    $price = number_format($butt["price"] + $butt["price"] / 100 * $setting["darsad"], 0, "", "");
                                                                                                                                    $db->update("users", ["step" => "buy2", "ordername1" => $text, "min" => $butt["min"], "max" => $butt["max"], "serviceid" => $butt["serviceid"], "price" => $price], $fid, ["s", "s", "i", "i", "i", "i"]);
                                                                                                                                    $re = $bot->sendMessage($fid, $media->text(["shop3", $user["ordername"], $text, $price, $butt["min"], $butt["max"], $butt["info"]]), json_encode(["keyboard" => [[["text" => $key["backtoshop"]], ["text" => $key["back"]]]], "resize_keyboard" => true]));
                                                                                                                                } else {
                                                                                                                                    $bot->sendMessage($fid, $media->text(["shop_justkey"]));
                                                                                                                                }
                                                                                                                            } else {
                                                                                                                                if ($user["step"] == "buy2" && $text !== $key["back"]) {
                                                                                                                                    if (is_numeric($text) && $user["min"] <= $text && $text <= $user["max"]) {
                                                                                                                                        $a1 = $user["price"] / 1000;
                                                                                                                                        $a2 = $text * $a1;
                                                                                                                                        if ($a2 <= $user["balance"]) {
                                                                                                                                            $sef = $user["ordername"] . " | " . $user["ordername1"];
                                                                                                                                            $db->update("users", ["step" => "buy3", "sefaresh" => $sef, "price" => $a2, "count" => $text], $fid, ["s", "s", "i", "i"]);
                                                                                                                                            $bot->sendMessage($fid, $media->text(["shop4", $channels["channel"]]), $back);
                                                                                                                                        } else {
                                                                                                                                            $db->update("users", ["step" => "none"], $fid, ["s"]);
                                                                                                                                            $bot->sendMessage($fid, $media->text(["lowbalance", $a2]), $home);
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        $bot->sendMessage($fid, $media->text(["shop_range", $user["min"], $user["max"]]));
                                                                                                                                    }
                                                                                                                                } else {
                                                                                                                                    if ($user["step"] == "buy3") {
                                                                                                                                        if (preg_match("/^[a-zA-Z0-9\$-\\/:-?@{-~!\"^_`\\[\\] ]+\$/", $text)) {
                                                                                                                                            $db->update("users", ["step" => "sefaresh", "link" => $text], $fid, ["s", "s"]);
                                                                                                                                            $bot->sendMessage($fid, $media->text(["shop5", $user["sefaresh"], $user["count"], $text, $user["price"]]), json_encode(["inline_keyboard" => [[["text" => $key["okorder"], "callback_data" => "sefaresh"], ["text" => $key["cancelorder"], "callback_data" => "closesefaresh"]]]]));
                                                                                                                                        } else {
                                                                                                                                            $bot->sendMessage($fid, $media->text(["shop_justen"]));
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        if ($data == "sefaresh" && $user["step"] == "sefaresh") {
                                                                                                                                            $stmt = $mysqli->prepare("SELECT * FROM `button` WHERE `text`=? and `type`='0'");
                                                                                                                                            $stmt->bind_param("s", $user["ordername1"]);
                                                                                                                                            $stmt->execute();
                                                                                                                                            $result = $stmt->get_result();
                                                                                                                                            $info = $result->fetch_assoc();
                                                                                                                                            $stmt->close();
                                                                                                                                            if ($result->num_rows === 1) {
                                                                                                                                                if ($info["off"] == 1) {
                                                                                                                                                    if ($info["api"] !== "noapi") {
                                                                                                                                                        $infoapi = $db->info($info["api"], "api", "name", "s");
                                                                                                                                                        if ($infoapi !== false) {
                                                                                                                                                            $res = $api->add_order($infoapi["smartpanel"], $infoapi["name"], $infoapi["api_url"], $infoapi["api_key"], $user["serviceid"], $user["link"], $user["count"]);
                                                                                                                                                            if ($res["status"] == "OK") {
                                                                                                                                                                $orderidapi = $res["code"];
                                                                                                                                                                $order = $db->listorder("Pending", $fid, $orderidapi, $user["count"], $user["link"], $user["price"], $user["sefaresh"], $date, $info["api"]);
                                                                                                                                                                if ($order["result"] == "OK") {
                                                                                                                                                                    $orderid = $order["code"];
                                                                                                                                                                    $oldmoj = $user["balance"];
                                                                                                                                                                    $newmoj = $oldmoj - $user["price"];
                                                                                                                                                                    $allord = $user["all_orders"] + 1;
                                                                                                                                                                    $allpay = $user["all_pay"] + $user["price"];
                                                                                                                                                                    $db->update("users", ["step" => "none", "balance" => $newmoj, "all_orders" => $allord, "all_pay" => $allpay], $fid, ["s", "i", "i", "i"]);
                                                                                                                                                                    $allord1 = $setting["all_orders"] + 1;
                                                                                                                                                                    $allpay2 = $setting["all_pay"] + $user["price"];
                                                                                                                                                                    $db->update("setting", ["all_orders" => $allord1, "all_pay" => $allpay2], 1, ["s", "i"]);
                                                                                                                                                                    $bot->sendMessage($channels["channelorder1"], $media->text(["order_receipt1", $user["sefaresh"], $first_name, $fid, $user["count"], $user["link"], $orderid, $orderidapi, $user["price"], $date, $oldmoj, $newmoj]), json_encode(["inline_keyboard" => [[["text" => (int) $orderid, "callback_data" => "fyk"], ["text" => "این سفارش به سایت ارسال شد", "callback_data" => "fyk"]], [["text" => "پیگیری سفارش", "callback_data" => "peygiri-" . $orderidapi]]]]));
                                                                                                                                                                    if ($off["okorder"] == 1) {
                                                                                                                                                                        $bot->sendMessage($channels["channelokorder"], $media->text(["order_receipt0", $user["ordername"], $user["ordername1"], $user["count"], $user["price"], $date, $idbot]), json_encode(["inline_keyboard" => [[["text" => $key["startbot"], "url" => "https://t.me/" . $idbot . "?start"]]]]));
                                                                                                                                                                    }
                                                                                                                                                                    $bot->deletemessage($fid, $message_id);
                                                                                                                                                                    $bot->sendMessage($fid, $media->text(["order_receipt_okapi", $user["ordername"], $user["ordername1"], $user["link"], $user["count"], $orderid, $orderidapi, $date]), $home);
                                                                                                                                                                } else {
                                                                                                                                                                    $db->update("users", ["step" => "none"], $fid, ["s"]);
                                                                                                                                                                    $bot->deletemessage($fid, $message_id);
                                                                                                                                                                    $bot->sendMessage($fid, $media->text(["error_order"]), $home);
                                                                                                                                                                }
                                                                                                                                                            } else {
                                                                                                                                                                $tx = "شرح مشکل :" . $res["error"];
                                                                                                                                                                $bot->sendMessage($admins[0], "1 ->> مشکل در ثبت سفارشی : \nمحصول : " . $user["ordername1"] . "\n" . $tx);
                                                                                                                                                                $db->update("users", ["step" => "none"], $fid, ["s"]);
                                                                                                                                                                $bot->deletemessage($fid, $message_id);
                                                                                                                                                                $bot->sendMessage($fid, $media->text(["error_order"]), $home);
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $db->update("button", ["off" => 0], $info["id"], ["i"]);
                                                                                                                                                            $bot->sendMessage($admins[0], "2->> مشکل در ثبت سفارشی : \nمحصول : " . $user["ordername1"] . "\napi این محصول در دیتابیس یافت نشد و محصول خاموش شد.");
                                                                                                                                                            $db->update("users", ["step" => "none"], $fid, ["s"]);
                                                                                                                                                            $bot->deletemessage($fid, $message_id);
                                                                                                                                                            $bot->sendMessage($fid, $media->text(["error_order"]), $home);
                                                                                                                                                        }
                                                                                                                                                    } else {
    /******** @San_trich********
 * The Best*
 * God*
 * source Bot*
 * python - Php - Laravel*
 * Owner*
  ********* @Ziazl**********
*/                                                                                                                                                    if ($info["api"] == "noapi") {
                                                                                                                                                            $order = $db->listorder("Pending", $fid, 0, $user["count"], $user["link"], $user["price"], $user["sefaresh"], $date, "noapi");
                                                                                                                                                            if ($order["result"] == "OK") {
                                                                                                                                                                $orderid = $order["code"];
                                                                                                                                                                $oldmoj = $user["balance"];
                                                                                                                                                                $newmoj = $oldmoj - $user["price"];
                                                                                                                                                                $allord = $user["all_orders"] + 1;
                                                                                                                                                                $allpay = $user["all_pay"] + $user["price"];
                                                                                                                                                                $db->update("users", ["step" => "none", "balance" => $newmoj, "all_orders" => $allord, "all_pay" => $allpay], $fid, ["s", "i", "i", "i"]);
                                                                                                                                                                $allord1 = $setting["all_orders"] + 1;
                                                                                                                                                                $allpay2 = $setting["all_pay"] + $user["price"];
                                                                                                                                                                $db->update("setting", ["all_orders" => $allord1, "all_pay" => $allpay2], 1, ["s", "i"]);
                                                                                                                                                                $bot->sendMessage($channels["channelorder2"], $media->text(["order_receipt2", $user["sefaresh"], $first_name, $fid, $user["count"], $user["link"], $orderid, $user["price"], $date, $oldmoj, $newmoj]), json_encode(["inline_keyboard" => [[["text" => "در انتظار", "callback_data" => "go-" . $orderid], ["text" => (int) $orderid, "callback_data" => "fyk"]], [["text" => (int) $orderid, "callback_data" => "fyk"], ["text" => "کنسل کردن سفارش", "callback_data" => "can-" . $orderid]]]]));
                                                                                                                                                                if ($off["okorder"] == 1) {
                                                                                                                                                                    $bot->sendMessage($channels["channelokorder"], $media->text(["order_receipt0", $user["ordername"], $user["ordername1"], $user["count"], $user["price"], $date, $idbot]), json_encode(["inline_keyboard" => [[["text" => $key["startbot"], "url" => "https://t.me/" . $idbot . "?start"]]]]));
                                                                                                                                                                }
                                                                                                                                                                $bot->deletemessage($fid, $message_id);
                                                                                                                                                                $bot->sendMessage($fid, $media->text(["order_receipt_oknoapi", $user["ordername"], $user["ordername1"], $user["link"], $user["count"], $orderid, $date]), $home);
                                                                                                                                                            } else {
                                                                                                                                                                $db->update("users", ["step" => "none"], $fid, ["s"]);
                                                                                                                                                                $bot->sendMessage($fid, $media->text(["error_order"]), $home);
                                                                                                                                                            }
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $db->update("users", ["step" => "none"], $fid, ["s"]);
                                                                                                                                                    $bot->sendMessage($fid, $media->text(["not_product"]), $home);
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $db->update("users", ["step" => "none"], $fid, ["s"]);
                                                                                                                                                $bot->sendMessage($fid, $media->text(["not_product"]), $home);
                                                                                                                                            }
                                                                                                                                            
                                                                                                                                        }else{
$result1 =  $db->info($fid, "users");


//-------------------trancfer----------------------//
if($text == '↗️ انتقال'){
$db->update("users", ["step" => 'trancfer'], $fid, ["s"]);        

	mysqli_query($con,"UPDATE `users` SET `step`='trancfer' WHERE `id`='$fid' LIMIT 1");
	$bot->sendMessage($fid,"👈 درصورتی که قصد انتقال موجودی  را دارید  شناسه کاربری مقصد را ارسال کنید.
	",$back);	

	
	
}elseif($result1['step']=="trancfer" and $text != $key['back']){
$db->update("users", ["commend" => $text], $fid, ["i"]);        

    if($text==$fid){
    $bot->sendMessage($fid,"❎ کاربری با این شناسه در ربات پیدا نشد و شما نمی توانید به این شناسه انتقال بدید.️",$home);
$db->update("users", ["step" => "none"], $fid, ["s"]);        
       
    }else{
    
    
      $result2 = $db->info($text, "users");
     $ress=$result2['id'];
     if($result2['id']){
      $bot->sendMessage($fid,"👈 درصورتی که درخواست انتقال موجودی مورد تاییدتان است، مبلغ مورد نظر را وارد نمایید!

💰 موجودی شما :{$user['balance']}",$back);
              
              $db->update("users", ["step" => "trancfer2"], $fid, ["s"]);
     }else{
         
        $bot->sendMessage($fid,"❎ کاربری با این شناسه در ربات پیدا نشد و شما نمی توانید به این شناسه انتقال دهید.️",$home);
$db->update("users", ["step" => "none"], $fid, ["s"]);         
     }
     
     
    }
     
}elseif($result1['step']=="trancfer2" and $text != $key['back']){
         $ress2=$result1['commend'];
         
        if($user['balance']>=$text && $text>=1000 && is_numeric($text)){
         
            $bot->sendMessage($fid,"✅ مبلغ $text تومان موجودی شما با موفقیت به کاربر $ress2 انتقال یافت.",$home);
       $ghab = $result1['balance'];
       $bad = $ghab - $text;
       $ghab1 =  $db->info($ress2, "users");
       $ghab2 = $ghab1['balance'];
       $bad1 = $ghab2 + $text;

      $db->update("users", ["balance" => $bad1], $ress2, ["i"]);        
      $db->update("users", ["balance" => $bad], $fid, ["i"]);        
        $bot->sendMessage($ress2,"✅ مبلغ $text تومان موجودی از کاربر $ress2 به شما انتقال یافت.️");

         $bot->sendMessage($channels["channelsupport"],"
         
         
✅ گزارش #انتقال #موجودی #u_$fid
️کاربر ارسال کننده  :  <a href='tg://user?id=$fid'>$fid</a> 
️کاربر دریافت کننده :  <a href='tg://user?id=$ress2'>$ress2</a> 
 مبلغ  : $text

تاریخ  : $date
         
         
         ");
        
$db->update("users", ["step" => "none"], $fid, ["s"]);        
        
        
         
            
        }else{
            
           $bot->sendMessage($fid,"❗️ موجودی حساب شما کافی نیست , برای انتقال باید حداقل 1000 تومان موجودی داشته باشید .️",$home);
$db->update("users", ["step" => "none"], $fid, ["s"]);        }
                                                                                                                                            }
                                                                                              }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }}}}}}}}}}}
                    } else {
                        $num = $db->notuser($fid, "users");
                        if ($num == 0) {
                            $db->insert($fid, $date, 0);
                        }
                        $bot->sendMessage($fid, $media->text(["lock_channel", $channels["channel"]]), $lockchannel);
                        exit;
                    }
                }
            }
        }
    }
}/******** @San_trich********
 * The Best*
 * God*
 * source Bot*
 * python - Php - Laravel*
 * Owner*
  ********* @Ziazl**********
*/
                                               
/**/
?>