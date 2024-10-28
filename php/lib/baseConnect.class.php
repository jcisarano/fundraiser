<?PHP
require_once('database.class.php');
//
// Basic create/db access functions
//

class BaseConnect {

    var $m_pDBConnection = null;

    function __construct( $pDB = null ) {
        if ( $pDB )
            $this->Connect( $pDB );
    }

    function Connect( $pDB = null, $pDSN = null, $pUser = null, $pPassword = null ) {
        if( $pDB )
        {
            $this->m_pDBConnection = $pDB;
            //$pDB->connect($pDSN, $pUser, $pPassword);
            return;
        }

        if ( $pDSN != null )
        {
            $this->m_pDBConnection = DatabaseFactory::Create("mysqli");
            $this->m_pDBConnection->connect( $pDSN, $pUser, $pPassword );
            return $this->m_pDBConnection->m_pConnection != null;
        }
    }


    function Query( $cQuery ) {
        if ( $this->m_pDBConnection == null ) 
            return null;
        
        $iNum_args = func_num_args();

        //if any args, pass them through
        if($iNum_args > 1)
        {
            //skip the first arg, it is the query string
            //TODO: flatten possible array args?
            $sArgs = func_get_args();
            $sArgs = array_slice($sArgs, 1);
            
            return $this->m_pDBConnection->execute_query( $cQuery, $sArgs );
        }
        
        return $this->m_pDBConnection->execute_query( $cQuery );
    }


    function free_result( $pResult ){ $this->m_pDBConnection->free_result( $pResult ); }
    function insert_id(){ return $this->m_pDBConnection->insert_id(); }
    
}