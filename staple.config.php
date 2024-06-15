<?php

/**
 * Staple Config
 *
 * This is where you can define configuration for your Staple
 * site. This file should return a function that accepts 2
 * parameters, which will be the $config instance and
 * the $staple instance.
 *
 * @param Staple\Config $config
 * @param Staple\Staple $staple
 */
return function( Staple\Config $config, Staple\Staple $staple ) {
    /**
     * Register a plugin with Staple
     *
     * This must be a valid callable.
     */
    $staple->addPlugin( function() use ($staple) {
        /**
         * This example plugin hooks into the "after.compile" filter
         * and adds a lang attribute to the <html> tag using the
         * configuration value defined below.
         */
        $staple->filters->add('after.compile', function(array $output) use ($staple) {
            foreach ( $output as $key => $file ) {
                if ( isset( $file['rendered'] ) ) {
                    $output[$key]['rendered'] = str_replace(
                        '<html>',
                        sprintf('<!doctype html><html lang="%s">', $staple->config->lang),
                        $file['rendered']
                    );
                }
            }

            return $output;
        });
    } );

    /**
     * Define arbitrary data as you need
     */
    $config->lang = 'en';

    /**
     * Define some files or directories to copy to the output directory
     */
    $config->addPassthroughCopy( $config->inputDir . '/assets' );
    $config->addPassthroughCopy( 'favicon.ico' );
};