<?php

class Sitemap_Test extends WP_UnitTestCase {
	protected $hestia_pages = [];
	protected $hestia_posts = [];

	function setUp() {
		parent::setUp();

		$this->hestia_pages = array_combine(
			[ 'first', 'second', 'third' ],
			array_map( 'get_post', $this->factory()->post->create_many( 3, [
				'post_type' => 'page',
			] ) )
		);

		$this->hestia_posts = array_combine(
			[ 'first', 'second', 'third' ],
			array_map( 'get_post', $this->factory()->post->create_many( 3 ) )
		);
	}

	function tearDown() {
		parent::tearDown();

		$this->hestia_attachments = [];
		$this->hestia_posts = [];
	}

	/** @test */
	function basic_output() {
		add_filter( 'hestia_sitemap_cache_lifetime', '__return_zero' );

		$rendered = sprintf(
			'<div class="hestia-sitemap hestia-wrap post-type-post">
		<h2>Recent Posts</h2>

		<ul>
							%s
							%s
							%s
					</ul>
	</div>
	<div class="hestia-sitemap hestia-wrap post-type-page">
		<h2>Recent Pages</h2>

		<ul>
							%s
							%s
							%s
					</ul>
	</div>',
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_posts['first']->ID ),
				$this->hestia_posts['first']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_posts['second']->ID ),
				$this->hestia_posts['second']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_posts['third']->ID ),
				$this->hestia_posts['third']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_pages['first']->ID ),
				$this->hestia_pages['first']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_pages['second']->ID ),
				$this->hestia_pages['second']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_pages['third']->ID ),
				$this->hestia_pages['third']->post_title
			)
		);

		$this->assertEquals( $rendered, trim( do_shortcode( '[sitemap]' ) ) );

		remove_filter( 'hestia_sitemap_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function override_max() {
		add_filter( 'hestia_sitemap_cache_lifetime', '__return_zero' );

		$rendered = sprintf(
			'<div class="hestia-sitemap hestia-wrap post-type-post">
		<h2>Recent Posts</h2>

		<ul>
							%s
							%s
					</ul>
	</div>
	<div class="hestia-sitemap hestia-wrap post-type-page">
		<h2>Recent Pages</h2>

		<ul>
							%s
							%s
					</ul>
	</div>',
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_posts['first']->ID ),
				$this->hestia_posts['first']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_posts['second']->ID ),
				$this->hestia_posts['second']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_pages['first']->ID ),
				$this->hestia_pages['first']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_pages['second']->ID ),
				$this->hestia_pages['second']->post_title
			)
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[sitemap max="2"]' ) )
		);

		remove_filter( 'hestia_sitemap_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function descending_order() {
		add_filter( 'hestia_sitemap_cache_lifetime', '__return_zero' );

		$rendered = sprintf(
			'<div class="hestia-sitemap hestia-wrap post-type-post">
		<h2>Recent Posts</h2>

		<ul>
							%s
							%s
							%s
					</ul>
	</div>
	<div class="hestia-sitemap hestia-wrap post-type-page">
		<h2>Recent Pages</h2>

		<ul>
							%s
							%s
							%s
					</ul>
	</div>',
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_posts['third']->ID ),
				$this->hestia_posts['third']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_posts['second']->ID ),
				$this->hestia_posts['second']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_posts['first']->ID ),
				$this->hestia_posts['first']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_pages['third']->ID ),
				$this->hestia_pages['third']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_pages['second']->ID ),
				$this->hestia_pages['second']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_pages['first']->ID ),
				$this->hestia_pages['first']->post_title
			)
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[sitemap order="DESC"]' ) )
		);

		remove_filter( 'hestia_sitemap_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function custom_max_in_descending_order() {
		add_filter( 'hestia_sitemap_cache_lifetime', '__return_zero' );

		$rendered = sprintf(
			'<div class="hestia-sitemap hestia-wrap post-type-post">
		<h2>Recent Posts</h2>

		<ul>
							%s
					</ul>
	</div>
	<div class="hestia-sitemap hestia-wrap post-type-page">
		<h2>Recent Pages</h2>

		<ul>
							%s
					</ul>
	</div>',
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_posts['third']->ID ),
				$this->hestia_posts['third']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_pages['third']->ID ),
				$this->hestia_pages['third']->post_title
			)
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[sitemap max="1" order="DESC"]' ) )
		);

		remove_filter( 'hestia_sitemap_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function it_handles_custom_post_types() {
		register_post_type( 'testing', [
			'labels' => [
				'name' => 'Just Testing',
			],
			'public' => true,
		] );

		$custom = $this->factory()->post->create_and_get( [
			'post_type' => 'testing',
		] );

		$rendered = sprintf(
			'<div class="hestia-sitemap hestia-wrap post-type-post">
		<h2>Recent Posts</h2>

		<ul>
							%s
							%s
							%s
					</ul>
	</div>
	<div class="hestia-sitemap hestia-wrap post-type-page">
		<h2>Recent Pages</h2>

		<ul>
							%s
							%s
							%s
					</ul>
	</div>
	<div class="hestia-sitemap hestia-wrap post-type-testing">
		<h2>Recent Just Testing</h2>

		<ul>
							%s
					</ul>
	</div>',
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_posts['first']->ID ),
				$this->hestia_posts['first']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_posts['second']->ID ),
				$this->hestia_posts['second']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_posts['third']->ID ),
				$this->hestia_posts['third']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_pages['first']->ID ),
				$this->hestia_pages['first']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_pages['second']->ID ),
				$this->hestia_pages['second']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $this->hestia_pages['third']->ID ),
				$this->hestia_pages['third']->post_title
			),
			sprintf(
				$this->list_item(),
				get_permalink( $custom->ID ),
				$custom->post_title
			)
		);

		$this->assertEquals( $rendered, trim( do_shortcode( '[sitemap]' ) ) );

		unregister_post_type( 'testing' );
	}

	protected function list_item() {
		return '<li>
					<a href="%s">
						%s					</a>
				</li>';
	}
}
