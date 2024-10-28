<?PHP

//
// ODBC Database wrapper
//
class ODBCConnection
{
    var $m_pConnection = null;
    var $m_iUseCount = 0;

    function __construct( $cDB = null, $cUser = null, $cPassword = null)
    {
        if ( $cDB != null )
            $this->Connect( $cDB, $cUser, $cPassword );
    }

    function Connect( $cDB, $cUser, $cPassword )
    {
        if ( $this->m_pConnection != null )
        {
            $this->m_iUseCount ++;
            return;
        }
        
        $this->m_pConnection = odbc_connect($cDB, $cUser, $cPassword );
        if($this->m_pConnection == null)
        {
            //      echo "Connection Failed\n";
            return;
        }
        
        //    echo "Connection Success\n";
        $this->m_iUseCount = 1;
        return;
    }

    function Disconnect()
    {
        if ( $this->m_iUseCount == 1 )
        {
            odbc_close($this->m_pConnection);
            unset($this->m_pConnection);
            $this->m_iUseCount = 0;
        }
        else
            if ( $this->m_iUseCount )
                $this->m_iUseCount --;
    }

    function __destructor()
    {
        $this->Disconnect();
    }

    function PreparedStatementQuery($cQuery, $args = array())
    {
        if ( $this->m_pConnection == null )
            return null;
            
        $stmt = odbc_prepare( $this->m_pConnection, $cQuery );
        return odbc_execute( $stmt, $args );
    }
  
    function Query( $pQuery )
    {
        //	echo "Query = $pQuery\n";

        if ( $this->m_pConnection == null )
            return null;
            
        return odbc_exec( $this->m_pConnection, $pQuery );
    }
}

