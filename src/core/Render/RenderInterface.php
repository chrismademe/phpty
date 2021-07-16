<?php

namespace Staple\Render;

use Staple\Staple;

interface RenderInterface {

    public function __construct( Staple $instance );
    public function render( string $file, string $content, array $context = [] );

}