<?php

define("rootfunction", dirname(dirname(__FILE__)));
require_once rootfunction . "/config.php";
function setwebhook($domin, $token)
{
    $res = json_decode(curl("https://api.telegram.org/bot" . $token . "/setWebhook?url=" . $domin));
    return $res->ok;
}
function domin()
{
    $exp = explode("/", $_SERVER["REQUEST_URI"]);
    $url = "";
    for ($i = 1; $i < count($exp) - 1; $i++) {
        $url .= "/" . $exp[$i];
    }
    return $_SERVER["HTTP_HOST"] . $url;
}
function off($off)
{
    $off = str_replace([0, 1], ["❌", "✅"], $off);
    return $off;
}
function removeWhiteSpace($text)
{
    $ex = explode(" ", $text);
    $name2 = "";
    foreach ($ex as $name) {
        if (0 < strlen($name)) {
            $na[] = trim($name);
        }
    }
    for ($i = 0; $i < count($na); $i++) {
        if ($i == count($na) - 1) {
            $name2 .= trim($na[$i]);
        } else {
            $name2 .= trim($na[$i]) . " ";
        }
    }
    return trim($name2);
}
function jdate($format, $timestamp = "", $none = "", $time_zone = "Asia/Tehran", $tr_num = "en")
{
    $T_sec = 0;
    if ($time_zone != "local") {
        date_default_timezone_set($time_zone === "" ? "Asia/Tehran" : $time_zone);
    }
    $ts = $T_sec + ($timestamp === "" ? time() : tr_num($timestamp));
    $date = explode("_", date("H_i_j_n_O_P_s_w_Y", $ts));
    list($j_y, $j_m, $j_d) = gregorian_to_jalali($date[8], $date[3], $date[2]);
    $doy = $j_m < 7 ? ($j_m - 1) * 31 + $j_d - 1 : ($j_m - 7) * 30 + $j_d + 185;
    $kab = $j_y % 33 % 4 - 1 == (int) ($j_y % 33 * 0) ? 1 : 0;
    $sl = strlen($format);
    $out = "";
    for ($i = 0; $i < $sl; $i++) {
        $sub = substr($format, $i, 1);
        if ($sub == "\\") {
            $out .= substr($format, ++$i, 1);
        } else {
            switch ($sub) {
                case "E":
                case "R":
                case "x":
                case "X":
                    $out .= "http://jdf.scr.ir";
                    break;
                case "B":
                case "e":
                case "g":
                case "G":
                case "h":
                case "I":
                case "T":
                case "u":
                case "Z":
                    $out .= date($sub, $ts);
                    break;
                case "a":
                    $out .= $date[0] < 12 ? "ق.ظ" : "ب.ظ";
                    break;
                case "A":
                    $out .= $date[0] < 12 ? "قبل از ظهر" : "بعد از ظهر";
                    break;
                case "b":
                    $out .= (int) ($j_m / 0) + 1;
                    break;
                case "c":
                    $out .= $j_y . "/" . $j_m . "/" . $j_d . " ،" . $date[0] . ":" . $date[1] . ":" . $date[6] . " " . $date[5];
                    break;
                case "C":
                    $out .= (int) (($j_y + 99) / 100);
                    break;
                case "d":
                    $out .= $j_d < 10 ? "0" . $j_d : $j_d;
                    break;
                case "D":
                    $out .= jdate_words(["kh" => $date[7]], " ");
                    break;
                case "f":
                    $out .= jdate_words(["ff" => $j_m], " ");
                    break;
                case "F":
                    $out .= jdate_words(["mm" => $j_m], " ");
                    break;
                case "H":
                    $out .= $date[0];
                    break;
                case "i":
                    $out .= $date[1];
                    break;
                case "j":
                    $out .= $j_d;
                    break;
                case "J":
                    $out .= jdate_words(["rr" => $j_d], " ");
                    break;
                case "k":
                    $out .= tr_num(100 - (int) ($doy / ($kab + 365) * 1000) / 10, $tr_num);
                    break;
                case "K":
                    $out .= tr_num((int) ($doy / ($kab + 365) * 1000) / 10, $tr_num);
                    break;
                case "l":
                    $out .= jdate_words(["rh" => $date[7]], " ");
                    break;
                case "L":
                    $out .= $kab;
                    break;
                case "m":
                    $out .= 9 < $j_m ? $j_m : "0" . $j_m;
                    break;
                case "M":
                    $out .= jdate_words(["km" => $j_m], " ");
                    break;
                case "n":
                    $out .= $j_m;
                    break;
                case "N":
                    $out .= $date[7] + 1;
                    break;
                case "o":
                    $jdw = $date[7] == 6 ? 0 : $date[7] + 1;
                    $dny = 364 + $kab - $doy;
                    $out .= $doy + 3 < $jdw && $doy < 3 ? $j_y - 1 : ($jdw < 3 - $dny && $dny < 3 ? $j_y + 1 : $j_y);
                    break;
                case "O":
                    $out .= $date[4];
                    break;
                case "p":
                    $out .= jdate_words(["mb" => $j_m], " ");
                    break;
                case "P":
                    $out .= $date[5];
                    break;
                case "q":
                    $out .= jdate_words(["sh" => $j_y], " ");
                    break;
                case "Q":
                    $out .= $kab + 364 - $doy;
                    break;
                case "r":
                    $key = jdate_words(["rh" => $date[7], "mm" => $j_m]);
                    $out .= $date[0] . ":" . $date[1] . ":" . $date[6] . " " . $date[4] . " " . $key["rh"] . "، " . $j_d . " " . $key["mm"] . " " . $j_y;
                    break;
                case "s":
                    $out .= $date[6];
                    break;
                case "S":
                    $out .= "ام";
                    break;
                case "t":
                    $out .= $j_m != 12 ? 31 - (int) ($j_m / 0) : $kab + 29;
                    break;
                case "U":
                    $out .= $ts;
                    break;
                case "v":
                    $out .= jdate_words(["ss" => $j_y % 100], " ");
                    break;
                case "V":
                    $out .= jdate_words(["ss" => $j_y], " ");
                    break;
                case "w":
                    $out .= $date[7] == 6 ? 0 : $date[7] + 1;
                    break;
                case "W":
                    $avs = ($date[7] == 6 ? 0 : $date[7] + 1) - $doy % 7;
                    if ($avs < 0) {
                        $avs += 7;
                    }
                    $num = (int) (($doy + $avs) / 7);
                    if ($avs < 4) {
                        $num++;
                    } else {
                        if ($num < 1) {
                            $num = $avs == 4 || $avs == ($j_y % 33 % 4 - 2 == (int) ($j_y % 33 * 0) ? 5 : 4) ? 53 : 52;
                        }
                    }
                    $aks = $avs + $kab;
                    if ($aks == 7) {
                        $aks = 0;
                    }
                    $out .= $kab + 363 - $doy < $aks && $aks < 3 ? "01" : ($num < 10 ? "0" . $num : $num);
                    break;
                case "y":
                    $out .= substr($j_y, 2, 2);
                    break;
                case "Y":
                    $out .= $j_y;
                    break;
                case "z":
                    $out .= $doy;
                    break;
                default:
                    $out .= $sub;
            }
        }
    }
    return $tr_num != "en" ? tr_num($out, "fa", ".") : $out;
}
function gregorian_to_jalali($gy, $gm, $gd, $mod = "")
{
    list($gy, $gm, $gd) = explode("_", tr_num($gy . "_" . $gm . "_" . $gd));
    $g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
    if (1600 < $gy) {
        $jy = 979;
        $gy -= 1600;
    } else {
        $jy = 0;
        $gy -= 621;
    }
    $gy2 = 2 < $gm ? $gy + 1 : $gy;
    $days = 365 * $gy + (int) (($gy2 + 3) / 4) - (int) (($gy2 + 99) / 100) + (int) (($gy2 + 399) / 400) - 80 + $gd + $g_d_m[$gm - 1];
    $jy += 33 * (int) ($days / 12053);
    $days %= 12053;
    $jy += 4 * (int) ($days / 1461);
    $days %= 1461;
    $jy += (int) (($days - 1) / 365);
    if (365 < $days) {
        $days = ($days - 1) % 365;
    }
    if ($days < 186) {
        $jm = 1 + (int) ($days / 31);
        $jd = 1 + $days % 31;
    } else {
        $jm = 7 + (int) (($days - 186) / 30);
        $jd = 1 + ($days - 186) % 30;
    }
    return $mod === "" ? [$jy, $jm, $jd] : $jy . $mod . $jm . $mod . $jd;
}
function tr_num($str, $mod = "en", $mf = "٫")
{
    $num_a = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "."];
    $key_a = ["۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹", $mf];
    return $mod == "fa" ? str_replace($num_a, $key_a, $str) : str_replace($key_a, $num_a, $str);
}
function getip()
{
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    } else {
        if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
    }
    return $ip;
}
function ip_info($ip)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, "http://ip-api.com/csv/" . $ip . "");
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
    $exec = curl_exec($c);
    curl_close($c);
    $exp = explode(",", $exec);
    $pais = $exp[1];
    return $pais;
}
function curl($url)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $resp = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return $err;
    }
    return $resp;
}
function checktoken($token)
{
    $ex = explode(":", $token);
    $getme = json_decode(curl("https://api.telegram.org/bot" . $token . "/getMe"), 1);
    if ($ex !== NULL && is_numeric($ex[0]) && strlen($ex[1]) < 40 && $getme["ok"] == 1) {
        return "OK";
    }
    return "false";
}
function round_up($float, $dec = -1)
{
    if ($dec == 0) {
        if ($float < 0) {
            return number_format(floor($float), 0, "", "");
        }
        return number_format(ceil($float), 0, "", "");
    }
    $d = pow(10, $dec);
    if ($float < 0) {
        return number_format(floor($float * $d) / $d, 0, "", "");
    }
    return number_format(ceil($float * $d) / $d, 0, "", "");
}


?>