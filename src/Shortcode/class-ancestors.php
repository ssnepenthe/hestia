<?php

namespace SSNepenthe\Hestia\Shortcode;

use SSNepenthe\Hestia\Template\Template;
use function SSNepenthe\Hestia\parse_atts;
use SSNepenthe\Hestia\Cache\Cache_Interface;
use function SSNepenthe\Hestia\generate_cache_key;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Ancestors {
	protected $cache;
	protected $template;

	public function __construct( Cache_Interface $cache, Template $template ) {
		$this->cache = $cache;
		$this->template = $template;
	}

	public function init() {
		add_action( 'plugins_loaded', function() {
			add_shortcode( 'ancestors', [ $this, 'shortcode_handler' ] );
		} );
	}

	public function shortcode_handler( $atts, $_ = null, $tag = '' ) {
		if ( ! is_post_type_hierarchical( get_post_type() ) ) {
			return '';
		}

		$atts = parse_atts( $atts, $tag );
		$cache_key = generate_cache_key( $atts, $tag );

		return $this->cache->remember( $cache_key, 60, function() use ( $atts ) {
			return $this->template->render(
				'hestia-ancestors',
				$this->build_data_array( $atts )
			);
		} );
	}

	protected function build_data_array( $atts ) {
		// Atts assumed to have already been validated.
		$ancestor_ids = get_post_ancestors( get_the_ID() );

		if ( 'ASC' === $atts['order'] ) {
			$ancestor_ids = array_reverse( $ancestor_ids );
		}

		$ancestors = [];

		foreach ( $ancestor_ids as $id ) {
			$permalink = get_permalink( $id );
			$thumbnail = $atts['thumbnails'] ? get_the_post_thumbnail( $id ) : '';
			$title = get_the_title( $id );

			$ancestors[] = compact(
				'id',
				'permalink',
				'thumbnail',
				'title'
			);
		}

		return compact( 'ancestors' );
	}
}
