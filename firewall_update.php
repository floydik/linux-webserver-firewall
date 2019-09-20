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
    
	
    function executerules($protocol,$rule,$ts) 
    {
	// $protocol: 4 or 6
	// $rule: add or del
	// $ts: date('Y-m-d H:i:s')
	if ($stmt = $mysqli->prepare("SELECT `ip`,`mask` FROM `ipv?` WHERE `updatetime` > ? AND `semaphore_id` BETWEEN 2 AND 5;")) {
		$stmt->bind_param("s", $protocol);
		$stmt->bind_param("s", $ts);
		$stmt->execute();
		$stmt->bind_result($ip,$mask);
		while ($stmt->fetch()) {
			$execute = "ip -".$protocol." route ".$rule." blackhole ".$ip."/".$mask;
			echo $execute.PHP_EOL;
			$out =  shell_exec($execute);
		}
		$stmt->close();
		$ex++;
	}    
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
	// update ALL rules 
	$protocol = "4";
	$rule = "add";
	$ts = date('Y-m-d H:i:s',$timestamp);
	$this->executerules($protocol,$rule,$ts);    
	$protocol = "6";
	$this->executerules($protocol,$rule,$ts);
	// remove GREEN
	$ts = date('Y-m-d H:i:s',$timestamp - GREEN_TIME);
	$protocol = "4";
	$rule = "del";
	$this->executerules($protocol,$rule,$ts);
	$protocol = "6";
	$this->executerules($protocol,$rule,$ts);
	// REMOVE YELLOW

	// REMOVE RED
	    
	    
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
echo "x: ".$x.PHP_EOL;
?>
