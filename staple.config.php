<?php

return function($config) {
    $config->addPlugin( function() {
        echo 'I plugin did things';
    } );

    $config->addPassthroughCopy( $config->inputDir . '/assets' );
    $config->addPassthroughCopy( 'favicon.ico' );

    $config->example = 'Example data';
};