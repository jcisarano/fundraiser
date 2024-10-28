<?php
require_once( "config.php" );
require_once( "database.class.php" );
require_once( "user.class.php" );
require_once( 'crossdomain.php' );
require_once( 'keycode.class.php' );
require_once( "email.class.php" );

$cookieName = $config["sessionCookie"];
$request = array_merge( $_GET, $_POST );

$pDB = DatabaseFactory::Create( 
    "mysqli", 
    $config["db"]->host,
    $config["db"]->user,
    $config["db"]->password,
    $config["db"]->catalog );

$cUsername = @$request[ "username" ];

$pUser = User_Get( $pDB, $cUsername );
if( $pUser == NULL ) {
    $cError = "Invalid user login given";
    echo json_encode( array( "success"=>false, "message"=>$cError ));
    die();
}

if ( $_SERVER['REQUEST_METHOD'] == 'GET' )
{
    //password change request

    //generate a single use keycode and save it to the database
    $cCode = Keycode_Generate();
    $iNumUses = 1;
    $cExpiration = date( "Y-m-d H:i:s", (time() + 60*60) );
    $pKeycode = Keycode_Create( $pDB, $pUser->field["user_id"], $cCode, $iNumUses, $cExpiration );
    
    if( is_numeric( $pKeycode )) {
        //TODO: Proper return values
        $cError = "Unable to request password reset, please try again later";
        echo json_encode( array( "success"=>false, "message"=>$cError ));
        die();
    }

    //build reset email & send it to the user
    $cResetLink = "http://{$config["host"]}/password-reset.php?reset={$cCode}";

    $mailData = new EmailData;
    $mailData->email = $cUsername;
    $mailData->firstName = $pUser->field["user_first_name"];
    $mailData->lastName = $pUser->field["user_last_name"];
    $mailData->zip = "";
    $mailData->replyName = "No reply";
    $mailData->replyEmail = "no-reply@".$config["host"];
    $mailData->apiKey = $config["email"]->mandrillKey;

    $wrapper = Email::BuildEmail( Email::WRAPPER_FILE, "" );
    $email = Email::BuildEmail( Email::WRAPPER_FILE . "passwordReset.txt", $wrapper["body"], $cResetLink );
    
    Email::MandrillInstantSend( $pDB, $mailData, $email["body"], $email["body"], $email["subject"] );
    
    //TODO: Proper return values
    $cMessage = "Password reset instructions have been sent to {$cUsername}";
    echo json_encode( array( "success"=>true, "message"=>$cMessage ));
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    //password change form

    $bSuccess = false;
    $cCode = @$request["reset_code"];
    $cNewPassword = @$request["new_pw"];
    $iUseCount = 1;

    if( Keycode_Validate( $pDB, $pUser->field["user_id"], $cCode ) ) {
        if( $pUser->UpdatePassword( $cNewPassword ) )
            $bSuccess = Keycode_Use( $pDB, $pUser->field["user_id"], $cCode, $iUseCount );
    }

    if( $bSuccess ) {
        $cMessage = "Password has been reset for {$cUsername}";
    } else {
        $cMessage = "Unable to reset password for {$cUsername}, invalid code given";
    }

    //TODO: Proper return values
    echo json_encode( array( "success"=>$bSuccess, "message"=>$cMessage ));
}