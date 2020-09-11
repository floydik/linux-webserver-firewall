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
	$ex = -2; //exit value
	$timestamp = intval($timestamp); // string -> integer
	$ts_date = date('Y-m-d H:i:s',$timestamp);
	// connect to DB
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if (mysqli_connect_error()) {
    	die('Connect Error (' . mysqli_connect_errno() . ') '
           . mysqli_connect_error());
	}
	echo 'Success... ' . $mysqli->host_info . "\n";
	
	// get data 
	    $timenow = time();
	    $ts_green = date('Y-m-d H:i:s',$timenow-GREEN_TIME);
	    $ts_yellow = date('Y-m-d H:i:s',$timenow-YELLOW_TIME);
	    $ts_red = date('Y-m-d H:i:s',$timenow-RED_TIME);
	// update ALL rules 
	// first update IPv4 rules    
	if ($stmt = $mysqli->prepare("SELECT `ip`,`mask` FROM `ipv4` WHERE `updatetime` > ? AND `semaphore_id` BETWEEN 2 AND 5;")) {
		$stmt->bind_param("s", $ts_date);
		$stmt->execute();
		$stmt->bind_result($ip,$mask);
		while ($stmt->fetch()) {
			$execute = "ip route add blackhole ".$ip."/".$mask;
			echo $execute.PHP_EOL;
			$out =  shell_exec($execute);
		}
		$stmt->close();
		$ex++;
	}
	// then update IPv6 rules
	if ($stmt = $mysqli->prepare("SELECT `ip`,`mask` FROM `ipv6` WHERE `updatetime` > ? AND `semaphore_id` BETWEEN 2 AND 5;")) {
		$stmt->bind_param("s", $ts_date);
		$stmt->execute();
		$stmt->bind_result($ip,$mask);
		while ($stmt->fetch()) {
			$execute = "ip route add blackhole ".$ip."/".$mask;
			echo $execute.PHP_EOL;
			$out =  shell_exec($execute);
		}
		$stmt->close();
		$ex++;
	}
	// remove IPv4 expired rules and "violet" rules
	$query = "SELECT `ip`,`mask` FROM `ipv4` WHERE `updatetime` > '$ts_date' AND 
	((`updatetime` < '$ts_green' AND `semaphore_id` = 2) 
	OR (`updatetime` < '$ts_yellow' AND `semaphore_id` = 3) 
	OR (`updatetime` < '$ts_red' AND `semaphore_id` = 4) 
	OR (`semaphore_id` = 1));";
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->execute();
		$stmt->bind_result($ip,$mask);
		while ($stmt->fetch()) {
			$execute = "ip route del blackhole ".$ip."/".$mask;
			echo $execute.PHP_EOL;
			$out =  shell_exec($execute);
		}
		$stmt->close();
		$ex++;
	}    
	// and finaly remove IPv6 expired and "violet" rules
	$query = "SELECT `ip`,`mask` FROM `ipv6` WHERE `updatetime` > '$ts_date' AND 
	((`updatetime` < '$ts_green' AND `semaphore_id` = 2) 
	OR (`updatetime` < '$ts_yellow' AND `semaphore_id` = 3) 
	OR (`updatetime` < '$ts_red' AND `semaphore_id` = 4) 
	OR (`semaphore_id` = 1));";
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->execute();
		$stmt->bind_result($ip,$mask);
		while ($stmt->fetch()) {
			$execute = "ip route del blackhole ".$ip."/".$mask;
			echo $execute.PHP_EOL;
			$out =  shell_exec($execute);
		}
		$stmt->close();
		$ex++;
	}    
	    
	$mysqli->close(); //close connection to MySQL 
	return($ex);
    }
}
// only for test
$fw = new Firewall();
$xts = $fw->gettimestamp();
echo '<!DOCTYPE html>\n<html>\n<body>\n';
echo $xts.PHP_EOL;
$x = $fw->getrules($xts);
$xts = $fw->settimestamp();
echo "... and it is done!".PHP_EOL;
echo $x;
echo '</body>\n</html>\n';
?>
