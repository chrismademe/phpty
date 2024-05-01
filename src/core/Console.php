<?php

namespace Staple;

class Console {

    public static function info( string $text, string $emoji = null ) {
        $emoji = $emoji ?? '🙂';
        echo $emoji . ' ' . $text . PHP_EOL;
    }

    public static function warn( string $text, string $emoji = null ) {
        self::info($text, $emoji ?? '❗️');
    }

}