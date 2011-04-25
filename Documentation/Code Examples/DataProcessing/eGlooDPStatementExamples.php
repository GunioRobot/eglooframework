<?php

$result = eGlooDPStatement::selectOnce( 'Product::getByProductID', array('product_id' => $id) );

// OR

$result = eGlooDPStatement::selectOnce( 'Product::getByProductID', $id );

// OR

$result = eGlooDPStatement::executeOnce( 'Product::getByProductID', array('product_id' => $id) );

// OR

$result = eGlooDPStatement::executeOnce( 'Product::getByProductID', $id );

// OR

$statement = new eGlooDPStatement( 'Product' );
$result = $statement->execute( 'getByProductID', array('product_id' => $id) );

// OR

$statement = new eGlooDPStatement( 'Product' );
$result = $statement->select( 'getByProductID', array('product_id' => $id) );

// OR

$statement = new eGlooDPStatement( 'Product' );
$result = $statement->select( 'getByProductID', $id );

// OR

$statement = new eGlooDPStatement( 'Product' );
$statement->bind('product_id', $id);
$result = $statement->execute( 'getByProductID' ); // Or $statement->select( 'getByProductID' );

// OR

$statement = new eGlooDPStatement();
$statement->setClass( 'Product' );
$statement->setID( 'getByProductID' );
$statement->bind('product_id', $id);
$result = $statement->execute(); // Or $statement->select();
