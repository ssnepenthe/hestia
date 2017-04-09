<?php

class Ancestors_Test extends WP_UnitTestCase {
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
		$this->hestia_attachments = [];
		$this->hestia_posts = [];
	}

	/** @test */
	function basic_output() {
		add_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $this->hestia_posts['third'];

		$rendered = sprintf(
			'<div class="hestia-ancestor hestia-wrap post-%1$s">
		<a href="http://example.org/?page_id=%1$s">
						%2$s		</a>
	</div>
	<div class="hestia-ancestor hestia-wrap post-%3$s">
		<a href="http://example.org/?page_id=%3$s">
						%4$s		</a>
	</div>',
			$this->hestia_posts['first']->ID,
			$this->hestia_posts['first']->post_title,
			$this->hestia_posts['second']->ID,
			$this->hestia_posts['second']->post_title
		);

		$this->assertEquals( $rendered, trim( do_shortcode( '[ancestors]' ) ) );

		remove_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function descending_order() {
		add_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $this->hestia_posts['third'];

		$rendered = sprintf(
			'<div class="hestia-ancestor hestia-wrap post-%1$s">
		<a href="http://example.org/?page_id=%1$s">
						%2$s		</a>
	</div>
	<div class="hestia-ancestor hestia-wrap post-%3$s">
		<a href="http://example.org/?page_id=%3$s">
						%4$s		</a>
	</div>',
			$this->hestia_posts['second']->ID,
			$this->hestia_posts['second']->post_title,
			$this->hestia_posts['first']->ID,
			$this->hestia_posts['first']->post_title
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[ancestors order="DESC"]' ) )
		);

		remove_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function with_thumbnails() {
		add_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );

		set_post_thumbnail(
			$this->hestia_posts['second'],
			$this->hestia_attachments['first']
		);

		$GLOBALS['post'] = $this->hestia_posts['third'];

		$rendered = sprintf(
			'<div class="hestia-ancestor hestia-wrap post-%1$s">
		<a href="http://example.org/?page_id=%1$s">
						%2$s		</a>
	</div>
	<div class="has-post-thumbnail hestia-ancestor hestia-wrap post-%3$s">
		<a href="http://example.org/?page_id=%3$s">
			%4$s			%5$s		</a>
	</div>',
			$this->hestia_posts['first']->ID,
			$this->hestia_posts['first']->post_title,
			$this->hestia_posts['second']->ID,
			get_the_post_thumbnail( $this->hestia_posts['second']->ID ),
			$this->hestia_posts['second']->post_title
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[ancestors thumbnails="true"]' ) )
		);

		remove_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function descending_with_thumbnails() {
		add_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );

		set_post_thumbnail(
			$this->hestia_posts['second'],
			$this->hestia_attachments['first']
		);

		$GLOBALS['post'] = $this->hestia_posts['third'];

		$rendered = sprintf(
			'<div class="has-post-thumbnail hestia-ancestor hestia-wrap post-%1$s">
		<a href="http://example.org/?page_id=%1$s">
			%2$s			%3$s		</a>
	</div>
	<div class="hestia-ancestor hestia-wrap post-%4$s">
		<a href="http://example.org/?page_id=%4$s">
						%5$s		</a>
	</div>',
			$this->hestia_posts['second']->ID,
			get_the_post_thumbnail( $this->hestia_posts['second']->ID ),
			$this->hestia_posts['second']->post_title,
			$this->hestia_posts['first']->ID,
			$this->hestia_posts['first']->post_title
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[ancestors order="DESC" thumbnails="true"]' ) )
		);

		remove_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function it_fails_gracefully() {
		$first = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );

		add_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $first;

		$this->assertEquals( '', trim( do_shortcode( '[ancestors]' ) ) );

		remove_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );
	}
}
