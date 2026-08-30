<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hooks into WooCommerce order lifecycle events and sends WhatsApp messages.
 */
class WhatsyCart_Order_Notifications {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'notify_admin_new_order' ), 10, 1 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'notify_customer_status_change' ), 10, 4 );
	}

	/**
	 * Notifies the store admin's WhatsApp number when a new order is placed.
	 *
	 * @param int $order_id Order ID.
	 */
	public function notify_admin_new_order( $order_id ) {
		$settings = WhatsyCart_Settings::instance()->get_settings();

		if ( 'yes' !== $settings['notify_admin_new_order'] || empty( $settings['admin_notify_number'] ) ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$message = sprintf(
			/* translators: 1: order number, 2: order total */
			__( 'New order #%1$s received for %2$s.', 'whatsycart' ),
			$order->get_order_number(),
			$order->get_formatted_order_total()
		);

		WhatsyCart_API::send_text_message( $settings['admin_notify_number'], wp_strip_all_tags( $message ) );
	}

	/**
	 * Notifies the customer via WhatsApp when their order status changes.
	 *
	 * @param int      $order_id   Order ID.
	 * @param string   $old_status Previous status.
	 * @param string   $new_status New status.
	 * @param WC_Order $order      Order object.
	 */
	public function notify_customer_status_change( $order_id, $old_status, $new_status, $order ) {
		$settings = WhatsyCart_Settings::instance()->get_settings();

		if ( 'yes' !== $settings['notify_customer_status'] ) {
			return;
		}

		$phone = $order->get_billing_phone();
		if ( empty( $phone ) ) {
			return;
		}

		$message = sprintf(
			/* translators: 1: order number, 2: new order status */
			__( 'Your order #%1$s status is now: %2$s.', 'whatsycart' ),
			$order->get_order_number(),
			wc_get_order_status_name( $new_status )
		);

		WhatsyCart_API::send_text_message( $phone, $message );
	}
}
