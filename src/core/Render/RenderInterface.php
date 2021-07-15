<?php

namespace Staple\Render;

use Staple\Staple;

interface RenderInterface {

    public function __construct( Staple $instance );
    public function render( string $template, array $context = [] );

}