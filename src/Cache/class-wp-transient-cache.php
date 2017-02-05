<?php

namespace SSNepenthe\Hestia\Cache;

use Closure;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Wp_Transient_Cache implements Cache_Interface {
	protected $prefix;

	public function __construct( $prefix ) {
		$prefix = (string) $prefix;

		// MD5 plus "_" take 33 characters which leaves us with 139 for our prefix.
		if ( 139 < strlen( $prefix ) ) {
			$prefix = substr( $prefix, 0, 139 );
		}

		$this->prefix = $prefix . '_';
	}

	public function forget( $key ) {
		return delete_transient( $this->generate_id( $key ) );
	}

	public function get( $key ) {
		$value = get_transient( $this->generate_id( $key ) );

		if ( false === $value ) {
			return null;
		}

		return $value;
	}

	public function has( $key ) {
		return false !== get_transient( $this->generate_id( $key ) );
	}

	public function put( $key, $value, $seconds = 0 ) {
		return set_transient( $this->generate_id( $key ), $value, $seconds );
	}

	public function remember( $key, $seconds, Closure $callback ) {
		if ( ! is_null( $value = $this->get( $key ) ) ) {
			return $value;
		}

		$this->put( $key, $value = $callback(), $seconds );

		return $value;
	}

	protected function generate_id( $key ) {
		return $this->prefix . hash( 'md5', (string) $key );
	}
}
