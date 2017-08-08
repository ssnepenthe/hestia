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
class Shortcode_Provider implements ServiceProviderInterface {
	/**
	 * Provider-specific boot logic.
	 *
	 * @param  Container $container Container instance.
	 *
	 * @return void
	 */
	public function boot( Container $container ) {
		add_action( 'init', function() use ( $container ) {
			add_shortcode( 'ancestors', [ $container['shortcode.ancestors'], 'render' ] );
			add_shortcode( 'attachments', [ $container['shortcode.attachments'], 'render' ] );
			add_shortcode( 'children', [ $container['shortcode.children'], 'render' ] );
			add_shortcode( 'siblings', [ $container['shortcode.siblings'], 'render' ] );
			add_shortcode( 'sitemap', [ $container['shortcode.sitemap'], 'render' ] );
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
		$container['shortcode.ancestors'] = function( Container $c ) {
			return new Ancestors( new Posts(), $c['plates'] );
		};
		$container['shortcode.attachments'] = function( Container $c ) {
			return new Attachments( new Posts(), $c['plates'] );
		};
		$container['shortcode.children'] = function( Container $c ) {
			return new Children( new Posts(), $c['plates'] );
		};
		$container['shortcode.siblings'] = function( Container $c ) {
			return new Siblings( new Posts(), $c['plates'] );
		};
		$container['shortcode.sitemap'] = function( Container $c ) {
			return new Sitemap( new Posts(), $c['plates'] );
		};
	}
}
