<?php

namespace SSNepenthe\Hestia\Shortcode;

use WP_Query;
use SSNepenthe\Hestia\Template\Template;
use SSNepenthe\Hestia\generate_cache_key;
use function SSNepenthe\Hestia\parse_atts;
use SSNepenthe\Hestia\Cache\Cache_Interface;
use function SSNepenthe\Hestia\generate_cache_key;

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

	public function shortcode_handler( $atts, $_ = null, $tag = '' ) {
		$atts = parse_atts( $atts, $tag );
		$cache_key = generate_cache_key( $atts, $tag );

		return $this->cache->remember( $cache_key, 60, function() use ( $atts ) {
			return $this->template->render(
				'hestia-attachments',
				$this->build_data_array( $atts )
			);
		} );
	}

	protected function build_data_array( $atts ) {
		// Atts assumed to have already been validated.
		$args = [
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'order'                  => $atts['order'],
			'post_parent'            => get_the_ID(),
			'post_status'            => 'inherit',
			'post_type'              => 'attachment',
			'posts_per_page'         => $atts['max'],
			'update_post_term_cache' => false,
		];

		if ( 'PAGE' === $atts['link'] ) {
			// wp_get_attachment_url() looks in post meta.
			$args['update_post_meta_cache'] = false;
		}

		$query = new WP_Query( $args );
		$attachments = [];

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$id = get_the_ID();
				$permalink = 'PAGE' === $atts['link']
					? get_permalink()
					: wp_get_attachment_url();
				$title = get_the_title();

				$attachments[] = compact( 'id', 'permalink', 'title' );
			}

			wp_reset_postdata();
		}

		return compact( 'attachments' );
	}
}
