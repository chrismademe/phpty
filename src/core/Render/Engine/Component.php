<?php

namespace Staple\Render\Engine;

class Component {

    private $ref;
    private $children;
    private $props;

    /**
     * Component Constructor
     *
     * @param string $name
     */
    public function __construct( private string $name ) {

        /**
         * Set a unique instance reference to avoid collisions with multiple
         * components in a template.
         */
        $this->ref = uniqid();

        $this->children = '';
        $this->props = [];
    }

	/**
	 * Render a component to valid Twig syntax
	 */
	public function render() {

        /**
         * Convert to Twig
         */
        $converter = new Convert( $this );
        return $converter->convert();

    }

    /**
     * Get Component Name
     */
    public function name() {
        return $this->name;
    }

    /**
     * Get Component Ref
     */
    public function ref() {
        return $this->ref;
    }

    /**
     * Get Prop
     */
    public function prop( string $name ) {
        return $this->has_prop($name) ? $this->props[ $name ] : null;
    }

    /**
     * Get Props
     */
    public function props() {
        return $this->props;
    }

    /**
     * Has Prop
     */
    public function has_prop( string $name ) {
        return isset( $this->props[ $name ] );
    }

    /**
     * Has Props
     */
    public function has_props() {
        return ! empty( $this->props );
    }

    /**
     * Set Prop
     *
     * @param string $name
     * @param mixed $value
     */
    public function set_prop( string $name, $value ) {
        $this->props[ $name ] = $value;
    }

    /**
     * Set Props
     */
    public function set_props( array $props ) {
        $this->props = $props;
    }

    /**
     * Remove Prop
     */
    public function remove_prop( string $name ) {
        if ( $this->has_prop($name) ) {
            unset( $this->props[ $name ] );
        }
    }

    /**
     * Get Children
     */
    public function children() {
        return $this->children;
    }

    /**
     * Set Children
     *
     * @param mixed $value
     */
    public function set_children( string $value ) {
        $this->children = $value;
    }

    /**
     * Has Children
     */
    public function has_children() {
        return !empty( $this->children );
    }

    /**
     * Is Dynamic Prop
     */
    public function is_prop_dynamic( string $name ) {
        $prop = $this->prop( $name );
        return $this->has_prop($name) && $name[0] === ':';
    }

    /**
     * Is Directive Prop
     */
    public function is_prop_directive( string $name ) {
        $prop = $this->prop( $name );
        return $this->has_prop($name) && $name[0] === '@';
    }

}