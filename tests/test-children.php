<?php

class Children_Test extends Hestia_Shortcode_Test_Case {
	protected $hestia_attachments = [];
	protected $hestia_posts = [];

	function setUp() {
		parent::setUp();

		$this->hestia_posts['first'] = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );
		$this->hestia_posts['second'] = $this->factory()->post->create_and_get( [
			'post_date' => \date( 'Y-m-d H:i:s', \time() - 60 ),
			'post_parent' => $this->hestia_posts['first']->ID,
			'post_type' => 'page',
		] );
		$this->hestia_posts['third'] = $this->factory()->post->create_and_get( [
			'post_parent' => $this->hestia_posts['first']->ID,
			'post_type' => 'page',
		] );
		$this->hestia_posts['fourth'] = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );

		$this->hestia_attachments['first'] = $this->factory()->attachment->create_object( [
			'file' => 'image.jpg',
			'post_parent' => $this->hestia_posts['second']->ID,
			'post_mime_type' => 'image/jpeg',
		] );
	}

	function tearDown() {
		parent::tearDown();

		$this->hestia_attachments = [];
		$this->hestia_posts = [];
	}

	/** @test */
	function basic_output() {
		// Attempts to simplify comparison by minimizing the impact of whitespace.

		$GLOBALS['post'] = $this->hestia_posts['first'];

		$expected = [
			\sprintf( '<div class="hestia-child hestia-post-%s hestia-wrapper">', $this->hestia_posts['second']->ID ),
			\sprintf( '<a href="%s">', get_permalink( $this->hestia_posts['second']->ID ) ),
			\sprintf( '%s		</a>', $this->hestia_posts['second']->post_title ),
			'</div>',
			\sprintf( '<div class="hestia-child hestia-post-%s hestia-wrapper">', $this->hestia_posts['third']->ID ),
			\sprintf( '<a href="%s">', get_permalink( $this->hestia_posts['third']->ID ) ),
			\sprintf( '%s		</a>', $this->hestia_posts['third']->post_title ),
			'</div>',
		];

		// Re-indexed for easier comparison.
		$actual = \array_values(
			// Render, explode on newline, trim each line and then remove empties.
			\array_filter( \array_map( 'trim', \explode( PHP_EOL, do_shortcode( '[children]' ) ) ) )
		);

		$this->assertEquals( $expected, $actual );
	}

	/** @test */
	function override_max() {
		$GLOBALS['post'] = $this->hestia_posts['first'];

		$this->assertShortcodeContent(
			$this->hestia_posts['second']->post_title,
			do_shortcode( '[children max="1"]' )
		);
	}

	/** @test */
	function descending_order() {
		$GLOBALS['post'] = $this->hestia_posts['first'];

		$this->assertShortcodeContent(
			\sprintf(
				'%s %s',
				$this->hestia_posts['third']->post_title,
				$this->hestia_posts['second']->post_title
			),
			do_shortcode( '[children order="DESC"]' )
		);
	}

	/** @test */
	function with_thumbnails() {
		set_post_thumbnail(
			$this->hestia_posts['second'],
			$this->hestia_attachments['first']
		);

		$GLOBALS['post'] = $this->hestia_posts['first'];
		$shortcode = do_shortcode( '[children thumbnails="true"]' );

		$this->assertContains(
			get_the_post_thumbnail( $this->hestia_posts['second']->ID ),
			$shortcode
		);
		$this->assertShortcodeContent(
			\sprintf(
				'%s %s',
				$this->hestia_posts['second']->post_title,
				$this->hestia_posts['third']->post_title
			),
			$shortcode
		);
	}

	/** @test */
	function custom_max_in_descending_order_with_thumbnails() {
		set_post_thumbnail(
			$this->hestia_posts['third'],
			$this->hestia_attachments['first']
		);

		$GLOBALS['post'] = $this->hestia_posts['first'];
		$shortcode = do_shortcode(
			'[children max="1" order="DESC" thumbnails="true"]'
		);

		$this->assertContains(
			get_the_post_thumbnail( $this->hestia_posts['third']->ID ),
			$shortcode
		);
		$this->assertShortcodeContent(
			$this->hestia_posts['third']->post_title,
			$shortcode
		);
	}

	/** @test */
	function with_custom_id() {
		// Without post global.
		$this->assertShortcodeContent( '', do_shortcode( '[children]' ) );
		$this->assertShortcodeContent(
			\sprintf(
				'%s %s',
				$this->hestia_posts['second']->post_title,
				$this->hestia_posts['third']->post_title
			),
			do_shortcode( "[children id=\"{$this->hestia_posts['first']->ID}\"]" )
		);

		// With post global.
		$GLOBALS['post'] = $this->hestia_posts['fourth'];

		$this->assertShortcodeContent( '', do_shortcode( '[children]' ) );
		$this->assertShortcodeContent(
			\sprintf(
				'%s %s',
				$this->hestia_posts['second']->post_title,
				$this->hestia_posts['third']->post_title
			),
			do_shortcode( "[children id=\"{$this->hestia_posts['first']->ID}\"]" )
		);
	}

	/** @test */
	function it_fails_gracefully() {
		$GLOBALS['post'] = $this->hestia_posts['second'];

		$this->assertShortcodeContent( '', do_shortcode( '[children]' ) );
	}
}
