<?php
/**
 * Task_Provider class.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia\Task;

use Metis\Container\Container;
use Metis\Container\Container_Aware_Trait;
use Metis\Container\Bootable_Service_Provider_Interface;

/**
 * Defines the task provider class.
 */
class Task_Provider implements Bootable_Service_Provider_Interface {
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
	 * Provider specific boot logic.
	 */
	public function boot() {
		if ( ! $this->is_cron() ) {
			// Why bother?
			return;
		}

		add_action(
			'wp_scheduled_delete',
			[ $this->container->make( 'hestia.task.garbage_collect' ), 'run_task' ]
		);
	}

	/**
	 * Provider specific registration logic.
	 */
	public function register() {
		$this->container->bind(
			'hestia.task.garbage_collect',
			function( Container $container ) {
				return new Garbage_Collect_Cache(
					$container->make( 'metis.cache' )->transient( 'hestia' )
				);
			}
		);
	}

	/**
	 * Determine if the current request is a wp-cron request.
	 *
	 * @return boolean
	 */
	protected function is_cron() : bool {
		return defined( 'DOING_CRON' ) && DOING_CRON;
	}
}
