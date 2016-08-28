<?php

namespace SSNepenthe\Hestia;

use League\Plates\Engine;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Tpl {
	private $instance = null;

	protected $engine;

	protected function __construct() {
		$this->engine = new Engine( plugin_dir_path( __DIR__ ) . 'partials' );

		$this->engine->registerFunction( 'a', function( $string ) {
			return esc_attr( $string );
		} );

		$this->engine->registerFunction( 'h', function( $string ) {
			return esc_html( $string );
		} );

		$this->engine->registerFunction( 'u', function( $string ) {
			return esc_url( $string );
		} );
	}

	public static function instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	public static function render( $name, array $data ) {
		return static::$instance->render( $name, $data );
	}
}
