<?php

namespace Staple\Render;

use Staple\Staple;

class PHP implements RenderInterface {

    private $instance;

    public function __construct(Staple $instance) {
        $this->instance = $instance;
    }

    /**
     * Render
     *
     * @param string $file Template file path
     * @param string $content Template contents, after being parsed for Front Matter
     * @param array $context Context array
     */
    public function render( string $file, string $content, array $context = [] ) {
        extract($context);

        // Include and run the template
        ob_start();
        include $this->instance->config->inputDir . '/' . $file;
        return ob_get_clean();
    }

}