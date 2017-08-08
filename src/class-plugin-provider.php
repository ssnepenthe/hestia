<?php
/**
 * Shortcode_Provider class.
 *
 * @package hestia
 */

namespace Hestia;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Defines the shortcode provider class.
 */
class Plugin_Provider implements ServiceProviderInterface {
	const SHORTCODES = [
		Ancestors::class,
		Attachments::class,
		Children::class,
		Siblings::class,
		Sitemap::class,
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
			foreach ( self::SHORTCODES as $shortcode ) {
				add_shortcode(
					$shortcode::TAG,
					[ new $shortcode( $container['posts'], $container['plates'] ), 'render' ]
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
		$container['posts'] = function( Container $c ) {
			return new Posts();
		};
	}
}
