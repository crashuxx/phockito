<?php

namespace Phockito;


class UnsuccessfulNoMoreInteractionsVerificationResult implements UnsuccessfulVerificationResult
{
    /** @var LegacyInvocation */
    private $_invocation;

    function __construct(LegacyInvocation $_invocation)
    {
        $this->_invocation = $_invocation;
    }

    /**
     * @return string
     */
    function describeConstraintFailure()
    {
        $backtraceFormatter = new BacktraceFormatter();
        return "No more interactions wanted, but found this interaction:\n" .
        $backtraceFormatter->formatBacktrace($this->_invocation->backtrace);
    }
}