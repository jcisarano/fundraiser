<?php
require_once( "baseConnect.class.php" );

class Raffle extends BaseConnect {
    const INTERNAL          = -1;
    const CREATEFAILED      = -2;

    var $field = NULL;
    
    function __construct( $pDB = NULL ) {
        parent::__construct( $pDB );
    }
    
    function Add( $cRaffleName, $iCreatorId, $cDescription, $cStartDate, $cEndDate ) {
        $cQuery = "INSERT INTO Raffle (
            name,createdate,creator_id,description,startdate,enddate ) VALUES (
            '%s',NOW(),%u,'%s','%s','%s')";
            
        $pResult = $this->Query(
            $cQuery,
            $cRaffleName,
            $iCreatorId,
            $cDescription,
            $cStartDate,
            $cEndDate
        );
        
        $iRaffleId = $this->m_pDBConnection->insert_id();
        
        $this->m_pDBConnection->free_result( $pResult );
        if( $iRaffleId < 1 )
            return false;
        
        $this->Retrieve( $iRaffleId );
        
        return $iRaffleId;
    }
    
    function Retrieve( $iRaffleId) {
        $pResult = NULL;
        $cQuery = "SELECT * FROM Raffle WHERE id=%u";
        $pResult = $this->Query( $cQuery, $iRaffleId );

        if ( $pResult == null ) {
            $this->field = null;
            return false;
        }

        if( $row = $this->m_pDBConnection->fetch_row( $pResult ) ) {
            foreach( $row as $key=>$value ) {
                $this->field[$key] = $value;
            }
        }

        $this->m_pDBConnection->free_result($pResult);

        return true;
    }
    
    function Save() {
        if ( $this->field == null || intval($this->field['id']) == 0 )
            return false;

        $cQuery = "UPDATE Raffle SET name='%s', description='%s', startdate='%s', 
            enddate='%s', winner_id=%u WHERE id=%u";

        $pResult = $this->Query( 
            $cQuery, 
            $this->field["name"], 
            $this->field["description"], 
            $this->field["startdate"], 
            $this->field["enddate"], 
            $this->field["winner_id"], 
            $this->field["id"]
        );

        $iCount = $this->m_pDBConnection->affected_rows();

        if( $pResult != NULL )
            $this->m_pDBConnection->free_result( $pResult );

        return $iCount > 0;
    }
}

function Raffle_Create( $pDB, $cRaffleName, $iCreatorId, $cDescription, 
        $cStartDate, $cEndDate ) {

    $pRaffle = new Raffle( $pDB );

    if( $cRaffleName == "" )
        return Raffle::CREATEFAILED;
        
    $iRaffleId = $pRaffle->Add( $cRaffleName, $iCreatorId, $cDescription, 
        $cStartDate, $cEndDate );

    if( $iRaffleId < 1 )
        return Raffle::CREATEFAILED;

    return $iRaffleId;
}

function Raffle_Get( $pDB, $iRaffleId ) {
    $pRaffle = new Raffle( $pDB );

    if( $pRaffle->Retrieve( $iRaffleId ))
        return $pRaffle;

    return NULL;
}

function Raffle_FetchAll( $pDB, $bActiveOnly, $iStart=0, $iCount=20 ) {
    $cQuery = "SELECT * FROM Raffle";
    
    if( $bActiveOnly ) {
        $cQuery .= " WHERE enddate > NOW()";
    }
    
    $cQuery .= " LIMIT %u,%u";
    
    $pResult = $pDB->execute_query( $cQuery, $iStart, $iCount );

    $raffles = array();
    while( $row = $pDB->fetch_row( $pResult ) ) {
        $raffles[] = $row;
    }

    return $raffles;
}

function Raffle_AddTicket( $pDB, $iUserId, $iRaffleId ) {
    $cQuery = "INSERT INTO RaffleUsers 
        (user_id,raffle_id,entered) VALUES 
        (%u,%u,NOW())";
    
    $pResult = $pDB->execute_query( $cQuery, $iUserId, $iRaffleId );
    $iCount = $pDB->affected_rows();

    $pDB->free_result($pResult);

    return $iCount > 0;
}


function Raffle_FetchRafflesForUser( $pDB, $iUserId ) {
    $cQuery = "SELECT r.* 
        FROM Raffle r, RaffleUsers ru 
        WHERE ru.user_id=%u AND ru.raffle_id=r.id";
    $pResult = $pDB->execute_query( $cQuery, $iUserId );

    $raffles = array();
    while( $row = $pDB->fetch_row( $pResult ) ) {
        $raffles[] = $row;
    }

    return $raffles;
}


function Raffle_SelectWinner( $pDB, $iRaffleId ) {
    $cQuery = "SELECT u.* 
        FROM RaffleUsers ru 
        WHERE ru.raffle_id=%u AND ru.user_id=u.userid 
        ORDER BY RAND() LIMIT 1";
    $pResult = $pDB->execute_query( $cQuery, $iRaffleId );

    $winner = $pDB->fetch_row( $pResult );
    $pDB->free_result( $pResult );
    
    if( $winner == NULL )
        return false;

    $cQuery = "UPDATE Raffle SET winner_id=%u WHERE id=%u";
    $pResult = $pDB->execute_query( $cQuery, $winner["user_id"], $iRaffleId );
    $pDB->free_result( $pResult );

    return $winner;

}

/*
DROP TABLE IF EXISTS `Raffle`;
CREATE TABLE `Raffle` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `createdate` datetime DEFAULT NULL,
  `creator_id` int(10) unsigned NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT 'Y',
  `startdate` datetime DEFAULT NULL,
  `enddate` datetime DEFAULT NULL,
  `winner_id` bigint(20) unsigned NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `RaffleUsers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `raffle_id` bigint(20) unsigned NOT NULL,
  `entered` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
*/