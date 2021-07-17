<?php

namespace Staple;

class Timer {

    private $startedAt = null;
    private $endedAt = null;

    public function __construct( $start = true ) {
        if ( $start ) $this->start();
    }

    public function start() {
        $this->startedAt = microtime(true);
    }

    public function end() {
        $this->endedAt = microtime(true);
        return $this->getResult();
    }

    private function getResult() {
        return sprintf('%s seconds', $this->endedAt - $this->startedAt);
    }

}