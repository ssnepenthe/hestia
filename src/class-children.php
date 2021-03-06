<?php
/**
 * The children shortcode.
 *
 * @package hestia
 */

namespace Hestia;

if ( ! \defined( 'ABSPATH' ) ) {
	die;
}

/**
 * The class defines the children shortcode.
 */
class Children implements Shortcode {
	use Parses_Shortcode_Atts;

	const TAG = 'children';
	const TEMPLATE_NAME = 'hestia-children';

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
	 * @param mixed  $atts Shortcode attributes.
	 * @param mixed  $_    The shortcode content.
	 * @param string $tag  The shortcode tag.
	 *
	 * @return string
	 */
	public function render( $atts, $_ = null, $tag = '' ) {
		$atts = $this->parse_atts( $atts, $tag );

		if ( false === $atts['id'] ) {
			return '';
		}

		$meta = (bool) apply_filters( 'hestia_children_preload_meta', $atts['thumbnails'] );

		$children = $this->repository->get_children(
			$atts['id'],
			$atts['max'],
			$atts['order'],
			$meta
		);

		return $this->template->render( self::TEMPLATE_NAME, [
			'children' => $children,
			'thumbnails' => $atts['thumbnails'],
		] );
	}
}
