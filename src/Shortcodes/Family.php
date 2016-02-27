<?php

namespace SSNepenthe\Hestia\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Family {
	public function init() {
		add_shortcode( 'family', [ $this, 'shortcode_handler' ] );
	}

	public function shortcode_handler( $atts, $content = null, $tag = '' ) {
		/**
		 * Not sure what exactly I want to do here... It should definitely get all
		 * ancestors, siblings, children and attachments. It should probably
		 * display in a hierarchy, maybe with finder-like styling.
		 */
	}
}
