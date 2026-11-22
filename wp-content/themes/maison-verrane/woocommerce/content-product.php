<?php
/**
 * Product card used in every product loop (shop, home, related, wishlist).
 *
 * @package maison-verrane
 */
defined( 'ABSPATH' ) || exit;

global $product;
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'mv-card', $product ); ?>>
	<div class="mv-card__media">
		<a class="mv-card__img" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>
		<?php woocommerce_show_product_loop_sale_flash(); ?>
		<?php mv_wishlist_button( $product->get_id() ); ?>
	</div>
	<div class="mv-card__body">
		<h3 class="mv-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php woocommerce_template_loop_rating(); ?>
		<?php woocommerce_template_loop_price(); ?>
	</div>
	<?php woocommerce_template_loop_add_to_cart(); ?>
</li>
