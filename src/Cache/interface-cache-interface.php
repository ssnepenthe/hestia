<?php
/**
 * Cache_Interface interface.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia\Cache;

use Closure;

/**
 * Defines the cache interface.
 */
interface Cache_Interface {
	/**
	 * Save the result of a closure call to the cache.
	 *
	 * @param  string  $key      The cache key.
	 * @param  int     $ttl      The cache duration in seconds.
	 * @param  Closure $callback The callback used to generate the cache value.
	 *
	 * @return mixed             The value from cache if it exists, otherwise return from $callback.
	 */
	public function remember( $key, $ttl, Closure $callback );
}
