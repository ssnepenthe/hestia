<?php

namespace SSNepenthe\Hestia;

use Pimple\Container;

class Plugin extends Container {
	public function __construct( $file ) {
		$this->share( 'file', (string) $file );
	}

	public function add( $id, $value ) {
		$this->offsetSet( $id, $this->factory( $value ) );
	}

	public function get( $id ) {
		return $this->offsetGet( $id );
	}

	public function init() {
		$classes = [
			Ancestors::class,
			Attachments::class,
			Children::class,
			// Descendants::class,
			// Family::class,
			Siblings::class,
			Sitemap::class,
		];

		foreach ( $classes as $class ) {
			$instance = new $class;

			add_action( 'init', [ $instance, 'init' ] );
		}
	}

	public function share( $id, $value ) {
		$this->offsetSet( $id, $value );
	}
}
