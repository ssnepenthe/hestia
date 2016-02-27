<?php

namespace SSNepenthe\Hestia\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Ancestors {
	public function init() {
		add_shortcode( 'ancestors', [ $this, 'shortcode_handler' ] );
	}

	public function shortcode_handler( $atts, $content = null, $tag = '' ) {
		if ( ! is_post_type_hierarchical( get_post_type() ) ) {
			return '';
		}

		$ancestors = get_post_ancestors( get_the_ID() );

		if ( ! $ancestors ) {
			return '';
		}

		$r = [];
		$ancestors = array_reverse( $ancestors );

		foreach ( $ancestors as $ancestor ) {
			$classes = [
				'hestia-ancestor',
				'hestia-wrap',
				sprintf( 'post-%s', $ancestor ),
			];
			$permalink = get_permalink( $ancestor );
			$has_thumbnail = has_post_thumbnail( $ancestor );

			if ( $has_thumbnail ) {
				// Because who doesn't love a properly alphabetized list?
				array_unshift( $classes, 'has-post-thumbnail' );
			}

			$r[] = sprintf( '<div class="%s">', implode( ' ', $classes ) );
			$r[] = sprintf(
				'<a href="%1$s">',
				esc_attr( $permalink )
			);

			if ( $has_thumbnail ) {
				$r[] = get_the_post_thumbnail( $ancestor );
			}

			$r[] = get_the_title( $ancestor );
			$r[] = '</a>';
			$r[] = '</div>';
		}

		return implode( "\n", $r );
	}
}
