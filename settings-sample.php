<?php
/* 
    Database settings 
*/
// The name of the database 
define( 'DB_NAME', 'some_databases' );
// username for DB
define( 'DB_USER', 'some_user' );
// password for DB
define( 'DB_PASSWORD', 'some_password' );
// MySQL hostname */
define( 'DB_HOST', 'some_server.some_domain.tld' );
// Database Charset
define( 'DB_CHARSET', 'utf8' );
// Database Collate type
define( 'DB_COLLATE', '' );
?>

/* 
    Rules settings 
*/

define( 'GREEN_TIME', 60 * 60 ); // one hour
define( 'YELLOW_TIME', 7 * 24 * 60 * 60 ); // one week
define( 'RED_TIME', 30 * 24 * 60 * 60 ); // 30 days

/* log file settings */
// api:
define ( 'LOGFIE', 'requests.log' );
// local incidents log:
define ( 'LOG', '/www/www/public_html/blackhole.log' );

?>
