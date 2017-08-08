<?php
/**
 * Parses_Shortcode_Atts trait.
 *
 * @package hestia
 */

namespace Hestia;

/**
 * Defines the "parses shortcode atts" trait.
 */
trait Parses_Shortcode_Atts {
	/**
	 * Apply defaults to and validate shortcode attributes.
	 *
	 * @param  mixed  $atts Shortcode attributes as extracted from the content.
	 * @param  string $tag  The shortcode tag.
	 *
	 * @return array
	 */
	public function parse_atts( $atts, $tag ) {
		$atts = shortcode_atts( [
			'link' => 'PAGE',
			'max' => 20,
			'order' => 'ASC',
			'thumbnails' => false,
		], $atts, $tag );

		$atts['link'] = 'FILE' === strtoupper( $atts['link'] ) ? 'FILE' : 'PAGE';
		$atts['max'] = min( 100, max( 1, intval( $atts['max'] ) ) );
		$atts['order'] = 'DESC' === strtoupper( $atts['order'] ) ? 'DESC' : 'ASC';
		$atts['thumbnails'] = filter_var( $atts['thumbnails'], FILTER_VALIDATE_BOOLEAN );

		return $atts;
	}
}
