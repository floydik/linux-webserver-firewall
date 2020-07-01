<?php
/*
Main script for insert rules
*/
// import settings
require ("./settings.php");

// autentization
// IP or range of IPs
// user and pasword
// api key
// client certificate

// log request
function logrequest() {
    if (isset($_GET['ip'])) $ip = $_GET['ip'];
    if (isset($_GET['source'])) $source = $_GET['source'];
    if (isset($_GET['reason'])) $reason = $_GET['reason'];
    if (isset($_GET['action'])) $action = $_GET['action'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $clientip = $_SERVER['HTTP_X_FORWARDED_FOR'] else $clientip = $_SERVER['REMOTE_ADDR'];
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



// Insert IP or update semaphore_id in ipv4 or ipv6 tables
function insertipvC($ip,$c) {
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
            echo "IP se našla ;-)".PHP_EOL;
            $fields = $result->fetch_assoc();
            echo "semafor: ".$fields['semaphore_id'].PHP_EOL;
            if ($fields['semaphore_id'] > 0) {
                $id = $fields['id'];
                $q2 = "UPDATE `ipv$c` SET `semaphore_id` = `semaphore_id` + 1 WHERE `id` = $id;";
                $result->close();
                echo $q2.PHP_EOL;
                if ($mysqli->query($q2) === TRUE) {
                    printf("UPDATE OK\n");
                }
            }
        } else {
            $q = "INSERT into `ipv$c` (`id`, `ip`, `mask`, `updatetime`, `semaphore_id`) VALUES (NULL, '$ip', '32', NULL, '3');";
            echo $q.PHP_EOL;
            if ($mysqli->query($q) === TRUE) {
                printf("INSERT OK\n");
            }
        }
    }
    $mysqli->close();
} // end of insertipvX



$x = "217.198.116.129";
$i = filterinputip($x);
echo $i.PHP_EOL;
insertipvC($x,$i);

$x = "2a00:19a0:3:74:0:d9c6:7481:1";
$i = filterinputip($x);
echo $i.PHP_EOL;
insertipvC($x,$i);

$x = "test.rest.cz";
$i = filterinputip($x);
echo $i.PHP_EOL;

print_r (logrequest());
?>
