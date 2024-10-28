<?php
require_once( "baseConnect.class.php" );

class SalesItem extends BaseConnect {
    const INVALID_TYPE  = -1;
    const INTERNAL      = -2;
    const NOPERMISSION  = -3;
    const INVALID_DATA  = -4;


    var $field = NULL;

    function __construct ( $pDB ) {
        parent::__construct( $pDB );
    }

    function Add( $cItemType, $cItemName, $iCreatorId=0, $iOrgId=0, $iCampId=0) {

        $cRealType = NULL;

        if( $this->ValidateType( $cItemType, $iItemTypeId ) == false ) {
            return self::INVALID_TYPE;
        }

        $cQuery = "INSERT INTO SalesItem ( 
            item_type, 
            item_orgid,
            item_campaignid,
            item_creatorid,
            item_name,
            item_datecreated) VALUES (%u,%u,%u,%u,'%s',NOW())";

        $pResult = $this->Query(
            $cQuery,
            $iItemTypeId,
            $iOrgId,
            $iCampId,
            $iCreatorId,
            $cItemName
        );
        
        $iItemId = $this->insert_id();
        $this->free_result( $pResult );

        if( $iItemId < 1 )
            return self::INTERNAL;

        $this->Retrieve( $iItemId );

        return $iItemId;
    }

    function Retrieve( $sItemId ) {
        $pResult = NULL;
        if( is_numeric( $sItemId )) {
            $iItemId = intval( $sItemId );
            $cQuery = "SELECT * FROM SalesItem WHERE item_id=%u";
            $pResult = $this->Query( $cQuery, $iItemId );
        } else {
            $cQuery = "SELECT * FROM SalesItem WHERE item_name='%s'";
            $pResult = $this->Query( $cQuery, $sItemId );
        }

        if( $pResult == NULL ) {
            $this->field = NULL;
            return false;
        }

        if( $row = $this->m_pDBConnection->fetch_row( $pResult ) ) {
            foreach( $row as $key=>$value ) {
                $this->field[$key] = $value;
            }

            $this->free_result( $pResult );
            return true;
        }

        $this->free_result( $pResult );
        return false;
    }

    function ValidateType( $cItemType, &$iItemTypeId ) {
        $cQuery = "SELECT item_type, item_id FROM SalesItemType WHERE item_type = '%s'";
        $pResult = $this->Query( $cQuery, $cItemType );
        
        if( $row = $this->m_pDBConnection->fetch_row( $pResult )) {
            if( $row["item_type"] != "" ) {
                $iItemTypeId = $row["item_id"];
                $this->free_result( $pResult );
                return true;
            }
        }

        $this->free_result( $pResult );
        return false;
    }
    
    //
    // Used to update existing item
    //
    function Save() {
        $cQuery = "UPDATE SalesItem 
            SET item_type=%u, item_orgid=%u, item_campaignid=%u, 
                item_creatorid=%u, item_name='%s', item_status=%u, 
                item_refname='%s', item_url='%s', item_startdate='%s', 
                item_enddate='%s', item_cost=%d 
            WHERE item_id=%u";

        $pResult = $this->Query(
            $cQuery,
            $this->field["item_type"],
            $this->field["item_orgid"],
            $this->field["item_campaignid"],
            $this->field["item_creatorid"],
            $this->field["item_name"],
            $this->field["item_status"],
            $this->field["item_refname"],
            $this->field["item_url"],
            $this->field["item_startdate"],
            $this->field["item_enddate"],
            $this->field["item_cost"],
            $this->field["item_id"]
        );

        $iCount = $this->m_pDBConnection->affected_rows();

        if( $pResult != NULL )
            $this->m_pDBConnection->free_result( $pResult );

        return $iCount > 0;
        
    }
}

//
//API Features Below
//

function SalesItem_Create( $pDB, $iCreatorId, $iOrgId, $iCampaignId, $cItemType, $cItemName ) {
    if( $cItemType == "" || $cItemName == "" 
        || $iCreatorId == NULL || $iOrgId == NULL
        || $iCampaignId == NULL )
        return SalesItem::INVALID_DATA;

    $pItem = new SalesItem( $pDB );
    if( $pItem == NULL )
        return SalesItem::INTERNAL;

    $iItemId = $pItem->Add( $cItemType, $cItemName, $iCreatorId, $iOrgId, $iCampaignId );
    return $iItemId;
}

function SalesItem_Get( $pDB, $sItemId ) {
    $pItem = new SalesItem( $pDB );
    if( $pItem == NULL )
        return NULL;

    if( $pItem->Retrieve( $sItemId ))
        return $pItem;

    return NULL;
}

function SalesItem_FetchCampaignItems( $pDB, $iCampaignId ) {
    //TODO: Add start/end date checks?
    $cQuery = "SELECT * FROM SalesItem WHERE item_campaignId='%s'";

    $pResult = $pDB->execute_query( $cQuery, $iCampaignId );

    $pList = array();
    while( $row = $pDB->fetch_row( $pResult ) ) {
        $pList[] = $row;
    }

    $pDB->free_result($pResult);

    return $pList;
}

function SalesItem_FetchItemsForOrg( $pDB, $iOrgId = 0 ) {
    //TODO: Check validity dates, check status
    $cQuery = "SELECT * FROM SalesItem WHERE item_orgid=%u";
    
    $pResult = $pDB->execute_query( $cQuery, $iOrgId );
    
    $pList = array();
    while( $row = $pDB->fetch_row( $pResult ) ) {
        $pList[] = $row;
    }

    $pDB->free_result($pResult);

    return $pList;
}

function SalesItem_FetchItemsForUser( $pDB, $iUserId ) {
    //TODO: Check validity dates, check status
    $cQuery = "SELECT * FROM SalesItem WHERE item_creatorid=%u";

    $pResult = $pDB->execute_query( $cQuery, $iOrgId );

    $pList = array();
    while( $row = $pDB->fetch_row( $pResult ) ) {
        $pList[] = $row;
    }

    $pDB->free_result($pResult);

    return $pList;
}

//
// Record the sale of an item to a user for a given campaign
//
function SalesItem_RecordSaleForUser( $pDB, $iOwnerId, $iCampaignId, 
    $iItemId, $iTransactionId ) {
    $cQuery = "INSERT INTO UserItem 
        (owner_id,item_id,campaign_id,transaction_id,purchasedate) VALUES 
        (%u,%u,%u,%u,NOW())";

    $pResult = $pDB->execute_query(
        $cQuery,
        $iOwnerId,
        $iItemId,
        $iCampaignId,
        $iTransactionId
    );

    $iId = $pDB->insert_id();
    $pDB->free_result($pResult);

    if( $iId < 1 )
        return false;

    return $iId;
}

//
// Select UserItem records by owner id, campaign id, or both
//      if neither is supplied, all records are returned
// Item type is required
//
function SalesItem_FetchRecords( $pDB, $iItemType, $iOwnerId=NULL, $iCampaignId=NULL,
        $iStart=0,$iCount=20 ) {

    $cQuery = "SELECT si.item_type,si.item_name, 
            ui.item_id,ui.transaction_id,ui.purchasedate,ui.completedate 
        FROM SalesItem si, UserItem ui
            WHERE ui.item_id=si.item_id
                AND si.item_type=%u";

    $sQueryArgs = array();
    $sQueryArgs[] = $iItemType;
    
    if( $iOwnerId != NULL ) {
        $cQuery .= " AND ui.owner_id=%u";
        $sQueryArgs[] = $iOwnerId;
    }

    if( $iCampaignId != NULL ) {
        $cQuery .= " AND ui.campaign_id=%u";
        $sQueryArgs[] = $iCampaignId;
    }

    $cQuery .= " LIMIT %u,%u";
    $sQueryArgs[] = $iStart;
    $sQueryArgs[] = $iCount;

    $pResult = $pDB->execute_query($cQuery,$sQueryArgs);

    $items = array();
    while( $row = $pDB->fetch_row( $pResult ) ) {
        $items[] = $row;
    }

    $pDB->free_result($pResult);

    return $items;
}

/*
DROP TABLE IF EXISTS `UserItem`;
CREATE TABLE `UserItem` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `campaign_id` bigint(20) unsigned NOT NULL,
  `transaction_id` bigint(20) unsigned NOT NULL,
  `purchasedate` datetime DEFAULT NULL,
  `completedate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
*/