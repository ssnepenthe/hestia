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

interface Cache_Interface {
	public function forget( $key );
	public function get( $key );
	public function has( $key );
	public function put( $key, $value, $seconds = 0 );
	public function remember( $key, $seconds, Closure $callback );
}
