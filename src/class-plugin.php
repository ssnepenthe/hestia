<?php

namespace SSNepenthe\Hestia;

use Pimple\Container;
use SSNepenthe\Hestia\Template\Template;
use SSNepenthe\Hestia\Cache\Wp_Transient_Cache;
use SSNepenthe\Hestia\Template\Dir_Template_Locator;
use SSNepenthe\Hestia\Template\Core_Template_Locator;
use SSNepenthe\Hestia\Template\Template_Locator_Stack;

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
			$instance = new $class( $this['cache'], $this['template'] );

			add_action( 'init', [ $instance, 'init' ] );
		}
	}

	public function register_services() {
		$this['cache'] = function( $c ) {
			return new Wp_Transient_Cache( 'hestia' );
		};

		$this['template'] = function( $c ) {
			return new Template( $c['template.locator_stack'] );
		};

		$this['template.core_locator'] = function( $c ) {
			return new Core_Template_Locator;
		};

		$this['template.dir_locator'] = function( $c ) {
			return new Dir_Template_Locator( plugin_dir_path( $c['file'] ) );
		};

		$this['template.locator_stack'] = function( $c ) {
			return new Template_Locator_Stack( [
				$c['template.core_locator'],
				$c['template.dir_locator'],
			] );
		};
	}
}
