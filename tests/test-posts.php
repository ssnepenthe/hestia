<?php

use Hestia\Posts;

class Posts_Test extends WP_UnitTestCase {
	protected $hestia_repository;
	protected $hestia_posts = [];
	protected $hestia_attachments = [];

	function setUp() {
		parent::setUp();

		$this->hestia_repository = new Posts();

		$this->hestia_posts['one-a'] = $this->factory()->post->create( [
			'post_date' => date( 'Y-m-d H:i:s', time() - 20 ),
			'post_status' => 'publish',
			'post_type' => 'page',
		] );
		$this->hestia_posts['one-b'] = $this->factory()->post->create( [
			'post_date' => date( 'Y-m-d H:i:s', time() - 19 ),
			'post_status' => 'publish',
			'post_type' => 'page',
		] );
		$this->hestia_posts['one-c'] = $this->factory()->post->create( [
			'post_date' => date( 'Y-m-d H:i:s', time() - 18 ),
			'post_status' => 'publish',
			'post_type' => 'page',
		] );

		$this->hestia_posts['two-a'] = $this->factory()->post->create( [
			'post_date' => date( 'Y-m-d H:i:s', time() - 17 ),
			'post_parent' => $this->hestia_posts['one-a'],
			'post_status' => 'publish',
			'post_type' => 'page',
		] );
		$this->hestia_posts['two-b'] = $this->factory()->post->create( [
			'post_date' => date( 'Y-m-d H:i:s', time() - 16 ),
			'post_parent' => $this->hestia_posts['one-a'],
			'post_status' => 'publish',
			'post_type' => 'page',
		] );
		$this->hestia_posts['two-c'] = $this->factory()->post->create( [
			'post_date' => date( 'Y-m-d H:i:s', time() - 15 ),
			'post_parent' => $this->hestia_posts['one-a'],
			'post_status' => 'publish',
			'post_type' => 'page',
		] );

		$this->hestia_posts['three-a'] = $this->factory()->post->create( [
			'post_date' => date( 'Y-m-d H:i:s', time() - 14 ),
			'post_parent' => $this->hestia_posts['two-a'],
			'post_status' => 'publish',
			'post_type' => 'page',
		] );
		$this->hestia_posts['three-b'] = $this->factory()->post->create( [
			'post_date' => date( 'Y-m-d H:i:s', time() - 13 ),
			'post_parent' => $this->hestia_posts['two-a'],
			'post_status' => 'publish',
			'post_type' => 'page',
		] );
		$this->hestia_posts['three-c'] = $this->factory()->post->create( [
			'post_date' => date( 'Y-m-d H:i:s', time() - 12 ),
			'post_parent' => $this->hestia_posts['two-a'],
			'post_status' => 'publish',
			'post_type' => 'page',
		] );

		$this->hestia_posts['not-page'] = $this->factory()->post->create( [
			'post_date' => date( 'Y-m-d H:i:s', time() - 11 ),
		] );

		$this->hestia_attachments['first'] = $this->factory()->attachment->create( [
			'file' => 'first.jpg',
			'post_date' => date( 'Y-m-d H:i:s', time() - 60 ),
			'post_mime_type' => 'image/jpeg',
			'post_parent' => $this->hestia_posts['one-a'],
			'post_title' => 'First Attachment',
		] );
		$this->hestia_attachments['second'] = $this->factory()->attachment->create( [
			'file' => 'second.jpg',
			'post_mime_type' => 'image/jpeg',
			'post_parent' => $this->hestia_posts['one-a'],
			'post_title' => 'Second Attachment',
		] );
		$this->hestia_attachments['third'] = $this->factory()->attachment->create( [
			'file' => 'third.jpg',
			'post_mime_type' => 'image/jpeg',
			'post_parent' => $this->hestia_posts['one-b'],
			'post_title' => 'Third Attachment',
		] );
	}

	function tearDown() {
		parent::tearDown();

		$this->hestia_attachments = [];
		$this->hestia_posts = [];
	}

	/** @test */
	function it_returns_empty_arrays_for_non_existent_posts() {
		$this->assertEquals(
			[],
			$this->hestia_repository->get_ancestors( 999999999, 'ASC', false )
		);
		$this->assertEquals(
			[],
			$this->hestia_repository->get_attachments( 999999999, 100, 'ASC', false )
		);
		$this->assertEquals(
			[],
			$this->hestia_repository->get_children( 999999999, 100, 'ASC', false )
		);
		$this->assertEquals(
			[],
			$this->hestia_repository->get_posts_by_type( 'fake_type', 100, 'ASC', false )
		);
		$this->assertEquals(
			[],
			$this->hestia_repository->get_siblings( 999999999, 100, 'ASC', false )
		);
	}

	/** @test */
	function it_can_get_ancestors_of_a_given_post() {
		$this->assertEquals(
			[
				$this->hestia_posts['one-a'],
				$this->hestia_posts['two-a'],
			],
			wp_list_pluck(
				$this->hestia_repository->get_ancestors(
					$this->hestia_posts['three-a'],
					'ASC',
					false
				),
				'ID'
			)
		);

		$this->assertEquals(
			[
				$this->hestia_posts['two-a'],
				$this->hestia_posts['one-a'],
			],
			wp_list_pluck(
				$this->hestia_repository->get_ancestors(
					$this->hestia_posts['three-a'],
					'DESC',
					false
				),
				'ID'
			)
		);
	}

	/** @test */
	function it_can_get_attachments_of_a_given_post() {
		$this->assertEquals(
			[
				$this->hestia_attachments['first'],
				$this->hestia_attachments['second'],
			],
			wp_list_pluck(
				$this->hestia_repository->get_attachments(
					$this->hestia_posts['one-a'],
					100,
					'ASC',
					false
				),
				'ID'
			)
		);

		$this->assertEquals(
			[ $this->hestia_attachments['first'] ],
			wp_list_pluck(
				$this->hestia_repository->get_attachments(
					$this->hestia_posts['one-a'],
					1,
					'ASC',
					false
				),
				'ID'
			)
		);

		$this->assertEquals(
			[ $this->hestia_attachments['second'] ],
			wp_list_pluck(
				$this->hestia_repository->get_attachments(
					$this->hestia_posts['one-a'],
					1,
					'DESC',
					false
				),
				'ID'
			)
		);

		$this->assertEquals(
			[
				$this->hestia_attachments['third'],
			],
			wp_list_pluck(
				$this->hestia_repository->get_attachments(
					$this->hestia_posts['one-b'],
					100,
					'ASC',
					false
				),
				'ID'
			)
		);
	}

	/** @test */
	function it_can_get_children_of_a_given_post() {
		$this->assertEquals(
			[
				$this->hestia_posts['two-a'],
				$this->hestia_posts['two-b'],
				$this->hestia_posts['two-c'],
			],
			wp_list_pluck(
				$this->hestia_repository->get_children(
					$this->hestia_posts['one-a'],
					100,
					'ASC',
					false
				),
				'ID'
			)
		);

		$this->assertEquals(
			[ $this->hestia_posts['two-a'] ],
			wp_list_pluck(
				$this->hestia_repository->get_children(
					$this->hestia_posts['one-a'],
					1,
					'ASC',
					false
				),
				'ID'
			)
		);

		$this->assertEquals(
			[ $this->hestia_posts['two-c'] ],
			wp_list_pluck(
				$this->hestia_repository->get_children(
					$this->hestia_posts['one-a'],
					1,
					'DESC',
					false
				),
				'ID'
			)
		);
	}

	/** @test */
	function it_can_get_posts_by_type() {
		$posts = $this->hestia_posts;
		unset( $posts['not-page'] );

		$this->assertEquals(
			array_values( $posts ),
			wp_list_pluck(
				$this->hestia_repository->get_posts_by_type( 'page', 100, 'ASC', false ),
				'ID'
			)
		);

		$this->assertEquals(
			[ $this->hestia_posts['one-a'] ],
			wp_list_pluck(
				$this->hestia_repository->get_posts_by_type( 'page', 1, 'ASC', false ),
				'ID'
			)
		);

		$this->assertEquals(
			[ $this->hestia_posts['three-c'] ],
			wp_list_pluck(
				$this->hestia_repository->get_posts_by_type( 'page', 1, 'DESC', false ),
				'ID'
			)
		);

		$this->assertEquals(
			[ $this->hestia_posts['not-page'] ],
			wp_list_pluck(
				$this->hestia_repository->get_posts_by_type( 'post', 100, 'ASC', false ),
				'ID'
			)
		);
	}

	/** @test */
	function it_can_get_the_siblings_of_a_given_post() {
		$this->assertEquals(
			[
				$this->hestia_posts['one-b'],
				$this->hestia_posts['one-c'],
			],
			array_values( wp_list_pluck(
				$this->hestia_repository->get_siblings(
					$this->hestia_posts['one-a'],
					100,
					'ASC',
					false
				),
				'ID'
			) )
		);

		$this->assertEquals(
			[ $this->hestia_posts['two-a'] ],
			wp_list_pluck(
				$this->hestia_repository->get_siblings(
					$this->hestia_posts['two-b'],
					1,
					'ASC',
					false
				),
				'ID'
			)
		);

		$this->assertEquals(
			[ $this->hestia_posts['three-b'] ],
			array_values( wp_list_pluck(
				$this->hestia_repository->get_siblings(
					$this->hestia_posts['three-c'],
					1,
					'DESC',
					false
				),
				'ID'
			) )
		);
	}
}
