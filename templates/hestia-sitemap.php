<?php
/**
 * The sitemap shortcode template.
 *
 * @package hestia
 */

foreach ( $posts as $post_type => $post_list ) : ?>
	<div class="hestia-sitemap hestia-post-type-<?php echo esc_attr( $post_type ); ?> hestia-post-type-wrapper">
		<h2>
			Recent <?php echo esc_html( get_post_type_object( $post_type )->labels->name ); ?>
		</h2>

		<ul>
			<?php foreach ( $post_list as $post ) : ?>
				<li class="<?php echo ( $thumbnails && has_post_thumbnail( $post ) ) ? 'hestia-has-thumbnail ' : ''; ?>hestia-post-<?php echo esc_attr( $post->ID ); ?> hestia-wrapper">
					<a href="<?php echo esc_url( get_the_permalink( $post ) ); ?>">
						<?php if ( $thumbnails && has_post_thumbnail( $post ) ) : ?>
							<?php echo get_the_post_thumbnail( $post ); ?>
						<?php endif; ?>

						<?php echo esc_html( get_the_title( $post ) ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endforeach; ?>
