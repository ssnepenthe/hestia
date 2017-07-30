<?php
/**
 * The children shortcode template.
 *
 * @package hestia
 */

foreach ( $children as $child ) : ?>
	<div class="<?php echo $child['thumbnail'] ? 'has-post-thumbnail ' : ''; ?>hestia-child hestia-wrap post-<?php echo esc_attr( $child['id'] ); ?>">
		<a href="<?php echo esc_url( $child['permalink'] ); ?>">
			<?php
				// Spacing is necessary for WPCS.
				echo $child['thumbnail']; // WPCS: XSS OK.
			?>
			<?php echo esc_html( $child['title'] ); ?>
		</a>
	</div>
<?php endforeach; ?>
