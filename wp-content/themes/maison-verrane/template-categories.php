<?php
/**
 * Template Name: Categories
 */
defined( 'ABSPATH' ) || exit;
get_header();
$cats = taxonomy_exists( 'product_cat' ) ? get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 0, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) ) : array();
?>
<main id="content" class="mv-main">
	<header class="mv-pagehead mv-container">
		<h1 class="mv-title mv-title--xl"><?php the_title(); ?></h1>
		<p class="mv-lede"><?php esc_html_e( 'Four houses of craft, from leather to fragrance.', 'maison-verrane' ); ?></p>
	</header>
	<div class="mv-container">
		<?php if ( $cats && ! is_wp_error( $cats ) ) : ?>
		<ul class="mv-cats mv-cats--page">
			<?php foreach ( $cats as $c ) :
				$thumb = get_term_meta( $c->term_id, 'thumbnail_id', true );
				$src   = $thumb ? wp_get_attachment_image_url( $thumb, 'woocommerce_single' ) : '';
				?>
				<li class="mv-cat">
					<a href="<?php echo esc_url( get_term_link( $c ) ); ?>">
						<span class="mv-cat__img"><?php if ( $src ) : ?><img src="<?php echo esc_url( $src ); ?>" alt="" loading="lazy" width="500" height="625"><?php endif; ?></span>
						<span class="mv-cat__name"><?php echo esc_html( $c->name ); ?></span>
						<span class="mv-cat__count"><?php echo esc_html( wp_strip_all_tags( $c->description ) ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php else : ?>
			<p><?php esc_html_e( 'No categories yet. Add product categories in WooCommerce → Products → Categories.', 'maison-verrane' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
