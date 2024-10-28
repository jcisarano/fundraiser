<?PHP

//
// UserSession management API
//

function CreateUserSessionId( $userid, $index )
{
  $iNow = time(0);

  $cKey = $index.chr(rand(1,24) + ord('A')).md5( $userid."_".$iNow."_".$index."_".mt_rand() );
  
  return $cKey; 

}


//
// Create a user session, return the session index
//
function UserSession_Create( $pDB, $sUserId )
{
    $query = "INSERT INTO UserSession SET user_id = '%s', user_token = ''";
    $pDB->execute_query( $query, $sUserId );
    
    $iIndex = $pDB->insert_id();
    
    $cKey = CreateUserSessionId( $sUserId, $iIndex );
    
    $query = "UPDATE UserSession SET user_startdate = NOW(), user_token = '%s', user_lastactive=NOW() WHERE session_id = '%u'";
    $pDB->execute_query( $query, $cKey, $iIndex );
    
    return $cKey;
}

//
// Validate a user session, and returns the user_id
//
function UserSession_Validate( $pDB, $cSessionToken )
{
    // Get the session (32/64 bit dependency), 
    // TODO: Compare it against a maximum time the session could be active
    $iIndex = intval( $cSessionToken );
    
    $cQuery = "SELECT user_id FROM UserSession WHERE session_id = '%u' AND user_token = '%s' AND user_enddate IS NULL";
    
    $pResult = $pDB->execute_query( $cQuery, $iIndex, $cSessionToken );
    
    if( $row = $pDB->fetch_row( $pResult )) {
        $pDB->free_result( $pResult );
        return intval( $row["user_id"] );
    }
    
    $pDB->free_result( $pResult );
    return 0;
}


//
// Close a user session
//
function UserSession_Close( $pDB, $cSessionToken )
{
    // Get the session (32/64 bit dependency), 
    $iIndex = intval( $cSessionToken );
    $cQuery = "UPDATE UserSession SET user_enddate=NOW() WHERE session_id = '%u' AND user_token = '%s' AND user_enddate IS NULL";
    
    $pResult = $pDB->execute_query( $cQuery, $iIndex, $cSessionToken );
    
    $count = $pDB->affected_rows();
    $pDB->free_result( $pResult );
    
    return $count > 0;
}





//
// "Touch" a user session
//
function UserSession_Touch( $pDB, $cSessionToken )
{
    // Pull the integer value of the session, which is the actual index ( index is the start of the session )
    // compare it to session token 
    // Note this will be limited to 3billion on 32 bit systems
    $iIndex = intval( $cSessionToken );
    
    $cQuery = "UPDATE UserSession SET user_lastactive=NOW() WHERE session_id = '%u' AND user_token = '%s' AND user_enddate IS NULL";
    $pResult = $pDB->execute_query( $cQuery, $iIndex, $cSessionToken );
    
    $count = $pDB->affected_rows();
    $pDB->free_result( $pResult );
    
    return $count > 0;
}



//
// Return the user information associated with the given session
//
function UserSession_GetUser( $pDB, $cSessionToken )
{
  if ( ($iUserId = UserSession_Validate( $pDB, $cSessionToken )) == 0 )
      return false;
  $pUser = new User( $pDB );
  if ( $pUser )
  {
    $pUser->Retrieve( $iUserId );
    return $pUser;
  }
  return false;
}


