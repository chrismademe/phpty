<?php

namespace Staple\Render\Engine;

use Staple\Render\Engine\Component;

class ForLoop implements DirectiveInterface {

    public function __construct( private string $key, private string $value ) {}

    public function before( string $twig, Component $component ) : string {

        // Convert markup to Twig syntax
        $markup = sprintf( '{%% for %s %%}', $this->value );

        // Clean up the prop
        $component->remove_prop( $this->key );

        return $markup;
    }

    public function after( string $twig, Component $component ) : string {
        return '{% endfor %}';
    }

    /**
     * Get the key from the directive value
     *
     * This is used to determine the prop that should be passed to the component
     * inside the for loop.
     *
     * If it's a simple value (1 word) we take that. If it's formatted with a period,
     * we take the second word.
     */
    private function get_prop_name_from_value( string $value ) {

        $name = strtok($value, ' ');

        if ( strpos($name, '.') !== false ) {
            $parts = explode('.', $name);
            $name = $parts[1];
        }

        return $name;
    }

}