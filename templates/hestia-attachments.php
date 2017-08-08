<?php
/**
 * The attachments shortcode template.
 *
 * @package hestia
 *
 * @todo Not sure if this is the most appropriate way to check for thumbnail...
 */

foreach ( $attachments as $attachment ) : ?>
	<div class="hestia-attachment<?php echo ( $thumbnails && wp_get_attachment_thumb_url( $attachment->ID ) ) ? ' hestia-has-thumbnail' : ''; ?> hestia-post-<?php echo esc_attr( $attachment->ID ); ?> hestia-wrapper">
		<?php if ( 'PAGE' === $link_to ) : ?>
			<a href="<?php echo esc_url( get_permalink( $attachment ) ); ?>">
		<?php else : ?>
			<a href="<?php echo esc_url( wp_get_attachment_url( $attachment->ID ) ); ?>">
		<?php endif; ?>
			<?php if ( $thumbnails && wp_get_attachment_thumb_url( $attachment->ID ) ) : ?>
				<?php echo wp_get_attachment_image( $attachment->ID ); ?>
			<?php endif; ?>
			<?php echo esc_html( get_the_title( $attachment ) ); ?>
		</a>
	</div>
<?php endforeach; ?>
