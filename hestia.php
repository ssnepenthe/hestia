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

namespace SSNepenthe\Hestia;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$plugin_root = dirname( __FILE__ );

/**
 * Require the Composer autoloader if it exists.
 */
if ( file_exists( $plugin_root . '/vendor/autoload.php' ) ) {
	require_once $plugin_root . '/vendor/autoload.php';
}

/**
 * Delay plugin initialization until the 'init' hook.
 *
 * @return void
 */
function init() {
	add_shortcode( 'ancestors',   __NAMESPACE__ . '\\ancestors_handler' );
	add_shortcode( 'attachments', __NAMESPACE__ . '\\attachments_handler' );
	add_shortcode( 'children',    __NAMESPACE__ . '\\children_handler' );
	// add_shortcode( 'family',      __NAMESPACE__ . '\\family_handler' );
	add_shortcode( 'siblings',    __NAMESPACE__ . '\\siblings_handler' );
}
add_action( 'init', __NAMESPACE__ . '\\init' );
