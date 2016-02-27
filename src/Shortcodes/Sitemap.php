<?php

namespace SSNepenthe\Hestia\Shortcodes;

class Sitemap {
	public function init() {
		add_shortcode( 'sitemap', [ $this, 'shortcode_handler' ] );
	}

	public function shortcode_handler( $atts, $content = null, $tag ) {
		$post_types = get_post_types( array(
			'public' => true,
		));

		$r = '';

		foreach( $post_types as $post_type ) {
			if ( in_array( $post_type, array( 'attachment' ) ) ) {
				continue;
			}

			$pt_object = get_post_type_object( $post_type );
			if ( $pt_object->labels->name == 'Posts' ) {
				$r .= "<h2>Blog Posts</h2>\n<ul>\n<li><a href=\"" . esc_url( trailingslashit( home_url( '/blog' ) ) ) . "\">Blog Index</a></li>";
			} elseif ( $pt_object->has_archive == 1 ) {
				$r .= "<h2>" . $pt_object->labels->name . "</h2>\n<ul>\n<li><a href=\"" . esc_url( trailingslashit( home_url( '/' . $pt_object->rewrite['slug'] ) ) ) . "\">" . $pt_object->labels->name . " Index</a></li>";
			} else {
				$r .= "<h2>" . $pt_object->labels->name . "</h2>\n<ul>";
			}

			$pt_args = array(
				'post_type' => $post_type,
				'posts_per_page' => -1,
			);
			$pt_query = new WP_Query( $pt_args );

			while ( $pt_query->have_posts() ) {
				$pt_query->the_post();
				$r .= "\n<li><a href=\"" . get_permalink() . "\">" . get_the_title() . "</a></li>";
			}
			$r .= "\n</ul>\n";
		}

		return $r;
	}
}
