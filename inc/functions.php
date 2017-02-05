<?php

namespace SSNepenthe\Hestia;

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

function generate_cache_key( $atts, $tag = '' ) {
	$key = $tag . get_the_ID() . implode( '', $atts );

	// False would otherwise be an empty string.
	if ( isset( $atts['thumbnails'] ) && ! $atts['thumbnails'] ) {
		$key .= '0';
	}

	return $key;
}
