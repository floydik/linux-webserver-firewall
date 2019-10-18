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
// Look up for IP. Is whitelisted? If yes exit
// Look up for IP range. Is whitelisted? If yes exit
// If is new then set semaphore = 2 and insert IP
// if is exists then semaphore++ and update tables

// IPv6
// Look up for IP. Is whitelisted? If yes exit
// Look up for IP range. Is whitelisted? If yes exit
// If is new then set semaphore = 2 and insert IP
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
