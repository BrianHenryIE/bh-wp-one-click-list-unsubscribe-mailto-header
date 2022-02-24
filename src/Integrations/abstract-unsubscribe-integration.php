<?php
/**
 *
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;
use Psr\Log\LoggerAwareTrait;

/**
 * Class Unsubscribe_Integration
 *
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */
abstract class Unsubscribe_Integration_Abstract {
	use LoggerAwareTrait;

	/** @var Settings_Interface */
	protected $settings;

	/**
	 * MailPoet constructor.
	 *
	 * @param Settings_Interface $settings
	 */
	public function __construct( $settings ) {

		$this->settings = $settings;
	}

	/**
	 * @return mixed
	 */
	abstract public function add_outgoing_filter();

	/**
	 * Unsubscribe using the encoded unsubscribe URL.
	 *
	 * @param $email_address
	 * @param $email_subject
	 */
	public function remove_subscriber( $email_address, $email_subject ): array {

		$result               = array();
		$result['from_email'] = $email_address;
		$result['url']        = null;
		$result['success']    = false;

		$output_array = array();
		if ( 1 === preg_match( '/unsubscribe:([^:]*)/', $email_subject, $output_array ) ) {

			$encoded_url = $output_array[1];

			$unsubscribe_url = base64_decode( $encoded_url );

			$result['url'] = $unsubscribe_url;

			$unsubscribe_url = wp_http_validate_url( $unsubscribe_url );

			if ( false == $unsubscribe_url ) {
				$this->logger->warning( 'Invalid URL provided as unsubscribe URL', array( 'url' => $unsubscribe_url ) );

			} else {
				// This is going to be a local URL.

				$request = wp_remote_post( $unsubscribe_url, array( 'body' => 'List-Unsubscribe=One-Click' ) );

				if ( ! is_wp_error( $request ) ) {

					if ( 200 === $request['response']['code'] ) {

						$result['success'] = true;
						$this->logger->info( "Removed subscriber {$email_address}." );

					} else {
						$this->logger->warning(
							'Unexpected response received',
							array(
								'unsubscribe_url' => $unsubscribe_url,
								'request'         => $request,
							)
						);
					}
				}
			}
		}

		return $result;
	}

	/**
	 *
	 * Find the one-click POST URL.
	 * Add it to the one-click mailto subject
	 * Append the mailto data to the List-Unsubscribe header
	 *
	 * @param array $headers
	 *
	 * @return array
	 */
	protected function add_mailto_to_existing_header( array $headers ): array {

		$unsubscribe_email_address = $this->settings->get_email_address();

		if ( empty( $unsubscribe_email_address ) ) {
			return $headers;
		}

		if ( ! isset( $headers['List-Unsubscribe'] ) ) {
			return $headers;
		}

		$existing_list_unsubscribe_header = $headers['List-Unsubscribe'];

		$output_array = array();
		if ( $existing_list_unsubscribe_header && 1 === preg_match( '/<(.*)>/', $existing_list_unsubscribe_header, $output_array ) ) {

			$unsubscribe_url = $output_array[1];

			$subject = 'unsubscribe:' . base64_encode( $unsubscribe_url );

			$headers['List-Unsubscribe'] = $existing_list_unsubscribe_header . ', <mailto:' . $unsubscribe_email_address . '?subject=' . $subject . '>';

		}

		return $headers;

	}

}
