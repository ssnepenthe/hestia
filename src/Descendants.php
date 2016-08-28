<?php

namespace SSNepenthe\Hestia;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Descendants {
	/**
	 * @hook
	 */
	public function init() {
		add_shortcode( 'descendants', [ $this, 'shortcode_handler' ] );
	}

	public function shortcode_handler( $atts, $content = null, $tag = '' ) {}
}
