<?php
// some settings
$tmpfile = "multiple.tmp";
$tmpfile2 = "multiple2.tmp";

require("settings.php");

class Rules
{
    function getrules()
    {
        global $tmpfile;
        global $tmpfile2;
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if (mysqli_connect_error()) {
              die('Connect Error (' . mysqli_connect_errno() . ') '
           .  mysqli_connect_error());
        }
        echo 'Success... ' . $mysqli->host_info . "\n";
        if ($stmt = $mysqli->prepare("SELECT `regex` , `log` , `threshold` , `execute` FROM `rules` WHERE `active` = 1;")) {
                $stmt->execute();
                $stmt->bind_result($rgx,$log,$trh,$ex);
                while ($stmt->fetch()) {
                        $execute = "cat ".$log." | grep -E '".$rgx."' | awk '{print $2}' | sort | uniq -c | sort -n > ".$tmpfile;
                        echo $execute.PHP_EOL;
                        $out =  shell_exec($execute);
                        if (file_exists($tmpfile)) {
                            $handle = fopen($tmpfile, "r");
                            while(($ln=fgets($handle)) !==false) {
                                $ln=trim($ln);
                                $val=explode(" ",$ln);
                                $ip=$val[1];
                                $count=$val[0];
                                if ($count > $trh) echo "blokujeme: ".$count.",".$ip.PHP_EOL; // for test only
                                // get last request from IP
                                $execute2 = "cat ".$log." | grep -E '".$rgx."' | grep ".$ip." > ".$tmpfile2;
                                $out2 =  shell_exec($execute2);
                                echo "vystup2: ".$out2.PHP_EOL;
                            };
                            fclose($handle);
                        }
                    
                }
                $stmt->close();
        }

      

    } // getrules
  
// SELECT `regex` , `log` , `threshold` , `execute` FROM `rules` WHERE `active` = 1;


} // end of Rules

$rul = new Rules();
$x = $rul->getrules();
echo "... and it is done!".PHP_EOL;

?>
