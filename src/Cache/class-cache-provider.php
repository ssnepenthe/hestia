<?php
/**
 * Cache_Provider class.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia\Cache;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Defines the cache provider class.
 */
class Cache_Provider implements ServiceProviderInterface {
	/**
	 * Provider-specific registration logic.
	 *
	 * @param  Container $container Container instance.
	 *
	 * @return void
	 */
	public function register( Container $container ) {
		$container['cache'] = function( Container $c ) {
			return new Repository( new Transient_Store( $c['wpdb'], $c['prefix'] ) );
		};
	}
}
