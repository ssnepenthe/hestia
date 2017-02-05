<?php

namespace SSNepenthe\Hestia;

use Pimple\Container;
use SSNepenthe\Hestia\Cache\Wp_Transient_Cache;

class Plugin extends Container {
	public function __construct( $file ) {
		parent::__construct( [ 'file' => $file ] );
	}

	public function init() {
		$this->register_services();

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
			$instance = new $class( $this['cache'] );;

			add_action( 'init', [ $instance, 'init' ] );
		}
	}

	public function register_services() {
		$this['cache'] = function( $c ) {
			return new Wp_Transient_Cache( 'hestia' );
		};
	}
}
