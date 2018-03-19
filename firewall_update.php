<?php
// some settings
$tsfile = "/tmp/firewall.sync";

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
	    $ts=0;
	    $fh = fopen($tsfile,"w+");
	    $cts = time().PHP_EOL;
	    fwrite($fh, $cts);
	    fclose($fh);
	}
	return($ts);
    }


}

// only for test
$fw = new Firewall();
$xts = $fw->gettimestamp();
echo $xts.PHP_EOL;

?>