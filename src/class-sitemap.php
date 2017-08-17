<?php
/**
 * The sitemap shortcode.
 *
 * @package hestia
 */

namespace Hestia;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class defines the sitemap shortcode.
 */
class Sitemap implements Shortcode {
	use Parses_Shortcode_Atts;

	const TAG = 'sitemap';
	const TEMPLATE_NAME = 'hestia-sitemap';

	/**
	 * Posts repository instance.
	 *
	 * @var Posts_Repository
	 */
	protected $repository;

	/**
	 * Template instance.
	 *
	 * @var Plates_Manager
	 */
	protected $template;

	/**
	 * Class constructor.
	 *
	 * @param Posts_Repository $repository Posts repository instance.
	 * @param Plates_Manager   $template   Template instance.
	 */
	public function __construct( Posts_Repository $repository, Plates_Manager $template ) {
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
	public function render( $atts, $_ = null, $tag = '' ) {
		$atts = $this->parse_atts( $atts, $tag );

		$meta = (bool) apply_filters( 'hestia_sitemap_preload_meta', $atts['thumbnails'] );
		// Not using "publicy_queryable" because it would exclude "page" post type.
		$post_types = array_diff( get_post_types( [
			'public' => true,
		] ), [ 'attachment' ] );
		$posts = [];

		foreach ( $post_types as $post_type ) {
			$posts[ $post_type ] = $this->repository->get_posts_by_type(
				$post_type,
				$atts['max'],
				$atts['order'],
				$meta
			);
		}

		return $this->template->render( self::TEMPLATE_NAME, [
			'posts' => $posts,
			'thumbnails' => $atts['thumbnails'],
		] );
	}
}
