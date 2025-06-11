<?php
class db extends mysqli
{
    public function info($fid, $table, $id = "id", $noe = "i")
    {
        global $mysqli;
        $stmt = $mysqli->prepare("SELECT * FROM " . $table . " WHERE `" . $id . "`=? LIMIT 1");
        $stmt->bind_param($noe, $fid);
        $stmt->execute();
        $arr = $stmt->get_result()->fetch_assoc();
        if (!$arr) {
            return false;
        }
        return $arr;
    }
    public function notuser($fid, $table, $id = "id", $noe = "i")
    {
        global $mysqli;
        $stmt = $mysqli->prepare("SELECT `" . $id . "` FROM `" . $table . "` WHERE `" . $id . "`=? LIMIT 1 ");
        $stmt->bind_param($noe, $fid);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return 0;
        }
        return 1;
    }
    public function insert($fid, $date, $refid)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("INSERT INTO `users`(`id`, `joindate` ,`refid`) VALUES (?,?,?)");
        $stmt->bind_param("isi", $fid, $date, $refid);
        $stmt->execute();
        $stmt->close();
    }
    public function insertproduct($price, $serviceid, $min, $max, $api, $text, $type, $category, $info = NULL)
    {
        global $mysqli;
        $off = 1;
        $stmt = $mysqli->prepare("INSERT INTO `button`(`price`, `serviceid`, `api`, `min`, `max`, `info`, `type`, `text`, `category`, `off`) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("issiisisii", $price, $serviceid, $api, $min, $max, $info, $type, $text, $category, $off);
        $stmt->execute();
        if (is_numeric($stmt->insert_id) && $stmt->affected_rows === 1) {
            return ["result" => "OK", "insert_id" => $stmt->insert_id];
        }
        return ["result" => "false"];
    }
    public function update($table, $data = [], $where, $noe = [], $id = "id")
    {
        global $mysqli;
        $i = 0;
        foreach ($data as $k => $v) {
            $k = $k;
            $v = $v;
            $stmt = $mysqli->prepare("UPDATE `" . $table . "` SET `" . $k . "`=? WHERE `" . $id . "` =? LIMIT 1");
            $stmt->bind_param($noe[$i] . "s", $v, $where);
            $stmt->execute();
            $stmt->close();
            $i++;
        }
    }
    public function addpayment($name, $file, $code, $off = 1)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("INSERT INTO `payment`(`name`, `code`, `file`, `off`) VALUES (?,?,?,?)");
        $stmt->bind_param("sssi", $name, $code, $file, $off);
        $stmt->execute();
        if (is_numeric($stmt->insert_id) && $stmt->affected_rows === 1) {
            return ["result" => "OK"];
        }
        return ["result" => "false"];
    }
    public function listorder($step, $fid, $codeapi, $count, $link, $factor, $sefaresh, $date, $api)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("INSERT INTO `list`(`status`, `chatid`, `codeapi`, `count`, `link`, `factor`, `sefaresh`, `date`, `api`) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sisisisss", $step, $fid, $codeapi, $count, $link, $factor, $sefaresh, $date, $api);
        $stmt->execute();
        if (is_numeric($stmt->insert_id) && $stmt->affected_rows === 1) {
            return ["result" => "OK", "code" => $stmt->insert_id];
        }
        return ["result" => "false"];
    }
    public function listpays($fid, $step, $paycode, $amount, $number, $date)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("INSERT INTO `pays`(`chatid`,`step`,`paycode`,`amount`,`number`,`date`) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("isiiis", $fid, $step, $paycode, $amount, $number, $date);
        $stmt->execute();
        if (is_numeric($stmt->insert_id) && $stmt->affected_rows === 1) {
            return ["result" => "OK", "code" => $stmt->insert_id];
        }
        return ["result" => "false"];
    }
    public function listpaysout($fid, $step, $type, $amount, $date)
    {
        global $mysqli;
        $stmt = $mysqli->prepare("INSERT INTO `paysout`(`chatid`, `step`, `type`, `amount`, `date`) VALUES (?,?,?,?,?)");
        $stmt->bind_param("issis", $fid, $step, $type, $amount, $date);
        $stmt->execute();
        if (is_numeric($stmt->insert_id) && $stmt->affected_rows === 1) {
            return ["result" => "OK", "code" => $stmt->insert_id];
        }
        return ["result" => "false"];
    }
    public function crtable($mysqli, $admin)
    {
        $res = $mysqli->multi_query("CREATE TABLE `api` ( \n`id` BIGINT(70) NOT NULL AUTO_INCREMENT, \n`smartpanel` INT(1) DEFAULT '1'  ,\n`name` VARCHAR(300) NOT NULL ,\n`api_url` VARCHAR(1000) NOT NULL ,\n`api_key` VARCHAR(1000) NOT NULL ,\nPRIMARY KEY (`id`)) CHARSET=utf8mb4;\n\nCREATE TABLE `banlist` ( \n`id` BIGINT(70) NOT NULL , \nPRIMARY KEY (`id`));\n\nCREATE TABLE `button` ( \n`id` BIGINT(70) NOT NULL AUTO_INCREMENT,\n`price` BIGINT(70) DEFAULT '0'  ,\n`serviceid` BIGINT(70) DEFAULT '0'  ,\n`api` VARCHAR(500) NOT NULL DEFAULT 'noapi',\n`min` BIGINT(70) DEFAULT '0',\n`max` BIGINT(70) DEFAULT '0',\n`info` text DEFAULT NULL,\n`type` INT(1) NOT NULL ,\n`text` VARCHAR(500) NOT NULL ,\n`category` BIGINT(70) NOT NULL ,\n`off` INT(1) NOT NULL DEFAULT '1',\nPRIMARY KEY (`id`)) CHARSET=utf8mb4;\n\nCREATE TABLE `channel` ( \n`id` BIGINT(70) NOT NULL AUTO_INCREMENT, \n`channel` VARCHAR(200) NOT NULL , \n`channelnumber` VARCHAR(200) NOT NULL DEFAULT '0',\n`channelorder1` VARCHAR(200) NOT NULL DEFAULT '0',\n`channelorder2` VARCHAR(200) NOT NULL DEFAULT '0',\n`channelpay` VARCHAR(200) NOT NULL DEFAULT '0',\n`channelout` VARCHAR(200) NOT NULL DEFAULT '0',\n`channelokorder` VARCHAR(200) NOT NULL DEFAULT '0',\n`channeladmins` VARCHAR(200) NOT NULL DEFAULT '0',\n`channelsupport` VARCHAR(200) NOT NULL DEFAULT '0',\nPRIMARY KEY (`id`))CHARACTER SET utf8mb4;\n\nCREATE TABLE `list` ( \n`code` BIGINT(70) NOT NULL AUTO_INCREMENT,\n`status` VARCHAR(11) NOT NULL ,\n`chatid` BIGINT(70) NOT NULL ,\n`codeapi` VARCHAR(50) NOT NULL DEFAULT '0',\n`count` BIGINT(70) NOT NULL ,\n`link` VARCHAR(1000) NOT NULL ,\n`factor` BIGINT(70) NOT NULL ,\n`sefaresh` VARCHAR(2000) NOT NULL ,\n`date` VARCHAR(50) NOT NULL ,\n`api` VARCHAR(500) NOT NULL DEFAULT 'noapi',\nPRIMARY KEY (`code`)) CHARSET=utf8mb4;\n\nCREATE TABLE `off` ( \n`id` BIGINT(70) NOT NULL AUTO_INCREMENT,\n`bot` INT(1) NOT NULL DEFAULT '1',\n`buy` INT(1) NOT NULL DEFAULT '0',\n`pay` INT(1) NOT NULL DEFAULT '0',\n`number1` INT(1) NOT NULL DEFAULT '0',\n`number2` INT(1) NOT NULL DEFAULT '0',\n`member` INT(1) NOT NULL DEFAULT '0',\n`free` INT(1) NOT NULL DEFAULT '0',\n`kart` INT(1) NOT NULL DEFAULT '0',\n`Porsant` INT(1) NOT NULL DEFAULT '0',\n`Free_mojodi` INT(1) NOT NULL DEFAULT '0',\n`First_Gift` INT(1) NOT NULL DEFAULT '0',\n`okorder` INT(1) NOT NULL DEFAULT '0',\n`sms1` INT(1) NOT NULL DEFAULT '0',\n`sms2` INT(1) NOT NULL DEFAULT '0',\nPRIMARY KEY (`id`));\n\nCREATE TABLE `payment` ( \n`id` BIGINT(70) NOT NULL AUTO_INCREMENT,\n`name` VARCHAR(500) NOT NULL ,\n`code` VARCHAR(200) NOT NULL ,\n`file` VARCHAR(200) NOT NULL ,\n`off` INT(1) NOT NULL DEFAULT '1',\nPRIMARY KEY (`id`)) CHARSET=utf8mb4;\n\nCREATE TABLE `pays` ( \n`code` BIGINT(70) NOT NULL AUTO_INCREMENT,\n`chatid` BIGINT(70) NOT NULL ,\n`step` VARCHAR(11) NOT NULL ,\n`paycode` VARCHAR(200) NOT NULL ,\n`amount` BIGINT(70) NOT NULL ,\n`number` BIGINT(70) NOT NULL ,\n`date` VARCHAR(50) NOT NULL ,\n`ip` VARCHAR(200) NOT NULL DEFAULT '0',\nPRIMARY KEY (`code`)); \n\nCREATE TABLE `paysout` ( \n`code` BIGINT(70) NOT NULL AUTO_INCREMENT,\n`chatid` INT(15) NOT NULL ,\n`step` VARCHAR(11) NOT NULL ,\n`type` VARCHAR(11) NOT NULL ,\n`amount` BIGINT(70) NOT NULL ,\n`date` VARCHAR(50) NOT NULL ,\nPRIMARY KEY (`code`)); \n\nCREATE TABLE `sendall` (\n`id` BIGINT(70) NOT NULL AUTO_INCREMENT,\n`step` varchar(20) NOT NULL,\n`msgid` varchar(200) NOT NULL,\n`text` text NOT NULL,\n`chat` varchar(200) NOT NULL,\n`user` INT(10) NOT NULL,\n`admin` INT(20) NOT NULL ,\nPRIMARY KEY (`id`)) CHARSET=utf8mb4; \n\nCREATE TABLE `setting`( \n`id` BIGINT(70) NOT NULL AUTO_INCREMENT,\n`Free_mojodi` BIGINT(70) NOT NULL DEFAULT '0',\n`Porsant` BIGINT(70) NOT NULL DEFAULT '0',\n`darsad` BIGINT(70) NOT NULL DEFAULT '0',\n`all_orders` BIGINT(70) NOT NULL DEFAULT '0',\n`all_pay` BIGINT(70) NOT NULL DEFAULT '0',\n`ptid` varchar(100) NOT NULL DEFAULT '0',\n`usersms` varchar(100) NOT NULL DEFAULT '0',\n`pasms` varchar(100) NOT NULL DEFAULT '0',\n`ticket` INT(5) NOT NULL DEFAULT '3',\n`First_Gift` BIGINT(70) NOT NULL DEFAULT '0',\n`lastcron` varchar(50) NOT NULL DEFAULT '0',\nPRIMARY KEY (`id`)); \n\nCREATE TABLE `text` ( \n`id` BIGINT(70) NOT NULL AUTO_INCREMENT, \n`textstart` text NOT NULL ,  \n`banertx` text NOT NULL ,  \n`kartbekart` text NOT NULL ,  \n`reftx` text NOT NULL ,  \n`backpay` text NOT NULL ,  \nPRIMARY KEY (`id`)) CHARSET=utf8mb4;\n\nCREATE TABLE `users` ( \n`id` BIGINT(70) NOT NULL , \n`step` VARCHAR(50) DEFAULT 'none', \n`commend` VARCHAR(50) DEFAULT '0', \n`balance` BIGINT(70)  DEFAULT '0',\n`link` VARCHAR(200)  DEFAULT 'none',\n`count` BIGINT(70)  DEFAULT '0', \n`serviceid` BIGINT(70)  DEFAULT '0', \n`ordername` VARCHAR(500)  DEFAULT 'none', \n`ordername1` VARCHAR(500)  DEFAULT 'none', \n`sefaresh` VARCHAR(2000)  DEFAULT 'none', \n`price` VARCHAR(200)  DEFAULT '0',\n`min` BIGINT(70)  DEFAULT '0',\n`max` BIGINT(70)  DEFAULT '0',\n`number` BIGINT(70)  DEFAULT '0',\n`all_orders` BIGINT(70)  DEFAULT '0',\n`all_pay` BIGINT(70)  DEFAULT '0',\n`Gift1` BIGINT(70)  DEFAULT '0',\n`Gift2` BIGINT(70)  DEFAULT '0',\n`GiftMoj` BIGINT(70)  DEFAULT '0',\n`refral` BIGINT(70)  DEFAULT '0',\n`joindate` VARCHAR(50) NOT NULL ,\n`ticket` INT(1)  DEFAULT '0',\n`refid` BIGINT(70) NOT NULL DEFAULT '0',\nPRIMARY KEY (`id`)) CHARACTER SET utf8mb4; \n\nCREATE TABLE `works` (\n`id` BIGINT(70) NOT NULL , \n`step` VARCHAR(2000) NOT NULL DEFAULT 'none',\n`ids` VARCHAR(2000)  DEFAULT '0',\n`toman` BIGINT(70)  DEFAULT '0',\nPRIMARY KEY (`id`)) CHARSET=utf8mb4;\n\nINSERT INTO `setting`(`Free_mojodi`, `Porsant`) VALUES ('100','5');\nINSERT INTO `off`(`bot`) VALUES (1);\nINSERT INTO `sendall` (`step`, `msgid`, `text`, `chat`, `user`, `admin`) VALUES('none', '0', '0', '0', '0','0');\nINSERT INTO `text` (`textstart`, `banertx`, `kartbekart`,`reftx`,`backpay`) VALUES ('start', 'baner', 'kart','reftxt','backpay');\nINSERT INTO `channel` (`channel`,`channelpay`,`channelout`,`channelsupport`) VALUES ('0','" . $admin . "','" . $admin . "','" . $admin . "');\nINSERT INTO `payment` (`name`,`code`,`file`) VALUES ('زرین پال',0,'zarinpal'),('آیدی پی',0,'idpay');");
        if ($res == 1) {
            return "OK";
        }
        return "false";
    }
}

?>