<?php
$dir = dirname(__DIR__);
require_once $dir . "/function/handler.php";
$ip = getip();
if (strtolower(ip_info($ip)) == "iran") {
    $domin = $domin;
    $domin = str_replace("/payment", NULL, $domin);
    $domin = $domin . "/payment";
    $idpay = $db->info("idpay", "payment", "file", "s");
    define("apidpay", $idpay["code"]);
    if ($idpay["off"] == 1) {
        if (isset($_GET["code"]) && isset($_GET["get"])) {
            $code = $_GET["code"];
            $pays = $db->info($code, "pays", "code", "s");
            $Amount = $pays["amount"];
            $fid = $pays["chatid"];
            $number = $pays["number"];
            $name = $bot->getChatMember($fid);
            if ($pays["step"] == "pay") {
                $db->update("pays", ["ip" => $ip], $code, ["s"], "code");
                if (isset($Amount) && isset($number)) {
                    $params = ["order_id" => $code, "amount" => $Amount . "0", "name" => $name, "phone" => $number, "desc" => "افزاش موجودی کاربر : " . $name . " | " . $fid, "callback" => "https://" . $domin . "/idpay.php?back"];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://api.idpay.ir/v1/payment");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "X-API-KEY: " . apidpay]);
                    $result = curl_exec($ch);
                    $err = curl_error($ch);
                    $result = json_decode($result);
                    curl_close($ch);
                    if ($err) {
                        echo "<h1 style=\"text-align: center\">لینک پرداخت ساخته نشد</h1>";
                        $bot->sendMessage($admins[0], "cURL Error idpay # : " . json_encode($err, 448));
                    } else {
                        if (empty($result) || empty($result->link)) {
                            $bot->sendMessage($admins[0], "مشکل در درگاه آیدی پی : " . json_encode($result, 448));
                            echo "<h1 style=\"text-align: center;margin-top:30px\">لینک پرداخت ساخته نشد</h1>";
                            return false;
                        }
                        header("Location:" . $result->link);
                    }
                } else {
                    echo "<h1 style=\"text-align: center;margin-top:30px\">درخواست نامعتبر است</h1>";
                }
            } else {
                echo "<h1 style=\"text-align: center;margin-top:30px\">لینک تراکنش فاقد اعتبار است دوباره تلاش کنید</h1>";
            }
        } else {
            if (isset($_GET["back"])) {
                $code = $_REQUEST["order_id"];
                $pays = $db->info($code, "pays", "code", "s");
                $Amount = $pays["amount"];
                $fid = $pays["chatid"];
                $number = $pays["number"];
                $ip = $pays["ip"];
                $name = $bot->getChatMember($fid);
                $user = $db->info($fid, "users");
                if (empty($_REQUEST["status"]) || empty($_REQUEST["id"]) || empty($_REQUEST["track_id"]) || empty($_REQUEST["order_id"])) {
                    $db->update("pays", ["step" => "NOK"], $code, ["s"], "code");
                    echo "<h1 style=\"text-align: center;margin-top:30px\">خطا رخ داده است</h1>";
                    $bot->sendMessage($fid, $media->text(["error_payment"]));
                    return false;
                }
                $params = ["id" => $_REQUEST["id"], "order_id" => $_REQUEST["order_id"]];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://api.idpay.ir/v1/payment/inquiry");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "X-API-KEY: " . apidpay]);
                $result = curl_exec($ch);
                $err = curl_error($ch);
                $result = json_decode($result);
                curl_close($ch);
                if ($err) {
                    echo "<h1 style=\"text-align: center\">لینک پرداخت ساخته نشد</h1>";
                    $bot->sendMessage($admins[0], "cURL Error idpay # : " . json_encode($err, 448));
                } else {
                    $Status = $result->status;
                    $cardno = $result->card_no;
                    if (empty($result) || empty($result->status)) {
                        $db->update("pays", ["step" => "NOK"], $code, ["s"], "code");
                        echo "<h1 style=\"text-align: center;margin-top:30px\">خطا رخ داده است</h1>";
                        $bot->sendMessage($fid, $media->text(["error_payment"]));
                        return false;
                    }
                    if ($pays["step"] == "pay") {
                        if ($Status != NULL) {
                            if ($Status == 1) {
                                $db->update("pays", ["step" => $Status], $code, ["s"], "code");
                                echo "<h1 style=\"text-align: center;margin-top:30px\">تراکنش توسط کاربر لغو شد</h1>";
                                $bot->sendMessage($fid, $media->text(["cancel_payment"]));
                                exit;
                            }
                            if ($Status == 2) {
                                $db->update("pays", ["step" => $Status], $code, ["s"], "code");
                                echo "<h1 style=\"text-align: center;margin-top:30px\">تراکنش انجام نشد</h1>";
                                $bot->sendMessage($fid, $media->text(["notpay_payment"]));
                                exit;
                            }
                            if ($Status == 3) {
                                $db->update("pays", ["step" => $Status], $code, ["s"], "code");
                                echo "<h1 style=\"text-align: center;margin-top:30px\">خطا رخ داده است</h1>";
                                $bot->sendMessage($fid, $media->text(["error_payment"]));
                                exit;
                            }
                            if ($Status == 100) {
                                echo "<h1 style=\"text-align: center;margin-top:30px\">تراکنش موفقیت آمیز بود.</h1>";
                                $pay = $_REQUEST["track_id"];
                                $mojodiold = $user["balance"];
                                $mojodinew = $mojodiold + $Amount;
                                $db->update("users", ["balance" => $mojodinew], $fid, ["i"]);
                                $db->update("pays", ["step" => "OK", "paycode" => $pay], $code, ["s", "s"], "code");
                                if ($user["refid"] !== 0 && $off["free"] == 1 && $off["Porsant"] == 1) {
                                    $gif = $db->info($user["refid"], "users");
                                    $gifi = $Amount * $setting["Porsant"] / 100;
                                    $gifmoj = $gif["GiftMoj"] + $gifi;
                                    $gif1 = $gif["Gift2"] + $gifi;
                                    $db->update("users", ["GiftMoj" => $gifmoj, "Gift2" => $gif1], $user["refid"], ["i", "i"]);
                                    $bot->sendMessage($user["refid"], $media->text(["refralGift_payment", $fid, $name, $Amount, $gifi]));
                                }
                                $bot->sendMessage($fid, $media->text(["ok_payment", $pay, $Amount, $mojodiold, $mojodinew, $idpay["name"], $channels["channel"]]));
                                $bot->sendMessage($channels["channelpay"], "#تراکنش جدید \n کاربر : <a href = 'tg://user?id=" . $fid . "'>" . $name . "</a> \n شناسه عددی : <code>" . $fid . "</code> \n شماره پیگیری : <code>" . $pay . "</code> \n مبلغ پرداختی : " . $Amount . " \n موجودی کلی : " . $mojodinew . " \n شماره کاربر : <code>0" . $number . "</code> \n Card : " . $cardno . "\nIP : " . $ip);
                                exit;
                            }
                        } else {
                            $db->update("pays", ["step" => "NOK"], $code, ["s"], "code");
                            echo "<h1 style=\"text-align: center;margin-top:30px\">خطا رخ داده است</h1>";
                            $bot->sendMessage($fid, $media->text(["error_payment"]));
                        }
                    } else {
                        echo "<h1 style=\"text-align: center;margin-top:10px\">لینک تراکنش فاقد اعتبار است دوباره تلاش کنید</h1>";
                    }
                }
            }
        }
    } else {
        echo "<h1 style=\"text-align: center\">این درگاه خاموش میباشد</h1>";
    }
} else {
    echo "<h1 style=\"text-align: center;margin-top:30px\">لطفا فیلرشکن خود را خاموش کنید و سپس اقدام به پرداخت نمایید</h1>";
}

?>