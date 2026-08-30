<?php
/**
 * Plugin Name: WhatsyCart
 * Plugin URI:  https://github.com/hsbanga/abudhabi
 * Description: Sends WhatsApp notifications for WooCommerce order events (new order, status changes, custom messages) via the WhatsApp Business Cloud API.
 * Version:     0.1.0
 * Author:      Harjinder Singh Banga
 * Text Domain: whatsycart
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WHATSYCART_VERSION', '0.1.0' );
define( 'WHATSYCART_PATH', plugin_dir_path( __FILE__ ) );
define( 'WHATSYCART_URL', plugin_dir_url( __FILE__ ) );

require_once WHATSYCART_PATH . 'includes/class-whatsycart-settings.php';
require_once WHATSYCART_PATH . 'includes/class-whatsycart-api.php';
require_once WHATSYCART_PATH . 'includes/class-whatsycart-order-notifications.php';

/**
 * Bootstraps the plugin once WooCommerce is confirmed active.
 */
function whatsycart_init() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-error"><p>' .
					esc_html__( 'WhatsyCart requires WooCommerce to be installed and active.', 'whatsycart' ) .
					'</p></div>';
			}
		);
		return;
	}

	WhatsyCart_Settings::instance();
	WhatsyCart_Order_Notifications::instance();
}
add_action( 'plugins_loaded', 'whatsycart_init' );
