<?php

namespace SSNepenthe\Hestia\Shortcode;

interface Shortcode_Interface {
	public function render( $atts, $content = null, $tag = '' );
}
