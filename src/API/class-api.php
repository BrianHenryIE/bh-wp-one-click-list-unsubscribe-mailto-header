<?php
/**
 * Handles email unsubscribes.
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\API;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\MailPoet;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Newsletter;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Unsubscribe_Integration_Abstract;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\BH_Email;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\API\API as BH_WP_Mailboxes;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\WP_Includes\Cron;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class API implements API_Interface {
	use LoggerAwareTrait;

	protected BH_WP_Mailboxes $mailboxes;

	/**
	 * Settings object with BH_WP_Mailboxes login credentials.
	 *
	 * @var Settings_Interface
	 */
	protected Settings_Interface $settings;

	/** @var Unsubscribe_Integration_Abstract[] */
	protected array $integrations = array();

	public function __construct( BH_WP_Mailboxes $mailboxes, Settings_Interface $settings, LoggerInterface $logger ) {
		$this->setLogger( $logger );
		$this->settings  = $settings;
		$this->mailboxes = $mailboxes;

	}

	/**
	 * @return Unsubscribe_Integration_Abstract[]
	 */
	public function get_unsubscribe_integrations(): array {

		/** @var array<string|Unsubscribe_Integration_Abstract> $unsubscribe_integrations */
		$unsubscribe_integrations = array(
			MailPoet::class,
			Newsletter::class,
		);

		$unsubscribe_integrations = apply_filters( 'bh_wp_one_click_list_unsubscribe_integrations', $unsubscribe_integrations );

		foreach ( $unsubscribe_integrations as $class_or_classname ) {

			if ( is_string( $class_or_classname ) && class_exists( $class_or_classname ) ) {
				$integration                               = new $class_or_classname( $this, $this->settings, $this->logger );
				$this->integrations[ $class_or_classname ] = $integration;
			} elseif ( $class_or_classname instanceof Unsubscribe_Integration_Abstract ) {
				$this->integrations[ get_class( $class_or_classname ) ] = $class_or_classname;
			}
		}

		return $this->integrations;
	}


	/**
	 *
	 * Find the one-click POST URL.
	 * Add it to the one-click mailto subject
	 * Append the mailto data to the List-Unsubscribe header
	 *
	 * @param array<string, string> $headers
	 *
	 * @return array<string, string>
	 */
	public function add_mailto_to_existing_headers( array $headers ): array {

		$unsubscribe_email_address = $this->settings->get_email_address();

		if ( empty( $unsubscribe_email_address ) ) {
			return $headers;
		}

		if ( ! isset( $headers['List-Unsubscribe'] ) ) {
			return $headers;
		}

		$headers['List-Unsubscribe'] = $this->add_mailto_to_existing_list_unsubscribe_header( $headers['List-Unsubscribe'] );

		return $headers;

	}


	public function add_mailto_to_existing_list_unsubscribe_header( string $header_value ): string {

		$unsubscribe_email_address = $this->settings->get_email_address();

		$output_array = array();
		if ( $header_value && 1 === preg_match( '/<(.*)>/', $header_value, $output_array ) ) {

			$unsubscribe_url = $output_array[1];

			$subject = 'unsubscribe:' . base64_encode( $unsubscribe_url );

			$header_value = $header_value . ', <mailto:' . $unsubscribe_email_address . '?subject=' . $subject . '>';
		}

		return $header_value;
	}

	/**
	 * Hooked to the new emails action.
	 *
	 * @hooked bh-wp-one-click-list-unsubscribe_mailboxes_fetch_emails_saved_...
	 *
	 * @param BH_Email[] $emails
	 *
	 * @return array{success:array<BH_Email>, failure:array<BH_Email>}
	 * @see \BrianHenryIE\WP_Mailboxes\API\API::check_email()
	 */
	public function process_new_emails( array $emails ): array {

		$this->logger->debug( 'process_new_emails called with ' . count( $emails ) . ' new emails.' );

		$process_new_emails            = array();
		$process_new_emails['success'] = array();
		$process_new_emails['failure'] = array();

		foreach ( $emails as $email ) {

			$subject = $email->get_subject();

			if ( 1 !== preg_match( '/unsubscribe:([^:]*)/', $email_subject ) ) {
				continue;
			}

			$from = $email->get_from_email();

			// Find if/what mailing list they are subscribed to.

			$found_before = array();
			$found_after  = array();

			foreach ( $this->get_unsubscribe_integrations() as $integration ) {
				if ( $integration->is_subscribed( $from ) ) {
					$found_before[] = $integration->get_friendly_name();
				}
			}

			$result = $this->remove_subscriber( $from, $subject );

			foreach ( $this->get_unsubscribe_integrations() as $integration ) {
				if ( $integration->is_subscribed( $from ) ) {
					$found_after[] = $integration->get_friendly_name();
				}
			}

			if ( count( $found_after ) < count( $found_before ) ) {
				// Success.

				$unsubscribed_from = array_diff( $found_before, $found_after );
				$this->logger->info( $from . ' unsubscribed from ' . implode( ',', $unsubscribed_from ) );

				$process_new_emails['success'][] = $email;

			} else {
				$this->logger->notice(
					'Email NOT unsubscribed from any integration. Subject: ' . $subject,
					array(
						'from'    => $from,
						'subject' => $subject,
					)
				);
			}
		}

		return $process_new_emails;
	}



	/**
	 * Unsubscribe using the encoded unsubscribe URL.
	 *
	 * @param string $email_address
	 * @param string $email_subject
	 */
	public function remove_subscriber( string $email_address, string $email_subject ): array {

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
						$this->logger->debug( "HTTP 200 returned, hopefully removed subscriber {$email_address}." );

					} else {

						// Newsletter has the text "Subscriber not found" and a 404
						// This seems to be firing twice.

						$this->logger->warning(
							'Unexpected response received',
							array(
								'unsubscribe_url' => $unsubscribe_url,
								'email_address'   => $email_address,
								'email_subject'   => $email_subject,
								'request'         => $request,
							)
						);
					}
				} else {
					// TODO: LOG ERROR!
				}
			}
		}

		return $result;
	}

	/**
	 * Synchronously check the emails.
	 *
	 * @return array{success:array, failure:array}
	 */
	public function check_for_unsubscribe_emails(): array {

		$check_now            = array();
		$check_now['success'] = array();
		$check_now['failure'] = array();

		remove_action(
			'bh_wp_mailboxes_fetch_emails_saved_bh-wp-one-click-list-unsubscribe',
			array(
				$this,
				'process_new_emails',
			)
		);
		$check_email = $this->mailboxes->check_email();

		if ( isset( $check_email['saved_emails'] ) ) {

			$emails             = $check_email['saved_emails'];
			$process_new_emails = $this->process_new_emails( $emails );

			$check_now = array_merge( $process_new_emails, $check_now );
		}

		return $check_now;
	}

	public function get_last_checked_time(): ?DateTimeInterface {
		$last_fetched_times = $this->mailboxes->get_last_fetched_times();
		$mailboxes          = $this->settings->get_configured_mailbox_settings();
		// Before the mailbox has been configured.
		if ( empty( $mailboxes ) ) {
			return null;
		}
		$account_name = $mailboxes[0]->get_account_unique_friendly_name();
		return $last_fetched_times[ $account_name ];
	}

	public function get_next_check_time(): ?DateTimeInterface {
		$cron      = new Cron( $this->mailboxes, $this->mailboxes->get_settings(), $this->logger );
		$cron_name = $cron->get_fetch_emails_cron_hook_name();

		$next_scheduled_event = wp_next_scheduled( $cron_name );

		if ( false === $next_scheduled_event ) {
			return null;
		}

		$next = DateTime::createFromFormat( 'U', "{$next_scheduled_event}", new DateTimeZone( 'UTC' ) );

		if ( false === $next ) {
			return null;
		}

		return $next;
	}
}

