<?php

$dir = dirname(__DIR__);
require_once $dir . "/function/handler.php";
function redirect($url)
{
    if (!headers_sent()){
        header("Location: $url");
    }else{
        echo "<script type='text/javascript'>window.location.href='$url'</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=$url'/></noscript>";
    }
    exit;
}
$ip = getip();
if (strtolower(ip_info($ip)) == "iran") {
    $domin = $domin;
    $domin = str_replace("/payment", NULL, $domin);
    $domin = $domin . "/payment";
    $nextpay = $db->info("nextpay", "payment", "file", "s");
    define("nextpay", $nextpay["code"]);
    if ($nextpay["off"] == 1) {
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
                    $url = "https://nextpay.org/nx/gateway/token";
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => "api_key=" . nextpay . "&currency=IRT&amount=" . $Amount . "&order_id=" . $code . "&customer_phone=" . $number . "&callback_uri=https://" . $domin . "/nextpay.php?back",
                    ));
                    $result = curl_exec($curl);
                    $result = json_decode($result);
                    curl_close($curl);

                    $trans_id = $result->trans_id;

                    if ($result->code !== null){
                        if ($result->code == "-1") {
                            redirect("https://nextpay.org/nx/gateway/payment/$trans_id"); 
                        } else {
						    $bot->sendMessage($admins[0], "مشکل در درگاه نکست پی : " . json_encode($result->code, 448));
                            echo "<h1 style=\"text-align: center\">لینک پرداخت ساخته نشد</h1>";
                        }
                        if ($result->code == "-49") {
                            echo "<h1 style=\"text-align: center\">تراکنش مورد نظر تکراریست</h1>";
                        }
                        if ($result->code == "-25") {
                            echo "<h1 style=\"text-align: center\">تراکنش قابل پرداخت نیست</h1>";
                        }
                    } else {
                        echo "<h1 style=\"text-align: center;margin-top:30px\">درخواست نامعتبر است</h1>";
                    }
                } else {
                    echo "<h1 style=\"text-align: center;margin-top:30px\">درخواست نامعتبر است</h1>";
                }
            } else {
                echo "<h1 style=\"text-align: center;margin-top:30px\">لینک تراکنش فاقد اعتبار است دوباره تلاش کنید</h1>";
            }
        } else {
            if (isset($_REQUEST["back"])) {
                $code = $_REQUEST["order_id"];
                $pays = $db->info($code, "pays", "code", "s");
                $Amount = $pays["amount"];
                $step = $pays["step"];
                $fid = $pays["chatid"];
                $number = $pays["number"];
                $name = $bot->getChatMember($fid);
                $ip = $pays["ip"];
                $order_id = $_REQUEST["order_id"];
                $Status = $_REQUEST["np_status"];
                $user = $db->info($fid, "users");

                $trans_id1 = $_REQUEST["trans_id"];
                $amount1 = $_REQUEST["amount"];

                if (empty($_REQUEST["np_status"]) || empty($_REQUEST["order_id"]) || empty($_REQUEST["amount"]) || empty($_REQUEST["trans_id"])) {
                    $db->update("pays", ["step" => "NOK"], $code, ["s"], "code");
                    echo "<h1 style=\"text-align: center;margin-top:30px\">مقدار های بک خالیست</h1>";
                    $bot->sendMessage($fid, $media->text(["error_payment"]));
                    return false;
                }
				
				if ($result->code == "-49") {
                    echo "<h1 style=\"text-align: center\">تراکنش مورد نظر تکراریست</h1>";
                }
                    
                if ($result->code == "-25") {
                    echo "<h1 style=\"text-align: center\">تراکنش قابل پرداخت نیست</h1>";
                }
				
                if ($Status == "OK") {
                    $url = "https://nextpay.org/nx/gateway/verify";
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => 'api_key=' . nextpay . '&amount=' . $amount1 . '&trans_id=' . $trans_id1,
                    ));
                    $result = curl_exec($curl);
                    $result = json_decode($result);
                    curl_close($curl);

                    if ($result->code == 0 and $step == "pay") {
                        echo "<h1 style=\"text-align: center\">تراکنش موفقیت آمیز حساب شما در ربات شارژ شد✅</h1>";
                        $pay = $result->Shaparak_Ref_Id;
                        $card = $result->card_holder;
                        $mojodiold = $user["balance"];
                        $mojodinew = $mojodiold + $amount1;
                        $db->update("users", ["balance" => $mojodinew], $fid, ["i"]);
                        $db->update("pays", ["step" => "OK", "paycode" => $pay], $code, ["s", "s"], "code");
                        if ($user["refid"] !== 0 && $off["free"] == 1 && $off["Porsant"] == 1) {
                            $gif = $db->info($user["refid"], "users");
                            $gifi = $amount1 * $setting["Porsant"] / 100;
                            $gifmoj = $gif["GiftMoj"] + $gifi;
                            $gif1 = $gif["Gift2"] + $gifi;
                            $db->update("users", ["GiftMoj" => $gifmoj, "Gift2" => $gif1], $user["refid"], ["i", "i"]);
                            $bot->sendMessage($user["refid"], $media->text(["refralGift_payment", $fid, $name, $amount1, $gifi]));
                        }
                        $bot->sendMessage($fid, $media->text(["ok_payment", $pay, $amount1, $mojodiold, $mojodinew, $zarin["name"], $channels["channel"]]));
                        $bot->sendMessage($channels["channelpay"], "#تراکنش جدید \n کاربر : <a href = 'tg://user?id=" . $fid . "'>" . $name . "</a> \n شناسه عددی : <code>" . $fid . "</code> \n شماره پیگیری : <code>" . $pay . "</code> \n مبلغ پرداختی : " . $amount1 . " \n موجودی کلی : " . $mojodinew . " \n شماره کاربر : <code>0" . $number . "</code>\nIP : " . $ip . "\ncard : " . $card);
                    } else {
                        $step = $result->code;
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
                echo "<h1 style=\"text-align: center\">درخواست نامعتبر است</h1>";
            }
        }
    } else {
        echo "<h1 style=\"text-align: center\">این درگاه خاموش میباشد</h1>";
    }
} else {
    echo "<h1 style=\"text-align: center;margin-top:30px\">لطفا فیلرشکن خود را خاموش کنید و سپس اقدام به پرداخت نمایید</h1>";
}
