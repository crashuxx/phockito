<?php

namespace Phockito;


interface VerifyBuilder 
{
    function __call($called, $args);
}