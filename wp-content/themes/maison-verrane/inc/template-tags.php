<?php
defined( 'ABSPATH' ) || exit;

/** Inline SVG icon (static markup, no user input). */
function mv_icon( $name, $size = 22 ) {
	$paths = array(
		'search' => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>',
		'user'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/>',
		'heart'  => '<path d="M12 20.5s-8-4.9-8-11A4.5 4.5 0 0 1 12 7a4.5 4.5 0 0 1 8 2.5c0 6.1-8 11-8 11z"/>',
		'bag'    => '<path d="M5 8h14l-1 12H6L5 8z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'menu'   => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'  => '<path d="M6 6l12 12M18 6L6 18"/>',
		'prev'   => '<path d="M15 5l-7 7 7 7"/>',
		'next'   => '<path d="M9 5l7 7-7 7"/>',
		'pause'  => '<path d="M9 5v14M15 5v14"/>',
		'play'   => '<path d="M8 5l11 7-11 7z"/>',
		'filter' => '<path d="M4 6h16M7 12h10M10 18h4"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	$size = (int) $size;
	return '<svg class="mv-ico mv-ico--' . esc_attr( $name ) . '" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/** Wishlist toggle button. State is applied by assets/js/main.js. */
function mv_wishlist_button( $product_id, $with_label = false ) {
	printf(
		'<button type="button" class="mv-wish%1$s" data-product="%2$d" aria-pressed="false" aria-label="%3$s">%4$s%5$s</button>',
		$with_label ? ' mv-wish--label' : '',
		(int) $product_id,
		esc_attr__( 'Add to wishlist', 'maison-verrane' ),
		mv_icon( 'heart', 20 ), // phpcs:ignore WordPress.Security.EscapeOutput
		$with_label ? '<span class="mv-wish__text">' . esc_html__( 'Add to wishlist', 'maison-verrane' ) . '</span>' : '' // phpcs:ignore WordPress.Security.EscapeOutput
	);
}

/** Render a product grid from WP_Query args using the theme's product card. */
function mv_product_grid( $args = array() ) {
	if ( ! function_exists( 'wc_get_template_part' ) ) {
		return;
	}
	$q = new WP_Query( wp_parse_args( $args, array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => 4,
		'ignore_sticky_posts' => true,
	) ) );
	if ( ! $q->have_posts() ) {
		return;
	}
	woocommerce_product_loop_start();
	while ( $q->have_posts() ) {
		$q->the_post();
		wc_get_template_part( 'content', 'product' );
	}
	woocommerce_product_loop_end();
	wp_reset_postdata();
}

/** Site logo or text wordmark. */
function mv_brand() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	echo '<a class="mv-wordmark" href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
}

/** Shop URL helper. */
function mv_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

/** Account URL helper. */
function mv_account_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
}

/** Cart count (frontend only). */
function mv_cart_count() {
	return ( function_exists( 'WC' ) && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;
}
