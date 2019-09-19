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
    
    function getrules($type,$timestamp,$iptype) 
    {
	
	return($dataset);
    }

}

// only for test
$fw = new Firewall();
$xts = $fw->gettimestamp();
echo $xts.PHP_EOL;
sleep(10);
$xts = $fw->settimestamp();
?>
