<?php

namespace Staple\Render\Engine;

class Convert {

	private $directives;
	private $parser;
	private $render_ref;

	public function __construct( private Component $component ) {
		$this->parser = new Parser;

		/**
		 * Setup a render_ref for Children
		 */
		$this->render_ref = $this->component->name() . '_children_' . $this->component->ref();
	}

	public function convert() {

		/**
		 * If this component has directives, convert them
		 */
		$this->set_directives();

		$twig = '';

		/**
		 * If this component has Children, let's pull the content
		 * up into a variable so we can pass it to the render function
		 */
		if ( $this->component->has_children() ) {
			$twig .= '{% set ' . $this->render_ref . ' %}' . $this->parser->parse($this->component->children()) . '{% endset %}' . PHP_EOL;
		}

		/**
		 * Open the render_component function
		 */
		$twig .= sprintf( '<!-- %s -->%s', str_replace('/', '.', $this->component->name()), PHP_EOL );
		$twig .= $this->convert_directives( 'before', $twig );
		$twig .= sprintf( '{{ render_component("%s"', $this->component->name() );

		/**
		 * If this component has props, let's convert those into a Twig
		 * hash and pass them to the render function
		 */
		$twig .= $this->convert_props();

		/**
		 * Close the render_component function
		 */
		$twig .= ') }}' . PHP_EOL;
		$twig .= $this->convert_directives( 'after', $twig );
		$twig .= sprintf( '<!-- /%s -->%s', str_replace('/', '.', $this->component->name()), PHP_EOL );

		return $twig;
	}

	private function convert_props() {
		$props = [];

		if ( $this->component->has_props() ) {
			foreach ( $this->component->props() as $key => $value ) {

				/**
				 * Unless this prop is dynamic, we need to wrap the value in
				 * quotes so Twig will treat it as a string.
				 */
				if ( ! $this->component->is_prop_dynamic($key) ) {
					$value = sprintf( '"%s"', $value );
				}

				/**
				 * Remove the : from the dynamic prop key
				 */
				if ( $this->component->is_prop_dynamic($key) ) {
					$key = str_replace( ':', '', $key );
				}

				$props[] = sprintf(
					'"%s": %s',
					$key,
					$value
				);
			}
		}

		/**
		 * Add the render_ref for children if it exists
		 */
		if ( $this->component->has_children() ) {
			$props[] = sprintf(
				'"children": %s',
				$this->render_ref
			);
		}

		return empty($props)
			? ''
			: sprintf( ', { %s }', implode( ', ', $props ) );
	}

	/**
	 * Set Directives
	 *
	 * Directives are special attributes that are converted into standard
	 * Twig control structure, like if and for loops.
	 */
	private function set_directives() {

		if ( ! $this->component->has_props() ) {
			return;
		}

		foreach ( $this->component->props() as $key => $value ) {

			// If this prop is not a directive, skip it
			if ( ! $this->component->is_prop_directive( $key ) ) {
				continue;
			}

			if ( $key === '@for' ) {
				$directive = new Directives\ForLoop( $key, $value );
				$this->directives[$key] = [
					'before' => [ $directive, 'before' ],
					'after' => [ $directive, 'after' ],
					'weight' => 50,
				];
			}

			if ( $key === '@if' ) {
				$directive = new Directives\IfCondition( $key, $value );
				$this->directives[$key] = [
					'before' => [ $directive, 'before' ],
					'after' => [ $directive, 'after' ],
					'weight' => 50,
				];
			}

			if ( $key === '@else' ) {
				$directive = new Directives\ElseCondition( $key, $value );
				$this->directives[$key] = [
					'before' => [ $directive, 'before' ],
					'after' => [ $directive, 'after' ],
					'weight' => 10,
				];
			}

			/**
			 * Sort the directives by weight, lightest to heaviest
			 *
			 * This is to ensure that the directives are output in the correct order
			 */
			if ( !empty($this->directives) ) {
				uasort( $this->directives, function( $a, $b ) {
					return $a['weight'] <=> $b['weight'];
				});
			}

		}

	}

	/**
	 * Convert Directives
	 *
	 * Convert directive markup to Twig syntax
	 *
	 * @param string $method before|after
	 * @param string $twig
	 * @return string
	 */
	private function convert_directives( string $method, string $twig ) : string {
		if ( empty($this->directives) ) {
			return '';
		}

		$markup = '';

		foreach ( $this->directives as $key => $directive ) {
			$markup .= call_user_func( $directive[ $method ], $twig, $this->component );
		}

		return $markup;
	}

}