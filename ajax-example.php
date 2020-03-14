<?php
/**
 * Plugin Name:     Ajax Example
 * Plugin URI:      https://github.com/machouinard/wp-ajax-example
 * Description:     AJAX demo in WordPress
 * Author:          Mark Chouinard
 * Author URI:      https://chouinard.me
 * Version:         1.0.0
 *
 * @package         Ajax_Example
 */

defined( 'ABSPATH' ) || die( 'no direct access' );

class AjaxExample {

	public function __construct() {

		$this->hooks();
	}

	/**
	 * Add any actions or filters we need
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function hooks() {

		// Enqueue our JS
		add_action( 'wp_enqueue_scripts', [ $this, 'public_enqueue' ] );
		// Hook our method to AJAX for logged in users
		add_action( 'wp_ajax_get_meta_time', [ $this, 'get_meta_time' ] );
		// Hook our method to AJAX for not-logged-in users
		add_action( 'wp_ajax_nopriv_get_meta_time', [ $this, 'get_meta_time' ] );

	}

	public function public_enqueue() {

		global $post;
		// Only enqueue if we're on a page or post with slug of 'ajax-example'
		if ( ! isset( $post ) || ! is_singular( [
				'page',
				'post',
			] ) || 'ajax-example' !== $post->post_name ) {
			return;
		}
		// Make sure we delete any existing post meta
		delete_post_meta( $post->ID, 'ajax_time' );
		// Use file modification time as version for cache busting
		$js_ver = filemtime( plugin_dir_path( __FILE__ ) . 'js/ajax-example.js' );
		// Enqueue our script with jQuery as a dependency
		wp_enqueue_script( 'ae-js', plugin_dir_url( __FILE__ ) . 'js/ajax-example.js', [ 'jquery' ], $js_ver, true );
		$opts = [
			'postID'   => (int) $post->ID,
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'security' => wp_create_nonce( 'secret-string' ),
		];
		// Localize our script with the post ID, ajaxurl and nonce
		wp_localize_script( 'ae-js', 'opts', $opts );
	}

	/**
	 * AJAX method to retrieve, update, and return post meta
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function get_meta_time() {

		// Check nonce
		if ( ! check_ajax_referer( 'secret-string', 'security', false ) ) {
			// If nonce is bad, send error
			wp_send_json_error( [ 'error' => 'Security token no bueno' ] );
		}
		// If no post ID, send error
		if ( ! isset( $_REQUEST['post_id'] ) ) {
			wp_send_json_error( [ 'error' => 'No Post ID, no bueno' ] );
		}
		// Get post ID sent from AJAX
		$post_id = (int) $_REQUEST['post_id'];

		$time = get_post_meta( $post_id, 'ajax_time', true );
		// Update post meta for future use (multiple button clicks)
		update_post_meta( $post_id, 'ajax_time', current_time( 'G:i:s F d, Y' ) );
		// If we have post meta send success, else send error
		if ( '' !== $time ) {
			wp_send_json_success( [ 'time' => $time ] );
		} else {
			wp_send_json_error( [ 'error' => 'We just updated post meta. Try again.' ] );
		}
	}

}

new AjaxExample();
