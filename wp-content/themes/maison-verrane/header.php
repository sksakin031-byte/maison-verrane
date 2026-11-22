<?php
/**
 * Site header.
 *
 * @package maison-verrane
 */
defined( 'ABSPATH' ) || exit;
$announcement = mv_opt( 'announcement' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="mv-skip" href="#content"><?php esc_html_e( 'Skip to content', 'maison-verrane' ); ?></a>

<?php if ( $announcement ) : ?>
	<div class="mv-announce"><p><?php echo esc_html( $announcement ); ?></p></div>
<?php endif; ?>

<header class="mv-header" id="mv-header">
	<div class="mv-container mv-header__inner">
		<button type="button" class="mv-iconbtn mv-navtoggle" aria-expanded="false" aria-controls="mv-nav">
			<?php echo mv_icon( 'menu', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'maison-verrane' ); ?></span>
		</button>

		<div class="mv-brand"><?php mv_brand(); ?></div>

		<nav class="mv-nav" id="mv-nav" aria-label="<?php esc_attr_e( 'Primary', 'maison-verrane' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'mv-menu',
				'fallback_cb'    => 'mv_fallback_menu',
				'depth'          => 2,
			) );
			?>
		</nav>

		<div class="mv-tools">
			<button type="button" class="mv-iconbtn mv-searchtoggle" aria-expanded="false" aria-controls="mv-search">
				<?php echo mv_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Search', 'maison-verrane' ); ?></span>
			</button>
			<a class="mv-iconbtn mv-account" href="<?php echo esc_url( mv_account_url() ); ?>">
				<?php echo mv_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span class="screen-reader-text"><?php esc_html_e( 'My account', 'maison-verrane' ); ?></span>
			</a>
			<a class="mv-iconbtn mv-wishlink" href="<?php echo esc_url( home_url( '/wishlist/' ) ); ?>">
				<?php echo mv_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span class="mv-count mv-wish-count" data-count="0">0</span>
				<span class="screen-reader-text"><?php esc_html_e( 'Wishlist', 'maison-verrane' ); ?></span>
			</a>
			<?php if ( function_exists( 'wc_get_cart_url' ) ) : ?>
				<a class="mv-iconbtn mv-cartlink" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
					<?php echo mv_icon( 'bag' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="mv-cart-count mv-count" data-count="<?php echo esc_attr( mv_cart_count() ); ?>"><?php echo esc_html( mv_cart_count() ); ?></span>
					<span class="screen-reader-text"><?php esc_html_e( 'Cart', 'maison-verrane' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>

	<div class="mv-search" id="mv-search" hidden>
		<div class="mv-container">
			<form role="search" method="get" class="mv-search__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="mv-s"><?php esc_html_e( 'Search products', 'maison-verrane' ); ?></label>
				<input id="mv-s" type="search" name="s" placeholder="<?php esc_attr_e( 'Search bags, watches, fragrance…', 'maison-verrane' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off">
				<input type="hidden" name="post_type" value="product">
				<button type="submit" class="mv-btn mv-btn--small"><?php esc_html_e( 'Search', 'maison-verrane' ); ?></button>
			</form>
		</div>
	</div>
</header>
