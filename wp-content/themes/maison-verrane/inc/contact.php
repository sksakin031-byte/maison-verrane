<?php
/**
 * Contact form handler (admin-post). Nonce, honeypot, rate limit, sanitisation.
 */
defined( 'ABSPATH' ) || exit;

function mv_handle_contact() {
	$back = wp_get_referer() ? remove_query_arg( array( 'sent', 'error' ), wp_get_referer() ) : home_url( '/' );

	if ( ! isset( $_POST['mv_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mv_contact_nonce'] ) ), 'mv_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'error', 'nonce', $back ) );
		exit;
	}
	// Honeypot: bots fill hidden field.
	if ( ! empty( $_POST['mv_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'sent', '1', $back ) );
		exit;
	}
	$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x';
	$lock = 'mv_contact_' . md5( $ip );
	if ( get_transient( $lock ) ) {
		wp_safe_redirect( add_query_arg( 'error', 'wait', $back ) );
		exit;
	}

	$name    = isset( $_POST['mv_name'] ) ? sanitize_text_field( wp_unslash( $_POST['mv_name'] ) ) : '';
	$email   = isset( $_POST['mv_email'] ) ? sanitize_email( wp_unslash( $_POST['mv_email'] ) ) : '';
	$subject = isset( $_POST['mv_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['mv_subject'] ) ) : '';
	$message = isset( $_POST['mv_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mv_message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || strlen( $message ) < 10 ) {
		wp_safe_redirect( add_query_arg( 'error', 'invalid', $back ) );
		exit;
	}

	$to      = is_email( mv_opt( 'email' ) ) ? mv_opt( 'email' ) : get_option( 'admin_email' );
	$title   = '[' . get_bloginfo( 'name' ) . '] ' . ( $subject ? $subject : __( 'New enquiry', 'maison-verrane' ) );
	$body    = "Name: {$name}\nEmail: {$email}\n\n{$message}";
	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	$ok = wp_mail( $to, $title, $body, $headers );
	set_transient( $lock, 1, 30 );
	wp_safe_redirect( add_query_arg( $ok ? 'sent' : 'error', $ok ? '1' : 'mail', $back ) );
	exit;
}
add_action( 'admin_post_nopriv_mv_contact', 'mv_handle_contact' );
add_action( 'admin_post_mv_contact', 'mv_handle_contact' );
