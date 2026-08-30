<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores and exposes plugin settings, and renders the admin settings page.
 */
class WhatsyCart_Settings {

	const OPTION_KEY = 'whatsycart_settings';

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Returns the saved settings merged with defaults.
	 *
	 * @return array
	 */
	public function get_settings() {
		$defaults = array(
			'phone_number_id'        => '',
			'access_token'           => '',
			'admin_notify_number'    => '',
			'notify_admin_new_order' => 'yes',
			'notify_customer_status' => 'yes',
		);

		return wp_parse_args( get_option( self::OPTION_KEY, array() ), $defaults );
	}

	public function register_settings_page() {
		add_submenu_page(
			'woocommerce',
			__( 'WhatsyCart', 'whatsycart' ),
			__( 'WhatsyCart', 'whatsycart' ),
			'manage_woocommerce',
			'whatsycart-settings',
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		register_setting(
			'whatsycart_settings_group',
			self::OPTION_KEY,
			array( $this, 'sanitize_settings' )
		);
	}

	/**
	 * Sanitizes settings before they are saved.
	 *
	 * @param array $input Raw posted settings.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		return array(
			'phone_number_id'        => sanitize_text_field( $input['phone_number_id'] ?? '' ),
			'access_token'           => sanitize_text_field( $input['access_token'] ?? '' ),
			'admin_notify_number'    => sanitize_text_field( $input['admin_notify_number'] ?? '' ),
			'notify_admin_new_order' => isset( $input['notify_admin_new_order'] ) ? 'yes' : 'no',
			'notify_customer_status' => isset( $input['notify_customer_status'] ) ? 'yes' : 'no',
		);
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$settings = $this->get_settings();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WhatsyCart Settings', 'whatsycart' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'whatsycart_settings_group' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="whatsycart_phone_number_id"><?php esc_html_e( 'WhatsApp Phone Number ID', 'whatsycart' ); ?></label></th>
						<td><input type="text" id="whatsycart_phone_number_id" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[phone_number_id]" value="<?php echo esc_attr( $settings['phone_number_id'] ); ?>" class="regular-text" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="whatsycart_access_token"><?php esc_html_e( 'Access Token', 'whatsycart' ); ?></label></th>
						<td><input type="password" id="whatsycart_access_token" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[access_token]" value="<?php echo esc_attr( $settings['access_token'] ); ?>" class="regular-text" autocomplete="off" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="whatsycart_admin_notify_number"><?php esc_html_e( 'Admin Notification Number', 'whatsycart' ); ?></label></th>
						<td><input type="text" id="whatsycart_admin_notify_number" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[admin_notify_number]" value="<?php echo esc_attr( $settings['admin_notify_number'] ); ?>" class="regular-text" placeholder="15551234567" /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Notifications', 'whatsycart' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[notify_admin_new_order]" <?php checked( $settings['notify_admin_new_order'], 'yes' ); ?> />
								<?php esc_html_e( 'Notify admin on new order', 'whatsycart' ); ?>
							</label>
							<br />
							<label>
								<input type="checkbox" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[notify_customer_status]" <?php checked( $settings['notify_customer_status'], 'yes' ); ?> />
								<?php esc_html_e( 'Notify customer on order status change', 'whatsycart' ); ?>
							</label>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
