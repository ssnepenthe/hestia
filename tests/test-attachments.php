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
		$this->hestia_attachments = [];
		$this->hestia_posts = [];
	}

	/** @test */
	function basic_output() {
		add_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $this->hestia_posts['first'];

		$rendered = sprintf(
			'<div class="hestia-attachment hestia-wrap post-%1$s">
		<a href="%2$s">
			%3$s		</a>
	</div>
	<div class="hestia-attachment hestia-wrap post-%4$s">
		<a href="%5$s">
			%6$s		</a>
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
			'<div class="hestia-attachment hestia-wrap post-%1$s">
		<a href="%2$s">
			%3$s		</a>
	</div>
	<div class="hestia-attachment hestia-wrap post-%4$s">
		<a href="%5$s">
			%6$s		</a>
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
			'<div class="hestia-attachment hestia-wrap post-%1$s">
		<a href="%2$s">
			%3$s		</a>
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
		$post = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );

		$first = $this->factory()->attachment->create_object( [
			'file' => 'first.jpg',
			'post_mime_type' => 'image/jpeg',
			'post_parent' => $post->ID,
			'post_title' => 'First Attachment',
		] );
		// Wait a second so we have sufficiently different created at timestamps.
		sleep( 1 );
		$second = $this->factory()->attachment->create_object( [
			'file' => 'second.jpg',
			'post_mime_type' => 'image/jpeg',
			'post_parent' => $post->ID,
			'post_title' => 'Second Attachment',
		] );

		add_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $post;

		$rendered = sprintf(
			'<div class="hestia-attachment hestia-wrap post-%1$s">
		<a href="%2$s">
			%3$s		</a>
	</div>
	<div class="hestia-attachment hestia-wrap post-%4$s">
		<a href="%5$s">
			%6$s		</a>
	</div>',
			$second,
			get_permalink( $second ),
			get_the_title( $second ),
			$first,
			get_permalink( $first ),
			get_the_title( $first )
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[attachments order="DESC"]' ) )
		);

		remove_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function link_to_file_with_custom_max_in_descending_order() {
		$post = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );

		$first = $this->factory()->attachment->create_object( [
			'file' => 'first.jpg',
			'post_mime_type' => 'image/jpeg',
			'post_parent' => $post->ID,
			'post_title' => 'First Attachment',
		] );
		// Wait a second so we have sufficiently different created at timestamps.
		sleep( 1 );
		$second = $this->factory()->attachment->create_object( [
			'file' => 'second.jpg',
			'post_mime_type' => 'image/jpeg',
			'post_parent' => $post->ID,
			'post_title' => 'Second Attachment',
		] );

		add_filter( 'hestia_attachments_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $post;

		$rendered = sprintf(
			'<div class="hestia-attachment hestia-wrap post-%1$s">
		<a href="%2$s">
			%3$s		</a>
	</div>',
			$second,
			wp_get_attachment_url( $second ),
			get_the_title( $second )
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
