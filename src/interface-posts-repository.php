<?php
/**
 * Posts_Repository interface.
 *
 * @package hestia
 */

namespace Hestia;

if ( ! \defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the posts repository interface.
 */
interface Posts_Repository {
	/**
	 * Get all ancestors of a given post.
	 *
	 * @param integer $post_id Post ID.
	 * @param string  $order   One of "ASC" or "DESC".
	 * @param boolean $meta    Whether post meta should be preloaded into the cache.
	 *
	 * @return \WP_Post[]
	 */
	public function get_ancestors( $post_id, $order, $meta );

	/**
	 * Get attachments that were uploaded to a given post.
	 *
	 * @param integer $post_id Post ID.
	 * @param integer $qty     Number of posts to retrieve.
	 * @param string  $order   Post order - one of "ASC" or "DESC".
	 * @param boolean $meta    Whether post meta should be preloaded into the cache.
	 *
	 * @return \WP_Post[]
	 */
	public function get_attachments( $post_id, $qty, $order, $meta );

	/**
	 * Get all child posts of a given post.
	 *
	 * @param integer $post_id Post ID.
	 * @param integer $qty     Number of posts to retrieve.
	 * @param string  $order   Post order - one of "ASC" or "DESC".
	 * @param boolean $meta    Whether post meta should be preloaded into the cache.
	 *
	 * @return \WP_Post[]
	 */
	public function get_children( $post_id, $qty, $order, $meta );

	/**
	 * Get all posts by post type.
	 *
	 * @param string  $post_type Post type.
	 * @param integer $qty       Number of posts to retrieve.
	 * @param string  $order     Post order - one of "ASC" or "DESC".
	 * @param boolean $meta      Whether post meta should be preloaded into the cache.
	 *
	 * @return \WP_Post[]
	 */
	public function get_posts_by_type( $post_type, $qty, $order, $meta );

	/**
	 * Get all siblings of a given post.
	 *
	 * @param integer $post_id Post ID.
	 * @param integer $qty     Number of posts to retrieve.
	 * @param string  $order   Post order - one of "ASC" or "DESC".
	 * @param boolean $meta    Whether post meta should be preloaded into the cache.
	 *
	 * @return \WP_Post[]
	 */
	public function get_siblings( $post_id, $qty, $order, $meta );
}
