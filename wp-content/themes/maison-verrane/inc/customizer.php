<?php
defined( 'ABSPATH' ) || exit;

/** Read a theme mod with a default. */
function mv_opt( $key, $default = '' ) {
	$val = get_theme_mod( 'mv_' . $key, $default );
	return ( '' === $val || null === $val ) ? $default : $val;
}

function mv_demo_img( $file ) {
	return MV_URI . '/assets/demo/' . $file;
}

function mv_customize_register( $wp_customize ) {
	/* ---------- Store text ---------- */
	$wp_customize->add_section( 'mv_store', array( 'title' => __( 'Store details', 'maison-verrane' ), 'priority' => 30 ) );
	$store = array(
		'announcement' => array( __( 'Announcement bar text', 'maison-verrane' ), 'Complimentary shipping on orders over $250' ),
		'email'        => array( __( 'Contact email', 'maison-verrane' ), 'concierge@maisonverrane.example' ),
		'phone'        => array( __( 'Contact phone', 'maison-verrane' ), '+1 (212) 555-0142' ),
		'address'      => array( __( 'Address', 'maison-verrane' ), '28 Mercer Row, New York, NY 10012' ),
		'hours'        => array( __( 'Opening hours', 'maison-verrane' ), 'Mon–Sat, 10:00–18:00' ),
		'footer_text'  => array( __( 'Footer brand statement', 'maison-verrane' ), 'Maison Verrane is a demo luxury house created for presentation. Every product, price and photograph on this site is fictional.' ),
		'instagram'    => array( __( 'Instagram URL', 'maison-verrane' ), '' ),
		'pinterest'    => array( __( 'Pinterest URL', 'maison-verrane' ), '' ),
	);
	foreach ( $store as $key => $def ) {
		$wp_customize->add_setting( 'mv_' . $key, array( 'default' => $def[1], 'sanitize_callback' => in_array( $key, array( 'instagram', 'pinterest' ), true ) ? 'esc_url_raw' : 'sanitize_text_field' ) );
		$wp_customize->add_control( 'mv_' . $key, array( 'label' => $def[0], 'section' => 'mv_store', 'type' => ( 'footer_text' === $key ) ? 'textarea' : 'text' ) );
	}
	$wp_customize->add_setting( 'mv_demo_notice', array( 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean' ) );
	$wp_customize->add_control( 'mv_demo_notice', array( 'label' => __( 'Show "demo store" notice in footer', 'maison-verrane' ), 'section' => 'mv_store', 'type' => 'checkbox' ) );

	/* ---------- Hero slides ---------- */
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	$hero = array(
		1 => array( 'Handbags finished by hand, built to be inherited', 'Bottle-green calf leather, solid brass hardware and a lining that outlasts the seasons.', 'Shop handbags', 'hero-1.jpg', home_url( '/product-category/handbags/' ) ),
		2 => array( 'Watches for the hours that matter', 'Automatic movements, sapphire crystal and straps cut to the wrist. Engraving is always complimentary.', 'Shop watches', 'hero-2.jpg', home_url( '/product-category/watches/' ) ),
		3 => array( 'Fragrance, bottled in small batches', 'Green vetiver, smoked amber and a crushed-fig eau de parfum that stays close to the skin.', 'Shop fragrance', 'hero-3.jpg', home_url( '/product-category/fragrance/' ) ),
	);
	foreach ( $hero as $i => $h ) {
		$sec = 'mv_hero_' . $i;
		/* translators: %d: slide number */
		$wp_customize->add_section( $sec, array( 'title' => sprintf( __( 'Hero banner %d', 'maison-verrane' ), $i ), 'priority' => 31 ) );
		$fields = array( 'title' => array( 'Headline', $h[0], 'text' ), 'text' => array( 'Text', $h[1], 'textarea' ), 'btn' => array( 'Button label', $h[2], 'text' ), 'url' => array( 'Button link', $h[4], 'url' ) );
		foreach ( $fields as $f => $d ) {
			$wp_customize->add_setting( "mv_hero_{$i}_{$f}", array( 'default' => $d[1], 'sanitize_callback' => 'url' === $d[2] ? 'esc_url_raw' : 'sanitize_text_field' ) );
			$wp_customize->add_control( "mv_hero_{$i}_{$f}", array( 'label' => $d[0], 'section' => $sec, 'type' => $d[2] ) );
		}
		$wp_customize->add_setting( "mv_hero_{$i}_img", array( 'default' => mv_demo_img( $h[3] ), 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "mv_hero_{$i}_img", array( 'label' => __( 'Image (1600×1000 recommended)', 'maison-verrane' ), 'section' => $sec ) ) );
	}

	/* ---------- Promo banners ---------- */
	$promos = array(
		1 => array( 'Winter edit: up to 25% off selected watches', 'Limited pieces, complimentary engraving.', 'View the offer', 'promo-1.jpg', add_query_arg( 'on_sale', '1', $shop ) ),
		2 => array( 'Welcome offer: 10% off your first order', 'Use code WELCOME10 at checkout.', 'Start shopping', 'promo-2.jpg', $shop ),
	);
	foreach ( $promos as $i => $p ) {
		$sec = 'mv_promo_' . $i;
		/* translators: %d: promo number */
		$wp_customize->add_section( $sec, array( 'title' => sprintf( __( 'Promo banner %d', 'maison-verrane' ), $i ), 'priority' => 32 ) );
		$fields = array( 'title' => array( 'Headline', $p[0], 'text' ), 'text' => array( 'Text', $p[1], 'text' ), 'btn' => array( 'Button label', $p[2], 'text' ), 'url' => array( 'Button link', $p[4], 'url' ) );
		foreach ( $fields as $f => $d ) {
			$wp_customize->add_setting( "mv_promo_{$i}_{$f}", array( 'default' => $d[1], 'sanitize_callback' => 'url' === $d[2] ? 'esc_url_raw' : 'sanitize_text_field' ) );
			$wp_customize->add_control( "mv_promo_{$i}_{$f}", array( 'label' => $d[0], 'section' => $sec, 'type' => $d[2] ) );
		}
		$wp_customize->add_setting( "mv_promo_{$i}_img", array( 'default' => mv_demo_img( $p[3] ), 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "mv_promo_{$i}_img", array( 'label' => __( 'Image (1000×700 recommended)', 'maison-verrane' ), 'section' => $sec ) ) );
	}
}
add_action( 'customize_register', 'mv_customize_register' );
