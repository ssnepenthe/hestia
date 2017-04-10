<?php
/**
 * Shortcode_Provider class.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia\Shortcode;

use Metis\Container\Container;
use Metis\Container\Container_Aware_Trait;
use Metis\Container\Bootable_Service_Provider_Interface;

/**
 * Defines the shortcode provider class.
 */
class Shortcode_Provider implements Bootable_Service_Provider_Interface {
	use Container_Aware_Trait;

	/**
	 * List of shortcodes registered by this provider.
	 *
	 * @var array
	 */
	protected $shortcodes = [
		'ancestors',
		'attachments',
		'children',
		'siblings',
		'sitemap',
	];

	/**
	 * Class constructor.
	 *
	 * @param Container $container Container instance.
	 */
	public function __construct( Container $container ) {
		$this->set_container( $container );
	}

	/**
	 * Provider specific boot logic.
	 */
	public function boot() {
		add_action( 'init', [ $this, 'register_shortcodes' ] );
	}

	/**
	 * Provider specific registration logic.
	 */
	public function register() {
		foreach ( $this->shortcodes as $shortcode ) {
			$this->container->bind(
				'hestia.shortcode.' . $shortcode,
				function( Container $container ) use ( $shortcode ) {
					$class = __NAMESPACE__ . '\\' . ucfirst( $shortcode );

					return new $class(
						$container->make( 'metis.cache' )->transient( 'hestia' ),
						$container->make( 'metis.view' )->overridable(
							$container->make( 'hestia.dir' )
						)
					);
				}
			);
		}
	}

	/**
	 * Register all shortcodes with WordPress.
	 */
	public function register_shortcodes() {
		foreach ( $this->shortcodes as $shortcode ) {
			add_shortcode( $shortcode, [
				$this->container->make( 'hestia.shortcode.' . $shortcode ),
				'shortcode_handler',
			] );
		}
	}
}
