<?php
/**
 * The ancestors shortcode template.
 *
 * @package hestia
 */

foreach ( $ancestors as $ancestor ) : ?>
	<div class="hestia-ancestor<?php echo ( $thumbnails && has_post_thumbnail( $ancestor ) ) ? ' hestia-has-thumbnail' : ''; ?> hestia-post-<?php echo esc_attr( $ancestor->ID ); ?> hestia-wrapper">
		<a href="<?php echo esc_url( get_the_permalink( $ancestor ) ); ?>">
			<?php if ( $thumbnails && has_post_thumbnail( $ancestor ) ) : ?>
				<?php echo get_the_post_thumbnail( $ancestor ); ?>
			<?php endif; ?>

			<?php echo esc_html( get_the_title( $ancestor ) ); ?>
		</a>
	</div>
<?php endforeach; ?>
