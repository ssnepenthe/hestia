<?php

namespace SSNepenthe\Hestia;

use SSNepenthe\Hestia\Template\Template;
use SSNepenthe\Hestia\Cache\Cache_Interface;

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
		add_shortcode( 'ancestors', [ $this, 'shortcode_handler' ] );
	}

	public function shortcode_handler( $atts, $content = null, $tag = '' ) {
		if ( ! is_post_type_hierarchical( get_post_type() ) ) {
			return '';
		}

		$cache_key = 'ancestors_' . get_the_ID();

		return $this->cache->remember( $cache_key, 60, function() {
			return $this->template->render(
				'hestia-ancestors',
				$this->build_data_array()
			);
		} );
	}

	protected function build_data_array() {
		$ancestor_ids = array_reverse( get_post_ancestors( get_the_ID() ) );
		$ancestors = [];

		foreach ( $ancestor_ids as $id ) {
			$permalink = get_permalink( $id );
			$thumbnail = get_the_post_thumbnail( $id );
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
