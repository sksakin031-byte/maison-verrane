<?php
/**
 * Template Name: Contact
 */
defined( 'ABSPATH' ) || exit;
get_header();
$sent  = isset( $_GET['sent'] ); // phpcs:ignore WordPress.Security.NonceVerification
$error = isset( $_GET['error'] ) ? sanitize_key( wp_unslash( $_GET['error'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$msgs  = array(
	'invalid' => __( 'Please enter your name, a valid email and a message of at least 10 characters.', 'maison-verrane' ),
	'wait'    => __( 'Please wait a moment before sending another message.', 'maison-verrane' ),
	'nonce'   => __( 'Your session expired. Reload the page and try again.', 'maison-verrane' ),
	'mail'    => __( 'We could not send your message. Please email us directly.', 'maison-verrane' ),
);
?>
<main id="content" class="mv-main">
	<?php while ( have_posts() ) : the_post(); ?>
	<header class="mv-pagehead mv-container">
		<h1 class="mv-title mv-title--xl"><?php the_title(); ?></h1>
	</header>
	<div class="mv-container mv-contact">
		<div class="mv-contact__info">
			<div class="mv-prose"><?php the_content(); ?></div>
			<dl class="mv-dl">
				<dt><?php esc_html_e( 'Email', 'maison-verrane' ); ?></dt><dd><a href="mailto:<?php echo esc_attr( mv_opt( 'email' ) ); ?>"><?php echo esc_html( mv_opt( 'email' ) ); ?></a></dd>
				<dt><?php esc_html_e( 'Phone', 'maison-verrane' ); ?></dt><dd><?php echo esc_html( mv_opt( 'phone' ) ); ?></dd>
				<dt><?php esc_html_e( 'Atelier', 'maison-verrane' ); ?></dt><dd><?php echo esc_html( mv_opt( 'address' ) ); ?></dd>
				<dt><?php esc_html_e( 'Hours', 'maison-verrane' ); ?></dt><dd><?php echo esc_html( mv_opt( 'hours' ) ); ?></dd>
			</dl>
		</div>
		<form class="mv-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php if ( $sent ) : ?><p class="mv-alert mv-alert--ok" role="status"><?php esc_html_e( 'Thank you. Your message has been sent and we will reply within one working day.', 'maison-verrane' ); ?></p><?php endif; ?>
			<?php if ( $error && isset( $msgs[ $error ] ) ) : ?><p class="mv-alert mv-alert--err" role="alert"><?php echo esc_html( $msgs[ $error ] ); ?></p><?php endif; ?>
			<input type="hidden" name="action" value="mv_contact">
			<?php wp_nonce_field( 'mv_contact', 'mv_contact_nonce' ); ?>
			<p class="mv-hp" aria-hidden="true"><label>Website<input type="text" name="mv_website" tabindex="-1" autocomplete="off"></label></p>
			<p><label for="mv_name"><?php esc_html_e( 'Your name', 'maison-verrane' ); ?></label><input id="mv_name" name="mv_name" type="text" required autocomplete="name"></p>
			<p><label for="mv_email"><?php esc_html_e( 'Email address', 'maison-verrane' ); ?></label><input id="mv_email" name="mv_email" type="email" required autocomplete="email"></p>
			<p><label for="mv_subject"><?php esc_html_e( 'Subject', 'maison-verrane' ); ?></label><input id="mv_subject" name="mv_subject" type="text"></p>
			<p><label for="mv_message"><?php esc_html_e( 'How can we help?', 'maison-verrane' ); ?></label><textarea id="mv_message" name="mv_message" rows="6" required minlength="10"></textarea></p>
			<button type="submit" class="mv-btn"><?php esc_html_e( 'Send message', 'maison-verrane' ); ?></button>
		</form>
	</div>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
