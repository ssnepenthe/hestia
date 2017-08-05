<?php
/**
 * Transient_Cache class.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia\Cache;

use wpdb;
use Closure;

/**
 * Defines the transient cache class.
 */
class Transient_Cache implements Cache_Interface {
	/**
	 * Maximum allowed length for cache keys.
	 */
	const MAX_KEY_LENGTH = 172;

	/**
	 * Database instance.
	 *
	 * @var wpdb
	 */
	protected $db;

	/**
	 * Cache prefix.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Default cache duration in seconds.
	 *
	 * @var int
	 */
	protected $default_ttl;

	/**
	 * Class constructor.
	 *
	 * @param wpdb    $db          Database instance.
	 * @param string  $prefix      Cache prefix.
	 * @param integer $default_ttl Default cache duration in seconds.
	 *
	 * @throws  InvalidArgumentException When $prefix exceeds maximum allowed length.
	 */
	public function __construct( wpdb $db, $prefix = '', $default_ttl = 0 ) {
		$prefix = strval( $prefix );
		$default_ttl = max( 0, intval( $default_ttl ) );

		// 40 for length of sha1, additional 1 for ":" separator.
		if ( self::MAX_KEY_LENGTH - 40 - 1 < strlen( $prefix ) ) {
			throw new InvalidArgumentException( sprintf(
				'Provided prefix [%s, length of %s] exceeds maximum allowed length of %s',
				$prefix,
				strlen( $prefix ),
				self::MAX_KEY_LENGTH
			) );
		}

		$this->db = $db;
		$this->prefix = $prefix;
		$this->default_ttl = $default_ttl;
	}

	/**
	 * Get an entry from the cache.
	 *
	 * @param  string $key Cache key.
	 *
	 * @return mixed       Cache value, null if it does not exist.
	 */
	public function get( $key ) {
		$value = get_transient( $this->item_key( $key ) );

		return false === $value ? null : $value;
	}

	/**
	 * Save the result of a closure call to the cache.
	 *
	 * @param  string  $key      The cache key.
	 * @param  integer $ttl      The cache duration in seconds.
	 * @param  Closure $callback The callback used to generate the cache value.
	 *
	 * @return mixed             The value from cache if it exists, otherwise return from $callback.
	 */
	public function remember( $key, $ttl, Closure $callback ) {
		$value = $this->get( $key );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		$value = $callback();

		$this->set( $key, $value, $ttl );

		return $value;
	}

	/**
	 * Set a value to the cache.
	 *
	 * @param string  $key   The cache key.
	 * @param mixed   $value Value to save in cache.
	 * @param integer $ttl   Cache duration in seconds, 0 to remember forever.
	 */
	public function set( $key, $value, $ttl = null ) {
		return set_transient( $this->item_key( $key ), $value, $this->item_ttl( $ttl ) );
	}

	/**
	 * Generate a cache item key from user key and prefix.
	 *
	 * @param  string $key User key.
	 *
	 * @return string
	 */
	protected function item_key( $key ) {
		$this->validate_item_key( $key );

		$prefix = $this->prefix ? "{$this->prefix}:" : '';
		$new_key = $prefix . $key;

		if ( strlen( $new_key ) <= self::MAX_KEY_LENGTH ) {
			return $new_key;
		}

		return $prefix . hash( 'sha1', $key );
	}

	/**
	 * Get the normalized TTL for an item.
	 *
	 * @param  integer $ttl User TTL.
	 *
	 * @return integer
	 */
	protected function item_ttl( $ttl ) {
		if ( is_null( $ttl ) ) {
			return $this->default_ttl;
		}

		return ( is_int( $ttl ) && 0 < $ttl ) ? $ttl : 0;
	}

	/**
	 * Ensure a cache key is valid.
	 *
	 * @param  mixed $key Cache key.
	 *
	 * @return void
	 *
	 * @throws \InvalidArgumentException When $key is not string.
	 * @throws \InvalidArgumentException When $key is empty string.
	 * @throws \InvalidArgumentException When $key contains reserved characters.
	 */
	protected function validate_item_key( $key ) {
		if ( ! is_string( $key ) ) {
			throw new \InvalidArgumentException( sprintf(
				'Cache key must be string, %s given',
				gettype( $key )
			) );
		}

		if ( ! isset( $key[0] ) ) {
			throw new \InvalidArgumentException( 'Cache key length must be greater than zero' );
		}

		if ( false !== strpbrk( $key, '{}()/\\@:' ) ) {
			throw new \InvalidArgumentException( sprintf(
				'Cache key [%s] contains one or more reserved characters {}()/\\@:',
				$key
			) );
		}
	}
}
