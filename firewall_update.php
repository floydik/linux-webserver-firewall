<?php
/* Description:
   Firewall have file in /tmp/
   After reboot file is missing and imported rules too
*/

// some settings
$tsfile = "/tmp/firewall.sync";
require("settings.php");

class Firewall
{
    function gettimestamp() 
    {
	global $tsfile;
	if (file_exists($tsfile)) {
	    $fh = fopen($tsfile,"r");
	    $ts = fgets($fh);
	    fclose($fh);
	} else {
	    $ts = 0;
	    $fh = fopen($tsfile,"w+");
	    $cts = time().PHP_EOL;
	    fwrite($fh, $cts);
	    fclose($fh);
	}
	return($ts);
    }

    function settimestamp() 
    {
	global $tsfile;
	    $ts = 0;
	    $fh = fopen($tsfile,"w+");
	    $cts = time().PHP_EOL;
	    fwrite($fh, $cts);
	    fclose($fh);
	return($ts);
    }
    
    function getrules($timestamp) 
    {
	// $type whitelist/blacklist
	// $timestamp 
	// $iptype IPv4/IPv6
	$ex = -2;
	// connect to DB
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if (mysqli_connect_error()) {
    	die('Connect Error (' . mysqli_connect_errno() . ') '
           . mysqli_connect_error());
	}
	echo 'Success... ' . $mysqli->host_info . "\n";

	
	// get data 
	if ($stmt = $mysqli->prepare("SELECT `ip`,`mask` FROM `ipv4` WHERE `updatetime` > ? AND `semaphore_id` BETWEEN 2 AND 4;")) {
		$stmt->bind_param("s", $timestamp);
		$stmt->execute();
		$stmt->bind_result($ip,$mask);
		while ($stmt->fetch()) {
			printf("ip route add blackhole %s/%s\n", $ip, $mask);
		}
		$stmt->close();
		$ex++;
	}
	if ($stmt = $mysqli->prepare("SELECT `ip`,`mask` FROM `ipv6` WHERE `updatetime` > ? AND `semaphore_id` BETWEEN 2 AND 4;")) {
		$stmt->bind_param("s", $timestamp);
		$stmt->execute();
		$stmt->bind_result($ip,$mask);
		while ($stmt->fetch()) {
			printf("ip route add blackhole %s/%s\n", $ip, $mask);
		}
		$stmt->close();
		$ex++;
	}		    
	$mysqli->close();
	return($ex);
    }

}

// only for test
$fw = new Firewall();
$xts = $fw->gettimestamp();
echo $xts.PHP_EOL;
$x = $fw->getrules($xts);
sleep(2);
$xts = $fw->settimestamp();
echo "x: "$x.PHP_EOL;
?>
