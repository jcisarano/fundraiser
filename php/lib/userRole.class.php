<?PHP
require_once('database.class.php');
require_once( "baseConnect.class.php" );
//
// User Role functions
//

/*
+-------------+------------+------+-----+---------+-------+
| Field       | Type       | Null | Key | Default | Extra |
+-------------+------------+------+-----+---------+-------+
| user_id     | bigint(20) | NO   | MUL | NULL    |       |
| org_id      | bigint(20) | YES  |     | NULL    |       |
| role_id     | bigint(20) | YES  |     | NULL    |       |
| camp_id     | bigint(20) | YES  |     | 0       |       |
| create_date | datetime   | YES  |     | NULL    |       |
| expire_date | datetime   | YES  |     | NULL    |       |
+-------------+------------+------+-----+---------+-------+
*/
class UserRole extends BaseConnect {

    var $field = null;

    function __construct( $pDB = null ) {
        parent::__construct( $pDB);
    }

    function Add( $iUserId, $iOrgId, $iRoleId, $iCampId, $sExpireDate ) {
        $cQuery = "INSERT INTO UserRole (user_id,org_id,role_id,camp_id,create_date,expire_date) VALUES (%u,%u,%u,%u,NOW(),'%s')";

        $pResult = $this->Query( $cQuery, 
            $iUserId, 
            $iOrgId, 
            $iRoleId, 
            $iCampId, 
            $sExpireDate
        );

        if( $pResult == NULL )
            return false;

        $iRoleId = $this->m_pDBConnection->insert_id();
        $this->m_pDBConnection->free_result( $pResult );

        if ( $iRoleId < 1 )
        {
            return false;
        }

        return $iRoleId;
    }


    function Retrieve( $sUserId ) {
        $pResult = null;

        $iUserId = intval($sUserId);

        $cQuery = "SELECT * FROM UserRole AS ur WHERE ur.user_id=%u";
        $pResult = $this->Query( $cQuery, $iUserId );
        
        if( $pResult == NULL ) {
            $this->field = null;
            return false;
        }

        while( $row = $this->m_pDBConnection->fetch_row( $pResult ) ) {
            $this->field[] = $row;
        }
        
        if( count($this->field) < 1 )
            return false;

        return true;
    }
}


//
// API Layer
//

function UserRole_AddRole( $pDB, $iUserId, $iOrgId, $iCampaignId, $iRoleId, $cExpires=NULL )
{
    if ( $cExpires == null ) {
        $cQuery = "INSERT INTO UserRole (user_id,org_id,camp_id,role_id,create_date) VALUES (%u,%u,%u,%u,NOW())";
        $pResult = $pDB->execute_query( $cQuery, $iUserId, $iOrgId, $iCampaignId, $iRoleId);
    }
    else {
        $cQuery = "INSERT INTO UserRole (user_id,org_id,camp_id,role_id,create_date,expire_date) VALUES (%u,%u,%u,%u,NOW(),'%s')";
        $pResult = $pDB->execute_query( $cQuery, $iUserId, $iOrgId, $iCampaignId, $iRoleId, $cExpires);
    }

    $iId = $pDB->insert_id();
    $pDB->free_result($pResult);

    if( $iId < 1 )
        return false;
    
    return $iId;
}

function UserRole_EditRole( $pDB, $iUserId, $iOrgId, $iCampaignId, $iRoleId, $cExpires=NULL ) {
    if( $cExpires == NULL ) {
        $cQuery = "UPDATE UserRole SET role_id=%u WHERE user_id=%u AND org_id=%u AND campaign_id=%u";
        $pResult = $pDB->execute_query( $cQuery, $iRoleId, $iUserId, $iOrgId, $iCampaignId );
    } else { 
        $cQuery = "UPDATE UserRole SET role_id=%u, expire_date='%s' WHERE user_id=%u AND org_id=%u AND campaign_id=%u";
        $pResult = $pDB->execute_query( $cQuery, $iRoleId, $cExpires, $iUserId, $iOrgId, $iCampaignId );
    }
    
    $iCount = $pDB->affected_rows();
    $pDB->free_result($pResult);

    return $iCount > 0;
}

function UserRole_DeleteRole($pDB, $iUserId, $iOrgId, $iCampaignId, $iRoleId=NULL ) {
    if( $iRoleId == NULL ) {
        $cQuery = "DELETE FROM UserRole WHERE WHERE user_id=%u AND org_id=%u AND campaign_id=%u";
        $pResult = $pDB->execute_query( $cQuery, $iUserId, $iOrgId, $iCampaignId );
    } else {
        $cQuery = "DELETE FROM UserRole WHERE WHERE user_id=%u AND org_id=%u AND campaign_id=%u AND role_id=%u";
        $pResult = $pDB->execute_query( $cQuery, $iUserId, $iOrgId, $iCampaignId, $iRoleId );
    }

    $iCount = $pDB->affected_rows();
    $pDB->free_result($pResult);

    return $iCount > 0;
}

function UserRole_CanUseCourse( $pDB, $iUserId, $iCampaignId, &$cExpires )
{
    $cQuery = "select b.expire_date 
        from Organization a, UserRole b, OrganizationRolePermission c, Permission d, Campaign e 
        where b.user_id = '%s' 
            and c.perm_id = d.perm_id 
            and e.camp_orgid = a.org_id 
            and (d.perm_name = 'ALLACCESS' or d.perm_name = 'USECOURSE' ) 
            and b.camp_id = '%s' 
            and (b.expire_date is null or b.expire_date = '0000-00-00 00:00:00' or b.expire_date > NOW()) 
        limit 1";
    $pResult = $pDB->execute_query( $cQuery, $iUserId, $iCampaignId );
    
    if( $pResult == NULL )
        return false;
    
    $bResult = false;
    if( $row = $pDB->fetch_row( $pResult ) ) {
        $cExpires = $row["expire_date"];
        $bResult = true;
    }

    $pDB->free_result($pResult);
    return $bResult;
}

function UserRole_GetInfo( $pDB, $cRole ) {
    if( is_numeric($cRole)) {
        $iRole = intval( $cRole );
        $cQuery = "SELECT * FROM Role WHERE role_id = %u";
        $pResult = $pDB->execute_query( $cQuery, $iRole );
    } else {
        $cQuery = "SELECT * FROM Role WHERE role_name LIKE '%s'";
        $pResult = $pDB->execute_query( $cQuery, $cRole );
    }

    if( $row = $pDB->fetch_row( $pResult )) {
        return $row;
    }

    return NULL;
}

