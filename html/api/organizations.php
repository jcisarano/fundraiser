<?php
require_once( "config.php" );
require_once( "database.class.php" );
require_once( "user.class.php" );
require_once( "organization.class.php" );
require_once( "userRole.class.php" );

$cookieName = $config["sessionCookie"];
$request = array_merge( $_GET, $_POST );

$db = DatabaseFactory::Create( 
    "mysqli", 
    $config["db"]->host,
    $config["db"]->user,
    $config["db"]->password,
    $config["db"]->catalog );

$iUserId = User_IsLoggedIn( $db, $cookieName );

/*
$iUserId = 2;
$_SERVER['REQUEST_METHOD'] = 'PUT';
$request["org_name"] = "THIS IS MY ORG";
$request["org_id"] = 15;
*/

if( $iUserId <= 0 ) {
    $cError = "You must log in to take that action ({$iUserId})";
    echo json_encode( array( "success"=>false, "message"=>$cError ));
    die();
}

if ( $_SERVER['REQUEST_METHOD'] == 'GET' )
{
    //fetch all organizations for the user

    $orgIds=$perms=NULL;
    if( isset( $request["org_ids"] ) )
        $orgIds = explode( ",", $request["org_ids"] );
    if( isset( $request["perms"] ))
        $perms = explode(",",$request["perms"]);

    //fetch orgs for user
    $orgs = Organization_FetchForUser( $db, $iUserId, $orgIds, $perms );
    
    $final = array();
    $keys = array();
    $iIndex = 0;
    foreach( $orgs as $oo ) {
        if( !isset( $keys[ $oo["org_id"] ] ) ) {
            $keys[$oo["org_id"]] = $iIndex;
            $final[$iIndex] = $oo;
            $iIndex++;
        }
        
        //TODO: This add isn't working quite right - it messes up the json array sent
        //  to the client. However, perms are not currently used on the client
        //  Could the problem be the 2d array added here?
        //$final[$key[$oo["org_id"]]]["perms"][$oo["perm_id"]] = $oo["perm_name"];
    }

    echo json_encode( array( "success"=>true, "results"=>$final ));
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    //create a new organization

    $cOrgName = @$request["org_name"];
    $iOrgId = Organization_Create( $db, $cOrgName );
    
    if( $iOrgId == Organization::INTERNAL ) {
        $sError = "Organizations: Internal error";
        die($sError);
        //TODO: Handle return to client
    } else if ( $iOrgId == Organization::CREATEFAILED ) {
        $sError = "Organizations: Unable to create organization";
        die($sError);
        //TODO: Handle return to client
    } else if ( $iOrgId == Organization::INVALIDNAME ) {
        $sError = "Organizations: Invalid name given";
        die($sError);
        //TODO: Handle return to client
    } else if ( $iOrgId <= 0 ) {
        $sError = "Organizations: Unhandled internal error ({$iOrgId})";
        error_log($sError);
        die($sError);
        //TODO: Handle return to client
    } else {
        //create was successful, so set up return
        $org = Organization_Get( $db, $iOrgId );

        $role = UserRole_GetInfo( $db, "Administrator" );
        UserRole_AddRole( $db, $iUserId, $org->field["org_id"], 0, $role["role_id"] );

        $org = FormatOrg( $org );

        //TODO: Handle return to client
        echo json_encode( $org );
    }
}

if( $_SERVER["REQUEST_METHOD"] == "PUT" ) {
    //update existing organization
    
    $cOrgId = @$request["org_id"];
    $pOrg = Organization_Get( $db, $cOrgId );
    
    //TODO: make some changes here
    
    $bSuccess = $pOrg->Save();
    
    echo json_encode(array( "success"=>$bSuccess, "organization"=>FormatOrg( $pOrg )));
}

//
// Returns array with organization info that can be sent to the client
//
function FormatOrg( $source ) {
    $dest = array();

    $dest["org_id"]         = $source->field["org_id"];
    $dest["org_name"]       = $source->field["org_name"];
    $dest["org_fullname"]   = $source->field["org_fullname"];
    $dest["org_datejoined"] = $source->field["org_datejoined"];
    $dest["org_status"]     = $source->field["org_status"];
    $dest["address_field"]  = $source->address_field;
    $dest["phone_field"]    = $source->phone_field;

    return $dest;
}