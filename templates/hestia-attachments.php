<?php
/**
 * The attachments shortcode template.
 *
 * @package hestia
 */

foreach ( $attachments as $attachment ) : ?>
	<div class="hestia-attachment hestia-wrap post-<?php echo esc_attr( $attachment['id'] ); ?>">
		<a href="<?php echo esc_url( $attachment['permalink'] ); ?>">
			<?php echo esc_html( $attachment['title'] ); ?>
		</a>
	</div>
<?php endforeach; ?>
