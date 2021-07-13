<?php

namespace PHPty;

class Console {

    public static function info( string $text, string $emoji = null ) {
        $emoji = $emoji ?? '🙂';
        echo $emoji . ' ' . $text . PHP_EOL;
    }

}