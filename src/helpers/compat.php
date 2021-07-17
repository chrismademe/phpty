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