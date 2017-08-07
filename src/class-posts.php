<?php
/**
 * Posts class.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia;

/**
 * Defines the posts class.
 */
class Posts implements Posts_Repository {
	/**
	 * Get all ancestors of a post.
	 *
	 * @param  integer $post_id The post ID.
	 * @param  string  $order   One of "ASC" or "DESC".
	 * @param  boolean $meta    Whether post meta should be preloaded into cache.
	 *
	 * @return \WP_Post[]
	 */
	public function get_ancestors( $post_id, $order, $meta ) {
		$post = get_post( $post_id );

		if ( ! $post || ! is_post_type_hierarchical( $post->post_type ) ) {
			return [];
		}

		$ancestor_ids = $post->ancestors;

		if ( empty( $ancestor_ids ) ) {
			return [];
		}

		if ( 'ASC' === $order ) {
			$ancestor_ids = array_reverse( $ancestor_ids );
		}

		$needs_update = [];

		if ( $meta ) {
			foreach ( $ancestor_ids as $ancestor_id ) {
				if ( false === wp_cache_get( $ancestor_id, 'post_meta' ) ) {
					$needs_update[] = $ancestor_id;
				}
			}
		}

		if ( ! empty( $needs_update ) ) {
			// If our templates need post meta (like for thumbnails) and it has not already been
			// loaded in to the cache, we will do so to avoid a likely n+1 situation.
			update_meta_cache( 'post', $needs_update );
		}

		$ancestors = array_map( 'get_post', $ancestor_ids );

		// The get_post() function might (shouldn't) return null so let's filter before returning.
		return array_filter( $ancestors );
	}

	/**
	 * Get attachments that were uploaded to a given post.
	 *
	 * @param  integer $post_id Post ID.
	 * @param  integer $qty     Number of posts to retrieve.
	 * @param  string  $order   Post order - one of "ASC" or "DESC".
	 * @param  boolean $meta    Whether post meta should be preloaded into the cache.
	 *
	 * @return \WP_Post[]
	 */
	public function get_attachments( $post_id, $qty, $order, $meta ) {
		$args = [
			'ignore_sticky_posts' => true,
			'no_found_rows' => true,
			'order' => $order,
			'post_parent' => $post_id,
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'posts_per_page' => $qty,
			'update_post_term_cache' => false,
		];

		if ( ! $meta ) {
			$args['update_post_meta_cache'] = false;
		}

		return $this->query( $args );
	}

	/**
	 * Get all child posts of a given post.
	 *
	 * @param  integer $post_id Post ID.
	 * @param  integer $qty     Number of posts to retrieve.
	 * @param  string  $order   Post order - one of "ASC" or "DESC".
	 * @param  boolean $meta    Whether post meta should be preloaded into the cache.
	 *
	 * @return \WP_Post[]
	 */
	public function get_children( $post_id, $qty, $order, $meta ) {
		$post_type = get_post_type( $post_id );

		if ( ! $post_type || ! is_post_type_hierarchical( $post_type ) ) {
			return [];
		}

		$args = [
			'ignore_sticky_posts' => true,
			'no_found_rows' => true,
			'order' => $order,
			'post_parent' => $post_id,
			'post_type' => $post_type,
			'posts_per_page' => $qty,
			'update_post_term_cache' => false,
		];

		if ( ! $meta ) {
			$args['update_post_meta_cache'] = false;
		}

		return $this->query( $args );
	}

	/**
	 * Get the array of found posts from a WP_Query instance.
	 *
	 * @param  array $args WP_Query args.
	 *
	 * @return \WP_Post[]
	 */
	protected function query( $args ) {
		return ( new \WP_Query( $args ) )->posts;
	}
}
