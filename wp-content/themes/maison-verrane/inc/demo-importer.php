<?php
/**
 * One-click demo content: categories, products, pages, menus, shipping, coupon, store options.
 * Run from Appearance → Maison demo setup, or: wp eval 'mv_run_demo_import();'
 * Safe to run more than once (items are matched by SKU / slug).
 */
defined( 'ABSPATH' ) || exit;

function mv_demo_menu() {
	add_theme_page( __( 'Maison demo setup', 'maison-verrane' ), __( 'Maison demo setup', 'maison-verrane' ), 'manage_options', 'mv-demo-setup', 'mv_demo_page' );
}
add_action( 'admin_menu', 'mv_demo_menu' );

function mv_demo_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$done = isset( $_GET['mv_done'] ); // phpcs:ignore WordPress.Security.NonceVerification
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Maison demo setup', 'maison-verrane' ); ?></h1>
		<?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
			<div class="notice notice-error"><p><?php esc_html_e( 'Install and activate WooCommerce first.', 'maison-verrane' ); ?></p></div>
		<?php else : ?>
			<?php if ( $done ) : ?><div class="notice notice-success"><p><?php esc_html_e( 'Demo content imported. Visit the site to review it.', 'maison-verrane' ); ?></p></div><?php endif; ?>
			<p><?php esc_html_e( 'Creates 4 categories, 12 sample products (original illustrations), pages, menus, a shipping zone, the WELCOME10 coupon and recommended store settings. Running it again does not create duplicates.', 'maison-verrane' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="mv_demo_import">
				<?php wp_nonce_field( 'mv_demo_import', 'mv_demo_nonce' ); ?>
				<?php submit_button( __( 'Import demo content', 'maison-verrane' ) ); ?>
			</form>
		<?php endif; ?>
	</div>
	<?php
}

function mv_demo_handle() {
	if ( ! current_user_can( 'manage_options' ) || ! isset( $_POST['mv_demo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mv_demo_nonce'] ) ), 'mv_demo_import' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'maison-verrane' ), '', array( 'response' => 403 ) );
	}
	mv_run_demo_import();
	wp_safe_redirect( admin_url( 'themes.php?page=mv-demo-setup&mv_done=1' ) );
	exit;
}
add_action( 'admin_post_mv_demo_import', 'mv_demo_handle' );

/** Import a theme demo image into the Media Library once. */
function mv_import_image( $file, $title ) {
	$existing = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_mv_demo_file', 'meta_value' => $file ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	if ( $existing ) {
		return (int) $existing[0];
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$src = MV_DIR . '/assets/demo/' . $file;
	if ( ! file_exists( $src ) ) {
		return 0;
	}
	$tmp = wp_tempnam( $file );
	copy( $src, $tmp );
	$id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), 0, $title );
	if ( is_wp_error( $id ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		return 0;
	}
	update_post_meta( $id, '_mv_demo_file', $file );
	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	return (int) $id;
}

function mv_demo_term( $name, $slug, $desc, $image ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( ! $term ) {
		$res = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug, 'description' => $desc ) );
		if ( is_wp_error( $res ) ) {
			return 0;
		}
		$term_id = (int) $res['term_id'];
	} else {
		$term_id = (int) $term->term_id;
	}
	$img = mv_import_image( $image, $name );
	if ( $img ) {
		update_term_meta( $term_id, 'thumbnail_id', $img );
	}
	return $term_id;
}

function mv_demo_page_create( $slug, $title, $content, $template = '', $excerpt = '' ) {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return (int) $page->ID;
	}
	$id = wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => $content,
		'post_excerpt' => $excerpt,
	) );
	if ( $id && ! is_wp_error( $id ) && $template ) {
		update_post_meta( $id, '_wp_page_template', $template );
	}
	return is_wp_error( $id ) ? 0 : (int) $id;
}

function mv_run_demo_import() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	/* ---------- Store options ---------- */
	update_option( 'woocommerce_currency', 'USD' );
	update_option( 'woocommerce_default_country', 'US:NY' );
	update_option( 'woocommerce_enable_reviews', 'yes' );
	update_option( 'woocommerce_enable_review_rating', 'yes' );
	update_option( 'woocommerce_review_rating_required', 'yes' );
	update_option( 'woocommerce_enable_coupons', 'yes' );
	update_option( 'woocommerce_enable_guest_checkout', 'yes' );
	update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
	update_option( 'woocommerce_thumbnail_cropping', 'custom' );
	update_option( 'woocommerce_thumbnail_cropping_custom_width', 4 );
	update_option( 'woocommerce_thumbnail_cropping_custom_height', 5 );
	update_option( 'woocommerce_calc_taxes', 'no' );
	update_option( 'permalink_structure', '/%postname%/' );
	update_option( 'blogdescription', 'Luxury handbags, watches and fragrance (demo store)' );

	/* ---------- Categories ---------- */
	$cat = array(
		'handbags'    => mv_demo_term( 'Handbags', 'handbags', 'Hand-finished leather bags.', 'tote.jpg' ),
		'watches'     => mv_demo_term( 'Watches', 'watches', 'Automatic and quartz timepieces.', 'meridian.jpg' ),
		'fragrance'   => mv_demo_term( 'Fragrance', 'fragrance', 'Small-batch eaux de parfum.', 'nuit-verte.jpg' ),
		'accessories' => mv_demo_term( 'Accessories', 'accessories', 'Silk, leather and eyewear.', 'scarf.jpg' ),
	);

	/* ---------- Products ---------- */
	$long = 'Designed in our demo atelier and finished by hand. Every detail, from the stitching to the hardware, is made to be repaired and kept for decades. This is fictional sample content for presentation.';
	$products = array(
		array( 'MV-BAG-001', 'Verrane Tote', 'handbags', 890, 0, 'tote.jpg', 'Structured calf-leather tote with a brass clasp and suede-lined interior.', array( 'Material' => 'Calf leather', 'Hardware' => 'Solid brass', 'Dimensions' => '34 × 28 × 14 cm' ), true ),
		array( 'MV-BAG-002', 'Crescent Shoulder Bag', 'handbags', 1120, 950, 'crescent.jpg', 'A soft crescent silhouette in oxblood leather with an adjustable strap.', array( 'Material' => 'Grained leather', 'Strap drop' => '22 cm', 'Dimensions' => '29 × 18 × 9 cm' ), false ),
		array( 'MV-BAG-003', 'Atelier Clutch', 'handbags', 540, 0, 'clutch.jpg', 'Slim evening clutch in black nappa with a magnetic closure.', array( 'Material' => 'Nappa leather', 'Closure' => 'Magnetic', 'Dimensions' => '26 × 16 × 3 cm' ), false ),
		array( 'MV-WAT-001', 'Meridian Automatic', 'watches', 1850, 0, 'meridian.jpg', 'A 40 mm automatic with a brass-tone case, ivory dial and leather strap.', array( 'Movement' => 'Automatic', 'Case' => '40 mm', 'Water resistance' => '50 m' ), true ),
		array( 'MV-WAT-002', 'Solstice Chronograph', 'watches', 2450, 1990, 'solstice.jpg', 'Steel chronograph with twin sub-dials and a deep teal dial.', array( 'Movement' => 'Automatic chronograph', 'Case' => '42 mm', 'Water resistance' => '100 m' ), true ),
		array( 'MV-WAT-003', 'Slate Dress Watch', 'watches', 1380, 0, 'slate.jpg', 'An ultra-slim dress watch with a green leather strap.', array( 'Movement' => 'Quartz', 'Case' => '36 mm', 'Thickness' => '6.5 mm' ), false ),
		array( 'MV-FRA-001', 'Nuit Verte Eau de Parfum', 'fragrance', 165, 0, 'nuit-verte.jpg', 'Green vetiver, fig leaf and cedar. 50 ml.', array( 'Volume' => '50 ml', 'Family' => 'Green woody', 'Notes' => 'Vetiver, fig, cedar' ), true ),
		array( 'MV-FRA-002', 'Ambre Gris Extrait', 'fragrance', 240, 0, 'ambre.jpg', 'Smoked amber, tonka and a trace of salt. 50 ml extrait.', array( 'Volume' => '50 ml', 'Family' => 'Amber', 'Notes' => 'Amber, tonka, salt' ), false ),
		array( 'MV-FRA-003', 'Vetiver Brume', 'fragrance', 95, 79, 'vetiver.jpg', 'A light body and linen mist. 100 ml.', array( 'Volume' => '100 ml', 'Family' => 'Fresh', 'Notes' => 'Vetiver, bergamot' ), false ),
		array( 'MV-ACC-001', 'Silk Twill Scarf', 'accessories', 210, 0, 'scarf.jpg', 'Hand-rolled silk twill scarf, 90 × 90 cm.', array( 'Material' => '100% silk twill', 'Size' => '90 × 90 cm', 'Finish' => 'Hand-rolled edges' ), true ),
		array( 'MV-ACC-002', 'Card Holder', 'accessories', 160, 0, 'cardholder.jpg', 'Six-slot card holder in black grained leather.', array( 'Material' => 'Grained leather', 'Slots' => '6', 'Dimensions' => '11 × 8 cm' ), false ),
		array( 'MV-ACC-003', 'Round Sunglasses', 'accessories', 320, 0, 'aviator.jpg', 'Acetate round sunglasses with UV400 mineral lenses.', array( 'Frame' => 'Acetate', 'Lens' => 'Mineral UV400', 'Fit' => 'Medium' ), true ),
	);

	foreach ( $products as $p ) {
		if ( wc_get_product_id_by_sku( $p[0] ) ) {
			continue;
		}
		$product = new WC_Product_Simple();
		$product->set_name( $p[1] );
		$product->set_sku( $p[0] );
		$product->set_regular_price( (string) $p[3] );
		if ( $p[4] ) {
			$product->set_sale_price( (string) $p[4] );
		}
		$product->set_short_description( $p[6] );
		$product->set_description( $p[6] . "\n\n" . $long );
		$product->set_manage_stock( true );
		$product->set_stock_quantity( 25 );
		$product->set_stock_status( 'instock' );
		$product->set_featured( (bool) $p[8] );
		$product->set_catalog_visibility( 'visible' );
		$product->set_reviews_allowed( true );
		if ( ! empty( $cat[ $p[2] ] ) ) {
			$product->set_category_ids( array( $cat[ $p[2] ] ) );
		}
		$attrs = array();
		$pos   = 0;
		foreach ( $p[7] as $label => $value ) {
			$a = new WC_Product_Attribute();
			$a->set_id( 0 );
			$a->set_name( $label );
			$a->set_options( array( $value ) );
			$a->set_position( $pos++ );
			$a->set_visible( true );
			$a->set_variation( false );
			$attrs[] = $a;
		}
		$product->set_attributes( $attrs );
		$img = mv_import_image( $p[5], $p[1] );
		if ( $img ) {
			$product->set_image_id( $img );
		}
		$product->save();
	}

	/* ---------- Pages ---------- */
	$about = '<p>Maison Verrane is a fictional house created to demonstrate a luxury WooCommerce store. The story below is sample copy that you can replace from the page editor.</p><h2>Craft before volume</h2><p>We make fewer styles, in better materials, and design every piece so it can be repaired. Our leather is vegetable-tanned, our brass is solid and our watch cases are machined, not stamped.</p><h2>Looked after for life</h2><p>Bring any bag or watch back to the atelier and we will service it. Complimentary engraving and gift wrapping are included with every order.</p><h2>Responsible by default</h2><p>Packaging is plastic-free and every parcel is insured and carbon-balanced.</p>';
	$contact = '<p>Our client care team replies within one working day. For order questions, please include your order number.</p>';
	$privacy = '<h2>Who we are</h2><p>This is a demo store. The text below is a template and must be reviewed by a qualified professional before you go live.</p><h2>Data we collect</h2><p>When you place an order we collect your name, contact details, delivery address and order history. Payments are handled by the payment provider you configure; this site never stores card numbers.</p><h2>How we use it</h2><p>To fulfil orders, provide support and meet legal obligations. We do not sell personal data.</p><h2>Cookies</h2><p>We use essential cookies for the cart and your session, and optional analytics only with consent.</p><h2>Your rights</h2><p>You may request access, correction or deletion of your data by contacting us using the details on the Contact page.</p><h2>Retention</h2><p>Order records are kept for the period required by tax law.</p>';

	$home_id = mv_demo_page_create( 'home', 'Home', '', '' );
	$about_id = mv_demo_page_create( 'about-us', 'About us', $about, 'template-about.php', 'A house built on craft, repair and quiet design.' );
	mv_demo_page_create( 'contact-us', 'Contact us', $contact, 'template-contact.php' );
	mv_demo_page_create( 'categories', 'Categories', '', 'template-categories.php' );
	mv_demo_page_create( 'wishlist', 'Wishlist', '', 'template-wishlist.php' );
	$privacy_id = mv_demo_page_create( 'privacy-policy', 'Privacy policy', $privacy, '' );

	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}
	if ( $privacy_id ) {
		update_option( 'wp_page_for_privacy_policy', $privacy_id );
	}
	// Classic shortcodes keep the cart and checkout fully styled by this theme and compatible with the demo gateway.
	foreach ( array( 'cart' => '[woocommerce_cart]', 'checkout' => '[woocommerce_checkout]', 'myaccount' => '[woocommerce_my_account]' ) as $key => $code ) {
		$pid = wc_get_page_id( $key );
		if ( $pid > 0 ) {
			wp_update_post( array( 'ID' => $pid, 'post_content' => $code ) );
		}
	}

	/* ---------- Menus ---------- */
	$shop_id = wc_get_page_id( 'shop' );
	$menu    = wp_get_nav_menu_object( 'Primary' );
	if ( ! $menu ) {
		$mid   = wp_create_nav_menu( 'Primary' );
		$items = array( array( 'Shop', $shop_id ) );
		foreach ( array( 'handbags' => 'Handbags', 'watches' => 'Watches', 'fragrance' => 'Fragrance', 'accessories' => 'Accessories' ) as $slug => $label ) {
			if ( ! empty( $cat[ $slug ] ) ) {
				wp_update_nav_menu_item( $mid, 0, array( 'menu-item-title' => $label, 'menu-item-object' => 'product_cat', 'menu-item-object-id' => $cat[ $slug ], 'menu-item-type' => 'taxonomy', 'menu-item-status' => 'publish' ) );
			}
		}
		foreach ( array( 'about-us' => 'About us', 'contact-us' => 'Contact us' ) as $slug => $label ) {
			$pg = get_page_by_path( $slug );
			if ( $pg ) {
				wp_update_nav_menu_item( $mid, 0, array( 'menu-item-title' => $label, 'menu-item-object' => 'page', 'menu-item-object-id' => $pg->ID, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
			}
		}
	} else {
		$mid = $menu->term_id;
	}
	$fmenu = wp_get_nav_menu_object( 'Footer' );
	if ( ! $fmenu ) {
		$fid = wp_create_nav_menu( 'Footer' );
		foreach ( array( 'about-us' => 'About us', 'contact-us' => 'Contact us', 'categories' => 'Categories', 'wishlist' => 'Wishlist', 'privacy-policy' => 'Privacy policy' ) as $slug => $label ) {
			$pg = get_page_by_path( $slug );
			if ( $pg ) {
				wp_update_nav_menu_item( $fid, 0, array( 'menu-item-title' => $label, 'menu-item-object' => 'page', 'menu-item-object-id' => $pg->ID, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
			}
		}
	} else {
		$fid = $fmenu->term_id;
	}
	set_theme_mod( 'nav_menu_locations', array( 'primary' => $mid, 'footer' => $fid ) );

	/* ---------- Shipping (rest-of-world zone) ---------- */
	$zone = new WC_Shipping_Zone( 0 );
	$has  = false;
	foreach ( $zone->get_shipping_methods() as $m ) {
		$has = true;
	}
	if ( ! $has ) {
		$flat = $zone->add_shipping_method( 'flat_rate' );
		update_option( 'woocommerce_flat_rate_' . $flat . '_settings', array( 'title' => 'Insured courier', 'tax_status' => 'none', 'cost' => '15' ) );
		$free = $zone->add_shipping_method( 'free_shipping' );
		update_option( 'woocommerce_free_shipping_' . $free . '_settings', array( 'title' => 'Complimentary shipping', 'requires' => 'min_amount', 'min_amount' => '250' ) );
	}

	/* ---------- Coupon ---------- */
	if ( ! wc_get_coupon_id_by_code( 'WELCOME10' ) ) {
		$c = new WC_Coupon();
		$c->set_code( 'WELCOME10' );
		$c->set_discount_type( 'percent' );
		$c->set_amount( 10 );
		$c->set_description( 'Welcome offer: 10% off the first order.' );
		$c->save();
	}

	/* ---------- Demo gateway ---------- */
	$gw = get_option( 'woocommerce_mv_demo_gateway_settings', array() );
	if ( empty( $gw ) ) {
		update_option( 'woocommerce_mv_demo_gateway_settings', array( 'enabled' => 'yes', 'title' => 'Demo payment (test mode)' ) );
	}

	flush_rewrite_rules();
	update_option( 'mv_demo_imported', time() );
	return true;
}
