<?php
/**
 * Hestia_Provider class.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia;

use Metis\Container\Container;
use Metis\Container\Container_Aware_Trait;
use Metis\Container\Service_Provider_Interface;

/**
 * Defines the hestia provider class.
 */
class Hestia_Provider implements Service_Provider_Interface {
	use Container_Aware_Trait;

	/**
	 * Class constructor.
	 *
	 * @param Container $container Container instance.
	 */
	public function __construct( Container $container ) {
		$this->set_container( $container );
	}

	/**
	 * Provider specific registration logic.
	 */
	public function register() {
		$this->container->singleton( 'hestia.dir', function() {
			return plugin_dir_path( __DIR__ );
		} );
	}
}
