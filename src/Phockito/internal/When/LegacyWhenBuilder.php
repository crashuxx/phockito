<?php

namespace Phockito\internal\When;


use Phockito\Phockito;
use Phockito\WhenBuilder;

/**
 * A builder than is returned by Phockito::when to capture the methods that specify the stubbed responses
 * for a particular mocked method / arguments set
 */
class LegacyWhenBuilder implements WhenBuilder
{
    protected $instance;
    protected $class;
    protected $method;
    protected $i;

    protected $lastAction = null;

    /**
     * Store the method and args we're stubbing
     */
    private function __phockito_setMethod($method, $args)
    {
        $instance = $this->instance;
        $this->method = $method;

        if (!isset(Phockito::$_responses[$instance])) {
            Phockito::$_responses[$instance] = array();
        }
        if (!isset(Phockito::$_responses[$instance][$method])) {
            Phockito::$_responses[$instance][$method] = array();
        }

        $this->i = count(Phockito::$_responses[$instance][$method]);
        foreach (Phockito::$_responses[$instance][$method] as $i => &$matcher) {
            if (Phockito::_arguments_match($this->class, $method, $matcher['args'], $args)) {
                $this->i = $i;
                break;
            }
        }

        Phockito::$_responses[$instance][$method][$this->i] = array(
            'args' => $args,
            'steps' => array()
        );
    }

    public function __construct($instance, $class, $method = null, $args = null)
    {
        $this->instance = $instance;
        $this->class = $class;
        if ($method) {
            $this->__phockito_setMethod($method, $args);
        }
    }

    /**
     * Either record the method we're stubbing, or record the next stubbed response in the sequence if we know the stubbed method already
     *
     * To be as flexible as possible, we accept _any_ method with "return" in it as a return response, and anything with
     * throw in it as a throw response.
     */
    public function __call($called, $args)
    {
        if (!$this->method) {
            $this->__phockito_setMethod($called, $args);
        } else {
            if (count($args) !== 1) {
                user_error("$called requires exactly one argument", E_USER_ERROR);
            }
            $value = $args[0];
            $action = null;

            if (preg_match('/return/i', $called)) {
                $action = 'return';
            } else {
                if (preg_match('/throw/i', $called)) {
                    $action = 'throw';
                } else {
                    if (preg_match('/callback/i', $called)) {
                        $action = 'callback';
                    } else {
                        if ($called == 'then') {
                            if ($this->lastAction) {
                                $action = $this->lastAction;
                            } else {
                                user_error(
                                    "Cannot use then without previously invoking a \"return\", \"throw\", or \"callback\" action",
                                    E_USER_ERROR
                                );
                            }
                        } else {
                            user_error(
                                "Unknown when action $called - should contain \"return\", \"throw\" or \"callback\" somewhere in method name",
                                E_USER_ERROR
                            );
                        }
                    }
                }
            }

            Phockito::$_responses[$this->instance][$this->method][$this->i]['steps'][] = array(
                'action' => $action,
                'value' => $value
            );

            $this->lastAction = $action;
        }

        return $this;
    }

    /**
     * @param mixed|null $value
     * @return WhenBuilder
     */
    public function thenReturn($value)
    {
        return $this->__call(__FUNCTION__, [$value]);
    }

    /**
     * @param \Exception|string $exception
     * @return WhenBuilder
     */
    public function thenThrow($exception)
    {
        return $this->__call(__FUNCTION__, [$exception]);
    }

    /**
     * @param callable|\Closure $callback
     * @return WhenBuilder
     */
    public function thenCallback($callback)
    {
        return $this->__call(__FUNCTION__, [$callback]);
    }
}