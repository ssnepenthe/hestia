<?php
/**
 * The ancestors shortcode.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia\Shortcode;

use Metis\View\Template;
use Metis\Cache\Repository;
use function SSNepenthe\Hestia\parse_atts;
use function SSNepenthe\Hestia\generate_cache_key;
use function SSNepenthe\Hestia\get_cache_lifetime;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * This class defines the ancestors shortcode.
 */
class Ancestors {
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
	 * @param Template   $template   Templatee instance.
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
		if ( ! is_post_type_hierarchical( get_post_type() ) ) {
			return '';
		}

		$atts = parse_atts( $atts, $tag );
		$key = generate_cache_key( $atts, $tag );
		$lifetime = get_cache_lifetime( $tag );

		return $this->repository->remember(
			$key,
			$lifetime,
			function() use ( $atts ) {
				return $this->template->render(
					'hestia-ancestors',
					$this->build_data_array( $atts )
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
	protected function build_data_array( array $atts ) : array {
		// Atts assumed to have already been validated.
		$ancestor_ids = get_post_ancestors( get_the_ID() );

		if ( 'ASC' === $atts['order'] ) {
			$ancestor_ids = array_reverse( $ancestor_ids );
		}

		$ancestors = [];

		foreach ( $ancestor_ids as $id ) {
			$permalink = get_permalink( $id );
			$thumbnail = $atts['thumbnails'] ? get_the_post_thumbnail( $id ) : '';
			$title = get_the_title( $id );

			$ancestors[] = compact(
				'id',
				'permalink',
				'thumbnail',
				'title'
			);
		}

		return compact( 'ancestors' );
	}
}
