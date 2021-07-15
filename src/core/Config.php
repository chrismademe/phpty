<?php

namespace Staple;

class Config {

    private $config = [];

    public function __construct() {
        $this->config = array_merge( $this->config, $this->getDefaultConfig() );
    }

    public function addPlugin( callable $plugin ) {
        $this->config['plugins'][] = $plugin;
    }

    public function addPassthroughCopy( string $path ) {

        // Make sure the file exists
        if ( !file_exists($path) ) {
            Console::warn(sprintf('File or Directory "%s" wasn\'t found, skipping copy.', $path));
            return;
        }

        $this->config['passthrough'][] = $path;
    }

    public function __set( string $key, $value ) {
        $this->config[$key] = $value;
    }

    public function __get( string $key ) {
        return array_key_exists($key, $this->config) ? $this->config[$key] : null;
    }

    protected function getDefaultConfig() {
        return [
            'inputDir' => 'input',
            'outputDir' => 'output',
            'plugins' => []
        ];
    }

}