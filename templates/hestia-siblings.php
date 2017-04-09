<?php
/**
 * The siblings shortcode template.
 *
 * @package hestia
 */

foreach ( $siblings as $sibling ) : ?>
	<div class="<?php echo $sibling['thumbnail'] ? 'has-post-thumbnail ' : '' ?>hestia-sibling hestia-wrap post-<?php echo esc_attr( $sibling['id'] ) ?>">
		<a href="<?php echo esc_url( $sibling['permalink'] ) ?>">
			<?php echo $sibling['thumbnail'] // WPCS: XSS OK. ?>
			<?php echo esc_html( $sibling['title'] ) ?>
		</a>
	</div>
<?php endforeach; ?>
