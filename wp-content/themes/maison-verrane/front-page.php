<?php
/**
 * Home page.
 *
 * @package maison-verrane
 */
defined( 'ABSPATH' ) || exit;
get_header();

$featured_tax = array( array( 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'featured', 'operator' => 'IN' ) );
$cats         = taxonomy_exists( 'product_cat' ) ? get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 4, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) ) : array();
$about        = get_page_by_path( 'about-us' );
?>
<main id="content" class="mv-main mv-home">

	<section class="mv-hero" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Featured collections', 'maison-verrane' ); ?>" data-hero>
		<div class="mv-hero__track">
			<?php for ( $i = 1; $i <= 3; $i++ ) :
				$title = mv_opt( "hero_{$i}_title" );
				if ( ! $title ) { continue; }
				$tag = ( 1 === $i ) ? 'h1' : 'h2';
				?>
				<article class="mv-slide<?php echo ( 1 === $i ) ? ' is-active' : ''; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( $i . ' / 3' ); ?>"<?php echo ( 1 === $i ) ? '' : ' aria-hidden="true"'; ?>>
					<div class="mv-slide__copy">
						<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="mv-slide__title"><?php echo esc_html( $title ); ?></<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<p class="mv-slide__text"><?php echo esc_html( mv_opt( "hero_{$i}_text" ) ); ?></p>
						<a class="mv-btn" href="<?php echo esc_url( mv_opt( "hero_{$i}_url", mv_shop_url() ) ); ?>"><?php echo esc_html( mv_opt( "hero_{$i}_btn", __( 'Shop now', 'maison-verrane' ) ) ); ?></a>
					</div>
					<div class="mv-slide__media">
						<img src="<?php echo esc_url( mv_opt( "hero_{$i}_img" ) ); ?>" alt="" width="1600" height="1000" <?php echo ( 1 === $i ) ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
					</div>
				</article>
			<?php endfor; ?>
		</div>
		<div class="mv-hero__controls">
			<button type="button" class="mv-iconbtn" data-hero-prev><?php echo mv_icon( 'prev' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php esc_html_e( 'Previous slide', 'maison-verrane' ); ?></span></button>
			<div class="mv-hero__dots" role="tablist" aria-label="<?php esc_attr_e( 'Choose slide', 'maison-verrane' ); ?>"></div>
			<button type="button" class="mv-iconbtn" data-hero-next><?php echo mv_icon( 'next' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="screen-reader-text"><?php esc_html_e( 'Next slide', 'maison-verrane' ); ?></span></button>
			<button type="button" class="mv-iconbtn" data-hero-toggle aria-label="<?php esc_attr_e( 'Pause slideshow', 'maison-verrane' ); ?>"><?php echo mv_icon( 'pause', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
		</div>
	</section>

	<?php if ( $cats && ! is_wp_error( $cats ) ) : ?>
	<section class="mv-section mv-container" aria-labelledby="mv-h-cats">
		<div class="mv-section__head">
			<h2 id="mv-h-cats" class="mv-h2"><?php esc_html_e( 'Shop by category', 'maison-verrane' ); ?></h2>
			<a class="mv-link" href="<?php echo esc_url( home_url( '/categories/' ) ); ?>"><?php esc_html_e( 'All categories', 'maison-verrane' ); ?></a>
		</div>
		<ul class="mv-cats">
			<?php foreach ( $cats as $c ) :
				$thumb = get_term_meta( $c->term_id, 'thumbnail_id', true );
				$src   = $thumb ? wp_get_attachment_image_url( $thumb, 'woocommerce_single' ) : '';
				?>
				<li class="mv-cat">
					<a href="<?php echo esc_url( get_term_link( $c ) ); ?>">
						<span class="mv-cat__img"><?php if ( $src ) : ?><img src="<?php echo esc_url( $src ); ?>" alt="" loading="lazy" width="500" height="625"><?php endif; ?></span>
						<span class="mv-cat__name"><?php echo esc_html( $c->name ); ?></span>
						<span class="mv-cat__count"><?php echo esc_html( sprintf( /* translators: %d: product count */ _n( '%d piece', '%d pieces', $c->count, 'maison-verrane' ), $c->count ) ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php endif; ?>

	<?php if ( function_exists( 'WC' ) ) : ?>
	<section class="mv-section mv-container" aria-labelledby="mv-h-feat">
		<div class="mv-section__head">
			<h2 id="mv-h-feat" class="mv-h2"><?php esc_html_e( 'The Maison selection', 'maison-verrane' ); ?></h2>
			<a class="mv-link" href="<?php echo esc_url( mv_shop_url() ); ?>"><?php esc_html_e( 'Shop everything', 'maison-verrane' ); ?></a>
		</div>
		<?php mv_product_grid( array( 'posts_per_page' => 4, 'tax_query' => $featured_tax ) ); // phpcs:ignore WordPress.DB.SlowDBQuery ?>
	</section>
	<?php endif; ?>

	<section class="mv-promos mv-container" aria-label="<?php esc_attr_e( 'Offers', 'maison-verrane' ); ?>">
		<?php for ( $i = 1; $i <= 2; $i++ ) : ?>
			<a class="mv-promo" href="<?php echo esc_url( mv_opt( "promo_{$i}_url", mv_shop_url() ) ); ?>">
				<img src="<?php echo esc_url( mv_opt( "promo_{$i}_img" ) ); ?>" alt="" loading="lazy" width="1000" height="700">
				<span class="mv-promo__copy">
					<span class="mv-promo__title"><?php echo esc_html( mv_opt( "promo_{$i}_title" ) ); ?></span>
					<span class="mv-promo__text"><?php echo esc_html( mv_opt( "promo_{$i}_text" ) ); ?></span>
					<span class="mv-btn mv-btn--light"><?php echo esc_html( mv_opt( "promo_{$i}_btn", __( 'Discover', 'maison-verrane' ) ) ); ?></span>
				</span>
			</a>
		<?php endfor; ?>
	</section>

	<?php if ( function_exists( 'WC' ) ) : ?>
	<section class="mv-section mv-container" aria-labelledby="mv-h-new">
		<div class="mv-section__head">
			<h2 id="mv-h-new" class="mv-h2"><?php esc_html_e( 'New arrivals', 'maison-verrane' ); ?></h2>
			<a class="mv-link" href="<?php echo esc_url( add_query_arg( 'orderby', 'date', mv_shop_url() ) ); ?>"><?php esc_html_e( 'See what is new', 'maison-verrane' ); ?></a>
		</div>
		<?php mv_product_grid( array( 'posts_per_page' => 4, 'orderby' => 'date', 'order' => 'DESC' ) ); ?>
	</section>
	<?php endif; ?>

	<section class="mv-story mv-container">
		<div class="mv-story__media"><img src="<?php echo esc_url( mv_demo_img( 'about.jpg' ) ); ?>" alt="" loading="lazy" width="1600" height="1000"></div>
		<div class="mv-story__copy">
			<h2 class="mv-h2"><?php esc_html_e( 'Made slowly, kept for decades', 'maison-verrane' ); ?></h2>
			<p><?php esc_html_e( 'Every Maison Verrane piece is designed to be repaired rather than replaced. We cut fewer styles, in better materials, and stand behind each one for life.', 'maison-verrane' ); ?></p>
			<a class="mv-btn mv-btn--ghost" href="<?php echo esc_url( $about ? get_permalink( $about ) : home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'Our story', 'maison-verrane' ); ?></a>
		</div>
	</section>

	<section class="mv-values mv-container" aria-label="<?php esc_attr_e( 'Our promises', 'maison-verrane' ); ?>">
		<div><strong><?php esc_html_e( 'Complimentary shipping', 'maison-verrane' ); ?></strong><span><?php esc_html_e( 'On every order over $250', 'maison-verrane' ); ?></span></div>
		<div><strong><?php esc_html_e( 'Easy returns', 'maison-verrane' ); ?></strong><span><?php esc_html_e( '30 days on unworn pieces', 'maison-verrane' ); ?></span></div>
		<div><strong><?php esc_html_e( 'Two-year guarantee', 'maison-verrane' ); ?></strong><span><?php esc_html_e( 'On every watch and bag', 'maison-verrane' ); ?></span></div>
		<div><strong><?php esc_html_e( 'Secure checkout', 'maison-verrane' ); ?></strong><span><?php esc_html_e( 'Demo payments run in test mode', 'maison-verrane' ); ?></span></div>
	</section>
</main>
<?php get_footer(); ?>
