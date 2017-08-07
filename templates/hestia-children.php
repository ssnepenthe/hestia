<?php
/**
 * The children shortcode template.
 *
 * @package hestia
 */

foreach ( $children as $child ) : ?>
	<div class="hestia-child<?php echo ( $thumbnails && has_post_thumbnail( $child ) ) ? ' hestia-has-thumbnail' : ''; ?> hestia-post-<?php echo esc_attr( $child->ID ); ?> hestia-wrap">
		<a href="<?php echo esc_url( get_the_permalink( $child ) ); ?>">
			<?php if ( $thumbnails && has_post_thumbnail( $child ) ) : ?>
				<?php echo get_the_post_thumbnail( $child ); ?>
			<?php endif; ?>

			<?php echo esc_html( get_the_title( $child ) ); ?>
		</a>
	</div>
<?php endforeach; ?>
