<?php
/**
 * Garbage collection task.
 *
 * @package  hestia
 */

namespace SSNepenthe\Hestia\Task;

use Hestia\Cache\Repository;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class handles cleanup of expired, plugin-specific transients.
 */
class Garbage_Collect_Cache {
	/**
	 * Cache repository.
	 *
	 * @var Repository
	 */
	protected $repository;

	/**
	 * Class constructor.
	 *
	 * @param Repository $repository Cache repository.
	 */
	public function __construct( Repository $repository ) {
		$this->repository = $repository;
	}

	/**
	 * Runs the garbage collection task.
	 */
	public function run_task() {
		$this->repository->flush_expired();
	}
}
