<?php
/**
 * The ancestors shortcode template.
 *
 * @package hestia
 */

foreach ( $ancestors as $ancestor ) : ?>
	<div class="<?php echo ( $thumbnails && has_post_thumbnail( $ancestor ) ) ? 'has-post-thumbnail ' : ''; ?>hestia-ancestor hestia-wrap post-<?php echo esc_attr( $ancestor->ID ); ?>">
		<a href="<?php echo esc_url( get_the_permalink( $ancestor ) ); ?>">
			<?php if ( $thumbnails && has_post_thumbnail( $ancestor ) ) : ?>
				<?php echo get_the_post_thumbnail( $ancestor ); ?>
			<?php endif; ?>

			<?php echo esc_html( get_the_title( $ancestor ) ); ?>
		</a>
	</div>
<?php endforeach; ?>
