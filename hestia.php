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

$hestia_checker = WP_Requirements\Plugin_Checker::make( 'Hestia', __FILE__ )
	// For transient key length.
	->wp_at_least( '4.4' )
	// Depends on ssnepenthe/metis.
	->php_at_least( '7.0' );

if ( $hestia_checker->requirements_met() ) {
	$hestia_plugin = new Metis\Package( [
		Metis\Cache\Cache_Provider::class,
		Metis\View\View_Provider::class,
		SSNepenthe\Hestia\Hestia_Provider::class,
		SSNepenthe\Hestia\Shortcode\Shortcode_Provider::class,
		SSNepenthe\Hestia\Task\Task_Provider::class,
	] );
	$hestia_plugin->init();
} else {
	$hestia_checker->deactivate_and_notify();
}

unset( $hestia_checker, $hestia_plugin );
