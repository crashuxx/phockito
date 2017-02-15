<?php

namespace Phockito;

use Phockito\internal\Marker\MockMarker;

/**
 * @param MockMarker|object|array $mocks
 */
function verifyNoMoreInteractions($mocks) {
    Phockito::verifyNoMoreInteractions($mocks);
}
