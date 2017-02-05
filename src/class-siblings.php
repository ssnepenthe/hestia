<?php

namespace SSNepenthe\Hestia;

use WP_Query;
use SSNepenthe\Hestia\Template\Template;
use SSNepenthe\Hestia\Cache\Cache_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Siblings {
	protected $cache;
	protected $template;

	public function __construct( Cache_Interface $cache, Template $template ) {
		$this->cache = $cache;
		$this->template = $template;
	}

	public function init() {
		add_shortcode( 'siblings', [ $this, 'shortcode_handler' ] );
	}

	public function shortcode_handler( $atts, $content = null, $tag = '' ) {
		$cache_key = 'siblings_' . get_the_ID();

		return $this->cache->remember( $cache_key, 60, function() {
			return $this->template->render(
				'hestia-siblings',
				$this->generate_data_array()
			);
		} );
	}

	protected function generate_data_array() {
		$post_id = get_the_ID();
		$args = [
			'no_found_rows'          => true,
			'order'                  => 'ASC',
			'orderby'                => 'menu_order',
			'post_parent'            => wp_get_post_parent_id( $post_id ),
			'post_type'              => get_post_type(),
			'posts_per_page'         => 20,
			'update_post_term_cache' => false,
		];
		$query = new WP_Query( $args );
		$siblings = [];

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$id = get_the_ID();

				if ( $post_id === $id ) {
					// Rather than use 'post__not_in' arg.
					continue;
				}

				$permalink = get_permalink();
				$thumbnail = get_the_post_thumbnail();
				$title = get_the_title();

				$siblings[] = compact( 'id', 'permalink', 'thumbnail', 'title' );
			}

			wp_reset_postdata();
		}

		return compact( 'siblings' );
	}
}
