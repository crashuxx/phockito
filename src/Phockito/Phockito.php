<?php

namespace Phockito;


use Hamcrest\Matcher;
use Phockito\internal\Clazz\ClazzFactory;
use Phockito\internal\Clazz\MethodFactory;
use Phockito\internal\Clazz\ParameterFactory;
use Phockito\internal\Context\LegacyContext;
use Phockito\internal\Context\LegacyMockContext;
use Phockito\internal\Context\LegacySpyContext;
use Phockito\internal\EnhancedClazz;
use Phockito\internal\Marker\MockMarker;
use Phockito\internal\Verify\LegacyVerifyBuilder;
use Phockito\internal\When\LegacyWhenBuilder;
use Phockito\internal\Writer\DefaultWriter;
use Phockito\VerificationMode\AtLeast;
use Phockito\VerificationMode\AtMost;
use Phockito\VerificationMode\NoMoreInteractions;
use Phockito\VerificationMode\Only;
use Phockito\VerificationMode\Times;
use Phockito\VerificationMode\VerificationMode;
use ReflectionClass;


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
class Phockito
{
    const MOCK_PREFIX = '__phockito_';

    /* ** Static Configuration *
        Feel free to change these at any time.
    */

    /**
     * If true, don't warn when doubling classes with final methods, just ignore the methods. If false, throw warnings when final methods encountered
     * @deprecated
     * @var bool
     */
    public static $ignore_finals = true;

    /**
     * Class name of a class with a static "register_double" method that will be called with any double to inject into some other type tracking system
     * @deprecated
     * @var string
     */
    public static $type_registrar = null;

    /* ** INTERNAL INTERFACES START **
        These are declared as public so that mocks and builders can access them,
        but they're for internal use only, not actually for consumption by the general public
    */

    /**
     * Each mock instance needs a unique string ID, which we build by incrementing this counter
     * @deprecated
     * @var int
     */
    public static $_instanceid_counter = 0;

    /**
     * Array of most-recent-first calls. Each item is an array of (instance, method, args) named hashes.
     * @deprecated
     * @var Invocation[]
     */
    public static $_invocation_list = array();

    /**
     * Array of stubs responses
     * Nested as [instance][method][0..n], each item is an array of ('args' => the method args, 'responses' => stubbed responses)
     * @deprecated
     * @var array
     */
    public static $_responses = array();

    /**
     * Array of defaults for a given class and method
     * @deprecated
     * @var array
     */
    public static $_defaults = array();

    /**
     * Records whether a given class is an interface, to avoid repeatedly generating reflection objects just to re-call type registrar
     * @deprecated
     * @var array
     */
    public static $_is_interface = array();

    /**
     * Checks if the two argument sets (passed as arrays) match. Simple serialized check for now, to be replaced by
     * something that can handle anyString etc matchers later
     *
     * @deprecated
     * @param $mockclass
     * @param $method
     * @param $a
     * @param $b
     * @return bool
     */
    public static function _arguments_match($mockclass, $method, $a, $b)
    {
        // See if there are any defaults for the given method
        if (isset(self::$_defaults[$mockclass][$method])) {
            // If so, get them
            $defaults = self::$_defaults[$mockclass][$method];
            // And merge them with the passed args
            $a = $a + $defaults;
            $b = $b + $defaults;
        }

        // If two argument arrays are different lengths, automatic fail
        if (count($a) > count($b)) {
            return false;
        }

        // Step through each item
        $i = count($a);
        while ($i--) {
            $u = $a[$i];
            $v = $b[$i];

            // If the argument in $a is a hamcrest matcher, call match on it. WONTFIX: Can't check if function was passed a hamcrest matcher
            if (interface_exists(Matcher::class) && ($u instanceof Matcher || isset($u->__phockito_matcher))
            ) {
                // The matcher can either be passed directly, or wrapped in a mock (for type safety reasons)
                $matcher = null;
                if ($u instanceof Matcher) {
                    $matcher = $u;
                } elseif (isset($u->__phockito_matcher)) {
                    $matcher = $u->__phockito_matcher;
                }
                if ($matcher != null && !$matcher->matches($v)) {
                    return false;
                }
            } // Otherwise check for equality by checking the equality of the serialized version
            else {
                if (serialize($u) != serialize($v)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Called by the mock instances when a method is called. Records the call and returns a response if one has been
     * stubbed in
     *
     * @deprecated
     * @param $class
     * @param $instance
     * @param $method
     * @param $args
     * @return mixed|null
     */
    public static function __called($class, $instance, $method, $args)
    {
        // Record the call as most recent first
        $invocation = new Invocation($class, $instance, $method, $args, debug_backtrace(0));
        array_unshift(self::$_invocation_list, $invocation);

        // Look up any stubbed responses
        if (isset(self::$_responses[$instance][$method])) {
            // Find the first one that matches the called-with arguments
            foreach (self::$_responses[$instance][$method] as &$matcher) {
                if (self::_arguments_match($class, $method, $matcher['args'], $args)) {
                    // Consume the next response - except the last one, which repeats indefinitely
                    if (count($matcher['steps']) > 1) {
                        return array_shift($matcher['steps']);
                    } else {
                        return reset($matcher['steps']);
                    }
                }
            }
        }

        return null;
    }

    /**
     * @deprecated
     * @noinspection PhpInconsistentReturnPointsInspection
     * @param $response
     * @param $args
     * @return mixed|null
     * @throws \Exception
     */
    public static function __perform_response($response, $args)
    {
        if ($response['action'] == 'return') {
            return $response['value'];
        } else {
            if ($response['action'] == 'throw') {
                /** @var \Exception $class */
                $class = $response['value'];
                throw (is_object($class) ? $class : new $class());
            } else {
                if ($response['action'] == 'callback') {
                    return call_user_func_array($response['value'], $args);
                } else {
                    user_error("Got unknown action {$response['action']} - how did that happen?", E_USER_ERROR);
                }
            }
        }

        return null;
    }

    /* ** INTERNAL INTERFACES END ** */

    /**
     * Passed a class as a string to create the mock as, and the class as a string to mock,
     * create the mocking class php and eval it into the current running environment
     *
     * @deprecated
     * @static
     * @param bool $partial - Should test double be a partial or a full mock
     * @param string $mockedClass - The name of the class (or interface) to create a mock of
     * @return EnhancedClazz The name of the mocker class
     */
    protected static function build_test_double($partial, $mockedClass)
    {
        // Bail if we were passed a classname that doesn't exist
        if (!class_exists($mockedClass) && !interface_exists($mockedClass)) {
            user_error(
                "Can't mock non-existent class $mockedClass",
                E_USER_ERROR
            );
        }

        // Reflect on the mocked class
        $reflect = new ReflectionClass($mockedClass);

        if ($reflect->isFinal()) {
            user_error("Can't mock final class $mockedClass", E_USER_ERROR);
        }

        $classFactory = new ClazzFactory(new MethodFactory(new ParameterFactory()));

        $clazz = $classFactory->createFromReflectionClass($reflect);

        $writer = new DefaultWriter();

        $mockerClass = $reflect->getShortName() . md5(rand(0, 100000));

        if ($reflect->getNamespaceName()) {
            $writer->writeNamespace($reflect->getNamespaceName());
        }

        if ($clazz->isInterface()) {
            $writer->writeInterfaceExtend($mockerClass, $reflect->getShortName(), MockMarker::class);
        } else {
            $writer->writeClassExtend($mockerClass, $reflect->getShortName(), MockMarker::class);
        }

        foreach ($clazz->getMethods() as $method) {
            if (!strcasecmp('__construct', $method->getName()) || !strcasecmp($reflect->getShortName(), $method->getName())) {
            } else if (!strcasecmp('__toString', $method->getName())) {
            } else if (!strcasecmp('__call', $method->getName())) {
            } else if ($method->isFinal() && !self::$ignore_finals) {
                user_error('Class ' . $mockedClass . ' has final method ' . $method->getName() . ', which we can\'t mock', E_USER_WARNING);
            } else if (!$method->isFinal()) {
                $writer->writeMethod($method);
            }
        }

        $writer->writeToStringMethod();
        $writer->writeCallMethod();
        $writer->writeClose();

        eval($writer->build());

        $fullMockClass = trim('\\' . $reflect->getNamespaceName() . '\\' . $mockerClass, '\\');
        return new EnhancedClazz($fullMockClass, $clazz);
    }

    /**
     * Alternative name for mock_instance
     *
     * @param $class
     * @return Object
     */
    public static function mock($class)
    {
        $enhancedClazz = self::build_test_double(false, $class);
        return $enhancedClazz->newInstance(new LegacyMockContext($enhancedClazz->getClazz()));
    }

    public static function spy($object)
    {
        $enhancedClazz = self::build_test_double(false, get_class($object));
        return $enhancedClazz->newInstance(new LegacySpyContext($enhancedClazz->getClazz(), $object));
    }

    /**
     * When builder. Starts stubbing the method called to build the argument passed to when
     *
     * @static
     * @param MockMarker|object|mixed|null $arg
     * @return WhenBuilder|Object
     */
    static function when($arg = null)
    {
        if ($arg instanceof MockMarker) {
            /** @vat \Phockito\internal\Marker\MockMarker $arg */
            $context = $arg->__phockito_context;

            if ($context instanceof LegacyContext) {
                return new LegacyWhenBuilder($context->getPhockitoInstanceId(), $context->getClazz()->getName());
            }
        }

        /** @var Invocation $invocation */
        $invocation = array_shift(self::$_invocation_list);
        return new LegacyWhenBuilder($invocation->instanceId, $invocation->className, $invocation->methodName, $invocation->args);
    }

    /**
     * Verify builder. Takes a mock instance and an optional number of times to verify against. Returns a
     * DSL object that catches the method to verify
     *
     * @static
     * @param MockMarker|object $mock - The mock instance to verify
     * @param string|int $times - The number of times the method should be called, either a number, or a number followed by "+"
     * @return mixed|VerifyBuilder
     */
    static function verify($mock, $times = 1)
    {
        if ($mock instanceof MockMarker) {
            /** @vat \Phockito\internal\Marker\MockMarker $arg */
            $context = $mock->__phockito_context;

            if ($context instanceof LegacyContext) {
                return new LegacyVerifyBuilder($context->getPhockitoInstanceId(), $times);
            }
        }

        return new LegacyVerifyBuilder($mock->__phockito_instanceid, $times);
    }

    /**
     * @param int $times
     * @return VerificationMode
     */
    static function times($times)
    {
        return new Times($times);
    }

    /**
     * @return VerificationMode
     */
    static function never()
    {
        return self::times(0);
    }

    /**
     * @param int $times
     * @return VerificationMode
     */
    static function atLeast($times)
    {
        return new AtLeast($times);
    }

    /**
     * @return VerificationMode
     */
    static function atLeastOnce()
    {
        return self::atLeast(1);
    }

    /**
     * @param int $times
     * @return VerificationMode
     */
    static function atMost($times)
    {
        return new AtMost($times);
    }

    static function only()
    {
        return new Only();
    }

    /**
     * Reset a mock instance. Forget all calls and stubbed responses for a given instance
     * @static
     * @param MockMarker|object $mock - The mock instance to reset
     * @param string $method
     */
    static function reset($mock, $method = null)
    {
        // Get the instance ID. Only resets instance-specific info ATM
        if ($mock instanceof MockMarker) {
            /** @vat \Phockito\internal\Marker\MockMarker $arg */
            $context = $mock->__phockito_context;

            if ($context instanceof LegacyMockContext) {
                $instance = $context->getPhockitoInstanceId();
            }
        } else {
            throw new \InvalidArgumentException('Argument "$mock" must be instance of ' . MockMarker::class);
        }

        // Remove any stored returns
        if ($method) {
            unset(self::$_responses[$instance][$method]);
        } else {
            unset(self::$_responses[$instance]);
        }

        // Remove all call history
        /** @var Invocation $invocation */
        foreach (self::$_invocation_list as $i => $invocation) {
            if (($method && $invocation->matchesInstanceAndMethod($instance, $method)) ||
                ($method == null && $invocation->matchesInstance($instance))
            ) {
                array_splice(self::$_invocation_list, $i, 1);
            }
        }
    }

    /**
     * @param MockMarker|Object|array $mocks
     */
    static function verifyNoMoreInteractions($mocks)
    {
        if (!is_array($mocks)) {
            $mocks = array($mocks);
        }

        $noMoreInteractionsVerificationMode = new NoMoreInteractions();

        foreach ($mocks as $mock) {
            if ($mock instanceof MockMarker) {
                /** @vat \MockMarker $arg */
                $context = $mock->__phockito_context;

                if ($context instanceof LegacyMockContext) {
                    $instance = $context->getPhockitoInstanceId();
                }
            } else {
                throw new \InvalidArgumentException('Argument of array "$mocks" contains invalid object, "' . MockMarker::class . '" required~');
            }

            $verificationContext = new VerificationContext($instance, null, array());
            $verificationResult = $noMoreInteractionsVerificationMode->verify($verificationContext);
            if ($verificationResult instanceof UnsuccessfulVerificationResult) {
                (new UnsuccessfulVerificationReporter())->reportUnsuccessfulVerification($verificationResult);
            }
        }
    }
}





