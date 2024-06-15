<?php

use Staple\Console;
use Staple\Timer;

/**
 * Checks to see if the file ends with a file extension
 *
 * @param string $input String to check
 */
function str_is_filename( string $input ) {
    $parts = explode('.', $input);
    return count($parts) > 1;
}

/**
 * Write File
 *
 * Writes a file but will also check and create any required directories
 */
function write_file(string $fullPathWithFileName, string $fileContents) {
    $exploded = explode( DIRECTORY_SEPARATOR, $fullPathWithFileName);

    array_pop($exploded);

    $directoryPathOnly = implode( DIRECTORY_SEPARATOR, $exploded );

    if ( ! file_exists($directoryPathOnly) ) {
        mkdir( $directoryPathOnly, 0775, true);
    }

    return file_put_contents($fullPathWithFileName, $fileContents);
}

/**
 * Fetch
 *
 * Provides a wrapper around file_get_contents for use with APIs.
 *
 * @param string $url
 * @param string $contentType Defaults to json
 */
function fetch( string $url, string $contentType = 'json' ) {
    $timer = new Timer;

    $result = @file_get_contents($url);

    if ( $contentType === 'json' ) {
        $result = json_decode($result, true);
    }

    Console::info( sprintf('Completed fetch to "%s" in %s', $url, $timer->result()), '🌍' );

    return $result;
}

/**
 * Generate Permalink
 *
 * Creates a URL friendly permalink from a file name
 *
 * @param string $filename
 * @return string
 */
function generate_permalink( string $filename ) {
    $permalinkParts = explode('.', $filename);
    array_pop($permalinkParts);
    $permalinkBare = join('.', $permalinkParts);

    // Index
    if ( $permalinkBare === 'index' ) {
        return 'index.html';

    // Filename path (e.g. something.json.php -> something.json)
    } elseif ( str_is_filename($permalinkBare) ) {
        return $permalinkBare;

    // Directory path (e.g. something.html -> something/index.html)
    } else {
        return sprintf( '%s/index.html', join('.', $permalinkParts) );
    }
}

/**
 * Remove a directory and all of its contents
 */
function remove_dir( string $src ) {
    if ( ! is_dir($src) ) return;

    $dir = opendir($src);
    while( false !== ( $file = readdir($dir) ) ) {
        if ( ( $file != '.' ) && ( $file != '..' ) ) {
            $full = $src . '/' . $file;
            if ( is_dir($full) ) {
                remove_dir($full);
            }
            else {
                unlink($full);
            }
        }
    }

    closedir($dir);
    rmdir($src);
}