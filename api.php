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



// IPv4 
function insertipv4($ip) {
    echo "fce insertipv4".PHP_EOL;
// Look up for IP. Is whitelisted? If yes exit
    $q = "SELECT * FROM `ipv4` WHERE `ip` LIKE '$ip';";
// Look up for IP range. Is whitelisted? If yes exit
// If is new then set semaphore = 3 and insert IP
// if is exists then semaphore++ and update tables
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if (mysqli_connect_error()) {
        die('Connect Error (' . mysqli_connect_errno() . ') '
           . mysqli_connect_error());
        }
    //
    if ($result = $mysqli->query($q)) {
        echo "IP se našla ;-)".PHP_EOL;
        $fields = $result->fetch_assoc();
        if ($fields['semaphore_id'] == 0) return (0);
        if ($fields['semaphore_id'] > 0) {
            $id = $fields['id'];
            $q2 = "UPDATE `ipv4` SET `semaphore_id` = `semaphore_id` + 1 WHERE `id` = $id;";
            return (1);    
        }
        $result->close();
        if ($mysqli->query($q2) === TRUE) {
            printf("UPDATE OK\n");
        }
    } else {
        $q = "INSERT into `ipv4` (`id`, `ip`, `mask`, `updatetime`, `semaphore_id`) VALUES (NULL, '$ip', '32', NULL, '3');";
        echo $q.PHP_EOL;
        if ($mysqli->query($q) === TRUE) {
            printf("INSERT OK\n");
        }
    }
    $mysqli->close();
} // end of insertipv4

// IPv6
// Look up for IP. Is whitelisted? If yes exit
// Look up for IP range. Is whitelisted? If yes exit
// If is new then set semaphore = 3 and insert IP
// if is exists then semaphore++ and update tables

$x = "217.198.116.129";
$i = filterinputip($x);
echo $i.PHP_EOL;
if ($i==4) insertipv4($x);

$x = "2a00:19a0:3:74:0:d9c6:7481:1";
$i = filterinputip($x);
echo $i.PHP_EOL;
$x = "test.rest.cz";
$i = filterinputip($x);
echo $i.PHP_EOL;


?>
