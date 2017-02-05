<?php

namespace SSNepenthe\Hestia\Shortcode;

use WP_Query;
use SSNepenthe\Hestia\Template\Template;
use function SSNepenthe\Hestia\parse_atts;
use SSNepenthe\Hestia\Cache\Cache_Interface;
use function SSNepenthe\Hestia\generate_cache_key;
use function SSNepenthe\Hestia\get_cache_lifetime;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Children {
	protected $cache;
	protected $template;

	public function __construct( Cache_Interface $cache, Template $template ) {
		$this->cache = $cache;
		$this->template = $template;
	}

	public function init() {
		add_action( 'plugins_loaded', function() {
			add_shortcode( 'children', [ $this, 'shortcode_handler' ] );
		} );
	}

	public function shortcode_handler( $atts, $_ = null, $tag = '' ) {
		if ( ! is_post_type_hierarchical( get_post_type() ) ) {
			return '';
		}

		$atts = parse_atts( $atts, $tag );
		$key = generate_cache_key( $atts, $tag );
		$lifetime = get_cache_lifetime( $tag );

		return $this->cache->remember( $key, $lifetime, function() use ( $atts ) {
			return $this->template->render(
				'hestia-children',
				$this->generate_data_array( $atts )
			);
		} );
	}

	protected function generate_data_array( $atts ) {
		// Atts assumed to have already been validated.
		$args = [
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'order'                  => $atts['order'],
			'post_parent'            => get_the_ID(),
			'post_type'              => get_post_type(),
			'posts_per_page'         => $atts['max'],
			'update_post_term_cache' => false,
		];

		if ( ! $atts['thumbnails'] ) {
			$args['update_post_meta_cache'] = false;
		}

		$query = new WP_Query( $args );
		$children = [];

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$id = get_the_ID();
				$permalink = get_permalink();
				$thumbnail = $atts['thumbnails'] ? get_the_post_thumbnail() : '';
				$title = get_the_title();

				$children[] = compact( 'id', 'permalink', 'thumbnail', 'title' );
			}

			wp_reset_postdata();
		}

		return compact( 'children' );
	}
}
