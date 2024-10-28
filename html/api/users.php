<?php
/**
  * Fetch, create & update user accounts
  */
require_once( "config.php" );
require_once( "database.class.php" );
require_once( "user.class.php" );
require_once( "userRole.class.php" );
require_once( 'crossdomain.php' );

$cookieName = $config["sessionCookie"];
$request = array_merge( $_GET, $_POST );

$pDB = DatabaseFactory::Create( 
    "mysqli", 
    $config["db"]->host,
    $config["db"]->user,
    $config["db"]->password,
    $config["db"]->catalog );

$iUserId = User_IsLoggedIn( $pDB, $cookieName );

$pUser = User_Get( $pDB, $iUserId );
if( $pUser == NULL ) {
    //TODO: proper return values
    echo json_encode( array("success"=>false));
    die();
}

if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
    //returns data for currently logged in user
    if( $iUserId <= 0 ) {
        //TODO: proper return values
        $sError = "You must log in to take that action ({$iUserId})";
        die($sError);
    }

    //fetch existing user data
    //$pUser = User_Get( $pDB, $iUserId );
    
    //TODO: proper return values
    //ALSO: remove pw, etc from return values
    echo( json_encode( array( "success"=>true, "user"=>FormatUserData( $pUser ) )));
}

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    //create a new user

    $bFail = false;
    $sResult = array();

    // Validate the create request
    $bFail = ValidateRequest( $request, $sResult );

    // Attempt the create
    if ( $bFail == false )
    {
        $db_config = $config["db"];

        $pDB = DatabaseFactory::Create( 
            "mysqli", 
            $db_config->host,
            $db_config->user,
            $db_config->password,
            $db_config->catalog
        );

        $cFirstName = @$request['signup_first_name'];
        $cLastName = @$request['signup_last_name'];

        $iUserId = User_Create( 
            $pDB, 
            $request['signup_email'], 
            $request['signup_password'], 
            $cFirstName, 
            $cLastName 
        );

        $sResult['userid'] = $iUserId;
        
        if ( $iUserId <= 0 ) 
        {
            $bFail = true;
            $sResult['signup_email'] = 'Email already registered';
        }
        else
        {
            // Add the minimum roles
            UserRole_AddRole( $pDB, $iUserId, 6, 0, 4, $cExpires );
            $cSession = "";
            $iResult = User_Login( 
                $pDB, 
                $request['signup_email'], 
                $request['signup_password'], 
                $cSession 
            );

            if ( $iResult > 0 )
            {
                $host = ".{$config["host"]}";
                setcookie($config["sessionCookie"], $cSession, 86400 + time(0),'/',$host );
            }
        }
        
        $sResult["user"] = FormatUserData( $pUser );
    }
    
    if ( $bFail )
        $sResult['success'] = false;
    else
        $sResult['success'] = true;

    //TODO: Create proper return to client
    echo json_encode($sResult);
}

if ( $_SERVER['REQUEST_METHOD'] == 'PUT' ) {
    //update an existing user
    if( $iUserId <= 0 ) {
        //TODO: proper return values
        $sError = "You must log in to take that action ({$iUserId})";
        die($sError);
    }
    
    $bFail = false;
    $sResult = array();

    // Validate the create request
    $bFail = ValidateRequest( $request,  $sResult );

    // Attempt the create
    if ( $bFail == false )
    {
        //update existing user data
        $pUser->field["user_first_name"]    = $request["signup_first_name"];
        //$pUser->field["user_login"]         = $request["signup_email"];
        $pUser->field["user_last_name"]     = $request["signup_last_name"];

        //TODO: changes based on data from client
        //TODO: proper return values
        //ALSO: remove pw, etc from return values
        $pUser->Save();
        
        $sResult["user"] = FormatUserData( $pUser );
    }
    
    if ( $bFail )
        $sResult['success'] = false;
    else
        $sResult['success'] = true;

    //TODO: Create proper return to client
    echo json_encode($sResult);
}

function ValidateRequest( $request, &$errors ) {
    $bFail = false;
    
    if ( strlen(@$request['signup_password']) < 4 )
    {
        $sResult['signup_password'] = 'Must be 4 or more characters'; 
        $bFail = true;
    }
    
    //other password validation needed?
    
    if ( @$request['signup_reenter_password'] != @$request['signup_password'] )
    {
        $sResult['signup_reenter_password'] = "Doesn't match password";
        $bFail = true;
    }

    if ( !User_ValidateEmail( @$request["signup_email"] ) )
    {
        $sResult['signup_email'] = 'Invalid email address';
        $bFail = true;
    } 

    $cFirstName = @$request['signup_first_name'];
    $cLastName = @$request['signup_last_name'];

    if ( strlen($cFirstName) < 2 )
    {
        $sResult['signup_first_name'] = 'Invalid First Name';
        $bFail = true;
    }
 
    if ( strlen($cLastName) < 2 )
    {
         $sResult['signup_last_name'] = 'Invalid Last Name';
         $bFail = true;
    }
    
    return $bFail;
}

function FormatUserData( $pUser ) {
    $return = array();

    $return["id"] = $pUser->field["user_id"];
    $return["login"] = $pUser->field["user_login"];
    $return["datecreated"] = $pUser->field["user_datecreated"];
    $return["status"] = $pUser->field["user_status"];
    $return["first_name"] = $pUser->field["user_first_name"];
    $return["last_name"] = $pUser->field["user_last_name"];

    return $return;
}