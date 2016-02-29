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
 * Description: This plugin introduces the following shortcodes for use within hierarchical post types: <code>[ancestors]</code>, <code>[attachments]</code>, <code>[children]</code>, <code>[siblings]</code>.
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

$plugin_root = plugin_dir_path( __FILE__ );

if ( file_exists( $plugin_root . '/vendor/autoload.php' ) ) {
	require_once $plugin_root . '/vendor/autoload.php';
}

/**
 * Initialize plugin on the 'init' hook.
 */
function hestia_init() {
	$name = 'hestia';
	$version = '0.1.0';

	$hestia = new \SSNepenthe\Hestia\Hestia( $name, $version );
	$hestia->init();
}
add_action( 'init', 'hestia_init' );
