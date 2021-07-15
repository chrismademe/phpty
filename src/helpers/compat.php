<?php

if ( !function_exists( 'str_contains' ) ) {
    function str_contains( $haystack, $needle ) {
        return strpos($haystack, $needle) !== false;
    }
}

if ( !function_exists( 'str_starts_with' ) ) {
    function str_starts_with( $haystack, $needle ) {
        $length = strlen( $needle );
        return substr( $haystack, 0, $length ) === $needle;
    }
}

if ( ! function_exists( 'str_ends_with' ) ) {
    function str_ends_with( $haystack, $needle ) {
        $length = strlen( $needle );

        if ( !$length ) {
            return true;
        }

        return substr( $haystack, -$length ) === $needle;
    }
}

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