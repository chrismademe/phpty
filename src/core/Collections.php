<?php

namespace Staple;

class Collections {

    public $collections = [];

    public function __construct() {}

    public function __get( string $key ) {
        return array_key_exists($key, $this->collections) ? $this->collections[$key] : null;
    }

    public function addPage( string $name, array $page ) {
        $this->collections[$name][] = $page;
    }

}