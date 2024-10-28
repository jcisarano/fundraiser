<?php
require_once("mysqli.class.php");
require_once("odbc.class.php");
/*
 */

class Database{
    var $m_pConnection = null;
    var $m_iUseCount = 0;
    var $m_bLogQueries = true;

    function __construct( $cDB = null, $cUser = null, $cPassword = null, $cCatalog = null ) {
        if ( $cDB != null )
            $this->connect( $cDB, $cUser, $cPassword, $cCatalog );
    }
    
    function __destruct(){
        $this->disconnect();
    }
    
    function connect( $cDB, $cUser, $cPassword, $cCatalog=null ) {}
    function disconnect() {}
    
    /**
      * Run given query with real_escape_string applied for security
      *
      * @param $query_str - Query
      * @param - Allows for variable number of params that will be added to string
      *             Optional params may be int,float,bool,string or 1d array
      */
    function execute_query( $cQuery_str ) {}
    function insert_id() {}
    function affected_rows() {}
    function free_result($pResult){}
    function fetch_row( $pResult ){}
    
    protected function _logQuery( $cString, $cFilePath ) {
        file_put_contents( $cFilePath,
            date("D M d H:i:s Y") . ": " . $cString . "\n",
            FILE_APPEND );
    }
    
    protected function _arrayFlatten($array) {
        $flat = array();
        
        foreach( $array as $aa ) {
            if( is_array( $aa ) ) {
                $flat = array_merge($flat,$aa);
                //$flat = array_merge($flat, $this->_arrayFlatten( $aa ));
            }
            else {
                $flat[] = $aa;
            }
        }
        
        return $flat;
    }
}

class DatabaseFactory {
    public static function Create( 
        $type="mysqli", 
        $cDB = null, 
        $cUser = null, 
        $cPassword = null, 
        $cCatalog = null ) {
            switch( strtolower( $type ) ) {
                case "mysqli": return new MySqliConnection($cDB, $cUser, $cPassword, $cCatalog );
                default:
                    throw new Exception("Unknown database type: {$type}");
        }
    
    }
}