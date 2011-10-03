<?php
namespace eGloo\Dialect;

/**
 * 
 * The Java.Lang.Object of eGloo - provides stubs for object behavior as well
 * as some conrete functionality, that in theory, would be shared amongst all
 * objects within the system
 * 
 * @author Christian Calloway
 *
 */
abstract class Object { 
	
	function __construct() { }
	
	/**
	 * 
	 * Copies the idea Ruby's ClassName.new() constructor; this is done to allow
	 * for object dereferencing on instantiation; will have to be overriden for
	 * signature specific constructors
	 * WARNING: This will only work in > 5.3
	 * @param mixed $arguments
	 * @return instanceofself
	 */
	public static function rnew($arguments) { 
		$className = get_called_class();
		return new $className($arguments);
	}
	
	/**
	 * 
	 * Provides equality interface for classes implementing Object
	 * @param unknown_type $object
	 */
	public function equals(\eGloo\Dialect\Object &$object) { }
	
	
	/**
	 * 
	 * Copies 'attr_reader' ruby paradigm - allows for "public" access
	 * to protected properties; will throw an exception if property
	 * is not available
	 * @param  string $name
	 * @throws Exception
	 * @return mixed
	 */
	public function __get($name) { 
		try { 
			return $this->$name();
		}
		catch (Exception $pass) {
			throw $pass;
		}		
	}
	
	/**
	 * 
	 * Copies 'attr_writer' ruby paradigm - allows for public mutation pf protected properties; 
	 * will throw an exception if property is non existent
	 * @param  String $name
	 * @param  mixed  $value
	 * @throws Exception
	 * @return void
	 */
	public function __set($name, $value) { 
		try { 
			$this->$name($value);
		}
		catch (Exception $pass) {
			throw $pass;
		}	
	}
	
	/**
	 * 
	 * Taking a step away from the java world, and into ruby, (protected) properties 
	 * will be accessed by dynamic method call "$object->propertyName($value = null)";
	 * like Ruby, this functionality can be overriden by defining a like named method 
	 * if one wishes to encapsulate business logic within an accessor/mutatator. As
	 * an added benefit, dynamic methods return a reference to self, so as to allow
	 * for chaining
	 * @param string $name
	 * @param mixed* $arguments
	 */
	public function &__call($name, $arguments) { 
		
		// determine if setter/getter - since we are setting single
		// property values, $arguments should have an value a single
		// value at the fist index
		if (count($arguments) && !is_null($arguments[0])) { 
			
			// set property, but only if it exists! since php allows you
			// to arbitrarily create public members, we are specifically
			// disallowing that behavior here - an exception is thrown if
			// an attempt to access a property that does not exist is made
			if ($this->propertyExists($name)) {
				$this->$name = $arguments[0];
			}
			
			else { 
				throw new \eGloo\Dialect\Exception(
					'ATTEMPTED TO ACCESS INVALID PROPERTY : ' . $name
				);
			}
			
			// now return a reference to the current instance so as to allow for
			// chaining: $this->name('christian')->doesWhat('sucks');	
			return $this;		
		}
		
		// otherwise we are attempting an accessor; try and return property
		// fail gracefully ? (or throw exception) if property does not exist
		else { 
			return $this->$name;
		}
	}
	
	protected function methodExists($methodName) { 
		return method_exists($this, $methodName);
	}
	
	protected function propertyExists($propertyName) { 
		return property_exists($this, $propertyName);	
	}
}