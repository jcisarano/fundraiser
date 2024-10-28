<?php
require_once( "config.php" );
require_once( "database.class.php" );
require_once( "user.class.php" );
require_once( "campaign.class.php" );
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
$iOrgId = @$request["org_id"];

/*
$iUserId = 2;
//$iOrgId = 12;
$request["camp_name"] = "CHHS Band Trip to NYC";
$_SERVER['REQUEST_METHOD'] = 'PUT';
$request["campaign_id"] = 3;
*/

if( $iUserId <= 0 ) {
    $cError = "You must log in to take that action ({$iUserId})";
    echo json_encode( array( "success"=>false, "message"=>$cError ));
    die();
}

$bSuccess = false;
$pFinal = array();
if ( $_SERVER['REQUEST_METHOD'] == 'GET' )
{
    //return list of valid campaigns for this user
    $pList = Campaign_GenerateListForUser( $pDB, $iUserId, $iOrgId );
    
    foreach( $pList as $cc) {
        $pFinal[] = FormatCamp( $cc );
    }
    $bSuccess = true;
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    //create a new campaign with this user as the owner
    $cCampaignType = isset( $request["org_id"] ) ? $request["org_id"] : "ORGANIZATION";
    $cCampaignName = @$request["camp_name"];

    $iCampaignId = Campaign_Create( 
        $pDB, 
        $iUserId, 
        $iOrgId, 
        $cCampaignType, 
        $cCampaignName 
    );

    if( $iCampaignId == Campaign::INTERNAL ) {
        $sError = "Unable to create campaign";
        die($sError);
    } else if ( $iCampaignId == Campaign::INVALIDTYPE ) {
        $sError = "Unrecognized type";
        die($sError);
    } else if ( $iCampaignId == Campaign::TOOMANYOFTYPE ) {
        $sError = "You have reached your limit on this type of campaign";
        die($sError);
    } else if ( $iCampaignId == Campaign::NOPERMISSION ) {
        $sError = "You do not have permission to create a campaign for this organization";
        die($sError);
    } else if ( $iCampaignId == Campaign::CREATEFAIL ) {
        $sError = "Unable to create campaign called '{$cCampaignName}'";
        die($sError);
    } else if ( $iCampaignId < 0 ) {
        $sError = "Internal error encountered ({$iCampaignId})";
        error_log( "createCampaign: Unhandled error encountered creating campaign, error:{$iCampaignId}" );
        die($sError);
    } else {
        $pCampaign = Campaign_Get($pDB,$iCampaignId);
    }

    $bSuccess = true;
    $pCampaign = FormatCamp( $pCampaign->field );
    $pFinal[] = $pCampaign;
}

if ( $_SERVER['REQUEST_METHOD'] == 'PUT' )
{
    //edit an existing campaign for this user
    $iCampaignId = @$request["campaign_id"];
    $pCampaign = Campaign_Get( $pDB, $iCampaignId );
    $orgs = array();

    $perms = array("ALLACCESS","CREATECAMPAIGN");
    $campaigns = Campaign_FetchForUser( $pDB, $iUserId, $iCampaignId, $perms );

    //also allow users with perms at the organization level if the first test fails
    //TODO: can this check be rolled into the one above?
    if( count( $campaigns ) < 1 ) {
        $orgs = Organization_FetchForUser( 
            $pDB, 
            $iUserId, 
            $pCampaign->field["camp_orgid"], 
            $perms 
        );
    }

    if( count( $campaigns ) > 0 || count( $orgs) > 0 ) {
        //make changes here
        $pCampaign->field["camp_startdate"] = date("Y-m-d h:i:s",time());
        $pCampaign->Save();

        $pCampaign = FormatCamp( $pCampaign->field );
        $pFinal[] = $pCampaign;
        $bSuccess = true;
    }
}

echo json_encode( array( "success"=>$bSuccess, "results"=>$pFinal ));

function FormatCamp( $source ) {
    $dest = array();

    $dest["id"]         = @$source["camp_id"];
    $dest["type"]       = @$source["camp_type"];
    $dest["org_id"]     = @$source["camp_orgid"];
    $dest["creatorid"]  = @$source["camp_creatorid"];
    $dest["name"]       = @$source["camp_name"];
    $dest["status"]     = @$source["camp_status"];
    $dest["refname"]    = @$source["camp_refname"];
    $dest["datecreated"]    = @$source["camp_datecreated"];
    $dest["startdate"]      = @$source["camp_startdate"];
    $dest["enddate"]    = @$source["camp_enddate"];
    $dest["cost"]       = @$source["camp_cost"];
    $dest["org_name"]   = @$source["org_name"];

    return $dest;
}