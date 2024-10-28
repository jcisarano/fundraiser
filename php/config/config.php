<?php
/**
  * Config data customizable by server
  */

$config = Config::GetConfig();
  
class Config {

    protected $CONFIG = array();
    
    public static function GetConfig() {
        $c = new Config;
        return $c->config;
    }
    
    public function __construct() {
        $this->CONFIG["host"] = $_SERVER["HTTP_HOST"];
        
        switch( $this->CONFIG["host"] ) {
            case "server.live":
                $environment = "LIVE";
            break;
            case "server.test":
                $environment = "TEST";
            break;
            case "fundraiser.local":
                $environment = "DEV";
            break;
            default:
                throw new Exception( "Server not recognized: {$this->CONFIG["host"]}" );
                die();
        }
        
        $this->CONFIG["environment"] = $environment;
    
        //this is intended for config data that is universal and doesn't ever change
        $this->doGlobalConfig();
        
        //for config data that changes based on environment, e.g. dev/staging/live
        $this->doEnvironmentConfig( $this->CONFIG["environment"] );
        
        //use this for overrides that are based on a specific server
        $this->doLocalConfig( $this->CONFIG["host"] );
    }
    
    function doGlobalConfig() {
        $this->CONFIG["sessionCookie"] = "FUNRAISER_SESSION";
    }
    function doEnvironmentConfig( $environment ) {
        switch( strtoupper( $environment )) {
            case "LIVE":
            break;
            case "TEST":
            break;
            case "DEV":
            break;
        }
    }
    
    function doLocalConfig( $host ) {
        switch( strtolower( $host )) {
            case "server.live":
            break;
            case "server.dev":
            break;
            case "fundraiser.local":
                $this->CONFIG["db"] = (object) array(
                    "host" => "localhost",
                    "user" => "YYYYY",
                    "password" => "XXXXX",
                    "catalog" => "fundraiser"
                );
                
                $this->CONFIG["email"] = (object) array(
                    "mandrillKey" => "RRRRRR"
                );
            
            break;
        }
    }
    
    
    public function __get( $name )
    {
        switch( strtolower( $name ))
        {
            case "config":   return $this->CONFIG;
            default:
                throw new Exception( "Unknown property {$name}" );
        }
    }    

}