<?php
/**
 * Garbage collection task.
 *
 * @package  hestia
 */

namespace SSNepenthe\Hestia\Task;

use SSNepenthe\Hestia\Cache\Cache_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class handles cleanup of expired, plugin-specific transients.
 */
class Garbage_Collect_Cache {
	/**
	 * Cache instance.
	 *
	 * @var Cache_Interface
	 */
	protected $cache;

	/**
	 * Class constructor.
	 *
	 * @param Cache_Interface $cache Cache instance.
	 */
	public function __construct( Cache_Interface $cache ) {
		$this->cache = $cache;
	}

	/**
	 * Runs the garbage collection task.
	 *
	 * @return void
	 */
	public function run_task() {
		$this->cache->flush_expired();
	}
}
