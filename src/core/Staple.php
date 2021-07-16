<?php

namespace Staple;

class Staple {

    public $config;
    public $filters;
    public $events;
    public $data;

    public function __construct() {
        $this->config = $this->loadConfig();
        $this->filters = new Filters;
        $this->events = new Events;
        $this->data = new Data;
        $this->loadDataFiles();
    }

    private function loadConfig() {
        $config = new Config();
        $userConfigFile = __DIR__ . '/../../staple.config.php';

        if ( is_readable($userConfigFile) ) {
            $userConfigFunction = require_once $userConfigFile;
            $userConfig = call_user_func_array($userConfigFunction, [$config, $this->data]);
        }

        return $config;
    }

    private function loadDataFiles() {
        $location = $this->config->inputDir . '/_data/';
        $dataFiles = glob( $location . '*.php' );

        if ( ! empty($dataFiles) ) {
            foreach ( $dataFiles as $file ) {
                $key = str_replace($location, '', rtrim($file, '.php'));
                $this->data->set($key, include $file);
            }
        }
    }

}