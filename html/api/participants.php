<?php
/**
  * Fetch or add participants for a campaign
  *
  */
require_once( "config.php" );
require_once( "database.class.php" );
require_once( "user.class.php" );
require_once( "campaign.class.php" );
require_once( "email.class.php" );
require_once( "userRole.class.php" );
require_once( "keycode.class.php" );
require_once( 'crossdomain.php' );

$cookieName = $config["sessionCookie"];
$request = array_merge( $_GET, $_POST );

$pDB = DatabaseFactory::Create( 
    "mysqli", 
    $config["db"]->host,
    $config["db"]->user,
    $config["db"]->password,
    $config["db"]->catalog );

$iCampaignId = isset( $request["camp_id"] ) ? $request["camp_id"] : NULL;
$iOrgId = isset( $request["org_id"] ) ? $request["org_id"] : NULL;
$iRoleId = isset( $request["role_id"] ) ? $request["role_id"] : NULL;

if ( $_SERVER['REQUEST_METHOD'] == 'GET' )
{
    //fetch participants for a campaign
    $participants = Campaign_FetchParticipants( $pDB, $iCampaignId );
    
    //TODO: proper return
    echo json_encode( $participants );
    die();
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    //set one or more participants for a campaign
    $iUserId = User_IsLoggedIn( $pDB, $cookieName );
    $orgs = array();

    if( $iUserId <= 0 ) {
        echo json_encode( array( "success"=>false,
            "messge"=>"You must log in to take that action ({$iUserId})" ));
        die();
    }

    $pCampaign = Campaign_Get( $pDB, $iCampaignId );
    if( $pCampaign == NULL ) {
        echo json_encode( array( "success"=>false,
            "messge"=>"Invalid campaign ({$iCampaignId})" ));
        die();
    }
    
    //validate campaign perms for the logged-in user
    $perms = array('ALLACCESS', 'EDITCAMPAIGN', 'CREATECAMPAIGN' );
    $permsFound = Campaign_FetchForUser( $pDB, $iUserId, $iCampaignId, $perms );
    
    if( count( $permsFound ) < 1 ) {
        $orgs = array(0); //includes admin perms
        $orgs[] = $iOrgId;
        $permsFound = Organization_FetchForUser( 
            $pDB, 
            $iUserId, 
            $orgs, 
            $perms 
        );
    }
    
    if( count( $permsFound ) < 1 ) {
        echo json_encode( array( "success"=>false,
            "messge"=>"You do not have permission to take this action." ));
        die();
    }

    $sParticipantRole = UserRole_GetInfo( $pDB, "PARTICIPANT" );

    //add new users, but use any existing ones where possible
    @$sUsersToAdd = $_POST["usersToAdd"];

    $partEmails = explode( ",", $sUsersToAdd );
    
    $invalidEmails = array();
    foreach( $partEmails as $cUserName ) {
        $cUserName = trim( $cUserName );
        
        if( !User_ValidateEmail( $cUserName )) {
            $invalidEmails[] = $cUserName;
            continue;
        }

        $cLink = $config["host"];
        
        $tmpPw = AddParticipants_GeneratePassword();
        $wrapper = Email::BuildEmail( Email::WRAPPER_FILE, "" );
        $iParticipantId = User_Create( $pDB, $cUserName, $tmpPw, $cFirstName, $cLastName );
        if( $iParticipantId > 0 ) {
            $email = Email::BuildEmail( 
                Email::EMAIL_FILE_PATH . "newParticipant.txt", 
                $wrapper["body"], 
                $cFirstName,
                $pCampaign->field["camp_name"],
                $cLink,
                $cUserName,
                $tmpPw
            );
        } else {
            //create failed, check if user exists
            $existing = User_Get( $pDB, $cUserName );

            if( $existing ) {
                $iParticipantId = $existing->field["user_id"];
                $email = Email::BuildEmail( 
                    Email::EMAIL_FILE_PATH . "existingParticipant.txt", 
                    $wrapper["body"], 
                    $cFirstName,
                    $pCampaign->field["camp_name"],
                    $cLink
                );
            } else {
                $invalidEmails[] = $cUserName;
            }
        }

        $cPartCampName = "My {$pCampaign->field["camp_name"]}";
        $iCampaignId = Campaign_CreateNoPermCheck( $pDB, $iUserId, $iOrgId, "PARTICIPANT", $cPartCampName );
        if( $iCampaignId > 0 ) {
            UserRole_AddRole(
                $pDB,
                $iParticipantId,
                $iOrgId,
                $iCampaignId,
                $sParticipantRole["role_id"]
            );
        }

        $mailData = new EmailData;
        $mailData->email = $cUserName;
        $mailData->firstName = $cFirstName;
        $mailData->lastName = $cLastName;
        $mailData->zip = "";
        $mailData->replyName = "No reply";
        $mailData->replyEmail = "no-reply@".$config["host"];
        $mailData->apiKey = $config["email"]->mandrillKey;
        
        Email::MandrillInstantSend( 
            $pDB, 
            $mailData, 
            $email["body"], 
            $email["body"], 
            $email["subject"] 
        );
    }

    echo json_encode( array( "success"=>true, "invalidEmails"=>$invalidEmails ));
}

if ( $_SERVER['REQUEST_METHOD'] == 'PUT' ) {
    //modify a user's role in a campaign

    $bSuccess = false;
    $cMessage = "Unable to change user role";
    if( $iRoleId != NULL ) {
        $cExpires = isset( $request["expire_date"] ) ? $request["expire_date"] : NULL;
        $bSuccess = UserRole_EditRole( 
            $pDB, 
            $iTargetUserId, 
            $iOrgId, 
            $iCampaignId, 
            $iRoleId, 
            $cExpires 
        );

        if( $bSuccess )
            $cMessage = "User's role has been updated";
        
    } else {
        $cMessage = "Unable to change user role: Invalid role given ({$iRoleId})";
    }
    
    echo json_encode( array( 
        "success"=>$bSuccess, 
        "message"=>$cMessage
    ));
}
if ( $_SERVER['REQUEST_METHOD'] == 'DELETE' ) {
    //remove a user's role in a campaign
    $cMessage = "Unable to remove that user's role";
    $bSuccess = UserRole_DeleteRole(
        $pDB, 
        $iTargetUserId, 
        $iOrgId, 
        $iCampaignId, 
        $iRoleId 
    );

    if( $bSuccess )
        $cMessage = "Removed user's role in this campaign";

    echo json_encode( array( 
        "success"=>$bSuccess, 
        "message"=>$cMessage
    ));
}

function AddParticipants_GeneratePassword( $length = 8 ) {
    return Keycode::GenerateCode(1,$length);
}