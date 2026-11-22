<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="content" class="mv-main">
	<div class="mv-container mv-narrow mv-center">
		<h1 class="mv-title"><?php esc_html_e( 'This page could not be found', 'maison-verrane' ); ?></h1>
		<p><?php esc_html_e( 'The link may be old or mistyped. Search the collection or return to the shop.', 'maison-verrane' ); ?></p>
		<a class="mv-btn" href="<?php echo esc_url( mv_shop_url() ); ?>"><?php esc_html_e( 'Go to the shop', 'maison-verrane' ); ?></a>
	</div>
</main>
<?php get_footer(); ?>
