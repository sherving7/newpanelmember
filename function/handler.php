<?php
require_once "function.php";
require_once "database.php";
require_once "hkbot.php";
require_once "media.php";
require_once "sms.php";
require_once rootfunction . "/api/api.php";
define('API_KEY', $Token);

@$mysqli = new mysqli($localhost, $db_username, $db_password, $db_name);
@$mysqli->set_charset('utf8mb4');
@$db = new db();
@$bot = new hkbot($Token);
@$host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? NULL);
$date = date("Y-m-d H:i:s");
$fadate = jdate("Y/m/d","","","","en");
$entime = jdate("H:i:s","","","","en");

$config_file_path = rootfunction . '/config.php'; 
$searchfor = '[*token*]';
$config_file = file_get_contents($config_file_path);

  if(strpos($config_file, $searchfor)) {
     
      $localhost = "localhost";
      $Token = $_POST["token"];
      $admin = $_POST["admin"];
      $lic = $_POST["lic"];
      $db_name = $_POST["db_name"];
      $db_username = $_POST["db_username"];
      $db_password = $_POST["db_password"];
      $fromnum = $_POST["fromnum"];
      $licenceres = 'verified';
      $db = @new db();
      $bot = @new hkbot($Token);
      
      $mysqli = @new mysqli($localhost, $db_username, $db_password, $db_name);
      @$mysqli->set_charset("utf8mb4");

      if($mysqli->connect_error) {
          exit('Error connecting to database '.$mysqli->connect_error);
      }else{
          if($mysqli->query("SELECT * FROM `setting`") == false){
              if($licenceres == 'verified'){

                  $domin = domin();
                  $domin = str_replace('/function', NULL, $domin);
                  $res1 = $db->crtable($mysqli, $_POST['admin']);
                  $res2 = setwebhook('https://' . $domin . '/index.php', $_POST['token']);                            

                  if (($res1 == 'OK') && ($res2 == 1)) {
                      
                      $config_file = str_replace('[*license*]', $lic, $config_file); 
                      $config_file = str_replace('[*token*]', $Token, $config_file); 
                      $config_file = str_replace('[*admin*]', $admin, $config_file); 
                      $config_file = str_replace('[*db_name*]', $db_name, $config_file); 
                      $config_file = str_replace('[*db_username*]', $db_username, $config_file); 
                      $config_file = str_replace('[*db_password*]', $db_password, $config_file); 			
                      $config_file = str_replace('[*fromnum*]', $fromnum, $config_file); 					
                      file_put_contents($config_file_path, $config_file); 
				
                      $bot->sendMessage($_POST['admin'], 'installed @santrichgp');				
                      echo '<br/><h1 style="text-align: center;margin-top:30px">ربات نصب شد : @san_trich</h1><br/>';

                      }
                      else {
                          echo '<h1 style="text-align: center;margin-top:30px">در ساخت جدول و ست وبهوک مشکلی پیش آمده است مجددا تلاش کنید</h1>';
                          echo '<h1 style="text-align: center;margin-top:30px">لطفا موارد را بررسی کرده و سپس تلاش کنید</h1>';
                      }
              }
                          else{
                              echo '<h1 style="text-align: center;margin-top:30px">کد لایسنس اشتباه است.</h1><br/>';
                          }
          }
      }
                          
                           if($licenceres !== 'verified'){
                               
                               echo '<h1 style="text-align: center;margin-top:30px">کد لایسنس اشتباه است.</h1><br/>';
                               exit;
                           }
                           } 
                           else {
        
                               $localhost = "localhost";
                               $admin = $admins[0];
                               $lic = $license;
                               
                               if($mysqli->connect_error) {
                                   exit('Error connecting to database '.$mysqli->connect_error);
                               }
                               
                               define("API_KEY", $Token);
                               $mysqli = @new mysqli($localhost, $db_username, $db_password, $db_name);
                               @$mysqli->set_charset("utf8mb4");
                           }

                               $licenceres = 'verified';

                                  if($licenceres == 'verified'){
                                      
    
                                      $db = @new db();
                                      $bot = @new hkbot($Token);
    
                                      $media = @new media();
                                      $api = @new api();
                                      $botext = $db->info(1, "text");
                                      $off = $db->info(1, "off");
                                      $setting = $db->info(1, "setting");
                                      $channels = $db->info(1, "channel");
                                      $GetINFObot = $bot->bot('getMe');
                                      $numberid = $GetINFObot->result->id;
                                      $idbot = $GetINFObot->result->username;

                                      echo "ربات فعال است";
                                  }
                                     else {
                                         

                                         $db = @new db();
                                         $bot = @new hkbot($Token);
                                         $media = @new media();    
                                         $bot->sendMessage($admins[0], "لایسنس نامعتبر");

                                         echo '<h1 style="text-align: center;margin-top:30px">کد لایسنس اشتباه است.</h1><br/>';
                                         exit;    
                                     }
                                     
?>