<?php

namespace SSNepenthe\Hestia;

use \WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * @todo  We should probably be checking is_post_type_hierarchical for all of
 *        these so we can return early if not, except maybe attachments.
 */

function ancestors_handler( $atts, $content = null, $tag = '' ) {
	$r = '';

	$ancestors = get_post_ancestors( get_the_ID() );

	if ( $ancestors ) :
		$ancestors = array_reverse( $ancestors );

		foreach ( $ancestors as $ancestor ) :
			$thumbnail = has_post_thumbnail( $ancestor );
			$permalink = get_permalink( $ancestor ); // esc_attr?

			$classes = [
				'hestia-wrap',
				'hestia-ancestor',
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
				get_the_title( $ancestor )
			);

			$r .= '</div>';
		endforeach;
	endif;

	return $r;
}

/**
 * @todo  check out attachment functions in wp-includes/post.php starting around line 4808
 *        wp_get_attachment_metadata
 *            width, height, file, sizes, image_meta
 *        wp_get_attachment_url
 *            original file URL
 *        wp_get_attachment_thumb_file
 *            false???
 *        wp_get_attachment_thumb_url
 *            URL to attachment thumbnail
 *        wp_attachment_is( {image, audio, video} ) (also wp_attachment_is_image)
 *            true/false
 *        wp_mime_type_icon
 *            URL to mime type icon, there is not an icon for images
 *
 *        wp-includes/media.php at line 695
 *        wp_get_attachment_image_src
 *        wp_get_attachment_image
 *
 *        if we are limiting to just images:
 *
 *        I am thinking register a small, icon-sized image size and then use
 *        wp_get_attachment_image and list attachments with a finder-like styling
 *
 *        or maybe we can treat this as an alternative gallery?
 *        set it up for use with lightbox plugins? just a thought.
 *        could be user selected through shrotcode atts.
 *
 *        or maybe it is better to list all attachment types?
 */
function attachments_handler( $atts, $content = null, $tag = '' ) {
	$r = '';

	$args = [
		'order'				=> 'ASC',
		'orderby'			=> 'menu_order',
		'post_parent'		=> get_the_ID(),
		'posts_per_page'	=> -1,
		'post_status'       => 'inherit', // http://codex.wordpress.org/Class_Reference/WP_Query#Type_Parameters
		'post_type'			=> 'attachment',
	];

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) :
		while ( $query->have_posts() ) :
			$query->the_post();

			$permalink = wp_get_attachment_url(); // use get_permalink() for attachment page

			$classes = [
				'hestia-wrap',
				'hestia-attachment',
				'post-' . get_the_ID()
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
 * @see  http://codex.wordpress.org/Function_Reference/get_page_children
 * @see  http://codex.wordpress.org/Function_Reference/get_children
 */
function children_handler( $atts, $content = null, $tag = '' ) {
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
				'hestia-wrap',
				'hestia-child',
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

function family_handler( $atts, $content = null, $tag = '' ) {
	// Not sure what I want to do here...
	// should get ancestors, siblings, children, attachments
	// should display in a hierarchy
	// maybe with finder-like styling
}

function siblings_handler( $atts, $content = null, $tag = '' ) {
	$r = '';

	$id = get_the_ID();

	$args = [
		'post__not_in'  	=> [ $id ],
		'order'				=> 'ASC',
		'orderby'			=> 'menu_order',
		'post_parent'		=> wp_get_post_parent_id( $id ),
		'posts_per_page'	=> -1,
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
