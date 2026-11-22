<?php
defined( 'ABSPATH' ) || exit;

function mv_setup() {
	load_theme_textdomain( 'maison-verrane', MV_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 60, 'width' => 240, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width'         => 600,
		'single_image_width'            => 1000,
		'gallery_thumbnail_image_width' => 160,
		'product_grid'                  => array(
			'default_columns' => 4,
			'min_columns'     => 2,
			'max_columns'     => 4,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary' => __( 'Primary menu', 'maison-verrane' ),
		'footer'  => __( 'Footer menu', 'maison-verrane' ),
	) );
}
add_action( 'after_setup_theme', 'mv_setup' );

function mv_assets() {
	wp_enqueue_style(
		'mv-fonts',
		'https://fonts.googleapis.com/css2?family=Bodoni+Moda:opsz,wght@6..96,400;6..96,500;6..96,600&family=Manrope:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'mv-main', MV_URI . '/assets/css/main.css', array(), MV_VERSION );
	wp_enqueue_script( 'mv-main', MV_URI . '/assets/js/main.js', array(), MV_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script( 'mv-main', 'MV', array(
		'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
		'nonce'     => wp_create_nonce( 'mv_nonce' ),
		'loggedIn'  => is_user_logged_in(),
		'wishlist'  => mv_get_wishlist(),
		'i18n'      => array(
			'add'      => __( 'Add to wishlist', 'maison-verrane' ),
			'remove'   => __( 'Remove from wishlist', 'maison-verrane' ),
			'empty'    => __( 'Your wishlist is empty.', 'maison-verrane' ),
			'error'    => __( 'Something went wrong. Please try again.', 'maison-verrane' ),
			'pause'    => __( 'Pause slideshow', 'maison-verrane' ),
			'play'     => __( 'Play slideshow', 'maison-verrane' ),
		),
	) );
}
add_action( 'wp_enqueue_scripts', 'mv_assets' );

/** Preconnect to Google Fonts. */
function mv_resource_hints( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'mv_resource_hints', 10, 2 );

/** Body classes. */
function mv_body_class( $classes ) {
	$classes[] = 'mv-theme';
	return $classes;
}
add_filter( 'body_class', 'mv_body_class' );

/** Fallback menu when no menu is assigned. */
function mv_fallback_menu() {
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	echo '<ul class="mv-menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'maison-verrane' ) . '</a></li>';
	echo '<li><a href="' . esc_url( $shop ) . '">' . esc_html__( 'Shop', 'maison-verrane' ) . '</a></li>';
	echo '</ul>';
}
