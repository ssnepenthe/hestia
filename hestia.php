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
 * Version: 0.1.0
 * Author: Ryan McLaughlin
 * Author URI: https://github.com/ssnepenthe
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$hestia_dir = plugin_dir_path( __FILE__ );
$hestia_basename = plugin_basename( __FILE__ );
$hestia_autoloader = $hestia_dir . 'vendor/autoload.php';

if ( file_exists( $hestia_autoloader ) ) {
	require_once $hestia_autoloader;
}

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

// The checker class itself requires PHP 5.3 for namespace support. Since Composer
// requires 5.3.2 I plan on leaving this as-is.
$hestia_checker = new SSNepenthe\Soter\Requirements_Checker(
	'Soter',
	$hestia_basename
);

// For use of short array syntax.
$hestia_checker->set_min_php( '5.4' );

if ( $hestia_checker->requirements_met() ) {
	add_action( 'init', 'hestia_init' );
} else {
	add_action( 'admin_init', [ $hestia_checker, 'deactivate' ] );
	add_action( 'admin_notices', [ $hestia_checker, 'notify' ] );
}

unset(
	$hestia_autoloader,
	$hestia_basename,
	$hestia_checker,
	$hestia_dir,
	$hestia_plugin
);
