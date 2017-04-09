<?php

class Ancestors_Test extends WP_UnitTestCase {
	/** @test */
	function basic_output() {
		$first = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );
		$second = $this->factory()->post->create_and_get( [
			'post_parent' => $first->ID,
			'post_type' => 'page',
		] );
		$third = $this->factory()->post->create_and_get( [
			'post_parent' => $second->ID,
			'post_type' => 'page',
		] );

		add_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $third;

		$rendered = sprintf( '<div class="hestia-ancestor hestia-wrap post-%1$s">
		<a href="http://example.org/?page_id=%1$s">
						%2$s		</a>
	</div>
	<div class="hestia-ancestor hestia-wrap post-%3$s">
		<a href="http://example.org/?page_id=%3$s">
						%4$s		</a>
	</div>', $first->ID, $first->post_title, $second->ID, $second->post_title );

		$this->assertEquals( $rendered, trim( do_shortcode( '[ancestors]' ) ) );

		remove_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function descending_order() {
		$first = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );
		$second = $this->factory()->post->create_and_get( [
			'post_parent' => $first->ID,
			'post_type' => 'page',
		] );
		$third = $this->factory()->post->create_and_get( [
			'post_parent' => $second->ID,
			'post_type' => 'page',
		] );

		add_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $third;

		$rendered = sprintf( '<div class="hestia-ancestor hestia-wrap post-%1$s">
		<a href="http://example.org/?page_id=%1$s">
						%2$s		</a>
	</div>
	<div class="hestia-ancestor hestia-wrap post-%3$s">
		<a href="http://example.org/?page_id=%3$s">
						%4$s		</a>
	</div>', $second->ID, $second->post_title, $first->ID, $first->post_title );

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[ancestors order="DESC"]' ) )
		);

		remove_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function with_thumbnails() {
		$first = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );
		$second = $this->factory()->post->create_and_get( [
			'post_parent' => $first->ID,
			'post_type' => 'page',
		] );
		$third = $this->factory()->post->create_and_get( [
			'post_parent' => $second->ID,
			'post_type' => 'page',
		] );

		$attachment_id = $this->factory()->attachment->create_object( [
			'file' => 'image.jpg',
			'post_parent' => $second->ID,
			'post_type' => 'attachment',
			'post_mime_type' => 'image/jpeg',
		] );

		set_post_thumbnail( $second, $attachment_id );

		add_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $third;

		$image_string = get_the_post_thumbnail( $second->ID );
		$rendered = sprintf(
			'<div class="hestia-ancestor hestia-wrap post-%1$s">
		<a href="http://example.org/?page_id=%1$s">
						%2$s		</a>
	</div>
	<div class="has-post-thumbnail hestia-ancestor hestia-wrap post-%3$s">
		<a href="http://example.org/?page_id=%3$s">
			%4$s			%5$s		</a>
	</div>',
			$first->ID,
			$first->post_title,
			$second->ID,
			$image_string,
			$second->post_title
		);

		$this->assertEquals(
			$rendered,
			trim( do_shortcode( '[ancestors thumbnails="true"]' ) )
		);

		remove_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );
	}

	/** @test */
	function descending_with_thumbnails() {
		$first = $this->factory()->post->create_and_get( [
			'post_type' => 'page',
		] );
		$second = $this->factory()->post->create_and_get( [
			'post_parent' => $first->ID,
			'post_type' => 'page',
		] );
		$third = $this->factory()->post->create_and_get( [
			'post_parent' => $second->ID,
			'post_type' => 'page',
		] );

		$attachment_id = $this->factory()->attachment->create_object( [
			'file' => 'image.jpg',
			'post_parent' => $second->ID,
			'post_type' => 'attachment',
			'post_mime_type' => 'image/jpeg',
		] );

		set_post_thumbnail( $second, $attachment_id );

		add_filter( 'hestia_ancestors_cache_lifetime', '__return_zero' );

		$GLOBALS['post'] = $third;

		$image_string = get_the_post_thumbnail( $second->ID );
		$rendered = sprintf(
			'<div class="has-post-thumbnail hestia-ancestor hestia-wrap post-%1$s">
		<a href="http://example.org/?page_id=%1$s">
			%2$s			%3$s		</a>
	</div>
	<div class="hestia-ancestor hestia-wrap post-%4$s">
		<a href="http://example.org/?page_id=%4$s">
						%5$s		</a>
	</div>',
			$second->ID,
			$image_string,
			$second->post_title,
			$first->ID,
			$first->post_title
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
