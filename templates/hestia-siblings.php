<?php
/**
 * The siblings shortcode template.
 *
 * @package hestia
 */

foreach ( $siblings as $sibling ) : ?>
	<div class="<?php echo ( $thumbnails && has_post_thumbnail( $sibling ) ) ? 'hestia-has-thumbnail ' : ''; ?>hestia-post-<?php echo esc_attr( $sibling->ID ); ?> hestia-sibling hestia-wrap">
		<a href="<?php echo esc_url( get_the_permalink( $sibling ) ); ?>">
			<?php if ( $thumbnails && has_post_thumbnail( $sibling ) ) : ?>
				<?php echo get_the_post_thumbnail( $sibling ); ?>
			<?php endif; ?>

			<?php echo esc_html( get_the_title( $sibling ) ); ?>
		</a>
	</div>
<?php endforeach; ?>
