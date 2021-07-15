<?php

namespace PHPty\Render;

use PHPty\PHPty;

class PHP implements RenderInterface {

    public function __construct(PHPty $instance) {
        $this->instance = $instance;
    }

    /**
     * Render
     *
     * @param string $template Template Content or File
     * @param array $context Context array
     */
    public function render( string $template, array $context = [] ) {
        extract($context);

        // Include and run the template
        ob_start();
        include $this->instance->config->inputDir . '/' . $template;
        return ob_get_clean();
    }

}