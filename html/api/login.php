<?php
require_once( "config.php" );
require_once( 'user.class.php' );
require_once( 'crossdomain.php' );

$cookieName = $config["sessionCookie"];
$domain = ".".$config["host"];
$request = array_merge( $_GET, $_POST );

$db = DatabaseFactory::Create( 
    "mysqli", 
    $config["db"]->host,
    $config["db"]->user,
    $config["db"]->password,
    $config["db"]->catalog );

$cUserName = $request['email'];
$cPassword = $request['password']; 

$cSession = "";
$iResult = User_Login( $db, $cUserName, $cPassword, $cSession );
if ( $iResult > 0 ) {
    setcookie($cookieName, $cSession, 86400 + time(0), '/', $domain );
    $sResult = Array();
    $sResult['success']=true;
    $sResult['Username']=$cUserName;
    $sResult['Session']=$cSession;
    $pUser = VerifySession( $db, $cSession );
    $sResult['FirstName']=$pUser->field['user_first_name'];
    $sResult['LastName']=$pUser->field['user_last_name'];
    echo json_encode($sResult); 
} else {
    echo json_encode(array("success"=>false,"message"=>"Invalid username or password.")); 
}

function VerifySession( $pDB, $cSession )
{
    $pUser = UserSession_GetUser( $pDB, $cSession );
    return $pUser;
}
