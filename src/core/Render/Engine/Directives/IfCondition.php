<?php

namespace Staple\Render\Engine;

use Staple\Render\Engine\Component;

class IfCondition implements DirectiveInterface {

    public function __construct( private string $key, private string $value ) {}

    public function before( string $twig, Component $component ) : string {

        // Convert markup to Twig syntax
        $markup = sprintf( '{%% if %s %%}', $this->value );

        // Clean up the prop
        $component->remove_prop( $this->key );

        return $markup;
    }

    public function after( string $twig, Component $component ) : string {
        return '{% endif %}';
    }

}