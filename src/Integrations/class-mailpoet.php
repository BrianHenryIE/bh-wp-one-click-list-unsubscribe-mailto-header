<?php
/**
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

class MailPoet extends Unsubscribe_Integration_Abstract {

	/**
	 * TODO: This filter does not exist... MailPoet needs to create one.
	 */
	public function add_outgoing_filter() {
		add_filter( 'mailpoet_headers', array( $this, 'add_unsubscribe_email_to_headers' ), 10, 4 );
	}

	/**
	 * Modify the outgoing email
	 *
	 * Unmodified header:
	 * GET or POST to this URL unsubscribes the users. The URL does not expire after use.
	 * ```
	 * List-Unsubscribe: <https://example.org.org?mailpoet_router&endpoint=track&action=click&data=WyI0IiwiZDAzYWE3IiwiMSIsImIzNjU4YjMzMDEwMCIsZmFsc2Vd>
	 * ```
	 * where in this example, data=base64_encode(["4","d03aa7","1","b3658b330100",false])
	 *
	 * @hooked mailpoet_headers
	 * NB: This filter was added by BrianHenryIE and is not part of MailPoet (yet!).
	 *
	 * @see Links::getUnsubscribeUrl($queue, $subscriberId) // This is where the regular URL is generated.
	 * @see PHPMail::configureMailerWithMessage() // This maybe doesn't add the full List-Ubsubscribe header... are there two parts?
	 *
	 * @param string[] $headers
	 * @param array    $newsletter
	 * @param array    $subscriber
	 * @param array    $extraParams
	 */
	public function add_unsubscribe_email_to_headers( $headers, $newsletter, $subscriber, $extraParams ) {

		$headers = $this->add_mailto_to_existing_header( $headers );

		return $headers;

	}
	//
	// **
	// * @param string $email_address
	// * @param string $email_subject
	// */
	// public function remove_subscriber( $email_address, $email_subject ) {
	//
	// parent::remove_subscriber( $email_address, $email_subject );
	//
	// if ( ! class_exists( MailPoetAPI::class ) ) {
	// return;
	// }
	//
	// try {
	// $mailpoet_api = MailPoetAPI::MP( 'v1' );
	// } catch ( \Exception $e ) {
	// }
	//
	// $subscriber = $mailpoet_api->getSubscriber( $email_address );
	//
	// $subscription_ids = array_map(
	// function( $subscription ) {
	// return $subscription['id'];
	// },
	// $subscriber['subscriptions']
	// );
	//
	// $mailpoet_api->unsubscribeFromLists( $subscriber['id'], $subscription_ids );
	// }

}
