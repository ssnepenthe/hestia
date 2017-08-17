<?php
/**
 * The ancestors shortcode.
 *
 * @package hestia
 */

namespace Hestia;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class defines the ancestors shortcode.
 */
class Ancestors implements Shortcode {
	use Parses_Shortcode_Atts;

	const TAG = 'ancestors';
	const TEMPLATE_NAME = 'hestia-ancestors';

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

		$meta = (bool) apply_filters( 'hestia_ancestors_preload_meta', $atts['thumbnails'] );

		$ancestors = $this->repository->get_ancestors( get_the_ID(), $atts['order'], $meta );

		return $this->template->render( self::TEMPLATE_NAME, [
			'ancestors' => $ancestors,
			'thumbnails' => $atts['thumbnails'],
		] );
	}
}
