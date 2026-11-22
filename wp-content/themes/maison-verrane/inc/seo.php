<?php
/**
 * Lightweight SEO: meta description, Open Graph and Organization schema.
 * Skipped automatically when Yoast SEO / Rank Math / AIOSEO is active.
 */
defined( 'ABSPATH' ) || exit;

function mv_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

function mv_meta_description() {
	$desc = '';
	if ( is_singular() ) {
		$post = get_queried_object();
		$desc = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$desc = term_description();
	} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
		$desc = get_bloginfo( 'description' );
	}
	if ( '' === trim( wp_strip_all_tags( $desc ) ) ) {
		$desc = get_bloginfo( 'description' );
	}
	return wp_html_excerpt( trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $desc ) ) ), 155, '…' );
}

function mv_seo_head() {
	if ( mv_seo_plugin_active() || is_404() ) {
		return;
	}
	$desc  = mv_meta_description();
	$title = wp_get_document_title();
	$url   = home_url( '/' );
	if ( is_singular() ) {
		$url = get_permalink();
	} elseif ( is_tax() ) {
		$link = get_term_link( get_queried_object() );
		$url  = is_wp_error( $link ) ? $url : $link;
	} elseif ( is_post_type_archive( 'product' ) ) {
		$url = get_post_type_archive_link( 'product' );
	}
	$img   = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$img = get_the_post_thumbnail_url( null, 'large' );
	} elseif ( has_custom_logo() ) {
		$img = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'large' );
	} else {
		$img = mv_opt( 'hero_1_img', mv_demo_img( 'hero-1.jpg' ) );
	}
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:type" content="' . ( is_singular( 'product' ) ? 'product' : 'website' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	if ( $img ) {
		echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

	if ( is_front_page() ) {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type'    => 'Organization',
			'name'     => get_bloginfo( 'name' ),
			'url'      => home_url( '/' ),
			'email'    => mv_opt( 'email' ),
			'telephone' => mv_opt( 'phone' ),
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
add_action( 'wp_head', 'mv_seo_head', 5 );

/** Internal search results and cart/checkout/account pages should not be indexed. */
function mv_robots( $robots ) {
	if ( is_search() || ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['max-image-preview'] );
	}
	return $robots;
}
add_filter( 'wp_robots', 'mv_robots' );
