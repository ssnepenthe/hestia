<?php

namespace SSNepenthe\Hestia\Task;

use SSNepenthe\Hestia\Cache\Wp_Transient_Cache;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Garbage_Collect_Transients {
	protected $cache;

	public function __construct( Wp_Transient_Cache $cache ) {
		$this->cache = $cache;
	}

	public function init() {
		add_action( 'wp_scheduled_delete', [ $this, 'run_task' ] );
	}

	public function run_task() {
		$this->cache->flush();
	}
}
