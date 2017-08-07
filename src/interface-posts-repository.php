<?php
/**
 * Posts_Repository interface.
 *
 * @package hestia
 */

namespace SSNepenthe\Hestia;

/**
 * Defines the posts repository interface.
 */
interface Posts_Repository {
	/**
	 * Get all ancestors of a given post.
	 *
	 * @param  integer $post_id Post ID.
	 * @param  string  $order   One of "ASC" or "DESC".
	 * @param  boolean $meta    Whether post meta should be preloaded into the cache.
	 *
	 * @return \WP_Post[]
	 */
	public function get_ancestors( $post_id, $order, $meta );
}
