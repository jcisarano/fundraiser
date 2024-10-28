<?php

require_once( "config.php" );
require_once( "database.class.php" );
//require_once( "userRole.class.php" );
//require_once( "user.class.php" );
//require_once( "organization.class.php" );
//require_once( "campaign.class.php" );
require_once( "salesItem.class.php" );
//require_once( "email.class.php" );




$iUserId=2;
$iOrgId=7;
$iRoleId=1;
$iCampId=2;
$sExpireDate = null;
$iCampaignId=16;
$iRoleId=1;
$cExpires=NULL;

$email = "cisarano2@gmail.com";
$pw = "abc123";
$cFirstName = "Jason2";
$cLastName = "Cisarano2";

echo "start<br/>";
$db_config = $config["db"];

$pDB = DatabaseFactory::Create( 
    "mysqli", 
    $db_config->host,
    $db_config->user,
    $db_config->password,
    $db_config->catalog );

$iItemId = 3;
$iTransactionId = 1;
$iItemType=3;
SalesItem_RecordSaleForUser( $pDB, $iUserId, $iCampId, $iItemId, $iTransactionId );
$items = SalesItem_FetchRecords( $pDB, $iItemType, $iUserId, $iCampId );
print_r( $items );
/*
$mailData = new EmailData;

$mailData->email = "cisarano@gmail.com";
$mailData->firstName = "Jason";
$mailData->lastName = "Cisarano";
$mailData->zip = "27510";
$mailData->replyEmail = "no-reply@mmoventures.com";
$mailData->replyName = "No Reply";
$mailData->apiKey = $config["email"]->mandrillKey;


$wrapper = "This is another test wrapper header<br> %s <br>This is another test wrapper footer";

$path = "./emails/testEmail.txt";

$body = Email::BuildEmail( $path, $wrapper );

print_r($body);

Email::MandrillInstantSend( $mysqli, $mailData, $body["body"], $body["body"], $body["subject"] );
*/
/*
$itemId = SalesItem_Create( $mysqli, $iUserId, 8, "SERVICE", "LAWN MOWING" );
$item = SalesItem_Get( $mysqli, $itemId );

echo "item:<br>";
print_r( $item );
*/
    
    
/*
    $iUserId = User_Create( $mysqli, $email, $pw, $cFirstName, $cLastName );
$sResult['userid'] = $iUserId;

if ( $iUserId <= 0 ) 
{
    $bFail = true;
    echo 'Email already registered';
}
else
{
    // Add the minimum roles
    @UserRole_AddRole( $mysqli, $iUserId, USER::PUBLIC_ORGANIZATION, 0, User::ROLE_MONITOR, $cExpires );
    $cSession = "";
    @$iResult = User_Login( $mysqli, $email, $pw, $cSession );
    if ( $iResult > 0 )
    {
        echo "successful login user {$iUserId}";
    }
    else
    {
        echo "login fail {$iUserId}";
    }
}
*/

/*
$orgId = Organization_Create( $mysqli, "MY TEST ORG" );

echo "Org Id:{$orgId}<br>";

UserRole_AddRole( $mysqli, $iUserId, $orgId, 0, User::ROLE_ADMINISTRATOR, NULL );

$iCampaignId = Campaign_Create( $mysqli, $iUserId, $orgId, 0, "This is test campaign" );
echo "cid:{$iCampaignId}<br>";


echo "this is campaign:";
$campaign = Campaign_Get( $mysqli, 1 );
print_r( $campaign );

$list = Campaign_GenerateListForUser( $mysqli, $iUserId );

echo "<br>All:<br>";
print_r( $list );
$list = Campaign_GenerateListForUser( $mysqli, $iUserId, 8 );

echo "<br>By id:<br>";
print_r( $list );


$list = Organization_GenerateListForUser( $mysqli, $iUserId );
echo "<br>first list:<br>";
print_r( $list );

//$list = Organization_FetchForUser( $mysqli, $iUserId, $iOrgId=NULL, $perms=NULL );
$list = Organization_FetchForUser( $mysqli, $iUserId, USER::PUBLIC_ORGANIZATION, 'ALLACCESS' );
echo "<br>second list:<br>";
print_r( $list );

if( Organization_CanUserEditCampaign( $mysqli, USER::PUBLIC_ORGANIZATION, $iUserId ) )
//if(  Organization_CanUserCreateCampaign( $mysqli, 22, $iUserId ))
    echo "<br>create: yes yes yes";
else
    echo "<br>no non no";
*/

echo "<br>done";