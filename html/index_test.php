<?php

require_once( "config.php" );
require_once( "database.php" );
require_once( "userRole.php" );
require_once( "organization.php" );
require_once( "campaign.php" );



$iUserId=47;
$iOrgId=7;
$iRoleId=1;
$iCampId=NULL;
$sExpireDate = null;
$iCampaignId=16;
$iRoleId=1;
$cExpires=NULL;


echo "start<br/>";
$db_config = $config["db"];

$mysqli = DatabaseFactory::Create( 
    "mysqli", 
    $db_config->host,
    $db_config->user,
    $db_config->password,
    $db_config->catalog );

echo "mysqli db:<br>";
print_r($mysqli);

//UserRole_AddRole( $mysqli, $iUserId, $iOrgId, $iCampaignId, $iRoleId, $cExpires );


if( Organization_CanUserCreateCampaign( $mysqli, $iOrgId, $iUserId ) ){
    echo "<br>can create";
}else{
    echo "<br>cannot create";
}

$cExpires;
if( UserRole_CanUseCourse( $mysqli, $iUserId, $iCampaignId, $cExpires )) {
    echo "<br>can use";
}else{
    echo "<br>cannot use";
}

echo "<br>{$cExpires}";

//$list = Organization_GenerateListForUser( $mysqli, $iUserId );
$list = Campaign_GenerateListForUser( $mysqli, $iUserId );

echo "<br>list:<br>";
print_r( $list );

die("done");

/*
$name = "testOrg".time();
$oid = Organization_Create( $mysqli, $name );

if( $oid < 0)
{
    echo "<br>failed to create new org {$oid}";
}
else
{
    echo "oid: {$oid}<br>New user:<br>";
    $user = new Organization($mysqli);
    $user->Retrieve($oid);
    print_r( $user );
}
*/

//$role = new UserRole( $mysqli );
//$role->Add( $iUserId, $iOrgId, $iRoleId, $iCampId, $sExpireDate  );

//$role->Retrieve( $iUserId );
//print_r( $role );

/*
$campaign = new Campaign( $mysqli );
$result = $campaign->Add( 1, "Hellohello3", $iUserId, $iOrgId );

if($result < 1 ) {
    echo "<h1>fail fail</h1>";
}

echo "Result:<br>";
print_r( $campaign );
*/

/*
$name = "testName".time();
$uid = User_Create( $mysqli, $name, "testPw", "testFN", "testLN" );

if( $uid < 0)
{
    echo "<br>failed to create new user {$uid}";
}
else
{
    echo "uid: {$uid}<br>New user:<br>";
    $user = new User($mysqli);
    $user->Retrieve($uid);
    print_r( $user );
}

$cSessionId = NULL;
$loginResult = User_Login( $mysqli, $name, "testPw", $cSessionId );

if( $loginResult < 0 )
{
    echo "could not authenticate<br/>";
}
else
{
    echo "got user session {$cSessionId}<br>Attempt to validate:<br>";
    $result = UserSession_Validate( $mysqli, $cSessionId );
    
    echo "validate result: {$result}<br>";
    
}

UserSession_Touch( $mysqli, $cSessionId );

if( UserSession_Close( $mysqli, $cSessionId ) )
{
    echo "Session closed<br>Validate again:<br>";
    $result = UserSession_Validate( $mysqli, $cSessionId );

    echo "second validate result: {$result}<br>";
}
else
{
    echo "failed to close<br>";
}
*/

/*
$dsn="Server={$db_config->host};Database={$db_config->catalog};";
$odbc = new ODBCConnection($dsn,$db_config->user,$db_config->password);
echo "odbc db:<br>";
print_r($odbc);
*/
echo "<br>done";