<?php
/**
 * Task_Provider class.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia\Task;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Defines the task provider class.
 */
class Task_Provider implements ServiceProviderInterface {
	/**
	 * Provider-specific boot logic.
	 *
	 * @param  Container $container Container instance.
	 *
	 * @return void
	 */
	public function boot( Container $container ) {
		if ( ! $this->is_cron() ) {
			// Why bother?
			return;
		}

		add_action( 'wp_scheduled_delete', [ $container['task.garbage_collect'], 'run_task' ] );
	}

	/**
	 * Provider-specific registration logic.
	 *
	 * @param  Container $container Container instance.
	 *
	 * @return void
	 */
	public function register( Container $container ) {
		$container['task.garbage_collect'] = function( Container $c ) {
			return new Garbage_Collect_Cache( $c['cache'] );
		};
	}

	/**
	 * Determine if the current request is a wp-cron request.
	 *
	 * @return boolean
	 */
	protected function is_cron() {
		return defined( 'DOING_CRON' ) && DOING_CRON;
	}
}
