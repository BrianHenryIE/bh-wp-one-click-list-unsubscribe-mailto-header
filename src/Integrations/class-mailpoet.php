<?php
/**
 *
 * Unmodified header:
 * GET or POST to this URL unsubscribes the users. The URL does not expire after use.
 * ```
 * List-Unsubscribe: <https://example.org.org?mailpoet_router&endpoint=track&action=click&data=WyI0IiwiZDAzYWE3IiwiMSIsImIzNjU4YjMzMDEwMCIsZmFsc2Vd>
 * ```
 * where in this example, data=base64_encode(["4","d03aa7","1","b3658b330100",false])
 *
 * @see Links::getUnsubscribeUrl($queue, $subscriberId) // This is where the regular URL is generated.
 * @see PHPMail::configureMailerWithMessage() // This maybe doesn't add the full List-Unsubscribe header... are there two parts?
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\PHPMailer_Intercept;

class MailPoet extends Unsubscribe_Integration_Abstract {

	public function get_friendly_name(): string {
		return 'MailPoet';
	}

	/**
	 * Check are we inside a MailPoet AJAX or Cron action, if so replace PHP Mailer with our own.
	 *
	 * TODO: How to check during cron?!
	 */
	public function add_outgoing_filter() {

		add_action( 'wp_ajax_mailpoet', array( $this, 'replace_phpmailer' ), 0 );
		add_action( 'wp_ajax_nopriv_mailpoet', array( $this, 'replace_phpmailer' ), 0 );
	}

	public function replace_phpmailer(): void {

		if ( ! class_exists( \PHPMailer::class ) ) {
			require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';

			/**
			 * MailPoet checks has PHPMailer already been loaded. By loading it earlier, we get to specify the class.
			 *
			 * @see PHPMailerLoader::load()
			 * @see wp-content/plugins/mailpoet/lib/Mailer/WordPress/PHPMailerLoader.php
			 */

			class_alias( PHPMailer_Intercept::class, 'PHPMailer' );
		}
	}

	/**
	 * Determine is the email address subscribed to the integration.
	 *
	 * Used before/after to see was it unsubscribed.
	 *
	 * @param string $email_address
	 *
	 * @return bool
	 */
	public function is_subscribed( string $email_address ): bool {

		if ( ! class_exists( \MailPoet\API\API::class ) ) {
			return false;
		}

		try {
			$mailpoet_api = \MailPoet\API\API::MP( 'v1' );
		} catch ( \Exception $e ) {
			// TODO.
			return false;
		}

		try {
			$subscriber = $mailpoet_api->getSubscriber( $email_address );
		} catch ( \MailPoet\API\MP\v1\APIException $e ) {
			// Subscriber probably does not exist
			return false;
		}

		return count( $subscriber['subscriptions'] ) > 0;

	}

}
