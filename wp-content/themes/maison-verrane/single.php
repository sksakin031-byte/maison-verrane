<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="content" class="mv-main">
	<div class="mv-container mv-narrow">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'mv-page' ); ?>>
				<h1 class="mv-title"><?php the_title(); ?></h1>
				<div class="mv-prose"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>
