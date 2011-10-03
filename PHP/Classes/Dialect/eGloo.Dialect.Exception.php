<?php
namespace eGloo\Dialect;

/**
 * 
 * Top level egloo system exception class; unfortunately we cannot extend the eGloo object
 * as its better to extend the native exception class (this will be addressed with the
 * introduction of traits)
 * @author Christian Calloway
 *
 */
class Exception extends \Exception { 
	
}