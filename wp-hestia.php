<?php
/**
 * A WordPress plugin that introduces a number of shortcodes for listing
 * related posts within a post hierarchy.
 *
 * @package wp-hestia
 */

/**
 * Plugin Name: WP Hestia
 * Plugin URI: https://github.com/ssnepenthe/wp-hestia
 * Description:
 * Version:
 * Author:
 * Author URI:
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:
 * Domain Path:
 */

namespace SSNepenthe\WPHestia;

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
