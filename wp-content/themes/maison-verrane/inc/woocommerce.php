<?php
/**
 * WooCommerce integration.
 */
defined( 'ABSPATH' ) || exit;

/* Use our own layout CSS instead of WooCommerce layout/smallscreen. */
add_filter( 'woocommerce_enqueue_styles', function ( $styles ) {
	unset( $styles['woocommerce-layout'], $styles['woocommerce-smallscreen'] );
	return $styles;
} );

/* Wrappers. */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
add_action( 'woocommerce_before_main_content', function () {
	echo '<main id="content" class="mv-main mv-shop"><div class="mv-container">';
}, 10 );
add_action( 'woocommerce_after_main_content', function () {
	echo '</div></main>';
}, 10 );

add_filter( 'loop_shop_per_page', function () { return 12; } );
add_filter( 'loop_shop_columns', function () { return 4; } );
add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );
add_filter( 'woocommerce_breadcrumb_defaults', function ( $d ) {
	$d['delimiter']   = '<span class="mv-crumb-sep" aria-hidden="true">/</span>';
	$d['wrap_before'] = '<nav class="woocommerce-breadcrumb mv-crumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'maison-verrane' ) . '">';
	return $d;
} );

/* Live cart count in header. */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$fragments['span.mv-cart-count'] = '<span class="mv-cart-count mv-count" data-count="' . mv_cart_count() . '">' . mv_cart_count() . '</span>';
	return $fragments;
} );

/* Single product extras. */
add_action( 'woocommerce_after_add_to_cart_button', function () {
	global $product;
	if ( $product ) {
		mv_wishlist_button( $product->get_id(), true );
	}
}, 20 );
add_action( 'woocommerce_single_product_summary', function () {
	echo '<ul class="mv-assurances">';
	echo '<li>' . esc_html__( 'Complimentary shipping over $250', 'maison-verrane' ) . '</li>';
	echo '<li>' . esc_html__( '30-day returns on unworn items', 'maison-verrane' ) . '</li>';
	echo '<li>' . esc_html__( 'Two-year Maison guarantee', 'maison-verrane' ) . '</li>';
	echo '</ul>';
}, 45 );

/* "On sale" filter for the main shop query (?on_sale=1). */
add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() || ! $q->is_main_query() || ! isset( $_GET['on_sale'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	if ( $q->is_post_type_archive( 'product' ) || $q->is_tax( get_object_taxonomies( 'product' ) ) || ( $q->is_search() && 'product' === $q->get( 'post_type' ) ) ) {
		$q->set( 'post__in', array_merge( array( 0 ), wc_get_product_ids_on_sale() ) );
	}
} );

/** Base URL for the filter bar (current archive without filter args). */
function mv_filter_base_url() {
	if ( is_search() ) {
		return add_query_arg( array( 'post_type' => 'product', 's' => get_search_query() ), home_url( '/' ) );
	}
	if ( is_tax() ) {
		$link = get_term_link( get_queried_object() );
		return is_wp_error( $link ) ? mv_shop_url() : $link;
	}
	return mv_shop_url();
}

/** Horizontal filter bar: categories, price ranges, on-sale. */
function mv_render_filters() {
	$base    = mv_filter_base_url();
	$orderby = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$min     = isset( $_GET['min_price'] ) ? absint( $_GET['min_price'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$max     = isset( $_GET['max_price'] ) ? absint( $_GET['max_price'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$sale    = isset( $_GET['on_sale'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$keep    = array_filter( array( 'orderby' => $orderby ) );
	$cats    = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
	$current = is_tax( 'product_cat' ) ? get_queried_object_id() : 0;
	$ranges  = array(
		array( 0, 150, __( 'Under $150', 'maison-verrane' ) ),
		array( 150, 400, __( '$150 to $400', 'maison-verrane' ) ),
		array( 400, 1000, __( '$400 to $1,000', 'maison-verrane' ) ),
		array( 1000, 0, __( 'Over $1,000', 'maison-verrane' ) ),
	);
	?>
	<div class="mv-filters" id="mv-filters">
		<button type="button" class="mv-filters__toggle" aria-expanded="false" aria-controls="mv-filters-panel">
			<?php echo mv_icon( 'filter', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span><?php esc_html_e( 'Filter', 'maison-verrane' ); ?></span>
		</button>
		<div class="mv-filters__panel" id="mv-filters-panel">
			<div class="mv-filters__group" role="group" aria-label="<?php esc_attr_e( 'Category', 'maison-verrane' ); ?>">
				<a class="mv-chip<?php echo ( ! $current && is_shop() ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( mv_shop_url() ); ?>"><?php esc_html_e( 'All', 'maison-verrane' ); ?></a>
				<?php if ( ! is_wp_error( $cats ) ) : foreach ( $cats as $c ) : ?>
					<a class="mv-chip<?php echo ( $current === $c->term_id ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $c ) ); ?>"><?php echo esc_html( $c->name ); ?></a>
				<?php endforeach; endif; ?>
			</div>
			<div class="mv-filters__group" role="group" aria-label="<?php esc_attr_e( 'Price', 'maison-verrane' ); ?>">
				<?php foreach ( $ranges as $r ) :
					$active = ( (int) $min === (int) $r[0] && (int) $max === (int) $r[1] && ( $min !== '' || $max !== '' ) );
					$args   = $keep;
					if ( $r[0] ) { $args['min_price'] = $r[0]; }
					if ( $r[1] ) { $args['max_price'] = $r[1]; }
					$url = $active ? add_query_arg( $keep, $base ) : add_query_arg( $args, $base );
					?>
					<a class="mv-chip<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( $url ); ?>" aria-pressed="<?php echo $active ? 'true' : 'false'; ?>"><?php echo esc_html( $r[2] ); ?></a>
				<?php endforeach; ?>
				<a class="mv-chip<?php echo $sale ? ' is-active' : ''; ?>" href="<?php echo esc_url( $sale ? add_query_arg( $keep, $base ) : add_query_arg( array_merge( $keep, array( 'on_sale' => 1 ) ), $base ) ); ?>" aria-pressed="<?php echo $sale ? 'true' : 'false'; ?>"><?php esc_html_e( 'On sale', 'maison-verrane' ); ?></a>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_before_shop_loop', 'mv_render_filters', 5 );

/* Cleaner shop title area. */
add_filter( 'woocommerce_show_page_title', '__return_true' );
