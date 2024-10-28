<?php
require_once( "config.php" );
require_once( 'user.class.php' );
require_once( 'crossdomain.php' );

$sCookieName = $config["sessionCookie"];
$sDomain = ".".$config["host"];
$request = array_merge( $_GET, $_POST );

setcookie( $sCookieName, '', time()-86400);
setcookie( $sCookieName, '', time()-86400,'/', $sDomain );

if( isset( $_COOKIE ) === false || isset( $_COOKIE[ $sCookieName ] ) === false ) {
    $sResult[ "success" ] = true;
    echo json_encode( $sResult );
    die();
}


$db = DatabaseFactory::Create( 
    "mysqli", 
    $config["db"]->host,
    $config["db"]->user,
    $config["db"]->password,
    $config["db"]->catalog );

if( VerifySession( $db, $_COOKIE[ $sCookieName ] ) === false ) {
    $sResult[ "success" ] = true;
    echo json_encode( $sResult );
    die();
}

User_Logout( $db, $_COOKIE[ $sCookieName ] );

$sResult[ "success" ] = true;
echo json_encode( $sResult );

function VerifySession( $pDB, $cSession )
{
  $iUserId = UserSession_Validate( $pDB, $cSession );
  if ( $iUserId > 0 )
    return true;
  return false;
}