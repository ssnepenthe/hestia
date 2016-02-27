<?php

namespace SSNepenthe\Hestia\Shortcodes;

use \WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Siblings {
	public function init() {
		add_shortcode( 'siblings', [ $this, 'shortcode_handler' ] );
	}

	public function shortcode_handler( $atts, $content = null, $tag = '' ) {
		$id = get_the_ID();
		$args = [
			'post__not_in' => [ $id ],
			'order' => 'ASC',
			'orderby' => 'menu_order',
			'post_parent' => wp_get_post_parent_id( $id ),
			// Should allow user to override with shortcode atts.
			'posts_per_page' => 20,
			'post_type' => get_post_type(),
		];
		$query = new WP_Query( $args );
		$r = [];

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$classes = [
					'hestia-sibling',
					'hestia-wrap',
					sprintf( 'post-%s', get_the_ID() ),
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

		return implode( "\n", $r );
	}
}
