<?php
/**
 * The plugin bootstrap.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia;

use Pimple\Container;
use SSNepenthe\Hestia\Shortcode\Sitemap;
use SSNepenthe\Hestia\Template\Template;
use SSNepenthe\Hestia\Shortcode\Children;
use SSNepenthe\Hestia\Shortcode\Siblings;
use SSNepenthe\Hestia\Shortcode\Ancestors;
use SSNepenthe\Hestia\Shortcode\Attachments;
use SSNepenthe\Hestia\Cache\Wp_Transient_Cache;
use SSNepenthe\Hestia\Template\Dir_Template_Locator;
use SSNepenthe\Hestia\Template\Core_Template_Locator;
use SSNepenthe\Hestia\Task\Garbage_Collect_Transients;
use SSNepenthe\Hestia\Template\Template_Locator_Stack;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class initializes the entire plugin.
 */
class Plugin extends Container {
	/**
	 * Class constructor.
	 *
	 * @param string $file The path to th emain plugin file.
	 */
	public function __construct( $file ) {
		parent::__construct( [
			'file' => (string) $file,
		] );
	}

	/**
	 * Initializes the plugin.
	 */
	public function init() {
		$this->register_services();

		$this->cron_init();
		$this->plugin_init();
	}

	/**
	 * Initializes the cron-specific functionality.
	 */
	protected function cron_init() {
		if ( ! $this->is_cron_request() ) {
			return;
		}

		$tasks = [
			Garbage_Collect_Transients::class,
		];

		foreach ( $tasks as $task ) {
			( new $task( $this['cache'] ) )->init();
		}
	}

	/**
	 * Determines if the current request is a cron request.
	 *
	 * @return bool
	 */
	protected function is_cron_request() {
		return defined( 'DOING_CRON' ) && DOING_CRON;
	}

	/**
	 * Initializes the sitewide plugin functionality.
	 */
	protected function plugin_init() {
		$shortcodes = [
			Ancestors::class,
			Attachments::class,
			Children::class,
			Siblings::class,
			Sitemap::class,
		];

		foreach ( $shortcodes as $shortcode ) {
			( new $shortcode( $this['cache'], $this['template'] ) )->init();
		}
	}

	/**
	 * Registers plugin services.
	 */
	protected function register_services() {
		$this['cache'] = function( $c ) {
			return new Wp_Transient_Cache( $c['prefix'] );
		};

		$this['prefix'] = 'hestia';

		$this['template'] = function( $c ) {
			return new Template( $c['template.locator_stack'] );
		};

		$this['template.core_locator'] = function( $c ) {
			return new Core_Template_Locator;
		};

		$this['template.dir_locator'] = function( $c ) {
			return new Dir_Template_Locator( plugin_dir_path( $c['file'] ) );
		};

		$this['template.locator_stack'] = function( $c ) {
			return new Template_Locator_Stack( [
				$c['template.core_locator'],
				$c['template.dir_locator'],
			] );
		};
	}
}
