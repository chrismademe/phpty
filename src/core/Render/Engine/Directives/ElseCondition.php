<?php

namespace Staple\Render\Engine;

use Staple\Render\Engine\Component;

class ElseCondition implements DirectiveInterface {

    public function __construct( private string $key, private string $value ) {}

    public function before( string $twig, Component $component ) : string {
        return '';
    }

    public function after( string $twig, Component $component ) : string {

        // Convert markup to Twig syntax
        $markup = sprintf( '{%% else %%}{{ %s }}', $this->value );

        // Clean up the prop
        $component->remove_prop( $this->key );

        return $markup;
    }

}