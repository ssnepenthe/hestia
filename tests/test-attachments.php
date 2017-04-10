<?php

class Attachments_Test extends WP_UnitTestCase {
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
		add_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $this->hestia_posts['first'];

		$rendered = sprintf(
			'<div class="hestia-attachment hestia-wrap post-%s">
		<a href="%s">
			%s		</a>
	</div>
	<div class="hestia-attachment hestia-wrap post-%s">
		<a href="%s">
			%s		</a>
	</div>',
			$this->hestia_attachments['first'],
			get_permalink( $this->hestia_attachments['first'] ),
			get_the_title( $this->hestia_attachments['first'] ),
			$this->hestia_attachments['second'],
			get_permalink( $this->hestia_attachments['second'] ),
			get_the_title( $this->hestia_attachments['second'] )
		);

		$this->assertEquals( $rendered, trim( do_shortcode( '[attachments]' ) ) );

		remove_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function link_to_file() {
		add_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $this->hestia_posts['first'];

		$rendered = sprintf(
			'<div class="hestia-attachment hestia-wrap post-%s">
		<a href="%s">
			%s		</a>
	</div>
	<div class="hestia-attachment hestia-wrap post-%s">
		<a href="%s">
			%s		</a>
	</div>',
			$this->hestia_attachments['first'],
			wp_get_attachment_url( $this->hestia_attachments['first'] ),
			get_the_title( $this->hestia_attachments['first'] ),
			$this->hestia_attachments['second'],
			wp_get_attachment_url( $this->hestia_attachments['second'] ),
			get_the_title( $this->hestia_attachments['second'] )
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[attachments link="FILE"]' ) )
		);

		remove_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function override_max() {
		add_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $this->hestia_posts['first'];

		$rendered = sprintf(
			'<div class="hestia-attachment hestia-wrap post-%s">
		<a href="%s">
			%s		</a>
	</div>',
			$this->hestia_attachments['first'],
			get_permalink( $this->hestia_attachments['first'] ),
			get_the_title( $this->hestia_attachments['first'] )
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[attachments max="1"]' ) )
		);

		remove_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function descending_order() {
		add_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $this->hestia_posts['first'];

		$rendered = sprintf(
			'<div class="hestia-attachment hestia-wrap post-%s">
		<a href="%s">
			%s		</a>
	</div>
	<div class="hestia-attachment hestia-wrap post-%s">
		<a href="%s">
			%s		</a>
	</div>',
			$this->hestia_attachments['second'],
			get_permalink( $this->hestia_attachments['second'] ),
			get_the_title( $this->hestia_attachments['second'] ),
			$this->hestia_attachments['first'],
			get_permalink( $this->hestia_attachments['first'] ),
			get_the_title( $this->hestia_attachments['first'] )
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[attachments order="DESC"]' ) )
		);

		remove_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function link_to_file_with_custom_max_in_descending_order() {
		add_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $this->hestia_posts['first'];

		$rendered = sprintf(
			'<div class="hestia-attachment hestia-wrap post-%s">
		<a href="%s">
			%s		</a>
	</div>',
			$this->hestia_attachments['second'],
			wp_get_attachment_url( $this->hestia_attachments['second'] ),
			get_the_title( $this->hestia_attachments['second'] )
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[attachments link="FILE" max="1" order="DESC"]' ) )
		);

		remove_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function it_fails_gracefully() {
		// Post without attachments.
		$post = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );

		add_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $post;

		$this->assertEquals( '', trim( do_shortcode( '[attachments]' ) ) );

		remove_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );
	}
}
