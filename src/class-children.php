<?php

namespace SSNepenthe\Hestia;

use WP_Query;
use SSNepenthe\Hestia\Cache\Cache_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Children {
	protected $cache;

	public function __construct( Cache_Interface $cache ) {
		$this->cache = $cache;
	}

	public function init() {
		add_shortcode( 'children', [ $this, 'shortcode_handler' ] );
	}

	public function shortcode_handler( $atts, $content = null, $tag = '' ) {
		if ( ! is_post_type_hierarchical( get_post_type() ) ) {
			return '';
		}

		return $this->cache->remember( 'children_' . get_the_ID(), 60, function() {
			$args = [
				'order' => 'ASC',
				'orderby' => 'menu_order',
				'post_parent' => get_the_ID(),
				// Query returns nothing if we don't include post type.
				'post_type' => get_post_type(),
				// Should allow user to override with shortcode atts.
				'posts_per_page' => 20,
			];
			$query = new WP_Query( $args );
			$r = [];

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					$classes = [
						'hestia-child',
						'hestia-wrap',
						sprintf( 'post-%s', esc_attr( get_the_ID() ) ),
					];
					$has_thumbnail = has_post_thumbnail();
					$permalink = get_permalink();

					if ( $has_thumbnail ) {
						// Because who doesn't love a properly alphabetized list?
						array_unshift( $classes, 'has-post-thumbnail' );
					}

					$r[] = sprintf( '<div class="%s">', implode( ' ', $classes ) );
					$r[] = sprintf( '<a href="%s">', esc_attr( $permalink ) );

					if ( $has_thumbnail ) {
						$r[] = get_the_post_thumbnail();
					}

					$r[] = get_the_title();

					$r[] = '</a>';
					$r[] = '</div>';
				}
			}

			wp_reset_postdata();

			return implode( '', $r );
		} );
	}
}
