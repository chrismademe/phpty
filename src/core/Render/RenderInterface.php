<?php

namespace PHPty\Render;

use PHPty\PHPty;

interface RenderInterface {

    public function __construct( PHPty $instance );
    public function render( string $template, array $context = [] );

}