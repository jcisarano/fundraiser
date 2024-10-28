<?PHP
require_once('baseConnect.class.php');
require_once('userRole.class.php');
require_once( "organization.class.php" );
//
// Campaign access functions
//

class Campaign extends BaseConnect {

    var $field = null;

    const INVALIDTYPE      = -1; // An invalid campaign type specified
    const TOOMANYOFTYPE    = -2; // There are too many of that type for this organization
    const NOTFOUND         = -3; // The specified campaign was not found
    const INTERNAL         = -4; // Internal error (DB error? )
    const NOPERMISSION     = -5; // No permissions for the operation
    const CREATEFAIL       = -6; // Unable to add new campaign to db

    function __construct( $pDB = null ) 
    {
        parent::__construct( $pDB );
    }

    //
    // Add a campaign
    // Returns:
    //   Campaign ID on success
    //   negative error result on error
    function Add( $cCampaignType, $cCampaignName, $iCreatorId = 0, $iOrgId = 0 )
    {
        $cRealType = null;

        if ( $this->ValidateType( $cCampaignType, $cRealType ) == false )
          return Campaign::INVALIDTYPE;

        /*
        if ( $this->GetCampaignCount( $iOrgId, $cRealType ) >= $this->GetMaximumCampaigns( $cRealType ) )
          return Campaign::TOOMANYOFTYPE;
        */

        $cQuery = "INSERT INTO Campaign (camp_name,camp_type,camp_datecreated,camp_creatorid,camp_orgid) VALUES ('%s','%s',NOW(),%u,%u)";
        
        $pResult = $this->Query($cQuery, $cCampaignName,$cRealType,$iCreatorId,$iOrgId);

        $iCampaignId = $this->m_pDBConnection->insert_id();
        $this->m_pDBConnection->free_result($pResult);
        
        if( $iCampaignId < 1 )
            return Campaign::CREATEFAIL;
        
        $this->Retrieve( $iCampaignId );
        
        return $iCampaignId;
    }

    function UpdateStartDate( $iCampaignId, $cStartDate )
    {
        //first verify that the new date is in the future
        $cFinal = strtotime( $cStartDate );
        if( time() >= $cFinal )
            return false;
        
        //then format it for MySQL Datetime
        $cFinal = date("Y-m-d H:i:s", $cFinal);
        
        
         $cQuery = "update Campaign set camp_startdate = '%s' where camp_id = '%u'";
         $pResult = $this->Query( $cQuery, $cFinal, $iCampaignId );
         
        $count = $this->m_pDBConnection->affected_rows();
        $this->m_pDBConnection->free_result($pResult);
        
         if( $count < 1 )
            return false;
        
        $this->field["camp_startdate"] = $cFinal;
        return true;
    }

    function UpdateEndDate( $iCampaignId, $cEndDate )
    {
        //TODO: Verify that the end date is after the start date?

        //first verify that the new date is in the future
        $cFinal = strtotime( $cStartDate );
        if( time() >= $cFinal )
            return false;

        //then format it for MySQL Datetime
        $cFinal = date("Y-m-d H:i:s", $cFinal);

         $cQuery = "update Campaign set camp_enddate = '%s' where camp_id = '%u'";
         $pResult = $this->Query( $cQuery, $cFinal, $iCampaignId );

        $count = $this->m_pDBConnection->affected_rows();
        $this->m_pDBConnection->free_result($pResult);

         if( $count < 1 )
            return false;

        $this->field["camp_enddate"] = $cFinal;
        return true;
    }

    function Retrieve( $sCampaignId )
    {
        $pResult = null;
        if ( is_numeric( $sCampaignId ) )
        {
            // Security feature, force into integer
            $iCampaignId = intval($sCampaignId);
            $cQuery = "SELECT * FROM Campaign a WHERE a.camp_id = '%u'";
            $pResult = $this->Query( $cQuery, $iCampaignId );
        }
        else
        {
            //NOTE: Campaign names are not guaranteed unique!
            $cQuery = "SELECT * FROM Campaign a WHERE a.camp_name = '%s'";
            $pResult = $this->Query( $cQuery, $sCampaignId ); 
        }

        if ( $pResult == null )
        {
            $this->field = null;
            return false;
        }

        if( $row = $this->m_pDBConnection->fetch_row( $pResult ) ) {
            foreach( $row as $key=>$value ) {
                $this->field[$key] = $value;
            }

            $this->m_pDBConnection->free_result( $pResult );
            return true;
        }

        $this->m_pDBConnection->free_result( $pResult );
        return false;
    }
    
    function Save() {
        $cQuery = "UPDATE Campaign SET camp_type='%s', camp_orgid=%u, camp_name='%s',camp_status='%s', camp_refname='%s', camp_url='%s',camp_startdate='%s',camp_enddate='%s',camp_cost=%d WHERE camp_id=%u";
        
        $pResult = $this->Query( $cQuery,
            $this->field["camp_type"], 
            $this->field["camp_orgid"], 
            $this->field["camp_name"], 
            $this->field["camp_status"], 
            $this->field["camp_refname"], 
            $this->field["camp_url"], 
            $this->field["camp_startdate"], 
            $this->field["camp_enddate"], 
            $this->field["camp_cost"], 
            $this->field["camp_id"]
        );

        if ( $pResult == null )
        {
            $this->field = null;
            return false;
        }
        
        $iCount = $this->m_pDBConnection->affected_rows();
        
        if( $pResult != NULL )
            $this->m_pDBConnection->free_result( $pResult );
            
        return $iCount > 0;
    }
    

    function ValidateType( $cType, &$cRealType )
    {
        $cQuery = "select camp_type from CampaignType where camp_type = '%s'";
        $pResult = $this->Query( $cQuery, $cType );
        
        if( $row = $this->m_pDBConnection->fetch_row( $pResult )) {
            if( $row["camp_type"] != "" ) {
                $cRealType = $row["camp_type"];
                $this->free_result( $pResult );
                return true;
            }
        }
        
        $this->free_result( $pResult );
        return false;
    }
}

//
// API level
//


//
// Return a campaign option
//
function Campaign_Get( $pDB, $iCampaignId )
{
    $pCampaign = new Campaign( $pDB );
    if ( $pCampaign == null )
        return null;

    if ( $pCampaign->Retrieve( intval($iCampaignId) ) )
        return $pCampaign;

    return null;
}

//
// Create a campaign.
// Returns: 
//   Campaign ID on success, 
//   negative number on error 
function Campaign_Create( $pDB, $iOwnerId, $iOrgId, $cCampaignType, $cCampaignName )
{
    $pCampaign = new Campaign( $pDB );
    if ( $pCampaign == null )
        return Campaign::INTERNAL;

    if ( Organization_CanUserCreateCampaign( $pDB, $iOrgId, $iOwnerId ) == false )
        return Campaign::NOPERMISSION;

    $iCampaignId = $pCampaign->Add( $cCampaignType, $cCampaignName, $iOwnerId, $iOrgId );

    return $iCampaignId; 
}

//
// Same as Campaign_Create() except it assumes you have already done the perm
//      check for the owner
//
function Campaign_CreateNoPermCheck( $pDB, $iOwnerId, $iOrgId, $cCampaignType, $cCampaignName )
{
    $pCampaign = new Campaign( $pDB );
    if ( $pCampaign == null )
        return Campaign::INTERNAL;

    $iCampaignId = $pCampaign->Add( $cCampaignType, $cCampaignName, $iOwnerId, $iOrgId );

    return $iCampaignId; 
}

function Campaign_FetchForUser( $pDB, $iUserId, $campIds=NULL, $perms=NULL ) {
    $cQuery = "SELECT o.org_id,o.org_fullname,p.perm_id,p.perm_name,p.perm_description,c.camp_id,c.camp_name
    FROM Campaign c, UserRole ur, OrganizationRolePermission orp, Permission p, Organization o
    WHERE ur.user_id=%u
        AND c.camp_id=ur.camp_id
        AND ur.role_id=orp.role_id
        AND orp.perm_id=p.perm_id
        AND orp.org_id=ur.org_id
        AND o.org_id=ur.org_id";

        $queryParams[] = $iUserId;

    //add campaign clause if needed
    if( $campIds != NULL ) {
        if( !is_array( $campIds ))
            $ids[] = $campIds;
        else
            $ids = $campIds;
        $cQuery .= " AND c.camp_id IN (";
        $ii = 0;
        foreach( $ids as $cc ) {
            if( $ii > 0 )
                $cQuery .= ", ";
            $queryParams[] = $cc;
            $cQuery .= "%u";
            $ii++;
        }
        $cQuery .= ")";
    }

    //add perms clause if needed
    if( $perms != NULL ) {
        if( !is_array( $perms ))
            $permList[] = $perms;
        else
            $permList = $perms;

        $cQuery .= " AND p.perm_name IN (";
        $ii=0;
        foreach( $permList as $pp ) {
            if( $ii > 0 )
                $cQuery .= ", ";
            $queryParams[] = $pp;
            $cQuery .= "'%s'";
            $ii++;
        }
        
        $cQuery .= ")";
    }

    $pResult = $pDB->execute_query( $cQuery, $queryParams );

    $pList = array();
    while( $row = $pDB->fetch_row( $pResult ) ) {
        $pList[] = $row;
    }

    $pDB->free_result( $pResult );
    return $pList;
}

function Campaign_GenerateListForUser( $pDB, $iUserId, $iOrgId=NULL )
{
    if( intval($iOrgId) > 0 ) {
        $cQuery = "select e.*, a.org_name 
            from Organization a, 
                UserRole b, 
                OrganizationRolePermission c, 
                Permission d, 
                Campaign e 
            where b.user_id = '%u' 
                and a.org_id = '%u' 
                and a.org_id = b.org_id 
                and c.org_id = b.org_id 
                and e.camp_orgid = a.org_id 
                and c.perm_id = d.perm_id 
                and (d.perm_name = 'ALLACCESS' 
                    or d.perm_name = 'VIEWCAMPAIGN' 
                    or d.perm_name = 'EDITCAMPAIGN' 
                    or d.perm_name = 'CREATECAMPAIGN' ) 
            group by e.camp_id";
        $pResult = $pDB->execute_query( $cQuery, $iUserId, $iOrgId );
    } else {
        $cQuery = "select e.*, a.org_name 
            from Organization a, 
                UserRole b, 
                OrganizationRolePermission c, 
                Permission d, 
                Campaign e 
            where b.user_id = '%u' 
                and c.perm_id = d.perm_id 
                and e.camp_orgid = a.org_id 
                and (d.perm_name = 'ALLACCESS' 
                    or d.perm_name = 'VIEWCAMPAIGN' 
                    or d.perm_name = 'EDITCAMPAIGN' 
                    or d.perm_name = 'CREATECAMPAIGN' ) 
            group by e.camp_id ";
        $pResult = $pDB->execute_query( $cQuery, $iUserId );
    }
    
    $pList = array();
    while( $row = $pDB->fetch_row( $pResult ) ) {
        $pList[] = $row;
    }

    $pDB->free_result($pResult);

    return $pList;
}


//
// Determine if a user can utilize the specified course/page
//
// ( Needs some serious optimization, this will check on every page access currently 
//
function Campaign_CanUseCourse( $pDB, $iUserId, $iCampId, $cLink )
{
  $pList = Campaign_GenerateListForUser( $pDB, $iUserId, 0 );
  $i = 0;


  $bFound = false;
  $cLink = "";
  for ( $i = 0; $i < count($pList); $i ++ )
  {
    if ( $pList[$i]['camp_type'] == 'COURSE' )
    {
       if ( intval($pList[$i]['camp_id']) == $iCampId )
       {
          if ( UserRole_CanUseCourse( $pDB, $iUserId, $iCampId, $cExpires ) == true )
          {
            $bFound = true;
            $cLink = $pList[$i]['camp_url'];
            break;
          }
       }
    }
  }
  return $bFound;
}

function Campaign_FetchParticipants( $pDB, $iCampaignId ) {
    $cQuery = "SELECT u.user_id, u.user_login, u.user_datecreated, u.user_first_name, u.user_last_name 
        FROM User u, UserRole ur 
        WHERE ur.camp_id=%u AND ur.user_id=u.user_id";
    $pResult = $pDB->execute_query( $cQuery, $iCampaignId );
    
    $campaigns = array();
    while( $row = $pDB->fetch_row( $pResult ) ) {
        $campaigns[] = $row;
    }
    
    return $campaigns;
}

