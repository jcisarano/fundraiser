<?PHP
require_once('baseConnect.class.php');
require_once('usersession.php');
//
// User access functions
//

class User extends BaseConnect {

    var $field = null;

    const LOGINFAILED       = -1;
    const INTERNAL          = -2;
    const CREATEFAILED      = -3;
    const NOCOOKIE          = -4;
    const EXPIRED_SESSION   = -5;
    
    const PUBLIC_ORGANIZATION = 2;
    
    const ROLE_ADMINISTRATOR = 2;
    const ROLE_MONITOR = 4;

    function __construct( $pDB = null ) {
        parent::__construct( $pDB );
    }

    function Add( $cUserName, $cPassword, $cFirstName = null, $cLastName = null ) {
        $cQuery = "insert into User set user_login = '%s', 
            user_datecreated = NOW(), 
            user_password_is_temp = 'Y', 
            user_password = PASSWORD('%s'), 
            user_first_name = '%s', 
            user_last_name = '%s'";
        
        $pResult = $this->Query(
            $cQuery,
            $cUserName,
            $cPassword,
            $cFirstName,
            $cLastName
        );


        $iUserId = $this->m_pDBConnection->insert_id();

        $this->m_pDBConnection->free_result($pResult);
        if ( $iUserId < 1 )
        {
            return false;
        }

        $this->Retrieve( $iUserId ); // Load the record into this object

        return $iUserId;
    }


    function AuthPassword( $cUserName, $cPassword ) {
        if ( strlen($cUserName) < 1 )
            return false;
        
        $cQuery = "SELECT COUNT(*) X FROM User AS u WHERE u.user_login = '%s' and user_password = PASSWORD('%s')";
        
        $pResult = $this->Query($cQuery,$cUserName,$cPassword);
        
        if( $row = $this->m_pDBConnection->fetch_row( $pResult ) )
        {
            if( $row["X"] > 0 )
                return true;
        }
        
        return false;
    }



    function UpdatePassword( $cPassword )
    {
        if ( $this->field == null || intval($this->field['user_id']) == 0 )
            return false;

        $cQuery = "update User set user_password = PASSWORD('%s'), user_password_is_temp = 'N', user_lastpasswordchange = NOW()  where user_id = '%s'";
        
        $pResult = $this->Query( $cQuery, $cPassword, $this->field["user_id"] );

        $iCount = $this->m_pDBConnection->affected_rows();
        
        if( $pResult != NULL )
            $this->m_pDBConnection->free_result( $pResult );
            
        return $iCount > 0;
    }

    function Retrieve( $sUserId ) {
        $pResult = null;
        if ( is_numeric( $sUserId ) ) {
            // Security feature, force into integer
            $iUserId = intval($sUserId);
            $query = "SELECT * FROM User AS u WHERE u.user_id=%u";
            $pResult = $this->Query(
                $query,
                $iUserId
            );
        }
        else {
            $query = "SELECT * FROM User AS u WHERE u.user_login = '%s'";
            $pResult = $this->Query(
                $query,
                $sUserId
            );
        }
        
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
        if ( $this->field == null || intval($this->field['user_id']) == 0 )
            return false;

        $cQuery = "UPDATE User SET user_status = %u, user_first_name = '%s', user_last_name = '%s' where user_id = %u";
        
        $pResult = $this->Query( 
            $cQuery, 
            $this->field["user_status"], 
            $this->field["user_first_name"], 
            $this->field["user_last_name"], 
            $this->field["user_id"] 
        );

        $iCount = $this->m_pDBConnection->affected_rows();
        
        if( $pResult != NULL )
            $this->m_pDBConnection->free_result( $pResult );
            
        return $iCount > 0;
        
    }
}


//
// API Layer
//

//
// Create a user
//
function User_Create( $pDB, $cUserName, $cPassword, $cFirstName, $cLastName ) {
    $pUser = new User( $pDB );
    if ( $pUser == null )
        return User::INTERNAL;

    if( $cUserName == NULL || $cPassword == NULL )
        return User::CREATEFAILED;

    $iUserId = $pUser->Add( $cUserName, $cPassword, $cFirstName, $cLastName );
    if ( $iUserId == false ) {
        return User::CREATEFAILED;
    }

    return $iUserId;
}

//
// Supports load by user ID or user login name
function User_Get( $pDB, $cUserId ) {
    $pUser = new User( $pDB );

    if( $pUser->Retrieve( $cUserId ))
        return $pUser;

    return NULL;
}


//
// Authenticate a user by password, creating a new session
// returns userid on success, negative result is error code
//
function User_Login( $pDB, $cUserName, $cPassword, &$cSessionId ) {
   $pUser = new User( $pDB );
   if ( $pUser == null )
     return User::INTERNAL;

   if ( $pUser->AuthPassword( $cUserName, $cPassword ) == false )
     return User::LOGINFAILED;

   if ( $pUser->Retrieve( $cUserName ) == false )
     return User::LOGINFAILED;

   $iUserId = intval($pUser->field['user_id']);
   if ( $iUserId <= 0 )
     return User::INTERNAL;

   $cSessionId = UserSession_Create( $pDB, $iUserId );
   return $iUserId;
}

//
// Log a user out, given the session info
// (Note: Should this close all sessions for this user? )
function User_Logout( $pDB, $cSessionId ) {
   UserSession_Close( $pDB, $cSessionId ); 
}

//
// Return id of currently logged in user
// Otherwise, return negative error code
//
function User_IsLoggedIn( $pDB, $cookieName ) {
    if ( isset($_COOKIE) === false || isset($_COOKIE[$cookieName]) === false )
    {
        return User::NOCOOKIE;
    }

    $iUserId = UserSession_Validate( $pDB, $_COOKIE[$cookieName] );
    
    if( $iUserId <= 0 )
        return User::EXPIRED_SESSION;

    UserSession_Touch( $pDB, $_COOKIE[$cookieName] );

    return $iUserId;
}

function User_ValidateEmail( $email ) {
    return filter_var( $email, FILTER_VALIDATE_EMAIL );
}

