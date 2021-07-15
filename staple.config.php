<?php

/**
 * Staple Config
 *
 * This is where you can define configuration for your Staple
 * site. This file should return a function that accepts 1
 * parameter, which will be the $config object
 *
 * @param Staple\Config $config
 */
return function($config) {
    /**
     * Register a plugin with Staple
     *
     * This must be a valid callable.
     */
    $config->addPlugin( function() {
        echo 'I plugin did things';
    } );

    /**
     * Define some files or directories to copy to the output directory
     */
    $config->addPassthroughCopy( $config->inputDir . '/assets' );
    $config->addPassthroughCopy( 'favicon.ico' );

    /**
     * Define arbitrary data as you need
     */
    $config->example = 'Example data';
};