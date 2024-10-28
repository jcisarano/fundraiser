<?php
require_once( "database.class.php" );
/*
 * Basic db functions including connect, disconnect, query
 *  Uses MySQLi connection
 *
 * 17 May 2015 - Improved error handling in connect()
 *               Improved support for arrays in execute_query()
 */

class MySqliConnection extends Database{


    function __construct( $cDB = null, $cUser = null, $cPassword = null, $cCatalog = null) {
        parent::__construct($cDB, $cUser, $cPassword, $cCatalog);
    }
    
    function __destruct(){
        parent::__destruct();
    }
    
    function connect( $cDB, $cUser, $cPassword, $cCatalog=null ) {
        if ( $this->m_pConnection != null )
        {
            $this->m_iUseCount ++;
            return $this->m_pConnection;
        }
    
        $this->m_pConnection = new mysqli( $cDB, $cUser, $cPassword );

        if ( isset( $this->m_pConnection->connect_errno )
            && $this->m_pConnection->connect_errno > 0 )
        {
            $cErr = $this->m_pConnection->connect_error;
            error_log( 'Database connection failure: ' .$cErr );
            return NULL;
        }
       
        if( $cCatalog ){
            mysqli_select_db( $this->m_pConnection, $cCatalog );
            }

        $this->m_iUseCount = 1;
        return $this->m_pConnection;
    }
    
    function disconnect() {
        if ( $this->m_iUseCount == 1 )
        {
            if( $this->m_pConnection != null ) {
                $iThread = mysqli_thread_id( $this->m_pConnection );
                mysqli_kill( $this->m_pConnection, $iThread );
                mysqli_close( $this->m_pConnection );
            }
        }
        else
            if ( $this->m_iUseCount )
                $this->m_iUseCount --;
    }
    
    /**
      * Run given query with real_escape_string applied for security
      *
      * @param $query_str - Query
      * @param - Allows for variable number of params that will be added to string
      *             Optional params may be int,float,bool,string or 1d array
      */
    function execute_query( $cQuery_str )
    {
        $fStartTime = microtime(true);
        $cQuery = '';
    
        $iNum_args = func_num_args();

        //if any args, we will use them for the query
        if($iNum_args > 1)
        {
            //skip the first arg, it is the query string
            $sArgs = func_get_args();
            $sArgs = array_slice($sArgs, 1);
            $sArgs = parent::_arrayFlatten( $sArgs );
            
            $sEscaped = array();
            foreach ($sArgs as $string)
            {
                if (gettype($string) == "object")
                {
                    writelog("ERROR: Object passed as param for query {$cQuery_str}");
                    return NULL;
                }
                $sEscaped[] = $this->m_pConnection->real_escape_string($string);
            }
     
            $cQuery = vsprintf($cQuery_str, $sEscaped);
        }
        else
        {
            //no args to deal with, assume query is complete
            $cQuery = $cQuery_str;
        }
    
        $this->m_pConnection->set_charset("UTF8");
        $pResult = $this->m_pConnection->query( $cQuery );
        $cErr    = mysqli_error($this->m_pConnection);
    
        if( $cErr != '' ) 
        {
            error_log( $cErr . ". Query =\"" . $cQuery . "\"" );
            return NULL;
        }
        
        if( $this->m_bLogQueries )
        {
            parent::_logQuery( "Query: {$cQuery}", "/var/tmp/query.log");
        }
        
        $endTime = microtime(true);
        $threshold = 0.1;
        if( $endTime - $fStartTime > $threshold )
        {
            $diff = $endTime - $fStartTime;
            parent::_logQuery("TIME OVERAGE: " . __CLASS__ . "::" . __FUNCTION__ . " took " 
                . round($diff,4) . " seconds. Query: " . $cQuery, "/var/tmp/slowQuery.log" );	
        }

        return $pResult;
    }
    
    function fetch_row( $pResult ) {
        return mysqli_fetch_array( $pResult, MYSQL_ASSOC );
    }
    
    function free_result( $pResult ) {
        if( $pResult instanceof mysqli_result )
            mysqli_free_result( $pResult );
    }
 
    function insert_id()
    {
        return $this->m_pConnection->insert_id;
    }

    function affected_rows()
    {
        return $this->m_pConnection->affected_rows;
    }
}