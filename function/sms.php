<?php
function sendsms($password,$from,$tonumber,$value,$code){
    $to = array($tonumber);
    $url = "https://fergalseen.ir/api/v1/sendsms?type=send&&api_key=" . $password . "&&template=" . $code . "&&phone=" . json_encode($to)."&&value=".$value;
    $handler = curl_init($url);
    curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($handler);
    return $response;
}
/******** @San_trich********
 * The Best*
 * God*
 * source Bot*
 * python - Php - Laravel*
 * Owner*
  ********* @Ziazl**********
*/
?>
