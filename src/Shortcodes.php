<?php

namespace SSNepenthe\WPHestia;

use \WP_Query;


/**
 * @todo  We should probably be checking is_post_type_hierarchical for all of these,
 *        except maybe attachments
 */
class Shortcodes {
	public function __construct() {}

	public static function ancestors_handler( $atts, $content = null, $tag = '' ) {
		$r = '';

		$ancestors = get_post_ancestors( get_the_ID() );

		if ( $ancestors ) :
			$ancestors = array_reverse( $ancestors );

			foreach ( $ancestors as $ancestor ) :
				$thumbnail = has_post_thumbnail( $ancestor );
				$permalink = get_permalink( $ancestor ); // esc_attr?

				$classes = [
					'wp-hestia-wrap',
					'wp-hestia-ancestor',
					'post-' . $ancestor
				];
				if ( $thumbnail ) {
					$classes[] = 'has-post-thumbnail';
				}

				$r .= sprintf( '<div class="%1$s">', implode( $classes, ' ' ) );

				if ( $thumbnail ) : // placeholder if false?
					$r .= sprintf( '<a href="%1$s">%2$s</a>',
						$permalink,
						get_the_post_thumbnail( $ancestor )
					);
				endif;

				$r .= sprintf( '<a href="%1$s">%2$s</a>',
					$permalink,
					get_the_title()
				);

				$r .= '</div>';
			endforeach;
		endif;

		return $r;
	}

	/**
	 * @todo  check out attachment functions in wp-includes/post.php starting around line 4808
	 */
	public static function attachments_handler( $atts, $content = null, $tag = '' ) {
		$r = '';

		$args = array(
			'order'				=> 'ASC',
			'orderby'			=> 'menu_order',
			'post_parent'		=> get_the_ID(),
			'posts_per_page'	=> -1,
			'post_status'       => 'inherit', // http://codex.wordpress.org/Class_Reference/WP_Query#Type_Parameters
			'post_type'			=> 'attachment',
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();

				$r .= '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
			endwhile;
		endif;

		wp_reset_postdata();

		return $r;
	}

	/**
	 * @see  http://codex.wordpress.org/Function_Reference/get_page_children
	 * @see  http://codex.wordpress.org/Function_Reference/get_children
	 */
	public static function children_handler( $atts, $content = null, $tag = '' ) {
		$r = '';

		$args = array (
			'order'				=> 'ASC',
			'orderby'			=> 'menu_order',
			'post_parent'		=> get_the_ID(),
			'posts_per_page'	=> -1,
			'post_type'			=> get_post_type() // returns nothing if we don't include post type
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();

				$thumbnail = has_post_thumbnail();
				$permalink = get_permalink();

				$classes = [
					'wp-hestia-wrap',
					'wp-hestia-child',
					'post-' . get_the_ID()
				];

				if ( $thumbnail ) {
					$classes[] = 'has-post-thumbnail';
				}

				$r .= sprintf( '<div class="%1$s">', implode( $classes, ' ' ) );

				if ( $thumbnail ) :
					$r .= sprintf( '<a href="%1$s">%2$s</a>',
						$permalink,
						get_the_post_thumbnail()
					);
				endif;

				$r .= sprintf( '<a href="%1$s">%2$s</a>',
					$permalink,
					get_the_title()
				);

				$r .= '</div>';
			endwhile;
		endif;

		wp_reset_postdata();

		return $r;
	}

	public static function family_handler( $atts, $content = null, $tag = '' ) {
		// Not sure what I want to do here...
		// should get ancestors, siblings, children, attachments
		// should display in a hierarchy
		// maybe with finder-like styling
	}

	public static function siblings_handler( $atts, $content = null, $tag = '' ) {
		$r = '';

		$id = get_the_ID();

		$args = array(
			'post__not_in'  	=> [ $id ],
			'order'				=> 'ASC',
			'orderby'			=> 'menu_order',
			'post_parent'		=> wp_get_post_parent_id( $id ),
			'posts_per_page'	=> -1,
			'post_type'			=> get_post_type(),
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) :
				$query->the_post();

				$thumbnail = has_post_thumbnail();
				$permalink = get_permalink();

				$classes = [
					'wp-hestia-wrap',
					'wp-hestia-sibling',
					'post-' . get_the_ID()
				];

				if ( $thumbnail ) {
					$classes[] = 'has-post-thumbnail';
				}

				$r .= sprintf( '<div class="%1$s">', implode( $classes, ' ' ) );

				if ( $thumbnail ) :
					$r .= sprintf( '<a href="%1$s">%2$s</a>',
						$permalink,
						get_the_post_thumbnail()
					);
				endif;

				$r .= sprintf( '<a href="%1$s">%2$s</a>',
					$permalink,
					get_the_title()
				);

				$r .= '</div>';
			endwhile;
		endif;

		wp_reset_postdata();

		return $r;
	}
}
