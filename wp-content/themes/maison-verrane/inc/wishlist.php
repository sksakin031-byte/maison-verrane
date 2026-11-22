<?php
/**
 * Wishlist: logged-in users are stored in user meta; guests use localStorage (see main.js).
 */
defined( 'ABSPATH' ) || exit;

function mv_get_wishlist( $user_id = 0 ) {
	$user_id = $user_id ? $user_id : get_current_user_id();
	if ( ! $user_id ) {
		return array();
	}
	$ids = get_user_meta( $user_id, 'mv_wishlist', true );
	return is_array( $ids ) ? array_values( array_map( 'absint', $ids ) ) : array();
}

function mv_clean_ids( $raw ) {
	$raw = is_array( $raw ) ? $raw : explode( ',', (string) $raw );
	$ids = array();
	foreach ( array_slice( $raw, 0, 100 ) as $id ) {
		$id = absint( $id );
		if ( $id && 'product' === get_post_type( $id ) && 'publish' === get_post_status( $id ) ) {
			$ids[] = $id;
		}
	}
	return array_values( array_unique( $ids ) );
}

function mv_verify_ajax() {
	if ( ! check_ajax_referer( 'mv_nonce', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => 'Invalid request.' ), 403 );
	}
}

/** Toggle one product (logged-in users). */
function mv_ajax_wishlist_toggle() {
	mv_verify_ajax();
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => 'Login required.' ), 401 );
	}
	$id   = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;
	$ids  = mv_get_wishlist();
	if ( $id && 'product' === get_post_type( $id ) ) {
		$ids = in_array( $id, $ids, true ) ? array_values( array_diff( $ids, array( $id ) ) ) : array_merge( $ids, array( $id ) );
		update_user_meta( get_current_user_id(), 'mv_wishlist', $ids );
	}
	wp_send_json_success( array( 'ids' => $ids ) );
}
add_action( 'wp_ajax_mv_wishlist_toggle', 'mv_ajax_wishlist_toggle' );

/** Merge a guest list into the account after login. */
function mv_ajax_wishlist_sync() {
	mv_verify_ajax();
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => 'Login required.' ), 401 );
	}
	$incoming = isset( $_POST['ids'] ) ? mv_clean_ids( wp_unslash( $_POST['ids'] ) ) : array();
	$ids      = array_values( array_unique( array_merge( mv_get_wishlist(), $incoming ) ) );
	update_user_meta( get_current_user_id(), 'mv_wishlist', $ids );
	wp_send_json_success( array( 'ids' => $ids ) );
}
add_action( 'wp_ajax_mv_wishlist_sync', 'mv_ajax_wishlist_sync' );

/** Return rendered product cards for the wishlist page. */
function mv_ajax_wishlist_cards() {
	mv_verify_ajax();
	$ids = isset( $_POST['ids'] ) ? mv_clean_ids( wp_unslash( $_POST['ids'] ) ) : array();
	if ( ! $ids ) {
		wp_send_json_success( array( 'html' => '' ) );
	}
	ob_start();
	mv_product_grid( array( 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => 48 ) );
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_mv_wishlist_cards', 'mv_ajax_wishlist_cards' );
add_action( 'wp_ajax_nopriv_mv_wishlist_cards', 'mv_ajax_wishlist_cards' );
