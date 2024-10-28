<?PHP
require_once('baseConnect.class.php');
//
// Organization access functions
//

class Organization extends BaseConnect {
    const TEMPLATE_ORGANIZATION = 1;
    const ADMIN_ORGANIZATION    = 3;

    const INTERNAL      = -1;
    const CREATEFAILED  = -2;
    const INVALIDNAME   = -3;

    var $field = null;
    var $address_field = null;
    var $phone_field = null;

    function __construct( $pDB = null ) {
        parent::__construct( $pDB );
    }

    function Add( $cOrganizationFullName )
    {
        $cQuery = "INSERT INTO Organization SET org_fullname = '%s', org_datejoined = NOW()";
        
        $pResult = $this->Query( $cQuery, $cOrganizationFullName );
        
        if ( $pResult == null ) {
            return false;
        }

        $iOrgId = $this->m_pDBConnection->insert_id();
        $this->m_pDBConnection->free_result($pResult);
        
        if( $iOrgId < 1 ) {
            return false;
        }

        $this->Retrieve( $iOrgId );

        return $iOrgId;

    }

    function Retrieve( $sOrganizationId )
    {
        $pResult = null;
        if ( is_numeric( $sOrganizationId ) )
        {
            // Security feature, force into integer
            $iOrganizationId = intval($sOrganizationId);

            $cQuery = "SELECT * FROM Organization a WHERE a.org_id = '%u'";
            $pResult = $this->Query( $cQuery, $iOrganizationId );
        }

        if ( $pResult == null )
        {
            //echo "setting field to null {$sOrganizationId}";
            $this->field = null;
            return false;
        }

        if( $row = $this->m_pDBConnection->fetch_row( $pResult ) ) {
            foreach( $row as $key=>$value ) {
                $this->field[$key] = $value;
            }
        }

        $this->m_pDBConnection->free_result($pResult);

        //$this->RetrieveAddressData( $this->field['org_id'] );
        //$this->RetrievePhoneData( $this->field['org_id'] );
        
        return true;
    }
    
    function Save() {
        $cQuery = "UPDATE Organization SET org_name='%s', org_fullname='%s',org_status=%u WHERE org_id=%u";
        
        $cQuery = "SELECT * FROM Organization a WHERE a.org_id = '%u'";
        $pResult = $this->Query( $cQuery,
            $this->field["org_name"], 
            $this->field["org_fullname"], 
            $this->field["org_status"], 
            $this->field["org_id"]);

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

    /*
        TODO: WILL WE NEED THESE?
  function RetrieveAddressData( $sOrganizationId )
  {
    $pResult = null;
    if ( is_numeric( $sOrganizationId ) )
    {
        // Security feature, force into integer
        $iOrganizationId = intval($sOrganizationId);
        $pResult = $this->Query("select * from OrganizationAddress a where a.org_id = '".$iOrganizationId."'" );
    }
    else
    {
	return false;
    }

    if ( $pResult == null )
    {
        $this->field = null;
        return false;
    }

    if ( odbc_num_rows( $pResult ) <= 0 )
    {
        odbc_free_result( $pResult );
        return false;
    }

    $this->address_field = array();
    for ($i = 1;$i <= odbc_num_fields($pResult); $i++ )
    {
        $this->address_field[odbc_field_name($pResult,$i)] = odbc_result($pResult,$i);
    }

    odbc_free_result( $pResult );


    return true;
  }

  function RetrievePhoneData( $sOrganizationId )
  {
    $pResult = null;
    if ( is_numeric( $sOrganizationId ) )
    {
        // Security feature, force into integer
        $iOrganizationId = intval($sOrganizationId);
        $pResult = $this->Query("select * from OrganizationPhone a where a.org_id = '".$iOrganizationId."'" );
    }
    else
    {
        return false;
    }

    if ( $pResult == null )
    {
        $this->field = null;
        return false;
    }

    if ( odbc_num_rows( $pResult ) <= 0 )
    {
        odbc_free_result( $pResult );
        return false;
    }

    $this->phone_field = array();
    for ($i = 1;$i <= odbc_num_fields($pResult); $i++ )
    {
        $this->phone_field[odbc_field_name($pResult,$i)] = odbc_result($pResult,$i);
    }

    odbc_free_result( $pResult );


    return true;
  }
  */

}

//
// Organization API
//

//
// Create an organization, performing all necessary housekeeping tasks.
//
function Organization_Create( $pDB, $cOrganizationFullName ) {
    $sOrg = new Organization( $pDB );
    if ( $sOrg == null )
    {
     return Organization::INTERNAL;
    }

    if( $cOrganizationFullName == "" )
     return Organization::INVALIDNAME;

    $id = $sOrg->Add( $cOrganizationFullName );
    if ( $id < 1 )
        return Organization::CREATEFAILED;

    Organization_CopyRolePermissions( $pDB, Organization::TEMPLATE_ORGANIZATION, $sOrg->field['org_id'] );

    return $id;
}

//
// Load an existing organization by ID
//
function Organization_Get( $pDB, $iOrgId ) {
    $pOrg = new Organization( $pDB );
    if ( $pOrg == null )
        return NULL;

    if ( $pOrg->Retrieve( intval($iOrgId) ) )
        return $pOrg;

    return NULL;
}

//
// Load one or more organizations for the user based on the given restrictions
// Returns array of organizations with basic info
// If user has ADMIN_ORGANIZATION perms, they are returned
//
// If $iOrgId given, perms will match that organization
// If $perms array given, only matching perms returned
//
function Organization_FetchForUser( $pDB, $iUserId, $orgIds=NULL, $perms=NULL ) {
    $cQuery = "SELECT o.org_id, o.org_name, o.org_fullname, o.org_datejoined, 
        p.perm_id, p.perm_name, p.perm_description 
    FROM Organization o, UserRole ur, OrganizationRolePermission orp, Permission p
    WHERE ur.user_id=%u 
        AND o.org_id=ur.org_id 
        AND ur.role_id=orp.role_id 
        AND orp.perm_id=p.perm_id 
        AND orp.org_id=o.org_id";

    $queryParams[] = $iUserId;

    //add org id clause if needed
    if( $orgIds != NULL ) {
        if( !is_array( $orgIds ))
            $ids[] = $orgIds;
        else
            $ids = $orgIds;
    
        $cQuery .= " AND o.org_id IN (";
        $ii=0;
        foreach( $ids as $oo ) {
            if( $ii > 0 )
                $cQuery .= ", ";
            $queryParams[] = $oo;
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

    return $pList;
}


//
// Can a user create a campaign for this organization?
// Returns true/false
//
function Organization_CanUserCreateCampaign( $pDB, $iOrgId, $iUserId ) {
    $orgIdArray = array(0);
    if( !is_array( $iOrgId )) {
        $orgIdArray[] = $iOrgId;
    }
    else {
        $orgIdArray = array_merge( $orgIdArray, $iOrgId );
    }

    $list = Organization_FetchForUser( $pDB, $iUserId, $orgIdArray, array('ALLACCESS','CREATECAMPAIGN') );
    
    return count($list) > 0;
}

//
// Can a user edit a campaign for this organization?
// Returns true/false
//
function Organization_CanUserEditCampaign( $pDB, $iOrgId, $iUserId ) {
    $orgIdArray = array(0);
    if( !is_array( $iOrgId )) {
        $orgIdArray[] = $iOrgId;
    }
    else {
        $orgIdArray = array_merge( $orgIdArray, $iOrgId );
    }
    $list = Organization_FetchForUser( $pDB, $iUserId, $orgIdArray, array('ALLACCESS','CREATECAMPAIGN','EDITCAMPAIGN') );
    
    return count($list) > 0;
}

//
// Output a list of unique organizations where the user has any permissions set
// Returns array of arrays with basic organization info and permission
//
function Organization_GenerateListForUser( $pDB, $iUserId ) {
    return Organization_FetchForUser( $pDB, $iUserId );
}

//
// Duplicate the Role Permissions from one organization to another
//
function Organization_CopyRolePermissions( $pDB, $iSource, $iDest ) {
    $cQuery = "INSERT INTO OrganizationRolePermission (org_id,role_id,perm_id) SELECT %u, role_id, perm_id FROM OrganizationRolePermission WHERE org_id=%u;";
    $pResult = $pDB->execute_query( $cQuery, $iDest, $iSource );

    $count = $pDB->affected_rows();

    $pDB->free_result($pResult);

    return $count > 0;
}

