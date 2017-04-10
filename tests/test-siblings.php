<?php

class Siblings_Test extends WP_UnitTestCase {
	protected $hestia_attachments = [];
	protected $hestia_posts = [];

	function setUp() {
		parent::setUp();

		$this->hestia_posts['first'] = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );
		$this->hestia_posts['second'] = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );
		$this->hestia_posts['third'] = $this->factory()->post->create_and_get( [
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
		add_filter( 'hestia_siblings_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $this->hestia_posts['first'];

		$rendered = sprintf(
			'<div class="hestia-sibling hestia-wrap post-%s">
		<a href="%s">
						%s		</a>
	</div>
	<div class="hestia-sibling hestia-wrap post-%s">
		<a href="%s">
						%s		</a>
	</div>',
			$this->hestia_posts['second']->ID,
			get_permalink( $this->hestia_posts['second']->ID ),
			$this->hestia_posts['second']->post_title,
			$this->hestia_posts['third']->ID,
			get_permalink( $this->hestia_posts['third']->ID ),
			$this->hestia_posts['third']->post_title
		);

		$this->assertEquals( $rendered, trim( do_shortcode( '[siblings]' ) ) );

		remove_filter( 'hestia_siblings_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function override_max() {
		add_filter( 'hestia_siblings_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $this->hestia_posts['first'];

		$rendered = sprintf(
			'<div class="hestia-sibling hestia-wrap post-%s">
		<a href="%s">
						%s		</a>
	</div>',
			$this->hestia_posts['second']->ID,
			get_permalink( $this->hestia_posts['second']->ID ),
			$this->hestia_posts['second']->post_title
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[siblings max="1"]' ) )
		);

		remove_filter( 'hestia_siblings_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function descending_order() {
		// Put these posts as children so we aren't fighting with top level posts.
		$first = $this->factory()->post->create_and_get( [
			'post_parent' => $this->hestia_posts['first']->ID,
			'post_type' => 'page',
		] );
		$second = $this->factory()->post->create_and_get( [
			'post_parent' => $this->hestia_posts['first']->ID,
			'post_type' => 'page',
		] );
		sleep( 1 );
		$third = $this->factory()->post->create_and_get( [
			'post_parent' => $this->hestia_posts['first']->ID,
			'post_type' => 'page',
		] );

		add_filter( 'hestia_siblings_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $first;

		$rendered = sprintf(
			'<div class="hestia-sibling hestia-wrap post-%s">
		<a href="%s">
						%s		</a>
	</div>
	<div class="hestia-sibling hestia-wrap post-%s">
		<a href="%s">
						%s		</a>
	</div>',
			$third->ID,
			get_permalink( $third->ID ),
			$third->post_title,
			$second->ID,
			get_permalink( $second->ID ),
			$second->post_title
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[siblings order="DESC"]' ) )
		);

		remove_filter( 'hestia_siblings_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function with_thumbnails() {
		add_filter( 'hestia_siblings_cache_lifetime', '__return_zero' );

		set_post_thumbnail(
			$this->hestia_posts['second'],
			$this->hestia_attachments['first']
		);

		$GLOBALS['post'] = $this->hestia_posts['first'];

		$rendered = sprintf(
			'<div class="has-post-thumbnail hestia-sibling hestia-wrap post-%s">
		<a href="%s">
			%s			%s		</a>
	</div>
	<div class="hestia-sibling hestia-wrap post-%s">
		<a href="%s">
						%s		</a>
	</div>',
			$this->hestia_posts['second']->ID,
			get_permalink( $this->hestia_posts['second']->ID ),
			get_the_post_thumbnail( $this->hestia_posts['second']->ID ),
			$this->hestia_posts['second']->post_title,
			$this->hestia_posts['third']->ID,
			get_permalink( $this->hestia_posts['third']->ID ),
			$this->hestia_posts['third']->post_title
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[siblings thumbnails="true"]' ) )
		);

		remove_filter( 'hestia_siblings_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function custom_max_in_descending_order_with_thumbnails() {
		add_filter( 'hestia_siblings_cache_lifetime', '__return_zero' );

		set_post_thumbnail(
			$this->hestia_posts['third'],
			$this->hestia_attachments['first']
		);

		$GLOBALS['post'] = $this->hestia_posts['first'];

		$rendered = sprintf(
			'<div class="has-post-thumbnail hestia-sibling hestia-wrap post-%s">
		<a href="%s">
			%s			%s		</a>
	</div>',
			$this->hestia_posts['third']->ID,
			get_permalink( $this->hestia_posts['third']->ID ),
			get_the_post_thumbnail( $this->hestia_posts['third']->ID ),
			$this->hestia_posts['third']->post_title
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[siblings max="1" order="DESC" thumbnails="true"]' ) )
		);

		remove_filter( 'hestia_siblings_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function it_fails_gracefully() {
		$post = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
			// There will be three posts at root level so lets down a level.
			'post_parent' => $this->hestia_posts['first']->ID,
		] );

		add_filter( 'hestia_siblings_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $post;

		$this->assertEquals( '', trim( do_shortcode( '[siblings]' ) ) );

		remove_filter( 'hestia_siblings_cache_lifetime', '__return_zero' );
	}
}
