<?php

namespace Phockito\VerificationMode;


use Phockito\SuccessfulVerificationResult;
use Phockito\UnsuccessfulNumericalVerificationResult;
use Phockito\VerificationContext;

abstract class NumericalVerificationMode implements VerificationMode
{
    protected $_wantedNumberOfCalls;

    function __construct($_wantedNumberOfCalls)
    {
        $this->_wantedNumberOfCalls = $_wantedNumberOfCalls;
    }

    function verify(VerificationContext $verificationContext)
    {
        $matchingCount = count($verificationContext->getMatchingInvocations());
        $success = $this->_numberOfInvocationsSatisfiesConstraint($matchingCount);

        if ($success) {
            return new SuccessfulVerificationResult();
        } else {
            return new UnsuccessfulNumericalVerificationResult($verificationContext, $this->_describeExpectation());
        }
    }

    protected abstract function _numberOfInvocationsSatisfiesConstraint($numberOfInvocations);

    protected abstract function _describeExpectation();
}