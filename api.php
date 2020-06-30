<?php
/*
Main script for insert rules
*/

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
// Look up for IP. Is whitelisted? If yes exit
    $q = "SELECT * FROM `ipv4` WHERE `ip` LIKE $ip AND `semaphore_id` = 0;";
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
            if ($result->num_rows) > 0 {
                $result->close();
                exit();
            }
    } else
        {
            $q = "INSERT into `ipv4` (`id`, `ip`, `mask`, `updatetime`, `semaphore_id`) VALUES (NULL, '$ip', '32', CURRENT_TIME(), '3');";
            echo $q;
            if ($mysqli->query($q) === TRUE) {
                printf("OK\n");
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
$x = "2a00:19a0:3:74:0:d9c6:7481:1";
$i = filterinputip($x);
echo $i.PHP_EOL;
$x = "test.rest.cz";
$i = filterinputip($x);
echo $i.PHP_EOL;

?>
