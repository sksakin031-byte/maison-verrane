<?php
/**
 * Template Name: Wishlist
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="content" class="mv-main">
	<header class="mv-pagehead mv-container">
		<h1 class="mv-title mv-title--xl"><?php the_title(); ?></h1>
		<p class="mv-lede"><?php echo is_user_logged_in() ? esc_html__( 'Saved to your account, available on every device.', 'maison-verrane' ) : esc_html__( 'Saved on this device. Sign in to keep your list on every device.', 'maison-verrane' ); ?></p>
	</header>
	<div class="mv-container">
		<div id="mv-wishlist-grid" class="woocommerce" aria-live="polite"></div>
		<div id="mv-wishlist-empty" class="mv-empty" hidden>
			<p><?php esc_html_e( 'Your wishlist is empty. Tap the heart on any piece to save it here.', 'maison-verrane' ); ?></p>
			<a class="mv-btn" href="<?php echo esc_url( mv_shop_url() ); ?>"><?php esc_html_e( 'Browse the shop', 'maison-verrane' ); ?></a>
		</div>
	</div>
</main>
<?php get_footer(); ?>
