<?php
/**
 * Plugin helper functions.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia;

/**
 * Generate a cache key for use in saving shortcode output.
 *
 * @param  array  $atts Shortcode attributes.
 * @param  string $tag  The shortcode tag.
 *
 * @return string
 */
function generate_cache_key( $atts, $tag = '' ) {
	$key = $tag . get_the_ID() . implode( '', $atts );

	// False would otherwise be an empty string.
	if ( isset( $atts['thumbnails'] ) && ! $atts['thumbnails'] ) {
		$key .= '0';
	}

	return $key;
}

/**
 * Get the filtered cache lifetime based on shortcode tag.
 *
 * @param  string $tag The shortcode tag.
 *
 * @return int
 */
function get_cache_lifetime( $tag ) {
	return absint( apply_filters( "hestia_{$tag}_cache_lifetime", 600 ) );
}

/**
 * Apply defaults and validate an array of shortcode attributes.
 *
 * @param  mixed  $atts Shortcode attributes.
 * @param  string $tag  The shortcode tag
 *
 * @return array
 */
function parse_atts( $atts, $tag = '' ) {
	$atts = shortcode_atts( [
		'link'       => 'page',
		'max'        => 20,
		'order'      => 'ASC',
		'thumbnails' => false,
	], $atts, $tag );

	$atts['link'] = 'FILE' === strtoupper( $atts['link'] ) ? 'FILE' : 'PAGE';
	$atts['max'] = min(
		100,
		max( 1, intval( $atts['max'] ) )
	);
	$atts['order'] = 'DESC' === strtoupper( $atts['order'] ) ? 'DESC' : 'ASC';
	$atts['thumbnails'] = filter_var(
		$atts['thumbnails'],
		FILTER_VALIDATE_BOOLEAN
	);

	return $atts;
}
