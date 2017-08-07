<?php

class Attachments_Test extends Hestia_Shortcode_Test_Case {
	protected $hestia_attachments = [];
	protected $hestia_posts = [];

	function setUp() {
		parent::setUp();

		$this->hestia_posts['first'] = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );

		$this->hestia_attachments['first'] = $this->factory()->attachment->create_object( [
			'file' => 'first.jpg',
			'post_date' => date( 'Y-m-d H:i:s', time() - 60 ),
			'post_mime_type' => 'image/jpeg',
			'post_parent' => $this->hestia_posts['first']->ID,
			'post_title' => 'First Attachment',
		] );
		$this->hestia_attachments['second'] = $this->factory()->attachment->create_object( [
			'file' => 'second.jpg',
			'post_mime_type' => 'image/jpeg',
			'post_parent' => $this->hestia_posts['first']->ID,
			'post_title' => 'Second Attachment',
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
			sprintf( '<div class="hestia-attachment hestia-post-%s hestia-wrap">', $this->hestia_attachments['first'] ),
			sprintf( '<a href="%s">', get_permalink( $this->hestia_attachments['first'] ) ),
			sprintf( '%s		</a>', get_the_title( $this->hestia_attachments['first'] ) ),
			'</div>',
			sprintf( '<div class="hestia-attachment hestia-post-%s hestia-wrap">', $this->hestia_attachments['second'] ),
			sprintf( '<a href="%s">', get_permalink( $this->hestia_attachments['second'] ) ),
			sprintf( '%s		</a>', get_the_title( $this->hestia_attachments['second'] ) ),
			'</div>',
		];

		// Re-indexed for easier comparison.
		$actual = array_values(
			// Render, explode on newline, trim each line and then remove empties.
			array_filter( array_map( 'trim', explode( PHP_EOL, do_shortcode( '[attachments]' ) ) ) )
		);

		$this->assertEquals( $expected, $actual );
	}

	/** @test */
	function link_to_file() {
		$GLOBALS['post'] = $this->hestia_posts['first'];

		$shortcode = do_shortcode( '[attachments link="FILE"]' );

		$this->assertContains(
			wp_get_attachment_url( $this->hestia_attachments['first'] ),
			$shortcode
		);
		$this->assertContains(
			wp_get_attachment_url( $this->hestia_attachments['second'] ),
			$shortcode
		);
	}

	/** @test */
	function override_max_attachments() {
		$GLOBALS['post'] = $this->hestia_posts['first'];

		$this->assertShortcodeContent(
			get_the_title( $this->hestia_attachments['first'] ),
			do_shortcode( '[attachments max="1"]' )
		);
	}

	/** @test */
	function descending_order() {
		$GLOBALS['post'] = $this->hestia_posts['first'];

		$this->assertShortcodeContent(
			sprintf(
				'%s %s',
				get_the_title( $this->hestia_attachments['second'] ),
				get_the_title( $this->hestia_attachments['first'] )
			),
			do_shortcode( '[attachments order="DESC"]' )
		);
	}

	/** @test */
	function link_to_file_with_custom_max_in_descending_order() {
		$GLOBALS['post'] = $this->hestia_posts['first'];

		$shortcode = do_shortcode(
			'[attachments link="FILE" max="1" order="DESC"]'
		);

		$this->assertContains(
			wp_get_attachment_url( $this->hestia_attachments['second'] ),
			$shortcode
		);
		$this->assertShortcodeContent(
			get_the_title( $this->hestia_attachments['second'] ),
			$shortcode
		);
	}

	/** @test */
	function it_fails_gracefully() {
		// Post without attachments.
		$post = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );

		$GLOBALS['post'] = $post;

		$this->assertShortcodeContent( '', do_shortcode( '[attachments]' ) );
	}
}
