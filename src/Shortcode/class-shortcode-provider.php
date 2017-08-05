<?php
/**
 * Shortcode_Provider class.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia\Shortcode;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Defines the shortcode provider class.
 */
class Shortcode_Provider implements ServiceProviderInterface {
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
	 * Provider-specific boot logic.
	 *
	 * @param  Container $container Container instance.
	 *
	 * @return void
	 */
	public function boot( Container $container ) {
		add_action( 'init', function() use ( $container ) {
			foreach ( $this->shortcodes as $shortcode ) {
				add_shortcode(
					$shortcode,
					[ $container[ "shortcode.{$shortcode}" ], 'render' ]
				);
			}
		} );
	}

	/**
	 * Provider-specific registration logic.
	 *
	 * @param  Container $container Container instance.
	 *
	 * @return void
	 */
	public function register( Container $container ) {
		foreach ( $this->shortcodes as $shortcode ) {
			$container[ "shortcode.{$shortcode}" ] = function( Container $c ) use ( $shortcode ) {
				$class = __NAMESPACE__ . '\\' . ucfirst( $shortcode );

				return new $class( $c['cache'], $c['plates'] );
			};
		}
	}
}
