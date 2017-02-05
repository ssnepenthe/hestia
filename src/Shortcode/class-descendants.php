<?php

namespace SSNepenthe\Hestia\Shortcode;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Descendants {
	public function init() {
		add_shortcode( 'descendants', [ $this, 'shortcode_handler' ] );
	}

	public function shortcode_handler( $atts, $content = null, $tag = '' ) {}
}
