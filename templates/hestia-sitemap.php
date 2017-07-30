<?php
/**
 * The sitemap shortcode template.
 *
 * @package hestia
 */

foreach ( $sections as $section ) : ?>
	<div class="hestia-sitemap hestia-wrap post-type-<?php echo esc_attr( $section['type'] ); ?>">
		<h2>Recent <?php echo esc_html( $section['name'] ); ?></h2>

		<ul>
			<?php foreach ( $section['links'] as $link ) : ?>
				<li>
					<a href="<?php echo esc_url( $link['permalink'] ); ?>">
						<?php echo esc_html( $link['title'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endforeach; ?>
