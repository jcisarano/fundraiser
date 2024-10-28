<?php

/*
DROP TABLE IF EXISTS `Keycode`;
CREATE TABLE `Keycode` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `max_uses` int(10) DEFAULT 1,
  `use_count` int(10) DEFAULT 0,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
*/

/**
  * Generate a random key
  */

require_once('baseConnect.class.php');

class Keycode extends BaseConnect {
    const INTERNAL = -1;
    const INVALID_CODE = -2;
    const CREATEFAILED = -3;
    
    var $field = NULL;

    function __construct( $pDB ) {
        parent::__construct( $pDB );
    }

    function Add( $iUserId, $cCode, $iMaxUses, $cExpiration ) {
        $cQuery = "INSERT INTO Keycode (owner_id,code,max_uses,expires,created) VALUES (%u,'%s',%u,'%s',NOW())";
        $pResult = $this->Query( $cQuery, $iUserId, $cCode, $iMaxUses, $cExpiration );

        if( $pResult == NULL )
            return false;

        $iCodeId = $this->insert_id();
        $this->free_result( $pResult );

        if( $iCodeId <= 0 )
            return false;

        $this->Retrieve( $iUserId, $iCodeId );

        return $iCodeId;
    }

    function Retrieve( $iUserId, $sCodeId ) {

        $pResult = NULL;
        if( is_numeric( $sCodeId )) {
            $iCodeId = intval( $sCodeId );
            $cQuery = "SELECT * FROM Keycode WHERE owner_id=%u AND id=%u";
            $pResult = $this->Query( $cQuery, $iUserId, $iCodeId );
        } else {
            $cQuery = "SELECT * FROM Keycode WHERE owner_id=%u AND code='%s'";
            $pResult = $this->Query( $cQuery, $iUserId, $sCodeId );
        }

        if( $pResult == NULL ) {
            $this->field = NULL;
            return false;
        }

        if( $row = $this->m_pDBConnection->fetch_row( $pResult )) {
            foreach( $row as $key=>$value ) {
                $this->field[$key] = $value;
            }
        }

        $this->free_result( $pResult );

        return true;
    }
    
    //
    // Generates a code in format 'XXXX-YYYY-ZZZZ'
    // $iNumTokens determines how many parts the code will have
    // $iTokenLen determines how many chars will be in each token
    // if $cChars is given, that will be used as the character pool for each token
    //      otherwise, the uppercase Latin alphabet is used.
    // Tokens are separated by a hyphen
    //
    // so a call like GenerateCode(2,3) will generate a code like 'ABC-DEF'
    public static function GenerateCode( $iNumTokens, $iTokenLen, $cChars = NULL ) {
        if( $cChars == NULL )
            $cChars = "ABCDEFGHIJKLMNPQRSTUVWXYZ";

        if( $iNumTokens < 1 || $iTokenLen < 1 )
            return "";

        $cCode = "";
        while( $iNumTokens ) {
            $cChars = str_shuffle( $cChars );
            $cCode .= substr( $cChars, 0, $iTokenLen );

            if( --$iNumTokens ) {
                $cCode .= "-";
            }
        }

        return $cCode;
    }
}

function Keycode_Create( $pDB, $iUserId, $cCode, $iMaxUses=1, $cExpiration=NULL ) {
    $pKeycode = new Keycode( $pDB);
    if( $pKeycode == NULL )
        return Keycode::INTERNAL;

    if( $cCode == "" )
       return Keycode::INVALID_CODE;

    $iCodeId = $pKeycode->Add( $iUserId, $cCode, $iMaxUses, $cExpiration );
    
    if( $iCodeId == false )
        return Keycode::CREATEFAILED;

    return $pKeycode;
}

//
// Supports loading key by id or by the code itself
//
function Keycode_Get( $pDB, $iUserId, $cKeycodeId ) {
    $pKeycode = new Keycode( $pDB );
    
    if( $pKeycode->Retrieve( $iUserId, $cKeycodeId ))
        return $pKeycode;

    return NULL;
}

function Keycode_Generate( $iNumTokens=4, $iTokenLen=4 ) {
    return Keycode::GenerateCode( $iNumTokens, $iTokenLen );
}

function Keycode_Validate( $pDB, $iUserId, $cCode ) {
    $pKeycode = Keycode_Get( $pDB, $iUserId, $cCode );
    
    if( $pKeycode == NULL )
        return false;
    
    if( $pKeycode->field["use_count"] < $pKeycode->field["max_uses"] )
        return true;
}

function Keycode_Use( $pDB, $iUserId, $cCode, $iCount ) {
    if( is_numeric( $cCode )) {
        $iCodeId = intval( $cCode );
        $cQuery = "UPDATE Keycode SET use_count = IFNULL( use_count, 0 ) + %u 
            WHERE id=%u AND owner_id=%u";
        $pResult = $pDB->execute_query( $cQuery, $iCount, $iCodeId, $iUserId );
    } else {
        $cQuery = "UPDATE Keycode SET use_count = IFNULL( use_count, 0 ) + %u 
            WHERE code='%s' AND owner_id=%u";
        $pResult = $pDB->execute_query( $cQuery, $iCount, $cCode, $iUserId );
    }

    $iRowCount = $pDB->affected_rows();
    $pDB->free_result( $pResult );

    return $iRowCount > 0;
}
