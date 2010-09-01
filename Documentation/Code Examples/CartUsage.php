<?php

$discountLineItem	= new DiscountLineItem( -10 );
$productLineItem	= new ProductLineItem( 25 );
$shippingLineItem	= new ShippingLineItem( 5 );
$taxLineItem		= new TaxLineItem( 2 );

$cartItem = new CartItem();

$cartItem->addDiscountLineItem($discountLineItem);
$cartItem->addProductLineItem($productLineItem);
$cartItem->addShippingLineItem($shippingLineItem);
$cartItem->addTaxLineItem($taxLineItem);

echo $cartItem->getSubTotal() . "<br />";

$cart = new Cart();
echo $cart->getTotal( CurrencyExchange::USD, false ) . "<br />";

$cart->addCartItem($cartItem);
echo $cart->getTotal( CurrencyExchange::USD, false ) . "<br />";

$cart->addCartItem($cartItem);
echo $cart->getTotal( CurrencyExchange::EUR, false ) . "<br />";

$cart->addCartItem($cartItem);
echo $cart->getTotal( CurrencyExchange::JPY, false ) . "<br />";
