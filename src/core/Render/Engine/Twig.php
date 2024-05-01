<?php

namespace Staple\Render\Engine;

use Staple\Staple;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class Twig {

	private $options = [];
	private $instance;
	private $loader;

	/**
	 * Constructor
	 *
	 * @param string $dir Path to the Twig templates directory.
	 */
	public function __construct( private Staple $staple, private string $dir, array $options = [] ) {

		// Set Options
		$this->options = array_merge( [ 'cache' => false ], $options );

		$this->loader = new FilesystemLoader( $this->dir );
		$this->instance = new Environment( $this->loader, $this->options );

		// Register Functions.
		$this->register_functions();

		// Expose the Twig instance through a hook for theme authors.
		$this->staple->events->dispatch( 'twig.init', $this->instance );

	}

	/**
	 * Render a Twig Template
	 *
	 * @param string $template Template name or raw template string.
	 * @param array $context Context to pass to the template.
	 * @param string $type Type of template to render. Either 'file' or 'string'.
	 * @return string Rendered template.
	 */
	public function render( string $template, array $context = [], string $type = 'file' ) {

		// Load the template
		if ($type === 'file') {
			$template_path = $this->loader->getSourceContext( $template )->getPath();
			$template_raw = file_get_contents( $template_path );
		} else {
			$template_raw = $template;
		}

		// Parse and Render Components
		$template_with_components = $this->parse_components( $template_raw, $context );

		// Render the view.
		return $this->instance->createTemplate($template_with_components)->render( $context );

	}

	/**
	 * Render a Component
	 *
	 * @param string $name Component name.
	 * @param array $context Context to pass to the component.
	 * @return void
	 */
	public function render_component( string $name, array $context = [] ) {

		try {
			$this->staple->filters->apply( 'component', $context );
			$this->staple->filters->apply( 'component.' . $name, $context );

			return $this->render( '_components/' . $name . '/template.twig', $context );
		} catch ( \Exception $e ) {
			throw new \Error( $e->getMessage() );
		}

	}

	/**
	 * Parse Components
	 *
	 * @param string $string
	 * @return string Rendered component HTML
	 */
	public function parse_components( string $string ) {
		$parser = new Parser;
		return $parser->parse( $string );
	}

	/**
	 * Register Functions
	 *
	 * @return void
	 */
	private function register_functions() {
		$this->staple->events->dispatch( 'twig.registerFunctions', $this->instance );

		$this->instance->addFunction(
			new TwigFunction( 'render_component', [$this, 'render_component'], [ 'is_safe' => [ 'html' ] ] )
		);

		$this->instance->addFunction(
			new TwigFunction( 'dump', 'print_r', [ 'is_safe' => [ 'html' ] ] )
		);
	}

	/**
	 * Get Option
	 *
	 * @param string $key
	 * @return mixed
	 */
	private function get_option( string $key ) {
		return $this->options[$key] ?? null;
	}

}