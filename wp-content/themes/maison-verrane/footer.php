<?php
/**
 * Site footer.
 *
 * @package maison-verrane
 */
defined( 'ABSPATH' ) || exit;
?>
<footer class="mv-footer">
	<div class="mv-container mv-footer__grid">
		<div class="mv-footer__brand">
			<p class="mv-footer__name"><?php bloginfo( 'name' ); ?></p>
			<p><?php echo esc_html( mv_opt( 'footer_text' ) ); ?></p>
			<p class="mv-footer__social">
				<?php if ( mv_opt( 'instagram' ) ) : ?><a href="<?php echo esc_url( mv_opt( 'instagram' ) ); ?>" rel="noopener"><?php esc_html_e( 'Instagram', 'maison-verrane' ); ?></a><?php endif; ?>
				<?php if ( mv_opt( 'pinterest' ) ) : ?><a href="<?php echo esc_url( mv_opt( 'pinterest' ) ); ?>" rel="noopener"><?php esc_html_e( 'Pinterest', 'maison-verrane' ); ?></a><?php endif; ?>
			</p>
		</div>
		<nav class="mv-footer__col" aria-label="<?php esc_attr_e( 'Footer', 'maison-verrane' ); ?>">
			<h2 class="mv-footer__h"><?php esc_html_e( 'Explore', 'maison-verrane' ); ?></h2>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'container'      => false,
				'menu_class'     => 'mv-footer__menu',
				'fallback_cb'    => false,
				'depth'          => 1,
			) );
			?>
		</nav>
		<div class="mv-footer__col">
			<h2 class="mv-footer__h"><?php esc_html_e( 'Client care', 'maison-verrane' ); ?></h2>
			<ul class="mv-footer__menu">
				<li><a href="mailto:<?php echo esc_attr( mv_opt( 'email' ) ); ?>"><?php echo esc_html( mv_opt( 'email' ) ); ?></a></li>
				<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', mv_opt( 'phone' ) ) ); ?>"><?php echo esc_html( mv_opt( 'phone' ) ); ?></a></li>
				<li><?php echo esc_html( mv_opt( 'address' ) ); ?></li>
				<li><?php echo esc_html( mv_opt( 'hours' ) ); ?></li>
			</ul>
		</div>
	</div>
	<div class="mv-footer__bar">
		<div class="mv-container mv-footer__barinner">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.</p>
			<?php if ( mv_opt( 'demo_notice', true ) ) : ?>
				<p class="mv-demo-note"><?php esc_html_e( 'Demo store: payments run in test mode and no real money moves.', 'maison-verrane' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
