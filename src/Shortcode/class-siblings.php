<?php
/**
 * The siblings shortcode.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia\Shortcode;

use WP_Query;
use SSNepenthe\Hestia\Template\Template;
use function SSNepenthe\Hestia\parse_atts;
use SSNepenthe\Hestia\Cache\Cache_Interface;
use function SSNepenthe\Hestia\generate_cache_key;
use function SSNepenthe\Hestia\get_cache_lifetime;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class defines the siblings shortcode.
 */
class Siblings {
	/**
	 * Cache provider.
	 *
	 * @var Cache_Interface
	 */
	protected $cache;

	/**
	 * Template instance.
	 *
	 * @var Template
	 */
	protected $template;

	/**
	 * Class constructor.
	 *
	 * @param Cache_Interface $cache    Cache provider.
	 * @param Template        $template Template instance.
	 */
	public function __construct( Cache_Interface $cache, Template $template ) {
		$this->cache = $cache;
		$this->template = $template;
	}

	/**
	 * Registers the shortcode on the init hook.
	 */
	public function init() {
		add_action( 'plugins_loaded', function() {
			add_shortcode( 'siblings', [ $this, 'shortcode_handler' ] );
		} );
	}

	/**
	 * Delegates to the template instance to render the shortcode output.
	 *
	 * @param  mixed  $atts Shortcode attributes.
	 * @param  mixed  $_    The shortcode content.
	 * @param  string $tag  The shortcode tag.
	 *
	 * @return string
	 */
	public function shortcode_handler( $atts, $_ = null, $tag = '' ) {
		$atts = parse_atts( $atts, $tag );
		$key = generate_cache_key( $atts, $tag );
		$lifetime = get_cache_lifetime( $tag );

		return $this->cache->remember( $key, $lifetime, function() use ( $atts ) {
			return $this->template->render(
				'hestia-siblings',
				$this->generate_data_array( $atts )
			);
		} );
	}

	/**
	 * Generates the data array for the template.
	 *
	 * @param  array $atts Shortcode attributes.
	 *
	 * @return array
	 */
	protected function generate_data_array( $atts ) {
		// Atts assumed to have already been validated.
		$post_id = get_the_ID();
		$args = [
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'order'                  => $atts['order'],
			'post_parent'            => wp_get_post_parent_id( $post_id ),
			'post_type'              => get_post_type(),
			'posts_per_page'         => $atts['max'],
			'update_post_term_cache' => false,
		];

		if ( ! $atts['thumbnails'] ) {
			$args['update_post_meta_cache'] = false;
		}

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
				$thumbnail = $atts['thumbnails'] ? get_the_post_thumbnail() : '';
				$title = get_the_title();

				$siblings[] = compact( 'id', 'permalink', 'thumbnail', 'title' );
			}

			wp_reset_postdata();
		}

		return compact( 'siblings' );
	}
}
