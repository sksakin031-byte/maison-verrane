<?php
/**
 * Plugin Name: Maison Demo Gateway (Test Mode)
 * Description: A clearly labelled DEMO payment gateway for WooCommerce. It never collects card data and never moves money. Use it to demonstrate the checkout flow, then replace it with a certified gateway (Stripe, PayPal, etc.) before going live.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: Maison Verrane Studio
 * License: GPL-2.0-or-later
 * Text Domain: maison-demo-gateway
 * WC requires at least: 8.0
 */
defined( 'ABSPATH' ) || exit;

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

add_action( 'plugins_loaded', function () {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	class MV_Demo_Gateway extends WC_Payment_Gateway {

		public function __construct() {
			$this->id                 = 'mv_demo_gateway';
			$this->icon               = '';
			$this->has_fields         = true;
			$this->method_title       = __( 'Demo payment (test mode)', 'maison-demo-gateway' );
			$this->method_description = __( 'DEMO ONLY. Simulates approved, declined and pending payments. No card details are collected and no money is moved.', 'maison-demo-gateway' );
			$this->supports           = array( 'products', 'refunds' );

			$this->init_form_fields();
			$this->init_settings();

			$this->title       = $this->get_option( 'title', __( 'Demo payment (test mode)', 'maison-demo-gateway' ) );
			$this->description = $this->get_option( 'description' );
			$this->enabled     = $this->get_option( 'enabled', 'no' );

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_notice' ) );
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_notice' ), 10, 3 );
		}

		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'     => array(
					'title'   => __( 'Enable', 'maison-demo-gateway' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable the demo gateway', 'maison-demo-gateway' ),
					'default' => 'no',
				),
				'title'       => array(
					'title'   => __( 'Title', 'maison-demo-gateway' ),
					'type'    => 'text',
					'default' => __( 'Demo payment (test mode)', 'maison-demo-gateway' ),
				),
				'description' => array(
					'title'   => __( 'Description', 'maison-demo-gateway' ),
					'type'    => 'textarea',
					'default' => __( 'This is a demonstration. No real payment is taken.', 'maison-demo-gateway' ),
				),
			);
		}

		/** Admin options page: make the demo status impossible to miss. */
		public function admin_options() {
			echo '<div class="notice notice-warning inline"><p><strong>' . esc_html__( 'Demo gateway.', 'maison-demo-gateway' ) . '</strong> ' . esc_html__( 'It never contacts a bank or processes real payments. Disable it and enable a certified gateway before taking live orders.', 'maison-demo-gateway' ) . '</p></div>';
			parent::admin_options();
		}

		/** Checkout fields: only a simulated outcome selector. No card number, expiry or CVC. */
		public function payment_fields() {
			if ( $this->description ) {
				echo '<p>' . esc_html( $this->description ) . '</p>';
			}
			echo '<p class="mv-demo-badge"><strong>' . esc_html__( 'TEST MODE', 'maison-demo-gateway' ) . '</strong> ' . esc_html__( 'Do not enter real card details. Choose an outcome to simulate:', 'maison-demo-gateway' ) . '</p>';
			echo '<fieldset class="mv-demo-outcomes"><legend class="screen-reader-text">' . esc_html__( 'Simulated payment result', 'maison-demo-gateway' ) . '</legend>';
			$options = array(
				'approve' => __( 'Approved payment', 'maison-demo-gateway' ),
				'decline' => __( 'Declined payment', 'maison-demo-gateway' ),
				'pending' => __( 'Pending review (order on hold)', 'maison-demo-gateway' ),
			);
			foreach ( $options as $value => $label ) {
				printf(
					'<label><input type="radio" name="mv_demo_outcome" value="%1$s" %2$s> %3$s</label>',
					esc_attr( $value ),
					checked( 'approve', $value, false ),
					esc_html( $label )
				);
			}
			echo '</fieldset>';
		}

		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				return array( 'result' => 'failure' );
			}
			// Nonce is verified by WooCommerce checkout before this method runs.
			$outcome = isset( $_POST['mv_demo_outcome'] ) ? sanitize_key( wp_unslash( $_POST['mv_demo_outcome'] ) ) : 'approve'; // phpcs:ignore WordPress.Security.NonceVerification
			if ( ! in_array( $outcome, array( 'approve', 'decline', 'pending' ), true ) ) {
				$outcome = 'approve';
			}
			$ref = 'DEMO-' . strtoupper( wp_generate_password( 10, false, false ) );

			if ( 'decline' === $outcome ) {
				$order->update_status( 'failed', __( '[DEMO] Simulated decline. No money was moved.', 'maison-demo-gateway' ) );
				wc_add_notice( __( 'The demo payment was declined (simulated). Choose "Approved payment" to continue.', 'maison-demo-gateway' ), 'error' );
				return array( 'result' => 'failure' );
			}
			if ( 'pending' === $outcome ) {
				$order->update_status( 'on-hold', __( '[DEMO] Simulated pending review. No money was moved.', 'maison-demo-gateway' ) );
				$order->set_transaction_id( $ref );
				$order->save();
			} else {
				$order->payment_complete( $ref );
				$order->add_order_note( __( '[DEMO] Simulated approved payment. No money was moved.', 'maison-demo-gateway' ) );
			}
			wc_reduce_stock_levels( $order_id );
			WC()->cart->empty_cart();

			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		}

		public function process_refund( $order_id, $amount = null, $reason = '' ) {
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				return false;
			}
			$order->add_order_note( sprintf(
				/* translators: %s: amount */
				__( '[DEMO] Simulated refund of %s. No money was moved.', 'maison-demo-gateway' ),
				wc_price( $amount, array( 'currency' => $order->get_currency() ) )
			) );
			return true;
		}

		public function thankyou_notice() {
			echo '<p class="woocommerce-notice mv-demo-badge"><strong>' . esc_html__( 'Demo order.', 'maison-demo-gateway' ) . '</strong> ' . esc_html__( 'This payment was simulated. No money was moved and no goods will be shipped.', 'maison-demo-gateway' ) . '</p>';
		}

		public function email_notice( $order, $sent_to_admin, $plain_text ) {
			if ( $order->get_payment_method() !== $this->id ) {
				return;
			}
			$text = __( 'DEMO ORDER: payment was simulated in test mode.', 'maison-demo-gateway' );
			echo $plain_text ? esc_html( $text ) . "\n\n" : '<p><strong>' . esc_html( $text ) . '</strong></p>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}

	add_filter( 'woocommerce_payment_gateways', function ( $gateways ) {
		$gateways[] = 'MV_Demo_Gateway';
		return $gateways;
	} );
} );

/** Warn administrators if the demo gateway is enabled on a production environment. */
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}
	$opt = get_option( 'woocommerce_mv_demo_gateway_settings', array() );
	if ( isset( $opt['enabled'] ) && 'yes' === $opt['enabled'] && 'production' === wp_get_environment_type() ) {
		echo '<div class="notice notice-warning"><p><strong>Maison Demo Gateway is enabled.</strong> Customers can place orders without paying. Disable it in WooCommerce → Settings → Payments before launching a live store.</p></div>';
	}
} );
