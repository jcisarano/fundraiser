<?php
require_once( "config.php" );
require_once( "database.class.php" );
require_once( "salesItem.class.php" );

session_start();
$request = array_merge( $_GET, $_POST );

/*
if( !isset( $request["item_id"] ) || $request["item_id"] == "" ) {
    //TODO: Proper return system
    $sError = "Incomplete item information given";
    die( $sError );
}
*/

$iItemId = @$request["item_id"];
$iQuantity = @$request["quantity"];

$db = DatabaseFactory::Create( 
    "mysqli", 
    $config["db"]->host,
    $config["db"]->user,
    $config["db"]->password,
    $config["db"]->catalog );

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    //add an item to the basket

    if( $iQuantity <= 0 ) {
        //TODO: Proper return system
        $sError = "Invalid item quantity given";
        die( $sError );
    }

    //TODO: validate the item
    $pItem = SalesItem_Get( $db, $iItemId );
    if( $pItem == NULL ) {
        //TODO: Proper return system
        $sError = "Invalid item number given";
        die( $sError );
    }

    if( !isset( $_SESSION[ "cart_items" ] )) {
        $_SESSION[ "cart_items" ] = array();
    }

    //
    //use the itemid as the index -- it should always be unique
    //
    if( isset($_SESSION[ "cart_items" ][ $iItemId ]) ) {
        $_SESSION[ "cart_items" ][ $iItemId ] += $iQuantity;
    } else {
        $_SESSION[ "cart_items" ][ $iItemId ] = $iQuantity;
    }

    //TODO: proper result return system
    $sMessage = "Added item to cart";
    echo json_encode( array( "message"=>$sMessage ));

} else if( $_SERVER['REQUEST_METHOD'] == 'DELETE' ) {
    //remove an item from the basket

    //TODO: Make sure DELETE is supported by browsers, jQuery
    //  if not, make a separate action for remove from cart

    if( isset($_SESSION[ "cart_items" ][ $iItemId ])) {

        if( $iQuantity <= 0 )
            $iQuantity = 1;

        $_SESSION[ "cart_items" ][ $iItemId ] -= $iQuantity;
        
        //TODO: proper result return system
        $sMessage = "Updated item quantity";
        echo json_encode( array( "message"=>$sMessage ));
    } else {
        //TODO: proper result return system
        $sMessage = "Unable to update cart, invalid item info given";
        echo json_encode( array( "message"=>$sMessage ));
    }

} else if( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
    //return basket contents
    
    if( !isset( $_SESSION[ "cart_items" ] )) {
        //TODO: proper result return system
        echo json_encode(array());
        die();
    }

    $items = array();
    foreach( $_SESSION[ "cart_items" ] as $iItemId=>$iQuantity ) {
        $pItem = SalesItem_Get( $db, $iItemId );
        $pItem->field["quantity"] = $iQuantity;
        
        //TODO: Format for return
        $items[] = $pItem;
    }

    echo json_encode($pItems);
}