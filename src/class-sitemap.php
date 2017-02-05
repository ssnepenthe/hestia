<?php

namespace SSNepenthe\Hestia;

use WP_Query;
use SSNepenthe\Hestia\Template\Template;
use SSNepenthe\Hestia\Cache\Cache_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Sitemap {
	protected $cache;
	protected $template;

	public function __construct( Cache_Interface $cache, Template $template ) {
		$this->cache = $cache;
		$this->template = $template;
	}

	public function init() {
		add_shortcode( 'sitemap', [ $this, 'shortcode_handler' ] );
	}

	public function shortcode_handler( $atts, $content = null, $tag = '' ) {
		return $this->cache->remember( 'sitemap', 10, function() {
			return $this->template->render(
				'hestia-sitemap',
				$this->generate_data_array()
			);
		} );
	}

	protected function generate_data_array() {
		// Publicly_queryable excludes "page" post type.
		$post_types = get_post_types( [
			'public' => true,
		] );

		$sections = [];

		foreach( $post_types as $post_type ) {
			if ( 'attachment' === $post_type ) {
				continue;
			}

			$object = get_post_type_object( $post_type );

			$args = [
				'order'          => 'ASC',
				'orderby'        => 'menu_order',
				'post_type'      => $post_type,
				// Arbitrary limit... Need to revisit this.
				'posts_per_page' => 20,
			];

			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				$name = $object->labels->name;
				$links = [];
				$type = str_replace(
					'_',
					'-',
					sanitize_html_class( $post_type )
				);

				while ( $query->have_posts() ) {
					$query->the_post();

					$links[] = [
						'permalink' => get_permalink(),
						'title'     => get_the_title(),
					];
				}

				$sections[] = compact( 'type', 'name', 'links' );

				wp_reset_postdata();
			}
		}

		return compact( 'sections' );
	}
}
