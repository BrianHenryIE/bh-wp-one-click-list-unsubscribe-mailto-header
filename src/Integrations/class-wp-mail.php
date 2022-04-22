<?php
/**
 *
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API_Interface;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;
use Psr\Log\LoggerInterface;

class WP_Mail extends Unsubscribe_Integration_Abstract {

	public function get_friendly_name(): string {
		return 'wp_mail()';
	}

	public function add_outgoing_filter() {
		add_filter( 'wp_mail', array( $this, 'update_outgoing_unsubscribe_headers' ) );
	}

	/**
	 * We've no way to know if the user is subscribed or unsubscribed at this point, since we have no idea what
	 * plugin added the original List-Unsubscribe header.
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	public function is_subscribed( string $email ): bool {
		return apply_filters( 'bh_wp_one_click_list_unsubscribe_is_subscribed', false, $email );
	}

	/**
	 *
	 * @hooked wp_mail
	 * @see wp_mail()
	 *
	 * @param array{to:string, subject:string, message:string, headers:string|array<string>, attachments:string|array<string>} $wp_mail_args The arguments passed to wp_mail() (before processing).
	 *
	 * @return array{to:string, subject:string, message:string, headers:string|array<string>, attachments:string|array<string>}
	 */
	public function update_outgoing_unsubscribe_headers( array $wp_mail_args ): array {

		$wp_mail_headers = $wp_mail_args['headers'];

		if ( is_string( $wp_mail_headers ) ) {
			$wp_mail_headers = array( $wp_mail_headers );
		}

		$headers = array();
		foreach ( $wp_mail_headers as $wp_mail_header ) {
			$header                = explode( ':', $wp_mail_header );
			$headers[ $header[0] ] = $header[1];
		}

		$headers = $this->api->add_mailto_to_existing_headers( $headers );

		// wp_mail() parses the headers as an array of strings of colon separated name:values, it does not use the PHP array keys.
		$headers = array_map(
			function( $key, $value ) {
				return "{$key}:{$value}";
			},
			array_keys( $headers ),
			$headers
		);

		$wp_mail_args['headers'] = $headers;

		return $wp_mail_args;
	}
}
