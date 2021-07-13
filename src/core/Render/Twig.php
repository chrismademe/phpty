<?php

namespace PHPty\Render;

use PHPty\PHPty;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Twig {

    private $twig;

    public function __construct(PHPty $instance) {
        $this->instance = $instance;
    }

    /**
     * Render
     *
     * @param string $template Template Name
     * @param array $context Context array
     */
    public function render( string $template, array $context = [] ) {
        $arrayLoader = new ArrayLoader([
            'template.html' => $template
        ]);

        $fileLoader = new FilesystemLoader($this->instance->config->inputDir);

        $twig = new Environment(new ChainLoader([$arrayLoader, $fileLoader]), [ 'cache' => false ]);

        return $twig->render('template.html', $context);
    }

}