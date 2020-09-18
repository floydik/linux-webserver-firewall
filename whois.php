<?php
// get country code from whois
$args = $_SERVER['argv'];
$host = $args[1];
$sh = "whois ".$host." | grep -m1 -i country\: | awk '{print $2}'";
$out =  trim(strtoupper (shell_exec($sh)));
echo $host." ".$out.PHP_EOL;
?>
