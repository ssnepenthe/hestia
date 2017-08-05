<?php

use SSNepenthe\Hestia\Cache\Transient_Cache;

class Transient_Store_Test extends WP_UnitTestCase {
	protected $prefix;
	protected $cache;
	protected $prefixed_cache;

	function setUp() {
		parent::setUp();

		global $wpdb;

		$this->prefix = 'pfx';
		$this->cache = new Transient_Cache( $wpdb );
		$this->prefixed_cache = new Transient_Cache( $wpdb, $this->prefix );
	}

	function tearDown() {
		parent::tearDown();

		$this->prefix = null;
		$this->cache = null;
		$this->prefixed_cache = null;
	}

	/** @test */
	function it_can_get_an_entry() {
		// It returns null when an entry is not set.
		$this->assertNull( $this->cache->get( 'test' ) );
		$this->assertNull( $this->prefixed_cache->get( 'test' ) );

		set_transient( 'test', 'non-prefixed' );
		set_transient( 'pfx:test', 'prefixed' );

		$this->assertEquals( 'non-prefixed', $this->cache->get( 'test' ) );
		$this->assertEquals( 'prefixed', $this->prefixed_cache->get( 'test' ) );
	}

	/** @test */
	function it_throws_when_attempting_to_get_non_string_keys() {
		$this->expectException( InvalidArgumentException::class );

		$this->cache->get( 5 );
	}

	/** @test */
	function it_throws_when_attempting_to_get_empty_key() {
		$this->expectException( InvalidArgumentException::class );

		$this->cache->get( '' );
	}

	/** @test */
	function it_throws_when_attempting_to_get_with_reserved_characters() {
		$this->expectException( InvalidArgumentException::class );

		$this->cache->get( 'test{' );
	}

	/** @test */
	function it_can_remember_an_entry() {
		set_transient( 'test-1', 'pre-existing', 0 );
		set_transient( 'pfx:test-1', 'prefixed-pre-existing', 0 );

		// Returns value immediately if already set.
		$this->assertEquals( 'pre-existing', $this->cache->remember( 'test-1', 0, function() {
			return 'new';
		} ) );

		$this->assertEquals(
			'prefixed-pre-existing',
			$this->prefixed_cache->remember( 'test-1', 0, function() {
				return 'new';
			} )
		);

		// Otherwise it calls callback, saves to cache and returns value.
		$this->assertEquals( 'new', $this->cache->remember( 'test-2', 0, function() {
			return 'new';
		} ) );
		$this->assertEquals( 'new', get_transient( 'test-2' ) );

		$this->assertEquals( 'new', $this->prefixed_cache->remember( 'test-2', 0, function() {
			return 'new';
		} ) );
		$this->assertEquals( 'new', get_transient( 'pfx:test-2' ) );
	}

	/** @test */
	function it_can_set_an_entry() {
		// Sanity.
		$this->assertFalse( get_transient( 'no-exp' ) );
		$this->assertFalse( get_transient( 'pfx:no-exp' ) );
		$this->assertFalse( get_transient( 'with-exp' ) );
		$this->assertFalse( get_transient( 'pfx:with-exp' ) );

		// Non-expiring.
		$this->assertTrue( $this->cache->set( 'no-exp', 'no-exp-value' ) );
		$this->assertTrue( $this->prefixed_cache->set( 'no-exp', 'pfx-no-exp-value' ) );

		$this->assertEquals( 'no-exp-value', get_transient( 'no-exp' ) );
		$this->assertEquals( 'pfx-no-exp-value', get_transient( 'pfx:no-exp' ) );

		// Expiring.
		$this->assertTrue( $this->cache->set( 'with-exp', 'with-exp-value', 1 ) );
		$this->assertTrue( $this->prefixed_cache->set( 'with-exp', 'pfx-with-exp-value', 1 ) );

		$this->assertEquals( 'with-exp-value', get_transient( 'with-exp' ) );
		$this->assertEquals( 'pfx-with-exp-value', get_transient( 'pfx:with-exp' ) );

		// Make sure the expiration is working.
		sleep( 2 );

		$this->assertEquals( 'no-exp-value', get_transient( 'no-exp' ) );
		$this->assertEquals( 'pfx-no-exp-value', get_transient( 'pfx:no-exp' ) );

		$this->assertFalse( get_transient( 'with-exp' ) );
		$this->assertFalse( get_transient( 'pfx:with-exp' ) );
	}

	/** @test */
	function it_throws_when_attempting_to_set_with_bad_key() {
		// Not checking all conditions - implied from get tests.
		$this->expectException( InvalidArgumentException::class );

		$this->cache->set( 5, 'value' );
	}

	/** @test */
	function it_hashes_cache_key_if_longer_than_allowed() {
		// 172 is max.
		$allowed_no_prefix = str_repeat( 'a', 172 );
		// 172 - 3 for prefix - 1 for separator.
		$allowed_with_prefix = str_repeat( 'b', 168 );
		$not_allowed_no_prefix = str_repeat( 'c', 173 );
		$not_allowed_with_prefix = str_repeat( 'd', 169 );
		$hashed_no_prefix = hash( 'sha1', $not_allowed_no_prefix );
		$hashed_with_prefix = hash( 'sha1', $not_allowed_with_prefix );

		// Sanity.
		$this->assertFalse( get_transient( $allowed_no_prefix ) );
		$this->assertFalse( get_transient( "pfx:{$allowed_with_prefix}" ) );
		$this->assertFalse( get_transient( $not_allowed_no_prefix ) );
		$this->assertFalse( get_transient( "pfx:{$not_allowed_with_prefix}" ) );
		$this->assertFalse( get_transient( $hashed_no_prefix ) );
		$this->assertFalse( get_transient( "pfx:{$hashed_with_prefix}" ) );

		// Unmodified keys.
		$this->cache->set( $allowed_no_prefix, 'test-a' );
		$this->prefixed_cache->set( $allowed_with_prefix, 'test-b' );

		$this->assertEquals( 'test-a', get_transient( $allowed_no_prefix ) );
		$this->assertEquals( 'test-b', get_transient( "pfx:{$allowed_with_prefix}" ) );

		// Hashed keys.
		$this->cache->set( $not_allowed_no_prefix, 'test-c' );
		$this->prefixed_cache->set( $not_allowed_with_prefix, 'test-d' );

		// Shouldn't be set because the key needs to be hashed.
		$this->assertFalse( get_transient( $not_allowed_no_prefix ) );
		$this->assertFalse( get_transient( "pfx:{$not_allowed_with_prefix}" ) );

		// Should be set.
		$this->assertEquals( 'test-c', get_transient( $hashed_no_prefix ) );
		$this->assertEquals(
			'test-d',
			get_transient( "pfx:{$hashed_with_prefix}" )
		);
	}
}
