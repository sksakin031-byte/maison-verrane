<?php
/**
 * Maison Verrane theme bootstrap.
 *
 * @package maison-verrane
 */
defined( 'ABSPATH' ) || exit;

define( 'MV_VERSION', '1.0.0' );
define( 'MV_DIR', get_template_directory() );
define( 'MV_URI', get_template_directory_uri() );

require_once MV_DIR . '/inc/setup.php';
require_once MV_DIR . '/inc/customizer.php';
require_once MV_DIR . '/inc/template-tags.php';
require_once MV_DIR . '/inc/wishlist.php';
require_once MV_DIR . '/inc/contact.php';
require_once MV_DIR . '/inc/seo.php';
require_once MV_DIR . '/inc/security.php';
require_once MV_DIR . '/inc/demo-importer.php';

if ( class_exists( 'WooCommerce' ) ) {
	require_once MV_DIR . '/inc/woocommerce.php';
}
