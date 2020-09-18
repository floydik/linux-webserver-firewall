<?php
/*
Main script for insert rules
*/
// import settings
require ("./settings.php");

// todo:
// autentization
// IP or range of IPs
// user and pasword
// api key
// client certificate

// log request
function logrequest() {
    (isset($_POST['ip'])) ? $ip = $_POST['ip'] : $ip=0;
    (isset($_POST['source'])) ? $source = $_POST['source'] : $source = 0;
    (isset($_POST['reason'])) ? $reason = $_POST['reason'] : $reason = "";
    (isset($_POST['action'])) ? $action = $_POST['action'] : $action = 0;
    (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $clientip = $_SERVER['HTTP_X_FORWARDED_FOR'] : $clientip = $_SERVER['REMOTE_ADDR'];
    $str = $ip." ".$source." ".$reason." ".$action." ".$clientip."\n";
    $handle = fopen(LOGFIE, "a+");
    fwrite($handle, $str);
    fclose($handle);
    return array ($ip, $action);
}


// verify IP (IPv4 or IPv6 or sth else)
function filterinputip($ip) {
    if (filter_var($ip, FILTER_VALIDATE_IP,FILTER_FLAG_IPV4)) {
        $ex = 4;
        }
        elseif (filter_var($ip, FILTER_VALIDATE_IP,FILTER_FLAG_IPV6)) {
          $ex = 6;
        }
    else {
        $ex = 0;
        }
  return($ex);
}


// Insert IP or update semaphore_id in ipv4 or ipv6 tables based on action $a
// 0 - temporarily blacklisted, 1 - permanently blacklisted, 2 - whitelisted, 3 - delisted
function insertipvC($ip,$c,$a) {
    echo "fce insertipv$c".PHP_EOL;
// Look up for IP. Is whitelisted? If yes exit
    $q = "SELECT * FROM `ipv$c` WHERE `ip` LIKE '$ip';";
// If is new then set semaphore = 3 and insert IP
// if is exists then semaphore++ and update tables
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if (mysqli_connect_error()) {
        die('Connect Error (' . mysqli_connect_errno() . ') '
           . mysqli_connect_error());
        }
    //
    if ($result = $mysqli->query($q)) {
        if (($result->num_rows) > 0) {
            $c=4 ? $mask = 32 : $mask = 128;
            echo "IP se našla ;-)".PHP_EOL;
            $fields = $result->fetch_assoc();
            echo "semafor: ".$fields['semaphore_id'].PHP_EOL;
            if ($fields['semaphore_id'] > 0) {
                $id = $fields['id'];
                switch ($a) {
                    case 0:
                        $q2 = "UPDATE `ipv$c` SET `semaphore_id` = `semaphore_id` + 1 WHERE `id` = $id;";
                        break;
                    case 1:
                        $q2 = "UPDATE `ipv$c` SET `semaphore_id` = 5 WHERE `id` = $id;";
                        break;
                    case 2:
                        $q2 = "UPDATE `ipv$c` SET `semaphore_id` = 0 WHERE `id` = $id;";
                        break;
                    case 3:
                        $q2 = "UPDATE `ipv$c` SET `semaphore_id` = 1 WHERE `id` = $id;";
                        break;
                }
                $result->close();
                echo $q2.PHP_EOL;
                if ($mysqli->query($q2) === TRUE) {
                    printf("UPDATE OK\n");
                }
            }
        } else {
            switch ($a) {
                case 0:
                    $q = "INSERT into `ipv$c` (`id`, `ip`, `mask`, `updatetime`, `semaphore_id`) VALUES (NULL, '$ip', '32', NULL, '3');";
                    break;
                case 1:
                    $q = "INSERT into `ipv$c` (`id`, `ip`, `mask`, `updatetime`, `semaphore_id`) VALUES (NULL, '$ip', '32', NULL, '5');";
                    break;
                case 2:
                    $q = "INSERT into `ipv$c` (`id`, `ip`, `mask`, `updatetime`, `semaphore_id`) VALUES (NULL, '$ip', '32', NULL, '0');";
                    break;
                case 3:
                    $q = "INSERT into `ipv$c` (`id`, `ip`, `mask`, `updatetime`, `semaphore_id`) VALUES (NULL, '$ip', '32', NULL, '0');";
                    break;
            }
            echo $q.PHP_EOL;
            if ($mysqli->query($q) === TRUE) {
                printf("INSERT OK\n");
            }
        }
    }
    $mysqli->close();
} // end of insertipvX


// Let's Rock'n'roll!

// dig off input parameters
$l = logrequest();
$ip = $l[0];
$x = filterinputip($ip);
$action = $l[1];

if ($x>0) { 
    echo "<!DOCTYPE html>\n<html>\n<body>\n";
    echo "<div>\n";
    echo "<p id=\"content\">";
    insertipvC($ip,$x,$action);
    echo "</p>";
    echo "</div>\n";
    echo "</body>\n</html>\n";
}


?>
