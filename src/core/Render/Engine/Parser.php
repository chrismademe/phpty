<?php

namespace Staple\Render\Engine;

class Parser {

	private $match_components;
	private $match_attributes;

	/**
	 * Parser constructor.
	 *
	 * @param array $options
	 */
	public function __construct( private array $options = [] ) {

		$this->match_components = '/<([A-Z][\w\.-]*)\s*([^>]*?)(\/>|>(.*?)<\/\1>)/s';
		$this->match_attributes = '/\s*([a-zA-Z0-9-_:.@-]+)\s*(?:=\s*(?:"([^"]*)"|{([^}]*)}))?/';

		$this->options = array_merge( [
			'ignore' => ['InnerBlocks'],
		], $this->options );

	}

	/**
	 * Parse Components
	 *
	 * @param string $string
	 * @return string Rendered component HTML
	 */
	public function parse( string $string ) {

		$result = preg_replace_callback( $this->match_components, function ( $matches ) {

			// Is self-closing?
			$is_self_closing = $matches[3] === '/>';

			// Get the component name
			$name = $this->get_component_name( $matches[1] );

			// If the name is in the ignore list, return the original string
			if ( is_array( $this->options['ignore'] ) && in_array( $name, $this->options['ignore'] ) ) {
				return $matches[0];
			}

			// Setup Component
			$component = new Component( $name );

			// matches[2] contains the attributes, parse them if they exist
			if ( ! empty( $matches[2] ) ) {
				$component->set_props( $this->parse_attributes( $matches[2] ) );
			}

			/**
			 * If the component has nested content, $matches[4] will contain that.
			 * We render that and pass it to the parent component as a "children" prop.
			 *
			 * This step is skipped for self-closing components
			 */
			if ( ! $is_self_closing && ! empty( $matches[4] ) ) {
				$component->set_children( $matches[4] );
			}

			// Return the rendered component
			return $component->render();
		}, $string);

		return $result;

	}

	/**
	 * Parse Attributes
	 *
	 * @param string $string
	 * @return array
	 */
	public function parse_attributes( string $string ) {
		$attributes = [];

		if ( ! empty( $string ) ) {
			preg_match_all( $this->match_attributes, $string, $matches, PREG_SET_ORDER );

			/**
			 * Push each attribute into the $attributes array
			 * If an attribute has no value, set it to true
			 */
			foreach ( $matches as $attr ) {

				// If the attribute has a value, set it to that, otherwise set it to true
				$value = $attr[2] ?? true;
				$attributes[ $attr[1] ] = $value;

			}
		}

		return $attributes;
	}

	/**
	 * Get Component Name
	 *
	 * @param string $name
	 * @return string
	 */
	public function get_component_name( string $name ) {

		// Replace . with a / to indicate a sub component
		$name = str_replace( '.', '/', $name );

		return $name;

	}

}