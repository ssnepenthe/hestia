<?php
/**
 * The attachments shortcode.
 *
 * @package hestia
 */

namespace Hestia;

use Hestia\Plates_Manager;
use Hestia\Posts_Repository;
use function Hestia\parse_atts;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class defines the attachments shortcode.
 */
class Attachments implements Shortcode {
	const TAG = 'attachments';
	const TEMPLATE_NAME = 'hestia-attachments';

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
		$atts = parse_atts( $atts, $tag );

		$meta = (bool) apply_filters(
			'hestia_attachments_preload_meta',
			$atts['thumbnails'] || 'PAGE' === $atts['link']
		);

		$attachments = $this->repository->get_attachments(
			get_the_ID(),
			$atts['max'],
			$atts['order'],
			$meta
		);

		return $this->template->render( self::TEMPLATE_NAME, [
			'attachments' => $attachments,
			'link_to' => $atts['link'],
			'thumbnails' => $atts['thumbnails'],
		] );
	}
}
