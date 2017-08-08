<?php

class Sitemap_Test extends Hestia_Shortcode_Test_Case {
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
		$expected = [
			'<div class="hestia-sitemap hestia-post-type-post hestia-post-type-wrapper">',
			'<h2>',
			'Recent Posts		</h2>',
			'<ul>',
			sprintf( '<li class="hestia-post-%s hestia-wrapper">', $this->hestia_posts['first']->ID ),
			sprintf( '<a href="%s">', get_permalink( $this->hestia_posts['first']->ID ) ),
			sprintf( '%s					</a>', $this->hestia_posts['first']->post_title ),
			'</li>',
			sprintf( '<li class="hestia-post-%s hestia-wrapper">', $this->hestia_posts['second']->ID ),
			sprintf( '<a href="%s">', get_permalink( $this->hestia_posts['second']->ID ) ),
			sprintf( '%s					</a>', $this->hestia_posts['second']->post_title ),
			'</li>',
			sprintf( '<li class="hestia-post-%s hestia-wrapper">', $this->hestia_posts['third']->ID ),
			sprintf( '<a href="%s">', get_permalink( $this->hestia_posts['third']->ID ) ),
			sprintf( '%s					</a>', $this->hestia_posts['third']->post_title ),
			'</li>',
			'</ul>',
			'</div>',
			'<div class="hestia-sitemap hestia-post-type-page hestia-post-type-wrapper">',
			'<h2>',
			'Recent Pages		</h2>',
			'<ul>',
			sprintf( '<li class="hestia-post-%s hestia-wrapper">', $this->hestia_pages['first']->ID ),
			sprintf( '<a href="%s">', get_permalink( $this->hestia_pages['first']->ID ) ),
			sprintf( '%s					</a>', $this->hestia_pages['first']->post_title ),
			'</li>',
			sprintf( '<li class="hestia-post-%s hestia-wrapper">', $this->hestia_pages['second']->ID ),
			sprintf( '<a href="%s">', get_permalink( $this->hestia_pages['second']->ID ) ),
			sprintf( '%s					</a>', $this->hestia_pages['second']->post_title ),
			'</li>',
			sprintf( '<li class="hestia-post-%s hestia-wrapper">', $this->hestia_pages['third']->ID ),
			sprintf( '<a href="%s">', get_permalink( $this->hestia_pages['third']->ID ) ),
			sprintf( '%s					</a>', $this->hestia_pages['third']->post_title ),
			'</li>',
			'</ul>',
			'</div>',
		];

		$actual = array_values(
			array_filter( array_map( 'trim', explode( PHP_EOL, do_shortcode( '[sitemap]' ) ) ) )
		);

		$this->assertEquals( $expected, $actual );
	}

	/** @test */
	function override_max() {
		$this->assertShortcodeContent(
			sprintf(
				'Recent Posts %s %s Recent Pages %s %s',
				$this->hestia_posts['first']->post_title,
				$this->hestia_posts['second']->post_title,
				$this->hestia_pages['first']->post_title,
				$this->hestia_pages['second']->post_title
			),
			do_shortcode( '[sitemap max="2"]' )
		);
	}

	/** @test */
	function descending_order() {
		$this->assertShortcodeContent(
			sprintf(
				'Recent Posts %s %s %s Recent Pages %s %s %s',
				$this->hestia_posts['third']->post_title,
				$this->hestia_posts['second']->post_title,
				$this->hestia_posts['first']->post_title,
				$this->hestia_pages['third']->post_title,
				$this->hestia_pages['second']->post_title,
				$this->hestia_pages['first']->post_title
			),
			do_shortcode( '[sitemap order="DESC"]' )
		);
	}

	/** @test */
	function custom_max_in_descending_order() {
		$this->assertShortcodeContent(
			sprintf(
				'Recent Posts %s Recent Pages %s',
				$this->hestia_posts['third']->post_title,
				$this->hestia_pages['third']->post_title
			),
			do_shortcode( '[sitemap max="1" order="DESC"]' )
		);
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

		$this->assertShortcodeContent(
			sprintf(
				'Recent Posts %s %s %s Recent Pages %s %s %s Recent Just Testing %s',
				$this->hestia_posts['first']->post_title,
				$this->hestia_posts['second']->post_title,
				$this->hestia_posts['third']->post_title,
				$this->hestia_pages['first']->post_title,
				$this->hestia_pages['second']->post_title,
				$this->hestia_pages['third']->post_title,
				$custom->post_title
			),
			do_shortcode( '[sitemap]' )
		);

		unregister_post_type( 'testing' );
	}
}
