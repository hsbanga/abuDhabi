<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thin client for the WhatsApp Business Cloud API.
 */
class WhatsyCart_API {

	/**
	 * Sends a text message to a WhatsApp number.
	 *
	 * @param string $to      Recipient phone number in E.164 format (no leading +).
	 * @param string $message Message body.
	 * @return true|WP_Error
	 */
	public static function send_text_message( $to, $message ) {
		$settings = WhatsyCart_Settings::instance()->get_settings();

		if ( empty( $settings['phone_number_id'] ) || empty( $settings['access_token'] ) ) {
			return new WP_Error( 'whatsycart_not_configured', __( 'WhatsyCart is not configured with a phone number ID and access token.', 'whatsycart' ) );
		}

		$to = self::sanitize_phone_number( $to );
		if ( empty( $to ) ) {
			return new WP_Error( 'whatsycart_invalid_number', __( 'Recipient phone number is invalid.', 'whatsycart' ) );
		}

		$endpoint = sprintf( 'https://graph.facebook.com/v19.0/%s/messages', rawurlencode( $settings['phone_number_id'] ) );

		$response = wp_remote_post(
			$endpoint,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $settings['access_token'],
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'messaging_product' => 'whatsapp',
						'to'                => $to,
						'type'              => 'text',
						'text'              => array( 'body' => $message ),
					)
				),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			return new WP_Error(
				'whatsycart_api_error',
				sprintf(
					/* translators: 1: HTTP status code, 2: response body */
					__( 'WhatsApp API returned an error (%1$d): %2$s', 'whatsycart' ),
					$code,
					wp_remote_retrieve_body( $response )
				)
			);
		}

		return true;
	}

	/**
	 * Strips everything but digits from a phone number.
	 *
	 * @param string $number Raw phone number.
	 * @return string
	 */
	private static function sanitize_phone_number( $number ) {
		return preg_replace( '/\D+/', '', (string) $number );
	}
}
