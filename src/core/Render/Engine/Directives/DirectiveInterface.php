<?php

namespace Staple\Render\Engine\Directives;

use Staple\Render\Engine\Component;

interface DirectiveInterface {

    /**
     * Insert markup before a component renders
     *
     * @param string $twig
     * @param Component $component
     * @return string
     */
    public function before( string $twig, Component $component ) : string;

    /**
     * Insert markup after a component renders
     *
     * @param string $twig
     * @param Component $component
     * @return string
     */
    public function after( string $twig, Component $component ) : string;

}