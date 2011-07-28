<?php
// REMINDER:
// If someone accesses a member (maybe methods too?) that are not set, have a hook to autoload the variable via a method specified in XML

// $product = new eGloo\DP\Product();
// 
// $product->save();
// $product->setID( 1 );
// $product->unpublish();
// $product->indianGiver();
// eGloo\DP\Product::somethingCool();
// 

eGloo\DP\Product::getByProductID( 11487 );
eGloo\DP\Product::getByProductID( 11488 );

// 
// $product->foo = 1;
// $product->blah;

// eGloo\DP\Product::$blahlj; <--- not that magical apparently
