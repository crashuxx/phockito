<?php

namespace Phockito;


use Hamcrest\Matcher;


class HamcrestTypeBridge
{
    /**
     * Creates a special mock of $type which wraps the given $matcher.
     *
     * @param string $type Name of the class to subtype
     * @param Matcher $matcher The matcher to proxy
     * @return object|mixed A special mock of type $type that wraps $matcher, circumventing type issues.
     */
    public static function argOfTypeThat($type, Matcher $matcher)
    {
        $mockOfType = Phockito::mock($type);
        $mockOfType->__phockito_matcher = $matcher;
        return $mockOfType;
    }
}