<?php

namespace SSNepenthe\Hestia;

use WP_Query;
use SSNepenthe\Hestia\Cache\Cache_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Attachments {
	protected $cache;

	public function __construct( Cache_Interface $cache ) {
		$this->cache = $cache;
	}

	public function init() {
		add_shortcode( 'attachments', [ $this, 'shortcode_handler' ] );
	}

	public function shortcode_handler( $atts, $content = null, $tag = '' ) {
		return $this->cache->remember( 'attachments_' . get_the_ID(), 60, function() {
			$args = [
				'order' => 'ASC',
				'orderby' => 'menu_order',
				'post_parent' => get_the_ID(),
				// Should allow user to override with shortcode atts.
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'posts_per_page' => 20,
			];
			$query = new WP_Query( $args );
			$r = [];

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					/**
					 * Should allow user to override with shortcode atts.
					 * Use get_permalink() for the attachment page instead.
					 */
					$permalink = wp_get_attachment_url();
					$classes = [
						'hestia-attachment',
						'hestia-wrap',
						sprintf( 'post-%s', esc_attr( get_the_ID() ) ),
					];

					$r[] = sprintf( '<div class="%s">', implode( ' ', $classes ) );

					// We should probably be displaying the actual attachment...
					$r[] = sprintf(
						'<a href="%s">%s</a>',
						esc_attr( $permalink ),
						get_the_title()
					);

					$r[] = '</div>';
				}
			}

			wp_reset_postdata();

			return implode( '', $r );
		} );
	}
}
