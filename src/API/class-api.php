<?php
/**
 * Handles email unsubscribes.
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\API;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\MailPoet;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Newsletter;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Unsubscribe_Integration_Abstract;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\BH_Email;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\API\API as BH_WP_Mailboxes;
use DateTime;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Class API
 *
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */
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
				$integration = new $class_or_classname( $this->settings );
				$integration->setLogger( $this->logger );
				$this->integrations[ $class_or_classname ] = $integration;
			} elseif ( $class_or_classname instanceof Unsubscribe_Integration_Abstract ) {
				$this->integrations[ get_class( $class_or_classname ) ] = $class_or_classname;
			}
		}

		return $this->integrations;
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

		$process_new_emails            = array();
		$process_new_emails['success'] = array();
		$process_new_emails['failure'] = array();

		foreach ( $emails as $email ) {

			$from    = $email->get_from_email();
			$subject = $email->get_subject();

			foreach ( $this->get_unsubscribe_integrations() as $integration ) {

				$result = $integration->remove_subscriber( $email, $subject );

				if ( $result['success'] ) {
					$process_new_emails['success'][] = $email;
				} else {
					$process_new_emails['failure'][] = $email;
				}
			}
		}

		return $process_new_emails;
	}

	/**
	 * Synchronously check the emails.
	 *
	 * @return void
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

	public function get_last_checked_time(): ?DateTime {
		$last_fetched_times = $this->mailboxes->get_last_fetched_times();
		$mailboxes          = $this->settings->get_configured_mailbox_settings();
		// Before the mailbox has been configured.
		if ( empty( $mailboxes ) ) {
			return null;
		}
		$account_name = $mailboxes[0]->get_account_unique_friendly_name();
		return $last_fetched_times[ $account_name ];
	}
}

