<?php
/**
 * The ancestors shortcode template.
 *
 * @package hestia
 */

foreach ( $ancestors as $ancestor ) : ?>
	<div class="<?php echo $ancestor['thumbnail'] ? 'has-post-thumbnail ' : '' ?>hestia-ancestor hestia-wrap post-<?php echo esc_attr( $ancestor['id'] ) ?>">
		<a href="<?php echo esc_url( $ancestor['permalink'] ) ?>">
			<?php echo $ancestor['thumbnail'] // WPCS: XSS OK. ?>
			<?php echo esc_html( $ancestor['title'] ) ?>
		</a>
	</div>
<?php endforeach; ?>
