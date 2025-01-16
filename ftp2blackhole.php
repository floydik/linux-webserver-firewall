<?php
// some settings
$tmpfile = "ftp.tmp";
$apiurl = 'http://somadomain.tld/linux-webserver-firewall/api.php';

require("settings.php");

class Rules
{
    function blackhole ($ip, $reason)
    {
        Rules::log($reason);
        $curl = curl_init();

        $postfields = array(
        "ip" => $ip,
        "source" => 'ftp attack detector',
        "reason" => $reason,
        "action" => ''
        );

        $options = array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $apiurl,
        CURLOPT_USERAGENT => 'Request z Narvi',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $postfields
        );

        curl_setopt_array($curl,$options);

        curl_exec($curl);
        curl_close($curl);
    }

    function log($log)
    {
        $handle = fopen(LOG, "a+");
        fwrite($handle, $log);
        fclose($handle);
    }

    function blackholed($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP,FILTER_FLAG_IPV4)) {
            $ex = "ip route sh | grep ".$ip;
            }
            elseif (filter_var($ip, FILTER_VALIDATE_IP,FILTER_FLAG_IPV6)) {
            $ex = "ip -6 route sh | grep ".$ip;;
            }
        else {
            $ex = "";
        }
        unset($out);
        $x = 0;
        exec($ex,$out,$stats);
        if (isset($out[0])) $x = 1;
        return($x);
    }


    function getrules($ip)
    {
        global $tmpfile;

        if (Rules::blackholed($ip) == 0) {
            echo "blokujeme: ".$ip.PHP_EOL; // for test only
            // get last request from IP
            $execute = "cat /var/log/syslog | grep \"Authentication failed\" | grep ".$ip." | tail -n 1 > ".$tmpfile;
            $out2 =  shell_exec($execute);
            if (file_exists($tmpfile)) {
                $handle2 = fopen($tmpfile, "r");
                while(($ln2=fgets($handle2)) !==false) {
                    // call api
                    Rules::blackhole($ip, $ln2);
                    echo $ln2.PHP_EOL;
                }
            fclose($handle2);
            }
       }

    }

} // end of Rules

$rul = new Rules();
$ip = trim($_SERVER['argv'][1]);
$x = $rul->getrules($ip);
echo "... and it is done!".PHP_EOL;

?>

