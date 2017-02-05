<?php foreach ( $sections as $section ) : ?>
	<div class="hestia-sitemap hestia-wrap post-type-<?php echo esc_attr( $section['type'] ) ?>">
		<h2>Recent <?php echo esc_html( $section['name'] ) ?></h2>

		<ul>
			<?php foreach ( $section['posts'] as $post ) : ?>
				<li>
					<a href="<?php echo esc_url( $post['permalink'] ) ?>">
						<?php echo esc_html( $post['title'] ) ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endforeach; ?>
