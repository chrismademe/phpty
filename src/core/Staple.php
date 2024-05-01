<?php

namespace Staple;

class Staple {

    public $plugins;
    public $config;
    public $filters;
    public $events;
    public $data;

    public function __construct() {
        $this->config = $this->loadConfig();
        $this->filters = new Filters;
        $this->events = new Events;
        $this->data = new Data;
        $this->runPlugins();
        $this->loadDataFiles();
    }

    private function loadConfig() {
        $config = new Config();
        $userConfigFile = __DIR__ . '/../../staple.config.php';

        if ( is_readable($userConfigFile) ) {
            $userConfigFunction = require_once $userConfigFile;
            $userConfig = call_user_func_array($userConfigFunction, [$config, $this]);
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

    private function runPlugins() {
        if ( $this->hasPlugins() ) {
            foreach ( $this->plugins as $plugin ) {
                call_user_func($plugin, $this);
            }
        }
    }

    public function addPlugin( callable $plugin ) {
        $this->plugins[] = $plugin;
    }

    public function hasPlugins() {
        return is_array($this->plugins) && !empty($this->plugins);
    }

}