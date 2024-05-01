<?php

namespace Staple;

class Staple {

    public $plugins;
    public $config;
    public $filters;
    public $events;
    public $data;

    /**
     * Constructor
     */
    public function __construct() {
        $this->config = $this->loadConfig();
        $this->filters = new Filters;
        $this->events = new Events;
        $this->data = new Data;
        $this->runPlugins();
        $this->loadDataFiles();
    }

    /**
     * Load the configuration file.
     */
    private function loadConfig() {
        $config = new Config();
        $userConfigFile = __DIR__ . '/../../staple.config.php';

        if ( is_readable($userConfigFile) ) {
            $userConfigFunction = require_once $userConfigFile;
            $userConfig = call_user_func_array($userConfigFunction, [$config, $this]);
        }

        return $config;
    }

    /**
     * Load data files from the _data directory.
     */
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

    /**
     * Run any plugins that have been registered.
     */
    private function runPlugins() {
        if ( $this->hasPlugins() ) {
            foreach ( $this->plugins as $plugin ) {
                call_user_func($plugin, $this);
            }
        }
    }

    /**
     * Add a plugin to the Staple instance.
     *
     * @param callable $plugin
     */
    public function addPlugin( callable $plugin ) {
        $this->plugins[] = $plugin;
    }

    /**
     * Check if there are any plugins registered.
     */
    public function hasPlugins() {
        return is_array($this->plugins) && !empty($this->plugins);
    }

}