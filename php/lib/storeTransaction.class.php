<?php
require_once( "baseConnect.class.php" );

class StoreTransaction extends BaseConnect{
    var $transactionId;

    function __construct ( $pDB ) {
        parent::__construct( $pDB );
    }
    
    function logTransaction() {
    }
}

class StoreTransactionFactory {
    public static Create( $pDB, $sProcessor ) {
        switch( strtolower( $sProcessor) ) {
            default:
                throw new Exception("Unknown transaction processor type: {$sProcessor}");
        }
    }
}

class StoreTransactionInformation {
    public $ship_name;
    public $ship_address;
    public $ship_city;
    public $ship_state;
    public $ship_zip;
    public $ship_country;

    public $bill_name;
    public $bill_address;
    public $bill_city;
    public $bill_state;
    public $bill_zip;
    public $bill_country;

    public $cc_num;
    public $cc_expire_month;
    public $cc_expire_year;
    public $cc_seccode;

    public $email;
    public $userid;
}