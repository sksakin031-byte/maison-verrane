<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="content" class="mv-main">
	<div class="mv-container mv-narrow">
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<h1 class="mv-title"><?php single_post_title(); ?></h1>
		<?php endif; ?>
		<?php if ( have_posts() ) : ?>
			<div class="mv-posts">
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class( 'mv-post' ); ?>>
					<h2 class="mv-post__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="mv-prose"><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
			</div>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<h1 class="mv-title"><?php esc_html_e( 'Nothing found', 'maison-verrane' ); ?></h1>
			<p><?php esc_html_e( 'Try a different search, or browse the shop.', 'maison-verrane' ); ?></p>
			<a class="mv-btn" href="<?php echo esc_url( mv_shop_url() ); ?>"><?php esc_html_e( 'Go to the shop', 'maison-verrane' ); ?></a>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
