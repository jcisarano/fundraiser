<?php
require_once( "config.php" );
require_once( "raffle.class.php" );
require_once( "database.class.php" );
require_once( "user.class.php" );
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
$iUserId = 2;


if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
    // fetch one or more existing raffles

    $pFinal = array();
    $bSuccess = false;

    $iRaffleId = isset($request["raffle_id"]) ? $request["raffle_id"] : NULL;
    if( $iRaffleId != NULL ) {
        $pRaffle = Raffle_Get( $pDB, $iRaffleId );

        if( $pRaffle != NULL ) {
            $bSuccess = true;
            $pFinal[] = $pRaffle->field;
        }

    } else {
        $iStart = isset($request["start"]) ? $request["start"] : NULL;
        $iCount = isset($request["count"]) ? $request["count"] : NULL;
        
        $bIsActive = isset($request["active_only"]) && $request["active_only"] > 0 ? true : false;
        
        $sRaffles = Raffle_FetchAll( $pDB, $bIsActive );
        if( count( $sRaffles ) > 0 ) {
            $bSuccess = true;
            foreach( $sRaffles as $rr ) {
                $pFinal[] = $rr;
            }
        }
    }
    
    echo json_encode( array( "success"=>$bSuccess,"raffle"=>$pFinal ));
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    if( $iUserId <= 0 ) {
        //TODO: proper return values
        echo json_encode( array("success"=>false,"message"=>"You must be logged in to take that action"));
        die();
    }

    // create new raffle
    $cRaffleName = isset($request["raffle_name"]) ? $request["raffle_name"] : NULL ;
    $cDescription = isset($request["raffle_desc"]) ? $request["raffle_desc"] : NULL;
    $cStartDate = isset($request["raffle_start"]) ? $request["raffle_start"] : NULL;
    $cEndDate = isset($request["raffle_end"]) ? $request["raffle_end"] : NULL;

    if( $cRaffleName == NULL || $cStartDate==NULL || $cEndDate == NULL ) {
        echo json_encode( 
            array("success"=>false,
            "message"=>"Incomplete data, unable to create raffle"));
        die();
    }

    $iRaffleId = Raffle_Create( $pDB, $cRaffleName, $iUserId, $cDescription, $cStartDate, $cEndDate );

    if( $iRaffleId < 1 ) {
        echo json_encode( array("success"=>false,"message"=>"Unable to create new raffle({$iRaffleId})"));
    } else {
        $pRaffle = Raffle_Get( $pDB, $iRaffleId );
        echo json_encode( array("success"=>true,"message"=>"Created new raffle","raffle"=>$pRaffle->field));
    }
}

if ( $_SERVER['REQUEST_METHOD'] == 'PUT' ) {
    // edit data for existing raffle
    $iRaffleId = isset($request["raffle_id"]) ? $request["raffle_id"] : NULL;
    $pRaffle = Raffle_Get( $pDB, $iRaffleId );

    if( $iRaffleId != NULL && $pRaffle != NULL ) {

        $cRaffleName = isset($request["raffle_name"]) ? $request["raffle_name"] : NULL ;
        $cDescription = isset($request["raffle_desc"]) ? $request["raffle_desc"] : NULL;
        $cStartDate = isset($request["raffle_start"]) ? $request["raffle_start"] : NULL;
        $cEndDate = isset($request["raffle_end"]) ? $request["raffle_end"] : NULL;
        $iWinnerId = isset($request["winner_id"]) ? $request["winner_id"] : NULL;

        if( $cRaffleName == NULL || $cStartDate==NULL || $cEndDate == NULL ) {
            echo json_encode( array(
                "success"=>false,
                "message"=>"Incomplete data, unable to update raffle"));
            die();
        }

        $pRaffle->field["name"] = $cRaffleName;
        $pRaffle->field["description"] = $cDescription;
        $pRaffle->field["startdate"] = $cStartDate;
        $pRaffle->field["enddate"] = $cEndDate;
        $pRaffle->field["winner_id"] = $iWinnerId;

        $bSuccess = $pRaffle->Save();

        echo json_encode( array("success"=>$bSuccess,"raffle"=>$pRaffle->field ));

    } else {
        echo json_encode( array(
            "success"=>false,
            "message"=>"Invalid raffle id, unable to update raffle"));
    }
}

//$pUser = User_Get( $pDB, $iUserId );
