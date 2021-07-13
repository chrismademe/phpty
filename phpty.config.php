<?php

return function($config) {
    $config->addPlugin( function() {
        echo 'I plugin did things';
    } );

    $config->example = 'Example data';
};