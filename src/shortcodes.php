<?php
/**
 * Various shortcode handler functions pertaining to hierarchical posts.
 *
 * @package hestia
 *
 * @todo  We should probably be checking is_post_type_hierarchical for all of
 *        these so we can return early if not, except maybe attachments.
 */

namespace SSNepenthe\Hestia;

use \WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Handler function for the [ancestors] shortcode
 *
 * @param array       $atts Shortcode attributes.
 * @param string/null $content Shortcode content.
 * @param string      $tag The shortcode tag.
 *
 * @return string HTML formatted list of ancestor pages or empty string
 */
function ancestors_handler( $atts, $content = null, $tag = '' ) {
	$r = '';

	$ancestors = get_post_ancestors( get_the_ID() );

	if ( $ancestors ) :
		$ancestors = array_reverse( $ancestors );

		foreach ( $ancestors as $ancestor ) :
			$thumbnail = has_post_thumbnail( $ancestor );
			$permalink = get_permalink( $ancestor ); // Esc_attr?

			$classes = [
				'hestia-wrap',
				'hestia-ancestor',
				'post-' . $ancestor,
			];

			if ( $thumbnail ) {
				$classes[] = 'has-post-thumbnail';
			}

			$r .= sprintf( '<div class="%1$s">', implode( $classes, ' ' ) );

			if ( $thumbnail ) : // Placeholder if false?
				$r .= sprintf( '<a href="%1$s">%2$s</a>',
					$permalink,
					get_the_post_thumbnail( $ancestor )
				);
			endif;

			$r .= sprintf( '<a href="%1$s">%2$s</a>',
				$permalink,
				get_the_title( $ancestor )
			);

			$r .= '</div>';
		endforeach;
	endif;

	return $r;
}

/**
 * Handler function for the [attachments] shortcode
 *
 * @param array       $atts Shortcode attributes.
 * @param string/null $content Shortcode content.
 * @param string      $tag The shortcode tag.
 *
 * @todo Look into related core functionality - see referenced links
 *
 * @see https://developer.wordpress.org/reference/functions/wp_get_attachment_metadata/
 * @see https://developer.wordpress.org/reference/functions/wp_get_attachment_url/
 * @see https://developer.wordpress.org/reference/functions/wp_get_attachment_thumb_file/
 * @see https://developer.wordpress.org/reference/functions/wp_get_attachment_thumb_url/
 * @see https://developer.wordpress.org/reference/functions/wp_attachment_is/
 * @see https://developer.wordpress.org/reference/functions/wp_mime_type_icon/
 * @see https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/
 * @see https://developer.wordpress.org/reference/functions/wp_get_attachment_image/
 *
 * @return string HTML formatted list of attachment pages or empty string
 */
function attachments_handler( $atts, $content = null, $tag = '' ) {
	$r = '';

	$args = [
		'order'				=> 'ASC',
		'orderby'			=> 'menu_order',
		'post_parent'		=> get_the_ID(),
		'posts_per_page'	=> 20, // Should allow shortcode atts to override.
		'post_status'       => 'inherit',
		'post_type'			=> 'attachment',
	];

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :
			$query->the_post();

			$permalink = wp_get_attachment_url(); // Use get_permalink() for attachment page.

			$classes = [
				'hestia-wrap',
				'hestia-attachment',
				'post-' . get_the_ID(),
			];

			$r .= sprintf( '<div class="%1$s">', implode( $classes, ' ' ) );

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

/**
 * Handler function for the [children] shortcode
 *
 * @param  array       $atts Shortcode attributes.
 * @param  string/null $content Shortcode content.
 * @param  string      $tag The shortcode tag.
 *
 * @todo Look into similar core functionality - see referenced links
 *
 * @see  http://codex.wordpress.org/Function_Reference/get_children
 * @see  http://codex.wordpress.org/Function_Reference/get_page_children
 *
 * @return string HTML formatted list of children pages or empty string
 */
function children_handler( $atts, $content = null, $tag = '' ) {
	$r = '';

	$args = array(
		'order'				=> 'ASC',
		'orderby'			=> 'menu_order',
		'post_parent'		=> get_the_ID(),
		'posts_per_page'	=> 20, // Should allow shortcode atts to override.
		'post_type'			=> get_post_type(),// Returns nothing if we don't include post type.
	);

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :
			$query->the_post();

			$thumbnail = has_post_thumbnail();
			$permalink = get_permalink();

			$classes = [
				'hestia-wrap',
				'hestia-child',
				'post-' . get_the_ID(),
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

/**
 * Handler function for the [family] shortcode
 *
 * @param  array       $atts Shortcode attributes.
 * @param  string/null $content Shortcode content.
 * @param  string      $tag The shortcode tag.
 *
 * @return void
 */
function family_handler( $atts, $content = null, $tag = '' ) {
	/**
	 * Not sure what exactly I want to do here... It should definitely get all
	 * ancestors, siblings, children and attachments. It should probably
	 * display ina hierarchy, maybe with finder-like styling.
	 */
}

/**
 * Handler function for the [siblings] shortcode
 *
 * @param  array       $atts Shortcode attributes.
 * @param  string/null $content Shortcode content.
 * @param  string      $tag The shortcode tag.
 *
 * @return string HTML formatted list of sibling pages or empty string
 */
function siblings_handler( $atts, $content = null, $tag = '' ) {
	$r = '';

	$id = get_the_ID();

	$args = [
		'post__not_in'  	=> [ $id ],
		'order'				=> 'ASC',
		'orderby'			=> 'menu_order',
		'post_parent'		=> wp_get_post_parent_id( $id ),
		'posts_per_page'	=> 20, // TODO: allow this to be overridden in shortcode.
		'post_type'			=> get_post_type(),
	];

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :
			$query->the_post();

			$thumbnail = has_post_thumbnail();
			$permalink = get_permalink();

			$classes = [
				'hestia-wrap',
				'hestia-sibling',
				'post-' . get_the_ID(),
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
