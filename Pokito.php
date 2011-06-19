<?php

/**
 * Phockito - Mockito for PHP
 *
 * Mocking framework based on Mockito for Java
 *
 * (C) 2011 Hamish Friedlander / SilverStripe. Distributable under the same license as SilverStripe.
 *
 * Example usage:
 *
 *   // Create the mock
 *   $iterator = Phockito.mock('ArrayIterator);
 *
 *   // Use the mock object - doesn't do anything, functions return null
 *   $iterator->append('Test');
 *   $iterator->asort();
 *
 *   // Selectively verify execution
 *   Phockito::verify($iterator)->append('Test');
 *   // 1 is default - can also do 2, 3  for exact numbers, or 1+ for at least one, or 0 for never
 *   Phockito::verify($iterator, 1)->asort();
 *
 * Example stubbing:
 *
 *   // Create the mock
 *   $iterator = Phockito.mock('ArrayIterator);
 *
 *   // Stub in a value
 *   Phockito::when($iterator->offsetGet(0))->return('first');
 *
 *   // Prints "first"
 *   print_r($iterator->offsetGet(0));
 *
 *   // Prints null, because get(999) not stubbed
 *   print_r($iterator->offsetGet(999));
 *
 *
 * Note that several functions are declared as public so that builder classes can access them. Anything
 * starting with an "_" is for internal consumption only
 */
class Phockito {

	/* ** INTERNAL INTERFACES START **
		These are declared as public so that mocks and builders can access them,
		but they're for internal use only, not actually for consumption by the general public
	*/

	/** Each mock instance needs a unique string ID, which we build by incrementing this counter @var int */
	public static $_instanceid_counter = 0;

	/** Array of most-recent-first calls. Each item is an array of (instance, method, args) named hashes. @var array */
	public static $_call_list = array();

	/**
	 * Array of stubs responses
	 * Nested as [instance][method][0..n], each item is an array of ('args' => the method args, 'responses' => stubbed responses)
	 * @var array
	 */
	public static $_responses = array();

	/**
	 * Array of defaults for a given class and method
	 * @var array
	 */
	public static $_defaults = array();

	/**
	 * Checks if the two argument sets (passed as arrays) match. Simple serialized check for now, to be replaced by
	 * something that can handle anyString etc matchers later
	 */
	public static function _arguments_match($mockclass, $method, $a, $b) {
		$defaults = self::$_defaults[$mockclass][$method]; $a = $a + $defaults; $b = $b + $defaults;
		
		if (count($a) != count($b)) return false;
		
		$i = count($a);
		while($i--) {
			$u = $a[$i]; $v = $b[$i];
			
			if (interface_exists('Hamcrest_Matcher') && $u instanceof Hamcrest_Matcher) {
				if (!$u->matches($v)) return false;
			}
			else {
				if (serialize($u) != serialize($v)) return false;
			}
		}
		
		return true;
	}

	/**
	 * Called by the mock instances when a method is called. Records the call and returns a response if one has been
	 * stubbed in
	 */
	public static function __called($class, $instance, $method, $args) {
		// Record the call as most recent first
		array_unshift(self::$_call_list, array(
			'class' => $class,
			'instance' => $instance,
			'method' => $method,
			'args' => $args
		));

		// Look up any stubbed responses
		if (isset(self::$_responses[$instance][$method])) {
			// Find the first one that matches the called-with arguments
			foreach (self::$_responses[$instance][$method] as $i => &$matcher) {
				if (self::_arguments_match($class, $method, $matcher['args'], $args)) {
					// Consume the next response - except the last one, which repeats indefinitely
					if (count($matcher['steps']) > 1) return array_shift($matcher['steps']);
					else return reset($matcher['steps']);
				}
			}
		}
	}

	public static function __perform_response($response) {
		if ($response['action'] == 'return') return $response['value'];
		else if ($response['action'] = 'throw') { $class = $response['value']; throw new $class(); }
		else user_error("Got unknown action {$response['action']} - how did that happen?", E_USER_ERROR);
	}

	/* ** INTERNAL INTERFACES END ** */

	/**
	 * Passed a class as a string to create the mock as, and the class as a string to mock,
	 * create the mocking class php and eval it into the current running environment
	 *
	 * @static
	 * @param bool $partial - Should test double be a partial or a full mock
	 * @param string $mockerClass - The name of the class to create the mock as
	 * @param string $mockedClass - The name of the class (or interface) to create a mock of
	 * @param bool $ignore_finals - If true, silently ignore method marked as final. If false, raise error if method marked as final encountered
	 * @return void
	 */
	protected static function build_test_double($partial, $mockerClass, $mockedClass, $ignore_finals = false) {
		// Bail if we were passed a classname that doesn't exist
		if (!class_exists($mockedClass) && !interface_exists($mockedClass)) user_error("Can't mock non-existant class $mockedClass", E_USER_ERROR);

		// Reflect on the mocked class
		$reflect = new ReflectionClass($mockedClass);

		// Build up an array of php fragments that make the mocking class definition
		$php = array();
		
		// And record the defaults at the same time
		self::$_defaults[$mockedClass] = array();

		// The only difference between mocking a class or an interface is how the mocking class extends from the mocked
		$extends = $reflect->isInterface() ? 'implements' : 'extends';
		$marker = $reflect->isInterface() ? ', Phockito_MockMarker' : 'implements Phockito_MockMarker';

		// Build the class opening stanza, including giving any instance a unique string ID
		$php[] = <<<EOT
class $mockerClass $extends $mockedClass $marker {

  public \$__phockito_class;
  public \$__phockito_instanceid;

  function __construct() {
    \$this->__phockito_class = '$mockedClass';
    \$this->__phockito_instanceid = '$mockedClass:'.(++Phockito::\$_instanceid_counter);
  }
EOT;

		// Step through every method declared on the object
		foreach ($reflect->getMethods() as $method) {
			// Skip private methods. They shouldn't ever be called anyway
			if ($method->isPrivate()) continue;

			// Either skip or throw error on final methods.
			if ($method->isFinal()) {
				if ($ignore_finals) continue;
				else user_error("Class $mockedClass has final method {$method->name}, which we can\'t mock", E_USER_WARNING);
			}

			// Get the modifiers for the function as a string (static, public, etc) - ignore abstract though, all mock methods are concrete
			$modifiers = implode(' ', Reflection::getModifierNames($method->getModifiers() & ~(ReflectionMethod::IS_ABSTRACT)));

			// PHP fragment that is the arguments definition for this method
			$defparams = array(); $callparams = array();

			// Array of defaults (sparse numeric)
			self::$_defaults[$mockedClass][$method->name] = array();
			
			foreach ($method->getParameters() as $i => $parameter) {
				// Turn the method arguments into a php fragment that calls a function with them
				$callparams[] = '$'.$parameter->getName();

				// Turn the method arguments into a php fragment the defines a function with them, including possibly the by-reference "&" and any default
				$defparam[] =
					($parameter->isPassedByReference() ? '&' : '') .
					'$'.$parameter->getName() .
					($parameter->isOptional() ? '=' . var_export($parameter->getDefaultValue(), true) : '')
				;

				// Finally cache the default value for matching against later
				if ($parameter->isOptional()) self::$_defaults[$mockedClass][$method->name][$i] = $parameter->getDefaultValue();
			}

			// Turn that array into a comma seperated list
			$defparams = implode(', ', $defparams); $callparams = implode(', ', $callparams);

			// What to do if there's no stubbed response
			$failover = $partial ? "parent::{$method->name}( $callparams )" : "null";

			// Build an overriding method that calls Phockito::__called, and never calls the parent
			$php[] = <<<EOT
  $modifiers function {$method->name}( $defparams ){
    \$backtrace = debug_backtrace();
    \$instance = \$backtrace[0]['type'] == '::' ? '::$mockedClass' : \$this->__phockito_instanceid;

    \$response = Phockito::__called('$mockedClass', \$instance, '{$method->name}', func_get_args());
  
    if (\$response) return Phockito::__perform_response(\$response);
    else return $failover;
  }
EOT;
		}

		// Close off the class definition and eval it to create the class as an extant entity.
		$php[] = '}';
		eval(implode("\n\n", $php));
	}

	/**
	 * Given a class name as a string, return a new class name as a string which acts as a mock
	 * of the passed class name. Probably not useful by itself until we start supporting static method stubbing
	 *
	 * @static
	 * @param string $class - The class to mock
	 * @param bool $ignore_finals - True if methods declared as final in the mock are silently ignored, false to throw an error
	 * @return string - The class that acts as a Phockito mock of the passed class
	 */
	static function mock_class($class, $ignore_finals = false) {
		$mockClass = '__phockito_'.$class.'_Mock';
		if (!class_exists($mockClass)) self::build_test_double(false, $mockClass, $class, $ignore_finals);

		return $mockClass;
	}

	/**
	 * Given a class name as a string, return a new instance which acts as a mock of that class
	 *
	 * @static
	 * @param string $class - The class to mock
	 * @param bool $ignore_finals - True if methods declared as final in the mock are silently ignored, false to throw an error
	 * @return Object - A mock of that class
	 */
	static function mock_instance($class, $ignore_finals = false) {
		$mockClass = self::mock_class($class, $ignore_finals);
		return new $mockClass();
	}

	/**
	 * Aternative name for mock_instance
	 */
	static function mock($class, $ignore_finals = false) {
		return self::mock_instance($class, $ignore_finals);
	}

	static function spy_class($class, $ignore_finals = false) {
		$spyClass = '__phockito_'.$class.'_Spy';
		if (!class_exists($spyClass)) self::build_test_double(true, $spyClass, $class, $ignore_finals);

		return $spyClass;
	}

	static function spy_instance($class, $ignore_finals = false) {
		$spyClass = self::spy_class($class, $ignore_finals);
		return new $spyClass();
	}

	static function spy($class, $ignore_finals = false) {
		return self::spy_instance($class, $ignore_finals);
	}

	/**
	 * When builder. Starts stubbing the method called to build the argument passed to when
	 *
	 * @static
	 * @return Phockito_WhenBuilder
	 */
	static function when($arg = null) {
		if ($arg instanceof Phockito_MockMarker) {
			return new Phockito_WhenBuilder($arg->__phockito_instanceid);
		}
		else {
			$method = array_shift(self::$_call_list);
			return new Phockito_WhenBuilder($method['instance'], $method['method'], $method['args']);
		}
	}

	/**
	 * Verify builder. Takes a mock instance and an optional number of times to verify against. Returns a
	 * DSL object that catches the method to verify
	 *
	 * @static
	 * @param Phockito_Mock $mock - The mock instance to verify
	 * @param string $times - The number of times the method should be called, either a number, or a number followed by "+"
	 * @return Phockito_VerifyBuilder
	 */
	static function verify($mock, $times = 1) {
		return new Phockito_VerifyBuilder($mock->__phockito_class, $mock->__phockito_instanceid, $times);
	}

	/**
	 * Reset a mock instance. Forget all calls and stubbed responses for a given instance
	 * @static
	 * @param Phockito_Mock $mock - The mock instance to reset
	 */
	static function reset($mock) {
		// Get the instance ID. Only resets instance-specific info ATM
		$instance = $mock->__phockito_instanceid;
		// Remove any stored returns
		unset(self::$_responses[$instance]);
		// Remove all call history
		foreach (self::$_call_list as $i => $call) {
			if ($call['instance'] == $instance) array_splice(self::$_call_list, $i, 1);
		}
	}

	/**
	 * Includes the Hamcrest matchers. You don't have to, but if you don't you can't to nice generic stubbing and verification
	 * @static
	 * @param bool $as_globals - When true (the default) the hamcrest matchers are available as global functions. If false, they're only available as static methods on Hamcrest_Matchers
	 */
	static function include_hamcrest($include_globals = true) {
		set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).'/hamcrest-php/hamcrest');
		
		if ($include_globals) require_once('Hamcrest.php');
		else require_once('Hamcrest/Matchers.php');
	}
}

/**
 * Marks all mocks for easy identification
 */
interface Phockito_MockMarker {

}

/**
 * A builder than is returned by Phockito::when to capture the methods that specify the stubbed responses
 * for a particular mocked method / arguments set
 */
class Phockito_WhenBuilder {

	protected $instance;
	protected $method;
	protected $i;

	/**
	 * Store the method and args we're stubbing
	 */
	private function __phockito_setMethod($method, $args) {
		$instance = $this->instance;
		$this->method = $method;

		if (!isset(Phockito::$_responses[$instance])) Phockito::$_responses[$instance] = array();
		if (!isset(Phockito::$_responses[$instance][$method])) Phockito::$_responses[$instance][$method] = array();

		$this->i = count(Phockito::$_responses[$instance][$method]);
		Phockito::$_responses[$instance][$method][] = array(
			'args' => $args,
			'steps' => array()
		);
	}

	function __construct($instance, $method = null, $args = null) {
		$this->instance = $instance;
		if ($method) $this->__phockito_setMethod($method, $args);
	}

	/**
	 * Either record the method we're stubbing, or record the next stubbed response in the sequence if we know the stubbed method already
	 *
	 * To be as flexible as possible, we accept _any_ method with "return" in it as a return response, and anything with
	 * throw in it as a throw response.
	 */
	function __call($called, $args) {
		if (!$this->method) {
			$this->__phockito_setMethod($called, $args);
		}
		else {
			$value = $args[0]; $action = null;

			if (preg_match('/return/i', $called)) $action = 'return';
			else if (preg_match('/throw/i', $called)) $action = 'throw';
			else user_error("Unknown when action $called - should contain return or throw somewhere in method name", E_USER_ERROR);

			Phockito::$_responses[$this->instance][$this->method][$this->i]['steps'][] = array(
				'action' => $action,
				'value' => $value
			);
		}

		return $this;
	}
}

/**
 * A builder than is returned by Phockito::verify to capture the method that specifies the verified method
 * Throws an exception if the verified method hasn't been called "$times" times, either a PHPUnit exception
 * or just an Exception if PHPUnit doesn't exist
 */
class Phockito_VerifyBuilder {

	static $exception_class = null;

	protected $class;
	protected $instance;
	protected $times;

	function __construct($class, $instance, $times) {
		$this->class = $class;
		$this->instance = $instance;
		$this->times = $times;

		if (self::$exception_class === null) {
			if (class_exists('PHPUnit_Framework_AssertionFailedError')) self::$exception_class = "PHPUnit_Framework_AssertionFailedError";
			else self::$exception_class = "Exception";
		}

	}

	function __call($called, $args) {
		$count = 0;

		foreach (Phockito::$_call_list as $call) {
			if ($call['instance'] == $this->instance && $call['method'] == $called && Phockito::_arguments_match($this->class, $called, $args, $call['args'])) {
				$count++;
			}
		}

		if (preg_match('/([0-9]+)\+/', $this->times, $match)) {
			if ($count >= (int)$match[1]) return;
		}
		else {
			if ($count == $this->times) return;
		}

		$exceptionClass = self::$exception_class;
		throw new $exceptionClass("Failed asserting that method $called called {$this->times} times");
	}
}

