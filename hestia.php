<?php
/**
 * A WordPress plugin that introduces a number of shortcodes for listing related
 * posts within a post hierarchy.
 *
 * @package hestia
 */

/**
 * Plugin Name: Hestia
 * Plugin URI: https://github.com/ssnepenthe/hestia
 * Description: This plugin introduces the following shortcodes: <code>[ancestors]</code>, <code>[attachments]</code>, <code>[children]</code>, <code>[siblings]</code>, <code>[sitemap]</code>.
 * Version: 0.3.0
 * Author: Ryan McLaughlin
 * Author URI: https://github.com/ssnepenthe
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Handles plugin initialization.
 */
function _hestia_init() {
	$checker = WP_Requirements\Plugin_Checker::make( 'Hestia', __FILE__ )
		// For transient key length.
		->wp_at_least( '4.4' )
		// Function imports.
		->php_at_least( '5.6' );

	if ( ! $checker->requirements_met() ) {
		$checker->deactivate_and_notify();

		return;
	}

	add_action( 'plugins_loaded', [ _hestia_instance(), 'boot' ] );
}

/**
 * Static plugin instance getter.
 *
 * @return Metis\Container
 */
function _hestia_instance() {
	static $instance = null;

	if ( is_null( $instance ) ) {
		$instance = new Metis\Container( [
			'dir' => __DIR__,
			'file' => __FILE__,
			'name' => 'Hestia',
			'prefix' => 'hestia',
			'version' => '0.3.0',
		] );

		$instance->register( new Metis\WordPress_Provider() );
		$instance->register( new Hestia\Plates_Provider() );
		$instance->register( new Hestia\Shortcode_Provider() );
	}

	return $instance;
}

/**
 * Include a file if it exists via "require_once".
 *
 * @param  string $file Path to file for inclusion.
 */
function _hestia_require_if_exists( $file ) {
	if ( file_exists( $file ) ) {
		require_once $file;
	}
}

_hestia_require_if_exists( __DIR__ . '/vendor/autoload.php' );
_hestia_init();
