<?php
/**
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

use TNP_Email;
use TNP_User;

/**
 * Class Newsletter
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */
class Newsletter extends Unsubscribe_Integration_Abstract {

	/**
	 * The POST list-unsubscribe header is added at priority 10.
	 *
	 * @see NewsletterUnsubscription::__construct
	 */
	public function add_outgoing_filter() {
		add_filter( 'newsletter_message_headers', array( $this, 'add_unsubscribe_email_to_headers' ), 11, 3 );
	}

	/**
	 * Prepends the existing List-Unsubscribe header with the email address.
	 *
	 * Existing header:
	 * ```
	 * List-Unsubscribe: <https://example.org/?na=uc&nk=2-438aa70b50&nek=1-66e61e32ae>
	 * List-Unsubscribe-Post: List-Unsubscribe=One-Click
	 * ```
	 *
	 * After:
	 * ```
	 * List-Unsubscribe: <https://example.org/?na=uc&nk=2-438aa70b50&nek=1-66e61e32ae> <mailto:unsubscribe@example.org?subject=...>
	 * ```
	 *
	 * @hooked newsletter_message_headers
	 * Hooked at priority 11 because the Newsletter developers use the same filter to initially add the header.
	 * @see \NewsletterUnsubscription
	 *
	 * @param string[]  $headers
	 * @param TNP_Email $email
	 * @param TNP_User  $user
	 *
	 * @return string[]
	 */
	public function add_unsubscribe_email_to_headers( array $headers, $email, $user ) {

		$headers = $this->add_mailto_to_existing_header( $headers );

		return $headers;
	}
	//
	// **
	// *
	// * @param string $email_address The email address that sent the unsubscribe request.
	// * @param string $subject The email subject.
	// */
	// public function unsubscribe_from_newsletter( $email_address, $subject ) {
	//
	// parent::remove_subscriber( $email_address, $subject );
	//
	// if ( ! class_exists( TNP::class ) ) {
	// return;
	// }
	//
	// $subject should have a regex run against it to pull out the data (list#etc) added in the outgoing method.
	// But the Newsletter API doesn't have any way to match the unsubscribe to the particular newsletter or lists.
	//
	// TNP::unsubscribe( array( 'email' => $email_address ) );
	// }

}
