<?php
/**
 * WooCommerce wrapper. Wrappers are added in inc/woocommerce.php.
 */
defined( 'ABSPATH' ) || exit;
get_header();
woocommerce_content();
get_footer();
