<?php

namespace PHPty;

class PHPty {

    public $config;
    public $filters;
    public $events;

    public function __construct() {
        $this->config = $this->loadConfig();
        $this->filters = new Filters;
        $this->events = new Events;
    }

    private function loadConfig() {
        $config = new Config();
        $userConfigFile = __DIR__ . '/../../phpty.config.php';

        if ( is_readable($userConfigFile) ) {
            $userConfigFunction = require_once $userConfigFile;
            $userConfig = call_user_func($userConfigFunction, $config);
        }

        return $config;
    }

}