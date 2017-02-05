<?php

namespace SSNepenthe\Hestia;

use WP_Query;
use SSNepenthe\Hestia\Template\Template;
use SSNepenthe\Hestia\Cache\Cache_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Attachments {
	protected $cache;
	protected $template;

	public function __construct( Cache_Interface $cache, Template $template ) {
		$this->cache = $cache;
		$this->template = $template;
	}

	public function init() {
		add_shortcode( 'attachments', [ $this, 'shortcode_handler' ] );
	}

	public function shortcode_handler( $atts, $content = null, $tag = '' ) {
		return $this->cache->remember( 'attachments_' . get_the_ID(), 60, function() {
			return $this->template->render(
				'hestia-attachments',
				$this->build_data_array()
			);
		} );
	}

	protected function build_data_array() {
		$args = [
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'post_parent'    => get_the_ID(),
			// Should allow user to override with shortcode atts.
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'posts_per_page' => 20,
		];
		$query = new WP_Query( $args );
		$attachments = [];

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$id = get_the_ID();
				/**
				 * Should allow user to override with shortcode atts.
				 * Use get_permalink() for the attachment page instead.
				 */
				$permalink = wp_get_attachment_url();
				$title = get_the_title();

				$attachments[] = compact( 'id', 'permalink', 'title' );
			}
		}

		wp_reset_postdata();

		return compact( 'attachments' );
	}
}
