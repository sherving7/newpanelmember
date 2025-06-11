<?php
$dir = dirname(__DIR__);
require_once $dir . "/function/handler.php";
$ip = getip();
if (strtolower(ip_info($ip)) == "iran") {
    $domin = $domin;
    $domin = str_replace("/payment", NULL, $domin);
    $domin = $domin . "/payment";
    $zarin = $db->info("zarinpal", "payment", "file", "s");
    if ($zarin["off"] == 1) {
        if (isset($_GET["code"]) && isset($_GET["get"])) {
            $code = $_GET["code"];
            $pays = $db->info($code, "pays", "code", "s");
            $Amount = $pays["amount"];
            $fid = $pays["chatid"];
            $number = $pays["number"];
            $name = $bot->getChatMember($fid);
            $step = $pays["step"];
            if ($step == "pay") {
                $db->update("pays", ["ip" => $ip], $code, ["s"], "code");
                if (isset($Amount) && isset($number)) {
                    $url = "https://api.zarinpal.com/pg/v4/payment/request.json";
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ["accept: application/json", "content-type: application/json"]);
                    $data = ["merchant_id" => $zarin["code"], "amount" => $Amount . "0", "callback_url" => "https://" . $domin . "/zarinpal.php?code=" . $code . "&back", "description" => "افزاش موجودی کاربر : " . $name . " | " . $fid, "metadata" => ["mobile" => "0" . $number]];
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    $result = curl_exec($ch);
                    $err = curl_error($ch);
                    $result = json_decode($result);
                    curl_close($ch);
                    if ($err) {
                        echo "<h1 style=\"text-align: center\">لینک پرداخت ساخته نشد</h1>";
                        $bot->sendMessage($admins[0], "cURL Error ZarinPal # : " . json_encode($err, 448));
                    } else {
                        if (empty($result->errors)) {
                            if ($result->data->code == 100) {
                                header("Location: https://www.zarinpal.com/pg/StartPay/" . $result->data->authority);
                            }
                        } else {
                            $bot->sendMessage($admins[0], "مشکل در درگاه زرین پال : " . json_encode($result->errors->code, 448) . "\n" . json_encode($result->errors->message, 448));
                            echo "<h1 style=\"text-align: center\">لینک پرداخت ساخته نشد</h1>";
                        }
                    }
                } else {
                    echo "<h1 style=\"text-align: center;margin-top:30px\">درخواست نامعتبر است</h1>";
                }
            } else {
                echo "<h1 style=\"text-align: center;margin-top:30px\">لینک تراکنش فاقد اعتبار است دوباره تلاش کنید</h1>";
            }
        } else {
            if (isset($_GET["code"]) && isset($_GET["Authority"]) && isset($_GET["Status"]) && isset($_GET["back"])) {
                $code = $_GET["code"];
                $pays = $db->info($code, "pays", "code", "s");
                $Amount = $pays["amount"];
                $step = $pays["step"];
                $fid = $pays["chatid"];
                $number = $pays["number"];
                $name = $bot->getChatMember($fid);
                $ip = $pays["ip"];
                $Authority = $_GET["Authority"];
                $Status = $_GET["Status"];
                $user = $db->info($fid, "users");
                if ($step == "pay") {
                    if ($Status == "OK") {
                        $url = "https://api.zarinpal.com/pg/v4/payment/verify.json";
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ["accept: application/json", "content-type: application/json"]);
                        $data = ["merchant_id" => $zarin["code"], "amount" => $Amount . "0", "authority" => $Authority];
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                        $result = curl_exec($ch);
                        $err = curl_error($ch);
                        $result = json_decode($result);
                        curl_close($ch);
                        if ($result->data->code == 100 || $result->data->code == 101 && $step == "pay") {
                            echo "<h1 style=\"text-align: center\">تراکنش موفقیت آمیز بود.</h1>";
                            $pay = $result->data->ref_id;
                            $card = $result->data->card_pan;
                            $mojodiold = $user["balance"];
                            $mojodinew = $mojodiold + $Amount;
                            $db->update("users", ["balance" => $mojodinew], $fid, ["i"]);
                            $db->update("pays", ["step" => "OK", "paycode" => $pay], $code, ["s", "s"], "code");
                            if ($user["refid"] !== 0 && $off["free"] == 1 && $off["Porsant"] == 1) {
                                $gif = info($user["refid"], "users");
                                $gifi = $Amount * $setting["Porsant"] / 100;
                                $gifmoj = $gif["GiftMoj"] + $gifi;
                                $gif1 = $gif["Gift2"] + $gifi;
                                $db->update("users", ["GiftMoj" => $gifmoj, "Gift2" => $gif1], $user["refid"], ["i", "i"]);
                                $bot->sendMessage($user["refid"], $media->text(["refralGift_payment", $fid, $name, $Amount, $gifi]));
                            }
                            $bot->sendMessage($fid, $media->text(["ok_payment", $pay, $Amount, $mojodiold, $mojodinew, $zarin["name"], $channels["channel"]]));
                            $bot->sendMessage($channels["channelpay"], "#تراکنش جدید \n کاربر : <a href = 'tg://user?id=" . $fid . "'>" . $name . "</a> \n شناسه عددی : <code>" . $fid . "</code> \n شماره پیگیری : <code>" . $pay . "</code> \n مبلغ پرداختی : " . $Amount . " \n موجودی کلی : " . $mojodinew . " \n شماره کاربر : <code>0" . $number . "</code>\nIP : " . $ip . "\ncard : " . $card);
                        } else {
                            $step = $result->data->code;
                            $db->update("pays", ["step" => $step], $code, ["s"], "code");
                            echo "<h1 style=\"text-align: center\">تراکنش انجام نشد</h1>";
                            $bot->sendMessage($fid, $media->text(["notpay_payment"]));
                        }
                    } else {
                        $db->update("pays", ["step" => "NOK"], $code, ["s"], "code");
                        echo "<h1 style=\"text-align: center\">تراکنش توسط کاربر لغو شد</h1>";
                        $bot->sendMessage($fid, $media->text(["cancel_payment"]));
                    }
                } else {
                    echo "<h1 style=\"text-align: center\">لینک تراکنش فاقد اعتبار است دوباره تلاش کنید</h1>";
                }
            } else {
                echo "<h1 style=\"text-align: center\">درخواست نامعتبر است</h1>";
            }
        }
    } else {
        echo "<h1 style=\"text-align: center\">این درگاه خاموش میباشد</h1>";
    }
} else {
    echo "<h1 style=\"text-align: center;margin-top:30px\">لطفا فیلرشکن خود را خاموش کنید و سپس اقدام به پرداخت نمایید</h1>";
}

?>
