<?php
// some settings
require("settings.php");

class Rules
{
    function getrules()
    {
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
                        $cmd = "cat ".$log." | grep -E '".$rgx."' awk '{print $2}' | sort | uniq -c | sort -n > multiple.tmp";
                        $execute = escapeshellcmd($cmd);
                        echo $execute.PHP_EOL;
                        //$out =  shell_exec($execute);
                    
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
