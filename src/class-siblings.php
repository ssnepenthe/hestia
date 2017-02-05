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
		return $this->cache->remember( 'siblings_' . get_the_ID(), 60, function() {
			return $this->template->render(
				'hestia-siblings',
				$this->generate_data_array()
			);
		} );
	}

	protected function generate_data_array() {
		$post_id = get_the_ID();
		$args = [
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'post__not_in'   => [ $post_id ],
			'post_parent'    => wp_get_post_parent_id( $post_id ),
			'post_type'      => get_post_type(),
			// Should allow user to override with shortcode atts.
			'posts_per_page' => 20,
		];
		$query = new WP_Query( $args );
		$siblings = [];

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$id = get_the_ID();
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
