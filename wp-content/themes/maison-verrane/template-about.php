<?php
/**
 * Template Name: About
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="content" class="mv-main">
	<?php while ( have_posts() ) : the_post(); ?>
	<header class="mv-pagehead mv-container">
		<h1 class="mv-title mv-title--xl"><?php the_title(); ?></h1>
		<?php if ( has_excerpt() ) : ?><p class="mv-lede"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
	</header>
	<div class="mv-container">
		<div class="mv-wide-img"><?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large' ); } else { ?><img src="<?php echo esc_url( mv_demo_img( 'about.jpg' ) ); ?>" alt="" width="1600" height="1000" loading="lazy"><?php } ?></div>
	</div>
	<div class="mv-container mv-narrow">
		<div class="mv-prose"><?php the_content(); ?></div>
	</div>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
