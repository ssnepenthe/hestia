<?php
/**
 * Garbage cleanup task.
 *
 * @package  hestia
 */

namespace SSNepenthe\Hestia\Task;

use SSNepenthe\Hestia\Cache\Wp_Transient_Cache;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class handles cleanup of expired, plugin-specific transients.
 */
class Garbage_Collect_Transients {
	/**
	 * Cache provider.
	 *
	 * @var Wp_Transient_Cache
	 */
	protected $cache;

	/**
	 * Class constructor.
	 *
	 * @param Wp_Transient_Cache $cache Transient cache provider.
	 */
	public function __construct( Wp_Transient_Cache $cache ) {
		$this->cache = $cache;
	}

	/**
	 * Hooks in to WordPress.
	 */
	public function init() {
		add_action( 'wp_scheduled_delete', [ $this, 'run_task' ] );
	}

	/**
	 * Runs the garbage collection task.
	 */
	public function run_task() {
		$this->cache->flush();
	}
}
