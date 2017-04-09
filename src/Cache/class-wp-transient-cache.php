<?php
/**
 * A WP Transient cache implementation.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia\Cache;

use Closure;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * A cache interface implementation using core transient caching.
 */
class Wp_Transient_Cache implements Cache_Interface {
	/**
	 * The prefix used to generate cache IDs.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Class constructor.
	 *
	 * @param string $prefix The cache prefix.
	 */
	public function __construct( $prefix ) {
		$prefix = (string) $prefix;

		// MD5 plus "_" take 33 characters which leaves us with 139 for our prefix.
		if ( 139 < strlen( $prefix ) ) {
			$prefix = substr( $prefix, 0, 139 );
		}

		$this->prefix = $prefix . '_';
	}

	/**
	 * Deletes all transients from the database with the specified prefix. Does
	 * nothing when site is using external object cache.
	 *
	 * Mostly swiped from populate_options() in wp-admin/includes/schema.php.
	 */
	public function flush() {
		global $wpdb;

		// Only needs to run if site is storing transients in database.
		if ( wp_using_ext_object_cache() ) {
			return;
		}

		$time = time();

		$transient_prefix = '_transient_' . $this->prefix;
		$timeout_prefix = '_transient_timeout_' . $this->prefix;
		$length = strlen( $transient_prefix ) + 1;

		$wpdb->query( $wpdb->prepare(
			"DELETE a, b FROM $wpdb->options a, $wpdb->options b
			WHERE a.option_name LIKE %s
			AND a.option_name NOT LIKE %s
			AND b.option_name = CONCAT( %s, SUBSTRING( a.option_name, %d ) )
			AND b.option_value < %d",
			$wpdb->esc_like( $transient_prefix ) . '%',
			$wpdb->esc_like( $timeout_prefix ) . '%',
			$timeout_prefix,
			$length,
			$time
		) );
	}

	/**
	 * Remove an entry from the cache.
	 *
	 * @param  string $key The cache key.
	 *
	 * @return bool
	 */
	public function forget( $key ) {
		return delete_transient( $this->generate_id( $key ) );
	}

	/**
	 * Get an entry from the cache if it exists.
	 *
	 * @param  string $key The cache key.
	 *
	 * @return mixed
	 */
	public function get( $key ) {
		$value = get_transient( $this->generate_id( $key ) );

		if ( false === $value ) {
			return null;
		}

		return $value;
	}

	/**
	 * Check whether an entry exists in the cache.
	 *
	 * @param  string $key The cache key.
	 *
	 * @return bool
	 */
	public function has( $key ) {
		return false !== get_transient( $this->generate_id( $key ) );
	}

	/**
	 * Put a value into the cache.
	 *
	 * @param  string $key     The cache key.
	 * @param  mixed  $value   The value to put in the cache.
	 * @param  int    $seconds Number of seconds the entry is valid for.
	 *
	 * @return bool
	 */
	public function put( $key, $value, $seconds = 0 ) {
		return set_transient( $this->generate_id( $key ), $value, $seconds );
	}

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
	public function remember( $key, $seconds, Closure $callback ) {
		$value = $this->get( $key );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		$value = $callback();

		$this->put( $key, $value, $seconds );

		return $value;
	}

	/**
	 * Generate a cache ID using the defined key and prefix.
	 *
	 * @param  string $key The cache key.
	 *
	 * @return string
	 */
	protected function generate_id( $key ) {
		return $this->prefix . hash( 'md5', (string) $key );
	}
}
