<?php

class Ancestors_Test extends Hestia_Shortcode_Test_Case {
	protected $hestia_attachments = [];
	protected $hestia_posts = [];

	function setUp() {
		parent::setUp();

		$this->hestia_posts['first'] = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );
		$this->hestia_posts['second'] = $this->factory()->post->create_and_get( [
			'post_parent' => $this->hestia_posts['first']->ID,
			'post_type' => 'page',
		] );
		$this->hestia_posts['third'] = $this->factory()->post->create_and_get( [
			'post_parent' => $this->hestia_posts['second']->ID,
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

		$GLOBALS['post'] = $this->hestia_posts['third'];

		$expected = [
			sprintf( '<div class="hestia-ancestor hestia-post-%s hestia-wrapper">', $this->hestia_posts['first']->ID ),
			sprintf( '<a href="%s">', get_permalink( $this->hestia_posts['first']->ID ) ),
			sprintf( '%s		</a>', $this->hestia_posts['first']->post_title ),
			'</div>',
			sprintf( '<div class="hestia-ancestor hestia-post-%s hestia-wrapper">', $this->hestia_posts['second']->ID ),
			sprintf( '<a href="%s">', get_permalink( $this->hestia_posts['second']->ID ) ),
			sprintf( '%s		</a>', $this->hestia_posts['second']->post_title ),
			'</div>',
		];

		// Re-indexed for easier comparison.
		$actual = array_values(
			// Render, explode on newline, trim each line and then remove empties.
			array_filter( array_map( 'trim', explode( PHP_EOL, do_shortcode( '[ancestors]' ) ) ) )
		);

		$this->assertEquals( $expected, $actual );
	}

	/** @test */
	function descending_order() {
		$GLOBALS['post'] = $this->hestia_posts['third'];

		$this->assertShortcodeContent(
			sprintf(
				'%s %s',
				$this->hestia_posts['second']->post_title,
				$this->hestia_posts['first']->post_title
			),
			do_shortcode( '[ancestors order="DESC"]' )
		);
	}

	/** @test */
	function with_thumbnails() {
		set_post_thumbnail(
			$this->hestia_posts['second'],
			$this->hestia_attachments['first']
		);

		$GLOBALS['post'] = $this->hestia_posts['third'];

		$shortcode = do_shortcode( '[ancestors thumbnails="true"]' );

		$this->assertContains(
			get_the_post_thumbnail( $this->hestia_posts['second']->ID ),
			$shortcode
		);
		$this->assertShortcodeContent(
			sprintf(
				'%s %s',
				$this->hestia_posts['first']->post_title,
				$this->hestia_posts['second']->post_title
			),
			$shortcode
		);
	}

	/** @test */
	function descending_with_thumbnails() {
		set_post_thumbnail(
			$this->hestia_posts['second'],
			$this->hestia_attachments['first']
		);

		$GLOBALS['post'] = $this->hestia_posts['third'];

		$shortcode = do_shortcode( '[ancestors order="DESC" thumbnails="true"]' );

		$this->assertContains(
			get_the_post_thumbnail( $this->hestia_posts['second']->ID ),
			$shortcode
		);
		$this->assertShortcodeContent(
			sprintf(
				'%s %s',
				$this->hestia_posts['second']->post_title,
				$this->hestia_posts['first']->post_title
			),
			$shortcode
		);
	}

	/** @test */
	function it_fails_gracefully() {
		$GLOBALS['post'] = $this->hestia_posts['first'];

		$this->assertShortcodeContent( '', do_shortcode( '[ancestors]' ) );
	}
}
