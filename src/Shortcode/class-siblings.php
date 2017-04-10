<?php
/**
 * The siblings shortcode.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia\Shortcode;

use WP_Query;
use Metis\View\Template;
use Metis\Cache\Repository;
use function SSNepenthe\Hestia\parse_atts;
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
	 * Cache repository.
	 *
	 * @var Repository
	 */
	protected $repository;

	/**
	 * Template instance.
	 *
	 * @var Template
	 */
	protected $template;

	/**
	 * Class constructor.
	 *
	 * @param Repository $repository Cache repository.
	 * @param Template   $template   Template instance.
	 */
	public function __construct( Repository $repository, Template $template ) {
		$this->repository = $repository;
		$this->template = $template;
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
	public function shortcode_handler(
		$atts,
		$_ = null,
		string $tag = ''
	) : string {
		$atts = parse_atts( $atts, $tag );
		$key = generate_cache_key( $atts, $tag );
		$lifetime = get_cache_lifetime( $tag );

		return $this->repository->remember(
			$key,
			$lifetime,
			function() use ( $atts ) {
				return $this->template->render(
					'hestia-siblings',
					$this->generate_data_array( $atts )
				);
			}
		);
	}

	/**
	 * Generates the data array for the template.
	 *
	 * @param  array $atts Shortcode attributes.
	 *
	 * @return array
	 */
	protected function generate_data_array( array $atts ) : array {
		// Atts assumed to have already been validated.
		$post_id = get_the_ID();
		$args = [
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'order'                  => $atts['order'],
			'post_parent'            => wp_get_post_parent_id( $post_id ),
			'post_type'              => get_post_type(),
			// Load an extra post b/c list may include current post.
			'posts_per_page'         => $atts['max'] + 1,
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

				if ( $atts['max'] <= count( $siblings ) ) {
					// We queried for one more post than desired to be able to filter
					// out the current post - if siblings count has hit our desired
					// max we need to break out early.
					break;
				}
			}

			wp_reset_postdata();
		}

		return compact( 'siblings' );
	}
}
