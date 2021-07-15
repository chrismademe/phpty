<?php

namespace Staple\Render;

use Staple\Staple;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Twig implements RenderInterface {

    private $twig;

    public function __construct(Staple $instance) {
        $this->instance = $instance;
    }

    /**
     * Render
     *
     * @param string $template Template Content
     * @param array $context Context array
     */
    public function render( string $template, array $context = [] ) {

        /**
         * The passed in template is a raw string of content, because it's
         * been parsed for Front Matter before it gets here.
         *
         * We use ArrayLoader for that, then FilesystemLoader for any other
         * templates that it might reference, like layouts or includes.
         */
        $arrayLoader = new ArrayLoader([ 'template.html' => $template ]);
        $fileLoader = new FilesystemLoader($this->instance->config->inputDir);
        $loader = new ChainLoader([$arrayLoader, $fileLoader]);
        $twig = new Environment($loader, [ 'cache' => false ]);

        return $twig->render('template.html', $context);
    }

}