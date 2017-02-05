<?php

namespace SSNepenthe\Hestia\Shortcode;

use WP_Query;
use SSNepenthe\Hestia\Template\Template;
use function SSNepenthe\Hestia\parse_atts;
use SSNepenthe\Hestia\Cache\Cache_Interface;
use function SSNepenthe\Hestia\generate_cache_key;

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
		add_action( 'plugins_loaded', function() {
			add_shortcode( 'sitemap', [ $this, 'shortcode_handler' ] );
		} );
	}

	public function shortcode_handler( $atts, $_ = null, $tag = '' ) {
		$atts = parse_atts( $atts, $tag );
		$cache_key = generate_cache_key( $atts, $tag );

		return $this->cache->remember( $cache_key, 60, function() use ( $atts ) {
			return $this->template->render(
				'hestia-sitemap',
				$this->generate_data_array( $atts )
			);
		} );
	}

	protected function generate_data_array( $atts ) {
		// Atts assumed to have already been validated.
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
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'order'                  => $atts['order'],
				'post_type'              => $post_type,
				'posts_per_page'         => $atts['max'],
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
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
