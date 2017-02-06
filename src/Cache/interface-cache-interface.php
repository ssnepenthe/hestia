<?php
/**
 * Cache provider interface.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia\Cache;

use Closure;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Cache provider interface.
 */
interface Cache_Interface {
	/**
	 * Delete all relevant cache entries.
	 */
	public function flush();

	/**
	 * Remove an entry from the cache.
	 *
	 * @param  string $key The cache key.
	 *
	 * @return bool
	 */
	public function forget( $key );

	/**
	 * Get an entry from the cache if it exists.
	 *
	 * @param  string $key The cache key.
	 *
	 * @return mixed
	 */
	public function get( $key );

	/**
	 * Check whether an entry exists in the cache.
	 *
	 * @param  string $key The cache key.
	 *
	 * @return bool
	 */
	public function has( $key );

	/**
	 * Put a value into the cache.
	 *
	 * @param  string $key     The cache key.
	 * @param  mixed  $value   The value to put in the cache.
	 * @param  int    $seconds Number of seconds the entry is valid for.
	 *
	 * @return bool
	 */
	public function put( $key, $value, $seconds = 0 );

	/**
	 * Retrieves a value from the cache if it exists, otherwise calls a callback and
	 * puts its return value into the cache.
	 *
	 * @param  string  $key      The cache key.
	 * @param  int     $seconds  Number of seconds the entry is valid for.
	 * @param  Closure $callback Callback to generate the cache value.
	 *
	 * @return mixed
	 */
	public function remember( $key, $seconds, Closure $callback );
}
