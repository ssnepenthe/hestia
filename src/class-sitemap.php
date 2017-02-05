<?php

namespace SSNepenthe\Hestia;

use WP_Query;
use SSNepenthe\Hestia\Cache\Cache_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Sitemap {
	protected $cache;

	public function __construct( Cache_Interface $cache ) {
		$this->cache = $cache;
	}

	public function init() {
		add_shortcode( 'sitemap', [ $this, 'shortcode_handler' ] );
	}

	/**
	 * @todo Should probably be checking publicly_queryable instead of public.
	 */
	public function shortcode_handler( $atts, $content = null, $tag = '' ) {
		return $this->cache->remember( 'sitemap', 60, function() {
			$post_types = get_post_types( [
				'public' => true,
			] );

			$r = [];

			foreach( $post_types as $post_type ) {
				// Skip attachments.
				if ( in_array( $post_type, [ 'attachment' ] ) ) {
					continue;
				}

				$object = get_post_type_object( $post_type );

				$args = [
					'order' => 'ASC',
					'orderby' => 'menu_order',
					'post_type' => $post_type,
					// Arbitrary limit... Need to revisit this.
					'posts_per_page' => 20,
				];

				$query = new WP_Query( $args );

				if ( $query->have_posts() ) {
					$type = str_replace( '_', '-', $post_type );
					$classes = [
						'hestia-wrap',
						'hestia-sitemap',
						sprintf( 'post-type-%s', sanitize_html_class( $type ) ),
					];
					$r[] = sprintf( '<div class="%s">', implode( ' ', $classes ) );
					$r[] = sprintf(
						'<h2>Recent %s</h2>',
						$object->labels->name
					);
					$r[] = '<ul>';

					while ( $query->have_posts() ) {
						$query->the_post();

						$r[] = sprintf(
							'<li><a href="%s">%s</a></li>',
							esc_url( get_permalink() ),
							esc_html( get_the_title() )
						);
					}

					$r[] = '</ul>';
					$r[] = '</div>';
				}

				wp_reset_postdata();
			}

			return implode( '', $r );
		} );
	}
}
