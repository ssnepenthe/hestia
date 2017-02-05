<?php
/**
 * A WordPress plugin that introduces a number of shortcodes for listing
 * related posts within a post hierarchy.
 *
 * @package hestia
 */

/**
 * Plugin Name: Hestia
 * Plugin URI: https://github.com/ssnepenthe/hestia
 * Description: This plugin introduces the following shortcodes: <code>[ancestors]</code>, <code>[attachments]</code>, <code>[children]</code>, <code>[siblings]</code>, <code>[sitemap]</code>.
 * Version: 0.1.0
 * Author: SSNepenthe
 * Author URI: https://github.com/ssnepenthe
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:
 * Domain Path:
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$autoloader = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

if ( file_exists( $autoloader ) ) {
	require_once $autoloader;
}

unset( $autoloader );

/**
 * Initialize plugin on the 'init' hook.
 */
function hestia_init() {
	$classes = [
		SSNepenthe\Hestia\Ancestors::class,
		SSNepenthe\Hestia\Attachments::class,
		SSNepenthe\Hestia\Children::class,
		// SSNepenthe\Hestia\Descendants::class,
		// SSNepenthe\Hestia\Family::class,
		SSNepenthe\Hestia\Siblings::class,
		SSNepenthe\Hestia\Sitemap::class,
	];

	foreach ( $classes as $class ) {
		( new $class )->init();
	}
}
add_action( 'init', 'hestia_init' );
