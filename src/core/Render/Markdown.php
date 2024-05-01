<?php

namespace Staple\Render;

use ParsedownExtra;
use Staple\Staple;

class Markdown implements RenderInterface {

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
        if ( $this->hasLayout($context) ) {
            $twig = new Twig($this->instance);
            $twigContent = $this->makeTwigContent($context['page']['layout'], $this->parseMarkdown($content));
            return $twig->render($file, $twigContent, $context);
        }

        return $this->parseMarkdown($content);
    }

    /**
     * Parse Markdown
     *
     * @param string $content
     * @return string
     */
    public function parseMarkdown( string $content ) {
        $parsedown = new ParsedownExtra;
        return $parsedown->text($content);
    }

    /**
     * Has Layout
     */
    private function hasLayout( array $context ) {
        return array_key_exists( 'layout', $context['page'] );
    }

    /**
     * Make Twig Content
     */
    private function makeTwigContent( string $layout, string $content ) {
        return sprintf( '<%1$s :page="page">%2$s</%1$s>', $layout, $content );
    }

}