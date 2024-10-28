<?php
require_once( "config.php" );
require_once( "database.class.php" );
require_once( "user.class.php" );
require_once( "campaign.class.php" );
require_once( "salesItem.class.php" );
require_once( "crossdomain.php" );

$cookieName = $config["sessionCookie"];
$request = array_merge( $_GET, $_POST );

$pDB = DatabaseFactory::Create( 
    "mysqli", 
    $config["db"]->host,
    $config["db"]->user,
    $config["db"]->password,
    $config["db"]->catalog );

$iCampaignId = @$request["camp_id"];
$bSuccess = false;
$pFinal = array();
$cMessage = "";

if ( $_SERVER['REQUEST_METHOD'] == 'GET' )
{
    //get items for a campaign
    $bSuccess = true;

    if( $iCampaignId != NULL ) {
        $pFinal = SalesItem_FetchCampaignItems( $pDB, $iCampaignId );
    }
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    //add a new item
    $iUserId = User_IsLoggedIn( $pDB, $cookieName );
    if( $iUserId <= 0 ) {
        //TODO: proper return values
        $sError = "You must log in to take that action ({$iUserId})";
        die($sError);
    }

    $iOrgId = @$request["org_id"];
    $permsNeeded = array("ALLACCESS","CREATECAMPAIGN","EDITCAMPAIGN");
    $permsFound = Campaign_FetchForUser( $pDB, $iUserId, $iCampaignId, $perms );

    //also allow users with perms at the organization level if the first test fails
    //TODO: can this check be rolled into the one above?
    if( count( $permsFound ) < 1 ) {
        $orgs = array(0); //start with admin level
        if( $iOrgId ) {
            $orgs[] = $iOrgId;
        }

        $permsFound = Organization_FetchForUser( 
            $pDB, 
            $iUserId, 
            $orgs, 
            $perms 
        );
    }

    if( count( $permsFound ) > 0 ) {
        $cItemType = @$request["item_type"];
        $cItemName = @$request["item_name"];

        $iItemId = SalesItem_Create( $pDB, $iUserId, $iOrgId, $iCampaignId, $cItemType, $cItemName );
        
        $cError = "";
        if( $iItemId == SalesItem::INTERNAL ) {
            $cError = "Unable to create item";
        } else if( $iItemId == SalesItem::NOPERMISSION ) {
            $cError = "You do not have permission to take that action";
        } else if( $iItemId == SalesItem::INVALID_TYPE ) {
            $cError = "Invalid item type given";
        } else if( $iItemId == SalesItem::INVALID_DATA ) {
            $cError = "Invalid item information given";
        } else if( $iItemId < 0 ) {
            $cError = "Internal error encountered ({$iItemId})";
            error_log( "Item create: Unhandled error encountered creating item, error:{$iItemId}" );
        }
    } else {
        $cError = "You do not have permission to take that action";
    }

    if( $cError == "" ) {
        $bSuccess = true;
        $pItem = SalesItem_Get( $pDB, $iItemId );
        $pFinal[] = FormatItem( $pItem->field );
    } else {
        $cMessage = $cError;
    }
}

if ( $_SERVER['REQUEST_METHOD'] == 'PUT' ) {
    //update an existing item

    $iUserId = User_IsLoggedIn( $pDB, $cookieName );
    if( $iUserId <= 0 ) {
        //TODO: proper return values
        $sError = "You must log in to take that action ({$iUserId})";
        die($sError);
    }

    
    $iItemId = @$request["item_id"];
    $pItem = SalesItem_Get( $pDB, $iItemId );

    $perms = array("ALLACCESS","CREATECAMPAIGN","EDITCAMPAIGN");
    $permsFound = Campaign_FetchForUser( $pDB, $iUserId, $pItem->field["item_campaignid"], $perms );

    //also allow users with perms at the organization level if the first test fails
    //TODO: can this check be rolled into the one above?
    if( count( $permsFound ) < 1 ) {
        $orgs = array(0);
        $orgs[] = $pItem->field["item_orgid"];
        $permsFound = Organization_FetchForUser( 
            $pDB,
            $iUserId,
            $orgs,
            $perms
        );
    }

    if( count( $permsFound ) > 0 ) {
        //make changes here

        $cItemType      = @$request["item_type"];
        $cItemName      = @$request["item_name"];
        $iOrgId         = @$request["org_id"];
        $iCampaignId    = @$request["campaign_id"];
        $cItemStatus    = @$request["item_status"];
        $cRefname       = @$request["item_refname"];
        $cUrl           = @$request["item_url"];
        $cStartDate     = @$request["item_startdate"];
        $cEndDate       = @$request["item_enddate"];
        $fCost          = @$request["item_cost"];

        if( $cItemName == "" || $iOrgId == NULL || $iCampaignId == NULL ) {
            $cMessage = "Unable to update item, invalid data given";
        } else {
            $pItem->field["item_name"] = $cItemName;
            $pItem->field["item_type"] = $cItemType;
            $pItem->field["item_orgid"] = $iOrgId;
            $pItem->field["item_campaignid"] = $iCampaignId;
            $pItem->field["item_status"] = $cItemStatus;
            $pItem->field["item_refname"] = $cRefname;
            $pItem->field["item_url"] = $cUrl;
            $pItem->field["item_startdate"] = $cStartDate;
            $pItem->field["item_enddate"] = $cEndDate;
            $pItem->field["item_cost"] = $fCost;
            
            $pItem->Save();
            
            $pFinal[] = FormatItem( $pItem->field );
            $bSuccess = true;
        }
    }
}

echo json_encode( array( "success"=>$bSuccess, "results"=>$pFinal, "message"=>$cMessage ));

function FormatItem( $source ) {
    $dest = array();

    $dest["id"] = $source["item_id"];
    $dest["type"] = $source["item_type"];
    $dest["orgid"] = $source["item_orgid"];
    $dest["campaignid"] = $source["item_campaignid"];
    $dest["creatorid"] = $source["item_creatorid"];
    $dest["name"] = $source["item_name"];
    $dest["status"] = $source["item_status"];
    $dest["refname"] = $source["item_refname"];
    $dest["url"] = $source["item_url"];
    $dest["datecreated"] = $source["item_datecreated"];
    $dest["startdate"] = $source["item_startdate"];
    $dest["enddate"] = $source["item_enddate"];
    $dest["cost"] = $source["item_cost"];

    return $dest;
}