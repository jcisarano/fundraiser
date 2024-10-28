<?php
require_once( "config.php" );
require_once( "keycode.php" );
require_once( "database.php" );

$iUserId = 2;

$db_config = $config["db"];
$mysqli = DatabaseFactory::Create( 
    "mysqli", 
    $db_config->host,
    $db_config->user,
    $db_config->password,
    $db_config->catalog );
    
$code = Keycode_Generate();

echo "Code:{$code}<br>";

$iId = Keycode_Create( $mysqli, $iUserId, $code, 1, "2016-12-01" );

echo "ID: {$iId}";

if( Keycode_Validate( $mysqli, $iUserId, $code )) {
    echo "valid code <br>";
    if( Keycode_Use( $mysqli, $code, 1 ) ) {
        echo "used<br>";
    } else {
        echo "not used<br>";
    }
} else {
    echo "invalid code<br>";
}