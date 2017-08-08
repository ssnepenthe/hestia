<?php
/**
 * Shortcode interface.
 *
 * @package hestia
 */

namespace Hestia;

/**
 * Defines the shortcode interface.
 */
interface Shortcode {
	/**
	 * Render the shortcode output.
	 *
	 * @param  mixed  $atts    Shortcode attrbiutes.
	 * @param  mixed  $content Shortcode content.
	 * @param  string $tag     Shortcode tag.
	 *
	 * @return string
	 */
	public function render( $atts, $content = null, $tag = '' );
}
